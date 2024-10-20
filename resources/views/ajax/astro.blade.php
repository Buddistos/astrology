<h4 class="text-center pb-3">Интерпретации аспектов на
    <b>
        <span class="attention cntdate">{{ isset($cntdate) ? $cntdate : date('d.m.Y', time()) }}</span>
    </b>
</h4>
@foreach($astrogroups as $key => $ag)
    @if($ag->_gorotable == '_sex')
{{--
        <div style='width: 323px; height: 100px; text-align: left; background: white;' class='ourgoro shadowing'>
            <p style='margin: 10px;'>Сексуальный гороскоп предоставляется только лицам, достигшим
                18-ти
                лет.
                Просмотр гороскопа стоит 7 звезд.</p>
        </div>
--}}
    @else
        <div class='ourgoro shadowing' id='{{ $ag->_gorotable }}'
             title='Стоимость - 2 звезды'>
            <a href='?gsk={{ $gsk[$key+1]}}&num={{$ag->id_gorogroup}}&udt={{ isset($astroDate) ? $astroDate : date('Ymd') }}'
               class='more'>
                <b></b>
            </a>
{{--
            <div style='position: absolute; margin-top: 0px; margin-left: 95px;'>
                <img src='/images/viewed.png' width='30'/>
            </div>
--}}
        </div>
    @endif
@endforeach

@isset($tga)
    <script>
        $(".more").click(function (event) {
            event.preventDefault(); // Предотвращаем стандартное поведение формы

            var href = $(this).attr('href');

            // Разбиваем href на URL и query string
            var [url, queryString] = href.split('?');

            queryString += '&' + tg.initData;
            // Парсим query string в объект
            var params = {};
            if (queryString) {
                queryString.split('&').forEach(function(param) {
                    var [key, value] = param.split('=');
                    params[key] = decodeURIComponent(value);
                });
            }
            params['_token'] = '{{ csrf_token() }}';

            /*
            .forEach(function(field) {
                params[field.name] = field.value;
            });
*/

            console.log(params);
            // Отправляем AJAX-запрос методом POST
            $.ajax({
                type: 'POST',
                url: 'astroview',
                data: params,
                success: function(data) {
                    console.log('Успешно отправлено!', data);
                    $('#userwin').html(data.html);
                },
                error: function(xhr, status, error) {
                    console.error('Ошибка отправки:', error);
                    showTelegramAlert(error);
                }
            });

            console.log(this);
        });
    </script>
@endisset
