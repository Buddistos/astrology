<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Auth\RegisterController;

use App\Models\AstroGroup;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Support\Arr;

use function App\Http\settings;

class AjaxController extends Controller
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
     * @return array $out
     * msg - сообщение о результате
     * err - наличие ошибки: 1 - есть, 0 - нет
     * html - код для отображения на сайте в основном окне
     */
    public function auth(Request $request)
    {
        if(isset($request['method'])){
            $method = $request['method'];
            unset($request['method']);
            $out = ClientController::$method($request);
            if(!$out['err']){
                $out['html'] = view('partials.step2', $this->vars)->render();
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
}
