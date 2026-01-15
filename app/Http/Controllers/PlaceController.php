<?php

namespace App\Http\Controllers;

use App\Models\Place;
use App\Models\UseModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Gate;
use App\Events\PlaceCreated;
use Illuminate\Support\Facades\Auth;

class PlaceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Кэширование мест на 5 минут с учетом параметров запроса
        $places = Cache::remember('places_all_' . md5(request()->getQueryString()), 300, function () {
            return Place::withCount('usages')
                       ->latest()
                       ->paginate(10)
                       ->withQueryString(); // ← ДОБАВЛЕНО
        });
        
        return view('places.index', compact('places'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $this->authorize('create', Place::class);
        return view('places.create');
    }

    /**
     * Store a newly created resource in storage.
     */
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
        
        // Отправляем событие
        broadcast(new PlaceCreated($place, Auth::user()));

        // Очищаем все кэши мест
        $this->clearAllPlaceCaches();

        return redirect()->route('places.index')
            ->with('success', 'Место хранения успешно создано!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Place $place)
    {
        $place->load(['usages.thing.owner', 'usages.user', 'usages.unit']);
        
        // Пагинация для использования вещей в этом месте
        $usages = $place->usages()
            ->with(['thing.owner', 'user', 'unit'])
            ->latest()
            ->paginate(10)
            ->withQueryString(); // ← ДОБАВЛЕНО, если на странице есть пагинация
        
        return view('places.show', compact('place', 'usages'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Place $place)
    {
        $this->authorize('update', $place);
        return view('places.edit', compact('place'));
    }

    /**
     * Update the specified resource in storage.
     */
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

        // Очищаем все кэши мест
        $this->clearAllPlaceCaches();

        return redirect()->route('places.index')
            ->with('success', 'Место хранения успешно обновлено!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Place $place)
    {
        $this->authorize('delete', $place);
        
        // Проверяем, нет ли вещей в этом месте
        if ($place->usages()->exists()) {
            return redirect()->route('places.index')
                ->with('error', 'Нельзя удалить место хранения, в котором есть вещи!');
        }

        $place->delete();

        // Очищаем все кэши мест
        $this->clearAllPlaceCaches();

        return redirect()->route('places.index')
            ->with('success', 'Место хранения успешно удалено!');
    }

    /**
     * Список доступных мест (не на ремонте и не в работе)
     */
    public function available()
    {
        $places = Cache::remember('places_available', 300, function () {
            return Place::where('repair', false)
                ->where('work', false)
                ->get();
        });
        
        return response()->json($places);
    }

    /**
     * Метод для очистки всех кэшей мест
     */
    private function clearAllPlaceCaches()
    {
        Cache::forget('places_all'); // Основной кэш
        
        // Также удаляем кэши с параметрами запроса
        $keys = Cache::get('*places_all_*');
        if ($keys) {
            foreach ($keys as $key) {
                Cache::forget($key);
            }
        }
        
        Cache::forget('places_available'); // Доступные места
    }
}