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

    $months = Array("январь","февраль","март","апрель","май","июнь","июль","август","сентябрь","октябрь","ноябрь","декабрь");
    $bymonth = Array("января","февраля","марта","апреля","мая","июня","июля","августа","сентября","октября","ноября","декабря");

    $day = 0;
    $myday = date("d",strtotime("$udt"));
    $mymonth = date("m",strtotime("$udt"));
    $myyear = date("Y",strtotime("$udt"));
    $cntdate = "$myday.$mymonth.$myyear";
    //$cntdate  = "12.07.2014"; // для тестов

    $goroday = date("Ymd");

    if($usk){
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
        if(!$so && $status == 2 && $days>$ftime){
            header("Location: http://best-horoscope.ru/week2/?usk=".$usk);
            exit;
        }

        for($i=1; $i<=7; $i++){
            $secretkey = md5($i.$id_client.$goroday);
            $cgsk[$i] = $secretkey;
        }

        $title = "Гороскоп на каждый день";
        setcookie("viewall", "1");
        setcookie("usk", $usk, time() + (86400 * 365 * 5), "/");
    }elseif($gsk){
        for($i=1; $i<=7; $i++){
            $sk = md5($i.$uid.$udt);
            if($gsk == $sk){
                $id_client = $uid;
                $id_gorogroup = $i;
            }
        }
        if(!$gsk){
            die ("Ошибка формирования гороскопа.");
        }
        $check = "SELECT * FROM $clients WHERE id_client = '$id_client'";
        $result = mysql_query($check);
        $existsk = mysql_num_rows($result) or die ("Error! No person found.");
        $firstname = mysql_result($result, 0, 'firstname');
        $usk = mysql_result($result, 0, 'secretkey');
        if(isset($_COOKIE["usk"]) && $usk != $_COOKIE["usk"]){
            $usk = "";
        }
        $birthday = date("d.m.Y", strtotime(mysql_result($result, 0, "birthday")));
        $curd = date("Ymd");
        $cgsk = md5($id_gorogroup.$id_client.$curd);
        $yesterday = $tomorrow = false;
        
        //получаем вчерашний гороскоп
        $yesd = date("Ymd",strtotime("-1 days"));
        $ydate = date("d.m.Y",strtotime("-1 days"));
        $ygsk = md5($id_gorogroup.$id_client.$yesd);

        //получаем завтрашний гороскоп
        $tomd = date("Ymd",strtotime("1 days"));
        $tdate = date("d.m.Y",strtotime("1 days"));
        $tgsk = md5($id_gorogroup.$id_client.$tomd);

        //получаем сегодняшний гороскоп
        if($gsk == $ygsk || $gsk == $tgsk){
            $yesterday = $gsk==$ygsk;
            $tomorrow = $gsk==$tgsk;
            $cntdate = $tomorrow ? $tdate : $ydate;
        }
        $title = "Индивидуальный ".$gorotitle[$id_gorogroup];
        $viewall = isset($_COOKIE["viewall"]) && $usk ? 1 : 0;
    }

?>
<?php if($gsk): ?>
    <div style="width:180px; height: 70px; float: right; "><a id="minigraph" href="javascript:void(0);" onclick="opengraph();"></a></div>
    <b>Имя:</b> <b class="mname"></b><br>
    <b>День рождения:</b> <b class="bdate"></b><br>
    <b>Расчетная дата:</b> <b><span class="attention"><?php echo $cntdate; ?></span></b><br>
    <br>
    <b><a href="#">Поделиться с друзьями</a></b><br>
    <br style="clear:both;">
    <div class="mygoro" style="margin: 0 0 30px 0; overflow: hidden; clear: both;">
        <?php require_once('makegraph.inc'); ?>
    </div>
<?php else: ?>
    <br>
    <b>Имя:</b> <b class="mname"></b><br>
    <b>День рождения:</b> <b class="bdate"></b><br>
    <br style="clear:both;">
    <br>
    <?php

        for($i=1; $i<=7; $i++){
            $sgn = $goroname[$i];
            $gn = $gorotitle[$i];
            if($sgn == '_sex') continue;
            echo "<div class='ourgoro' id='$sgn'>
                    <a href='?gsk=".$cgsk[$i]."&uid=$id_client&udt=$goroday' class='more'>
                        <b></b>
                    </a>";
            $imgname = $id_client."_".$i."_".$mymonth."_".$myyear.".jpg";
            echo "<a href='?gsk=".$cgsk[$i]."&uid=$id_client' class='fancybox' rel='group' title='$gn'><!--img align='center' src='/graphs/$imgname' style='width:200px;height: 100px;'/--></a>";
            echo "</div>";
        }
        
    ?>
<?php endif ?>