@extends('layouts.site')

@section('css')
    <link href="{{ asset('/css/styles-tmp.css') }}" rel="stylesheet">
@endsection

@section('js')
    <script src="{{ asset('/js/common.js') }}"></script>
    @isset($view)
        <script src="{{ asset('/js/view' . $view . '.js') }}"></script>
    @elseif(!$auth)
        <script src="{{ asset('/js/auth.js') }}"></script>
    @endif
@endsection

@section('content')
    @if(isset($view))
        @include($views[$view])
    @else
        <h4 class="text-center">Добро пожаловать</h4>
        <div class="info">
            <h5>Как читать астропрогноз</h5>
            <p>В конце каждого ежедневного прогноза Вы увидите график оценки влияния планет на этот день. График
                охватывает диапазон в один месяц, начиная с выбранного дня прогноза. Таким образом, Вы сможете
                визуально оценить влияние планет на следующие 30 календарных дней.<br>
                Точки на графике отображают благоприятные возможности и неприятности, которые могут случиться. Сила
                аспектов говорит о том, насколько сильно планеты оказывают воздействие на жизнь. Можно сказать, что
                это вероятность события. Но, естественно, 100% она не может быть - ведь неизвестно, что именно может
                произойти. Например, одинаковый прогноз, обещающий обогащение для нищего и короля, одному даст корку
                хлеба, а другому мешок золота. При этом и тот, и другой сочтут прогноз сбывшимся.<br>
                И все зависит только от Вас.<br>
                Если на графике Вы видите неблагоприятный период, то следует быть предельно внимательным в аспектах,
                которые указаны в прогнозе.<br>
                Но, это всего лишь предупреждение, как прогноз погоды - если обещают дождь, то нужно взять зонт.
                Зонтом в Вашей жизни является внимательность и осознанность.<br>
                Например, Вы получили указание на день, что возможны нервные срывы, гнев и как, следствие,
                разрушение отношений с партнером. Но, если Вы осознаете каждый миг и внимательны к окружающим Вас
                мелочам, то Вас вряд ли что-то сможет вывести из себя и заставить даже чуточку приблизится к
                прогнозу. А если Вы "засыпаете" и позволяете вовлечь себя в круговорот событий, не осознавая этого
                движения, то вероятность того, что все произойдет именно так, крайне высока.<br>
                Принимайте астропрогноз как указание, чему следует уделить особое внимание в этот день, и самое
                главное УДЕЛЯЙТЕ этому внимание, тогда Вы сами не заметите, что станете осознавать каждый свой шаг и
                астропрогноз станет для Вас привычным, как и прогноз погоды, негативные аспекты перестанут быть
                пугающими, а благоприятные будут указывать верный путь для достижения ваших целей.</p>
            <p><img border="0" src="/images/b_plus.gif"> Поднимаясь выше уровня плюс 50%, график показывает наиболее
                благоприятные для Вас периоды.<br>
                <img border="0" src="/images/b_minus.gif"> График, опускаясь ниже уровня минус 50%, показывает
                наиболее напряженные, может быть, даже опасные в некотором смысле дни. </p>
            <p class="aligncenter">Пример графика:<br><img border="0" class="shadowing" src="/images/graph.jpg" width="100%"/></p>
            <p><b>Если график идет на уровне 0% - это значит, что в эти дни нет аспектов между транзитными планетами
                    и планетами Вашего гороскопа, а значит, нет и символизируемых этим аспектами влияний и событий.
                    Проще говоря, спокойный, обычный день без каких-либо особенностей... Соответственно и в тексте
                    гороскопа на этот день ничего не написано. </b></p>
            <p>Поскольку транзитные планеты движутся сравнительно медленно, "влияние" их аспектов сохраняется
                несколько дней, поэтому в описании каждого такого дня могут повторяться одни и те же события и
                рекомендации.<br>
                Не следует смущаться, что в Вашем гороскопе в один день есть и хорошие, и плохие "предсказания" -
                наша жизнь действительно разнообразна и подвержена многим различным влияниям. Как говорится, "и
                хочется, и колется"... Если указания на данный день противоречивы, они отражают противоречивость,
                сложность Вашей ситуации - и лучше всего в такой день не принимать серьезных решений. И все же, зная
                все различные возможности, Вы сможете всесторонне оценить ситуацию и решить для себя, как поступить.<br>
                Цель гороскопа - предупредить Вас о благоприятных возможностях и неприятностях, которые могут
                случиться. Но очень многое зависит от Вас, от Вашего поведения. Если предстоит плохой и даже опасный
                период, следует учесть предупреждение судьбы и не рисковать, не ввязываться в авантюры, быть
                осторожным и предусмотрительным - все пройдет и наступит хорошее время. В крайнем случае,
                неприятности будут умеренными. С другой стороны, благоприятные возможности надо использовать, иначе
                они останутся всего лишь упущенными возможностями...<br>
                В конце концов, свободная воля дана нам Творцом именно для того, чтобы мы свободно выбирали между
                Добром и Злом, учитывая и используя, по мере возможности, предупреждения и подсказки Судьбы.</p>
        </div>
        <p></p>
    @endif
@endsection
