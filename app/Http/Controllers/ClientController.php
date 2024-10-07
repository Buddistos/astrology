<?php

namespace App\Http\Controllers;

use App\Models\AstroGroup;
use App\Models\Client;
use Illuminate\Http\Request;
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
            $out = self::$method($request);
            if (!$out['err']) {
                $client = $out['client'];
                $out['name'] = $client->firstname;
                unset($out['client']);
                $this->vars['client'] = $client;
                $this->vars['astrogroups'] = AstroGroup::get();
                $this->vars['gsk'] = $client->clientAstroKeys();
                if($client->status == 1){
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
    public function tga($request)
    {
        if (isset($request['hash'])) {
            $auth_data = $request->all();
            $check_hash = $auth_data['hash'];

            unset($auth_data['hash']);
            $data_check_arr = [];

            foreach ($auth_data as $key => $value) {
                $data_check_arr[] = $key . '=' . $value;
            }
            sort($data_check_arr);

            $data_check_string = implode("\n", $data_check_arr);
            $secret_key = hash('sha256', $_ENV['TG_BOT_TOKEN'], true);
            $hash = hash_hmac('sha256', $data_check_string, $secret_key);

            $out['err'] = 0;
            if (strcmp($hash, $check_hash) !== 0) {
                $out['err'] = 1;
                $out['msg'] = 'Данные не соответствуют Telegram';
            } elseif ((time() - $auth_data['auth_date']) > 86400) {
                $out['err'] = 1;
                $out['msg'] = 'Время ожидания вышло';
            } else {
                $clients = Client::class;
                $client = $clients::where('telegram_id', $auth_data['id'])->where('app', 'tga')->first();

                //Проверка наличия зрегистрированного пользователя
                if ($client) {
                    $out['msg'] = '<h5 class="text-center"><b>' . $auth_data['first_name'] . '</b></h5>';
                } else {
                    $client = new Client();
                    $validator = Validator::make($auth_data, [
                        "id" => "required|max:255",
                        "username" => "required|max:255",
                        "first_name" => "max:255",
                        "last_name" => "max:255",
                        "photo_url" => "max:255",
//                        "auth_date" => "1717798888",
                    ]);
                    if ($validator->fails()) {
                        $out['err'] = 1;
                        $out['msg'] = 'Ошибка регистрации';
                    } else {
                        $client['telegram_id'] = $auth_data['id'];
                        $client['name'] = $auth_data['username'];
                        $client['firstname'] = isset($auth_data['first_name']) ? $auth_data['first_name'] : $auth_data['username'];
                        $client['lastname'] = $auth_data['last_name'];
                        $client['avatar'] = $auth_data['photo_url'];
                        $client['app'] = 'tga';
                        $client['status'] = 1;
                        $client->save();
                        $out['msg'] = $auth_data['first_name'] . ', Вы зарегистрированы';
                    }
                }
                $out['client'] = $client;
                //session(['client_id' => $client->id]);
                \Session::put('client_id', $client->id);
                //session()->save();
            }
        } else {
            $out['err'] = 1;
            $out['msg'] = 'Данные не соответствуют запросу';
        }
        return $out;
    }
}
