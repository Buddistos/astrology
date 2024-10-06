<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    @include('partials.metas')

    <link href="{{ asset('/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('/css/jquery.toast.min.css') }}" rel="stylesheet">
    <link href="{{ asset('/css/styles.css') }}" rel="stylesheet">

    <!-- Google fonts -->
    <link href='https://fonts.googleapis.com/css?family=Seymour+Display|Marmelad&subset=cyrillic' rel='stylesheet'>

    @yield('css')

    {!! setting('in_head') !!}
</head>
<body style="font-family: marmelad;">
<div class="container">
    <h1 class="textshadow text-center">{{ $sitename }}</h1>
    <div class="offset-lg-3 col-lg-6 p-3" id="astrowin">
        <div class="row">
            <div class="col-6">
                <a href="/">
                    <img src="/images/gohome.png" width="40"/>
                </a>
            </div>
            <div id="authwin" class="col-6" class="float-end">
                @if($auth)
                    <form method="post" action="{{ route('logout') }}" class="float-end flo">
                        @csrf
                        <button type="submit" class="btn btn-primary" >Выход</button>
                    </form>
                    <h5><b>{!! $client['name']  !!}</b></h5>
                @else
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                            data-bs-target="#modal_auth" style="float: right">
                        Вход
                    </button>
                @endif
            </div>
        </div>
        <div id="userwin" class="row pb-2">

            @yield('content')

        </div>
    </div>
    <div class="row mt-3">
        <p class="text-white text-center" style="font-size: 12px;">
            © 2014-2024 Андрей Перье и
            <a href="javascript:;"
               style="font-size: 12px; text-decoration: none; color: #777;">Интернет-группа
                "Астролог и
                я"</a><br>
            Интерпретации разработаны с помощью
            команды профессиональных астрологов
        </p>
    </div>
</div>
{{ !$auth ? view('modal.auth') : '' }}
</body>


<script src="{{ asset('/js/bootstrap.min.js') }}"></script>
<script src="{{ asset('/js/jquery.min.js') }}"></script>
<script src="{{ asset('/js/jquery.toast.min.js') }}"></script>
<script src="{{ asset('/js/d3.v7.min.js') }}"></script>
<script> var _token = '{{ csrf_token() }}'; </script>

@yield('js')

{!! setting('in_body') !!}

</body>
</html>
