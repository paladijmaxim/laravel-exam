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
        // ИЗМЕНЕНИЕ: убрать 'deleted_at' из latest()
        $archivedThings = ArchivedThing::latest()  // без параметра
            ->paginate(15);
        
        return view('archived.index', compact('archivedThings'));
    }

    public function show(ArchivedThing $archivedThing)
    {
        return view('archived.show', compact('archivedThing'));
    }

    public function restore(Request $request, ArchivedThing $archivedThing)
    {
        if ($archivedThing->restored) {
            return back()->with('error', 'Эта вещь уже была восстановлена ранее.');
        }

        $thing = Thing::restoreFromArchive($archivedThing, Auth::user());
        
        return redirect()
            ->route('things.show', $thing)
            ->with('success', 'Вещь успешно восстановлена! Вы стали ее новым владельцем.');
    }

    public function forceDelete(ArchivedThing $archivedThing)
    {
        if (!Auth::user()->isAdmin()) {
            abort(403, 'Только администратор может удалять записи из архива навсегда.');
        }

        $name = $archivedThing->name;
        $archivedThing->forceDelete();
        
        return redirect()
            ->route('archived.index')
            ->with('success', "Запись '{$name}' полностью удалена из архива.");
    }
}