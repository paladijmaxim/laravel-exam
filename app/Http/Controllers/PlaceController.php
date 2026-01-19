<?php

namespace App\Http\Controllers;

use App\Models\Place;
use App\Models\UseModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Auth;
use App\Events\PlaceCreated;


class PlaceController extends Controller
{
    public function index()
    {
        // Ключ кеша без параметров запроса - только для списка мест
        $cacheKey = 'places_list_all';
        
        $places = Cache::remember($cacheKey, 300, function () {
            return Place::withCount('usages')
                       ->latest()
                       ->paginate(10);
        });
        
        return view('places.index', compact('places'));
    }

    public function create()
    {
        $this->authorize('create', Place::class);
        return view('places.create');
    }

    public function store(Request $request)
    {
        $this->authorize('create', Place::class);
        
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'repair' => 'boolean',
            'work' => 'boolean',
        ]);

        $place = Place::create([
            'name' => $request->name,
            'description' => $request->description,
            'repair' => $request->has('repair'),
            'work' => $request->has('work'),
            'user_id' => Auth::id(),
        ]);

        broadcast(new PlaceCreated($place, Auth::user()))->toOthers();

        // Очищаем кеш списка мест
        Cache::forget('places_list_all');
        Cache::forget('places_available');

        return redirect()->route('places.index')
            ->with('success', 'Место хранения успешно создано!');
    }

    public function show(Place $place)
    {
        $place->load(['usages.thing.owner', 'usages.user', 'usages.unit']);
        
        $usages = $place->usages()
            ->with(['thing.owner', 'user', 'unit'])
            ->latest()
            ->paginate(10);
        
        return view('places.show', compact('place', 'usages'));
    }

    public function edit(Place $place)
    {
        $this->authorize('update', $place);
        return view('places.edit', compact('place'));
    }

    public function update(Request $request, Place $place)
    {
        $this->authorize('update', $place);
        
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'repair' => 'boolean',
            'work' => 'boolean',
        ]);

        $place->update([
            'name' => $request->name,
            'description' => $request->description,
            'repair' => $request->has('repair'),
            'work' => $request->has('work'),
        ]);

        // Очищаем кеш
        Cache::forget('places_list_all');
        Cache::forget('places_available');

        return redirect()->route('places.index')
            ->with('success', 'Место хранения успешно обновлено!');
    }

    public function destroy(Place $place)
    {
        $this->authorize('delete', $place);
        
        // Проверяем, нет ли вещей в этом месте
        if ($place->usages()->exists()) {
            return redirect()->route('places.index')
                ->with('error', 'Нельзя удалить место хранения, в котором есть вещи!');
        }

        $place->delete();

        // Очищаем кеш
        Cache::forget('places_list_all');
        Cache::forget('places_available');

        // Возвращаемся на страницу со списком мест
        return redirect()->route('places.index')
            ->with('success', 'Место хранения успешно удалено!');
    }

    // Список доступных мест (не на ремонте и не в работе)
    public function available()
    {
        $places = Cache::remember('places_available', 300, function () {
            return Place::where('repair', false)
                ->where('work', false)
                ->get();
        });
        
        return response()->json($places);
    }
}