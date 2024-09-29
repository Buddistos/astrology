
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
