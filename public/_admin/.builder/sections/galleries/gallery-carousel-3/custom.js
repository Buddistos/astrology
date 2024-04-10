//------------------------------------------------------------------------
//						OWL CAROUSEL OPTIONS
//------------------------------------------------------------------------

$('.carousel-3item-nav-marg').owlCarousel({
    loop: false,
    margin: 120,
    nav: true,
    dots: false,
    navText: ['',''],
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
            stagePadding: 118
        },
        1600: {
            items: 4,
            stagePadding: 118
        }
    }

});
