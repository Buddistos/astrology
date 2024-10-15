<?php

namespace App\Http\Controllers;

use App\Models\AstroGroup;
use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
use function PHPUnit\Framework\isNull;

class TelegramController extends Controller
{

    public function index(Request $request)
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

            if ($client ) {
                Cookie::queue('client_id', $client->id, 60);
                if($client->status == 1){
                    $out['html'] = view('partials/step2', ['tga' => 1])->render();
                }else{
                    $this->vars = $client->toArray();
                    $this->vars['astrogroups'] = AstroGroup::get();
                    $this->vars['gsk'] = $client->clientAstroKeys();
                    $this->vars['tga'] = 1;
                    $out['html'] = view('ajax.astro', $this->vars)->render();
                }
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

    public function validateTelegramAuth($authData)
    {
        $checkHash = $authData['hash'];
        unset($authData['hash']);

        $dataCheckString = collect($authData)
            ->map(function ($value, $key) {
                return $key . '=' . $value;
            })
            ->sort()
            ->implode("\n");



        $secretKey = hash_hmac('sha256', config('tg.bot_token'), "WebAppData", true);
        $hash = hash_hmac('sha256', $dataCheckString, $secretKey);

        return hash_equals($hash, $checkHash);
    }

    public function astroview(Request $request)
    {
        $data = $request->all();

        $gskdata = [
            "gsk" => $data['gsk'] ?? '',
            "num" => $data['num'] ?? '',
            "udt" => $data['udt'] ?? '',
        ];
        $data = Arr::except($data, array_keys($gskdata));
        unset($data['_token']);
        $verify = TelegramController::validateTelegramAuth($data);
        if ($verify && isset($request->gsk)) {
            $user = json_decode($data['user']);
            $client_id = Cookie::get('client_id');
            $client = Client::where('id', $client_id)->where('telegram_id', $user->id)->first();
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
            $validator = Validator::make($gskdata, $rules, $messages);
            if (!$validator->fails()) {
                $check = $client->checkAstroForClient($request->gsk, $request->num, $request->udt);
            } else {
                $check = false;
                $this->vars['view'] = $client->status;
            }
            if ($check) {
                $astrogroups = AstroGroup::all();
                $this->vars['astrogroups'] = $astrogroups;
                $this->vars['client'] = $client;

                //меняем контент на просмотр гороскопа view('forecast') - номер 3
                $this->vars['astroname'] = $astrogroups->where('id_gorogroup', $request->num)->pluck('goroname')->get(0);

                $birthday = $client->birthday;
                $birthtime = $client->birthtime;

                $mydate = $birthday;
                $fordate = date("Y-m-d", strtotime($request->udt));
                $utc = $client->utc;

                $aspectController = new AspectController();
                $aspects = $aspectController->getAspects($birthday, $birthtime, $fordate, $utc, $request->num);

                //Определяем максимальный суммарный рейтинг аспектов
                $aspects->each(function ($aspect, $aspday) use ($client, $request) {
                    $aspect->sum = $aspect->sum('rating');
                    $astroday = date("Ymd", strtotime($aspday));
                    $aspect->astrourl = 'gsk=' . md5($request->num . $client->id . $astroday) . '&num=' .$request->num . '&udt=' . $astroday;
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
                $out['html'] = view('mini-apps.forecast', $this->vars)->render();
                $out['msg'] =  $this->vars['astroname'];
                return $out;
            }
        } else {
            $this->vars['view'] = $client->status;
        }

    }
}
