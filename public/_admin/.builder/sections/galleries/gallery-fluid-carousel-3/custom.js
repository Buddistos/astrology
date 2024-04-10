//------------------------------------------------------------------------
//						OWL CAROUSEL OPTIONS
//------------------------------------------------------------------------

$('.carousel-3item-dots-fluid-marg').owlCarousel({
    loop: false,
    margin: 60,
    nav: false,
    autoplay: true,
    autoplayHoverPause: true,
    rewind: true,
    responsive: {
        0: {
            items: 1,
            stagePadding: 30
        },
        600: {
            items: 2,
            stagePadding: 30
        },
        1000: {
            items: 3,
            stagePadding: 60
        },
        1600: {
            items: 4,
            stagePadding: 60
        }
    }

});
