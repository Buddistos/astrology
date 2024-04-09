<?php
    require_once('../admin/myconf.php');

    $usk = isset($_GET['usk']) ? $_GET['usk'] : '';
    $order = isset($_GET['order']) ? $_GET['order'] : '';
    $promo = isset($_GET['promo']) ? $_GET['promo'] : '';
    $admin = isset($_GET['admin']) ? $_GET['admin'] : 0;

    if($usk){
        $check = "SELECT * FROM $clients WHERE secretkey = '$usk'";
        $result = mysql_query($check);
        $existsk = mysql_num_rows($result) or die ("Error! No person found.");
        $id_client = mysql_result($result, 0, 'id_client');
        $firstname = mysql_result($result, 0, 'firstname');
        $bday = date("d", strtotime(mysql_result($result, 0, "birthday")));
        $bmonth = date("m", strtotime(mysql_result($result, 0, "birthday")));
        $byear = date("Y", strtotime(mysql_result($result, 0, "birthday")));
        $email = mysql_result($result, 0, 'email');
        $bplace = mysql_result($result, 0, 'birthplace');
        list($hours, $minutes, $secs) = split(":", mysql_result($result, 0, 'birthtime'));
    }else{
        die ("Ошибка! Пользователь не найден.");
    }

?>
<html>
<head>
    <meta charset="utf-8">
    <title>Заявка на составление Натальной карты | Best-horoscope.ru</title>
    <script src="//ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
    <script type="text/javascript" src="../js/jquery-migrate-1.2.1.min.js"></script>
    
    <link href='http://fonts.googleapis.com/css?family=Seymour+Display|Marmelad&subset=cyrillic' rel='stylesheet' type='text/css'>
    <link href="//ajax.googleapis.com/ajax/libs/jqueryui/1.10.4/themes/smoothness/jquery-ui.css" rel="stylesheet" />
    <script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.10.4/jquery-ui.min.js"></script>
    
    <script src="../js/jquery.bpopup.min.js" type="text/javascript"></script>
    <script src="../js/jquery.mousewheel-3.0.6.pack.js" type="text/javascript"></script>

    <link rel="stylesheet" href="../js/fancybox/jquery.fancybox.css?v=2.1.5" type="text/css" media="screen" />
    <script src="../js/fancybox/jquery.fancybox.pack.js?v=2.1.5" type="text/javascript"></script>
    <script src="../js/jcarousellite_1.0.1.min.js" type="text/javascript"></script>
    
    <link rel="stylesheet" href="../js/jqueryformstyler/jquery.formstyler.css" type="text/css" media="screen" />
    <script src="../js/jqueryformstyler/jquery.formstyler.min.js" type="text/javascript"></script>

    <link href="../css/mystyle.css" rel="stylesheet" type="text/css" media="screen" />
    <script src="../js/myscript.js" type="text/javascript"></script>
</head>
<body>
    <div class="container" style="margin-top: 0;">
        <h1 class="attention textshadow" style="color: white;"><?php echo $order ? "Заказ" : "Заявка на составление" ?> Натальной карты</h1>
        <div class="block">
          <?php if($order): ?>
            <?php if(!$promo): ?>
                <p class="aligncenter"><b><?php echo $firstname; ?></b>,
                    спасибо за Ваш заказ.<br><br>
                    Стоимость составления натальной карты <strong>3000 рублей</strong>.</p>
                    <p class="aligncenter">
                        <br>
                        Чтобы перейти на страницу оплаты заказа нажмите кнопку ниже:<br>
                    <br>
                    <br>
                    <a class="button" href="https://nickoche.ecommtools.com/buy/1?noref=1">ПЕРЕЙТИ К ОПЛАТЕ НАТАЛЬНОЙ КАРТЫ</a>
                    <br>
                    <br>
                    <br>
                </p>
            <?php else: ?>
                <p class="aligncenter"><b><?php echo $firstname; ?></b>,
                    спасибо за Ваш заказ.<br><br>
                    Только сегодня у Вас есть возможность заказать<br> составление натальной карты со скидкой <strong>82%</strong>.</p>
                    <h3>Всего за 540 рублей</h3>
                    <p class="aligncenter">Вы экономите 2560 рублей!<br>
                        Для получения скидки используйте Промокупон: <strong>NATAL82</strong><br>
                        Чтобы перейти на страницу оплаты заказа и ввести промокупон, нажмите кнопку ниже:<br>
                    <br>
                    <br>
                    <a class="button" href="https://nickoche.ecommtools.com/buy/1?noref=1">ПЕРЕЙТИ К ОПЛАТЕ НАТАЛЬНОЙ КАРТЫ</a>
                    <br>
                    <br>
                    <br>
                </p>
            <?php endif ?>
          <?php else: ?>
            <p>
                Натальная карта - личный гороскоп, построенный на момент рождения человека.<br>
                Составление и интерпретация натальной карты занятие трудоемкое и требует от астролога как астрономических, так и математических знаний. Совместно со знаниями астрологии, эти знания позволяют формировать и интерпретировать положения планет и светил, зафиксированных в момент рождения человека. Поэтому для составления натальной карты необходимо учитывать все параметры рождения: время - желательно до минуты, место - желательно точное место рождения, чтобы при расчетах учитывать реально положение планет над человеком.
            </p>
            <p><b><?php echo $firstname; ?></b>, оставьте заявку на составление Вашей Натальной карты. Для этого проверьте свои данные, обязательно введите email, на который Вы получаете гороскопы, и нажмите кнопку <b>ОСТАВИТЬ ЗАЯВКУ</b>. Для составления Натальной карты все поля обязательны.</p>

            <div style="width: 600px; margin: 0 auto; display: block;" id="order" >
                <form action='ordernata.php' method='POST'>
                    <p><input type='text' name='firstname' placeholder='Имя' required style="margin-right: 20px;" value="<?php echo $firstname; ?>"><input type='email' name='email' placeholder='Проверочный email' required></p>
                    <!--p><input type='text' name='birthday' placeholder='День рождения (01.01.1970)' required id="datepicker" autocomplete="off"></p-->
                    <p>
                        <select class="width-80" name="myday">
                            <option value="0">День</option>
                          <?php
                            for($i=1; $i<=31; $i++){
                                $sel = $bday == $i ? "selected" : "";
                                echo "<option value='".($i<10 ? "0".$i : $i)."' $sel>$i</option>";
                            }
                          ?>
                        </select>
                        <select class="width-180" name="mymonth">
                            <option value="0">Месяц рождения</option>
                          <?php
                            $monthAr = array(
                                    1 => array('Январь', 'Января'),
                                    2 => array('Февраль', 'Февраля'),
                                    3 => array('Март', 'Марта'),
                                    4 => array('Апрель', 'Апреля'),
                                    5 => array('Май', 'Мая'),
                                    6 => array('Июнь', 'Июня'),
                                    7 => array('Июль', 'Июля'),
                                    8 => array('Август', 'Августа'),
                                    9 => array('Сентябрь', 'Сентября'),
                                    10=> array('Октябрь', 'Октября'),
                                    11=> array('Ноябрь', 'Ноября'),
                                    12=> array('Декабрь', 'Декабря')
                                );
                            for($i=1; $i<=12; $i++){
                                $sel = $bmonth == $i ? "selected" : "";
                                echo "<option value='".($i<10 ? "0".$i : $i)."' $sel>".$monthAr[$i][0]."</option>";
                            }
                          ?>
                        </select>
                        <select class="width-90" name="myyear">
                            <option value="0">Год</option>
                          <?php
                            for($i=2014; $i>=1914; $i--){
                                $sel = $byear == $i ? "selected" : "";
                                echo "<option value='$i' $sel>$i</option>";
                            }
                          ?>
                        </select>
                        <span style="float: left; margin: 5px 10px 0 0;width: 5px;">&nbsp;</span>
                        <select class="width-80" name="hours">
                            <option value="-1">Часы</option>
                          <?php
                            for($i=0; $i<=24; $i++){
                                $sel = $hours == $i ? "selected" : "";
                                echo "<option value='$i' $sel>$i</option>";
                            }
                          ?>
                        </select>
                        <span style="float: left; margin: 5px 10px 0 0;">:</span>
                        <select class="width-80" name="minutes">
                            <option value="-1">Минуты</option>
                          <?php
                            for($i=0; $i<=55; $i+=5){
                                $sel = $minutes == $i ? "selected" : "";
                                echo "<option value='$i' $sel>$i</option>";
                            }
                          ?>
                        </select>
                    </p>
                    <p><span>Место рождения: </span><input type='text' name='birthplace' placeholder='не определено' value='<?php echo $bplace; ?>' style="width: 410px;" required></p>
                    <p><input type='hidden' name='usk' value='<?php echo $usk; ?>'></p>
                    <p style="font-size: 18px;"><b class="attention" style="font-size: 18px;">Внимание!</b> Если Вы измените дату Вашего рождения, изменить её обратно Вы уже не сможете и будете получать ежедневные гороскопы с измененной датой.</h4>
                    <p class="inform aligncenter" style="float: none;"><input class="button" type='submit' value='ОСТАВИТЬ ЗАЯВКУ НА СОСТАВЛЕНИЕ НАТАЛЬНОЙ КАРТЫ' style="width: auto;"></p>
                    <div id="inform" class="inform">
                        <p align="center">Передача данных..</p>
                    </div>
                </form>
            </div>
          <?php endif ?>
        </div>
        <p style="font-size: 12px; color: #999; text-align: left; padding-bottom: 0;margin-bottom: 0;">© COPYRIGHT 2014 Все права защищены<br>
        г. Екатеринбург, <a href="/" style="font-size: 12px; text-decoration: none; color: #777;">Интернет-группа "Астроном и я"</a>, "Андрей Перье", ИП, ОГРНИП 310667016100048, +7 932-123-97-51, email: <a href="mailto:best-horoscope@yandex.ru" style="font-size: 12px; text-decoration: none; color: #777;">andreyperje@best-horoscope.ru</a>
        <br>Система расчетов разработана с помощью команды профессиональных астрологов
        </p>
    </div>
    <script type="text/javascript">
        $('form').submit(function(){
            $("#inform").html("<p align='center' style='color: green;'>Передача данных..</p>");
            //$(".inform").toggle();
            var s = $('form').serializeArray();
            $.post("ordernatal.php", s, function(data){
                $.fancybox.open([
                    {
                        content: data
                    },
                    {
                        index: 10000
                    }
                ]);
            });
            return false;
        });
        $(function() {
            $('a.fancy-close').click(function(){
                location.href = '/show/?usk='+usk;
            });
        });
    </script>

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