<?php
    require_once('myconf.php');

    $usk = $_GET['usk'];    

    $check = "SELECT * FROM $clients WHERE secretkey = '$usk'";
    $result = mysql_query($check);
    $existsk = mysql_num_rows($result);
    if($existsk == 1){
        $firstname = mysql_result($result, 0, "firstname");
        $email = mysql_result($result, 0, "email");
        $birthday = date("d.m.Y", strtotime(mysql_result($result, 0, "birthday")));
        $birthtime = mysql_result($result, 0, "birthtime");
        $birthplace = mysql_result($result, 0, "birthplace");
        $coords = mysql_result($result, 0, "coords");
        $ip = mysql_result($result, 0, "ip");
        $rd = date("d.m.Y", strtotime(mysql_result($result, 0, "regdate")));
        $ud = date("d.m.Y", strtotime(mysql_result($result, 0, "updatedate")));
    }

?>
<html>
<head>
    <meta charset="utf-8">
    <title>Администрирование</title>
    <style>
        body{font-size: 12px;}
        textarea{width: 130px; height: 50px;}
        #themes span{float: left; width: 130px; text-align: center;}
        #themes p{clear: both;}
        #themes b{float: left; width: 100px; padding-top: 10px;}
        input[type="text"], input[type="email"]{padding: 5px 3px; width: 250px;}
        input[type="submit"]{padding: 5px 10px;}
    </style>
    <script src="//ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
    <link rel="stylesheet" href="//ajax.googleapis.com/ajax/libs/jqueryui/1.10.4/themes/smoothness/jquery-ui.css" />
    <script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.10.4/jquery-ui.min.js"></script>
    <script>
        $(function() {
            $("#datepicker").datepicker({ 
                dateFormat: "dd.mm.yy",
                monthNames: ["Январь","Февраль","Март","Апрель","Май","Июнь","Июль","Август","Сентябрь","Октябрь","Ноябрь","Декабрь"],
                firstDay: 1,
                dayNamesMin: ["Вс", "Пн", "Вт", "Ср", "Чт", "Пт", "Сб"],
                changeYear: true,
                yearRange: "1900:"
            });

            $('form1').submit(function(){
              alert($(this).serialize());
              return false;
              $("input[type='submit']").attr("disabled", "true");
              iframe = $("#console").contents().find("body");
              iframe.html((iframe.html()?iframe.html()+"<br>":"") + "<b style='color: green;font-size: 12px;'>Передача данных..</b><br>");
              iframe.animate(
                { scrollTop: $('#console').contents().scrollTop()+1000 }, 'medium'
              );
              var s = $('form').serializeArray();
              $.post("client.php", s, function(data){
                  iframe.html(iframe.html() + data);
                  iframe.animate(
                    { scrollTop: $('#console').contents().scrollTop()+1000 }, 'medium',
                    function(){
                        $("input[type='submit']").removeAttr("disabled");
                    }
                  );
                  
              });
              return false;
            });
        });
    </script>

</head>
<body>
<h2><?php if($existsk){echo 'Редактирование клиента';}else{echo 'Новый клиент';} ?></h2>

<p>
    from IP: <b><?php echo $ip; ?></b>,
    date start: <b><?php echo $rd; ?></b>,
    <?php if($ud){
        echo "date change: <b>$ud</b>,";
    }?>
    coords: <b><?php echo $coords; ?></b>
</p>

<form action="client.php" method="post" enctype="multipart/form-data" target="console">

    <div style="width: 350px; float: left;">
    <p>
        <input type='text' name='firstname' placeholder='Имя' required value="<?php echo $firstname; ?>"><br>
        <input type='email' name='email' placeholder='Email' required value="<?php echo $email; ?>" <?php if($email) echo 'readonly'; ?>><br>
        <input type='text' name='birthday' placeholder='День рождения (01.01.1970)' required value="<?php echo $birthday; ?>" style="width: 120px;margin-right: 7px;">
        <input type='text' name='birthtime' placeholder='Время рождения (00:00)' value="<?php echo $birthtime; ?>" style="width: 120px;">
        <input type='text' name='birthplace' placeholder='Место рождения' value="<?php echo $birthplace; ?>"><br>
        <label><input type='checkbox' name='notcheck'> - не проверять совпадения</label><br>
        <input type='submit' value='записать'>
    </p>
    <!--div style="clear: both;" id="themes">
        <b>Финансы</b><input type="file" name="_finance"><br>
        <b>Бизнес</b><input type="file" name="_business"><br>
        <b>Авто</b><input type="file" name="_auto"><br>
        <b>Любовь</b><input type="file" name="_love"><br>
        <b>Секс</b><input type="file" name="_sex"><br>
        <b>Отпуск</b><input type="file" name="_holyday"><br>
        <b>Здоровье</b><input type="file" name="_health"><br>
    </div-->
</div>
    <div style="float: left; font-size: 12px;">
        <b>Консоль</b><br>
        <iframe name="console" id="console" style="width: 600px; height: 400px;"></iframe>
        <p>
            <?php 
                if($existsk){
                    echo "<input type='hidden' name='usk' value='$usk'><input type='hidden' name='ip' value='$ip'>";
                }
            ?>
        </p>
    </div>

    <!--div style="clear: both; display: none;" id="themes">
    <b></b><span>Месяц 1</span><span>Месяц 2</span><span>Месяц 3</span><span>Месяц 4</span><span>Месяц 5</span><span>Месяц 6</span>
    <p>
        <b>Финансы</b>
        <textarea name="group1_1" placeholder="Финансы"></textarea>
        <textarea name="group1_2" placeholder="Финансы"></textarea>
        <textarea name="group1_3" placeholder="Финансы"></textarea>
        <textarea name="group1_4" placeholder="Финансы"></textarea>
        <textarea name="group1_5" placeholder="Финансы"></textarea>
        <textarea name="group1_6" placeholder="Финансы"></textarea>
    </p>
    <p>
        <b>Бизнес</b>
        <textarea name="group2_1" placeholder="Бизнес"></textarea>
        <textarea name="group2_2" placeholder="Бизнес"></textarea>
        <textarea name="group2_3" placeholder="Бизнес"></textarea>
        <textarea name="group2_4" placeholder="Бизнес"></textarea>
        <textarea name="group2_5" placeholder="Бизнес"></textarea>
        <textarea name="group2_6" placeholder="Бизнес"></textarea>
    </p>
    <p>
        <b>Авто</b>
        <textarea name="group3_1" placeholder="Авто"></textarea>
        <textarea name="group3_2" placeholder="Авто"></textarea>
        <textarea name="group3_3" placeholder="Авто"></textarea>
        <textarea name="group3_4" placeholder="Авто"></textarea>
        <textarea name="group3_5" placeholder="Авто"></textarea>
        <textarea name="group3_6" placeholder="Авто"></textarea>
    </p>
    <p>
        <b>Любовь</b>
        <textarea name="group4_1" placeholder="Любовь"></textarea>
        <textarea name="group4_2" placeholder="Любовь"></textarea>
        <textarea name="group4_3" placeholder="Любовь"></textarea>
        <textarea name="group4_4" placeholder="Любовь"></textarea>
        <textarea name="group4_5" placeholder="Любовь"></textarea>
        <textarea name="group4_6" placeholder="Любовь"></textarea>
    </p>
    <p>
        <b>Секс</b>
        <textarea name="group5_1" placeholder="Секс"></textarea>
        <textarea name="group5_2" placeholder="Секс"></textarea>
        <textarea name="group5_3" placeholder="Секс"></textarea>
        <textarea name="group5_4" placeholder="Секс"></textarea>
        <textarea name="group5_5" placeholder="Секс"></textarea>
        <textarea name="group5_6" placeholder="Секс"></textarea>
    </p>
    <p>
        <b>Отпуск</b>
        <textarea name="group6_1" placeholder="Отпуск"></textarea>
        <textarea name="group6_2" placeholder="Отпуск"></textarea>
        <textarea name="group6_3" placeholder="Отпуск"></textarea>
        <textarea name="group6_4" placeholder="Отпуск"></textarea>
        <textarea name="group6_5" placeholder="Отпуск"></textarea>
        <textarea name="group6_6" placeholder="Отпуск"></textarea>
    </p>
    <p>
        <b>Здоровье</b>
        <textarea name="group7_1" placeholder="Здоровье"></textarea>
        <textarea name="group7_2" placeholder="Здоровье"></textarea>
        <textarea name="group7_3" placeholder="Здоровье"></textarea>
        <textarea name="group7_4" placeholder="Здоровье"></textarea>
        <textarea name="group7_5" placeholder="Здоровье"></textarea>
        <textarea name="group7_6" placeholder="Здоровье"></textarea>
    </p>
    <p><input type='submit' value='записать' style="margin-left: 100px;"></p>
    </div-->

</form>
</body>
</html>