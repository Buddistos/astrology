$(document).ready(function () {
    var _token = $('input[name=_token]').val();
    var successLabel = "<span class=\"label label-sm label-success\"> Решено </span>";


    $('input.siteSubDomen').change(function () {
        if ($(this).prop('checked')) {
            $('div.siteSubDomen').css('display', 'inline-block');
        } else {
            $('div.siteSubDomen').css('display', 'none');
        }
    });

    var subDomen = 0;
    $('input[name=siteSubDomen]').click(function () {
        if (subDomen === 0) {
            subDomen = 1;
        } else {
            subDomen = 0;
        }
    });


    $('#start_search').click(function () {

        var _this = $(this);

        var url = $('input.this_site').val();

        $.ajax({
            url: '/startBrokenLinks',
            type: 'post',
            data: {_token: _token, subDomen: subDomen, url: url},
            dataType: 'json',
            beforeSend: function () {
                _this.prop('disabled', true);
            },
            success: function (response) {
                if (response.status === 'false') {
                    toastr['error']("Процесс поиска по адресу " + response.url + " уже запущен", "Дождитесь окончания процесса");
                } else {
                    location.reload();
                }
            }
        });

    });


    $('.problem_is_solved').click(function () {
        var id = $(this).data('id');

        var _this = $(this);
        var thisHtml = _this.html();
        var parentTd = _this.parents('.problem_is_solved_td');

        $.ajax({
            url: '/brokenlink_is_solved',
            type: 'post',
            data: {_token: _token, id: id},
            success: function (response) {
                if (response === 'true') {
                    parentTd.html(successLabel);
                }
            }
        });
    });


    $('.question-tooltip').tooltip({
        placement: 'right',
        title: 'При выборе данной настройки - к адресу сайта будет подставлятся поддомен города (например - https://moscow.example.com), а адрес без поддомена будет игнорироваться',
        template: '<div class="tooltip" role="tooltip"><div class="tooltip-arrow"></div><div class="tooltip-inner">3333</div></div>'
    });

    //удаление информации о битых ссылках
    $(".deleteBrokenLinks").on("click",function() {
        var urlid = $(this).data("urlid");
        $.ajax({
            url:"/ajax/deleteBrokenLinks",
            type: "POST",
            data: {_token: _token, urlid: urlid},
            success: $(this).parent().parent().addClass("hidden"),
        })
    });

});