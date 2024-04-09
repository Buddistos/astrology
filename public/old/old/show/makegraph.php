<?php
    $aspects = "";
    $ratio = 0;
    $asp = Array();

    $transplan = Array(0, 2, 3, 4); //номера транзитных планет
    $degrees = Array(0, 60, 90, 120, 180);
	
	$edate = date("Y-m-d", strtotime("$birthday"));
	$cdate = date("Y-m-d", strtotime("$cntdate"));
	$cdate30 = date("Y-m-d", strtotime("$cdate +30 days"));
	$check = "SELECT edate, etime, sun, moon, mercury, venus, mars, jupiter, saturn, uranium, neptune, pluto FROM _ephemerides WHERE edate = '$edate' OR edate BETWEEN '$cdate' AND '$cdate30'";
//	$check = "SELECT * FROM _ephemerides WHERE edate = '$edate' OR edate = '$cdate'";
    $ephemerides = mysql_query($check);
    $exist = mysql_num_rows($ephemerides) or die(mysql_error());
    if(!$exist){                        
        die ("Something wrong! Please call to administrator.<br>".mysql_error());
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
		$mydate = $row->edate;
		$mytime = $row->etime;
    	if($row->edate == $edate){
			for($i=3; $i<13; $i++){
				$np = $i-3;
				$natal[$t][] = $row->$planets[$np];
			}
			$t++;
		}else{
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
                            $ccc++;
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
            $planet1[$row->id_planet1-1].' '.$planet2[$row->id_planet1-1].' '.$inorb[$row->degrees].' с '.$planet3[$row->id_planet2-1].' - <span style="font-family: fantasy;">'.$symbols[$row->id_planet1-1].' '.$symorb[$row->degrees].' '.$symbols[$row->id_planet2-1].'</span>',
            $row->aspects,
            $row->rating>100 ? 100-$row->rating: $row->rating
        );
    }
    $asp = 0;
    foreach($aspects as $mydate => $dateasp){
        $ratio = 0;
        $ad = "";
        for($i=0; $i<sizeOf($dateasp); $i++){
            if(!sizeOf($aspect[$dateasp[$i]])) continue;
            $r = $aspect[$dateasp[$i]][2];
            if($cdate == $mydate){
                $asp = 1;
                $rimg = $r>0 ? 'b_blu'.$r : 'b_red'.abs($r);
                echo '<img src="'.$rimg.'.gif" style="float: left; margin-top:3px" align="left"><b>'.$aspect[$dateasp[$i]][0].'</b><br>'.$aspect[$dateasp[$i]][1].'<br><br/>';
            }
            $ratio += $r;
            $ad .= '<b style="font-size: 18px; padding: 2px 5px; background-color: yellow; float: left; clear: both; width: 80px; text-align: center; font-family: fantasy; color: '.($r>0?'green':'red').'">'.$symbol[$dateasp[$i]][0].'</b><br>';
        }
        $rate[] = $ratio;
        $aspday[] = $ad;
    }

    if ($asp){
        if($viewall) require_once('../admin/social.inc');
    }else{
        echo "<b>В этот день звезды не оказывают особого влияния на Вашу жизнь. Ни положительных, ни отрицательных аспектов не выявлено.</b>";
    }

    $sd = date("d ", strtotime("$cdate")).$bymonth[date("n", strtotime("$cdate"))-1].(date("Y", strtotime("$cdate")) <> date("Y", strtotime("$mydate")) ? date(" Y г.", strtotime("$cdate")) : "");
    $ed = date("d ", strtotime("$mydate")).$bymonth[date("n", strtotime("$mydate"))-1].date(" Y г.", strtotime("$mydate"));
    
    echo "
    <div class='info'>
        <p>
            <b>Обозначения</b><br>
            Для наглядности благоприятные указания помечены синими черточками перед текстом, а неблагоприятные - красными черточками. Их длина соответствует силе влияния этого аспекта. 
        </p>
        <p>
            <table border='0' width='100%'>
            <tbody><tr><td>
            <p><img border='0' src='b_blu1.gif'> 1  Очень слабое позитивное указание <br>
            <img border='0' src='b_blu2.gif'> 2  Слабое позитивное указание  <br>
            <img border='0' src='b_blu3.gif'> 3  Позитивное указание средней силы  <br>
            <img border='0' src='b_blu4.gif'> 4  Сильное позитивное указание <br>
            <img border='0' src='b_blu5.gif'> 5  Очень сильное позитивное указание </p>
            </td> <td>
            <p> <img border='0' src='b_red1.gif'> -1  Очень слабое негативное указание <br>
            <img border='0' src='b_red2.gif'> -2  Слабое негативное указание  <br>
            <img border='0' src='b_red3.gif'> -3  Негативное указание средней силы  <br>
            <img border='0' src='b_red4.gif'> -4  Сильное негативное указание <br>
            <img border='0' src='b_red5.gif'> -5  Очень сильное негативное указание </p>
            </td></tr> </tbody></table>
        </p>
    </div>
    <h4>График оценки влияния планет с ".$sd." по ".$ed."</h4>
    <div id='graph' style='height: 300px; width: 800px; margin-top: 10px; margin-left: 20px; padding: 10px;'>
        <svg class='rate shadowing' style='height: 100%; width: 100%; background: white; border-radius: 10px;padding: 0;'
            xmlns='http://www.w3.org/2000/svg' version='1.1' 
            xmlns:xlink='http://www.w3.org/1999/xlink'>
        </svg>
    </div>
    <script>
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
            .attr('x', x)
            .attr('y', y)
            .attr('fill', c)
            .attr('style', s ? s : 'font-size: 14px;');
    }
    function makehtml(t, x, y, c, s){
        svg.append('foreignObject')
            .html(t)
            .attr('x', x)
            .attr('y', y)
            .attr('width', '90')
            .attr('height', 200);
    }
    var svg = d3.select('.rate');
    var line = d3.svg.line()
        .x(function(d) { return d[0]; })
        .y(function(d) { return d[1] + 150; });
    var rate = [];
    var aspect = [];
    ";
    echo "    rate.push([50, 0]);\n";
    $q = sizeOf($rate);
    for($i=0; $i<=30; $i++){
        $k = $i + 2;
        $maxabs = abs(max($rate))>abs(min($rate)) ? max($rate) : min($rate);
        $v = floor(13*$rate[$i]/abs($maxabs));
        echo "rate.push([".($k*25).",".(-$v*10)."]);\n";
        echo "aspect[$i] = '".$aspday[$i]."';\n";
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
    var online = 0;
    svg.selectAll('circle')
        .data(rate)
        .enter().append('circle')
            .attr('cx', function(d,i){ return d[0]; })
            .attr('cy', function(d,i){ return d[1] + 150 })
            .attr('fill', function(d,i){ return d[1]<=0 ? 'green' : 'red' })
            .attr('r', 7)
            .attr('style', 'cursor: pointer;')
            .on('mouseenter',function(data, index){
                if(online){
                    $('#graph svg path:last').remove();
                    $('#graph svg *:last-child').remove();
                }
                makeline([[data[0],-150],[data[0], +150]], '', '#333', 0.5, '');
                makehtml(aspect[index], index>15 ? data[0]-91: data[0]+1, data[1]>0 ? data[1]+150-aspect[index].split('<br>').length*21 : data[1]+160, '#000', '');
                console.log(data[1]);
                online = 1;
            })
            .on('mouseleave',function(data, index){
                if(online){
                    $('#graph svg path:last').remove();
                    $('#graph svg *:last-child').remove();
                    online = 0;
                }  
            })
            .on('click',function(){
                 yaCounter25119665.reachGoal('pipclick');
                 return true;
            });

    maketext('100%', 10, 30, '#555', '');
    maketext('50%', 20, 90, '#555', '');
    maketext('0%', 25, 155, '#555', '');
    maketext('-50%', 15, 220, '#555', '');
    maketext('-100%', 5, 285, '#555', '');
    maketext('".date("d-m-Y", strtotime("$cdate"))."', 52, 15, '#555', '');
    maketext('".date("d-m-Y", strtotime("$mydate"))."', 722, 15, '#555', '');";

    $sd = date("d-m-Y", strtotime("$cdate"));
    $ed = date("d-m-Y", strtotime("$mydate"));

    $i=0;
    while($sd != $ed || $i < 30){
        $sd = date("d-m-Y", strtotime("$sd +1 days"));
        echo "maketext('".date("d", strtotime("$sd"))."', ".($i*25+67).", 295, '#555', '');";
        $i++;
    }
    echo "
        $(  '*', 
            $('.rate').clone()
                .appendTo('#minigraph')
        ).attr('transform', 'scale(0.4)');;
        </script>";

?>