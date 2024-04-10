//------------------------------------------------------------------------
//						OWL CAROUSEL OPTIONS
//------------------------------------------------------------------------

$('.carousel-3item-dotseach-fluid-marg').owlCarousel({
    loop: false,
    margin: 30,
    nav: false,
    dotsEach: true,
    autoplay: true,
    autoplayHoverPause: true,
    rewind: true,
    startPosition:1,
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
            stagePadding: 150
        },
        1600: {
            items: 4,
            stagePadding: 150
        }
    }

});
