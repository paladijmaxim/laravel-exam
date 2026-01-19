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

        // аутентифицируем через сессии
        Auth::guard('web')->login($user);
        $request->session()->regenerate();

        // создаем токен Sanctum
        $token = $user->createToken('web-token')->plainTextToken; // возвращает plain text токен (не хэшированный)
        
        // сохраняем токен в сессии
        session(['sanctum_token' => $token]); // хранить токен безопасно на сервере

        return redirect()->route('dashboard')
            ->with('success', 'Регистрация успешна!'); 
    }
}