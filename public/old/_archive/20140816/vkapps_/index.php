<?php
    
    require_once('../admin/myconf.php');

    $uid = isset($_GET['uid']) ? $_GET['uid'] : $_COOKIE["vkuid"];
    $mid = isset($_COOKIE["vkuid"]) ? $_COOKIE["vkuid"] : $uid;
    $fid = isset($_GET['fid']) ? $_GET['fid'] : ($uid != $mid ? $uid : '');
    $gid = $fid ? $fid : $uid;

    $gsk = isset($_GET['gsk']) ? $_GET['gsk'] : '';
    $udt = isset($_GET['udt']) ? $_GET['udt'] : '';
    
    $check = "SELECT * FROM $gorogroup";
    $allgoro = mysql_query($check);
    while ($row = mysql_fetch_array($allgoro)) {
        $goroname[$row[0]] = $row[2];
        $gorotitle[$row[0]] = $row[1];
    }

    $goroday = date("Ymd");
    

    $title = "Астрологический календарь";
    $gid = $fid ? $fid : $uid;
    if($mid){
        $query = "SELECT * FROM _vkapps WHERE vkuser = $mid;";
        $result = mysql_query($query);
        $exists = mysql_num_rows($result);
        if($exists){
            $stars = mysql_result($result, 0, 'vkstars');
            $birthday = mysql_result($result, 0, 'vkuserbd');
        }else{
            die ("Извините, непредвиденная ошибка. Пожалуйста, обратитесь к разработчикам приложения.");
        }
    }
    if($fid){
        $query = "SELECT * FROM _vkapps WHERE vkuser = $fid;";
        $result = mysql_query($query);
        $exists = mysql_num_rows($result);
        if($exists){
            $birthday = mysql_result($result, 0, 'vkuserbd');
        }else{
            die ("Извините, непредвиденная ошибка. Пожалуйста, обратитесь к разработчикам приложения.");
        }
    }
    if($gsk && $uid){
        $query = "SELECT * FROM _vkviews WHERE vkgsk = '$gsk' AND vkuser = '$mid' AND vkfriend = '$gid';";
        $result = mysql_query($query);
        $exists = mysql_num_rows($result);
        if(!$exists && $stars>0){
            $stars--;
            $query = "INSERT INTO _vkviews (vkgsk, vkuser, vkfriend) VALUES ('$gsk', '$mid', '$gid');";
            $result1 = mysql_query($query);
            if(!$result1){
                die ("Извините, непредвиденная ошибка. Пожалуйста, обратитесь к разработчикам приложения.".mysql_error());
            }
            $query = "UPDATE _vkapps SET vkstars = $stars WHERE vkuser = $mid;";
            $result2 = mysql_query($query);
            $viewed = 1;
            if(!$result2){
                die ("Извините, непредвиденная ошибка. Пожалуйста, обратитесь к разработчикам приложения.");
            }
        }elseif($exists){
            $viewed = 1;
        }
        $stars_ = $stars>999 ? '999+' : $stars;

        $months = Array("январь","февраль","март","апрель","май","июнь","июль","август","сентябрь","октябрь","ноябрь","декабрь");
        $bymonth = Array("января","февраля","марта","апреля","мая","июня","июля","августа","сентября","октября","ноября","декабря");

        $day = 0;
        $myday = date("d",strtotime("$udt"));
        $mymonth = date("m",strtotime("$udt"));
        $myyear = date("Y",strtotime("$udt"));
        $cntdate = "$myday.$mymonth.$myyear";

        for($i=1; $i<=7; $i++){
            $sk = md5($i.$gid.$udt);
            if($gsk == $sk){
                $id_gorogroup = $i;
            }
        }
        //получаем сегодняшний гороскоп
        $title = $gorotitle[$id_gorogroup];
    }
?>
<html>
<head>
    <meta charset="utf-8">
    <title><?php echo $title; ?></title>
    <link rel="stylesheet" href="//ajax.googleapis.com/ajax/libs/jqueryui/1.10.4/themes/smoothness/jquery-ui.css" type="text/css"/>
    <link rel='stylesheet' href='//fonts.googleapis.com/css?family=Seymour+Display|Marmelad&subset=cyrillic' type='text/css'>
    <link rel="stylesheet" href="../js/fancybox/jquery.fancybox.css?v=2.1.5" type="text/css" type="text/css" media="screen" />
    <link rel="stylesheet" href="mystyle.css" type="text/css" media="screen" />

    <script src="//ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
    <script src="../js/jquery-migrate-1.2.1.min.js" type="text/javascript"></script>
    <script src="../js/fancybox/jquery.fancybox.pack.js?v=2.1.5" type="text/javascript"></script>
    <script src="../js/d3.min.js" type="text/javascript"></script>
    <script src="../js/slider.js" type="text/javascript"></script>
    <script src="//vk.com/js/api/xd_connection.js?2"  type="text/javascript"></script>
    <script type="text/javascript" src="//vk.com/js/api/share.js?90" charset="windows-1251"></script>

    <script type="text/javascript"> 
        $(function(){
            var uid;
            var fid = "<?php echo $fid; ?>";
            VK.init(function() { 
             // API initialization succeeded 
             // Your code here 
                uids = fid ? [fid, "<?php echo $uid; ?>"] : window.viewer;
                VK.api("users.get", {user_ids: uids, fields:"bdate, sex, photo_medium"}, function(data) { 
                    if(data.response.length>1) muid = data.response[1].id;
                    referrer = "<?php echo $_GET['user_id']; ?>";
                    uid = data.response[0].id;
                    $(".mylink").attr("href","//vk.com/app4455160_"+uid)
                                .text("http://vk.com/app4455160_"+uid);
                    bdate = data.response[0].bdate;
                    bdate = bdate ? bdate : "<?php echo $birthday; ?>";
                    
                    if(bdate == undefined){
                        $.fancybox.open({content:"<h3>Внимание!</h3><p class='aligncenter'>День рождения не определен.<br>Показан прогноз на случайную дату.<br>Пожалуйста, измените день рождения в <a href='//vk.com/edit' target='_blank'>настройках.</p>",topRatio:0,margin:[100, 20, 20, 20],autoHeight:true,height:'auto'});
                    }else if("<?php echo $birthday; ?>"=="0000-00-00"){
                        if(bdate){
                            document.cookie="bdate="+bdate+"; path=/; expires=";
                        }else{
                            $.fancybox.open({content:"<h3>Внимание!</h3><p class='aligncenter'>День рождения пользователя не определен.<br>Гороскоп будет не точным.<br>Пожалуйста, не тратьте свои звезды напрасно.</p>",topRatio:0,margin:[100, 20, 20, 20],autoHeight:true,height:'auto'});
                        }
                    }
                    $(".share").html(VK.Share.button({
                        url: "//vk.com/app4455160_"+uid,
                        title: "Астрологический календарь",
                        description: "Точные интерпретации положения планет по темам деньги, здоровье, любовь, авто, бизнес, отдых на каждый день по дате рождения.\nЭто круче, чем гороскопы по знакам Зодиака!\nЕсть возможность посмотреть календари друзей! ;)",
                        image: "//best-horoscope.ru/images/nostradamus.png",
                        noparse: true
                    },{
                        type: "round_nocount",
                        text: "Поделиться с друзьями"
                    }));
                    uname = data.response[0].first_name;
                    uphoto = data.response[0].photo_medium;
                    gsk = "<?php echo $gsk; ?>";
                    if(!gsk){
                        $("#goro").load("body.php",{fid: fid, uid: uid, bdate: bdate, uname: uname, referrer: referrer}, function(){
                            if(!fid){
                                VK.api("friends.getAppUsers", {uid: uid}, function(data) { 
                                    // Действия с полученными данными 
                                    var friends = data.response;
                                    if(friends.length){
                                        VK.api("users.get", {user_ids: friends, fields: "photo_medium"}, function(data) { 
                                            af = data.response.length;
                                            if(af){
                                                html = "";
                                                var mfr = 5-af<1 ? 1 : 5-af;
                                                for(var i=0; i < mfr; i++){
                                                    html += "<li style='float: left;'><a href='javascript:void(0);' onclick='VK.callMethod(\"showInviteBox\");'><img class='shadowing' src='addfr.png' width='50' height='50'></a></li>";
                                                }
                                                for(var i=0; i < af; i++){
                                                    mid = data.response[i].id;
                                                    html += "<li style='float: left;'><a href='?fid="+mid+"'><img class='shadowing' src='"+data.response[i].photo_medium+"' width='50' height='50'></a></li>";
                                                }
                                                $("#friends ul").html(html);
                                                makeSlider();
                                            }
                                        });
                                    }
                                });
                            }
                            VK.callMethod('resizeWindow', 600, $('body .container').height()+30);
                        });
                    }else{
                        $(".uname").text(uname);
                        //$(".bdate").text(bdate);
                        VK.callMethod('resizeWindow', 600, $('body .container').height()+40);
                    }
                    VK.Widgets.Like("vk_like", {type: "button"});
                    $(".uphoto").attr("src", uphoto);
                });
            }, function() { 
             // API initialization failed 
             // Can reload page here 
                alert("API FAIL");
            }, '5.23');
            
            $(".rating").click(function(){
                var p = $(this);
                var t = $("b", p).text();
                var c = $(p).text().replace(t, "");
                var vk = "Вот как для меня выстроились планеты на "+$("span.cntdate").text()+":\n"+t+"\n"+c;
                VK.api(
                    'wall.post', 
                    {
                        message: vk,
                        attachments: 'http://vk.com/app4455160_'+uid
                    }, 
                    function(r) {console.log(r)});
            });

/*                    
            VK.addCallback('onLocationChanged', function(){
                console.log("!");
            });
            
            VK.callMethod('setLocation','<?php echo $uid ? $_SERVER["QUERY_STRING"] : ""; ?>');
*/
        });

        function order(v) {
            $('.callbacks:visible').text('');
            var params = {
                type:   'item',
                item:   v*10 + 'stars'
            };
            VK.callMethod('showOrderBox', params);
            
            var callbacksResults = $('.callbacks:visible');
            
            VK.addCallback('onOrderSuccess', function(order_id) {
                callbacksResults.html('<b>Заказ подтвержден</b>. Обновление через 5 секунд, или перейдите по <a href="">ссылке</a>.');
                setTimeout('location.reload()', 3000);
            });
            VK.addCallback('onOrderFail', function() {
                callbacksResults.html('Ошибка обработки заказа.');
            });
            VK.addCallback('onOrderCancel', function() {
                callbacksResults.html('Заказ отменен.');
            });
        }

        function opengraph(){
            $.fancybox.open(
                $("#graph"),
                {
                    width:      "600",
                    height:     "300",
                    topRatio:   0,
                    margin:     [100, 0, 0, 0],
                    autoSize:   true,
                    scrolling:  "no",
                    title:      $("#graph input").val()
                });
        }
        function selEl(selection) {
            var e=selection; 
            if(window.getSelection){ 
            var s=window.getSelection(); 
            if(s.setBaseAndExtent){ 
                s.setBaseAndExtent(e,0,e,e.innerText.length-1); 
            }else{ 
                var r=document.createRange(); 
                r.selectNodeContents(e); 
                s.removeAllRanges(); 
                s.addRange(r);} 
            }else if(document.getSelection){ 
                var s=document.getSelection(); 
                var r=document.createRange(); 
                r.selectNodeContents(e); 
                s.removeAllRanges(); 
                s.addRange(r); 
            }else if(document.selection){ 
                var r=document.body.createTextRange(); 
                r.moveToElementText(e); 
                r.select();
            }
        }
    </script>
    <style>
        h1{
            font-size: 24px;
            padding-top: 10px;
        }
        .block{
            margin-bottom: 0;
        }
        .block .mygoro img{
            width: 50px;
            height: 15px;
        }
        .info p, .info b, .info span{
            font-size: 14px;
        }
        .container{
            margin-top: 0;
            min-height: 330px;
        }
        .fancybox{
            position: absolute;
            margin-top: -200px;
            margin-left: -100px;
        }
        .ourgoro{
            margin: 20px 20px 10px 27px;
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
            top: 10px;
        }
        #mystar{
            position: absolute;
            margin-left: 500px;
            top: 10px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1 class="attention textshadow" style="color: white;"><?php echo $title; ?></h1>
        <div class="block">
            <?php if($gsk): ?>
                <div id="goback"><a href="javascript:history.back();"><img src="/images/arrow-left.png" width="40"/></a></div>
            <?php elseif($fid): ?>
                <div id="goback"><a href="/vkapps/"><img src="/images/gohome.png" width="40"/></a></div>
            <?php endif; ?>
        
            <?php if(!$gsk): ?>
                <div style="float: right; margin-top: 9px;">
                    <div style="margin-top: 5px;">
                        <a href='javascript:void(0);' onclick='VK.callMethod("showInviteBox");'>Пригласить друзей</a>
                    </div>
                    <!--div style="margin-top: 5px;">
                        <a id="showcal" href='javascript:void(0);' onclick='$.fancybox.open("Данный функционал в разработке. Ожидайте в скором времени. Спасибо.");'>Посмотреть календарь друга</a>
                    </div-->
                    <div style="margin-top: 5px;">
                        <a href='/apps/'>Аспекты планет</a>
                    </div>
                    <div style="margin-top: 5px;">
                        <a href='javascript:void(0);' onclick='VK.callMethod("showSettingsBox", 256);'>Добавить в меню</a>
                    </div>
                    <div style="margin-top: 5px;">
                        <a href='help.html'>Помощь</a>
                    </div>
                </div>
            <?php else: ?>
                <div style="margin-left: 295px; margin-top: -5px; width:180px; height: 70px; position: absolute;"><a id="minigraph" href="javascript:void(0);" onclick="opengraph();"></a></div>
            <?php endif; ?>

                <div style="position: absolute; width: 49px; height: 50px; background: url(star.png) no-repeat; background-size: 50px; margin-top: 100px; margin-left: 15px;">
                    <b style="cursor: pointer; display: block; padding: 18px 0; text-align: center;" class="stars" onclick="$.fancybox.open({content:$('#payment').html(),topRatio:0,margin:[100, 20, 20, 20],autoHeight:true,height:'auto'});" title="<?php echo $stars; ?>" ><?php echo $stars_; ?></b>
                </div>

            <div style="float: left; margin-top: 10px; width: 280px;">
                <img src="" align="left" class="uphoto shadowing" width="70" height="70" style="margin-right: 10px;margin-left: 0;margin-top: 0;" />
                <div id="vk_like" style="position: absolute!important; margin-left: 92px; margin-top: 5px;"></div>
                <div style="margin-top: 35px;" class="share"></div>
                <p style="margin:8px;"><b>Имя:</b> <b class="uname"><?php echo $uname; ?></b></p>
            </div>
            <div style="margin: 0 auto; clear: both; margin-top: 10px; overflow: hidden;"><h3>Интерпретации аспектов на <b><span class="attention cntdate" style="font-size: 14px;"><?php echo $cntdate; ?></span></b></h3></div>
            <div id="goro" style="clear: both; overflow: hidden;">
                <?php if($gsk): ?>
                    <div class="mygoro shadowing" style="overflow: hidden; clear: both;margin-top: 10px; float:left; border-radius: 10px; padding: 10px; background: white;">
                        <?php require_once('makegraph.php'); ?>
                    </div>
                <?php else: ?>
                    <h3 class="aligncenter" style="padding-top: 20px;">Предназначено для использования в социальных сетях.</h3>
                <?php endif ?>
            </div>
            <?php if(!$gsk && !$fid): ?>
                <p></p>
                <h3>Мои друзья</h3>
                <div id="friends" style="overflow: hidden;">
                    <div class="fr_res">
                        <ul>
                        </ul>
                    </div>
    				<div class="shadow"></div>
    				<div class="shadow right"></div>
    				<div class="arrow r_arr"></div>
    				<div class="arrow left l_arr"></div>
    				<div class="pp">старт/пауза</div>
                </div>
            <?php endif; ?>
        </div>
        <p style="font-size: 12px; color: #999; text-align: center; padding-bottom: 0;margin-bottom: 0;">2014 Андрей Перье и 
        <a href="http://best-horoscope.ru" target="_blank" style="font-size: 12px; text-decoration: none; color: #777;">Интернет-группа "Астроном и я"</a><br>
        Интерпретации разработаны с помощью <a href="http://astrohit.ru" style="font-size: 12px; text-decoration: none; color: #777;">команды профессиональных астрологов</a>
        </p>
        <div id="payment" style="display: none;">
            <style>
                ul li{ padding: 2px 5px; font-size: 16px;}
            </style>
            <h3>Для чего нужны звезды?</h3>
            <p>
                Для просмотра интерпретаций аспектов необходимы <b>Звезды</b>. Каждый день, когда вы заходите в приложение <b>Астрологический календарь</b> вам начисляются три звезды.<br>
                Таким образом, у вас будет возможность просматривать интерпретации минимум трех аспектов ежедневно. Если по какой-либо причине вам будет не хватать ежедневных звезд, то вы всегда сможете получить их в обмен на голоса прямо здесь:
            </p>
            <center>
                <ul style='list-style: none;'>
                    <li><a href='javascript:void(0);' onclick='order(1);'>Приобрести 10 звезд за 1 голос</a></li>
                    <li><a href='javascript:void(0);' onclick='order(5);'>Приобрести 50 звезд за 4 голоса</a></li>
                    <li><a href='javascript:void(0);' onclick='order(10);'>Приобрести 100 звезд за 7 голосов</a></li>
                </ul>
            </center>
            <p>И, конечно, же в качестве приятного бонуса, вы получите 10 звезд за каждого вашего друга, получившего от вас приглашение и воспользовавшись приложением <b>Астрологический календарь</b>. Для приглашения друзей воспользуйтесь ссылкой <a href="javascript:void(0);" onclick="VK.callMethod(&quot;showInviteBox&quot;);">Пригласить друзей</a> на главной странице приложения.</p>
            <p class="callbacks">И пусть звезды благоволят вам.</p>
        </div>
    </div>
</body>
</html>