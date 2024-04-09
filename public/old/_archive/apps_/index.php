<?php
    
    require_once('../admin/myconf.php');

    $block = 1; //заглушка для VK, направляющая на расчет углов планет
    
    $uid = isset($_GET['uid']) ? $_GET['uid'] : '';
    $gsk = isset($_GET['gsk']) ? $_GET['gsk'] : '';
    $udt = isset($_GET['udt']) ? $_GET['udt'] : '';
    $ubd = isset($_GET['ubd']) ? $_GET['ubd'] : '';

    $star = isset($_GET['star']) ? $_GET['star'] : '';
    
    $check = "SELECT * FROM $gorogroup";
    $allgoro = mysql_query($check);
    while ($row = mysql_fetch_array($allgoro)) {
        $goroname[$row[0]] = $row[2];
        $gorotitle[$row[0]] = $row[1];
    }

    $goroday = date("Ymd");
    $birthday = date("d.m.Y", strtotime($ubd));
    

    $title = "Астрономический календарь";
    if($gsk){
        if($uid){
            $query = "SELECT * FROM _vkapps WHERE vkuser = $uid;";
            $result = mysql_query($query);
            $exists = mysql_num_rows($result);
            if($exists){
                $stars = mysql_result($result, 0, 'vkstars');
                $viewed = 1;
            }
            $stars_ = $stars>999 ? '999+' : $stars;
        }else{
            die ("Извините, непредвиденная ошибка. Пожалуйста, обратитесь к разработчикам приложения.");
        }

        
        $guest = $_COOKIE["vkuid"] == $uid ? 0 : 1;

        $months = Array("январь","февраль","март","апрель","май","июнь","июль","август","сентябрь","октябрь","ноябрь","декабрь");
        $bymonth = Array("января","февраля","марта","апреля","мая","июня","июля","августа","сентября","октября","ноября","декабря");

        $day = 0;
        $myday = date("d",strtotime("$udt"));
        $mymonth = date("m",strtotime("$udt"));
        $myyear = date("Y",strtotime("$udt"));
        $cntdate = "$myday.$mymonth.$myyear";

        for($i=1; $i<=7; $i++){
            $sk = md5($i.$uid.$udt);
            if($gsk == $sk){
                $id_gorogroup = $i;
            }
        }
        //получаем сегодняшний гороскоп
        //$title = $gorotitle[$id_gorogroup];
        $pls = Array('Солнце', 'Луна', 'Меркурий', 'Венера', 'Марс', 'Юпитер', 'Сатурн', 'Уран', 'Нептун', 'Плутон');
        $title = "$pls[$star]";
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
    <script src="//vk.com/js/api/xd_connection.js?2"  type="text/javascript"></script>
    <script type="text/javascript" src="//vk.com/js/api/share.js?90" charset="windows-1251"></script>

    <script type="text/javascript"> 
        $(function(){
            var uid;
            VK.init(function() { 
             // API initialization succeeded 
             // Your code here 
                VK.api("getProfiles", {uids:window.viewer_id, fields:"bdate, sex, photo_medium"}, function(data) { 
                    referrer = "<?php echo $_GET['user_id']; ?>";
                    uid = data.response[0].id;
                    $(".mylink").attr("href","//vk.com/app4493120_"+uid)
                                .text("http://vk.com/app4493120_"+uid);
                    bdate = data.response[0].bdate;
                    console.log(bdate);
                    if(bdate == undefined){
                        $.fancybox.open({content:"<h3>Внимание!</h3><p class='aligncenter'>День рождения не определен.<br>Показано положение планет на случайную дату.<br>Пожалуйста, измените день рождения в <a href='//vk.com/edit' target='_blank'>настройках.</p>",topRatio:0,margin:[100, 20, 20, 20],autoHeight:true,height:'auto'});
                    }
                    
                    $(".share").html(VK.Share.button({
                        url: "//vk.com/app4493120_"+uid,
                        title: "Астрономический календарь",
                        description: "Уникальное приложение, раcсчитывающее положение планет относительно даты рождения на текущий день. Незаменимая программа для помощи начинающему астроному, астрологу и просто любителю ночного неба.",
                        image: "//best-horoscope.ru/apps_/astronom.png",
                        noparse: true
                    },{
                        type: "round_nocount",
                        text: "Поделиться с друзьями"
                    }));
                    uname = data.response[0].first_name;
                    uphoto = data.response[0].photo_medium;
                    gsk = "<?php echo $gsk; ?>";
                    if(!gsk){
                        $("#goro").load("<?php echo $block ? 'stars' : 'body'; ?>.php",{uid: uid, bdate: bdate, uname: uname, referrer: referrer}, function(){
                            VK.callMethod('resizeWindow', 600, $('body .container').height()+40);
                        });
                    }else{
                        $(".uname").text(uname);
                        //$(".bdate").text(bdate);
                        VK.callMethod('resizeWindow', 600, $('body .container').height()+70);
                    }
                    VK.Widgets.Like("vk_like", {type: "button"});
                    $(".uphoto").attr("src", uphoto);
                });
                //window.userSex1=data.response[0].sex;
                //window.userId1=data.response[0].uid;
            }, function() { 
             // API initialization failed 
             // Can reload page here 
                alert("API FAIL");
            }, '5.23');
            
            $(".rating_").click(function(){
                var p = $(this);
                var t = $("b", p).text();
                var c = $(p).text().replace(t, "");
                var vk = "Вот как выстроились планеты на "+$("span.cntdate").text()+":\n"+t+"\n"+c;
                VK.api(
                    'wall.post', 
                    {
                        message: vk,
                        attachments: '//vk.com/app4493120_'+uid
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
        .block .mygoro img.imgstar{
            width: 50px;
            height: 50px;
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
        <h1 class="attention textshadow"><?php echo $title; ?></h1>
        <div class="block">
            <?php if(!$guest && $gsk): ?>
                <div id="goback"><a href="/apps_/"><img src="/images/arrow-left.png" width="40"/></a></div>
            <?php endif; ?>
        
            <?php if(!$gsk || 1): ?>
                <div style="float: right; margin-top: 9px;">
                    <div style="margin-top: 5px;">
                        <a href='javascript:void(0);' onclick='VK.callMethod("showInviteBox");'>Пригласить друзей</a>
                    </div>
                    <div style="margin-top: 5px;">
                        <a href='javascript:void(0);' onclick='$.fancybox.open("Данный функционал в разработке. Ожидайте в скором времени. Спасибо.");'>Посмотреть календарь друга</a>
                    </div>
                    <div style="margin-top: 5px;">
                        <a href='/vkapps_/'>Интерпретации аспектов</a>
                    </div>
                    <div style="margin-top: 5px;">
                        <a href='javascript:void(0);' onclick='VK.callMethod("showSettingsBox", 256);'>Добавить в меню</a><span>&nbsp;&nbsp;&nbsp;</span><a href='help.html'>Помощь</a>
                    </div>
                </div>
            <?php else: ?>
                <div style="margin-left: 295px; margin-top: -5px; width:180px; height: 70px; position: absolute;"><a id="minigraph" href="javascript:void(0);" onclick="opengraph();"></a></div>
            <?php endif; ?>

            <?php if(!$guest): ?>
                <div style="position: absolute; width: 49px; height: 50px; background: url(star.png) no-repeat; background-size: 50px; margin-top: 100px; margin-left: 15px;">
                    <b style="cursor: pointer; display: block; padding: 18px 0; text-align: center;" class="stars" onclick="$.fancybox.open({content:$('#payment').html(),topRatio:0,margin:[100, 20, 20, 20],autoHeight:true,height:'auto'});" title="<?php echo $stars; ?>" ><?php echo $stars_; ?></b>
                </div>
            <?php endif; ?>

            <div style="float: left; margin-top: 10px; width: 280px;">
                <img src="" align="left" class="uphoto shadowing" width="70" height="70" style="margin-right: 10px;margin-left: 0;margin-top: 0;" />
                <div id="vk_like" style="position: absolute!important; margin-left: 92px; margin-top: 5px;"></div>
                <div style="margin-top: 35px;" class="share"></div>
                <p style="margin:8px;"><b>Имя:</b> <b class="uname"><?php echo $uname; ?></b></p>
            </div>

            <div style="text-align: center; clear: both;overflow: hidden;"><h3 style="margin: 17px 0 0px 0;">Расчет положения планет на <b><span class="attention cntdate" style="font-size: 14px;"><?php echo $cntdate; ?></span></b></h3></div>
            <div id="goro" style="clear: both;">
                <?php if($gsk): ?>
                    <div class="mygoro" style="overflow: hidden; clear: both;margin-top: 10px; float:left;">
                        <?php require_once('makegraph.php'); ?>
                    </div>
                <?php else: ?>
                    <h3 class="aligncenter" style="padding-top: 20px;">Предназначено для использования в социальных сетях.</h3>
                <?php endif ?>
            </div>
        </div>
        <div id="payment" style="display: none;">
            <style>
                ul li{ padding: 2px 5px; font-size: 16px;}
            </style>
            <h3>Для чего нужны звезды?</h3>
            <p>
                Для просмотра <a href="/vkapps_/">интерпретаций аспектов</a> необходимы <b>Звезды</b>. Каждый день, когда вы заходите в приложение <b>Астрологический календарь</b> вам начисляются три звезды.<br>
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