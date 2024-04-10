//------------------------------------------------------------------------
//						OWL CAROUSEL OPTIONS
//------------------------------------------------------------------------

$('.carousel-2item-dots').owlCarousel({
    loop: false,
    margin: 40,
    autoplay: true,
    autoplayHoverPause: true,
    nav: false,
    rewind: true,
    responsive: {
        0: {
            items: 1
        },
        1000: {
            items: 2
        }
    }

});
