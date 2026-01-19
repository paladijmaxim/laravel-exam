<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Thing;
use App\Models\UseModel;
use App\Models\User;
use App\Models\Place;
use App\Models\Unit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class ThingController extends Controller
{
    public function index(Request $request)
    {
        $query = Thing::with(['owner', 'usages' => function($query) {
            $query->latest()->take(1)->with(['user', 'place', 'unit']);
        }, 'descriptions' => function($query) {
            $query->where('is_current', true);
        }])
        ->whereDoesntHave('usages.place', function($q) {
            $q->where('repair', true)->orWhere('work', true);
        })
        ->latest();

        // фильтрация по поиску
        if ($request->has('search')) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%');
            });
        }

        // пагинация с сохранением параметров запроса
        $perPage = $request->get('per_page', 10);
        $things = $query->paginate($perPage)->withQueryString(); 

        return response()->json([
            'data' => $things->items(),
            'meta' => [
                'current_page' => $things->currentPage(),
                'last_page' => $things->lastPage(),
                'per_page' => $things->perPage(),
                'total' => $things->total(),
                'next_page_url' => $things->nextPageUrl(),
                'prev_page_url' => $things->previousPageUrl(),
                'path' => $things->path(),
            ]
        ]);
    }

    public function store(Request $request)
    {
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
                'amount' => $request->amount ?? 1,
                'unit_id' => $request->unit_id,
            ]);
        }

        Cache::forget('things_all');

        return response()->json([
            'message' => 'Вещь успешно создана!',
            'data' => $thing->load(['owner', 'usages.place', 'usages.unit'])
        ], 201);
    }

    public function show($id)
    {
        $thing = Thing::with(['owner', 'usages.user', 'usages.place', 'usages.unit', 'descriptions.creator'])->findOrFail($id);

        return response()->json([
            'data' => $thing,
            'current_usage' => $thing->usages()->latest()->first()
        ]);
    }

    public function update(Request $request, $id)
    {
        $thing = Thing::findOrFail($id);
        
        // проверка прав доступа
        if ($thing->master != Auth::id()) {
            return response()->json(['message' => 'Недостаточно прав'], 403);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'wrnt' => 'nullable|date',
        ]);

        $oldDescription = $thing->description;
        $thing->update($request->only(['name', 'description', 'wrnt']));

        Cache::forget('things_all');
        Cache::forget('things_my_' . Auth::id());

        return response()->json([
            'message' => 'Вещь успешно обновлена!',
            'data' => $thing
        ]);
    }

    public function destroy($id)
    {
        $thing = Thing::findOrFail($id);
        
        if ($thing->master != Auth::id()) {
            return response()->json(['message' => 'Недостаточно прав'], 403);
        }

        $thing->delete();
        Cache::forget('things_all');

        return response()->json([
            'message' => 'Вещь успешно удалена!'
        ]);
    }

    public function myThings(Request $request)
    {
        $things = Thing::with(['usages.user', 'usages.place', 'usages.unit', 'descriptions' => function($query) {
            $query->where('is_current', true);
        }])
        ->where('master', Auth::id())
        ->latest()
        ->paginate($request->get('per_page', 10))
        ->withQueryString();

        return response()->json([
            'data' => $things->items(),
            'meta' => [
                'current_page' => $things->currentPage(),
                'last_page' => $things->lastPage(),
                'per_page' => $things->perPage(),
                'total' => $things->total(),
                'next_page_url' => $things->nextPageUrl(),
                'prev_page_url' => $things->previousPageUrl(),
                'path' => $things->path(),
            ]
        ]);
    }

    public function borrowedThings(Request $request) // взятые вещи
    {
        $usages = UseModel::where('user_id', Auth::id())
            ->with(['thing.owner', 'thing.descriptions' => function($query) {
                $query->where('is_current', true);
            }, 'place', 'unit'])
            ->latest()
            ->paginate($request->get('per_page', 10))
            ->withQueryString();

        return response()->json([
            'data' => $usages->items(),
            'meta' => [
                'current_page' => $usages->currentPage(),
                'last_page' => $usages->lastPage(),
                'per_page' => $usages->perPage(),
                'total' => $usages->total(),
                'next_page_url' => $usages->nextPageUrl(),
                'prev_page_url' => $usages->previousPageUrl(),
                'path' => $usages->path(),
            ]
        ]);
    }

    public function transfer(Request $request, $id)
    {
        $thing = Thing::findOrFail($id);
        
        if ($thing->master != Auth::id()) {
            return response()->json(['message' => 'Только владелец может передавать вещь'], 403);
        }

        $request->validate([
            'user_id' => 'required|exists:users,id',
            'place_id' => 'required|exists:places,id',
            'amount' => 'required|integer|min:1',
            'unit_id' => 'nullable|exists:units,id',
        ]);

        $recipient = User::findOrFail($request->user_id);
        $place = Place::findOrFail($request->place_id);

        UseModel::where('thing_id', $thing->id)->where('user_id', Auth::id())->delete();

        // создать новое использование
        UseModel::create([
            'thing_id' => $thing->id,
            'place_id' => $request->place_id,
            'user_id' => $request->user_id,
            'amount' => $request->amount,
            'unit_id' => $request->unit_id,
        ]);

        Cache::forget('things_all');
        Cache::forget('things_used');

        return response()->json([
            'message' => 'Вещь успешно передана в пользование!',
            'data' => [
                'thing' => $thing,
                'recipient' => $recipient,
                'place' => $place
            ]
        ]);
    }

    public function returnThing($id) // возврат вещи
    {
        $thing = Thing::findOrFail($id);
        $usage = $thing->usages()->latest()->first();
        
        if (!$usage) {
            return response()->json(['message' => 'Эта вещь не в пользовании'], 400);
        }

        DB::table('uses')
            ->where('thing_id', $usage->thing_id)
            ->where('place_id', $usage->place_id)
            ->where('user_id', $usage->user_id)
            ->delete();

        Cache::forget('things_all');
        Cache::forget('things_used');

        return response()->json([
            'message' => 'Вещь успешно возвращена!'
        ]);
    }

    public function addDescription(Request $request, $id)
    {
    
        $this->authorize('update', $thing);
        
        $thing = Thing::findOrFail($id);

        $request->validate([
            'description' => 'required|string|min:3'
        ]);

        // сброс текущий статус у всех описаний этой вещи
        $thing->descriptions()->update(['is_current' => false]);
        
        // новое описание как текущее
        $newDescription = $thing->descriptions()->create([
            'description' => $request->description,
            'is_current' => true,
            'created_by' => Auth::id()
        ]);

        // обнов основное описание вещи
        $thing->update(['description' => $request->description]);

        Cache::forget('things_all');
        Cache::forget('things_my_' . Auth::id());

        return response()->json([
            'message' => 'Описание успешно добавлено!',
            'data' => $newDescription
        ]);
    }

    public function adminAll(Request $request)
    {
        if (!Auth::user()->is_admin) {
            return response()->json(['message' => 'Недостаточно прав'], 403);
        }

        $things = Thing::withTrashed() // вкл удаленные
            ->with([
                'owner',
                'usages' => function($query) {
                    $query->latest()->take(1)->with(['user', 'place', 'unit']);
                },
                'descriptions' => function($query) {
                    $query->where('is_current', true);
                }
            ])
            ->latest()
            ->paginate($request->get('per_page', 20))
            ->withQueryString();

        return response()->json([
            'data' => $things->items(),
            'meta' => [
                'current_page' => $things->currentPage(),
                'last_page' => $things->lastPage(),
                'per_page' => $things->perPage(),
                'total' => $things->total(),
                'next_page_url' => $things->nextPageUrl(),
                'prev_page_url' => $things->previousPageUrl(),
                'path' => $things->path(),
            ]
        ]);
    }

    public function repair(Request $request)
    {
        $query = Thing::with(['owner', 'usages.place', 'usages.unit', 'descriptions' => function($query) {
            $query->where('is_current', true);
        }])
        ->whereHas('usages', function($query) {
            $query->whereHas('place', function($q) {
                $q->where('repair', true);
            });
        })
        ->latest();

        $perPage = $request->get('per_page', 10);
        $things = $query->paginate($perPage)->withQueryString();

        return response()->json([
            'data' => $things->items(),
            'meta' => [
                'current_page' => $things->currentPage(),
                'last_page' => $things->lastPage(),
                'per_page' => $things->perPage(),
                'total' => $things->total(),
                'next_page_url' => $things->nextPageUrl(),
                'prev_page_url' => $things->previousPageUrl(),
                'path' => $things->path(),
            ]
        ]);
    }

    public function work(Request $request)
    {
        $query = Thing::with(['owner', 'usages.place', 'usages.unit', 'descriptions' => function($query) {
            $query->where('is_current', true);
        }])
        ->whereHas('usages', function($query) {
            $query->whereHas('place', function($q) {
                $q->where('work', true);
            });
        })
        ->latest();

        $perPage = $request->get('per_page', 10);
        $things = $query->paginate($perPage)->withQueryString();

        return response()->json([
            'data' => $things->items(),
            'meta' => [
                'current_page' => $things->currentPage(),
                'last_page' => $things->lastPage(),
                'per_page' => $things->perPage(),
                'total' => $things->total(),
                'next_page_url' => $things->nextPageUrl(),
                'prev_page_url' => $things->previousPageUrl(),
                'path' => $things->path(),
            ]
        ]);
    }

    public function used(Request $request)
    {
        $query = Thing::with(['usages.user', 'usages.place', 'usages.unit', 'descriptions' => function($query) {
            $query->where('is_current', true);
        }])
        ->whereHas('usages')
        ->where('master', Auth::id())
        ->whereHas('usages', function($query) {
            $query->where('user_id', '!=', Auth::id());
        })
        ->latest();

        $perPage = $request->get('per_page', 10);
        $things = $query->paginate($perPage)->withQueryString(); 

        return response()->json([
            'data' => $things->items(),
            'meta' => [
                'current_page' => $things->currentPage(),
                'last_page' => $things->lastPage(),
                'per_page' => $things->perPage(),
                'total' => $things->total(),
                'next_page_url' => $things->nextPageUrl(),
                'prev_page_url' => $things->previousPageUrl(),
                'path' => $things->path(),
            ]
        ]);
    }
}