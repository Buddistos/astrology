<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    @include('partials.metas')
    <link href="{{ mix('/css/styles.css') }}" rel="stylesheet">

    {!! setting('in_head') !!}
</head>
<body>

@yield('content')

<link href="{{ mix('/js/scripts.js') }}" rel="stylesheet">

{!! setting('in_body') !!}

</body>
</html>
