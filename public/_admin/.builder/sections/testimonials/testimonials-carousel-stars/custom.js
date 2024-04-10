$('.carousel-4item-dots-marg').owlCarousel({
    loop: false,
    margin: 60,
    nav: false,
    autoplay: true,
    autoplayHoverPause: true,
    rewind: true,
    responsive: {
        0: {
            items: 1
        },
        600: {
            items: 2
        },
        1000: {
            items: 4
        }
    }
});