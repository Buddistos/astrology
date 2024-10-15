@if(!$aspects[$fordate]->isEmpty())
    @foreach($aspects[$fordate] as $aspect)
        <div class="mb-2">
            <img src="/images/{{ ($aspect['rating'] > 0 ? 'b_blu' : 'b_red').abs($aspect['rating']) }}.gif"
                 style="float: left; margin-top: 7px;" align="left" alt="O">
            <b>{!! $aspect['aspect'] !!}</b>
            {{$aspect['interpretation']}}
        </div>
    @endforeach
@else
    <div class="mb-2">
        <b>В этот день звезды не оказывают особого влияния на Вашу жизнь. Ни положительных, ни отрицательных
            аспектов не выявлено.</b>
    </div>
@endif

<h6>График оценки влияния планет с {{$sd}} по {{$ed}}</h6>
<div id='graph' class="flex-fill mb-3" style='height: 320px; width: 620px; margin-right: 10px'>
    <svg class='rate shadowing'
         style='height: 100%; width: 100%; background: white; border-radius: 10px;padding: 0;'
         xmlns='http://www.w3.org/2000/svg' version='1.1'
         xmlns:xlink='http://www.w3.org/1999/xlink'>
    </svg>
</div>

<div class='info'>
    <p>
        <b>Обозначения</b><br>
        Для наглядности благоприятные указания помечены синими черточками перед текстом, а неблагоприятные -
        красными
        черточками. Их длина соответствует силе влияния этого аспекта.
    </p>
    <p>
        <table border='0' width='100%'>
            <tbody>
            <tr>
                <td>
    <p><img border='0' src='/images/b_blu1.gif'> 1 Очень слабое позитивное указание <br>
        <img border='0' src='/images/b_blu2.gif'> 2 Слабое позитивное указание <br>
        <img border='0' src='/images/b_blu3.gif'> 3 Позитивное указание средней силы <br>
        <img border='0' src='/images/b_blu4.gif'> 4 Сильное позитивное указание <br>
        <img border='0' src='/images/b_blu5.gif'> 5 Очень сильное позитивное указание </p>
    </td>
    <td>
        <p><img border='0' src='/images/b_red1.gif'> -1 Очень слабое негативное указание <br>
            <img border='0' src='/images/b_red2.gif'> -2 Слабое негативное указание <br>
            <img border='0' src='/images/b_red3.gif'> -3 Негативное указание средней силы <br>
            <img border='0' src='/images/b_red4.gif'> -4 Сильное негативное указание <br>
            <img border='0' src='/images/b_red5.gif'> -5 Очень сильное негативное указание </p>
    </td>
    </tr> </tbody></table>
    </p>
</div>

<div class="modal" tabindex="-1" id="astroGo">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Смотреть астропрогноз на <span class="astroday"></span>?</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Закрыть"></button>
            </div>
            <div class="modal-body">
{{--                <p>Если Вы еще не смотрели этот день, то это будет стоить Вам одну звезду.</p>--}}
                <p>Просмотр других дней временно не доступен.</p>

            </div>
{{--
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Закрыть</button>
                <button type="button" class="btn btn-primary">Смотреть</button>
            </div>
--}}
        </div>
    </div>
</div>

<script>
    /**
     * v. 0.1.29/09
     **/
    $(document).ready(function () {
        var scl = 1.0;
        var width = $("#graph").width();
        var height = $("#graph").height();
        var step = 10 * scl;
        var shift = 40;

        const modal = new bootstrap.Modal('#astroGo');

        function makeline(a, f, s, sw, i) {
            svg.data([a])
                .append('svg:path')
                .attr('d', line)
                .attr('fill', f)
                .attr('stroke', s)
                .attr('stroke-width', sw);
        }

        function maketext(t, x, y, c, s) {
            svg.append('text')
                .text(t)
                .attr('x', x * scl)
                .attr('y', y * scl)
                .attr('fill', c)
                .attr('style', s ? s : 'font-size: 10px;');
        }

        function makehtml(t, x, y, c, s) {
            svg.append('foreignObject')
                .html(t)
                .attr('x', x * scl)
                .attr('y', y * scl)
                .attr('width', 90)
                .attr('height', 200);
        }

        var svg = d3.select('.rate');

        var line = d3.line()
            .x(function (d) {
                return d[0] * scl;
            })
            .y(function (d) {
                return d[1] * scl + 150 * scl;
            })
            .curve(d3.curveBasis);

        var rate = [];
        var aspect = [];
        var daygoro = [];

        /**
         * Отрисовка фона графика
         */
        makeline([[step + shift, -65], [width - shift / 4, -65]], 'transparent', '#ccc', 0.5, '');
        makeline([[step + shift, 65], [width - shift / 4, 65]], 'transparent', '#ccc', 0.5, '');
        makeline([[step + shift, -130], [width - shift / 4, -130]], 'transparent', '#ccc', 0.5, '');
        makeline([[step + shift, 130], [width - shift / 4, 130]], 'transparent', '#ccc', 0.5, '');
        makeline([[step + shift, -150], [step + shift, 150]], 'transparent', '#777', 1, '');
        //makeline([[width - shift / 4, -150], [width - shift / 4, 150]], 'transparent', '#777', 1, '');
        //makeline([[step, 0], [width-13, 0]], 'transparent', '#777', 1, '');

        scale = width / 30;
        for (var i = 1; i < 30; i++) {
            makeline([[step + i * scale + shift, 130], [step + i * scale + shift, -130]], '#FFF', '#ccc', 0.3, '');
            {{--
                if (i == {{date("d", strtotime($fordate))}}) {
                    vline = makeline([[i * scale, -130], [i * scale, 130]], '', '#111', 2)
                }
            --}}
        }

        var points = [
                @foreach ($aspects as $aspday => $aspect)
                @php
                    $ad = '<p class="aspday"><b>' . date("d-m-Y", strtotime($aspday)) . '</b><br>';
                    if($aspect->isEmpty()){
                        $ad .= '<span style="font-size: 10px;">Нет аспектов</span>';
                    }else{
                        foreach($aspect as $aspone){
                            $rcolor = $aspone['rating'] > 0 ? 'green' : ($aspone['rating'] <0 ? 'red': 'gray');
                            $ad .= '<span style="clear: both; float: left; color: ' . $rcolor . '">' . $aspone['rating'] . '</span><strong style="float: right;">' . $aspone['symbol'] . '</strong>';
                        }
                    }
                    $aspectday[] = $ad . "</p>";
                    $dayline[] = 'maketext("' . date("d", strtotime($aspday)) . '", ' . $loop->iteration . ' * scale + 23, 290, "#555", "");';
                    /**
                     *  130 - координата Y - минимальная для отрицательного аспекта, Y - максимальная для положительного
                    **/
                    $max = abs($maxsumrating) > abs($minsumrating)? $maxsumrating: $minsumrating;
                    $ypoint = -130 * $aspect->sum / $max;
                @endphp
                {{--{ xpoint: {{ ($loop->iteration + 1)  * 50 / 2 * scale }}, y0: 150, ypoint: {{ -$sumrate * 7 }} },--}}
            {
                xpoint: step + {{ $loop->iteration - 1 }} * scale + shift,
                y0: 150,
                ypoint: {{ $ypoint }},
                index: {{ $loop->iteration - 1 }},
                url: '{!! $aspect->astrourl !!}',
                aspect: '{!! $ad !!}',
                astroday: '{{ $aspday }}'
            },
            @endforeach
        ];
        var area = d3.area()
            .x((p) => p.xpoint)
            .y0((p) => p.y0)
            .y1((p) => p.ypoint + 150)
            .curve(d3.curveCardinal.tension(0));

        svg.append("path")
            .datum(points)
            .attr("class", "area")
            .attr("d", area)
            .attr('fill', '#ccc')
            .attr('stroke', '#aaa')
            .attr('stroke-width', 1);

        var online = 0;
        svg.selectAll("circle")
            .data(points)
            .enter().append("circle")
            .attr("class", "little")
            .attr('fill', function (d, i) {
                return d.ypoint <= 0 ? 'green' : 'red'
            })
            .attr('stroke', '#aaa')
            .attr('cx', function (d, i) {
                return d.xpoint * scl;
            })
            .attr('cy', function (d, i) {
                return d.ypoint * scl + 150 * scl
            })
            .attr("r", 7 * scl)
            .attr('style', 'cursor: pointer;')
            .on('mouseenter', function (func, data) {
                var index = data.index;
                if (online) {
                    $('#graph svg path:last').remove();
                    $('#graph svg *:last-child').remove();
                }

                circle = d3.select(this);
                cx = circle.attr('cx');
                cy = circle.attr('cy');
                makeline([[cx, 130], [cx, data.ypoint + 7]], 'red', '#333', 1.5, '');
                var myxplus = Number(cx) + 10;
                var myxminus = Number(cx) - 80;
                makehtml(data.aspect, index <= 15 ? myxplus : myxminus, cy > 150 ? cy - data.aspect.split('<br>').length * 30 : cy, '#000', '');
                online = 1;
            })
            .on('mouseleave', function (func, data) {
                if (online) {
                    $('#graph svg path:last').remove();
                    $('#graph svg *:last-child').remove();
                    online = 0;
                }
            })
            .on('click', function (func, data) {
                $("#astroGo .astroday").text(data.astroday);
                modal.show();
                $("#astroGo button.btn-primary").click(function () {
                    location.href = "?" + data.url;
                });
                /*
                                    $.fancybox.open('<p class=\'aligncenter\' style=\'font-size: 14px;\'>Вы хотите посмотреть астропрогноз на ' + daygoro[i][0] + '?<br><br><a href=\'?' + daygoro[i][1] + '\' style=\'text-decoration: none;\'><b style=\'font-size: 20px; text-decoration: none;\'>Да</b></a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href=\'javascript:void(0);\' onclick=\'$.fancybox.close(); opengraph();\' style=\'font-size: 20px;text-decoration: none;\'>нет</a></p><p class=\'aligncenter\'><br>Если Вы еще не смотрели этот день, то это будет стоить Вам одну звезду.</p>', {
                                        topRatio: 0,
                                        margin: [100, 0, 0, 0]
                                    });
                */
                return true;
            });
        maketext('100%', 5, 30, '#555', '');
        maketext('50%', 10, 90, '#555', '');
        maketext('0%', 15, 155, '#555', '');
        maketext('-50%', 5, 220, '#555', '');
        maketext('-100%', 0, 285, '#555', '');

        maketext('{{ $aspects->keys() ->first()}}', shift + step + 2, 15, '#555', '');
        maketext('{{ $aspects->keys()->last() }}', width - 60, 15, '#555', '');

        {!! implode($dayline, "\n") !!}
    });

</script>
