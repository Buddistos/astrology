//------------------------------------------------------------------------
//						OWL CAROUSEL OPTIONS
//------------------------------------------------------------------------

$('.carousel-5item-dots').owlCarousel({
    loop: false,
    margin: 60,
    nav: false,
    dots: true,
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
            items: 5
        }
    }

});
