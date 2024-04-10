//------------------------------------------------------------------------
//						OWL CAROUSEL OPTIONS
//------------------------------------------------------------------------

$('.carousel-5item-dots').owlCarousel({
    loop: false,
    autoplay: true,
    autoplayHoverPause: true,
    margin: 0,
    nav: false,
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
        1300: {
            items: 5
        }
    }

});
