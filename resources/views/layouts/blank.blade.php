<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">

<head>

    <!-- Basic Page Needs
    ================================================== -->
    <title>@yield('title')</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">

    <!-- Favicon icon -->
    <link rel="shortcut icon" href="/upload/storage/favicon.ico" type="image/x-icon">

    @stack('head')

    @stack('styles')

    <link rel="stylesheet" href="/css/custom.css">

    {!! settings('in_head') !!}

</head>
<body>

{{--
@section('header')
    @include('partials.header.header')
@show
--}}


@yield('content')

{{--
@section('footer')
    @include('partials.footer.footer')
@show
--}}

    @stack('scripts')

</body>
</html>
