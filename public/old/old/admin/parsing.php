<?php
    if($usk){
        $what = 'Обновление';
    }else{
        $what = 'Создание';
    }
    echo "<b style='color: blue;'>$what клиента</b><br><b>$firstname</b>, <b>$bd</b> <b>$birthtime<b>, <b>$birthplace<b>, <b>$email<b><br>";
    $howmuch = 0;

    $check = "SELECT * FROM $gorogroup";
    $allgoro = mysql_query($check);
    while ($row = mysql_fetch_array($allgoro)) {
        $goroname[$row[1]] = Array($row[0],$row[2]);
        $gorofile = $row[2];
        $id_gorogroup = $row[0];

        if(isset($_FILES[$gorofile]) && strpos($_FILES[$gorofile]["name"], ".zip")){
            if(is_uploaded_file($_FILES[$gorofile]["tmp_name"])){
                $zip = new ZipArchive();
                $filename = $_FILES[$gorofile]["tmp_name"];
                if ($zip->open($filename, ZIPARCHIVE::CREATE) !== TRUE) {
                    echo "Невозможно открыть <$filename><br>";
                }else{
                    echo "Файл <b style='color:900100;'>".$_FILES[$gorofile]["name"]."</b> успешно загружен.<br>";
                    $zip->open($filename);
                    for ($i = 0; $i < $zip->numFiles; $i++) {
                        $stat = $zip->statIndex($i);
                        $file = $stat["name"];
                        if(strpos($file, "html")){
                            $content = $zip->getFromName($file);
                            $regexp = "/<div.*>|<.div>|<font.*>|<.font>|<meta.*>/i";
                            $content = preg_replace($regexp, "<!-- replaced -->", $content);
                            $content = iconv("cp1251","utf8",$content);
                            
                            $html = new DOMDocument();
                            $html->loadHTML($content);

                            $title = utf8_decode($html->getElementsByTagName("title")->item(0)->nodeValue);
                            $h1 = utf8_decode($html->getElementsByTagName("h1")->item(0)->nodeValue);

                            $arr = explode(", ", $h1);
                            $goroname2 = $arr[1];
                            
                            if(mb_ucwords($firstname) <> mb_ucwords($goroname2)){
                                echo "Для кого составлен гороскоп? Имя клиента в прогнозе: <b style='color: red;'>".$goroname2."</b>.<br>";
                            }
                            $howmuch++;
                            
                            $months = Array("январь","февраль","март","апрель","май","июнь","июль","август","сентябрь","октябрь","ноябрь","декабрь");
                            $bymonth = Array("января","февраля","марта","апреля","мая","июня","июля","августа","сентября","октября","ноября","декабря");
                            
                            foreach($months as $n => $m){
                                $findme = stripos($h1, $m);
                                if($findme){
                                    $month = $n+1;
                                    $year = substr($h1, $findme + strlen($m) + 1, 4);
                                    echo $title." на ".$m." ".$year." года - ";
                                    break;
                                }
                            }
                            
                            $bm = $bymonth[$month-1];
                            $parsing = split("\n",$content);
                            $d = 1;
                            foreach($parsing as $key => $value){

                                $findme = stripos($value, "Гороскоп на ".$d." ".$bm);
                                if($findme){

                                    $daygoro[$d] = "<h4>".strip_tags($parsing[$key])."</h4>";

                                    $k = 1;
                                    
                                    while(stripos($parsing[$key+$k],"img")){
                                        $daygoro[$d] .= $parsing[$key+$k];
                                        $k++;
                                    }
                                    $mytable = $goroname[trim($title)][1];
                                    $mygorogroup = $goroname[trim($title)][0];
                                    if($mygorogroup <> $id_gorogroup){
                                        die("<b style='color: red;'>Ошибка несоответствия ID горокопа в базе и в zip файле: $id_gorogroup <> $mygorogroup для таблицы $mytable. Необходима проверка.</b>");
                                    }
                                    $check = "SELECT * FROM $mytable WHERE day=$d AND month=$month AND year=$year AND id_client=$id_client";
                                    $result = mysql_query($check);
                                    if(mysql_num_rows($result) and $usk){
                                        $query = "UPDATE $mytable SET daygoro='$daygoro[$d]', updatedate=NOW() WHERE day=$d AND month=$month AND year=$year AND id_client=$id_client";
                                    }else{
                                        $secretkey = "";
                                        while(!$secretkey){
                                            $secretkey = md5(uniqid(rand(),1));
                                            $check = "SELECT * FROM $mytable WHERE gsk = '$secretkey'";
                                            $result = mysql_query($check) or die("SQL error: ".mysql_error());
                                            if(mysql_num_rows($result)) $secretkey = ""; 
                                        }
                                        $query = "INSERT INTO $mytable (id_gorogroup, id_client, day, month, year, createdate, gsk, daygoro) VALUES($id_gorogroup, $id_client, $d, $month, $year, NOW(), '$secretkey', '$daygoro[$d]')";
                                    }
                                    $result = mysql_query($query);
                                    if(!$result){
                                        die ("<b style='color: red;'>Ошибка формировании гороскопа на $d-$month-$year.</b><br>$query<br>".mysql_error());
                                    }
                                    $d++;
                                }
                            }
                        }elseif(strpos($file, "jpeg")){
                            $imgname = str_replace("Hor",$id_client."_".$id_gorogroup,$file);
                            $imgname = str_replace("jpeg","jpg",$imgname);
                            $newfile = "/home/bestho/public_html/graphs/".$imgname;
                            $act = file_exists($newfile) ? "update" : "create";
                            if(copy("zip://".$filename."#".$file, $newfile)){
                                echo $imgname." - $act<br>";
                            }else{
                                die ("<b style='color: red;'>Ошибка!<br> Не смог сохраненить график гороскопа $file -> $imgname</b>");
                            }
                        }
                    }
                    $zip->close();
                }
            }else{
                echo "Ошибка загрузки файла.<br>";
            }
            echo "<b style='color: blue;'>Составление гороскопа</b> для клиента <b style='color: blue;'>".$goroname2."</b>.<br>";
        }
    }

    if($howmuch){

        $check = "SELECT * FROM $clients WHERE secretkey = '$usk' AND email = '$email'";
        $result = mysql_query($check);
        if(mysql_num_rows($result)){
            $query = "UPDATE $clients SET status='2' WHERE secretkey='$usk' AND email='$email'";
            $result = mysql_query($query);
            if(!$result){
                echo "<b style='color: red;'>Ошибка. Не удалось обновить статус клиента.</b><br>".mysql_error();
            }else{            
                echo "Статус клиента обновлен - 2 (подписан на раздачу гороскопов)<br>";
            }
        }else{
            echo "<b style='color: red;'>Ошибка. Клиент не найден для обновления статуса.</b><br>".mysql_error();
        }
    
        if($usk){
            $what = 'Обновлено';
        }else{
            $what = 'Добавлено';
        }
        echo "$what гороскопов: <b>$howmuch</b>.<br>";
        $API->debug = TRUE;
        $beginner = $API->getSubscriber("BEGINNER", "$email");
        $oneweek = $API->getSubscriber("ONEWEEK", "$email");
        if($beginner && !$oneweek){
            $change = $API->addSubscribe("ONEWEEK", $email);
            echo "Клиент подписан на недельную раздачу!<br>";
        }elseif($beginner){
            echo "<b style='color: orangered;'>Клиент уже был подписан на недельную раздачу!</b><br>";
        }else{
            echo "<b style='color: red;'>Клиент не прописан в начальной базе, что странно! Необходимо разобраться..</b><br>";
        }
        print_r($API->debug_output);
    }else{
        echo "Операций с гороскопами не было.<br>";
    }
    
    $maked++;
    $query = "UPDATE $option SET option_value = $maked WHERE option_key = 'maked';";
    $result = mysql_query($query) or die("<b style='color: red;'>Ошибка. Не удалось увеличить количество составленных гороскопов _option.maked</b>");
    
    echo "<b style='color: blue;'>Успешно завершено!</b>";
?>