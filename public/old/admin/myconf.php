<?php
/*
Статус клиента:
0 - Отправил заявку
1 - Подтвердил подписку
2 - Подписан на раздачу гороскопов. В последних изменениях статус 2 устанавливается автоматически при подтверждении подписки.
3 - Продлил подписку бесплатно (поделился в СС)
*/
header('Content-Type: text/html; charset=UTF-8');
setlocale(LC_ALL, "ru_RU.utf8");
date_default_timezone_set("Asia/Yekaterinburg");
error_reporting(E_ERROR | E_PARSE);

$hostname = "localhost";

$dbname = "astro";
$username = "root";
$password = "123";

/*
    $dbname = "u35810_goro";
    $username = "u35810";
    $password = "y4rtuy7hrt";
*/

$clients = "_clients";
$goros = "_goros";
$gorogroup = "astrogroup";
$option = "options";
$ftime = 10; /* Дней для первой серии бесплатных гороскопов

    /** Кодировка базы данных для создания таблиц. */
define('DB_CHARSET', 'utf8');

$mysql = mysqli_connect($hostname, $username, $password, $dbname) or die("Не могу создать соединение ");
//mysqli_set_charset("utf8");
//mysqli_select_db("$dbname") or die("Не могу выбрать базу данных ".mysqli_error());

$check = "SELECT * FROM $option WHERE option_key = 'maked' OR option_key = 'orders'";
$result = mysqli_query($mysql, $check);
$exist = mysqli_num_rows($result);


function mysqli_result($res,$row=0,$col=0){
    $numrows = mysqli_num_rows($res);
    if ($numrows && $row <= ($numrows-1) && $row >=0){
        mysqli_data_seek($res,$row);
        $resrow = (is_numeric($col)) ? mysqli_fetch_row($res) : mysqli_fetch_assoc($res);
        if (isset($resrow[$col])){
            return $resrow[$col];
        }
    }
    return false;
}

if ($exist) {
    $maked = mysqli_result($result, 0, 'option_value');
    $orders = mysqli_result($result, 1, 'option_value');
} else {
    $maked = 0;
    $orders = 0;
}

$exist = "";


/* API smartresponder */

include('smartresponder.class.php');

$config = array(
    'api_id' => '565597', // эти цифры берем из "Настройки" -> "настройки вашего аккаунта" -> "API"
    'api_key' => '3QLH8ttZqAuNbjBFHYXPSpGo2d4t9iP3', // этот ключ тоже оттуда же
    'format' => 'json', // это не трогаем
);


// в этот массив собираем все id и произвольные имена списков, с которыми будем работать(необязательны)
$lists = array(
    'BEGINNER' => 653511, // цифры - id списка который можно найти в в админке "Рассылки" -> "Список рассылки" -> ID рассылки(did):
    'ONEWEEK' => 653895,
    'EXTENDED' => 681996,
    'MINUTES' => 699918,
    'NATAL' => 700344,
    'FINALLY' => 720626 //окончательная рассылка с минуткой астрономии длительностью 42 дня. Остальные рассылки, кроме BEGINNER, становятся неактуальны.
);

// запускаем класс
$API = new api_smartresponder($config, $lists);

$months = array("январь", "февраль", "март", "апрель", "май", "июнь", "июль", "август", "сентябрь", "октябрь", "ноябрь", "декабрь");
$bymonth = array("января", "февраля", "марта", "апреля", "мая", "июня", "июля", "августа", "сентября", "октября", "ноября", "декабря");

?>
