<?php

namespace App\Http\Controllers;

use App\Models\AstroGroup;
use App\Models\Client;
use Illuminate\Support\Facades\Validator;

class IndexController extends Controller
{
    public $views = [
        1 => 'partials.step2',
        2 => 'ajax.astro',
        3 => 'forecast',
    ];

    public function __construct()
    {
        $this->vars['sitename'] = setting('site_name');
        $this->vars['views'] = $this->views;
    }

    public function index()
    {
        $client_id = session('client_id');
        $this->vars['auth'] = 0;
        if ($client_id) {
            $client = Client::where('id', $client_id)->first();

            //Проверка на зарегистрированного пользователя
            if ($client) {
                $this->vars['auth'] = 1;
                $astrogroups = AstroGroup::all();
                $this->vars['astrogroups'] = $astrogroups;
                $this->vars['client'] = $client;

                //загружаем контент, соответствующий статусу клиента
                $this->vars['view'] = $client->status;

                if (isset($_GET['gsk'])) {
                    $rules = [
                        "gsk" => 'required|size:32',
                        "num" => 'required|digits_between:0,7',
                        "udt" => 'required|date_format:Ymd',
                    ];
                    $messages = [
                        "size" => "Ошибка валидации запроса #1",
                        "num" => "Ошибка валидации запроса #2",
                        "date_format" => "Ошибка валидации запроса #3",
                    ];
                    $validator = Validator::make($_GET, $rules, $messages);
                    if (!$validator->fails()) {
                        $check = $client->checkAstroForClient($_GET['gsk'], $_GET['num'], $_GET['udt']);
                    } else {
                        $check = false;
                        $this->vars['view'] = $client->status;
                    }
                    if ($check) {
                        //меняем контент на просмотр гороскопа view('forecast') - номер 3
                        $this->vars['astroname'] = $astrogroups->where('id_gorogroup', $_GET['num'])->pluck('goroname')->get(0);

                        $birthday = $client->birthday;
                        $birthtime = $client->birthtime;

                        $mydate = $birthday;
                        $fordate = date("Y-m-d", strtotime($_GET['udt']));
                        $utc = $client->utc;

                        $aspectController = new AspectController();
                        $aspects = $aspectController->getAspects($birthday, $birthtime, $fordate, $utc, $_GET['num']);

                        //Определяем максимальный суммарный рейтинг аспектов
                        $aspects->each(function ($aspect, $aspday) use ($client) {
                            $aspect->sum = $aspect->sum('rating');
                            $astroday = date("Ymd", strtotime($aspday));
                            $aspect->astrourl = 'gsk=' . md5($_GET['num'] . $client->id . $astroday) . '&num=' .$_GET['num'] . '&udt=' . $astroday;
                        });
                        $this->vars['maxsumrating'] = $aspects->max('sum');
                        $this->vars['minsumrating'] = $aspects->min('sum');
                        $this->vars['aspects'] = $aspects;

//                        $months = array("январь", "февраль", "март", "апрель", "май", "июнь", "июль", "август", "сентябрь", "октябрь", "ноябрь", "декабрь");
                        $bymonth = array("января", "февраля", "марта", "апреля", "мая", "июня", "июля", "августа", "сентября", "октября", "ноября", "декабря");
                        $this->vars['sd'] = date("d ", strtotime("$fordate")) . $bymonth[date("n", strtotime("$fordate")) - 1] . (date("Y", strtotime("$fordate")) <> date("Y", strtotime("$mydate")) ? date(" Y г.", strtotime("$fordate")) : "");
                        $this->vars['ed'] = date("d ", strtotime("$fordate +30days")) . $bymonth[date("n", strtotime("$fordate +30days")) - 1] . date(" Y г.", strtotime("$fordate +30days"));
                        $this->vars['fordate'] = $fordate;
                        $this->vars['mydate'] = $mydate;

                        $this->vars['view'] = 3;
                        return view('forecast', $this->vars);
                    }
                } else {
                    $this->vars['view'] = $client->status;
                }

                $this->vars['gsk'] = $client->clientAstroKeys();
            }
        }
        return view('welcome', $this->vars);
    }
}
