<?php

namespace App\Http\Controllers;

use App\Models\Thing;
use App\Models\Description;
use App\Models\Place;
use App\Models\User;
use App\Models\UseModel;
use App\Models\Unit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Mail\ThingAssignedMail;
use App\Mail\ThingDescriptionUpdated;
use App\Jobs\SendThingAssignedEmail;
use Illuminate\Support\Facades\DB;
use App\Models\Notification as AppNotification;
use App\Models\DescriptionNotification;
use App\Events\ThingCreated;

class ThingController extends Controller
{
    public function index()
    {
        $things = Cache::remember('things_public_' . md5(request()->getQueryString()), 300, function () {
            return Thing::with(['owner', 'usages' => function($query) {
                $query->latest()->take(1)->with(['user', 'place', 'unit']);
            }, 'descriptions' => function($query) {
                $query->where('is_current', true);
            }])
            ->whereDoesntHave('usages.place', function($q) {
                $q->where('repair', true)->orWhere('work', true);
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();
        });
        
        return view('things.index', compact('things'));
    }

    public function show(Thing $thing)
    {
        $thing->load(['owner', 'usages.user', 'usages.place', 'usages.unit', 'descriptions.creator']);
        $currentUsage = $thing->usages()->latest()->first();
        
        return view('things.show', compact('thing', 'currentUsage'));
    }

    public function create()
    {
        $this->authorize('create', Thing::class);
        return view('things.create');
    }

    public function store(Request $request)
    {
        $this->authorize('create', Thing::class);
        
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'wrnt' => 'nullable|date',
            'place_id' => 'nullable|exists:places,id',
        ]);

        $thing = Thing::create([
            'name' => $request->name,
            'description' => $request->description,
            'wrnt' => $request->wrnt,
            'master' => Auth::id(),
        ]);
        
        broadcast(new ThingCreated($thing, Auth::user()));
        $this->clearAllThingCaches();
        
        return redirect()->route('things.index')
            ->with('success', 'Вещь успешно создана!');
    }

    public function edit(Thing $thing)
    {
        $this->authorize('update', $thing);
        return view('things.edit', compact('thing'));
    }

    public function update(Request $request, Thing $thing)
    {
        $this->authorize('update', $thing);

        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'wrnt' => 'nullable|date',
        ]);

        // сохраняем старое описание для проверки изменений
        $oldDescription = $thing->description;

        $thing->update($request->only(['name', 'description', 'wrnt']));

        // отправляем уведомление если изменилось описание в основном поле
        if ($oldDescription !== $request->description && $request->description) {
            $this->sendDescriptionNotifications($thing, $request->description, false); // false = не новое описание, а обновленное
        }
        $this->clearAllThingCaches();
        return redirect()->route('things.index')->with('success', 'Вещь успешно обновлена!');
    }

    public function destroy(Thing $thing)
    {
        $this->authorize('delete', $thing);
        $thing->delete();
        
        $this->clearAllThingCaches();

        return redirect()->route('things.index')
            ->with('success', 'Вещь успешно удалена!');
    }

    public function my() // мои вещи
    {
        $things = Cache::remember('things_my_' . Auth::id() . '_' . md5(request()->getQueryString()), 300, function () {
            return Thing::with(['usages.user', 'usages.place', 'usages.unit', 'descriptions' => function($query) {
                $query->where('is_current', true);
            }])
            ->where('master', Auth::id())
            ->latest()
            ->paginate(10)
            ->withQueryString(); 
        });
        
        return view('things.my', compact('things'));
    }

    public function borrowed() // взятые вещи
    {
        $usages = UseModel::where('user_id', Auth::id())
            ->with(['thing.owner', 'thing.descriptions' => function($query) {
                $query->where('is_current', true);
            }, 'place', 'unit'])
            ->latest()
            ->paginate(10)
            ->withQueryString();
        
        return view('things.borrowed', compact('usages'));
    }

    public function repair()
    {
        $things = Cache::remember('things_repair_' . md5(request()->getQueryString()), 300, function () {
            return Thing::with(['owner', 'usages.place', 'usages.unit', 'descriptions' => function($query) {
                $query->where('is_current', true);
            }])
            ->whereHas('usages', function($query) {
                $query->whereHas('place', function($q) {
                    $q->where('repair', true);
                });
            })->latest()->paginate(10)->withQueryString();
        });
        
        return view('things.repair', compact('things'));
    }

    public function work()
    {
        $things = Cache::remember('things_work_' . md5(request()->getQueryString()), 300, function () {
            return Thing::with(['owner', 'usages.place', 'usages.unit', 'descriptions' => function($query) {
                $query->where('is_current', true);
            }])
            ->whereHas('usages', function($query) {
                $query->whereHas('place', function($q) {
                    $q->where('work', true);
                });
            })->latest()->paginate(10)->withQueryString(); 
        });
        
        return view('things.work', compact('things'));
    }

    public function used() // используемые мои вещи
    {
        $things = Cache::remember('things_used_' . md5(request()->getQueryString()), 300, function () {
            return Thing::with(['usages.user', 'usages.place', 'usages.unit', 'descriptions' => function($query) {
                $query->where('is_current', true);
            }])
            ->whereHas('usages')
            ->where('master', Auth::id())
            ->whereHas('usages', function($query) {
                $query->where('user_id', '!=', Auth::id());
            })->latest()->paginate(10)->withQueryString(); 
        });
        return view('things.used', compact('things'));
    }

    public function all() // все вещи - админка
    {
        $this->authorize('viewAll', Thing::class);
        
        $things = Thing::with([
            'owner',
            'usages' => function($query) {
                $query->latest()->limit(1);
            },
            'usages.user',
            'usages.place',
            'usages.unit',
            'descriptions' => function($query) {
                $query->where('is_current', true);
            }
        ])->latest()->paginate(20)->withQueryString(); 
        
        // удобные отношения для представления
        $things->each(function($thing) {
            $thing->latest_usage = $thing->usages->first();
            $thing->latest_user = $thing->latest_usage?->user;
            $thing->latest_place = $thing->latest_usage?->place;
            $thing->latest_unit = $thing->latest_usage?->unit;
        });
        
        return view('things.admin-all', compact('things'));
    }

    public function addDescription(Request $request, Thing $thing) // добавление нового описания
    {
        $this->authorize('update', $thing);
        
        $request->validate([
            'description' => 'required|string|min:3'
        ]);

        // сброс тек статус у всех описаний этой вещи
        $thing->descriptions()->update(['is_current' => false]);
        
        // создание нового описания как текущего
        $thing->descriptions()->create([
            'description' => $request->description,
            'is_current' => true,
            'created_by' => Auth::id()
        ]);

        $thing->update(['description' => $request->description]);
        // Отправляем уведомления
        $this->sendDescriptionNotifications($thing, $request->description, true);
        // Очищаем кэш
        $this->clearAllThingCaches();

        return back()->with('success', 'Описание успешно добавлено!');
    }

    public function updateDescription(Request $request, Thing $thing, Description $description)
    {
        $this->authorize('update', $thing);
        
        if ($description->thing_id != $thing->id) {
            abort(403, 'Описание не принадлежит этой вещи');
        }
        $request->validate(['new_description' => 'required|string|min:3']);

        $oldDescription = $description->description; // сохраняем старое описание для сравнения

        $description->update([ 
        'description' => $request->new_description,
        'is_current' => true,
        'created_by' => Auth::id()
        ]);
       
        $thing->descriptions() // сброс тек описания у других опис
            ->where('id', '!=', $description->id) // кроме только что обновленного
            ->update(['is_current' => false]);
        
        $thing->update(['description' => $request->new_description]); // обновляет поле description в таблице things
        
        if ($oldDescription !== $request->new_description) { // Отправляем email уведомления только если описание действительно изменилось
            $this->sendDescriptionNotifications($thing, $request->new_description, false);
        }
        $this->clearAllThingCaches();
        return back()->with('success', 'Описание успешно обновлено!');
    }

    public function setCurrentDescription(Request $request, Thing $thing, Description $description) // установка старого описания как текущего
    {
        $this->authorize('update', $thing);
        
        if ($description->thing_id != $thing->id) {
            abort(403, 'Описание не принадлежит этой вещи');
        }
        
        $thing->descriptions()->update(['is_current' => false]); // сброс тек статус у всех описаний
        
        $description->update(['is_current' => true]); // установ выбранное как текущее
        
        $thing->update(['description' => $description->description]); // обновление описания в табл things
       
        $this->clearAllThingCaches();

        return back()->with('success', 'Текущее описание обновлено!');
    }

    public function transfer(Request $request, Thing $thing) // передача вещи другому пользователю
    {
        if ($thing->master != Auth::id()) {
            abort(403, 'Только владелец может передавать вещь!');
        }

        $request->validate([
            'user_id' => 'required|exists:users,id',
            'place_id' => 'required|exists:places,id',
            'amount' => 'required|integer|min:1',
            'unit_id' => 'nullable|exists:units,id',
        ]);

        $recipient = User::findOrFail($request->user_id);
        $place = Place::findOrFail($request->place_id);
        $unit = $request->unit_id ? Unit::find($request->unit_id) : null;

        UseModel::where('thing_id', $thing->id)
         ->where('user_id', Auth::id())
         ->delete();

        UseModel::create([
            'thing_id' => $thing->id,
            'place_id' => $request->place_id,
            'user_id' => $request->user_id,
            'amount' => $request->amount,
            'unit_id' => $request->unit_id,
        ]);

        AppNotification::create([
            'user_id' => $recipient->id,
            'thing_id' => $thing->id,
            'from_user_id' => Auth::id(),
            'type' => 'assignment',
            'title' => 'Вам назначена вещь',
            'message' => "Пользователь " . Auth::user()->name . 
                         " назначил вам вещь '{$thing->name}' для использования в месте: {$place->name}. " .
                         "Количество: {$request->amount} шт."
        ]);

        $emailSent = false; // флаг успешности отправки
        $emailMethod = 'sync';
        
        try {
            if ($emailMethod === 'queue') {
                SendThingAssignedEmail::dispatch(
                    $thing,
                    Auth::user(),
                    $recipient,
                    $request->amount,
                    $place,
                    $unit
                )->onQueue('emails');
                
                Log::info("Email job отправлен в очередь для пользователя {$recipient->email}");
            } else {
                Mail::to($recipient->email)
                    ->send(new ThingAssignedMail(
                        $thing,
                        Auth::user(),
                        $recipient,
                        $request->amount,
                        $place,
                        $unit
                    ));
                
                Log::info("Email отправлен синхронно пользователю {$recipient->email} о назначении вещи {$thing->name}");
            }
            
            $emailSent = true;
            
        } catch (\Exception $e) {
            Log::error('Ошибка отправки email: ' . $e->getMessage());
        }

        $this->clearAllThingCaches();

        $message = 'Вещь успешно передана в пользование!';
        if ($emailSent) {
            if ($emailMethod === 'queue') {
                $message .= ' Уведомление поставлено в очередь на отправку.';
            } else {
                $message .= ' Уведомление отправлено на email пользователя.';
            }
        } else {
            $message .= ' Не удалось отправить email уведомление.';
        }

        return redirect()->route('things.show', $thing)
            ->with('success', $message);
    }

    public function return(Thing $thing) // возврат вещи
    {
        $usage = $thing->usages()->latest()->first();
        
        if (!$usage) {
            return back()->with('error', 'Эта вещь не находится в пользовании');
        }

        DB::table('uses')->where('thing_id', $usage->thing_id)
        ->where('place_id', $usage->place_id)->where('user_id', $usage->user_id)->delete();

        $this->clearAllThingCaches();

        return redirect()->route('things.show', $thing)
            ->with('success', 'Вещь успешно возвращена');
    }

    public function transferForm(Thing $thing) // форма передачи вещи
    {
        if ($thing->master != Auth::id()) {
            abort(403, 'Только владелец может передавать вещь!');
        }

        $users = User::all();
        $places = Place::where('repair', false)->where('work', false)->get();
        $units = Unit::all();
        
        return view('things.transfer', compact('thing', 'users', 'places', 'units'));
    }

    private function clearAllThingCaches()
    {   
        Cache::forget('things_public_' . md5(request()->getQueryString()));
        Cache::forget('things_my_' . Auth::id() . '_' . md5(request()->getQueryString()));
        Cache::forget('things_repair_' . md5(request()->getQueryString()));
        Cache::forget('things_work_' . md5(request()->getQueryString()));
        Cache::forget('things_used_' . md5(request()->getQueryString()));
        Cache::forget('things_admin_all');
    }

    private function sendDescriptionNotifications(Thing $thing, $descriptionText, $isNew)
    {
        try {
            $usersToNotify = collect(); // создание коллекции
            $currentUser = Auth::user(); // тот кто создал описание

            // хозяин вещи если это не сам пользователь
            if ($thing->master != $currentUser->id) {
                $owner = $thing->owner;
                if ($owner) {
                    $usersToNotify->push($owner);
                }
            }

            // текущий пользователь, у которого вещь
            $currentUsage = $thing->currentUsage();
            if ($currentUsage && $currentUsage->user_id != $currentUser->id) {
                $assignedUser = $currentUsage->user;
                if ($assignedUser) {
                    $usersToNotify->push($assignedUser);
                }
            }

            // если некому отправлять, выходим
            if ($usersToNotify->isEmpty()) {
                Log::info("нет получателей");
                return true;
            }

            foreach ($usersToNotify as $user) {
                // Email уведомление
                if ($user->email) {
                    try {
                        Mail::to($user->email)
                            ->queue(new ThingDescriptionUpdated($thing, $currentUser, $descriptionText, $isNew));
                        Log::info("Email уведомление об описании поставлено в очередь для {$user->email}");
                        
                    } catch (\Exception $e) {
                        Log::error("Ошибка отправки email для {$user->email}: " . $e->getMessage());
                    }
                }
                
                // уведомление в системе 
                try {
                    DescriptionNotification::create([
                        'user_id' => $user->id,
                        'thing_id' => $thing->id,
                        'from_user_id' => $currentUser->id,
                        'type' => $isNew ? 'description_added' : 'description_updated',
                        'title' => $isNew ? 'Добавлено описание' : 'Обновлено описание',
                        'message' => ($isNew ? 'Добавлено новое описание для вещи: ' : 'Обновлено описание вещи: ') . $thing->name,
                        'read' => false,
                    ]);
                    
                    Log::info("Системное уведомление создано для пользователя {$user->id} (вещь: {$thing->name})");
                    
                } catch (\Exception $e) {
                    Log::error("Ошибка создания системного уведомления для {$user->id}: " . $e->getMessage());
                }
            }

            return true;
            
        } catch (\Exception $e) {
            Log::error('Ошибка при отправке уведомлений об описании вещи ' . $thing->id . ': ' . $e->getMessage());
            return false;
        }
    }
}