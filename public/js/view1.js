$(document).ready(function () {
    console.log('view1');
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
                    location.reload();
                    //$("#userwin").html(data.html);
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
