$(document).ready(() => {
    
    // NAVBAR 
    // $('.product-menu').hover(function () {
    //     $('.item-menu').stop(true, true).show();
    // }, function () {
    //     $('.item-menu').stop(true, true).hide();
    // });

    // $('.item-menu').hover(function () {
    //     $(this).stop(true, true).show();
    // }, function () {
    //     $(this).stop(true, true).hide();
    // });

    // Hero image slides
    // $("#hero-image-slide").slick({
    //     autoplay: true,
    //     autoplaySpeed: 1500,
    //     prevArrow: $("#prevArrow"),
    //     nextArrow: $("#nextArrow"),
    //     dots: true,
    //     customPaging: () => {
    //         return "<button></button>"
    //     }
    // });

    $("#outer-hero-image").on("mouseenter",() => {
        const prevArrow = document.getElementById("prevArrow");
        const nextArrow = document.getElementById("nextArrow");

        prevArrow.style.marginLeft = "10px"
        nextArrow.style.marginRight = "10px"

    })
    $("#outer-hero-image").on("mouseleave",() => {
        const prevArrow = document.getElementById("prevArrow");
        const nextArrow = document.getElementById("nextArrow");

        prevArrow.style.marginLeft = "-35px"
        nextArrow.style.marginRight = "-35px"

    })

    // Item menu slide
    // $("#item-slides").slick({
    //     rows: 2,
    //     slidesPerRow: 3,
    //     prevArrow: $("#itemPrevArrow"),
    //     nextArrow: $("#itemNextArrow"),
        
    // });



    // Event slides
    // $("#event-slides").slick({
    //     slidesToShow: 3,
    //     slidesToScroll: 3,
    //     prevArrow: $("#eventPrevBtn"),
    //     nextArrow: $("#eventNextBtn")
    // })
    // $("#event-section").on("mouseenter",() => {
    //     const prevArrow = document.getElementById("eventPrevBtn");
    //     const nextArrow = document.getElementById("eventNextBtn");

    //     prevArrow.style.marginLeft = "-50px"
    //     nextArrow.style.marginRight = "-20px"

    // })
    // $("#event-section").on("mouseleave",() => {
    //     const prevArrow = document.getElementById("eventPrevBtn");
    //     const nextArrow = document.getElementById("eventNextBtn");

    //     prevArrow.style.marginLeft = "-100rem"
    //     nextArrow.style.marginRight = "-100rem"

    // })
});
