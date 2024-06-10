<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ephemerides extends Model
{
    use HasFactory;

    protected $table = 'ephemerides';

    public function __construct()
    {

    }
}
