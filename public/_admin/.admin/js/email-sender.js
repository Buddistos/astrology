$(document).ready(function () {

    if ($("table").data("model") == "users" && $("input").is(".group-checkable")) {
        var button1 = '<a id="add_group" href="javascript:void(0);" class="add_group btn btn-danger btn-sm"><i class="fa fa-plus-circle"></i> Добавить выбранных в группу </a>';
        $(button1).prependTo(".actions");
        /*
                var button2 = '<a id="add_all_group" href="javascript:void(0);" class="add_group btn btn-danger btn-sm"><i class="fa fa-arrow-circle-up"></i> Добавить в группу всех</a>';
                $(button2).prependTo(".actions");
        */
        toastr.options.timeOut = 2000;

        $(".actions").on("click", ".add_group", function () {
            var group = prompt("Введите имя группы пользователей");
            if (group === null) {
                return;
            } else if (!group) {
                toastr["error"]("Необходимо ввести имя группы", "Уведомление");
                return;
            }
            var ids = [], data = [];
            $("table[data-model=users] input.checkboxes:checked").each(function () {
                ids.push($(this).data("id"));
            });
            data = {ids: ids, group: group};
            if (ids.length <= 0) {
                toastr["error"]("Необходимо выбрать хотя бы одного пользователя", "Уведомление");
                return;
            }
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: "/ajax/addemailgroup",
                data: data,
                dataType: "JSON",
                type: "POST",
                success: function (data) {
                    ajaxAnswer(data);
                }
            });
        });
    }

    if ($("div").is("#email_list") && $("button").is("[value=stay]")) {
        if ($("#email_list #status select").val() === "0") {
            var button1 =
                '<a id="send_me" href="javascript:void(0);" class="send_me btn btn-danger btn-sm" style="float: right;"><i class="fa fa-arrow-circle-o-right"></i> Отправить рассылку </a>' +
                '<span class="text-right" style="float: right; margin: 5px;">После отправки редактирование рассылки будет невозможно.</span>';
            $(button1).appendTo(".form-actions .row .col-md-12");
            $("#email_list form input").on("change keyup", function () {
                $("#send_me").next().text("Необходимо сохранить рассылку перед отправкой.");
                $("#send_me").remove();
            });

        } else {
            $("#email_list form input").prop("disabled", true);
            var button1 =
                '<a  id="send_me" href="javascript:void(0);" class="send_me btn btn-warning btn-sm" style="float: right;"><i class="fa fa-arrow-circle-o-right"></i> Повторить отправку </a>' +
                '<span class="text-right" style="float: right; margin: 5px;">Рассылка была отправлена, редактирование невозможно.</span>';
            $(button1).appendTo(".form-actions .row .col-md-12");
        }

        $("#email_list").on("click", "#send_me:not(.sent)", function () {
            $(this).attr("disabled", "disabled").addClass("sent");
            var group_id = $("#email_list form").data("module-id");
            var data = "_token=" + $("#email_list form input[name=_token]").val() + "&id=" + group_id;
            $.ajax({
                url: "/ajax/sendemailgroup",
                data: data,
                type: "POST",
                success: function (data) {
                    ajaxAnswer(data);
                }
            });
        });

    }


});
