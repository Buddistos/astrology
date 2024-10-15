<?php

namespace App\Http\Controllers;

use App\Models\AstroGroup;
use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Validator;

class ClientController extends Controller
{

    /**
     * Авторизация через соцсети и мессенджеры
     * @param Request $request
     * @return $out
     * msg - сообщение о результате
     * err - наличие ошибки: 1 - есть, 0 - нет
     * html - код для отображения на сайте в основном окне
     */
    public function auth(Request $request)
    {
        if (isset($request['method'])) {
            $method = $request['method'];
            unset($request['method']);
            unset($request['_token']);
            unset($request['query_id']);
            $out = self::$method($request);
            if (!$out['err']) {
                $client = $out['client'];
                $out['name'] = $client->firstname;
                unset($out['client']);
                $this->vars['client'] = $client;
                $this->vars['astrogroups'] = AstroGroup::get();
                $this->vars['gsk'] = $client->clientAstroKeys();
                if ($client->status == 1) {
                    $this->vars['view'] = 1;
                    $out['html'] = view('partials.step2', $this->vars)->render();
                } else {
                    $this->vars['views'] = 2;
//                    return redirect('/');
                    $out['html'] = view('ajax.astro', $this->vars)->render();
                }
            }
        } else {
            $out['err'] = 1;
            $out['msg'] = 'Неизвестный метод';
        }
        return redirect('/');
//        return $out;
    }

    /**
     * @param $request
     * @return mixed
     * @throws \Exception
     * For example
     * array:7 [
     * "id" => "55156568"
     * "first_name" => "Nickolas"
     * "last_name" => "Che"
     * "username" => "Nickoche"
     * "photo_url" => "https://t.me/i/userpic/320/jqOkmVNp6wDOgX0kGNRT9_WidnPr1-T3MmZ95uCfK0Q.jpg"
     * "auth_date" => "1717798888"
     * "hash" => "01469d6c1459151a3e93366176dea1e8cbfbd76333e8de8b9539f6f3d7462e8e"
     * ]
     */
    public function tga($request, $key = null)
    {
        if (isset($request['hash'])) {
            $auth_data = $request->all();
            $check_hash = $auth_data['hash'];

            unset($auth_data['hash']);
            unset($auth_data['method']);
            unset($auth_data['_token']);

            $data_check_arr = [];

            foreach ($auth_data as $key => $value) {
                $data_check_arr[] = $key . '=' . $value;
            }
            sort($data_check_arr);

            $data_check_string = implode("\n", $data_check_arr);

            if ($key) {
                $secret_key = hash_hmac('sha256', config('tg.bot_token'), $key, true);
            } else {
                $secret_key = hash('sha256', config('tg.bot_token'), true);
            }

            $hash = hash_hmac('sha256', $data_check_string, $secret_key);

            $out['err'] = 0;
            if (strcmp($hash, $check_hash) !== 0) {
                $out['err'] = 1;
                $out['msg'] = 'Данные не соответствуют Telegram';
            } elseif ((time() - $auth_data['auth_date']) > 86400) {
                $out['err'] = 1;
                $out['msg'] = 'Время ожидания вышло';
            } else {
                $client = Client::where('telegram_id', $auth_data['id'])->where('app', 'tga')->first();

                //Проверка наличия зрегистрированного пользователя
                if ($client) {
                    $out['msg'] = '<h5 class="text-center"><b>' . $auth_data['first_name'] . '</b></h5>';
                } else {
                    $client->create($auth_data);
                }
                $out['client'] = $client;
                Cookie::queue('client_id', "$client->id", 60);
            }
        } else {
            $out['err'] = 1;
            $out['msg'] = 'Данные не соответствуют запросу';
        }
        return $out;
    }

    public function addfields(Request $request)
    {
        $data = $request->all();
        $newdata = [
            "utc" => $data['utc'] ?? '',
            "birthday" => $data['birthday'] ?? '',
            "birthtime" => $data['birthtime'] ?? '',
        ];
        $data = Arr::except($data, array_keys($newdata));
        unset($data['_token']);
        $verify = TelegramController::validateTelegramAuth($data);
        if(!$verify){
            /**TODO
             * Сделать вывод ошибки авторизации
             */
            $out['msg'] = 'Ошибка авторизации #1';
            $out['err'] = 1;
            return $out;
        }
        $user = json_decode($data['user']);
        $client_id = Cookie::get('client_id');
        $client = Client::where('id', $client_id)->where('telegram_id', $user->id)->first();

        if ($client) {
            $rules = [
                "utc" => 'required|max:6',
                "birthday" => 'required|date_format:Y-m-d',
                "birthtime" => "required|date_format:H:i",
            ];
            $messages = [
                "max" => "Неверный формат поля :attributes",
                "date_format" => "Неверный формат для :attributes",
                "required" => "Поле :attributes должно быть заполнено.",
            ];
            $validator = Validator::make($newdata, $rules, $messages);
            $out['msg'] = '';
            if ($validator->fails()) {
                foreach ($validator->errors()->all() as $field => $error) {
                    $out['msg'] .= $error . '<br>';

                }
                $failedRules = $validator->failed();
                $out['err'] = 1;
            } else {
                /**
                 * TODO
                 * Создать проверку на изменение по времени уже созданных полей
                 * Изменение возможно не чаще, чем раз в две недели
                 */
                $client->utc = $request['utc'];
                $client->birthday = $request['birthday'];
                $client->birthtime = $request['birthtime'];
                $client->status = 2;
                $client->save();
                $out['msg'] = "Сохранено";
                $this->vars = $client->toArray();
                $this->vars['astrogroups'] = AstroGroup::get();
                $this->vars['gsk'] = $client->clientAstroKeys();
                $out['html'] = view('ajax.astro', $this->vars)->render();
            }
        } else {
            $out['err'] = 1;
            $out['msg'] = "Ошибка авторизации";
        }
        return $out;
    }

}
