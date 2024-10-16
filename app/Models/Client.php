<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;

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

    /**
     * @param null $date
     * @return mixed Гороскоп клиента
     * хэш <Номер гороскопа><ID клиента><Дата расчета>
     */
   public function clientAstroKeys($date = null)
    {
        if(!$date) $date = date('Ymd');

        for ($i = 1; $i <= 7; $i++) {
            $cgsk[$i] = md5($i . $this->id . $date);
        }
        return $cgsk;
    }

    /**
     * Проверка доступа клиента к гороскопу
     * Соответствие номера гороскопа, ID клиента, даты расчета
     * @param Client $client
     * @param null $num
     * @param null $date is current date by default
     */
    public function checkAstroForClient($gsk, $num, $date = null)
    {
        //'gsk=' . md5($id_gorogroup . $gid . $newdate) . "&uid=$gid&udt=$newdate";
        $astrokeys = $this->clientAstroKeys($date);
        return $astrokeys[$num] == $gsk;
    }

    public function create($user)
    {
        $client = new Client();
        $validator = Validator::make($user, [
            "id" => "required|max:255",
            "username" => "required|max:255",
            "first_name" => "max:255",
            "last_name" => "max:255",
            "photo_url" => "max:255",
//                        "auth_date" => "1717798888",
        ]);

        if ($validator->fails()) {
            return -1;
        } else {
            $client['telegram_id'] = $user['id'];
            $client['name'] = $user['username'] ?? '';
            $client['firstname'] = $user['first_name'] ?? $user['username'];
            $client['lastname'] = $user['last_name'] ?? '';
            $client['avatar'] = $user['photo_url'] ?? '';
            $client['app'] = $user['app'];
            $client['status'] = 1;
            $client->save();
            return $client;
        }
    }
}
