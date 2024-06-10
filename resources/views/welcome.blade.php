@extends('layouts.site')

@section('css')
    <link href="{{ asset('/css/styles-tmp.css') }}" rel="stylesheet">
@endsection

@section('js')
    <script src="{{ asset('/js/vkapp.js') }}"></script>
@endsection

@section('content')
    <body>
    <div class="container">
        <h1 class="textshadow text-center">{{ setting('site_name')}}</h1>
        <div class="offset-lg-3 col-lg-6 p-3" id="astrowin">
            <div class="row">
                <div class="col-6">
                    <a href="/vkapp/">
                        <img src="/images/gohome.png" width="40"/>
                    </a>
                </div>
                <div id="userwin" class="col-6">
                    @if($auth)

                    @else
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modal_auth" style="float: right">
                            Вход
                        </button>
                    @endif
                </div>
            </div>
            <div id="astrowin" class="row">

            </div>
        </div>
    </div>
    <div class="row">
        <p class="text-white text-center" style="font-size: 12px;">
            © 2014-2024 Андрей Перье и
            <a href="#" target="_blank"
               style="font-size: 12px; text-decoration: none; color: #777;">Интернет-группа
                "Астроном и
                я"</a><br>
            Интерпретации разработаны с помощью
            команды профессиональных астрологов
        </p>
    </div>

    <div class="modal" tabindex="-1" id="modal_auth">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Авторизация</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Закрыть"></button>
                </div>
                <div class="modal-body">
                    <p>Выберите основное приложение для входа. Остальные сможете привязать в личном кабинете после авторизации за дополнительное вознаграждение =)</p>
                    <h5>Телеграм:</h5>
                    <script async src="https://telegram.org/js/telegram-widget.js?22"
                            data-telegram-login="astro4me_bot" data-size="medium" data-radius="7"
                            data-onauth="onTelegramAuth(user)" data-request-access="write"></script>
                </div>
                <div class="modal-footer">
{{--
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Закрыть</button>
                    <button type="button" class="btn btn-primary">Сохранить изменения</button>
--}}
                </div>
            </div>
        </div>
    </div>
    </body>
@endsection
