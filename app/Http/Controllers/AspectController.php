<?php

namespace App\Http\Controllers;

use App\Models\Aspect;
use App\Models\Ephemerides;
use Illuminate\Http\Request;
use PhpParser\Node\Expr\Array_;
use function PHPUnit\Framework\isEmpty;
use function PHPUnit\Framework\isNull;

class AspectController extends Controller
{
    private $planets = array('sun', 'moon', 'mercury', 'venus', 'mars', 'jupiter', 'saturn', 'uranium', 'neptune', 'pluto');
    public $symbols = array('&#9788;', '&#9790;', '&#9791;', '&#9792;', '&#9794;', '&#9795;', '&#9796;', '&#9797;', '&#9798;', '&#9799;');
    //public $orbises = array(10, 10, 8, 8, 8, 6, 6, 4, 4, 4);
    //public $orbises = array(2, 2, 1, 1, 1, 1, 1, 1, 1, 1);
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

    /**
     * @param $birthday - ДР
     * @param $birthtime - Время рождения
     * @param $fordate - расчетная дата
     * @param $utc - UTC места рождения
     * @param null $astro - Тема аспекта
     * @param int $during - Количество расчетных дней
     * @return - Массив аспектов на расчетные даты
     */
    public function getAspects($birthday, $birthtime, $fordate, $utc, $astro = null, $during = 30)
    {
        $ratio = 0;
        $asp = array();

        $transplan = array(0, 2, 3, 4); //номера транзитных планет
        $degrees = array(0, 60, 90, 120, 180);

        $bdate = date("Y-m-d", strtotime($birthday));
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
        if (strlen($checktime[0]) == 1) {
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
         * $ephemerieds1 - Положение планет на день рождения по времени рождения
         * $mydegree - углы планет
         */
        $ephemerides = new Ephemerides();
        $ephemerides1 = $ephemerides->where('edate', $bdate)->whereTime('etime', $etime)->get();
        foreach ($ephemerides1 as $row) {
            for ($i = 0; $i < 10; $i++) {
                $p = $this->planets[$i];
                $mydegree[] = $row->$p;
            }
        }

        /**
         * $ephemerieds1 - Эфемериды на месяц от выбранной даты по времени рождения
         */
        $ephemerides2 = $ephemerides->whereBetween('edate', [$sdate, $fdate])->whereTime('etime', $etime)->get();
        $aspects = Aspect::where('id_gorogroup', $astro)->get();
        $results = collect();
        foreach ($ephemerides2 as $ephemeride) {
            $mydate = $ephemeride->edate;
            $results->put($mydate, []);
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
                     * Таблица $this->orbises
                     */
                    $orbis = 1; //$this->orbises[$np];
                    for ($c = -$orbis; $c <= $orbis; $c++) {
                        $dc = abs($d1 - $d2 + $c);
                        $dc = $dc > 180 ? 360 - $dc : $dc;
                        $aa = $mydate . $tp . $dc . $np; //для занесения в проверочный массив $asp и последующей проверки для исключения дублей
                        if (array_key_exists($aa, $asp)) {
                            $br = true;
                            break;
                        } else if (in_array($dc, $degrees)) {
                            $asp[$aa] = 1;
                            $myval = ($tp) . $dc . ($np);
                            $myvals[] = $myval;

                            $aspectSearch = $aspects->filter(function ($item) use ($tp, $dc, $np) {
                                if ($item->id_planet1 == $tp && $item->degrees == $dc && $item->id_planet2 == $np) return true;
                            });
                            $aspect = $aspectSearch->first();
                            if (isset($aspect->aspects)) {
                                $r['interpretation'] = $aspect->aspects;
                                $r['symbol'] = $this->symbols[$tp] . ' ' . $this->symorb[$dc] . ' ' . $this->symbols[$np];
                                $r['aspect'] = $this->transit[$tp] . ' ' . $this->planet1[$tp] . ' ' . $this->inorb[$dc] . ' с ' . $this->planet2[$np] . ' - <span style="font-family: fantasy; font-size: 16px;">' . $this->symbols[$tp] . ' ' . $this->symorb[$dc] . ' ' . $this->symbols[$np] . '</span><br>';
                                $rate = $aspect->rating > 100 ? 100 - $aspect->rating : $aspect->rating;
                                $r['rating'] = (abs($rate)-abs($c)<=0 ? 1 : abs($rate)-abs($c))*$rate/abs($rate);
                                $result[] = $r;
                            } else {
                                unset($aspect);
                            }
                        }
                    }
                    $results->put($mydate, collect($result));
                    if ($br) break;
                }
            }
            if ($results[$mydate]) {
            } else {
                $r['interpretation'] = '';
                $r['aspect'] = 'В этот день звезды не оказывают особого влияния на Вашу жизнь. Ни положительных, ни отрицательных аспектов не выявлено.';
                $r['rating'] = 0;
                $r['symbol'] = '';
                $results->put($mydate, collect($r));
            }
            /* @foreach ($aspects as $aspday => $aspect)
             *
             * @php
             * $ad[] = '<span style="font-size: 12px; padding: 2px 3px; background-color: #ffff00; float: left; clear: both; width: 60px; text-align: center; font-family: fantasy;">' . date("d-m-Y", strtotime($mydate)) . '</span><br>';
             * $dayline[] = 'maketext("' . date("d", strtotime($aspday)) . '", ' . $loop->iteration . ' * scale + 23, 290, "#555", "");';
             * $sumrate = array_sum(array_column($aspect, 'rating'));
             * $maxrate = max(array_column($aspect, 'rating'));
             * $minrate = min(array_column($aspect, 'rating'));
             * $maxabs = abs($maxrate) > abs($minrate) ? $maxrate : $minrate;
             * @endphp
             * {{--{ xpoint: {{ ($loop->iteration + 1)  * 50 / 2 * scale }}, y0: 150, ypoint: {{ -$sumrate * 7 }} },--}}
             * {xpoint: step + {{ $loop->iteration - 1 }} * scale + shift, y0: 150, ypoint: {{ -$sumrate * 9 }}},
             * @endforeach
             */


        }
        return $results;
    }

}
