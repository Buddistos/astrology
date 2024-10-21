@extends('layouts.blank')

@section('css')
    <link href="{{ asset('/css/styles-tmp.css') }}" rel="stylesheet">
@endsection
<style>
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    body {
        font-family: marmelad;
        /*
        color: var(--tg-theme-text-color);
        background: var(--tg-theme-bg-color);
         */
    }
    .main {
        width: 100%;
/*        text-align: center;*/
    }
    #userwin small {
        text-align: left;
    }
    .main h1 {
        font-size: 22px;
        text-align: center;
        white-space: pre;
    }
    .main h2 {
        font-size: 20px;
        text-align: left;
        white-space: pre;
    }
    .main .btn-info{
        text-align: center;
    }
    #username{
        font-size: 22px;
        white-space: pre;
        width: 270px;
        overflow: hidden;
        text-align: right;
    }
    .f-btn{
        width: 100%;
    }
</style>

@section('content')
    <div class="main">
        <h1>Персональный астропрогноз</h1>
        <button class="btn btn-info f-btn">Читать астропрогноз на сегодня</button>
        <div>
            @include('partials/tgmain')
        </div>
    </div>
@endsection

@section('js')
    <script src="https://telegram.org/js/telegram-web-app.js"></script>

    <script>
        let tg = window.Telegram.WebApp;
        $("#username").text(tg.initDataUnsafe.user.username);

        let fBtn = document.getElementsByClassName("f-btn")[0];

        function showTelegramAlert(msg) {
            console.log(msg);
            tg.showAlert(msg);
        }

        fBtn.addEventListener("click", () => {
            var data = tg.initData + '&method=tga&_token=' + '{{ csrf_token() }}';

            $.ajax({
                url: "/tga",
                type: "POST",
                data: data,
                dataType: 'json',
                success: function (data) {
                    console.log('SUCCESS');
                    //location.reload();
//            $("#authwin").html('<h5 class="float-end"><b>' + data.name + '</b></h5>');
                    $("#userwin").html(data.html);
                },
                error: function (jqXHR) {
                    console.log('Error');
                    data = jqXHR.responseText;
                    console.log((data));
                    msg = data.msg;
                    $.map(JSON.parse(data), function (message, field) {
                        msg += '<br>' + message;
                    });
                    showTelegramAlert(msg, 1);
                }
            });
        });
    </script>
@endsection
