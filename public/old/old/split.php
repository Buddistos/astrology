<?php
    require_once('admin/myconf.php');

    if(rand(1,10) == 5){
        $orders++;
        $query = "UPDATE $option SET option_value = $orders WHERE option_key = 'orders';";
        $result = mysql_query($query);
    }
    if(rand(1,10) == 5){
        $maked++;
        $query = "UPDATE $option SET option_value = $maked WHERE option_key = 'maked';";
        $result = mysql_query($query);
    }

    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        $ip = $_SERVER['HTTP_CLIENT_IP'];
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    } else {
        $ip = $_SERVER['REMOTE_ADDR'];
    }
    include("geo/SxGeo.php");
    $SxGeo = new SxGeo('geo/SxGeoCity.dat', SXGEO_BATCH | SXGEO_MEMORY);
    $city = $SxGeo->getCityFull($ip);
    unset($SxGeo);
    $country = $city['country'];
    $region = $city['region_name'];
    $town = $city['city'];
    $geocity = $country ? $country : "";
    $geocity= $geocity ? $geocity.($region ? ", ".$region : "") : ($region ? $region : "");
    $geocity = $geocity ? $geocity.($town ? ", ".$town : "") : ($town ? $town : "");
    $geocity = $geocity ? "$geocity" : "";
?>
<html>
<head>
    <meta charset="utf-8">
    <title>Персональный астропрогноз по дате рождения | Best-horoscope.ru</title>
    <meta name="description" content="Составление индивидуальных астропрогнозов по шести гороскопам: любовный, автогороскоп, для здоровья, для отпуска, финансовый и бизнес-гороскоп.">
    <link href="/favicon.ico" rel="shortcut icon" type="image/x-icon">
    <link href='http://fonts.googleapis.com/css?family=Seymour+Display|Marmelad&subset=cyrillic' rel='stylesheet' type='text/css'>

    <script type="text/javascript">
        var geocity = "<?php echo $geocity; ?>";
        var geocoords = "<?php echo $lat.','.$lng; ?>";
    </script>
    <script src="//ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
    <script type="text/javascript" src="js/jquery-migrate-1.2.1.min.js"></script>

    <link href="//ajax.googleapis.com/ajax/libs/jqueryui/1.10.4/themes/smoothness/jquery-ui.css" rel="stylesheet" />
    <script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.10.4/jquery-ui.min.js"></script>
    
    <script src="js/jquery.bpopup.min.js" type="text/javascript"></script>
    <script src="js/jquery.mousewheel-3.0.6.pack.js" type="text/javascript"></script>

    <link rel="stylesheet" href="js/fancybox/jquery.fancybox.css?v=2.1.5" type="text/css" media="screen" />
    <script src="js/fancybox/jquery.fancybox.pack.js?v=2.1.5" type="text/javascript"></script>
    <script src="js/jcarousellite_1.0.1.min.js" type="text/javascript"></script>
    
    <link rel="stylesheet" href="js/jqueryformstyler/jquery.formstyler.css" type="text/css" media="screen" />
    <script src="js/jqueryformstyler/jquery.formstyler.min.js" type="text/javascript"></script>

    <link href="css/mystyle.css" rel="stylesheet" type="text/css" media="screen" />
    <script src="js/myscript.js" type="text/javascript"></script>
</head>
<body>
    <a name="gotop"></a>
    <div class="container">
        <div id="menu" class="block">
            <a href="#gotop">В начало</a>
            <a href="#goro">Гороскопы</a>
            <a href="#problem">Проблемы</a>
            <a href="#about">О нас</a>
            <a href="#example">Примеры</a>
            <a href="#reviews">Отзывы</a>
        </div>
        <!--div class="header block">
            <h1>Индивидуальные гороскопы для Вас<br>
            на <b class="attention" style="font-size: 36px;">каждый день бесплатно</b></h1>
        </div>
        
        <h2 class="attention textshadow" style="margin: 60px 0 40px;color: white;">
            До конца акции осталось:<br>
        </h2>
        <center>
            <script type="text/javascript" src="http://timegenerator.ru/s/1ed7ac9d6ac6dc54c24edc16cade02ae.js"></script>
        </center-->

        <div class="block">
            <h2>Хотите, на протяжении ближайших ДЕСЯТИ ЛЕТ получать астропрогнозы по всем основным сферам Вашей жизни?</h2>
            <img src="images/magicbook.gif" align="left" width="350" style="margin-top: 40px;">
            <div style="padding-left: 365px;">
                <p>
                    Знать, что Вас ожидает на любовном фронте.
                    Быть готовым к проблемам на дорогах.
                    Определить наиболее удачные периоды для вложения денег или наоборот их трат.
                </p><p>
                    Запланировать отпуск именно на благоприятные дни и провести его так, чтобы этот период запомнился Вам на всю жизнь. Быть готовым к тяжелым временам для вашего бизнеса, а если его ещё нет, то узнать, когда лучше начать своё дело.
                </p><p>
                    Узнать, когда лучше воздержаться от опасных видов спорта, когда стоит начать плановую операцию, почему сегодня не следует идти купаться на озеро. И многое другое.
                </p>
            </div>
            <h3 class="attention">Сегодня у Вас есть такая возможность, а главное,<br> Вы можете получить всё это абсолютно БЕСПЛАТНО!<br>
            Мы просто дарим Вам все эти гороскопы!</h3>
            <h3>Без каких либо SMS и оплат, просто скажите, куда нам отправить Ваши гороскопы! И смотрите на сколько изменится Ваша жизнь!
            </h3>
            <p class="aligncenter" style="padding: 40px 0;">
                <a href="javascript:void(0);" class="button show_modal" onclick="yaCounter25119665.reachGoal('button_1'); return true;"><b>Я ХОЧУ ПОЛУЧИТЬ ГОРОСКОПЫ НА ВСЕ СЛУЧАИ ЖИЗНИ БЕСПЛАТНО</b></a>
            </p>
            <p class="aligncenter">
                Уже оставили заявку на гороскопы <b><?php echo $orders; ?></b> раз!
            </p>
        </div>
        
        <a name="goro"></a>
        <div class="block">
            <h2>Наши гороскопы</h2>
            <div class="ourgoro" id="_love">
                <a href="javascript:void(0);" class="more">
                    <h2>Влюбленным</h2>
                    <b>подробнее</b>
                </a>
            </div>
            <div class="ourgoro" id="_auto">
                <a href="javascript:void(0);" class="more">
                    <h2>Водителям</h2>
                    <b>подробнее</b>
                </a>
            </div>
            <div class="ourgoro" id="_health">
                <a href="javascript:void(0);" class="more">
                    <h2>Для здоровья</h2>
                    <b>подробнее</b>
                </a>
            </div>
            <div class="ourgoro" id="_holyday">
                <a href="javascript:void(0);" class="more">
                    <h2>Отпуск</h2>
                    <b>подробнее</b>
                </a>
            </div>
            <div class="ourgoro" id="_finance">
                <a href="javascript:void(0);" class="more">
                    <h2>Финансы</h2>
                    <b>подробнее</b>
                </a>
            </div>
            <div class="ourgoro" id="_business">
                <a href="javascript:void(0);" class="more">
                    <h2>Бизнес</h2>
                    <b>подробнее</b>
                </a>
            </div>
        </div>

        <div class="block" style="padding-bottom: 50px;">
            <h2 style="color: darkred;">ПОЛУЧАТЬ ЕЖЕДНЕВНЫЕ ГОРОСКОПЫ ПО ОСНОВНЫМ СФЕРАМ ЖИЗНИ<br> В ТЕЧЕНИИ ДЕСЯТИ ЛЕТ БЕСПЛАТНО!</h2>
            <p class="aligncenter">
                <br>
                <a href="javascript:void(0);" class="button show_modal" onclick="yaCounter25119665.reachGoal('button_2'); return true;"><b>Я ХОЧУ ПОЛУЧИТЬ ГОРОСКОПЫ</b></a>
            </p>
        </div>

        <a name="problem"></a>
        <div class="block problem">
            <h2>Основные пробемы при обращении к астрологу</h2>
            <div class="alignleft">
                <img src="images/pr_car.png" class="shadowing">
                <p>
                    <b>Компетенция</b><br>
                    Часто бывает, что человек прочитав пару книг по астрологии, считает себя профессиональным астрологом. Доверившись прогнозу такого "специалиста", можно подвергнуть себя опасности, навредить самому себе, например, получить травму или попасть в аварию в то время, когда лучше было бы воздержаться от поездки.
                </p>
            </div>
            <div class="alignright">
                <img src="images/pr_planets.png" class="shadowing">
                <p>
                    <b>Качество услуг</b><br>
                    Многие астрологи при составлении гороскопов учитывают только дату рождения, не уделяя внимания времени и месту рождения. Между тем, судьбы людей, родившихся в один день, но в разное время и разных городах, могут сильно различаться, так как положение планет над ними значительно отличаются.
                </p>
            </div>
            <div class="alignleft">
                <img src="images/timer.gif" class="shadowing">
                <p>
                    <b>Сроки составления</b><br>
                    Для того, чтобы составить личный гороскоп необходимо разобрать и проанализировать натальную карту человека, а это довольно длительный процесс. Только после этого можно переходить к составлению ежедневного гороскопа. Если астролог работает один и без помощников, то сроки выполнения могут сильно затянуться, если поступает более трех заказов в день.
                </p>
            </div>
            <div class="alignright">
                <img src="images/pr_mask.png" class="shadowing">
                <p>
                    <b>Мошенники</b><br>
                   Часто встречаются ситуации, когда некоторые личности создавая образ квалифицированных экспертов, располагают к себе людей. Люди "покупаются" на дешевые предложения создания индивидуальных гороскопов, а потом, после внесения денег, не могут найти этих новоявленных "астрологов".<br>
                   Будьте аккуратнее! Никогда не платите деньги неизвестно кому. Как вариант, попросите рассчитать пробный или частичный гороскоп перед тем, как заказывать.
                </p>
            </div>
        </div>

        <a name="about"></a>
        <div class="block" style="padding-bottom: 50px;">
            <h2>Уже составлено <b class="attention" style="font-size: 30px;"><?php echo $maked; ?></b> гороскопов!</h2>
            <img src="images/zod1.png" align="left" height="250" style="margin-top: -60px;">
            <img src="images/zod2.png" align="right" height="250" style="margin-top: -60px;">
            <div class="innertext">
                <p><b>Воспользуйтесь своим шансом!</b> Мы составляем гороскопы основываясь исключительно на индивидуальных данных!</p>
                <p>Доверьтесь астрологам с 15-летним опытом составления гороскопов и астропрогнозов!</p>
                <center><br><a href="javascript:void(0);" class="button show_modal" onclick="yaCounter25119665.reachGoal('button_3'); return true;"><b>СОСТАВИТЬ ЛИЧНЫЙ ГОРОСКОП БЕСПЛАТНО</b></a></center>
            </div>
        </div>

        <div class="block choice">
            <h2>Почему люди нас выбирают</h2>
            <div class="problem aligncenter">
                <img src="images/nostradamus.png" class="shadowing">
                <p class="alignleft">
                    <b>Опыт</b><br>
                    Более чем 15-летний опыт работы наших астрологов и отзывы клиентов говорят сами за себя.
                </p>
            </div>
            <div class="problem aligncenter">
                <img src="images/guarantee.jpg" class="shadowing">
                <p class="alignleft">
                    <b>100% гарантия качества</b><br>
                    При составлении индивидуальных гороскопов мы учитываем максимально возможное количество параметров. Поэтому наши клиенты уверены в точности полученных предсказаний, которые впоследствии легко сравнить с прошедшими событиями вашей жизни.
                </p>
            </div>
             <div class="problem aligncenter">
                <img src="images/repka.jpg" class="shadowing">
                <p class="alignleft">
                    <b>Уверенность в результате</b><br>
                    Мы уверены в наших знаниях и поэтому представляем возможность всем желающим получить качественные гороскопы на каждый день абсолютно бесплатно.
                </p>
            </div>
            <div class="problem aligncenter">
                <img src="images/goodprice.jpg" class="shadowing">
                <p class="alignleft">
                    <b>Адекватные цены</b><br>
                    За счет больших объемов заказов и регулярном обращении большого числа постоянных клиентов мы можем держать наши цены на доступном для всех желающих уровне.
                </p>
            </div>
            <div class="problem aligncenter" style="width: 880px;">
                <p class="aligncenter" style="text-align: justify;">
                    <b>Командная работа</b><br>
                    К составлению гороскопов и прогнозов для вас приложили руку профессиональные астрологи с огромным стажем работы. Благодаря слаженной команде мы можем обрабатывать большое количество запросов и составлять прогнозы и гороскопы в кратчайшие сроки. Практически все компании, которые заказывают у нас гороскопы для своих сотрудников, довольны качеством и оперативностью выполнения поставленных задач.
                </p>
                <img src="images/command.jpg" class="shadowing" style="width: 580px;">
            </div>
        </div>

        <div class="block" style="padding-bottom: 50px;">
            <h2 style="color: darkred;">ПОЛУЧИТЬ БЕСПЛАТНО ЛИЧНЫЕ ГОРОСКОПЫ<br>
            ОТ КОМАНДЫ <b style="color: orangered; font-size: 32px;">ПРОФЕССИОНАЛЬНЫХ АСТРОЛОГОВ</b></h2>
            <p class="aligncenter">
                <br>
                <a href="javascript:void(0);" class="button show_modal" onclick="yaCounter25119665.reachGoal('button_4'); return true;"><b>Я ХОЧУ ПОЛУЧИТЬ ГОРОСКОПЫ НА ВСЕ СЛУЧАИ ЖИЗНИ БЕСПЛАТНО</b></a>
            </p>
        </div>

        <a name="example"></a>
        <h2 class="attention textshadow" style="margin: 60px 0 40px;color: white;">
            Примеры наших гороскопов
        </h2>

        <div class="feature">
            <button class="prev"></button>
            <button class="next"></button>
                    
            <div class="carusel">
                <ul>
                    <li><a href="images/feature/1.jpg" class="fancybox" rel="group"><img src="images/feature/1pr.jpg"></a></li>
                    <li><a href="images/feature/4.jpg" class="fancybox" rel="group"><img src="images/feature/4pr.jpg"></a></li>
                    <li><a href="images/feature/3.jpg" class="fancybox" rel="group"><img src="images/feature/3pr.jpg"></a></li>
                    <li><a href="images/feature/2.jpg" class="fancybox" rel="group"><img src="images/feature/2pr.jpg"></a></li>
                </ul>
            </div>    
        </div>

        <a name="reviews"></a>
        <div class="block reviews">
            <h2>Истории клиентов</h2>
            <div class="alignright">
                <img src="images/luda.jpg" class="person shadowing"> 
                <p style="padding-left: 220px;">
                    <b>Людмила. Москва. Любовный гороскоп.</b><br>
                    <i>Я не ожидала такого, но была безумно счастлива! Я со своим парнем встречаюсь уже около года. И вот мы решили отпраздновать годовщину нашей встречи в ресторане. Мы заказали еду, играла живая музыка. Мы спокойно посидели выпили вина и перешли к десерту. Тут неожиданно мой парень начал мяться, невнятно что-то бормотать.<br>
                    Я спросила, что случилось, и тут он достает обручальное кольцо и просит выйти за него замуж. Я сначала потеряла дар речи, но как только эффект неожиданности прошел я с радостью согласилась.Это был лучший день в моей жизни! Спасибо Вам огромное, теперь буду ещё больше прислушиваться к Вашим гороскопам!</i>
                </p>
                <img src="images/luda.png" class="mygoro">
                <p>
                    <b>Комментарий астролога</b><br>
                    Людмила обратилась ко мне, как к астрологу уже после этой истории с просьбой подобрать удачную дату для свадьбы. Конечно, я посмотрел этот предыдущий прогноз на месяц и решил, что такой пример стоит обнародовать. Судя по всему, ее любимый не слишком решительный человек, но в марте "звезды благоволили" Людмиле - предложение руки и сердца могло поступить в любой из 4-5 удачных дней.
                </p>
            </div>
            <div class="alignright">
                <img src="images/grig.jpg" class="person shadowing"> 
                <p style="padding-left: 220px;">
                    <b>Григорий. Самара. Отпускной гороскоп.</b><br>
                    <i>Я много работаю и в определенный момент усталость очень сильно накопилась. Мои друзья решили слетать в Египет на отдых и пригласили меня к ним присоединится. Я долго думал лететь или не лететь, так как работы было много, да и начальство не сильно хотело отпускать. Но тут я решил посмотреть свой гороскоп для туриста и как раз на период поездки были очень благоприятные дни. Это стало еще одним аргументам "за" поездку.<br>
                    Правда мне пришло сильно постараться, чтобы руководство меня отпустило, но я смог это сделать. И я не пожалел. отдых был просто супер, отель, пляж, обслуживание были на высоте. Также я открыл для себя дайвинг и стал его фанатом. В итоге я получил заряд энергии еще наверное на целый год.</i>
                </p>
                <img src="images/grig.png" class="mygoro">
                <p>
                    <b>Комментарий астролога</b><br>
                    Можно только позавидовать умению Григория правильно выбирать время и место для отдыха.
                </p>
            </div>
            <div class="alignright">
                <img src="images/anna.jpg" class="person shadowing"> 
                <p style="padding-left: 220px;">
                    <b>Анна. Белгород. Гороскоп здоровья.</b><br>
                    <i>Как я сломала руку. Была суббота, 10 мая, погода была отличная и я решила развеяться, сходить в парк покататься на роликах. Сразу хочу сказать, что на роликах я кататься умею, но чувствую себя не очень уверенно. Я с удовольствием покаталась целый час и поехала сдавать ролики. И буквально за 10 метров до пункта проката я неожиданно упала, при этом выставила руки, чтобы смягчить падение. Упала я неудачно и сломала правую руку.<br>
                    Забегая вперед могу сказать, что в гипсе я проходила долго, и было очень обидно и неудобно жить со сломанной правой рукой.</i>
                </p>
                <img src="images/anna.png" class="mygoro">
                <p>
                    <b>Комментарий астролога</b><br>
                    А что тут комментировать - рисковать, проявлять чрезмерную физическую активность 10-12 мая было просто нельзя. Не пошла бы кататься на роликах - глядишь, дома бы просто сломала маникюр или ушиблась, но обошлось бы без перелома руки. К предупреждениям надо относиться серьезнее...
                </p>
            </div>
            <div class="alignright">
                <img src="images/gen.jpg" class="person shadowing"> 
                <p style="padding-left: 220px;">
                    <b>Геннадий. Волгоградская область. Гороскоп здоровья.</b><br>
                    <i>На 7 марта мне была назначена хирургическая операция. Операция была плановая, все консультации с врачами были позади, платная палата была подготовлена. Но мне было все равно не по себе, так как операцию тяжело назвать приятным событием, тем более наслушавшись от своих знакомых всяких страшилок, о том что может случится или же пойти не так.<br>
                    Поэтому я был, что называется на нервах. В это время мне в интернете попался Ваш сайт, если честно не очень верил, во все эти гороскопы и предсказания. Но всё же заказал гороскоп здоровья, в котором был указан период моей операции. График на гороскопе показывал положительные результаты операции. Это меня довольно сильно расслабило, я почти перестал переживать по поводу операции. В итоге операция прошла "без сучка и задоринки" и уже через несколько дней после нее я был дома в полном здравии!</i>
                </p>
                <img src="images/gen.png" class="mygoro">
                <p>
                    <b>Комментарий астролога</b><br>
                    Хотя дата операции была назначена заранее, но попала на "удачные" дни - в результате Геннадий Львович меньше волновался и прошел испытание вполне успешно. Но, строго говоря, время для таких серьезных событий надо выбирать заранее и осознанно. Если указания гороскопа неблагоприятны, плановую операцию лучше сдвинуть на благоприятный период.
                </p>
            </div>
        </div>

        <h2 class="attention textshadow" style="margin: 60px 0;color: white;">
            Оставили заявку на гороскопы <?php echo $orders; ?> раз<br>
            Успевайте и Вы получить гороскопы бесплатно!
        </h2>
        
        <p class="aligncenter" style="padding: 20px 0 50px 0;">
                <a href="javascript:void(0);" class="button show_modal" onclick="yaCounter25119665.reachGoal('button_5'); return true;"><b>Я ХОЧУ ПОЛУЧИТЬ ГОРОСКОПЫ НА ВСЕ СЛУЧАИ ЖИЗНИ БЕСПЛАТНО</b></a>
        </p>
        
        <div class="block">
            <h2>От авторов</h2>
            <img src="images/zodiac.png" align="left">
            <p>
            Прогнозы рассчитываюся по методу транзитов. При этом на каждый день месяца рассчитываются эфемериды (положение на небе) текущих, реально движущихся планет и определяются их аспекты с натальными планетами, то есть планетами, зафиксированными в Вашем гороскопе рождения. Аспекты могут быть "положительными", благоприятными (угол между планетами 60 или 120 градусов) и "отрицательными", неблагоприятными (угол между планетами 90 или 180 градусов). Эти аспекты символизируют благоприятное или нежелательное влияние на Вас возникающих событий и обстоятельств Вашей жизни. Эти влияния могут быть сильными или слабыми. Их "сила" суммируется - и представляется в виде графика-прогноза Вашей жизни на месяц. Текстовый прогноз на конкретный день - это описание смысла аспектов, приключившихся в небесах над Вами, в этот день.
            </p>
        </div>
        
        <p style="font-size: 12px; color: #999; text-align: left; padding-bottom: 0;margin-bottom: 0;">© COPYRIGHT 2014 Все права защищены<br>
        г. Екатеринбург, Интернет-группа "Астроном и я", "Андрей Перье" (<span style="font-size: 12px; color: #999; text-align: left; padding-bottom: 0;margin-bottom: 0; border-bottom: 1px dotted;" title="ИП Перетыкин Андрей Александрович">ИП</span>), ОГРНИП 310667016100048, +7 932-123-97-51, email: <a href="mailto:best-horoscope@yandex.ru" style="font-size: 12px; text-decoration: none; color: #777;">best-horoscope@yandex.ru</a>
        <br>Система расчетов разработана с помощью <a href="http://astrohit.ru" style="font-size: 12px; text-decoration: none; color: #777;">команды профессиональных астрологов</a></p>
    </div>
    
    <div class="popup_modal" id="_love_modal">
        <div class="modal_head">
            <img src="images/love.gif" alt="" />
        </div>
        <h2>Любовный гороскоп</h2>
        <p>Персональный любовный гороскоп покажет Вам благоприятные и "нехорошие" периоды. А так же описание возможных событий в сфере романтических отношений, свиданий, объяснений, помолвки и брака. Подскажет дни, когда наиболее вероятны новые знакомства, дни для выяснения отношений, дни для удачного флирта, дни сексуальной активности.
        </p><p>
        Но, как и всегда в жизни, бывают неблагоприятные периоды, когда все идет не так. Точнее, пошло бы не так, если бы Вы не знали чего ждать и к чему готовиться. Мы предупредим Вас о предстоящих неприятностях, и в Ваших силах будет их избежать.
        </p><p class="aligncenter" style="padding: 20px 0;">
            <a href="javascript:void(0);" class="button show_modal" onclick="yaCounter25119665.reachGoal('button_love'); return true;">Я хочу получить гороскопы на все случаи жизни бесплатно</a>
        </p>
    </div>
    
    <div class="popup_modal" id="_auto_modal">
        <div class="modal_head">
            <img src="images/auto.gif" alt="" />
        </div>
        <h2>Для автолюбителей</h2>
        <p>Возможно, автомобилистам прогноз нужен больше, чем кому-бы то ни было другому - ведь у них есть средство повышенной опасности - любимый автомобиль!
        </p><p>
        Авто-гороскоп подскажет, когда лучше всего покупать и продавать автомобиль, когда обращаться в автосервис, когда заняться тюнингом, когда обращаться к властям с гарантией успешного решения вопроса, когда лучше всего отправляться в дальнюю поездку и т.д.
        </p><p>
        Но, конечно, важны и предупреждения об опасных днях, о возможной аварии, ненужных расходах, о конфликтах с полицией (ГИБДД), о собственной опасной невнимательности. Будут периоды, когда нельзя покупать/продавать авто, ибо понесете убытки или Вас просто обманут.
        </p><p>
        Как говорят, кто предупрежден, тот вооружен. И если Вы учтете эти предупреждения, не будете проявлять излишнюю активность в опасные дни, то все пройдет, и наступит хороший период, а Вы избежите неприятностей.
        </p><p class="aligncenter" style="padding: 20px 0;">
            <a href="javascript:void(0);" class="button show_modal" onclick="yaCounter25119665.reachGoal('button_auto'); return true;">Я хочу получить гороскопы на все случаи жизни бесплатно</a>
        </p>
    </div>

    
    <div class="popup_modal" id="_health_modal">
        <div class="modal_head">
            <img src="images/doktor.gif" alt="" />
        </div>
        <h2>Гороскоп здоровья</h2>
        <p>Ваш персональный гороскоп здоровья предупредит Вас об опасных днях, когда Вы рискуете заболеть, получить травму, неудачно пройти обследование, стать жертвой ошибки стоматолога и т.д. Так же Вы получите и данные о благоприятных периодах, когда удачны будут плановые операции, когда надо начинать курс назначенных процедур, когда лучше входить в диету, когда обследование даст надежные результаты и т.д.
        </p><p class="aligncenter" style="padding: 20px 0;">
            <a href="javascript:void(0);" class="button show_modal" onclick="yaCounter25119665.reachGoal('button_health'); return true;">Я хочу получить гороскопы на все случаи жизни бесплатно</a>
        </p>
    </div>
    
    
    <div class="popup_modal" id="_holyday_modal">
        <div class="modal_head">
            <img src="images/sun.gif" alt="" />
        </div>
        <h2>Отпускной гороскоп</h2>
        <p>Вы, скорее всего, планируете свой отпуск заранее, но все ли Вы учли, есть ли у Вас прогноз на время отпуска? Умные и информированные люди подбирают время отдыха с учетом личного гороскопа-прогноза. Правильно подобранный период для отдыха поможет Вам получить максимум удовольствия и пользы от отдыха. В то время как поездка в неблагоприятный период может обернуться сущим адом, будут вечные задержки в аэропортах, проблемы с документами или вообще болезнь.
        </p><p class="aligncenter" style="padding: 20px 0;">
            <a href="javascript:void(0);" class="button show_modal" onclick="yaCounter25119665.reachGoal('button_holyday'); return true;">Я хочу получить гороскопы на все случаи жизни бесплатно</a>
        </p>
    </div>
    
    
    <div class="popup_modal" id="_finance_modal">
        <div class="modal_head">
            <img src="images/moneytree.gif" alt="" />
        </div>
        <h2>Финансовый гороскоп</h2>
        <p>В Вашем личном финансовом гороскопе Вы получите подробные указания на благоприятные и неблагоприятные периоды для Ваших денежных дел. Какие сферы деятельности наиболее прибыльны, когда можно и нужно работать с партнерами, когда удачны будут зарубежные инвестиции/контакты, когда просить (и получать) кредиты на выгодных условиях, когда Вы можете получить просто неожиданный подарок, когда лучше составлять планы и т.д.
        </p><p>
        Естественно, мы предупредим Вас и об опасных в финансовом смысле периодах ошибок, обманов, неудач и невезений. Когда партнеры могут подвести, договоры составляются с ошибками, когда Вы сами склонны к ошибкам и расточительности, когда обращение к властям и в суды принесут убытки и потери. Перечень неприятностей может быть довольно большим, но тут же есть и рекомендации, что делать, а что лучше отложить на "потом".
        </p><p class="aligncenter" style="padding: 20px 0;">
            <a href="javascript:void(0);" class="button show_modal" onclick="yaCounter25119665.reachGoal('button_finance'); return true;">Я хочу получить гороскопы на все случаи жизни бесплатно</a>
        </p>
    </div>
    
    
    <div class="popup_modal" id="_business_modal">
        <div class="modal_head">
            <img src="images/business.gif" alt="" />
        </div>
        <h2>Бизнес-гороскоп</h2>
        <p>Рассчитав бизнес-гороскоп, Вы получите наглядную картину, когда Ваш бизнес будет развиваться успешно и как именно, а когда надвигаются неприятности. Для благоприятных периодов будут указаны "причины" этого и рекомендации по наиболее успешным действиям.
        </p><p>
        Для неблагоприятных периодов будут отмечены явные и скрытые, закулисные обстоятельства, которые непременно надо учесть в своей работе, чтобы не понести чрезмерные потери. Опять-таки, главное, что делает Ваш персональный бизнес-гороскоп, это предупреждение о благоприятных и опасных периодах.
        </p><p>
        Если Вы будете просто "плыть по течению", инстинктивно, не вполне осознанно реагировать на изменяющиеся обстоятельства жизни и бизнеса, то и получите то, "что на роду написано". Но если Вы сможете учесть в своей деятельности подсказки и советы судьбы, то будете намного успешнее и эффективнее продвигаться в неспокойном море бизнеса.
        </p><p class="aligncenter" style="padding: 20px 0;">
            <a href="javascript:void(0);" class="button show_modal" onclick="yaCounter25119665.reachGoal('button_business'); return true;">Я хочу получить гороскопы на все случаи жизни бесплатно</a>
        </p>
    </div>
    
    
    <div id="order" class="popup_modal">
        <p>
        Введите Ваши <strong>Имя</strong>, <strong>Email</strong> и <strong>Дату рождения</strong>, необходимые для расчета гороскопа.
        Точное <strong>время</strong> и <strong>место рождения</strong> необходимы для составления личной Натальной Карты, созданной в момент Вашего рождения. Если Вы планируете заказать Вашу Натальную Карту, то можете заполнить и эти два поля. Пожалуйста, место рождения указывайте в формате: <strong>Страна, Область, Город</strong>
        </p>
        <div style="width: 590px; margin: 0 auto;">
            <form action='' method='POST'>
                <p><input type='text' name='firstname' placeholder='Имя' required style="margin-right: 20px;"><input type='email' name='email' placeholder='Email' required></p>
                <!--p><input type='text' name='birthday' placeholder='День рождения (01.01.1970)' required id="datepicker" autocomplete="off"></p-->
                <p>
                    <select class="width-80" name="myday">
                        <option value="0">День</option>
                      <?php
                        for($i=1; $i<=31; $i++){
                            
                            echo "<option value='".($i<10 ? "0".$i : $i)."'>$i</option>";
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
                            echo "<option value='".($i<10 ? "0".$i : $i)."'>".$monthAr[$i][0]."</option>";
                        }
                      ?>
                    </select>
                    <select class="width-90" name="myyear">
                        <option value="0">Год</option>
                      <?php
                        for($i=2014; $i>=1914; $i--){
                            echo "<option value='$i'>$i</option>";
                        }
                      ?>
                    </select>
                    <span style="float: left; margin: 5px 10px 0 0;width: 5px;">&nbsp;</span>
                    <select class="width-80" name="hours">
                        <option value="0">Часы</option>
                      <?php
                        for($i=0; $i<=24; $i++){
                            echo "<option value='$i'>$i</option>";
                        }
                      ?>
                    </select>
                    <span style="float: left; margin: 5px 10px 0 0;">:</span>
                    <select class="width-80" name="minutes">
                        <option value="0">Минуты</option>
                      <?php
                        for($i=0; $i<=55; $i+=5){
                            echo "<option value='$i'>$i</option>";
                        }
                      ?>
                    </select>
                </p>
                <p><span>Место рождения: </span><input type='text' name='birthplace' placeholder='не определено' value='<?php echo $geocity; ?>' style="width: 410px;"></p>
                <p></p>
                <p class="inform aligncenter" style="float: none;"><input class="button" type='submit' value='ПОЛУЧИТЬ ГОРОСКОП НА КАЖДЫЙ ДЕНЬ' onclick="yaCounter25119665.reachGoal('zakaz'); return true;"></p>
                <div id="inform" class="inform">
                    <p align="center">Передача данных..</p>
                </div>
            </form>
        </div>
        <div style="float: left;">
            <p>
                Полностью конфиденциально! Мы гарантируем, что Ваши данные будут использоваться только для формирования Ваших гороскопов и астропрогнозов.
            </p>
        </div>
    </div>
    <div class="share42init" data-top1="50" data-top2="50" data-margin="0"></div>
    <script type="text/javascript" src="share42/share42.js"></script>

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