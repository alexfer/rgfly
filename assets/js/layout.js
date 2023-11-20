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
    $('.toast').toast('hide');

    let flash = $('.d-tech-form input[name="flash"], .tech-form input[name="flash"]').attr('value');

    if (typeof flash !== 'undefined') {
        let messages = $.parseJSON(flash);

        if (messages.message !== undefined) {
            $('.toast .toast-body').text(messages.message);
            $('.toast').toast('show');
        }
    }

    $('ul[role="tablist"] a[data-toggle="tab"]').on('click', function (e) {
        e.preventDefault();
        let location = $(this).attr('aria-controls');
        window.history.replaceState({}, '', location);
    });

    $('a[data-toggle="accordion-content"]').on('click', function (e) {
        e.preventDefault();

        $(this).toggleClass('active');
        $(this).next().slideToggle('slow');
        $('.accordion-content').not($(this).next()).slideUp('toggle');
        $('.accordion-title').not($(this)).removeClass('active');
    });

    $('a[data-toggle="collapse"]').on('click', function (e) {
        e.preventDefault();

        let arrow = $(this).children('.arrow');

        if (arrow.hasClass('bi-arrow-down-short')) {
            arrow.removeClass('bi-arrow-down-short').addClass('bi-arrow-right-short');
        } else {
            arrow.removeClass('bi-arrow-right-short').addClass('bi-arrow-down-short');
        }
    });

    $('.delete-entry').on('click', function (e) {
        $('.modal input[name="_token"]').attr('value', $(this).attr('data-token'));
        $('.modal .confirm').attr('action', $(this).attr('data-url'));
    });

    $(function () {
        let options = {
            delay: {
                'show': 100,
                'hide': 400
            }
        };
        $('[data-toggle="tooltip"]').tooltip(options);
    });
});
