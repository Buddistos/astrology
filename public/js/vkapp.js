const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]')
const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl))

function onTelegramAuth(user) {
    user['method'] = 'tga';
    $.ajax({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        url: "/ajax/auth",
        type: "POST",
        data: user,
        success: function (data) {
            $('#modal_auth').modal('toggle');
            $("#userwin").html(data.msg)
            $("#astrowin").append(data.html);

        },
        error: function (jqXHR, textStatus, errorThrown) {
            $('#modal_auth').modal('toggle');
            data = jqXHR.responseText;
            msg = 'Ошибка!';
            $.map(JSON.parse(data), function (message, field) {
                msg += '<br>' + message;
            });
            $("#userwin").html(msg);
        }
    });
}
