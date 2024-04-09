<?php
    
    require_once('admin/myconf.php');

    $out = "";
/* Этот скрипт получает переменные из index.html */
    function mb_ucwords($str) {
        $rstr = "";
        foreach(explode(" ", $str) as $word){
            if($rstr) $rstr .= " ";
            $first = substr($word, 0, 1); //первая буква
            $last = substr($word, 1, strlen($word)); //все кроме первой буквы
            $first = strtoupper($first);
            $last = strtolower($last);
            $rstr .= $first.$last;
        }
        return $rstr;
    }

    $firstname = mb_ucwords($_POST['firstname']);
    $lastname = " ";
    $email = strtolower($_POST['email']);
    if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
        echo "<h2 align=center>Ошибка</h2><center>Некорректный адрес почты (email).</center>";
        exit;
    }

    $bday = $_POST['myday'];
    $bmonth = $_POST['mymonth'];
    $byear = $_POST['myyear'];
    $bd = $bday.'.'.$bmonth.'.'.$byear;
    $birthday = $byear.$bmonth.$bday;
    $birthtime = $_POST['hours'].":".$_POST['minutes'];
    $birthplace = mb_ucwords($_POST['birthplace']);
    
    $err = 0;
    
    if(!$firstname){
        $err_txt .= "<p style='color: red; text-align: center;'><b>Извините, ошибка! Поле Имя должно быть заполнены.</b></p>";
        $err = 1;
    }
    if(!$email){
        $err_txt .= "<p style='color: red; text-align: center;'><b>Извините, ошибка! Поле Email должно быть заполнены.</b></p>";
        $err = 1;
    }
    if(!$bday or !$bmonth or !$byear){
        $err_txt .= "<p style='color: red; text-align: center;'><b>Извините, ошибка! Все поля даты рождения должны быть заполнены.</b></p>";
        $err = 1;
    }

    $insmart = $API->getSubscriber(NULL, "$email");
    if($insmart){
        $err_txt = "<p style='color: red; text-align: center;'><b>Извините, ошибка! Адрес $email уже подписан на рассылку гороскопов.</b></p>";
        $err = 1;
    }
    
    if($err){
        echo $err_txt;
        exit;
    }

/** Определение местоположения **/
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        $ip = $_SERVER['HTTP_CLIENT_IP'];
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    } else {
        $ip = $_SERVER['REMOTE_ADDR'];
    }
    if(!$birthplace){
        include("geo/SxGeo.php");
        $SxGeo = new SxGeo('geo/SxGeoCity.dat', SXGEO_BATCH | SXGEO_MEMORY);
        $city = $SxGeo->getCityFull($ip);
        unset($SxGeo);
        $country = $city['country'];
        $region = $city['region_name'];
        $town = $city['city'];
        $geocity = $country ? $country : "";
        $geocity= $geocity ? $geocity.($region ? ", ".$region : "") : ($region ? $region : "");
        $birthplace = $geocity ? $geocity.($town ? ", ".$town : "") : ($town ? $town : "");
    }
    $xml = simplexml_load_file('http://maps.google.com/maps/api/geocode/xml?address='.$birthplace.'&sensor=false');
    $status = $xml->status;
    if ($status == 'OK') {
        $lat = $xml->result->geometry->location->lat;
		$lng = $xml->result->geometry->location->lng;
        $coords = $lat.','.$lng;
    }else{
        $coords = '0';
    }
/** Конец определения местоположения **/

    $check = "SELECT * FROM $clients WHERE email = '$email'";
    $result = mysql_query($check);
    $existemails = mysql_num_rows($result);

    if($existemails){
        echo "<p style='color: red;'><b>Извините, ошибка уникальности email!</b><br>Email <b>$email</b> уже зарегистрирован. Пожалуйста, укажите другой адрес.</p>";
        //$exist = 1;
        exit;
    }
    
    $check = "SELECT * FROM $clients WHERE ip = '$ip' AND lastname = '$lastname'";
    $result = mysql_query($check);
    $existnameonsameip = mysql_num_rows($result);

    if($existnameonsameip){
        $name = mysql_result($result, 0, "lastname");
        $out .= "<p style='color: orange;'><b>Обратите внимание!</b> С Вашего IP-адреса <b>$ip</b> уже зарегистрирован один или несколько пользователей.</p>";
    }
    
    $check = "SELECT * FROM $clients WHERE ip = '$ip' AND birthday = '$birthday'";
    $result = mysql_query($check);
    $existbdonsameip = mysql_num_rows($result);

    $months = Array("январь","февраль","март","апрель","май","июнь","июль","август","сентябрь","октябрь","ноябрь","декабрь");
    $bymonth = Array("января","февраля","марта","апреля","мая","июня","июля","августа","сентября","октября","ноября","декабря");

    if($existbdonsameip){
        $name = mysql_result($result, 0, "firstname")." ".mysql_result($result, 0, "lastname");
        $out .= "<p style='color: orange;'><b>Обратите внимание!</b> С Вашего IP-адреса <b>$ip</b> уже зарегистрирован пользователь, рожденный <b>$bd</b>.</p>";
    }

    
    if($existnameonsameip or $existbdonsameip){
        $out .= "<p style='color: orange;'>Мы принимаем Вашу заявку, но по правилам предоставления бесплатных гороскопов повторные регистрации не допускаются и, так как гороскопы составляются в ручную, все регистрации отслеживаются. В случае повторения регистрации гороскоп предоставлен не будет.</p>";
    }

    if($exist){
        echo $out;
        exit;
    }
    
    $secretkey = "";
    while(!$secretkey){
        $secretkey = md5(uniqid(rand(),1));
        $check = "SELECT * FROM $clients WHERE secretkey = '$secretkey'";
        $result = MYSQL_QUERY($check);
        if(mysql_num_rows($result)) $secretkey = ""; 
    }

    $query = "INSERT INTO $clients (firstname, lastname, email, birthday, birthtime, birthplace, regdate, ip, secretkey, coords) VALUES('$firstname', '$lastname', '$email', '$birthday', '$birthtime', '$birthplace', NOW(), '$ip', '$secretkey', '$coords')";
    $result = mysql_query($query);
    
    if($result){
        setcookie("usk", $secretkey, time() + (86400 * 365 * 5));
        $out .= "<h2 align=center>Спасибо за Ваш запрос гороскопов</h2><p>Для получения ежедневной рассылки гороскопов нам необходимо подтверждение Вашего email адреса - проверьте Ваш почтовый ящик с тестовым письмом и подтвердите свою подписку. Если письмо не приходит продолжительное время, загляните в папку <b>СПАМ</b>.<br>
        <br>После подтверждения Вы получите первую ссылку на Ваши гороскопы и будете подписаны на ежедневную бесплатную рассылку гороскопов на каждый день.<br>Вы сможете отписаться от получения гороскопов в любой момент.</p><p>С уважением, Андрей Перье и интернет-группа 'Астроном и я'</p>
        <script type='text/javascript'>
            yaCounter25119665.reachGoal('real_zakaz');
        </script>";
        
        $orders++;
        $query = "UPDATE $option SET option_value = $orders WHERE option_key = 'orders';";
        $result = mysql_query($query);

//        $API->debug = TRUE;
        
        $result = $API->addSubscriber( // добавляем человека и подписываем на конкретную рассылку
            'BEGINNER', 
            array(  'email' => $email, 
                    'first_name' => $firstname, 
                    'last_name' => $lastname, 
                    'extra_s1' => $secretkey,
                    'birthday' => $bday,
                    'birth_month' => $bmonth,
                    'birth_year' => $byear
            )
        );
        
//        print_r($API->debug_output);
    }else{
        $out .= "<h2 align=center>Извините, программная ошибка</h2><p>Пожалуйста, обратитесь к администратору или попробуйте позднее.</p>".mysql_error();
    }

    //$out .= "<a style='float: right;margin-bottom: 5px;' href='javascript:void(0);' onclick=$('.inform').toggle();>закрыть</a>";
    echo $out;

    $adminaddress = "nickoche@mail.ru, andreyperje@gmail.com";
/* Отправляем email */
/* Данное письмо пойдет через смартреспондер
mail($email, "Ваш подарок - гороскоп на каждый день в течение недели.", 
"Здравствуйте, $firstname, и спасибо за ваш заказ\n
В ближайшее время Вы получите серию ежедневных гороскопов по различным тематикам:
Финансы, Бизнес, Авто, Любовь, Отпуск, Здоровье.\n
Нам необходимо некоторое время, чтобы подготовить их Вам.
Первое письмо с гороскопами будет отправлено в течение 24-х часов.
После Вы будете в течение недели получать ежедневные гороскопы.\n
С уважением,
Лучшие гороскопы
");
*/

    $newbddate = date("d.m.Y", strtotime($birthday));
mail($adminaddress,
"Оставлена заявка на бесплатные гороскопы.",
"Клиент: $firstname $lastname
День рождения: $newbddate $birthtime
Город: $birthplace
Email: $email
IP: $ip
Ссылка на редактирование будет предоставлена после подтверждения подписки пользователем.
");

    mysql_close();
    
?>