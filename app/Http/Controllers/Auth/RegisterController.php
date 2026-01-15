<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class RegisterController extends Controller
{
    public function showRegistrationForm()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        // 1. Аутентифицируем через сессии
        Auth::guard('web')->login($user);
        $request->session()->regenerate();

        // 2. Создаем токен Sanctum
        $token = $user->createToken('web-token')->plainTextToken;
        
        // 3. Сохраняем токен в сессии
        session(['sanctum_token' => $token]);

        return redirect()->route('dashboard')
            ->with('success', 'Регистрация успешна!')
            ->withCookie(cookie('sanctum_token', $token)); // Сессионная кука
    }
}