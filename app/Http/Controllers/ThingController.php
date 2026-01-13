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
        $things = Cache::remember('things_all', 300, function () {
            return Thing::with(['owner', 'usages.user', 'usages.place', 'usages.unit', 'descriptions' => function($query) {
                $query->where('is_current', true);
            }])
            ->latest()
            ->paginate(10);
        });
        
        return view('things.index', compact('things'));
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

        if ($request->place_id) {
            UseModel::create([
                'thing_id' => $thing->id,
                'user_id' => Auth::id(),
                'place_id' => $request->place_id,
                'amount' => 1, // или нужное количество
                'unit_id' => 1, // или другой unit
            ]);
        }
        
        // ОТПРАВКА СОБЫТИЯ В PUSHER
        broadcast(new ThingCreated($thing, Auth::user()));

        Cache::forget('things_all');
        
        return redirect()->route('things.index')
            ->with('success', 'Вещь успешно создана!');
    }

    public function show(Thing $thing)
    {
        $thing->load(['owner', 'usages.user', 'usages.place', 'usages.unit', 'descriptions.creator']);
        $currentUsage = $thing->usages()->latest()->first();
        
        return view('things.show', compact('thing', 'currentUsage'));
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

        // Сохраняем старое описание для проверки изменений
        $oldDescription = $thing->description;

        $thing->update($request->only(['name', 'description', 'wrnt']));

        // Отправляем уведомление если изменилось описание в основном поле
        if ($oldDescription !== $request->description && $request->description) {
            $this->sendDescriptionNotifications($thing, $request->description, false);
        }

        Cache::forget('things_all');

        return redirect()->route('things.index')
            ->with('success', 'Вещь успешно обновлена!');
    }

    public function destroy(Thing $thing)
    {
        $this->authorize('delete', $thing);
        $thing->delete();
        Cache::forget('things_all');

        return redirect()->route('things.index')
            ->with('success', 'Вещь успешно удалена!');
    }

    public function my()
    {
        $things = Cache::remember('things_my_' . Auth::id(), 300, function () {
            return Thing::with(['usages.user', 'usages.place', 'usages.unit', 'descriptions' => function($query) {
                $query->where('is_current', true);
            }])
            ->where('master', Auth::id())
            ->latest()
            ->paginate(10);
        });
        
        return view('things.my', compact('things'));
    }

    public function borrowed()
    {
        $usages = UseModel::where('user_id', Auth::id())
            ->with(['thing.owner', 'thing.descriptions' => function($query) {
                $query->where('is_current', true);
            }, 'place', 'unit'])
            ->latest()
            ->paginate(10);
        
        return view('things.borrowed', compact('usages'));
    }

    public function repair()
    {
        $things = Cache::remember('things_repair', 300, function () {
            return Thing::with(['owner', 'usages.place', 'usages.unit', 'descriptions' => function($query) {
                $query->where('is_current', true);
            }])
            ->whereHas('usages', function($query) {
                $query->whereHas('place', function($q) {
                    $q->where('repair', true);
                });
            })
            ->latest()
            ->paginate(10);
        });
        
        return view('things.repair', compact('things'));
    }

    public function work()
    {
        $things = Cache::remember('things_work', 300, function () {
            return Thing::with(['owner', 'usages.place', 'usages.unit', 'descriptions' => function($query) {
                $query->where('is_current', true);
            }])
            ->whereHas('usages', function($query) {
                $query->whereHas('place', function($q) {
                    $q->where('work', true);
                });
            })
            ->latest()
            ->paginate(10);
        });
        
        return view('things.work', compact('things'));
    }

    public function used()
    {
        $things = Cache::remember('things_used', 300, function () {
            return Thing::with(['usages.user', 'usages.place', 'usages.unit', 'descriptions' => function($query) {
                $query->where('is_current', true);
            }])
            ->whereHas('usages')
            ->where('master', Auth::id())
            ->whereHas('usages', function($query) {
                $query->where('user_id', '!=', Auth::id());
            })
            ->latest()
            ->paginate(10);
        });
        
        return view('things.used', compact('things'));
    }

    public function all()
    {
        $this->authorize('viewAll', Thing::class);
        
        $things = Cache::remember('things_admin_all', 300, function () {
            return Thing::with(['owner', 'usages.user', 'usages.place', 'usages.unit', 'descriptions' => function($query) {
                $query->where('is_current', true);
            }])
            ->latest()
            ->paginate(20);
        });
        
        return view('things.admin-all', compact('things'));
    }

    public function addDescription(Request $request, Thing $thing)
    {
        $this->authorize('update', $thing);
        
        $request->validate([
            'description' => 'required|string|min:3'
        ]);

        // Сбрасываем текущий статус у всех описаний этой вещи
        $thing->descriptions()->update(['is_current' => false]);
        
        // Создаем новое описание как текущее
        $thing->descriptions()->create([
            'description' => $request->description,
            'is_current' => true,
            'created_by' => Auth::id()
        ]);

        // ОБНОВЛЯЕМ ОСНОВНОЕ ОПИСАНИЕ ВЕЩИ
        $thing->update(['description' => $request->description]);

        // Отправляем email уведомления
        $this->sendDescriptionNotifications($thing, $request->description, true);
        //discription
        $this->sendDescriptionNotificationsNew($thing, $request->description, true);
        // Очищаем кэш
        Cache::forget('things_all');
        Cache::forget('things_my_' . Auth::id());
        Cache::forget('things_used');
        Cache::forget('things_repair');
        Cache::forget('things_work');
        Cache::forget('things_admin_all');

        return back()->with('success', 'Описание успешно добавлено!');
    }

    public function updateDescription(Request $request, Thing $thing, Description $description)
    {
        $this->authorize('update', $thing);
        
        if ($description->thing_id != $thing->id) {
            abort(403, 'Описание не принадлежит этой вещи');
        }

        $request->validate([
            'new_description' => 'required|string|min:3'
        ]);

        // Сохраняем старое описание для сравнения
        $oldDescription = $description->description;

        // Обновляем описание
        $description->update([
            'description' => $request->new_description,
            'is_current' => true,
            'created_by' => Auth::id()
        ]);

        // Сбрасываем текущий статус у других описаний
        $thing->descriptions()
            ->where('id', '!=', $description->id)
            ->update(['is_current' => false]);

        // ОБНОВЛЯЕМ ОСНОВНОЕ ОПИСАНИЕ ВЕЩИ
        $thing->update(['description' => $request->new_description]);

        // Отправляем email уведомления только если описание действительно изменилось
        if ($oldDescription !== $request->new_description) {
            $this->sendDescriptionNotifications($thing, $request->new_description, false);
        }

        // Очищаем кэш
        Cache::forget('things_all');
        Cache::forget('things_my_' . Auth::id());
        Cache::forget('things_used');
        Cache::forget('things_repair');
        Cache::forget('things_work');
        Cache::forget('things_admin_all');

        return back()->with('success', 'Описание успешно обновлено!');
    }

    public function setCurrentDescription(Request $request, Thing $thing, Description $description)
    {
        $this->authorize('update', $thing);
        
        if ($description->thing_id != $thing->id) {
            abort(403, 'Описание не принадлежит этой вещи');
        }

        // Сбрасываем текущий статус у всех описаний
        $thing->descriptions()->update(['is_current' => false]);
        
        // Устанавливаем выбранное как текущее
        $description->update(['is_current' => true]);
        
        // ОБНОВЛЯЕМ ОСНОВНОЕ ОПИСАНИЕ ВЕЩИ
        $thing->update(['description' => $description->description]);

        Cache::forget('things_all');
        Cache::forget('things_my_' . Auth::id());
        Cache::forget('things_used');
        Cache::forget('things_repair');
        Cache::forget('things_work');
        Cache::forget('things_admin_all');

        return back()->with('success', 'Текущее описание обновлено!');
    }

    public function transfer(Request $request, Thing $thing)
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

        UseModel::where('thing_id', $thing->id)->delete();

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

        $emailSent = false;
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
            Log::error('Trace: ' . $e->getTraceAsString());
        }

        Cache::forget('things_all');
        Cache::forget('things_used');
        Cache::forget('things_repair');
        Cache::forget('things_work');
        Cache::forget('things_my_' . Auth::id());

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

    public function return(Thing $thing)
    {
        $usage = $thing->usages()->latest()->first();
        
        if (!$usage) {
            return back()->with('error', 'Эта вещь не находится в пользовании!');
        }

        DB::table('uses')
            ->where('thing_id', $usage->thing_id)
            ->where('place_id', $usage->place_id)
            ->where('user_id', $usage->user_id)
            ->delete();

        Cache::forget('things_all');
        Cache::forget('things_used');
        Cache::forget('things_repair');
        Cache::forget('things_work');

        return redirect()->route('things.show', $thing)
            ->with('success', 'Вещь успешно возвращена!');
    }

    public function transferForm(Thing $thing)
    {
        if ($thing->master != Auth::id()) {
            abort(403, 'Только владелец может передавать вещь!');
        }

        $users = User::all();
        $places = Place::where('repair', false)->where('work', false)->get();
        $units = Unit::all();
        
        return view('things.transfer', compact('thing', 'users', 'places', 'units'));
    }

    /**
     * Отправка email уведомлений при изменении описания вещи
     *
     * @param Thing $thing Вещь
     * @param string $descriptionText Текст описания
     * @param bool $isNew Это новое описание (true) или обновление (false)
     * @return bool
     */
    private function sendDescriptionNotifications(Thing $thing, $descriptionText, $isNew)
{
    try {
        // Получаем пользователей для уведомления
        $usersToNotify = collect();
        $currentUser = Auth::user();

        // 1. Хозяин вещи (master) - если это не сам пользователь
        if ($thing->master != $currentUser->id) {
            $owner = $thing->owner;
            if ($owner && $owner->email) {
                $usersToNotify->push($owner);
            }
        }

        // 2. Текущий пользователь, у которого вещь - если есть и не сам пользователь
        $currentUsage = $thing->currentUsage(); // Используем метод, а не свойство
        if ($currentUsage && $currentUsage->user_id != $currentUser->id) {
            $assignedUser = $currentUsage->user; // Используем отношение через usage
            if ($assignedUser && $assignedUser->email) {
                $usersToNotify->push($assignedUser);
            }
        }

        // Если некому отправлять, выходим
        if ($usersToNotify->isEmpty()) {
            Log::info("Нет получателей для email уведомления об описании вещи {$thing->id}");
            return true;
        }

        // Отправляем email каждому пользователю
        foreach ($usersToNotify as $user) {
            try {
                // Используем queue для асинхронной отправки
                Mail::to($user->email)
                    ->queue(new ThingDescriptionUpdated(
                        $thing, 
                        $currentUser, 
                        $descriptionText, 
                        $isNew
                    ));
                
                Log::info("Email уведомление об описании поставлено в очередь для {$user->email} (вещь: {$thing->name})");
                
            } catch (\Exception $e) {
                Log::error("Ошибка отправки email для {$user->email}: " . $e->getMessage());
            }
        }

        return true;
        
    } catch (\Exception $e) {
        Log::error('Ошибка при отправке уведомлений об описании вещи ' . $thing->id . ': ' . $e->getMessage());
        return false;
    }
}
    /**
 * Отправка уведомлений через Notification (ЗАДАНИЕ 19)
 */
private function sendDescriptionNotificationsNew(Thing $thing, $descriptionText, $isNew)
{
    $usersToNotify = collect();
    $currentUser = Auth::user();

    // Хозяин вещи
    if ($thing->master != $currentUser->id) {
        $owner = $thing->owner;
        if ($owner) {
            $usersToNotify->push($owner);
        }
    }

    // Текущий пользователь вещи
    $currentUsage = $thing->currentUsage();
    if ($currentUsage && $currentUsage->user_id != $currentUser->id) {
        $assignedUser = $currentUsage->user;
        if ($assignedUser) {
            $usersToNotify->push($assignedUser);
        }
    }

    // Сохраняем в НОВУЮ таблицу
    foreach ($usersToNotify as $user) {
        DescriptionNotification::create([
            'user_id' => $user->id,
            'thing_id' => $thing->id,
            'from_user_id' => $currentUser->id,
            'type' => 'description_updated',
            'title' => $isNew ? 'Добавлено описание' : 'Обновлено описание',
            'message' => ($isNew ? 'Добавлено новое описание для вещи: ' : 'Обновлено описание вещи: ') . $thing->name,
        ]);
    }
}
}