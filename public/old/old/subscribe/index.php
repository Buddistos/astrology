<?php
    require_once('../admin/myconf.php');

    $email = $_GET['smartemail'];
 
    if($email){
        $check = "SELECT * FROM $clients WHERE email = '$email'";
        $result = mysql_query($check);
        $existsk = mysql_num_rows($result) or die ("Ошибка! Пользователь не определен.");
        $id_client = mysql_result($result, 0, 'id_client');
        $firstname = mysql_result($result, 0, 'firstname');
        $usk = mysql_result($result, 0, 'secretkey');
        $status = mysql_result($result, 0, 'status');
    }else{
        die ("Ошибка! Пользователь не определен. $email");
    }

    
    if($status==0){
        $query = "UPDATE $clients SET status=2, updatedate=NOW() WHERE id_client = '$id_client';";    
        mysql_query($query);
        if($check){
            $info = "Получено подтверждение подписки!\n";
            $beginner = $API->getSubscriber("BEGINNER", "$email");
            $finally = $API->getSubscriber("FINALLY", "$email");
            if($beginner && !$finally){
                $change = $API->addSubscribe("FINALLY", $email);
                $info .= "Клиент успешно подписан на недельную раздачу! Статус клиента - 2.<br>";
            }elseif($beginner){
                $info .= "<b style='color: orangered;'>Клиент уже был подписан на недельную раздачу!</b><br>";
            }else{
                $info .= "<b style='color: red;'>Клиент не прописан в начальной базе, что странно! Необходимо разобраться..</b><br>";
            }
            
        }else{
            $err = 1;
            echo $info = "Программная ошибка<br>Пожалуйста, обратитесь к администратору или попробуйте позднее.";
        }
    
        $birthday = date("d.m.Y", strtotime(mysql_result($result, 0, "birthday")));
        $birthtime = mysql_result($result, 0, 'birthtime');
        $birthplace = mysql_result($result, 0, 'birthplace');
        $lastname = mysql_result($result, 0, 'lastname');
        $ip = mysql_result($result, 0, 'ip');
        $secretkey = mysql_result($result, 0, 'secretkey');
    
        $adminaddress = "nickoche@mail.ru, andreyperje@gmail.com";
/* Отправляем email */
        mail($adminaddress,
        "$firstname $lastname: подтверждение подписки на бесплатные гороскопы.",
        "
        $info
        ============================
        Клиент: $firstname $lastname
        День рождения: $birthday $birthtime
        Город: $birthplace
        Email: $email.
        IP: $ip
        http://best-horoscope.ru/admin/?usk=$secretkey
        ");
        if($err){
            exit;
        }
    }

?>
<html>
<head>
    <meta charset="utf-8">
    <title>Подтверждение подписки на Best-horoscope.ru</title>
    <link rel="stylesheet" href="//ajax.googleapis.com/ajax/libs/jqueryui/1.10.4/themes/smoothness/jquery-ui.css" type="text/css"/>
    <link rel='stylesheet' href='http://fonts.googleapis.com/css?family=Seymour+Display|Marmelad&subset=cyrillic' type='text/css'>
    <link rel="stylesheet" href="../js/fancybox/jquery.fancybox.css?v=2.1.5" type="text/css" type="text/css" media="screen" />
    <link rel="stylesheet" href="../css/mystyle.css" type="text/css" media="screen" />
</head>
<body>
    <div class="container">
        <h1 class="attention textshadow" style="color: white;"><?php echo $firstname; ?>, спасибо!</h1>
        <div class="block">
    <?php if($status == 0): ?>
            <h2>Вы подтвердили свою подписку на Best-horoscope.ru</h2>
            <p>Ваша ссылка для сегодняшнего гороскопа готова:</p>
            <p class="aligncenter" style="padding: 40px 0;">
                <a href="/show/?usk=<?php echo $usk; ?>" class="button"><b>МОИ ГОРОСКОПЫ НА ВЧЕРА, СЕГОДНЯ И ЗАВТРА</b></a>
            </p>
            <p>Мы подготовили для Вас ежедневную рассылку астропрогнозов по следующим гороскопа:</p>  
    
            <img src="/images/zodiac.png" align="left">
            <ul style="font-size: 20px;margin-left: 200px; margin-top: 30px;">
                <li>Финансовый гороскоп</li>
                <li>Бизнес-гороскоп</li>
                <li>Авто-гороскоп</li>
                <li>Любовный гороскоп</li>
                <li>Отпускной гороскоп</li>
                <li>Гороскоп для здоровья</li>
            </ul>
            <div style="clear: both;">&nbsp;</div>
            <p>С уважением, Андрей Перье<br>Интернет-группа "Астроном и я"</p>
    <?php else: ?>
            <h2>Вы уже подтверждали свою подписку<br>на Best-horoscope.ru</h2>
            <p>Ваша ссылка для сегодняшнего гороскопа:</p>
            <p class="aligncenter" style="padding: 40px 0;">
                <a href="/show/?usk=<?php echo $usk; ?>" class="button"><b>МОИ ГОРОСКОПЫ НА ВЧЕРА, СЕГОДНЯ И ЗАВТРА</b></a>
            </p>
    <?php endif; ?>
        </div>
    </div>
    
    <script src="//ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
    <script src="../js/jquery-migrate-1.2.1.min.js" type="text/javascript"></script>
    <script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.10.4/jquery-ui.min.js"></script>
    <script src="../js/jquery.bpopup.min.js" type="text/javascript"></script>
    <script src="../js/jquery.mousewheel-3.0.6.pack.js" type="text/javascript"></script>
    <script src="../js/fancybox/jquery.fancybox.pack.js?v=2.1.5" type="text/javascript"></script>
<!-- Yandex.Metrika counter -->
<script type="text/javascript">
(function (d, w, c) {
    (w[c] = w[c] || []).push(function() {
        try {
            w.yaCounter25119665 = new Ya.Metrika({id:25119665,
                    webvisor:true,
                    clickmap:true,
                    trackLinks:true,
                    accurateTrackBounce:true});
        } catch(e) { }
    });

    var n = d.getElementsByTagName("script")[0],
        s = d.createElement("script"),
        f = function () { n.parentNode.insertBefore(s, n); };
    s.type = "text/javascript";
    s.async = true;
    s.src = (d.location.protocol == "https:" ? "https:" : "http:") + "//mc.yandex.ru/metrika/watch.js";

    if (w.opera == "[object Opera]") {
        d.addEventListener("DOMContentLoaded", f, false);
    } else { f(); }
})(document, window, "yandex_metrika_callbacks");
</script>
<noscript><div><img src="//mc.yandex.ru/watch/25119665" style="position:absolute; left:-9999px;" alt="" /></div></noscript>
<!-- /Yandex.Metrika counter -->
</body>
</html>