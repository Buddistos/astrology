<p>
    Для успешного расчета астропрогнозов не хватает данных. Пожалуйста, заполните поля ниже.
</p>
<form method="POST" id="addForm" action="/ajax/profilechange">
    <div class="row g-1 offset-md-1 col-lg-10 col-md-10">
        <div class="col-5">
            <span>Часовой пояс:</span>
        </div>
        <div class="col-7">
            @if(isset($client['utc']))
                <span>{{ $client['utc'] }}</span>
            @else
                <select class="form-select pt-0 pb-0" name="utc">
                    <option value="-12:00">(GMT -12:00) Эневеток, Кваджалейн</option>
                    <option value="-11:00">(GMT -11:00) Остров Мидуэй, Самоа</option>
                    <option value="-10:00">(GMT -10:00) Гавайи</option>
                    <option value="-09:00">(GMT -9:00) Аляска</option>
                    <option value="-08:00">(GMT -8:00) Тихоокеанское время (США и Канада), Тихуана</option>
                    <option value="-07:00">(GMT -7:00) Горное время (США и Канада), Аризона</option>
                    <option value="-06:00">(GMT -6:00) Центральное время (США и Канада), Мехико</option>
                    <option value="-05:00">(GMT -5:00) Восточное время (США и Канада), Богота, Лима</option>
                    {{--                        <option value="-04:30">(GMT -4:30) Каракас</option>--}}
                    <option value="-04:00">(GMT -4:00) Атлантическое время (Канада), Ла Пас</option>
                    {{--                        <option value="-03:30">(GMT -3:30) Ньюфаундленд</option>--}}
                    <option value="-03:00">(GMT -3:00) Бразилия, Буэнос-Айрес, Джорджтаун</option>
                    <option value="-02:00">(GMT -2:00) Среднеатлантическое время</option>
                    <option value="-01:00">(GMT -1:00) Азорские острова, острова Зелёного Мыса</option>
                    <option value="+00:00">(GMT) Дублин, Лондон, Лиссабон, Касабланка, Эдинбург</option>
                    <option value="+01:00">(GMT +1:00) Брюссель, Копенгаген, Мадрид, Париж, Берлин</option>
                    <option value="+02:00">(GMT +2:00) Афины, Киев, Минск, Бухарест, Рига, Таллин</option>
                    <option value="+03:00" selected>(GMT +3:00) Москва, Санкт-Петербург, Волгоград</option>
                    {{--                        <option value="+03:30">(GMT +3:30) Тегеран</option>--}}
                    <option value="+04:00">(GMT +4:00) Абу-Даби, Баку, Тбилиси, Ереван</option>
                    {{--                        <option value="+04:30">(GMT +4:30) Кабул</option>--}}
                    <option value="+05:00">(GMT +5:00) Екатеринбург, Исламабад, Карачи, Ташкент</option>
                    {{--                        <option value="+05:30">(GMT +5:30) Мумбай, Колката, Ченнаи, Нью-Дели</option>--}}
                    {{--                        <option value="+05:45">(GMT +5:45) Катманду</option>--}}
                    <option value="+06:00">(GMT +6:00) Омск, Новосибирск, Алма-Ата, Астана</option>
                    {{--                        <option value="+06:30">(GMT +6:30) Янгон, Кокосовые острова</option>--}}
                    <option value="+07:00">(GMT +7:00) Красноярск, Норильск, Бангкок, Ханой, Джакарта</option>
                    <option value="+08:00">(GMT +8:00) Иркутск, Пекин, Перт, Сингапур, Гонконг</option>
                    <option value="+09:00">(GMT +9:00) Якутск, Токио, Сеул, Осака, Саппоро</option>
                    {{--                        <option value="+09:30">(GMT +9:30) Аделаида, Дарвин</option>--}}
                    <option value="+10:00">(GMT +10:00) Владивосток, Восточная Австралия, Гуам</option>
                    <option value="+11:00">(GMT +11:00) Магадан, Сахалин, Соломоновы Острова</option>
                    <option value="+12:00">(GMT +12:00) Камчатка, Окленд, Уэллингтон, Фиджи</option>
                </select>
                </select>
            @endif
        </div>
        <small>Для расчета важен часовой пояс места, где Вы родились. Если Вашего населенного пункта нет,
            выберите
            города из списка, соответствующие Вашему часовому поясу.</small>
        <div class="col-5">
            <span>Дата рождения:</span>
        </div>
        <div class="col-7">
            @if(isset($client['birthday']))
                <span>{{ $client['birthday'] }}</span>
            @else
                <input type="date" class="form-control p-0" name="birthday" required>
            @endif
        </div>
        <div class="col-5">
            <span>Время рождения:</span>
        </div>
        <div class="col-7">
            @if(isset($client['birthtime']))
                <span>{{ $client['birthtime'] }}</span>
            @else
                <input type="time" class="form-control p-0" name="birthtime" required>
            @endif
        </div>
        <small>
            Составления аспектов напрямую связано с датой и временем Вашего рождения. Планеты образуют углы
            от
            своего
            положения, которое было зафиксировано в момент Вашего рождения.
        </small>
        <div class="col-md-12">
            <span>&nbsp;</span>
        </div>
        <div class="col-12">
            <p>После отправки данных изменить их будет возможно только через две недели. Нажимая кнопку
                <b>Подтвердить</b>
                Вы соглашаетесь с Правилами пользования.</p>

            <div class="text-center">
                <button type="submit" class="btn btn-primary" name="dataSaveBtn">
                    Подтвердить
                </button>
            </div>
        </div>
    </div>
    @isset($tga)
        <input type="hidden" name="_token" value="{{ csrf_token() }}">
    @endisset
</form>
@isset($tga)
    <script>
        $('#addForm').submit(function (event) {
            event.preventDefault(); // Предотвращаем стандартное поведение формы

            // Сохраняем данные формы в переменной
            var formData = $(this).serialize() + "&" + tg.initData;
            $.ajax({
                type: 'POST', // Метод отправки
                url: '/addfields', // URL для обработки данных на сервере
                data: formData, // Данные формы
                success: function (data) {
                    $("#userwin").html(data.html);
                },
                error: function (xhr, status, error) {
                    // Обработка ошибок
                    console.error('Ошибка при отправке формы: ' + error);
                    showTelegramAlert('Ошибка авторизации');
                }
            });
        });
    </script>
@endisset
