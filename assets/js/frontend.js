import $ from 'jquery';

$(window).on('load', function () {
    $('.preloader').fadeOut('500');
});

$(window).on('scroll', function () {
    if ($(this).scrollTop() > 120) {
        $('.navbar-area').addClass("is-sticky");
    } else {
        $('.navbar-area').removeClass("is-sticky");
    }
});
$(function () {

    $('a[data-toggle="accordion-content"]').on('click', function (e) {
        e.preventDefault();

        $(this).toggleClass('active');
        $(this).next().slideToggle('slow');
        $('.accordion-content').not($(this).next()).slideUp('slow');
        $('.accordion-title').not($(this)).removeClass('active');
    });

    $('a[aria-controls="blog"]').on('click', function (e) {
        e.preventDefault();

        let arrow = $(this).children('.arrow');

        if (arrow.hasClass('bi-arrow-down-short')) {            
            arrow.removeClass('bi-arrow-down-short').addClass('bi-arrow-right-short');
        } else {
            arrow.removeClass('bi-arrow-right-short').addClass('bi-arrow-down-short');
        }
        
        
    });
});
