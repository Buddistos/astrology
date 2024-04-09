<style>
    body{font-size: 12px;}
</style>
<?php

    require_once('myconf.php');

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
    $email = mb_strtolower($_POST['email']);
    $bd = $_POST['birthday'];
    $bda = explode(".", $bd);
    $birthday = $bda[2].$bda[1].$bda[0];
    $birthtime = $_POST['birthtime'];
    $birthplace = mb_ucwords($_POST['birthplace']);
    $ip = $_POST['ip'];
    $usk = $_POST['usk']; //для проверки зарегистрированного клиента: если usk присутствует, то клиент есть в базе

    if(!$firstname or !$email or !$bd){
        echo "<b style='color: red;'>Отказано:</b> Имя, email и дата рождения должны быть заполнены.";
        exit;
    }

    mysql_connect($hostname,$username,$password) OR DIE("Не могу создать соединение ");
    mysql_set_charset("utf8");
    mysql_select_db("$dbname") or die("Не могу выбрать базу данных "); 
    
    if(isset($_POST['notcheck'])){
        $nc = $_POST['notcheck'];
    }
    
    $check = "SELECT * FROM $clients WHERE email = '$email'";
    $result = mysql_query($check);
    $existemails = mysql_num_rows($result);

    if($existemails and !$usk){
        echo "<b style='color: red;'>Отказано!</b> Email <b>$email</b> уже зарегистрирован.";
        exit;
    }
    
    if($ip){
        $check = "SELECT * FROM $clients WHERE birthday = '$birthday' AND ip = '$ip'";
        $result = mysql_query($check);
        $existnbi = mysql_num_rows($result);

        if($existnbi and $usk != mysql_result($result, 0, "secretkey")){
            $findname = mysql_result($result, 0, "firstname");
            $findplace = mysql_result($result, 0, "birthplace");
            $findtime = mysql_result($result, 0, "birthtime");
            echo "<b style='color: orange;'>Внимание!</b><br>C IP-адреса <b>$ip</b> уже зарегистрирован некто <b>$findname</b> из города <b>$findplace</b>, рожденный <b>$bd</b> в <b>$findtime</b>.<br>";
            if(!$nc){
                echo "<b style='color: red;'>Отказано. Для записи отключите проверку.</b>";
                exit;
            }
        }
    }

    $check = "SELECT * FROM $clients WHERE birthday = '$birthday' AND birthplace = '$birthplace'";
    $result = mysql_query($check);
    $existnbp = mysql_num_rows($result);

    if($existnbp and $usk != mysql_result($result, 0, "secretkey")){
        $findname = mysql_result($result, 0, "firstname");
        $findplace = mysql_result($result, 0, "birthplace");
        $findtime = mysql_result($result, 0, "birthtime");
        echo "<b style='color: orange;'>Внимание!</b><br>Уже зарегистрирован некто <b>$findname</b> из города <b>$findplace</b>, рожденный <b>$bd</b> в <b>$findtime</b>.<br>";
        if(!$nc){
            echo "<b style='color: red;'>Отказано. Для записи отключите проверку.</b>";
            exit;
        }
    }

    $check = "SELECT * FROM $clients WHERE birthday = '$birthday'";
    $result = mysql_query($check);
    $existnb = mysql_num_rows($result);

    if($existnb and !$existnbp and $usk != mysql_result($result, 0, "secretkey")){
        $findname = mysql_result($result, 0, "firstname");
        $findplace = mysql_result($result, 0, "birthplace");
        $findtime = mysql_result($result, 0, "birthtime");
        echo "<b style='color: orange;'>Внимание!</b><br>Уже зарегистрирован некто <b>$findname</b> из города <b>$findplace</b>, рожденный <b>$bd</b> в <b>$findtime</b>.<br>";
        if(!$nc){
            echo "<b style='color: red;'>Отказано. Для записи отключите проверку.</b>";
            exit;
        }
    }

    if(!$usk){
        $secretkey = "";
        while(!$secretkey){
            $secretkey = md5(uniqid(rand(),1));
            $check = "SELECT * FROM $clients WHERE secretkey = '$secretkey'";
            $result = mysql_query($check);
            if(mysql_num_rows($result)) $secretkey = ""; 
        }
        $query = "INSERT INTO $clients (firstname, email, birthday, birthtime, birthplace, regdate, ip, secretkey) VALUES('$firstname', '$email', '$birthday', '$birthtime', '$birthplace', NOW(), '$ip', '$secretkey')";
    }else{
        $secretkey = $usk;
        $check = "SELECT * FROM $clients WHERE secretkey = '$usk' AND email = '$email'";
        $result = mysql_query($check);
        if(mysql_num_rows($result)){
            $query = "UPDATE $clients SET firstname='$firstname', birthday='$birthday', birthtime='$birthtime', birthplace='$birthplace', updatedate=NOW() WHERE secretkey='$usk' AND email='$email'";
        }else{
            echo "<b style='color: red;'>Странно, но клиент не найден.</b>";
            exit;
        }
    }
    $result = mysql_query($query);
    if(!$result){
        echo "<b style='color: red;'>Ошибка. $query</b>";
        exit;
    }

    $check = "SELECT id_client FROM $clients WHERE secretkey = '$secretkey'";
    $result = mysql_query($check);
    if(mysql_num_rows($result)){
        $id_client = mysql_result($result, 0);
    }

    echo "No parsing ;)<br>";
//    require_once('parsing.php');

    mysql_close();
    
?>