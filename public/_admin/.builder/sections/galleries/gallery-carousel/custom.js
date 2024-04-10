//------------------------------------------------------------------------
//						OWL CAROUSEL OPTIONS
//------------------------------------------------------------------------

$('.carousel-4item-dots').owlCarousel({
    loop: false,
    margin: 0,
    autoplay: true,
    autoplayHoverPause: true,
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
        }
    }

});
