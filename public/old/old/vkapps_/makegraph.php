<?php
    $aspects = "";
    $ratio = 0;
    $asp = Array();

    $transplan = Array(0, 2, 3, 4); //номера транзитных планет
    $degrees = Array(0, 60, 90, 120, 180);
	if($birthday == "0000-00-00"){
        $bd = $_COOKIE["bdate"];
        $bd = explode(".", $bd);
        $birthday = "$bd[2]-$bd[1]-$bd[0]";
    }
	$edate = date("Y-m-d", strtotime("$birthday"));
	$cdate = date("Y-m-d", strtotime("$cntdate"));
	$fdate = date("Y-m-1", strtotime("$cntdate"));
	$ldate = date("Y-m-t", strtotime("$cntdate"));
	$check = "SELECT edate, etime, sun, moon, mercury, venus, mars, jupiter, saturn, uranium, neptune, pluto FROM _ephemerides WHERE edate = '$edate' OR edate BETWEEN '$fdate' AND '$ldate'";
//	$check = "SELECT * FROM _ephemerides WHERE edate = '$edate' OR edate = '$cdate'";
    $ephemerides = mysql_query($check);
    $exist = mysql_num_rows($ephemerides) or die(mysql_error());
    if(!$exist){                        
        die ("Извините, непредвиденная ошибка. Пожалуйста, обратитесь к разработчикам приложения.");
    }
    
    $planets = Array('sun', 'moon', 'mercury', 'venus', 'mars', 'jupiter', 'saturn', 'uranium', 'neptune', 'pluto');
    $symbols = Array('&#9788;', '&#9790;', '&#9791;', '&#9792;', '&#9794;', '&#9795;', '&#9796;', '&#9797;', '&#9798;', '&#9799;');
    $orbises = Array(0, 0, 1, 1, 1, 0, 0, 1, 1, 1);
    //Натальные данные

    $planet1 = Array('Транзитное', 'Транзитная', 'Транзитный', 'Транзитная', 'Транзитный', 'Транзитный', 'Транзитный', 'Транзитный', 'Транзитный', 'Транзитный');
    $planet2 = Array('солнце', 'луна', 'меркурий', 'венера', 'марс', 'юпитер', 'сатурн', 'уран', 'нептун', 'плутон');
    $inorb = Array(
        0   => "в соединении", 
        60  => "в секстиле",
        90  => "в квадратуре", 
        120 => "в тригоне",
        180 => "в оппозиции"
    );
    $symorb = Array(
        0   => "&#9740;", 
        60  => "&#10033;",
        90  => "&#9744;", 
        120 => "&#9651;",
        180 => "&#9741;"
    );
    $planet3 = Array('солнцем', 'луной', 'меркурием', 'венерой', 'марсом', 'юпитером', 'сатурном', 'ураном', 'нептуном', 'плутоном');
    $t = 0;
    $aspects = Array();
    while($row = mysql_fetch_object($ephemerides)){
    	if($row->edate == $edate){
			$mydate = $row->edate;
			$mytime = $row->etime;
			for($i=3; $i<13; $i++){
				$np = $i-3;
				$natal[$t][] = $row->$planets[$np];
			}
			$t++;
		}else{
    		$mydate = $row->edate;
			$mytime = $row->etime;
            if(!array_key_exists($mydate,$aspects)){
                $aspects[$mydate] = Array();
            }
	    	for($i=3; $i<=7; $i++){
	    		if($i-3 == 1) continue;
				$tp = $i-3;
	    		$d2 = $row->$planets[$tp];
                $br = false;
			    for($t=0; $t<12; $t++){
					for($np=0; $np<10; $np++){
    					$d1 = $natal[$t][$np];
						for($c=-1; $c<=1; $c++){
                            //$ccc++;
							//if($np>5 && $c!=0) continue;
							$dc = abs($d1-$d2+$c);
							$dc = $dc>180 ? 360-$dc : $dc;
							$aa = $mydate.$tp.$dc.$np;
                            if(array_key_exists($aa, $asp)){
                                $br = true;
                                break;
                            }else if(in_array($dc, $degrees)){
                                $asp[$aa] = 1;
                                $myval = ($tp+1).$dc.($np+1);
                                $myvals[] = $myval;
                                $aspects[$mydate][] = $myval;
                                $rating[$mydate][] = $c;
							}
						}
                        if($br) break;
					}
                    if($br) break;
				}
	    	}
	    }
    }
    mysql_free_result($ephemerides);
    $check = "SELECT id_planet1, degrees, id_planet2, aspects, rating  FROM _aspects WHERE id_gorogroup = $id_gorogroup";
    $aspres = mysql_query($check);
    while($row = mysql_fetch_object($aspres)){
        $myval = $row->id_planet1.$row->degrees.$row->id_planet2;
        if(!in_array($myval, $myvals)) continue;
        $symbol[$row->id_planet1.$row->degrees.$row->id_planet2] = Array($symbols[$row->id_planet1-1].' '.$symorb[$row->degrees].' '.$symbols[$row->id_planet2-1]);
        $aspect[$row->id_planet1.$row->degrees.$row->id_planet2] = Array(
            //Заголовок аспекта
            $planet1[$row->id_planet1-1].' '.$planet2[$row->id_planet1-1].' '.$inorb[$row->degrees].' с '.$planet3[$row->id_planet2-1].' - <span style="font-family: fantasy; font-size: 16px;">'.$symbols[$row->id_planet1-1].' '.$symorb[$row->degrees].' '.$symbols[$row->id_planet2-1].'</span>',
            //Текстовая часть аспекта
            $row->aspects,
            //Оценка аспекта, приведение к виду -0+
            $row->rating>100 ? 100-$row->rating: $row->rating
        );
    }
    $asp = "";
    foreach($aspects as $mydate => $dateasp){
        $ratio = 0;
        $ad = '<span style="font-size: 12px; padding: 2px 3px; background-color: yellow; float: left; clear: both; width: 60px; text-align: center; font-family: fantasy;">'.date("d-m-Y",strtotime($mydate)).'</span><br>';
        for($i=0; $i<sizeOf($dateasp); $i++){
            if(!sizeOf($aspect[$dateasp[$i]])) continue;
            $r = $aspect[$dateasp[$i]][2];
            //$cr = abs($rating[$mydate][$i]);
            //$r = (abs($r)-$cr<=0?1:abs($r)-$cr)*$r/abs($r);
            if($cdate == $mydate){
                $rimg = $r>0 ? 'b_blu'.$r : 'b_red'.abs($r);
                $singlerate[] = $r;
                $asp[] = '<p class="rating" style="cursor: pointer;" title="Опубликовать на стене"><img title="Поделиться этим аспектом" src="'.$rimg.'.gif" style="float: left; margin-top:4px;" align="left"><b>'.$aspect[$dateasp[$i]][0].'</b><br>'.$aspect[$dateasp[$i]][1].'</p>';
            }
            $ad .= '<b style="font-size: 12px; padding: 2px 3px; background-color: yellow; float: left; clear: both; width: 60px; text-align: center; font-family: fantasy; color: '.($r>0?'green':'red').'">'.$symbol[$dateasp[$i]][0].'</b><br>';
            $ratio += $r;
        }
        $rate[] = $ratio;
        $aspday[] = $ad;
    }

    if($gsk && $stars<=0 && !$viewed){
        echo "
            <h3 class='aligncenter callbacks'>Извините, у Вас закончились звезды для просмотра аспектов.</h3>
            <br style='clear:both;'>
            <style>
                ul li{ padding: 2px 5px; font-size: 16px;}
            </style>
            <p class='aligncenter'><b>Вы можете приобрести звезды за голоса:</b><br>
                <center>
                <ul style='list-style: none;'>
                    <li><a href='javascript:void(0);' onclick='order(1);'>Приобрести 10 звезд за 1 голос</a></li>
                    <li><a href='javascript:void(0);' onclick='order(5);'>Приобрести 50 звезд за 4 голоса</a></li>
                    <li><a href='javascript:void(0);' onclick='order(10);'>Приобрести 100 звезд за 7 голосов</a></li>
                </ul>
                </center>
            </p>
        ";
    }else{
        if (!$asp){
            echo "<b>В этот день звезды не оказывают особого влияния на Вашу жизнь. Ни положительных, ни отрицательных аспектов не выявлено.</b>";
        }else{
            echo "<span>Вы можете разместить любой из аспектов у себя на стене, кликнув в любом месте в абзаце с аспектом.</span>";
            //echo $asp;
            arsort($singlerate);
            $mmax = $mmin = $mmid = 0;
            $ccc = 0;
            foreach($singlerate as $key => $val){
                if(!$mmax && $val == max($singlerate) && $val>0){
                    echo $asp[$key];
                    unset($asp[$key]);
                    $mmax = 1;
                    $ccc++;
                }else if(!$mmin && $val == min($singlerate) && $val<0){
                    echo $asp[$key];
                    unset($asp[$key]);
                    $mmin = 1;
                    $ccc++;
                }
            }
            if(!$mmid && count($singlerate)-$ccc){
                echo "<span style='border-bottom: 1px dashed; cursor: pointer;' onclick='$(\".reverse\").toggle(); VK.callMethod(\"resizeWindow\", 600, $(\"body .container\").height()+70);'>Менее значимые аспекты <b class='reverse'>+</b><b class='reverse' style='display: none;'>-</b></span><div class='reverse' style='display: none;'>";
                $mmid = 1;
            }
            foreach($singlerate as $key => $val){
                echo $asp[$key];
            }
            if($mmid){
                echo "</div>";
            }
        }
    }
    $sd = date("d ", strtotime("$cdate")).$bymonth[date("n", strtotime("$cdate"))-1].(date("Y", strtotime("$cdate")) <> date("Y", strtotime("$mydate")) ? date(" Y г.", strtotime("$cdate")) : "");
    $ed = date("d ", strtotime("$mydate")).$bymonth[date("n", strtotime("$mydate"))-1].date(" Y г.", strtotime("$mydate"));
    
    $sd = date("d ", strtotime("$fdate"));
    $ed = date("d ", strtotime("$ldate")).$bymonth[date("n", strtotime("$ldate"))-1].date(" Y г.", strtotime("$fdate"));
    echo "
    <div id='graph' style='height: 180px; width: 475px; margin-top: 10px; padding-bottom: 25px;display: none;'>
        <input type='hidden' value='График силы аспектов по дням с ".$sd." по ".$ed."'>
        <svg class='rate shadowing' style='height: 100%; width: 96%; background: white; border-radius: 10px;padding: 0;'
            xmlns='http://www.w3.org/2000/svg' version='1.1' 
            xmlns:xlink='http://www.w3.org/1999/xlink'>
        </svg>
    </div>
    <script>
    var scl = 0.56;
    function makeline(a, f, s, sw, i){
        line.interpolate(i);
        svg.data([a])
            .append('svg:path')
            .attr('d', line)
            .attr('fill', f)
            .attr('stroke', s)
            .attr('stroke-width', sw);
    }
    function maketext(t, x, y, c, s){
        svg.append('text')
            .text(t)
            .attr('x', x*scl)
            .attr('y', y*scl)
            .attr('fill', c)
            .attr('style', s ? s : 'font-size: 10px;');
    }
    function makehtml(t, x, y, c, s){
        svg.append('foreignObject')
            .html(t)
            .attr('x', x*scl)
            .attr('y', y*scl)
            .attr('width', 90)
            .attr('height', 200);
    }
    var svg = d3.select('.rate');
    var line = d3.svg.line()
        .x(function(d) { return d[0]*scl; })
        .y(function(d) { return d[1]*scl + 150*scl; });
    var rate = [];
    var aspect = [];
    var daygoro = [];
    ";
    echo "rate.push([50, 0]);\n";
    $myday = date("d",strtotime($cdate));
    $q = sizeOf($rate);
    for($i=0; $i<=30; $i++){
        $k = $i + 2;
        $maxabs = abs(max($rate))>abs(min($rate)) ? max($rate) : min($rate);
        if($maxabs==0) $maxabs=1;
        $v = floor(13*$rate[$i]/abs($maxabs));
        echo "rate.push([".($k*25).",".(-$v*10)."]);\n";
        echo "aspect[$i] = '".$aspday[$i]."';\n";
        $newdate = date("Ymd", strtotime("$fdate + $i days"));
        $newdate_ = date("d.m.Y", strtotime($newdate));
        $tmplink = 'gsk='.md5($id_gorogroup.$gid.$newdate)."&uid=$gid&udt=$newdate";
        echo "daygoro[$i]= ['$newdate_', '".$tmplink."'];\n";
        if($myday == $i){
            $curdateline = "makeline([[".($k*25).",-150],[".($k*25).", 150]], '', '#111', 2, '');\n";
        }
    }
    
    echo "rate.push([".($k*25).", 0]);\n";
    echo "
    makeline(rate, '#ccc', '#aaa', 2, 'cardinal');
    rate.splice(0,1);
    rate.splice($q,1);
    makeline([[50, -150],[50, 150],[50, 0],[800, 0]], 'transparent', '#777', 3, '');
    makeline([[50, -65],[800, -65]], 'transparent', '#ccc', 0.5, '');
    makeline([[50, 65],[800, 65]], 'transparent', '#ccc', 0.5, '');
    makeline([[50, -130],[800, -130]], 'transparent', '#ccc', 0.5, '');
    makeline([[50, 130],[800, 130]], 'transparent', '#ccc', 0.5, '');
    for(i=0; i<30; i++){
        makeline([[50+i*25, 130],[50+i*25, -130]], '#FFF', '#ccc', 0.3, '');
    }
    $curdateline;
    var online = 0;
    svg.selectAll('circle')
        .data(rate)
        .enter().append('circle')
            .attr('cx', function(d,i){ return d[0]*scl; })
            .attr('cy', function(d,i){ return d[1]*scl + 150*scl })
            .attr('fill', function(d,i){ return d[1]<=0 ? 'green' : 'red' })
            .attr('r', 7)
            .attr('style', 'cursor: pointer;')
            .on('mouseenter',function(data, index){
                if(online){
                    $('#graph svg path:last').remove();
                    $('#graph svg *:last-child').remove();
                }
                makeline([[data[0],data[1]+10],[data[0], +150]], '', '#333', 0.5, '');
                makehtml(aspect[index], index>15 ? data[0]-100: data[0]+1, data[1]>0 ? data[1]+150-aspect[index].split('<br>').length*31 : data[1]+160, '#000', '');
                online = 1;
            })
            .on('mouseleave',function(data, index){
                if(online){
                    $('#graph svg path:last').remove();
                    $('#graph svg *:last-child').remove();
                    online = 0;
                }  
            })
            .on('click',function(d, i){
                 $.fancybox.open('<p class=\'aligncenter\' style=\'font-size: 14px;\'>Вы хотите посмотреть астропрогноз на '+daygoro[i][0]+'?<br><br><a href=\'?'+daygoro[i][1]+'\' style=\'text-decoration: none;\'><b style=\'font-size: 20px; text-decoration: none;\'>Да</b></a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href=\'javascript:void(0);\' onclick=\'$.fancybox.close(); opengraph();\' style=\'font-size: 20px;text-decoration: none;\'>нет</a></p><p class=\'aligncenter\'><br>Если Вы еще не смотрели этот день, то это будет стоить Вам одну звезду.</p>',{topRatio:0,margin:[100, 0, 0, 0]});
                 return true;
            });

    maketext('100%', 5, 30, '#555', '');
    maketext('50%', 15, 90, '#555', '');
    maketext('0%', 20, 155, '#555', '');
    maketext('-50%', 10, 220, '#555', '');
    maketext('-100%', 0, 285, '#555', '');
    maketext('".date("d-m-Y", strtotime("$fdate"))."', 52, 15, '#555', '');
    maketext('".date("d-m-Y", strtotime("$ldate"))."', 722, 15, '#555', '');";

    $sd = date("d-m-Y", strtotime("$fdate"));
    $ed = date("d-m-Y", strtotime("$ldate"));

    $i=0;
    while($sd != $ed || $i < 30){
        $sd = date("d-m-Y", strtotime("$sd +1 days"));
        echo "maketext('".date("d", strtotime("$sd"))."', ".($i*25+67).", 305, '#555', '');";
        $i++;
    }
    echo "
        $(  '*', 
            $('.rate').clone()
                .appendTo('#minigraph')
        ).attr('transform', 'scale(0.4)');
        </script>";

?>