<div>
    <p>
        Для успешного расчета астропрогнозов не хватает данных.<br>
        Пожалуйста, заполните поля ниже.
    </p>
    <div class="row  offset-md-2 col-8">
        <div class="col-6">
            <span
                data-bs-toggle="tooltip" data-bs-placement="top"
                data-bs-custom-class="custom-tooltip"
                data-bs-title="This top tooltip is themed via CSS variables.">
                Город рождения:</span>
        </div>
        <div class="col-6">
            <select class="form-control p-0" id="btimezone">
                <option value="-12:00">(GMT -12:00) Эневеток, Кваджалейн</option>
                <option value="-11:00">(GMT -11:00) Остров Мидуэй, Самоа</option>
                <option value="-10:00">(GMT -10:00) Гавайи</option>
                <option value="-09:00">(GMT -9:00) Аляска</option>
                <option value="-08:00">(GMT -8:00) Тихоокеанское время (США и Канада), Тихуана</option>
                <option value="-07:00">(GMT -7:00) Горное время (США и Канада), Аризона</option>
                <option value="-06:00">(GMT -6:00) Центральное время (США и Канада), Мехико</option>
                <option value="-05:00">(GMT -5:00) Восточное время (США и Канада), Богота, Лима</option>
                <option value="-04:30">(GMT -4:30) Каракас</option>
                <option value="-04:00">(GMT -4:00) Атлантическое время (Канада), Ла Пас</option>
                <option value="-03:30">(GMT -3:30) Ньюфаундленд</option>
                <option value="-03:00">(GMT -3:00) Бразилия, Буэнос-Айрес, Джорджтаун</option>
                <option value="-02:00">(GMT -2:00) Среднеатлантическое время</option>
                <option value="-01:00">(GMT -1:00) Азорские острова, острова Зелёного Мыса</option>
                <option value="+00:00">(GMT) Дублин, Лондон, Лиссабон, Касабланка, Эдинбург</option>
                <option value="+01:00">(GMT +1:00) Брюссель, Копенгаген, Мадрид, Париж, Берлин</option>
                <option value="+02:00">(GMT +2:00) Афины, Киев, Минск, Бухарест, Рига, Таллин</option>
                <option value="+03:00" selected>(GMT +3:00) Москва, Санкт-Петербург, Волгоград</option>
                <option value="+03:30">(GMT +3:30) Тегеран</option>
                <option value="+04:00">(GMT +4:00) Абу-Даби, Баку, Тбилиси, Ереван</option>
                <option value="+04:30">(GMT +4:30) Кабул</option>
                <option value="+05:00">(GMT +5:00) Екатеринбург, Исламабад, Карачи, Ташкент</option>
                <option value="+05:30">(GMT +5:30) Мумбай, Колката, Ченнаи, Нью-Дели</option>
                <option value="+05:45">(GMT +5:45) Катманду</option>
                <option value="+06:00">(GMT +6:00) Омск, Новосибирск, Алма-Ата, Астана</option>
                <option value="+06:30">(GMT +6:30) Янгон, Кокосовые острова</option>
                <option value="+07:00">(GMT +7:00) Красноярск, Норильск, Бангкок, Ханой, Джакарта</option>
                <option value="+08:00">(GMT +8:00) Иркутск, Пекин, Перт, Сингапур, Гонконг</option>
                <option value="+09:00">(GMT +9:00) Якутск, Токио, Сеул, Осака, Саппоро</option>
                <option value="+09:30">(GMT +9:30) Аделаида, Дарвин</option>
                <option value="+10:00">(GMT +10:00) Владивосток, Восточная Австралия, Гуам</option>
                <option value="+11:00">(GMT +11:00) Магадан, Сахалин, Соломоновы Острова</option>
                <option value="+12:00">(GMT +12:00) Камчатка, Окленд, Уэллингтон, Фиджи</option>
            </select><br>';
            </select>
        </div>
        <small>Для расчета важен часовой пояс места, где Вы родились. Если Вашего населенного пункта нет, выберите
            ближайший из списка, соответствующий </small>
        <div class="col-6">
            <span>Дата рождения:</span>
        </div>
        <div class="col-6">
            @if(isset($bdate))
                <span>{{ $bdate }}</span>
            @else
                <input type="date" class="form-control p-0" id="bdate">
            @endif
        </div>

        <div class="col-6">
            <span>Время рождения:</span>
        </div>
        <div class="col-6">
            @if(isset($btime))
                <span>{{ $btime }}</span>
            @else
                <input type="time" class="form-control p-0" id="btime">
            @endif
        </div>
        <div class="col-md-12">
            <span>&nbsp;</span>
        </div>
        <div class="col-md-12">
            <span>&nbsp;</span>
        </div>
    </div>
</div>

