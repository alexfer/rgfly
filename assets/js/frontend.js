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
});
