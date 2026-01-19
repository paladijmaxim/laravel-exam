<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['Неверные учетные данные.'],
            ]);
        }

        //  Аутентифицируем через сессии (web guard)
        Auth::guard('web')->login($user);
        $request->session()->regenerate(); // создание новой session id

        //  Создаем токен Sanctum
        $token = $this->createSanctumToken($user);
        
        //  Сохраняем токен в сессии
        session(['sanctum_token' => $token]);

        //  Подготавливаем ответ с кукой
        $response = redirect()->route('dashboard')
            ->with('success', 'Вход выполнен успешно!');
        return $response;
    }
    
     // создание токена Sanctum
    private function createSanctumToken(User $user): string
    {
        $tokenName = 'web-token';
        
        // удаление старых токенов того же типа
        $user->tokens()
            ->where('name', $tokenName)
            ->delete();
        
        // токен без срока истечения
        return $user->createToken($tokenName)->plainTextToken;
    }
}