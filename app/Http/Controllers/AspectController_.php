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
    public $orbises = array(10, 10, 8, 8, 8, 6, 6, 4, 4, 4);
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
        if(strlen($checktime[0]) == 1){
            $checktime[0] = sprintf("%02d", $checktime[0]);
        }

        /**
         * TODO
         * Исправить проверочное время с учетом разницы UTC
         */
        $checktime[1] = '00';
        $checktime[2] = '00';
        $etime = implode(':', $checktime);

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

        /**
         * $ephemerieds1 - Эфемериды на месяц от выбранной даты
         */
        $ephemerides2 = $ephemerides->whereBetween('edate', [$sdate, $fdate])->whereTime('etime', $etime)->get();
        $t = 0;
        $aspects = Aspect::where('id_gorogroup', $astro)->get();
        $results = collect();
        foreach ($ephemerides2 as $ephemeride) {
            $mydate = $ephemeride->edate;
            $result = [];
            foreach ($transplan as $tp) {
                $p = $this->planets[$tp];
                //Угол транзитной планеты
                $d2 = $ephemeride->$p;
                $br = false;

                for ($np = 0; $np < 10; $np++) {
                     //Угол натальной планеты
                    $d1 = $mydegree[$np];

                    /**
                     * Высчитываем орбис - допустимое отклонение для аспекта)
                     * Таблица $orbises
                     */

                    for ($c = -1; $c <= 1; $c++) {
                        $dc = abs($d1 - $d2 + $c);

                        $dc = $dc > 180 ? 360 - $dc : $dc;

                        if ($tp == 2) {
                           // dump($p . ' (' . $d1 .') '  . ' ' . (isset($this->inorb[$dc])?$this->inorb[$dc]:'мимо') . ' (' . $dc .') ' . $this->planet2[$np] . ' (' . $d2 .') ' );
                        }


                        $aa = $mydate . $tp . $dc . $np;
                        if (array_key_exists($aa, $asp)) {
                            $br = true;
                            break;
                        } else if (in_array($dc, $degrees)) {
                            $asp[$aa] = 1;
                            $myval = ($tp + 1) . $dc . ($np + 1);
                            $myvals[] = $myval;

                            $aspectSearch = $aspects->filter(function ($item) use ($tp, $dc, $np) {
                                if($item->id_planet1 == $tp+1 && $item->degrees == $dc && $item->id_planet2 == $np) return true;
                            });
                            $aspect = $aspectSearch->first();
                            if (isset($aspect->aspects)) {
                                $r['interpretation'] = $aspect->aspects;
                                $r['symbol'] = $this->symbols[$tp] . ' ' . $this->symorb[$dc] . ' ' . $this->symbols[$np];
                                $r['aspect'] = $this->transit[$tp] . ' ' . $this->planet1[$tp] . ' ' . $this->inorb[$dc] . ' с ' . $this->planet2[$np] . ' - <span style="font-family: fantasy; font-size: 16px;">' . $this->symbols[$tp] . ' ' . $this->symorb[$dc] . ' ' . $this->symbols[$np] . '</span><br>';
                                $r['rating'] = $aspect->rating > 100 ? 100 - $aspect->rating : $aspect->rating;
                                $result[] = $r;
                            } else {
                                unset($aspect);
                            }
                        }
                    }
                    if ($br) break;
                }
                if (isset($result)) {
        //            dd($result);
                } else {
                    $r['interpretation'] = '';
                    $r['aspect'] = 'В этот день звезды не оказывают особого влияния на Вашу жизнь. Ни положительных, ни отрицательных аспектов не выявлено.';
                    $r['rating'] = 0;
                    $r['symbol'] = '';
                    $result[] = collect($r);
                }
                /*                                @foreach ($aspects as $aspday => $aspect)
                @php
                $ad[] = '<span style="font-size: 12px; padding: 2px 3px; background-color: #ffff00; float: left; clear: both; width: 60px; text-align: center; font-family: fantasy;">' . date("d-m-Y", strtotime($mydate)) . '</span><br>';
                $dayline[] = 'maketext("' . date("d", strtotime($aspday)) . '", ' . $loop->iteration . ' * scale + 23, 290, "#555", "");';
                $sumrate = array_sum(array_column($aspect, 'rating'));
                $maxrate = max(array_column($aspect, 'rating'));
                $minrate = min(array_column($aspect, 'rating'));
                $maxabs = abs($maxrate) > abs($minrate) ? $maxrate : $minrate;
                @endphp
                {{--{ xpoint: {{ ($loop->iteration + 1)  * 50 / 2 * scale }}, y0: 150, ypoint: {{ -$sumrate * 7 }} },--}}
                {xpoint: step + {{ $loop->iteration - 1 }} * scale + shift, y0: 150, ypoint: {{ -$sumrate * 9 }}},
                @endforeach*/
                $results->put($mydate, $result);
            }
        }
        dd("!!!");
        dd($result->all());
        return $result;
    }

}
