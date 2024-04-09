<?php
    require_once('myconf.php');
    $udt = isset($_GET['udt']) ? $_GET['udt'] : '';
    $uid = isset($_GET['uid']) ? $_GET['uid'] : '';
?>
<html>
    <body>
      <?php
        $usk = md5($uig.$uid.$udt);
        for($i=1; $i<=7; $i++){
            $gsk = md5($i.$uid.$udt);
            echo "<a href='/show/?gsk=".$gsk."&uid=".$uid."&udt=".$udt."'>$gsk</a><br>";
        }
      ?>
    </body>
</html>