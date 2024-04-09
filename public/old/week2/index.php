<?php
    require_once('../admin/myconf.php');

    $usk = isset($_GET['usk']) ? $_GET['usk'] : '';
    $gsk = isset($_GET['gsk']) ? $_GET['gsk'] : '';
    $goro = isset($_GET['goro']) ? $_GET['goro'] : '';
    $admin = isset($_GET['admin']) ? $_GET['admin'] : 0;

    if($usk){
        $check = "SELECT * FROM $clients WHERE secretkey = '$usk'";
        $result = mysql_query($check);
        $existsk = mysql_num_rows($result) or die ("Error! No person found.");
        $id_client = mysql_result($result, 0, 'id_client');
        $firstname = mysql_result($result, 0, 'firstname');
        $birthday = date("d.m.Y", strtotime(mysql_result($result, 0, "birthday")));
        $updatedate = date("Y-m-d", strtotime(mysql_result($result, 0, "updatedate")));
        $status = mysql_result($result, 0, 'status');
    }else{
        die ("Ошибка! Пользователь не найден.");
    }

    $datetime1 = new DateTime(date("Y-m-d"));
    $datetime2 = new DateTime($updatedate);
    $interval = $datetime1->diff($datetime2);
    $days = $interval->format('%a');

    if(!$admin && (($status == 2 && $days<=10) || in_array($status, Array(0, 1, 3)))){
        header("Location: http://best-horoscope.ru/show/?usk=".$usk);
        exit;
    }

?>
<html>
<head>
    <meta charset="utf-8">
    <title>Продление подписки на Best-horoscope.ru</title>
    <link rel="stylesheet" href="//ajax.googleapis.com/ajax/libs/jqueryui/1.10.4/themes/smoothness/jquery-ui.css" type="text/css"/>
    <link rel='stylesheet' href='http://fonts.googleapis.com/css?family=Seymour+Display|Marmelad&subset=cyrillic' type='text/css'>
    <link rel="stylesheet" href="../js/fancybox/jquery.fancybox.css?v=2.1.5" type="text/css" type="text/css" media="screen" />
    <link rel="stylesheet" href="../css/mystyle.css" type="text/css" media="screen" />
    <style>
        .block .mygoro img{
            width: 55px;
            height: 17px;
        }
        .info p, .info b, .info span{
            font-size: 14px;
        }
        .container{
            margin-top: 0;
        }
        .fancybox{
            position: absolute;
            margin-top: -200px;
            margin-left: -100px;
        }
        .ourgoro a.more{
            padding-top: 70px;
        }
        .ourgoro h2{
            margin-bottom: 145px;
            overflow: hidden;
            height: 40px;
            padding-top: 25px;
            font-size: 20px;
        }
        #goback{
            position: absolute;
            margin-left: 0;
            top: 40px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1 class="attention textshadow" style="color: white;"><?php echo $firstname; ?>, Вам нравится наш сайт?</h1>
        <div class="block">
            <h2>Мы <b class="attention" style="font-size: 32px;">никогда</b> и ни в каком виде не будем брать деньги за ежедневные гороскопы. Просто расскажите о нас своим друзьям и Вы не увидите больше эту страницу. Поделитесь со своими друзями ссылкой на наш сайт.</h2>
            <center id="sociallocker-5330">Загрузка замка...</center>

            <?php
                
                /*
                $check = "SELECT * FROM $gorogroup";
                $allgoro = mysql_query($check);
                while ($row = mysql_fetch_array($allgoro)) {
                    $goroname[$row[2]] = Array($row[0],$row[1]);
                    $goronum[$row[0]] = Array($row[1],$row[2]);
                }

                for($i=1; $i<=7; $i++){
                    $gn = $goronum[$i][0];
                    $sgn = $goronum[$i][1];
                    if($sgn == '_sex') continue;
                    echo "<div class='ourgoro' id='$sgn'>
                            <a href='?gsk=$cgsk[$sgn]&goro=$sgn' class='more'>
                                <h2>$gn</h2>
                                <b>подробнее</b>
                            </a>";
                    $imgname = $id_client."_".$i."_".$mymonth."_".$myyear.".jpg";
                    echo "<a href='?gsk=$cgsk[$sgn]&goro=$sgn' class='fancybox' rel='group' title='$gn'><img align='center' src='/graphs/$imgname' style='width:200px;height: 100px;'/></a>";
                    echo "</div>";
                }
                */
            ?>
            
        </div>
    </div>
    
    <script src="//ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
    <script src="../js/jquery-migrate-1.2.1.min.js" type="text/javascript"></script>
    <script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.10.4/jquery-ui.min.js"></script>
    <script src="../js/jquery.bpopup.min.js" type="text/javascript"></script>
    <script src="../js/jquery.mousewheel-3.0.6.pack.js" type="text/javascript"></script>
    <script src="../js/fancybox/jquery.fancybox.pack.js?v=2.1.5" type="text/javascript"></script>
    <script type="text/javascript">
        var usk = '<?php echo $usk; ?>';
        $(function() {
            $('a.fancy-close').click(function(){
                location.href = '/show/?usk='+usk;
            });
        });
    </script>
<!-- Начало кода Социального Замка (id:5330) -->
<script type="text/javascript">
if ('undefined' == typeof(sl_sociallockers)) { var sl_sociallockers = [5330]; } else { sl_sociallockers.push(5330); } var sl5330_jqi = false; var sl5330_ale = false; function sl5330_iJQ() { if (!window.jQuery) { if (!sl5330_jqi) { if (typeof $ == 'function') { sl5330_ale = true; } var script = document.createElement('script'); script.type = "text/javascript"; script.src = "//ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js"; document.getElementsByTagName('head')[0].appendChild(script); sl5330_jqi = true; } setTimeout('sl5330_iJQ()', 50); } else { if (true == sl5330_ale) { $j = jQuery.noConflict(); } else { $j = jQuery; }
var sociallocker5330 = 'eyJpZCI6IjUzMzAiLCJwYWdlX3VybCI6Imh0dHA6XC9cL2Jlc3QtaG9yb3Njb3BlLnJ1XC8iLCJjb3B5cmlnaHRzIjoib24iLCJoaWRlX3VzZWQiOiJvbiIsImNvcHlyaWdodHNfbGluayI6Imh0dHA6XC9cL2hvcC5jbGlja2dldC5ydVwvZjBmYzhhZjM5YzEzNmU1ODE3ZDAyM2RkNGZjOGYwMWFcLyIsInRpdGxlMSI6Ik1TNGcwS0RRc05HQjBZSFF1dEN3MExiUXVOR0MwTFVnMExUUmdOR0QwTGZSak5HUDBMd2cwTDRnMEwzUXNDRFF2ZEN3MFlqUXVOR0ZJTkN6MEw3UmdOQyswWUhRdXRDKzBMXC9Rc05HRkxDRFFzdEdMMExIUXRkR0EwTGpSZ3RDMUlOR0IwTDdSaHRDNDBMRFF1OUdNMEwzUmc5R09JTkdCMExYUmd0R01JTkM5MExqUXR0QzFPZz09Iiwic3VidGl0bGUxIjoiMExnZzBMelJpeURRc2RDKzBMdlJqTkdJMExVZzBMM1F0U0RRc2RHRDBMVFF0ZEM4SU5DXC8wTDdRdXRDdzBMZlJpOUN5MExEUmd0R01JTkNTMExEUXZDRFJqZEdDMFlNZzBZSFJndEdBMExEUXZkQzQwWWJSZ3c9PSIsInRpdGxlMiI6IiIsImNoYW5nZV9hZnRlcl9vcGVuaW5nIjoib24iLCJ0aXRsZTFfb3BlbmVkIjoiTVM0ZzBKTFJpeURRdk5DKzBMYlF0ZEdDMExVZzBMXC9RdnRDMDBMWFF1OUM0MFlMUmpOR0IwWThnMExYUmlkQzFJTkN5SU5DKzBMVFF2ZEMrMExrZzBZSFF2dEdHMFlIUXRkR0MwTGc9Iiwic3VidGl0bGUxX29wZW5lZCI6IjBMQWcwTHJRdmRDKzBMXC9RdXRDd0lOR0QwTGJRdFNEUXZ0R0MwTHJSZ05HTDBZTFFzQ0RRdUNEUXRkR1JJTkM4MEw3UXR0QzkwTDRnMEwzUXNOQzUwWUxRdUNEUmg5R0QwWUxSakNEUXZkQzQwTGJRdFE9PSIsInRpdGxlMl9vcGVuZWQiOiJNaTRnMEtIUXY5Q3cwWUhRdU5DeDBMNGhJTkNhMEx2UXVOQzYwTDNRdU5HQzBMVWcwTDNRc0NEUXV0QzkwTDdRdjlDNjBZTWcwTFRRdTlHUElOQ1wvMExYUmdOQzEwWVhRdnRDMDBMQWcwTG9nMExQUXZ0R0EwTDdSZ2RDNjBMN1F2OUN3MEx3aCIsInN1YnRpdGxlMl9vcGVuZWQiOiIwTGdnMEx6Uml5RFFzZEMrMEx2UmpOR0kwTFVnMEwzUXRTRFFzZEdEMExUUXRkQzhJTkNcLzBMN1F1dEN3MExmUmk5Q3kwTERSZ3RHTUlOQ1MwTERRdkNEUmpkR0MwWU1nMFlIUmd0R0EwTERRdmRDNDBZYlJndz09IiwiY2xvc2VkX2FyZWFfdGV4dF9jbG9zZWQiOiIwSmZRdE5DMTBZSFJqQ0RRdjlDKzBZXC9Rc3RDNDBZTFJqTkdCMFk4ZzBMclF2ZEMrMExcL1F1dEN3SU5DMDBMdlJqeURRdjlHQTBMN1F0TkM3MExYUXZkQzQwWThnMExIUXRkR0IwTFwvUXU5Q3cwWUxRdmRDKzBMa2cwTFwvUXZ0QzAwTFwvUXVOR0IwTHJRdUE9PSIsIm1vZHVsZV93aGVyZV9sb2FkZWQiOiJzaXRlIiwidGFicyI6eyJmYWNlYm9va19saWtlIjp7InNob3dfY291bnQiOiJvbiIsImRlbGF5IjoiMCIsImFwcGlkIjoiIn0sInZrb250YWt0ZV9zaGFyZSI6eyJjYXB0aW9uIjoiXHUwNDFmXHUwNDNlXHUwNDM0XHUwNDM1XHUwNDNiXHUwNDM4XHUwNDQyXHUwNDRjXHUwNDQxXHUwNDRmIn0sInR3aXR0ZXJfdHdlZXQiOnsidGV4dCI6Ilx1MDQxYlx1MDQ0M1x1MDQ0N1x1MDQ0OFx1MDQzOFx1MDQzOSBcdTA0NDFcdTA0MzVcdTA0NDBcdTA0MzJcdTA0MzhcdTA0NDEgXHUwNDMwXHUwNDQxXHUwNDQyXHUwNDQwXHUwNDNlXHUwNDNmXHUwNDQwXHUwNDNlXHUwNDMzXHUwNDNkXHUwNDNlXHUwNDM3XHUwNDNlXHUwNDMyIFx1MDQzZFx1MDQzMCBCZXN0LWhvcm9zY29wZS5ydSIsInNob3J0X3VybCI6IiIsInJlbGF0ZWQiOiIiLCJsYXN0X3VybCI6Imh0dHA6XC9cL2Jlc3QtaG9yb3Njb3BlLnJ1XC8iLCJzaG93X2NvdW50Ijoib24ifSwiZ29vZ2xlcGx1cyI6eyJzaG93X2NvdW50Ijoib24iLCJkZWxheSI6IjAifSwibWFpbF9saWtlIjp7ImJvcmRlcl90eXBlIjoiMSIsInRleHRfbW9pbWlyIjoiMSIsInRleHRfb2Rub2tsYXNzbmlraSI6IjEiLCJ3aGF0X2J1dHRvbnMiOiJjb21ibyIsInNob3dfY291bnQiOiJvbiJ9fSwiZ2lmdHMiOlt7ImFjdGlvbnMiOiIxIiwiY29udGVudCI6IjxwIHN0eWxlPVwidGV4dC1hbGlnbjogY2VudGVyO1wiPlxuXHQmbmJzcDs8XC9wPlxuPHAgc3R5bGU9XCJ0ZXh0LWFsaWduOiBjZW50ZXI7XCI+XG5cdDxhIGNsYXNzPVwiYnV0dG9uIHNob3dfbW9kYWxcIiBocmVmPVwiamF2YXNjcmlwdDp2b2lkKDApO1wiPjxiPlx1MDQxZlx1MDQyMFx1MDQxZVx1MDQxNFx1MDQxYlx1MDQxOFx1MDQyMlx1MDQyYyBcdTA0MWZcdTA0MWVcdTA0MTRcdTA0MWZcdTA0MThcdTA0MjFcdTA0MWFcdTA0MjM8XC9iPjxcL2E+PFwvcD5cbjxwIHN0eWxlPVwidGV4dC1hbGlnbjogY2VudGVyO1wiPlxuXHQmbmJzcDs8XC9wPlxuPHNjcmlwdD5cbiAgJC5mYW5jeWJveC5jbG9zZVxuICAkKCdhLnNob3dfbW9kYWwnKS5jbGljayhmdW5jdGlvbigpe1xuXHQkLmFqYXgoe1xuXHRcdHR5cGVcdFx0OiBcIlBPU1RcIixcblx0XHRjYWNoZVx0ICAgICAgICA6IGZhbHNlLFxuXHRcdHVybFx0XHQ6IFwiY29udGludWUucGhwXCIsXG5cdFx0ZGF0YVx0XHQ6IHsndXNrJzp1c2t9LFxuXHRcdHN1Y2Nlc3M6IGZ1bmN0aW9uKGRhdGEpIHtcblx0XHRcdCQuZmFuY3lib3goZGF0YSk7XG5cdFx0fVxuXHR9KTtcbiAgfSk7XG48XC9zY3JpcHQ+IiwiZ2lmdF9wcmVjb250ZW50IjoiXHUwNDE3XHUwNDM0XHUwNDM1XHUwNDQxXHUwNDRjIFx1MDQzZlx1MDQzZVx1MDQ0Zlx1MDQzMlx1MDQzOFx1MDQ0Mlx1MDQ0Y1x1MDQ0MVx1MDQ0ZiBcdTA0M2FcdTA0M2RcdTA0M2VcdTA0M2ZcdTA0M2FcdTA0MzAgXHUwNDM0XHUwNDNiXHUwNDRmIFx1MDQzZlx1MDQ0MFx1MDQzZVx1MDQzNFx1MDQzYlx1MDQzNVx1MDQzZFx1MDQzOFx1MDQ0ZiBcdTA0MzFcdTA0MzVcdTA0NDFcdTA0M2ZcdTA0M2JcdTA0MzBcdTA0NDJcdTA0M2RcdTA0M2VcdTA0MzkgXHUwNDNmXHUwNDNlXHUwNDM0XHUwNDNmXHUwNDM4XHUwNDQxXHUwNDNhXHUwNDM4In1dLCJjbG9zZWRfYXJlYV90ZXh0X29wZW5lZCI6IlBIQWdjM1I1YkdVOUluUmxlSFF0WVd4cFoyNDZJR05sYm5SbGNqc2lQZ29KSm01aWMzQTdQQzl3UGdvOGNDQnpkSGxzWlQwaWRHVjRkQzFoYkdsbmJqb2dZMlZ1ZEdWeU95SStDZ2s4WVNCamJHRnpjejBpWW5WMGRHOXVJSE5vYjNkZmJXOWtZV3dpSUdoeVpXWTlJbXBoZG1GelkzSnBjSFE2ZG05cFpDZ3dLVHNpUGp4aVB0Q2YwS0RRbnRDVTBKdlFtTkNpMEt3ZzBKXC9RbnRDVTBKXC9RbU5DaDBKclFvend2WWo0OEwyRStQQzl3UGdvOGNDQnpkSGxzWlQwaWRHVjRkQzFoYkdsbmJqb2dZMlZ1ZEdWeU95SStDZ2ttYm1KemNEczhMM0ErQ2p4elkzSnBjSFErQ2lBZ0pDNW1ZVzVqZVdKdmVDNWpiRzl6WlFvZ0lDUW9KMkV1YzJodmQxOXRiMlJoYkNjcExtTnNhV05yS0daMWJtTjBhVzl1S0NsN0Nna2tMbUZxWVhnb2V3b0pDWFI1Y0dVSkNUb2dJbEJQVTFRaUxBb0pDV05oWTJobENTQWdJQ0FnSUNBZ09pQm1ZV3h6WlN3S0NRbDFjbXdKQ1RvZ0ltTnZiblJwYm5WbExuQm9jQ0lzQ2drSlpHRjBZUWtKT2lCN0ozVnpheWM2ZFhOcmZTd0tDUWx6ZFdOalpYTnpPaUJtZFc1amRHbHZiaWhrWVhSaEtTQjdDZ2tKQ1NRdVptRnVZM2xpYjNnb1pHRjBZU2s3Q2drSmZRb0pmU2s3Q2lBZ2ZTazdDand2YzJOeWFYQjBQZz09In0=';
jQuery.ajax({ type: 'POST', url: '/sociallocker/sociallocker.php?type=js', data: {'settings' : sociallocker5330}, dataType: "script" });
}} sl5330_iJQ();
</script>
<!-- Конец кода Социального Замка -->
<h2 class="attention textshadow" style="margin: 60px 0 40px;color: white;">или</h2>
<p class="aligncenter" style="padding: 40px 0;">
    <a href="javascript:void(0);" class="button show_modal fancy-close" onclick="document.cookie='socialout=1;expires='+(Math.round((new Date(now)).getTime() / 1000)+ 3600)+';path=/;';"><b>ПЕРЕЙДИТЕ НА СТРАНИЦУ С ГОРОСКОПАМИ</b></a>
</p>
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