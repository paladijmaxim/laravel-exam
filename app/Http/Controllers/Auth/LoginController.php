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
            'remember' => 'boolean',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['Неверные учетные данные.'],
            ]);
        }

        // 1. Аутентифицируем через сессии (web guard)
        Auth::guard('web')->login($user, $request->filled('remember'));
        $request->session()->regenerate();

        // 2. Создаем токен Sanctum
        $token = $this->createSanctumToken($user, $request->filled('remember'));
        
        // 3. Сохраняем токен в сессии
        session(['sanctum_token' => $token]);

        // 4. Подготавливаем ответ с кукой
        $response = redirect()->route('dashboard')
            ->with('success', 'Вход выполнен успешно!');
            
        // 5. Устанавливаем куку с токеном
        if ($request->filled('remember')) {
            $response->withCookie(cookie('sanctum_token', $token, 60 * 24 * 30)); // 30 дней
        } else {
            $response->withCookie(cookie('sanctum_token', $token)); // Сессионная кука
        }

        return $response;
    }
    
    /**
     * Создает токен Sanctum
     */
    private function createSanctumToken(User $user, bool $remember = false): string
    {
        $tokenName = $remember ? 'web-token-remember' : 'web-token';
        
        // Удаляем старые токены того же типа
        $user->tokens()
            ->where('name', $tokenName)
            ->delete();
        
        if ($remember) {
            // Токен с истечением срока через 30 дней
            $expiresAt = now()->addDays(30);
            return $user->createToken($tokenName, ['*'], $expiresAt)->plainTextToken;
        } else {
            // Токен без срока истечения
            return $user->createToken($tokenName)->plainTextToken;
        }
    }
}