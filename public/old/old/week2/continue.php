<?php
    require_once('../admin/myconf.php');

    $usk = $_POST['usk'];

    if($usk){
        $check = "SELECT * FROM $clients WHERE secretkey = '$usk'";
        $result = mysql_query($check);
        $existsk = mysql_num_rows($result) or die ("<p>Error! No person found.</p>");
        $id_client = mysql_result($result, 0, 'id_client');
        $email = mysql_result($result, 0, 'email');
        $firstname = mysql_result($result, 0, 'firstname');
        $birthday = date("d.m.Y", strtotime(mysql_result($result, 0, "birthday")));
        $updatedate = date("Y-m-d", strtotime(mysql_result($result, 0, "updatedate")));
        $status = mysql_result($result, 0, 'status');
    }else{
        die ("<p>Error! No person found.</p>");
    }
    $datetime1 = new DateTime(date("Y-m-d"));
    $datetime2 = new DateTime($updatedate);
    $interval = $datetime1->diff($datetime2);
    $days = $interval->format('%a');
    if($status == 2){
        if($days<=$ftime){
            $info = "Вы уже подписаны на бесплатную рассылку!";
        }else{
            $query = "UPDATE $clients SET status = 3, updatedate=NOW() WHERE id_client = '$id_client';";
            $result = mysql_query($query);
            if($result){
//                $change = $API->deleteSubscriber('ONEWEEK', $email);
//                $change = $API->addSubscribe("EXTENDED", $email);
                $info = "Спасибо, что поделились нашим сайтом!<br>";
            }else{
                echo "<p>Извините, программная ошибка<br>Пожалуйста, обратитесь к администратору или попробуйте позднее.</p>";
            }
        }
    }elseif($status == 3){
        $info = "Вы уже подписаны на бесплатную рассылку!";
    }else{
        die ("<p>Бесплатная подписка невозможна</p>");
    }
?>
<p style="text-align:center;">
    <?php echo $info; ?>
</p>
<p style="text-align:center;">
    <br>
    <a class="button" href="javascript:void(0);" onclick="location.href='/show/?usk=<?php echo $usk; ?>'"><b>ПЕРЕЙТИ К МОИМ ГОРОСКОПАМ</b></a>
    <br>
    &nbsp;
</p>