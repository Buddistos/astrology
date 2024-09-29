@isset($page)
    @dd($page)
    <title>{{ isset($page->h1) ? $page->h1 : $page->meta_title }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta charset="utf-8">

    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta itemprop="name" content="{{ isset($page->meta_title) ? $page->meta_title : env('APP_NAME')}}">
    <meta itemprop="description" content="{{ isset($page->meta_description) ? $page->meta_description : '' }}">
    <meta itemprop="image" content="{{ isset($page->smm_img) ? $page->smm_img : '' }}"/>

    <meta name="description" content="{{ isset($page->meta_description) ? $page->meta_description : '' }}"/>
    <meta name="keywords" content="{{isset($page->meta_keywords) ? $page->meta_keywords : ''}}"/>

    <meta name="copyright" content="{{ isset($page->meta_title) ? $page->meta_title : env('APP_NAME')}}">
    <meta name="author" content="{{ $sitename }}">
    <meta name="twitter:card" content="summary"/>
    <meta name="twitter:site" content="{{ $sitename }}"/>
    <meta name="twitter:title" content="{{isset($page->meta_title) ? $page->meta_title : ''}}"/>
    <meta name="twitter:description" content="{{ isset($page->meta_description) ? $page->meta_description : '' }}"/>
    <meta name="twitter:image:src" content="{{ isset($page->smm_img) ? $page->smm_img : '' }}"/>
    <meta name="twitter:domain" content="{{ $sitename.$_SERVER['REQUEST_URI'] }}"/>
    <meta property="og:type" content="website"/>
    <meta property="og:title" content="{{isset($page->meta_title) ? $page->meta_title : ''}}"/>
    <meta property="og:description" content="{{ isset($page->meta_description) ? $page->meta_description : '' }}"/>
    <meta property="og:image" content="{{ isset($page->smm_img) ? $page->smm_img : '' }}"/>
    <meta property="og:image:width" content=""/>
    <meta property="og:image:height" content=""/>
    <meta property="og:site_name" content="{{ $sitename }}"/>
    <meta property="og:url" content="{{ $sitename . $_SERVER['REQUEST_URI']}}"/>
@else
    <title>{{ $sitename }}</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta charset="utf-8">
@endif
