//------------------------------------------------------------------------
//						OWL CAROUSEL OPTIONS
//------------------------------------------------------------------------

$('.carousel-5item-nav').owlCarousel({
    loop: false,
    margin: 30,
    autoplay: true,
    autoplayHoverPause: true,
    nav: true,
    dots: false,
    navText: ['', ''],
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