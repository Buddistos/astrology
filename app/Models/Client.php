<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    use HasFactory;

    protected $table = 'clients';

    public function __construct()
    {

    }

    /**
     * @param $method
     *  telegram_id
     *  vk_id
     * @param $clientId
     */
    public function getClient($method, $clientId)
    {
        return self::where($method, $clientId)->first();
    }

}
