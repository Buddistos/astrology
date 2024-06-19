<?php

namespace App\Http\Controllers;

use App\Models\Aspect;
use App\Models\AstroGroup;
use App\Models\Client;
use App\Models\Ephemerides;
use http\Env\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use function App\Http\settings;
use TgWebValid\TgWebValid;

class IndexController extends Controller
{
    public $views = [
        1 => 'partials.step2',
        2 => 'ajax.astro',
        3 => 'partials.forecast',
    ];

    public function index()
    {
        $client_id = session('client_id');
        $this->vars['auth'] = 0;
        if ($client_id) {
            $client = Client::where('id', $client_id)->first();

            //Проверка на зарегистрированного пользователя
            if ($client) {
                $astrogroups = AstroGroup::all();
                $this->vars['astrogroups'] = $astrogroups;
                $this->vars['client'] = $client;

                //загружаем контент, соответствующий статусу клиента
                $this->vars['view'] = $this->views[$client->status];
                if ($_GET) {
                    $rules = [
                        "gsk" => 'size:32',
                        "num"=>'digits_between:0,7',
                        "udt" => 'date_format:Ymd',
                    ];
                    $messages = [
                        "size" => "Ошибка валидации запроса #1",
                        "num"=>"Ошибка валидации запроса #2",
                        "date_format" => "Ошибка валидации запроса #3",
                    ];
                    $validator = Validator::make($_GET, $rules, $messages);
                    if (isset($_GET['gsk']) && !$validator->fails()) {
                        //меняем контент на просмотр гороскопа view('forecast')
                        //$this->vars['astroname'] = $astrogroups->where('id_gorogroup', $_GET['num'])->goroname;
                        $this->vars['astroname'] = $astrogroups->where('id_gorogroup', $_GET['num'])->pluck('goroname')->get(0);
                        $this->vars['view'] = $this->views[3];
                        /**
                         * Получаем положение планет
                         */
                        $birthday = $client->birthday;
                        $birthtime = $client->birthtime;
                        $fordate = $_GET['udt'];
                        $utc = $client->utc;

                        $aspectController = new AspectController();
                        $aspects = $aspectController->getAspects($birthday, $birthtime, $fordate, $utc, $_GET['num']);
                        dd($aspects);
                    }
                }

                $this->vars['gsk'] = $client->clientAstroKeys();
                $this->vars['auth'] = 1;
            }
        }
        return view('welcome', $this->vars);
    }


}
