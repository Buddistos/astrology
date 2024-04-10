<?php

namespace App\Http\Controllers;

use function App\Http\settings;

class IndexController extends Controller
{

    public function index()
    {
        $this->vars['main_page'] = 1;
        return view('main', $this->vars);
    }

}
