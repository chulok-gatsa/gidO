<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{

    // тут регистрация //
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'login' => 'required|string|max:255|unique:users',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
        ], [
            'name.required' => 'Поле "Имя" обязательно для заполнения',
            'login.required' => 'Поле "Логин" обязательно для заполнения',
            'login.unique' => 'Пользователь с таким логином уже существует',
            'email.required' => 'Поле "Email" обязательно для заполнения',
            'email.email' => 'Поле "Email" должно быть валидным email адресом',
            'email.unique' => 'Пользователь с таким email уже существует',
            'password.required' => 'Поле "Пароль" обязательно для заполнения',
            'password.min' => 'Пароль должен содержать не менее 6 символов',
            'password.confirmed' => 'Пароли не совпадают',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        // тут создаем пользователя //
        $user = User::create([
            'name' => $request->name,
            'login' => $request->login,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        // тут авторизуем пользователя //
        Auth::login($user);
        $request->session()->regenerate();

        return response()->json([
            'success' => true,
            'message' => 'Регистрация успешна',
            'user' => $user
        ]);
    }

    // тут выводим форму входа //
    public function showLoginForm()
    {
        return view('login');
    }

    // тут авторизация //
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'login' => 'required|string',
            'password' => 'required|string',
        ], [
            'login.required' => 'Поле "Логин" обязательно для заполнения',
            'password.required' => 'Поле "Пароль" обязательно для заполнения',
        ]);

        // тут проверяем авторизацию //
        if (Auth::attempt(['login' => $request->login, 'password' => $request->password], $request->filled('remember'))) {
            $request->session()->regenerate();
            
            return response()->json([
                'success' => true,
                'message' => 'Вход выполнен успешно',
                'user' => Auth::user()
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Предоставленные учетные данные не соответствуют нашим записям'
        ], 401);
    }

    // тут выход //
    public function logout(Request $request)
    {
        Auth::logout();
        
        return response()->json([
            'success' => true,
            'message' => 'Выход выполнен успешно'
        ]);
    }

    // тут получаем данные текущего пользователя //
    public function getCurrentUser()
    {
        if (Auth::check()) {
            return response()->json([
                'success' => true,
                'user' => Auth::user()
            ]);
        }
        
        return response()->json([
            'success' => false,
            'message' => 'Пользователь не авторизован'
        ], 401);
    }

    // тут обновление пароля //
    public function updatePassword(Request $request)
    {
        if (!Auth::check()) {
            return response()->json([
                'success' => false,
                'message' => 'Пользователь не авторизован'
            ], 401);
        }

        // тут получаем пользователя //
        $user = Auth::user();
        
        $validator = Validator::make($request->all(), [
            'current_password' => 'required',
            'password' => 'required|string|min:6|confirmed',
        ], [
            'current_password.required' => 'Поле "Текущий пароль" обязательно для заполнения',
            'password.required' => 'Поле "Новый пароль" обязательно для заполнения',
            'password.min' => 'Новый пароль должен содержать не менее 6 символов',
            'password.confirmed' => 'Пароли не совпадают',
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        // тут проверяем пароль //
        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Текущий пароль указан неверно'
            ], 422);
        }

        // тут обновляем пароль //
        $user->password = Hash::make($request->password);
        $user->save();
        
        return response()->json([
            'success' => true,
            'message' => 'Пароль успешно изменен'
        ]);
    }
}
