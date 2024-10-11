<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class TelegramController extends Controller
{
    public function handleCallback(Request $request)
    {
        // Получаем данные от Telegram
        $authData = $request->all();

        return response();
        // Проверяем данные
        if ($this->validateTelegramAuth($authData)) {
            // Данные проверены, можно получить информацию о пользователе
            $user = [
                'id' => $authData['id'],
                'first_name' => $authData['first_name'],
                'last_name' => $authData['last_name'] ?? '',
                'username' => $authData['username'] ?? '',
                'photo_url' => $authData['photo_url'] ?? ''
            ];

            // Сохраняем пользователя или используем для логики приложения
            return response()->json($user);
        } else {
            // Ошибка проверки данных
            return response()->json(['error' => 'Invalid data'], 401);
        }
    }

    private function validateTelegramAuth($authData)
    {
        // Верификация данных на основе hash-подписи
        $checkHash = $authData['hash'];
        unset($authData['hash']);

        $dataCheckString = collect($authData)
            ->map(function ($value, $key) {
                return $key . '=' . $value;
            })
            ->sort()
            ->implode("\n");

        $secretKey = hash('sha256', env('TG_BOT_TOKEN'), true);
        $hash = hash_hmac('sha256', $dataCheckString, $secretKey);

        return hash_equals($hash, $checkHash);
    }

    public function showView(Request $request)
    {
        dd($request);
        return view('mini-apps.tgbot', ['request' => $request]);
        // Получаем данные пользователя от Telegram
        $authData = $request->all();

        // Проверяем данные Telegram (это важно для безопасности)
        if ($this->validateTelegramAuth($authData)) {
            // Данные проверены, передаём их во view
            return view('mini-apps.tgbot', ['user' => $authData]);
        } else {
            // Ошибка проверки данных, можно перенаправить на страницу ошибки или вывести сообщение
            return response()->json(['error' => 'Invalid data'], 401);
        }
    }

}
