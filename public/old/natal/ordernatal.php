<?php
    require_once('../admin/myconf.php');

    $usk = $_POST['usk'];

    if($usk){
        $check = "SELECT * FROM $clients WHERE secretkey = '$usk'";
        $result = mysql_query($check);
        $existsk = mysql_num_rows($result) or die ("<p>Error! No person found.</p>");
        $id_client = mysql_result($result, 0, 'id_client');
        $email = mysql_result($result, 0, 'email');
    }else{
        die ("<p>Error! No person found.</p>");
    }
    $cemail = isset($_POST['email']) ? $_POST['email'] : die ("<p style='text-align:center;'>Ошибка! Проверьте правильность ввода Email</p>");
    if($email != $cemail){
        echo "<p style='text-align:center;'>Введенный email не совпадает с тем, на который Вы получаете наши гороскопы.<br>Пожалуйста, введите правильный адрес электронной почты!<br></p>";
        exit;
    }
    
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
    
    $firstname = isset($_POST['firstname']) ? mb_ucwords($_POST['firstname']) : die ("<p style='text-align:center;'>Ошибка! Проверьте правильность ввода Имени</p>");
    $md = isset($_POST['myday']) && $_POST['myday'] ? $_POST['myday'] : die ("<p style='text-align:center;'>Ошибка! Проверьте правильность ввода Дня рождения</p>");
    $mm = isset($_POST['mymonth']) && $_POST['mymonth'] ? $_POST['mymonth'] : die ("<p style='text-align:center;'>Ошибка! Проверьте правильность ввода Месяца рождения</p>");
    $my = isset($_POST['myyear']) && $_POST['myyear'] ? $_POST['myyear'] : die ("<p style='text-align:center;'>Ошибка! Проверьте правильность ввода Года рождения</p>");
    $hours = isset($_POST['hours']) && $_POST['hours']>=0? $_POST['hours'] : die ("<p style='text-align:center;'>Ошибка! Проверьте правильность ввода Часов рождения</p>");
    $minutes = isset($_POST['minutes']) && $_POST['minutes']>=0 ? $_POST['minutes'] : die ("<p style='text-align:center;'>Ошибка! Проверьте правильность ввода Минут рождения</p>");
    $bt = $hours.":".$minutes;
    $bp = isset($_POST['birthplace']) ? $_POST['birthplace'] : die ("<p style='text-align:center;'>Ошибка! Проверьте правильность ввода Места рождения</p>");
    
    $birthday = $my.$mm.$md;

    $udata = $API->getSubscriber("NATAL", $email);
    if($udata['id']){
        $info = "Вы уже оставляли заявку на составление своей Натальной карты.<br>Ваши данные не изменены. Спасибо.";
    }else{
        $query = "UPDATE $clients SET firstname='$firstname', birthday='$birthday', birthplace='$bp', birthtime='$bt' WHERE id_client = '$id_client';";
        $result = mysql_query($query) or die ("<p>Error! No person found.</p>");;
        
       $change = $API->addSubscribe("NATAL", $email);
       $info = "Ваша заявка на составление Натальной карты принята.<br>Мы свяжемся с Вами в ближайшее время.<br>Спасибо!<br>";
    }
?>
<p style="text-align:center;">
    <?php echo $info; ?>
</p>
<p style="text-align:center;">
    <br>
    <a class="button" href="/show/?usk=<?php echo $usk; ?>"><b>ПЕРЕЙТИ К МОИМ ГОРОСКОПАМ</b></a>
    <br>
    &nbsp;
</p>