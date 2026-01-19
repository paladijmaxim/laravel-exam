<?php

namespace App\Http\Controllers;

use App\Models\Thing;
use App\Models\Place;
use App\Models\UseModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        // Мои вещи
        $myThings = Thing::where('master', $user->id)
            ->with(['usages.user', 'usages.place'])
            ->latest()
            ->take(5)
            ->get();
        
        // Взятые мной вещи
        $borrowedThings = UseModel::where('user_id', $user->id)
            ->with(['thing.owner', 'place'])
            ->latest()
            ->take(5)
            ->get();
        
        return view('dashboard', compact('myThings', 'borrowedThings')); // compact()  создает массив ['myThings' => $myThings, и тд]
    }
}