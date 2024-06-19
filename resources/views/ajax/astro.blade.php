<h4 class="text-center pb-3">Интерпретации аспектов на
    <b>
        <span class="attention cntdate">{{ isset($cntdate) ? $cntdate : date('d.m.Y', time()) }}</span>
    </b>
</h4>
@foreach($astrogroups as $key => $ag)
    @if($ag->_gorotable == '_sex')
        <div style='width: 265px; text-align: left; background: white;' class='ourgoro shadowing'>
            <p style='margin: 10px;'>Сексуальный гороскоп предоставляется только лицам, достигшим
                18-ти
                лет.
                Просмотр гороскопа стоит 7 звезд.</p>
        </div>
    @else
        <div class='ourgoro shadowing' id='{{ $ag->_gorotable }}'
             title='Стоимость - 2 звезды'>
            <a href='?gsk={{ $gsk[$key]}}&num={{$ag->id_gorogroup}}&udt={{ isset($astroDate) ? $astroDate : date('Ymd') }}'
               class='more'>
                <b></b>
            </a>
            <div style='position: absolute; margin-top: -25px; margin-left: 75px;'>
                <img src='/images/viewed.png' width='30'/>
            </div>
        </div>
    @endif
@endforeach

