<?php
    require_once('../admin/myconf.php');

    $uid = isset($_POST['uid']) ? $_POST['uid'] : $_COOKIE["vkuid"];
    $mid = isset($_COOKIE["vkuid"]) ? $_COOKIE["vkuid"] : $uid;
    $fid = isset($_POST['fid']) ? $_POST['fid'] : ($uid != $mid ? $uid : '');
    $gid = $fid ? $fid : $uid;

    $bdate = isset($_POST['bdate']) ? $_POST['bdate'] : '';
    $udt = isset($_POST['udt']) ? $_POST['udt'] : date("Ymd");
    $ubd = date("Ymd", strtotime($bdate));
    $uname = isset($_POST['uname']) ? $_POST['uname'] : '';
    $referrer = isset($_POST['referrer']) ? $_POST['referrer'] : '';
    
    $months = Array("январь","февраль","март","апрель","май","июнь","июль","август","сентябрь","октябрь","ноябрь","декабрь");
    $bymonth = Array("января","февраля","марта","апреля","мая","июня","июля","августа","сентября","октября","ноября","декабря");

    //$udt  = "20140717"; // для тестов

    $months = Array("январь","февраль","март","апрель","май","июнь","июль","август","сентябрь","октябрь","ноябрь","декабрь");
    $bymonth = Array("января","февраля","марта","апреля","мая","июня","июля","августа","сентября","октября","ноября","декабря");

    $myday = date("d",strtotime("$udt"));
    $mymonth = date("m",strtotime("$udt"));
    $myyear = date("Y",strtotime("$udt"));
    $cntdate = "$myday.$mymonth.$myyear";

    $check = "SELECT * FROM $gorogroup";
    $allgoro = mysql_query($check);
    while ($row = mysql_fetch_array($allgoro)) {
        $goroname[$row[0]] = $row[2];
        $gorotitle[$row[0]] = $row[1];
    }

    if($mid){
        $bonus = 10;
        $query = "SELECT * FROM _vkapps WHERE vkuser = $mid;";
        $result = mysql_query($query);
        $exists = mysql_num_rows($result);
        if(!$exists){
            $referrer!=$mid ? $referrer : 'NULL';
            $query = "INSERT INTO _vkapps (vkuser, vkuserbd, vkdate, vkstars, referrer) VALUES ($mid, $ubd, ".date("Ymd").", 30, $referrer);";
            $result = mysql_query($query);
            if(!$result){
                die ("Извините, непредвиденная ошибка - 1. Пожалуйста, обратитесь к разработчикам приложения.".mysql_error());
            }
            $stars = 30;
        }else{
            $stars = mysql_result($result, 0, 'vkstars');
            $vkdate = mysql_result($result, 0, 'vkdate');
            $vkuserbd = mysql_result($result, 0, 'vkuserbd');
            $refcount = mysql_result($result, 0, 'refcount');
            if(date("Ymd") > date("Ymd", strtotime($vkdate))){
                $stars += 3;
                $query = "UPDATE _vkapps SET vkstars = $stars, ".($ubd==$vkuserbd ? "" : "vkuserbd = $ubd, ")."vkdate = ".date("Ymd")." WHERE vkuser = $mid;";
                $result = mysql_query($query);
                if(!$result) die ("Извините, непредвиденная ошибка - 2. Пожалуйста, обратитесь к разработчикам приложения.");
            }else if($refcount){
                $query = "UPDATE _vkapps SET refcount = 0".($ubd==$vkuserbd ? "" : ", vkuserbd = $ubd")." WHERE vkuser = $mid;";
                $result = mysql_query($query);
                if(!$result) die ("Извините, непредвиденная ошибка - 3. Пожалуйста, обратитесь к разработчикам приложения.");
            }else if($ubd!=$vkuserbd){
                $query = "UPDATE _vkapps SET vkuserbd = $ubd WHERE vkuser = $mid;";
                $result = mysql_query($query);
                if(!$result) die ("Извините, непредвиденная ошибка - 3. Пожалуйста, обратитесь к разработчикам приложения.");
            }
            $referrer = '';
        }
        $stars_ = $stars>999 ? '999+' : $stars;
        $query = "SELECT * FROM _vkviews WHERE vkuser='$mid' AND vkfriend='$gid' AND vkgsk IN (";
        for($i=1; $i<=7; $i++){
            $secretkey = md5($i.$gid.$udt);
            $cgsk[$i] = $secretkey;
            $query .= "'".$secretkey."',";
        }
        $query = rtrim($query,",").");";
        $result = mysql_query($query);
        $exists = mysql_num_rows($result);

        $viewed = Array();
        for($i=0; $i<$exists; $i++){
            $viewed[] = mysql_result($result, $i, 'vkgsk');
        }
        setcookie("vkuid", $mid);
        $title = "Гороскоп на каждый день";
        
        if($referrer && $referrer != $mid){
            $query = "SELECT * FROM _vkapps WHERE vkuser = $referrer;";
            $result = mysql_query($query);
            $exists = mysql_num_rows($result);
            if($exists){
                $rstars = mysql_result($result, 0, 'vkstars')+$bonus;
                $rcount = mysql_result($result, 0, 'refcount')+1;
                $rearn = mysql_result($result, 0, 'refearn')+$bonus;
                $query = "UPDATE _vkapps SET vkstars = $rstars, refcount = $rcount, refearn = $rearn WHERE vkuser = $referrer;";
                $result = mysql_query($query);
                if(!$result) die ("Извините, непредвиденная ошибка - 4. Пожалуйста, обратитесь к разработчикам приложения.".mysql_error());
            }
        }

    }else{
        die ("Извините, непредвиденная ошибка - 5. Пожалуйста, обратитесь к разработчикам приложения.");
    }
?>
<script>
    $(".uname").text("<?php echo $uname; ?>");
    $(".stars").text("<?php echo $stars_; ?>");
    $(".stars").attr("title", "<?php echo $stars; ?>");
    $(".cntdate").text("<?php echo $cntdate; ?>");
  <?php if($refcount): ?>
    $.fancybox.open({content:"<h3>Бонус!</h3><p>Приведено друзей: <?php echo $refcount; ?><br>Начислено звезд: <?php echo $refcount*$bonus; ?><br>Поздравляем!</p>",topRatio:0,margin:[100, 20, 20, 20]});
  <?php endif; ?>
</script>
<?php if($gid): ?>
    <?php

        for($i=1; $i<=7; $i++){
            $sgn = $goroname[$i];
            $gn = $gorotitle[$i];
            if($sgn == '_sex') continue;
            echo "<div class='ourgoro shadowing' id='$sgn'>
                    <a href='?gsk=".$cgsk[$i]."&uid=$gid&udt=$udt' class='more'>
                        <b></b>
                    </a>";
            if(in_array($cgsk[$i], $viewed)){
               echo "<div style='position: absolute; margin-top: -25px; margin-left: 75px;'><img src='viewed.png' width='30' /></div>";
            }
            echo "</div>";
        }
        
    ?>
<?php else: ?>
    Извините, непредвиденная ошибка. Пожалуйста, обратитесь к разработчикам приложения.
<?php endif ?>