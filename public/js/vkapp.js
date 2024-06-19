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
            messageAlert(data.msg)
            $("#authwin").html('<h5 class="float-end"><b>' + data.name + '</b></h5>');
            $("#userwin").html(data.html);
        },
        error: function (jqXHR, textStatus, errorThrown) {
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

function messageAlert(msg, type = 0, hide = 3000) {
    if (type == 1) {
        $.toast({
            heading: 'Error',
            text: msg,
            showHideTransition: 'fade',
            position: 'top-left',
            icon: 'error',
            hideAfter: hide
        })
    } else {
        $.toast({
            text: msg,
            showHideTransition: 'slide',
            position: 'top-left',
            icon: 'info'
        })
    }
}

$(document).ready(function () {
    $("#addForm").submit(function(e) {
        e.preventDefault(); // avoid to execute the actual submit of the form.
        var form_query = $(this).serialize();
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            type: 'POST',
            url: '/ajax/profilechange',
            data: form_query, // serializes the form's elements.
            success: function (data) {
                if (data.err) {
                    messageAlert(data.msg, 1, 5000);
                } else {
                    messageAlert(data.msg);
                    $("#userwin").html(data.html);
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                data = jqXHR.responseText;
                msg = data.msg;
                $.map(JSON.parse(data), function (message, field) {
                    msg += '<br>' + message;
                });
                messageAlert(data.msg, 1, false);
            }
        });
    });
});
