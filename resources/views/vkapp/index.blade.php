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
                <div>
                    <a href="/vkapp/">
                        <img src="/images/gohome.png" width="40"/>
                    </a>
                </div>
{{--
                <div style="width: 50px; height: 50px; background: url({{ asset('/images/vkapp/star.png')}}) no-repeat; background-size: 50px; margin-left: 15px;">
                    <b style="cursor: pointer; display: block; padding: 18px 0; text-align: center; font-size: 12px;" class="stars"
                       onclick="$.fancybox.open({content:$('#1payment').html(),topRatio:0,margin:[100, 20, 20, 20],autoHeight:true,height:'auto'});"
                       title="STAR">{!! $vkstars !!}</b>
                </div>
--}}
                <div>
                    <a id="minigraph" href="javascript:void(0);" onclick="opengraph();">OPENGRAPH</a>
                </div>
            </div>
            <div class="row">
                <div class="col-3">
                    <img src="{{ $photo_100 }}" class="shadowing" width="100" height="100"/>
                </div>
                <div class="row col-9">
                    <div class="col-6">
                        <span>Имя:</span>
                    </div>
                    <div class="col-6">
                        <span>{{ $uname }} ({{$vkuid}})</span>
                    </div>

                    <div class="col-6">
                        <span>Город рождения:</span>
                    </div>
                    <div class="col-6">
                        @if($bcity)
                            <span>{{ $bcity }}</span>
                        @else
                            <input type="date" class="form-control p-0" id="bcity">
                        @endif
                    </div>

                    <div class="col-6">
                        <span>Дата рождения:</span>
                    </div>
                    <div class="col-6">
                        @if($bdate)
                            <span>{{ $bdate }}</span>
                        @else
                            <input type="date" class="form-control p-0" id="bdate">
                        @endif
                    </div>

                    <div class="col-6">
                        <span>Время рождения:</span>
                    </div>
                    <div class="col-6">
                        @if($btime)
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
            <div class="row" id="main">
                @include('ajax.index')
{{--
                <div class="spinner-border text-warning float" role="status">
                    <span class="visually-hidden">Загрузка...</span>
                </div>
--}}
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
    </div>
    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modal">
        Запустите демо модального окна
    </button>
    <div class="modal" tabindex="-1" id="modal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Заголовок модального окна</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Закрыть"></button>
                </div>
                <div class="modal-body">
                    <p>Здесь идет основной текст модального окна</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Закрыть</button>
                    <button type="button" class="btn btn-primary">Сохранить изменения</button>
                </div>
            </div>
        </div>
    </div>

    </body>
@endsection
