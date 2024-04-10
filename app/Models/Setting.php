<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
class Setting extends Model
{
	protected $table = '_settings';

	public static $settings;

    static public $rolesExists, $allRoles, $user;

    public function __construct()
    {
        //$this->settings = DB::table('_settings')->get();
    }

}
