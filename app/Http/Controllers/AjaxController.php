<?php

namespace App\Http\Controllers;

use App\Models\AstroGroup;
use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use function App\Http\settings;

class AjaxController extends IndexController
{
    public function _html(Request $request, $action)
    {

        //--- Поиск Ajax метода для исполнения
        if (method_exists($this, $action)) {
            return $this->$action($request);
        }

        return false;
    }

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
            $out = ClientController::$method($request);
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
                    $this->vars['view'] = 2;
                    $out['html'] = view('ajax.astro', $this->vars)->render();
                }
            }
        } else {
            $out['err'] = 1;
            $out['msg'] = 'Неизвестный метод';
        }
        return $out;
    }

    public function vkapp()
    {
        $this->vars['cntdate'] = date('d.m.Y', time());
        $this->vars['astrogroups'] = AstroGroup::get();
        $out['content'] = view('ajax.index', $this->vars)->render();

        return $out;
    }

    public function _finance()
    {
        $this->vars['cntdate'] = date('d.m.Y', time());
        $this->vars['astrogroups'] = AstroGroup::get();
        $out['content'] = view('ajax.index', $this->vars)->render();

        return $out;
    }

    public function profilechange(Request $request)
    {
        $client_id = session('client_id');
        $out['err'] = 0;
        if ($client_id) {
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
            $validator = Validator::make($request->all(), $rules, $messages);
            $out['msg'] = '';
            if ($validator->fails()) {
                foreach($validator->errors()->all() as $field => $error){
                    $out['msg'] .= $error . '<br>';

                }
                $failedRules = $validator->failed();
                $out['err'] = 1;
            }else{
                $client = Client::where('id', $client_id)->first();
/**
 * TODO
 * Создать проверку на изменение уже созданных полей
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
        }else{
            $out['err'] = 1;
            $out['msg'] = "Ошибка авторизации";
        }
        return $out;
    }
}
