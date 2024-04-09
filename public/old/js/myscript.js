$(function() {
    $("#datepicker").datepicker({ 
        dateFormat: "dd.mm.yy",
        monthNames: ["Январь","Февраль","Март","Апрель","Май","Июнь","Июль","Август","Сентябрь","Октябрь","Ноябрь","Декабрь"],
        firstDay: 1,
        dayNamesMin: ["Вс", "Пн", "Вт", "Ср", "Чт", "Пт", "Сб"],
        changeYear: true,
        yearRange: "1900:"
    });

    $('form#startform').submit(function(){
        $("#inform").html("<p align='center' style='color: green;'>Передача данных..</p>");
        //$(".inform").toggle();
        var s = $('form').serializeArray();
        $.post("getit.php", s, function(data){
//            $("#inform").html(data);
            $.fancybox.open([
                {
                    content: data
                },
                {
                    index: 10000
                }
            ]);
        });
        return false;
    });

    $('.ourgoro').click(function(){
        $('#' +$(this).attr('id') + '_modal').bPopup({
        easing: 'easeOutBack', //uses jQuery easing plugin
            speed: 650,
            transition: 'slideDown'
        });
     });
     $('.show_modal').click(function(){
        $('#order').bPopup({
            speed: 650,
            transition: 'slideIn'
        });
     });
     $(".carusel").jCarouselLite({
         btnNext: ".next",
         btnPrev: ".prev"
     });
     $(".fancybox").fancybox();
     $('input, select').styler({
        selectSearch: false
     });
});
