<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Place;
use App\Models\UseModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class PlaceController extends Controller
{
    public function index(Request $request)
    {
        // Кэширование на 5 минут
        $places = Cache::remember('places_api_all', 300, function () {
            return Place::withCount('usages')
                       ->latest()
                       ->paginate($request->get('per_page', 10));
        });
        
        return response()->json([
            'data' => $places->items(),
            'meta' => [
                'current_page' => $places->currentPage(),
                'last_page' => $places->lastPage(),
                'per_page' => $places->perPage(),
                'total' => $places->total(),
            ]
        ]);
    }

    public function store(Request $request)
    {
        // Проверка прав
        if (!Auth::user()->isAdmin()) {
            return response()->json([
                'message' => 'Нет прав'
            ], 403);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'repair' => 'boolean',
            'work' => 'boolean',
        ]);

        $place = Place::create([
            'name' => $request->name,
            'description' => $request->description,
            'repair' => $request->boolean('repair', false),
            'work' => $request->boolean('work', false),
        ]);

        Cache::forget('places_api_all'); // очистка кеша, чтобы данные удалились 
        Cache::forget('places_available_api');

        return response()->json([
            'message' => 'Место хранения успешно создано!',
            'data' => $place
        ], 201);
    }

    public function show($id)
    {
        $place = Place::with(['usages.thing.owner', 'usages.user', 'usages.unit'])->findOrFail($id);
        return response()->json([
            'data' => $place
        ]);
    }

    public function update(Request $request, $id)
    {
        $place = Place::findOrFail($id);
        
        // Проверка прав
        if (!Auth::user()->isAdmin()) {
            return response()->json([
                'message' => 'Недостаточно прав для обновления мест!'
            ], 403);
        }

        $request->validate([
            'name' => 'string|max:255',
            'description' => 'nullable|string',
            'repair' => 'boolean',
            'work' => 'boolean',
        ]);

        $place->update([
            'name' => $request->input('name', $place->name),
            'description' => $request->input('description', $place->description),
            'repair' => $request->boolean('repair', $place->repair),
            'work' => $request->boolean('work', $place->work),
        ]);

        Cache::forget('places_api_all');
        Cache::forget('places_available_api');

        return response()->json([
            'message' => 'Место хранения успешно обновлено!',
            'data' => $place
        ]);
    }

    public function destroy($id)
    {
        $place = Place::findOrFail($id);
        
        // Проверка прав
        if (!Auth::user()->isAdmin()) {
            return response()->json([
                'message' => 'Недостаточно прав для удаления мест!'
            ], 403);
        }

        // Проверяем, нет ли вещей в этом месте
        if ($place->usages()->exists()) { // проверяет, есть ли связанные записи в таблице uses
            return response()->json([
                'message' => 'Нельзя удалить место хранения, в котором есть вещи!'
            ], 400);
        }

        $place->delete();

        Cache::forget('places_api_all');
        Cache::forget('places_available_api');

        return response()->json([
            'message' => 'Место хранения успешно удалено!'
        ]);
    }

    public function available() // доступные места
    {
        $places = Cache::remember('places_available_api', 300, function () {
            return Place::where('repair', false)
                ->where('work', false)
                ->get();
        });
        
        return response()->json([
            'data' => $places
        ]);
    }

    public function stats()
    {
        $total = Place::count();
        $inRepair = Place::where('repair', true)->count();
        $inWork = Place::where('work', true)->count();
        $available = Place::where('repair', false)->where('work', false)->count();
        $withItems = Place::has('usages')->count();

        return response()->json([
            'data' => [
                'total' => $total,
                'in_repair' => $inRepair,
                'in_work' => $inWork,
                'available' => $available,
                'with_items' => $withItems,
                'empty' => $total - $withItems
            ]
        ]);
    }
}