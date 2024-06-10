@isset($page)
    <title>{{ isset($page->h1) ? $page->h1 : $page->meta_title }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta charset="utf-8">

    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta itemprop="name" content="{{ $page->meta_title ?? env('APP_NAME')}}">
    <meta itemprop="description" content="{{ $page->meta_description ?? '' }}">
    <meta itemprop="image" content="{{ $page->smm_img ?? '' }}"/>

    <meta name="description" content="{{ $page->meta_description ?? '' }}"/>
    <meta name="keywords" content="{{$page->meta_keywords ?? ''}}"/>

    <meta name="copyright" content="{{ $page->meta_title ?? env('APP_NAME')}}">
    <meta name="author" content="{{ setting('site_name') }}">
    <meta name="twitter:card" content="summary"/>
    <meta name="twitter:site" content="{{ setting('site_name') }}"/>
    <meta name="twitter:title" content="{{$page->meta_title ?? ''}}"/>
    <meta name="twitter:description" content="{{ $page->meta_description ?? '' }}"/>
    <meta name="twitter:image:src" content="{{ $page->smm_img ?? '' }}"/>
    <meta name="twitter:domain" content="{{ setting('site_name').$_SERVER['REQUEST_URI'] }}"/>
    <meta property="og:type" content="website"/>
    <meta property="og:title" content="{{$page->meta_title ?? ''}}"/>
    <meta property="og:description" content="{{ $page->meta_description ?? '' }}"/>
    <meta property="og:image" content="{{ $page->smm_img ?? '' }}"/>
    <meta property="og:image:width" content=""/>
    <meta property="og:image:height" content=""/>
    <meta property="og:site_name" content="{{ setting('site_name') }}"/>
    <meta property="og:url" content="{{ setting('site_name') . $_SERVER['REQUEST_URI']}}"/>
@else
    <title>{{ setting('site_name') }}</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta charset="utf-8">
@endif
