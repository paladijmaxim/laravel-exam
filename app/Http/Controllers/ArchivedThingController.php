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

public function restore(Request $request, $id) // Принимаем ID вместо модели
{
    // нахождение записи даже если она soft deleted
    $archivedThing = ArchivedThing::withTrashed()->findOrFail($id);
    
    if ($archivedThing->restored) {
        return back()->with('error', 'Эта вещь уже была восстановлена ранее.');
    }

    // восстановление вещи (статический метод в модели Thing)
    $thing = Thing::restoreFromArchive($archivedThing, Auth::user());
    
    // редирект на страницу восстановленной веши
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

    $archivedThing = ArchivedThing::withTrashed()->findOrFail($id); // нашли запись
    $name = $archivedThing->name; // сохранили для сообщения пользователю
    $archivedThing->forceDelete();
    
    return redirect()
        ->route('archived.index')
        ->with('success', "Запись '{$name}' полностью удалена из архива.");
}
}