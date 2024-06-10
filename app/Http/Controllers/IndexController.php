<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use function App\Http\settings;
use TgWebValid\TgWebValid;

class IndexController extends Controller
{

    public function index()
    {
        $this->vars['main_page'] = 1;
        $this->vars['auth'] = Auth::check();
        return view('welcome', $this->vars);
    }



}
