<?php

namespace App\Http\Controllers;

use App\Models\AstroGroup;
use App\Models\Client;
use App\Models\VKUser;
use Illuminate\Http\Request;

class VKappController extends Controller
{
    private $v = '5.199';
    private $token;

    public function __construct()
    {
        $this->token = $_ENV['VKSERVICE'];
        //dd($_ENV);
    }

    public function index(Request $request)
    {

        //$name = $request->session()->put('vkuid', 6541);
        //dump(session()->driver());
        $user_id = isset($name) ? $name : $_GET['viewer_id'];
        $vkuserModel = new VKUser();
        $userdata = $vkuserModel->where('vkuser', $user_id)->first();
        $data = [
            'user_ids' => $userdata->vkuser,
            'fields' => 'bdate,country,city,photo_100,photo_max_orig',
        ];

//Данные пользователя из базы ВК
        $request = $this->VKapiRequest('users.get', $data);
        $vkuser = collect($request['response'][0]);

//Данные пользователя из таблицы клиентов
        $user['vkuid'] = $user_id;
        $user['uname'] = $vkuser['first_name'];
        $user['photo_100'] = $vkuser['photo_100'];
        $user['bcity'] = isset($vkuser['city']) ? $vkuser['city']['title'] : 0;
        $user['bdate'] = isset($vkuser['bdate']) ? $vkuser['bdate'] : 0;
        $user['btime'] = isset($vkuser['btime']) ? $vkuser['btime'] : 0;

        $this->vars = array_merge($user, $userdata->toArray());
        $this->vars['gsk'] = 0;
        $this->vars['cntdate'] = date('d.m.Y', time());

        //Дата прогноза
        $udt = isset($_POST['udt']) ? $_POST['udt'] : date("Ymd");
        $this->vars['udt'] = $udt;

        /**
         * $cgsk - Массив ссылок прогнозов на конкретную дату $udt
         */
        for ($i = 0; $i <= 6; $i++) {
            $this->vars['cgsk'][$i] = md5($i . $user_id . $udt);
        }
        $this->vars['astrogroups'] = AstroGroup::get();

        //$request->session()->put('vkuid', $user_id);
        if (!$name) {
            session(['user_id' => $user_id]);
            session()->save();
        }
        //dd(session('user_id'));
        return view('vkapp/index', $this->vars);
    }

    private function VKapiRequest($method, $data = array())
    {
        $data['v'] = $this->v;
        $data['access_token'] = $this->token;

        $string = http_build_query($data);

        $url = 'https://api.vk.com/method/' . $method . '?';
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url . urldecode($string));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Accept-Language: ru,en-us'));
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $response = curl_exec($ch);
        curl_close($ch);
        return json_decode($response, true);
    }
}
