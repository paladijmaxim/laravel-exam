<?php

namespace App\Http\Controllers;

use App\Models\Thing;
use Illuminate\View\View;

class WelcomeController extends Controller
{
    public function index(): View
    {
        $totalThings = Thing::count();
        
        $recentThings = Thing::with(['owner', 'usages' => function($q) {
            $q->latest()->take(1)->with(['user', 'place']);
        }])
        ->whereDoesntHave('usages.place', function($q) {
            $q->where('repair', true)->orWhere('work', true);
        })
        ->latest()
        ->take(5)
        ->get();
        
        return view('welcome', compact('totalThings', 'recentThings'));
    }
}