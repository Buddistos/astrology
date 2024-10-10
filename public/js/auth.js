function onTelegramAuth(user) {
    user['method'] = 'tga';
    $.ajax({
        headers: {
            'X-CSRF-TOKEN': token
        },
        url: "/ajax/auth",
        type: "POST",
        data: user,
        dataType: 'json',
        success: function (data) {
            $('#modal_auth').modal('toggle');
            messageAlert(data.msg)
            location.reload();
//            $("#authwin").html('<h5 class="float-end"><b>' + data.name + '</b></h5>');
//            $("#userwin").html(data.html);
        },
        error: function (jqXHR) {
            $('#modal_auth').modal('toggle');
            data = jqXHR.responseText;
            msg = data.msg;
            $.map(JSON.parse(data), function (message, field) {
                msg += '<br>' + message;
            });
            messageAlert(msg, 1);
        }
    });
}
