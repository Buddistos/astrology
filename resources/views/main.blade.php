@extends('layouts.site')

@section('content')

<body>
    <div class="container">
        <h1 class="attention textshadow" style="color: white;">{{ setting('site_name')}}</h1>
        <div class="block">
            <div id="goback"><a href="/vkapp/"><img src="/images/gohome.png" width="40" /></a></div>

            <div style="margin-left: 295px; margin-top: -5px; width:180px; height: 70px; position: absolute;"><a id="minigraph" href="javascript:void(0);" onclick="opengraph();"></a></div>

            <div style="position: absolute; width: 49px; height: 50px; background: url(star.png) no-repeat; background-size: 50px; margin-top: 100px; margin-left: 15px;">
                <b style="cursor: pointer; display: block; padding: 18px 0; text-align: center;" class="stars" onclick="$.fancybox.open({content:$('#payment').html(),topRatio:0,margin:[100, 20, 20, 20],autoHeight:true,height:'auto'});" title="STAR">STAR</b>
            </div>

            <div id="goro" style="text-align: center;">{!! setting('stubtext') !!}</div>
            <div id="goro" style="clear: both; overflow: hidden;">
                <h3 class="aligncenter" style="padding-top: 20px;">Предназначено для использования в социальных сетях.</h3>
            </div>
        </div>
        <p style="font-size: 12px; color: #999; text-align: center; padding-bottom: 0;margin-bottom: 0;">(с) 2014-2015 Андрей Перье и
            <a href="http://best-horoscope.ru" target="_blank" style="font-size: 12px; text-decoration: none; color: #777;">Интернет-группа "Астроном и я"</a><br>
            Интерпретации разработаны с помощью <a href="http://astrohit.ru" style="font-size: 12px; text-decoration: none; color: #777;">команды профессиональных астрологов</a>
        </p>
    </div>
</body>
@endsection
