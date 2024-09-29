<?php
require_once('../admin/myconf.php');

$usk = isset($_GET['usk']) ? $_GET['usk'] : '';
$gsk = isset($_GET['gsk']) ? $_GET['gsk'] : '';
$uid = isset($_GET['uid']) ? $_GET['uid'] : '';
$udt = isset($_GET['udt']) ? $_GET['udt'] : '';

$check = "SELECT * FROM $gorogroup";
$allgoro = mysql_query($check);
while ($row = mysql_fetch_array($allgoro)) {
    $goroname[$row[0]] = $row[2];
    $gorotitle[$row[0]] = $row[1];
}

$months = array("январь", "февраль", "март", "апрель", "май", "июнь", "июль", "август", "сентябрь", "октябрь", "ноябрь", "декабрь");
$bymonth = array("января", "февраля", "марта", "апреля", "мая", "июня", "июля", "августа", "сентября", "октября", "ноября", "декабря");

$day = 0;
$myday = date("d", strtotime("$udt"));
$mymonth = date("m", strtotime("$udt"));
$myyear = date("Y", strtotime("$udt"));
$cntdate = "$myday.$mymonth.$myyear";
//$cntdate  = "12.07.2014"; // для тестов

$goroday = date("Ymd");

if ($usk) {
    $check = "SELECT * FROM $clients WHERE secretkey = '$usk'";
    $result = mysql_query($check);
    $existsk = mysql_num_rows($result) or die ("Error! No person found.");
    $id_client = mysql_result($result, 0, 'id_client');
    $firstname = mysql_result($result, 0, 'firstname');
    $birthday = date("d.m.Y", strtotime(mysql_result($result, 0, "birthday")));
    $updatedate = date("Y-m-d", strtotime(mysql_result($result, 0, "updatedate")));
    $status = mysql_result($result, 0, 'status');

    $datetime1 = new DateTime(date("Y-m-d"));
    $datetime2 = new DateTime($updatedate);
    $interval = $datetime1->diff($datetime2);
    $days = $interval->format('%a');
    $so = $_COOKIE["socialout"];
    if (!$so && $status == 2 && $days > $ftime) {
        header("Location: http://best-horoscope.ru/week2/?usk=" . $usk);
        exit;
    }

    for ($i = 1; $i <= 7; $i++) {
        $secretkey = md5($i . $id_client . $goroday);
        $cgsk[$i] = $secretkey;
    }

    $title = "Гороскоп на каждый день";
    setcookie("viewall", "1");
    setcookie("usk", $usk, time() + (86400 * 365 * 5), "/");
} elseif ($gsk) {
    for ($i = 1; $i <= 7; $i++) {
        $sk = md5($i . $uid . $udt);
        if ($gsk == $sk) {
            $id_client = $uid;
            $id_gorogroup = $i;
        }
    }
    if (!$gsk) {
        die ("Ошибка формирования гороскопа.");
    }
    $check = "SELECT * FROM $clients WHERE id_client = '$id_client'";
    $result = mysql_query($check);
    $existsk = mysql_num_rows($result) or die ("Error! No person found.");
    $firstname = mysql_result($result, 0, 'firstname');
    $usk = mysql_result($result, 0, 'secretkey');
    if (isset($_COOKIE["usk"]) && $usk != $_COOKIE["usk"]) {
        $usk = "";
    }
    $birthday = date("d.m.Y", strtotime(mysql_result($result, 0, "birthday")));
    $curd = date("Ymd");
    $cgsk = md5($id_gorogroup . $id_client . $curd);
    $yesterday = $tomorrow = false;

    //получаем вчерашний гороскоп
    $yesd = date("Ymd", strtotime("-1 days"));
    $ydate = date("d.m.Y", strtotime("-1 days"));
    $ygsk = md5($id_gorogroup . $id_client . $yesd);

    //получаем завтрашний гороскоп
    $tomd = date("Ymd", strtotime("1 days"));
    $tdate = date("d.m.Y", strtotime("1 days"));
    $tgsk = md5($id_gorogroup . $id_client . $tomd);

    //получаем сегодняшний гороскоп
    if ($gsk == $ygsk || $gsk == $tgsk) {
        $yesterday = $gsk == $ygsk;
        $tomorrow = $gsk == $tgsk;
        $cntdate = $tomorrow ? $tdate : $ydate;
    }
    $title = "Индивидуальный " . $gorotitle[$id_gorogroup];
    $viewall = isset($_COOKIE["viewall"]) && $usk ? 1 : 0;
}

?>
<html>
<head>
    <meta charset="utf-8">
    <title><?php echo $title; ?></title>
    <link rel="stylesheet" href="//ajax.googleapis.com/ajax/libs/jqueryui/1.10.4/themes/smoothness/jquery-ui.css"
          type="text/css"/>
    <link rel='stylesheet' href='http://fonts.googleapis.com/css?family=Seymour+Display|Marmelad&subset=cyrillic'
          type='text/css'>
    <link rel="stylesheet" href="../js/fancybox/jquery.fancybox.css?v=2.1.5" type="text/css" type="text/css"
          media="screen"/>
    <link rel="stylesheet" href="../css/mystyle.css" type="text/css" media="screen"/>

    <script src="//ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
    <script src="../js/jquery-migrate-1.2.1.min.js" type="text/javascript"></script>
    <script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.10.4/jquery-ui.min.js"></script>
    <script src="../js/jquery.bpopup.min.js" type="text/javascript"></script>
    <script src="../js/jquery.mousewheel-3.0.6.pack.js" type="text/javascript"></script>
    <script src="../js/fancybox/jquery.fancybox.pack.js?v=2.1.5" type="text/javascript"></script>
    <script src="../js/d3.min.js" type="text/javascript"></script>
    <style>
        .block .mygoro img {
            width: 55px;
            height: 17px;
        }

        .info p, .info b, .info span {
            font-size: 14px;
        }

        .container {
            margin-top: 0;
        }

        .fancybox {
            position: absolute;
            margin-top: -200px;
            margin-left: -100px;
        }

        .ourgoro a.more {
            padding-top: 70px;
        }

        .ourgoro h2 {
            margin-bottom: 145px;
            overflow: hidden;
            height: 40px;
            padding-top: 25px;
            font-size: 20px;
        }

        #goback {
            position: absolute;
            margin-left: 0;
            top: 40px;
        }
    </style>
    <style media="print">
        .noprint {
            display: none;
        }

        .forprint img {
            height: 100px;
        }
    </style>

</head>
<body>
<div class="container">
    <?php if ($gsk): ?>

        <h1 class="attention textshadow" style="color: white;"><?php echo $gorotitle[$id_gorogroup]; ?></h1>
        <div class="block">
            <div style="width:320px; height: 120px; float: right; "><a id="minigraph" href="javascript:void(0);"
                                                                       onclick="wscroll();"></a></div>
            <br>
            <span>Имя:</span> <b><?php echo "$firstname"; ?></b><br>
            <span>День рождения:</span> <b><?php echo "$birthday"; ?></b>
            <div class="noprint" style="width: 300px; margin-top: 0px;">
                <?php if ($viewall): ?>
                    <span>&nbsp;</span><br>
                    <div id="goback"><a href="<?php echo '/show/?usk=' . $usk; ?>"><img
                                src="/images/arrow-left.png"/></a></div>
                    <div style="float: left; width: 100px;">
                        <?php if ($yesterday): ?>
                            <b class="attention">Вчера</b>
                        <?php elseif ($ygsk): ?>
                            <a href="?gsk=<?php echo $ygsk . '&uid=' . $id_client . '&udt=' . $yesd; ?>">Вчера</a>
                        <?php endif ?>
                    </div>
                    <div style="float: right; width: 100px; text-align: right;">
                        <?php if ($tomorrow): ?>
                            <b class="attention">Завтра</b>
                        <?php elseif ($tgsk): ?>
                            <a href="?gsk=<?php echo $tgsk . '&uid=' . $id_client . '&udt=' . $tomd; ?>">Завтра</a>
                        <?php endif ?>
                    </div>
                    <div style="margin:0 auto; width: 100px;">
                        <?php if ($curd == $udt && !$yesterday && !$tomorrow): ?>
                            <b class="attention">Сегодня</b>
                        <?php else: ?>
                            <a href="?gsk=<?php echo $cgsk . '&uid=' . $id_client . '&udt=' . $curd; ?>">Сегодня</a>
                        <?php endif ?>
                    </div>
                    <br style="clear:both;">
                <?php endif; ?>
                <span>Гороскоп на </span> <b><span class="attention"><?php echo $cntdate; ?></span></b>
                <br style="clear:both;">
                <br style="clear:both;">
            </div>

            <div class="mygoro" style="margin: 0 0 30px 0; overflow: hidden;">
                <p class="aspects" style="margin: 0 0 20px 0;">
                    <?php require_once('makegraph.php'); ?>
                </p>
            </div>
        </div>
    <?php else: ?>
        <h1 class="attention textshadow" style="color: white;">Выберите тематику гороскопа</h1>
        <div style="position:absolute; margin-left: 730px; margin-top: 30px;"><a href="/natal/?usk=<?php echo $usk; ?>"
                                                                                 target="_blank">Натальная карта</a>
        </div>
        <div class="block">
            <br>
            <span>Имя:</span> <b><?php echo "$firstname"; ?></b><br>
            <span>День рождения:</span> <b><?php echo "$birthday"; ?></b><br>
            <!--b>Расчетная дата: <span class="attention"><?php echo "$myday.$mymonth.$myyear"; ?></span></b-->
            <br style="clear:both;">
            <br>
            <?php

            for ($i = 1; $i <= 7; $i++) {
                $sgn = $goroname[$i];
                $gn = $gorotitle[$i];
                if ($sgn == '_sex') continue;
                echo "<div class='ourgoro' id='$sgn'>
                                    <a href='?gsk=" . $cgsk[$i] . "&uid=$id_client&udt=$goroday' class='more'>
                                        <h2>$gn</h2>
                                        <b>подробнее</b>
                                    </a>";
                $imgname = $id_client . "_" . $i . "_" . $mymonth . "_" . $myyear . ".jpg";
                echo "<a href='?gsk=" . $cgsk[$i] . "&uid=$id_client' class='fancybox' rel='group' title='$gn'><!--img align='center' src='/graphs/$imgname' style='width:200px;height: 100px;'/--></a>";
                echo "</div>";
            }

            ?>

            <div class="info">
                <h4>Как читать гороскоп</h4>
                <p>В конце каждого ежедневного прогноза Вы увидите график оценки влияния планет на этот день. График
                    охватывает диапазон в один месяц, начиная с выбранного дня прогноза. Таким образом, Вы сможете
                    визуально оценить влияние планет на следующие 30 календарных дней.<br>
                    Точки на графике отображают благоприятные возможности и неприятности, которые могут случиться. Сила
                    аспектов говорит о том, насколько сильно планеты оказывают воздействие на жизнь. Можно сказать, что
                    это вероятность события. Но, естественно, 100% она не может быть - ведь неизвестно, что именно может
                    произойти. Например, одинаковый прогноз, обещающий обогащение для нищего и короля, одному даст корку
                    хлеба, а другому мешок золота. При этом и тот, и другой сочтут прогноз сбывшимся.<br>
                    И все зависит только от Вас.<br>
                    Если на графике Вы видите неблагоприятный период, то следует быть предельно внимательным в аспектах,
                    которые указаны в прогнозе.<br>
                    Но, это всего лишь предупреждение, как прогноз погоды - если обещают дождь, то нужно взять зонт.
                    Зонтом в Вашей жизни является внимательность и осознанность.<br>
                    Например, Вы получили указание на день, что возможны нервные срывы, гнев и как, следствие,
                    разрушение отношений с партнером. Но, если Вы осознаете каждый миг и внимательны к окружающим Вас
                    мелочам, то Вас вряд ли что-то сможет вывести из себя и заставить даже чуточку приблизится к
                    прогнозу. А если Вы "засыпаете" и позволяете вовлечь себя в круговорот событий, не осознавая этого
                    движения, то вероятность того, что все произойдет именно так, крайне высока.<br>
                    Принимайте астропрогноз как указание, на что следует уделить особое внимание в этот день, и самое
                    главное УДЕЛЯЙТЕ этому внимание, тогда Вы сами не заметите, что станете осознавать каждый свой шаг и
                    астропрогноз станет для Вас привычным, как и прогноз погоды, негативные аспекты перестанут быть
                    пугающими, а благоприятные будут указывать верный путь для достижения ваших целей.</p>
                <p><img border="0" src="b_plus.gif"> Поднимаясь выше уровня плюс 50%, график показывает наиболее
                    благоприятные для Вас периоды.<br>
                    <img border="0" src="b_minus.gif"> График, опускаясь ниже уровня минус 50%, показывает наиболее
                    напряженные, может быть, даже опасные в некотором смысле дни. </p>
                <p class="aligncenter">Пример графика:<br><img border="0" class="shadowing" src="/images/graph.jpg"
                                                               align="center"/></p>
                <p><b>Если график идет на уровне 0% - это значит, что в эти дни нет аспектов между транзитными планетами
                        и планетами Вашего гороскопа, а значит, нет и символизируемых этим аспектами влияний и событий.
                        Проще говоря, спокойный, обычный день без каких-либо особенностей... Соответственно и в тексте
                        гороскопа на этот день ничего не написано. </b></p>
                <p>Поскольку транзитные планеты движутся сравнительно медленно, "влияние" их аспектов сохраняется
                    несколько дней, поэтому в описании каждого такого дня могут повторяться одни и те же события и
                    рекомендации.<br>
                    Не следует смущаться, что в Вашем гороскопе в один день есть и хорошие, и плохие "предсказания" -
                    наша жизнь действительно разнообразна и подвержена многим различным влияниям. Как говорится, "и
                    хочется, и колется"... Если указания на данный день противоречивы, они отражают противоречивость,
                    сложность Вашей ситуации - и лучше всего в такой день не принимать серьезных решений. И все же, зная
                    все различные возможности, Вы сможете всесторонне оценить ситуацию и решить для себя, как поступить.<br>
                    Цель гороскопа - предупредить Вас о благоприятных возможностях и неприятностях, которые могут
                    случиться. Но очень многое зависит от Вас, от Вашего поведения. Если предстоит плохой и даже опасный
                    период, следует учесть предупреждение судьбы и не рисковать, не ввязываться в авантюры, быть
                    осторожным и предусмотрительным - все пройдет и наступит хорошее время. В крайнем случае,
                    неприятности будут умеренными. С другой стороны, благоприятные возможности надо использовать, иначе
                    они останутся всего лишь упущенными возможностями...<br>
                    В конце концов, свободная воля дана нам Творцом именно для того, чтобы мы свободно выбирали между
                    Добром и Злом, учитывая и используя, по мере возможности, предупреждения и подсказки Судьбы.</p>
            </div>
        </div>
    <?php endif ?>
    <p style="font-size: 12px; color: #999; text-align: left; padding-bottom: 0;margin-bottom: 0; padding-left: 40px;">©
        COPYRIGHT 2014 Все права защищены<br>
        г. Екатеринбург, <a href="/" style="font-size: 12px; text-decoration: none; color: #777;">Интернет-группа
            "Астроном и я"</a>, "Андрей Перье", ИП, ОГРНИП 310667016100048, +7 932-123-97-51, email: <a
            href="mailto:best-horoscope@yandex.ru" style="font-size: 12px; text-decoration: none; color: #777;">andreyperje@best-horoscope.ru</a>
        <br>Система расчетов разработана с помощью команды профессиональных астрологов
    </p>
</div>

<!-- Yandex.Metrika counter -->
<script type="text/javascript">
    function wscroll() {
        var st;
        var s = ws = 0;
        st = setInterval(function () {
            s = $(window).scrollTop();
            $(window).scrollTop(s + 10);
            if ($(window).scrollTop() <= ws) clearInterval(st);
            console.log(s, ws);
            ws = s;
        });
    }

    (function (d, w, c) {
        (w[c] = w[c] || []).push(function () {
            try {
                w.yaCounter25119665 = new Ya.Metrika({
                    id: 25119665,
                    webvisor: true,
                    clickmap: true,
                    trackLinks: true,
                    accurateTrackBounce: true
                });
            } catch (e) {
            }
        });

        var n = d.getElementsByTagName("script")[0],
            s = d.createElement("script"),
            f = function () {
                n.parentNode.insertBefore(s, n);
            };
        s.type = "text/javascript";
        s.async = true;
        s.src = (d.location.protocol == "https:" ? "https:" : "http:") + "//mc.yandex.ru/metrika/watch.js";

        if (w.opera == "[object Opera]") {
            d.addEventListener("DOMContentLoaded", f, false);
        } else {
            f();
        }
    })(document, window, "yandex_metrika_callbacks");
</script>
<noscript>
    <div><img src="//mc.yandex.ru/watch/25119665" style="position:absolute; left:-9999px;" alt=""/></div>
</noscript>
<!-- /Yandex.Metrika counter -->
</body>
</html>
