<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class VPappController extends Controller
{
    public function index()
    {
        $this->vars['main_page'] = 1;
        return view('main', $this->vars);
    }
}
