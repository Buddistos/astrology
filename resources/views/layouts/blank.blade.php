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
<body>
<div class="container mt-2 mb-2">
    <div class="offset-lg-3 col-lg-6 p-3" id="astrowin">
        <div class="header clearfix mb-1">
            <a href="/astro" class="float-start">
                <img src="/images/gohome.png" width="40"/>
            </a>
            <b id="username" class="float-end"></b>
        </div>
        <div id="userwin" class="row pb-2">

            @yield('content')

        </div>
    </div>
</div>
</body>


<script src="{{ asset('/js/bootstrap.min.js') }}"></script>
<script src="{{ asset('/js/jquery.min.js') }}"></script>
<script src="{{ asset('/js/jquery.toast.min.js') }}"></script>
<script src="{{ asset('/js/d3.v7.min.js') }}"></script>

@yield('js')

{!! setting('in_body') !!}

</body>
</html>
