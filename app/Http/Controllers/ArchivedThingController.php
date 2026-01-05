<?php

namespace App\Http\Controllers;

use App\Models\ArchivedThing;
use App\Models\Thing;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ArchivedThingController extends Controller
{
    public function index()
    {
        $archivedThings = ArchivedThing::withTrashed()
            ->latest()
            ->paginate(15);
        
        return view('archived.index', compact('archivedThings'));
    }

    // app/Http\Controllers\ArchivedThingController.php
public function restore(Request $request, $id) // Принимаем ID вместо модели
{
    // Находим запись даже если она soft deleted
    $archivedThing = ArchivedThing::withTrashed()->findOrFail($id);
    
    \Log::info('Восстановление вещи', [
        'archived_id' => $id,
        'found' => $archivedThing ? 'YES' : 'NO',
        'restored' => $archivedThing->restored,
        'deleted_at' => $archivedThing->deleted_at
    ]);
    
    if ($archivedThing->restored) {
        return back()->with('error', 'Эта вещь уже была восстановлена ранее.');
    }

    $thing = Thing::restoreFromArchive($archivedThing, Auth::user());
    
    return redirect()
        ->route('things.show', ['thing' => $thing->id])
        ->with('success', 'Вещь успешно восстановлена! Вы стали ее новым владельцем.');
}

public function show($id)
{
    $archivedThing = ArchivedThing::withTrashed()->findOrFail($id);
    return view('archived.show', compact('archivedThing'));
}

public function forceDelete($id)
{
    if (!Auth::user()->isAdmin()) {
        abort(403, 'Только администратор может удалять записи из архива навсегда.');
    }

    $archivedThing = ArchivedThing::withTrashed()->findOrFail($id);
    $name = $archivedThing->name;
    $archivedThing->forceDelete();
    
    return redirect()
        ->route('archived.index')
        ->with('success', "Запись '{$name}' полностью удалена из архива.");
}
}