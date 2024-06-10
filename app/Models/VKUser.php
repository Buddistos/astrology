<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VKUser extends Model
{
    use HasFactory;

    protected $table = 'vkusers';

    public function __construct()
    {
        //$this->settings = DB::table('_settings')->get();
    }


}
