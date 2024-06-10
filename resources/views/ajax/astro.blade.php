<h4 class="text-center pb-3">Интерпретации аспектов на
    <b>
        <span class="attention cntdate">{{ $cntdate }}</span>
    </b>
</h4>
@if(isset($bdate) && isset($btime) || 1)
    @foreach($astrogroups as $key => $ag)
        {{--
                        $sexhtml .= "<div class='ourgoro shadowing' id='$sgn' title='Стоимость - 7 звезд'>
                            <a href='?gsk=".$cgsk[$i]."&uid=$gid&udt=$udt' class='more'>
                                <b></b>
                            </a>";
                            if(in_array($cgsk[$i], $viewed)){
                                $sexhtml .= "<div style='position: absolute; margin-top: -25px; margin-left: 75px;'><img src='viewed.png' width='30' /></div>";
                            }
                            $sexhtml .= "</div>
        --}}
        @if($ag->_gorotable == '_sex' && 0)
            <div style='width: 265px; text-align: left; background: white;' class='ourgoro shadowing'>
                <p style='margin: 10px;'>Сексуальный гороскоп предоставляется только лицам, достигшим
                    18-ти
                    лет.
                    Просмотр гороскопа стоит 7 звезд.<br><br>
                    Проверьте также гороскоп сексуальности у друга или подруги для удачного планирования
                    взаимоотношений ;)</p>
            </div>
        @else
            <div class='ourgoro shadowing' id='{{ $ag->_gorotable }}'
                 title='Стоимость - 2 звезды'>
                <a href='?gsk={{$cgsk[$key]}}&udt={{$udt}}' class='more'>
                    <b></b>
                </a>
                <div style='position: absolute; margin-top: -25px; margin-left: 75px;'>
                    <img src='/images/viewed.png' width='30'/>
                </div>
            </div>
        @endif
    @endforeach
@else
    <p>
        Введите дату и время Вашего рождения. Без этих данных предоставления
        астропрогнозов невозможно.<br>
        <b>Дата и время вводятся один раз и привязываются к Вашей учетной записи.<br>
            Вводите корректные данные - изменить их возможно только по запросу в
            поддержку приложения.</b>
    </p>
@endif
