//------------------------------------------------------------------------
//						OWL CAROUSEL OPTIONS
//------------------------------------------------------------------------

$('.carousel-4item-dots-fluid').owlCarousel({
    loop: false,
    margin: 0,
    nav: false,
    autoplay: true,
    autoplayHoverPause: true,
    rewind: true,
    responsive: {
        0: {
            items: 1
        },
        600: {
            items: 3
        },
        1000: {
            items: 4
        },
        1600: {
            items: 5
        }
    }

});
