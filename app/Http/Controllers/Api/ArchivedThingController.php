<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ArchivedThing;
use App\Models\Thing;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ArchivedThingController extends Controller
{
    /**
     * Display a listing of archived things
     */
    public function index(Request $request)
    {
        $query = ArchivedThing::query();
        
        // Фильтрация по поиску
        if ($request->has('search')) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%')
                  ->orWhere('owner_name', 'like', '%' . $request->search . '%');
            });
        }
        
        // Сортировка
        $sortBy = $request->get('sort_by', 'deleted_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);
        
        // Пагинация
        $perPage = $request->get('per_page', 15);
        $archivedThings = $query->paginate($perPage);

        return response()->json([
            'data' => $archivedThings->items(),
            'meta' => [
                'current_page' => $archivedThings->currentPage(),
                'last_page' => $archivedThings->lastPage(),
                'per_page' => $archivedThings->perPage(),
                'total' => $archivedThings->total(),
            ]
        ]);
    }

    /**
     * Display the specified archived thing
     */
    public function show($id)
    {
        $archivedThing = ArchivedThing::findOrFail($id);

        return response()->json([
            'data' => $archivedThing
        ]);
    }

    /**
     * Restore thing from archive
     */
    public function restore($id)
    {
        // Проверка прав доступа
        if (!Auth::user()->isAdmin()) {
            return response()->json([
                'message' => 'Только администратор может восстанавливать вещи из архива!'
            ], 403);
        }

        $archivedThing = ArchivedThing::findOrFail($id);
        
        if ($archivedThing->restored) {
            return response()->json([
                'message' => 'Эта вещь уже была восстановлена ранее.'
            ], 400);
        }

        // Восстанавливаем вещь
        $thing = Thing::restoreFromArchive($archivedThing, Auth::user());

        return response()->json([
            'message' => 'Вещь успешно восстановлена! Вы стали ее новым владельцем.',
            'data' => [
                'archived_thing' => $archivedThing,
                'restored_thing' => $thing
            ]
        ]);
    }

    /**
     * Permanently delete from archive (force delete)
     */
    public function forceDelete($id)
    {
        // Проверка прав доступа
        if (!Auth::user()->isAdmin()) {
            return response()->json([
                'message' => 'Только администратор может удалять записи из архива навсегда!'
            ], 403);
        }

        $archivedThing = ArchivedThing::findOrFail($id);
        $name = $archivedThing->name;
        $archivedThing->forceDelete();

        return response()->json([
            'message' => "Запись '{$name}' полностью удалена из архива."
        ]);
    }

    /**
     * Get statistics about archived things
     */
    public function stats()
    {
        $total = ArchivedThing::count();
        $restored = ArchivedThing::where('restored', true)->count();
        $notRestored = ArchivedThing::where('restored', false)->count();

        return response()->json([
            'data' => [
                'total' => $total,
                'restored' => $restored,
                'not_restored' => $notRestored,
                'restored_percentage' => $total > 0 ? round(($restored / $total) * 100, 2) : 0
            ]
        ]);
    }
}