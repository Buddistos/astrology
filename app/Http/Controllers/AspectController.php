<?php

namespace App\Http\Controllers;

use App\Models\Aspect;
use App\Models\Ephemerides;
use Illuminate\Http\Request;
use PhpParser\Node\Expr\Array_;
use function PHPUnit\Framework\isEmpty;

class AspectController extends Controller
{
    private $planets = array('sun', 'moon', 'mercury', 'venus', 'mars', 'jupiter', 'saturn', 'uranium', 'neptune', 'pluto');
    public $symbols = array('&#9788;', '&#9790;', '&#9791;', '&#9792;', '&#9794;', '&#9795;', '&#9796;', '&#9797;', '&#9798;', '&#9799;');
    public $orbises = array(0, 0, 1, 1, 1, 0, 0, 1, 1, 1);
    public $transit = array('Транзитное', 'Транзитная', 'Транзитный', 'Транзитная', 'Транзитный', 'Транзитный', 'Транзитный', 'Транзитный', 'Транзитный', 'Транзитный');
    public $planet1 = array('солнце', 'луна', 'меркурий', 'венера', 'марс', 'юпитер', 'сатурн', 'уран', 'нептун', 'плутон');
    public $inorb = array(
        0 => "в соединении",
        60 => "в секстиле",
        90 => "в квадратуре",
        120 => "в тригоне",
        180 => "в оппозиции"
    );
    public $symorb = array(
        0 => "&#9740;",
        60 => "&#10033;",
        90 => "&#9744;",
        120 => "&#9651;",
        180 => "&#9741;"
    );
    public $planet2 = array('солнцем', 'луной', 'меркурием', 'венерой', 'марсом', 'юпитером', 'сатурном', 'ураном', 'нептуном', 'плутоном');

    public function __construct()
    {
    }

    public function getAspects($birthday, $birthtime, $fordate, $utc, $astro = null, $during = 30)
    {
        $ratio = 0;
        $asp = array();

        $transplan = array(0, 2, 3, 4); //номера транзитных планет
        $degrees = array(0, 60, 90, 120, 180);

        $edate = date("Y-m-d", strtotime($birthday));
        $sdate = date("Y-m-d", strtotime($fordate));
        $fdate = date("Y-m-d", strtotime($sdate . ' +' . $during . 'days'));
        $etime = date("H:i:s", strtotime($birthtime));

        $utcArray = explode(':', $etime);
        $utcHours = $utcArray[0];

        $checktime = explode(':', $etime);
        if ($checktime[0] % 2 > 0) {
            if ($checktime[1] > 0) {
                $checktime[0]++;
            } else {
                $checktime[0]--;
            }
        }
        /**
         * TODO
         * Исправить проверочное время с учетом разницы UTC
         */
        $checktime[1] = '00';
        $checktime[2] = '00';
        $etime = implode($checktime, ':');

        /**
         * $ephemerieds1 - Положение планет на день рождения
         * $mydegree - углы планет
         */
        $ephemerides = new Ephemerides();
        $ephemerides1 = $ephemerides->where('edate', $edate)->whereTime('etime', $etime)->get();
        foreach ($ephemerides1 as $row) {
            for ($i = 0; $i < 10; $i++) {
                $p = $this->planets[$i];
                $mydegree[] = $row->$p;
            }
        }
        //dd($mydegree);

        /**
         * $ephemerieds1 - Эфемериды на месяц от выбранной даты
         */
        $ephemerides2 = $ephemerides->whereBetween('edate', [$sdate, $fdate])->whereTime('etime', $etime)->get();
        $t = 0;
        $aspects = Aspect::where('id_gorogroup', $astro)->get();
        foreach ($ephemerides2 as $ephemeride) {
            $mydate = $ephemeride->edate;
/*            if (!array_key_exists($mydate, $aspects)) {
                $aspects[$mydate] = array();
            }*/
            for ($i = 0; $i <= 4; $i++) {
                if ($i == 1) continue;
                $tp = $i - 3;
                $p = $this->planets[$i];
                $d2 = $ephemeride->$p;
                $br = false;

                for ($np = 0; $np < 10; $np++) {
                    $d1 = $mydegree[$np];
                    for ($c = -1; $c <= 1; $c++) {
                        $dc = abs($d1 - $d2 + $c);
                        $dc = $dc > 180 ? 360 - $dc : $dc;
                        $aa = $mydate . $i . $dc . $np;
                        if (array_key_exists($aa, $asp)) {
                            $br = true;
                            break;
                        } else if (in_array($dc, $degrees)) {
                            $asp[$aa] = 1;
                            $myval = ($i + 1) . $dc . ($np + 1);
                            $myvals[] = $myval;
                            //$aspect[$mydate][] = $myval;
                            // $i=4; $dc = 180; $np = 3;
                            //dump($i + 1, $dc, $np + 1);
                            //$aspect = Aspect::where('id_gorogroup', $astro);
                            //$aspect = $aspect->where('id_planet1', $i + 1)->where('degrees', $dc)->where('id_planet2', $np + 1)->first();

                            $aspectSearch = $aspects->filter(function ($item) use ($i, $dc, $np) {
                                if($item->id_planet1 == $i+1 && $item->degrees == $dc && $item->id_planet2 == $np) return true;
                            });
                            $aspect = $aspectSearch->first();
                            if (isset($aspect->aspects)) {
                                $r['aspect'] = $aspect->aspects;
                                $r['symbol'] = $this->symbols[$i] . ' ' . $this->symorb[$dc] . ' ' . $this->symbols[$np];
                                $r['interpretation'] = $this->transit[$i] . ' ' . $this->planet1[$i] . ' ' . $this->inorb[$dc] . ' с ' . $this->planet2[$np] . ' - <span style="font-family: fantasy; font-size: 16px;">' . $this->symbols[$i] . ' ' . $this->symorb[$dc] . ' ' . $this->symbols[$np] . '</span><br>';
                                $r['rating'] = 100 ? 100 - $aspect->rating : $aspect->rating;
                                $result[$ephemeride->edate][] = $r;
                                unset($r);
                            } else {
                                unset($aspect);
                            }
                        }
                    }
                    if ($br) break;
                }
            }
//            }
        }
        dd($result);

        $aspect = array();
        $aspres = Aspect::where('id_gorogroup', $astro)->get();
        foreach ($aspres as $row) {
            $myval = $row->id_planet1 . $row->degrees . $row->id_planet2;
            if (!in_array($myval, $myvals)) continue;
            $symbol = $this->symbols[$row->id_planet1 - 1] . ' ' . $this->symorb[$row->degrees] . ' ' . $this->symbols[$row->id_planet2 - 1];
            $aspect[$row->id_planet1 . $row->degrees . $row->id_planet2] = array(
                //Заголовок аспекта
                $this->transit[$row->id_planet1 - 1] . ' ' . $this->planet1[$row->id_planet1 - 1] . ' ' . $this->inorb[$row->degrees] . ' с ' . $this->planet2[$row->id_planet2 - 1] . ' - <span style="font-family: fantasy; font-size: 16px;">' . $this->symbols[$row->id_planet1 - 1] . ' ' . $this->symorb[$row->degrees] . ' ' . $this->symbols[$row->id_planet2 - 1] . '</span>',
                //Текстовая часть аспекта
                $row->aspects,
                //Оценка аспекта, приведение к виду -0+
                $row->rating > 100 ? 100 - $row->rating : $row->rating,
                //символика аспекта
                $symbol
            );
        }
        dd($aspect);
        return $aspect;
    }

}
