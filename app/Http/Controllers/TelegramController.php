<?php

namespace App\Http\Controllers;

use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Http;
use function PHPUnit\Framework\isNull;

class TelegramController extends Controller
{

    public function index()
    {
        //$this->vars['auth'] = 1;
        return view('mini-apps.tgbot', $this->vars);
    }

    public function auth(Request $request)
    {
        // Получаем данные от Telegram
        $authData = $request->all();

        unset($authData['method']);
        unset($authData['_token']);

        /// $client = ClientController::tga($authData);
        // Проверяем данные
        if ($this->validateTelegramAuth($authData)) {
            $user = json_decode($authData['user'], true);
            $client = Client::where('app', 'tga')->where('telegram_id', $user['id'])->first();

            if(!$client){
                $user['app'] = 'tga';
                $client = Client::create($user);
            }

            if ($client) {
                Cookie::queue('client_id', $client->id, 60);
                setcookie('user_id', $client->id, 60);
                $out['html'] = view('partials/step2')->render();
                $out['name'] = $client->firstname;
                $out['msg'] = '';
            }else{
                $out['err'] = 1;
                $out['msg'] = ' Ошибка';
            }
            return $out;
        } else {
            // Ошибка проверки данных
            return response()->json(['error' => 'Invalid data'], 401);
        }
    }

    private function validateTelegramAuth($authData)
    {
        $checkHash = $authData['hash'];
        unset($authData['hash']);

        $dataCheckString = collect($authData)
            ->map(function ($value, $key) {
                return $key . '=' . $value;
            })
            ->sort()
            ->implode("\n");

        $secretKey = hash_hmac('sha256', $_ENV['TG_BOT_TOKEN'], "WebAppData", true);
        $hash = hash_hmac('sha256', $dataCheckString, $secretKey);

        return hash_equals($hash, $checkHash);
    }
}
