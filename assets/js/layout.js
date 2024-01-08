import $ from 'jquery';
import tooltip from "bootstrap/js/src/tooltip";

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
    let toast = $('.toast');

    let flash = $('.d-tech-form input[name="flash"], .tech-form input[name="flash"]').attr('value');

    if (typeof flash !== 'undefined') {
        let messages = $.parseJSON(flash);

        if (messages.message !== undefined) {
            $('.toast .toast-body').text(messages.message);
            toast.removeClass('hide').toggleClass('show');
        }
    }
    toast.delay(3000).fadeOut(200);

    $('ul[role="tablist"] a[data-bs-toggle="tab"]').on('click', function (e) {
        e.preventDefault();
        let location = $(this).attr('aria-controls');
        window.history.replaceState({}, '', location);
    });

    $('a[data-bs-toggle="accordion-content"]').on('click', function (e) {
        e.preventDefault();

        $(this).toggleClass('active');
        $(this).next().slideToggle('slow');
        $('.accordion-content').not($(this).next()).slideUp('toggle');
        $('.accordion-title').not($(this)).removeClass('active');
    });

    $('a[data-bs-toggle="collapse"]').on('click', function (e) {
        e.preventDefault();

        let arrow = $(this).children('.arrow');

        if (arrow.hasClass('bi-arrow-down-short')) {
            arrow.removeClass('bi-arrow-down-short').addClass('bi-arrow-right-short');
        } else {
            arrow.removeClass('bi-arrow-right-short').addClass('bi-arrow-down-short');
        }
    });

    $('.delete-entry').on('click', function () {
        $('.modal input[name="_token"]').attr('value', $(this).attr('data-token'));
        $('.modal .confirm').attr('action', $(this).attr('data-url'));
    });

    $('.add-entry').on('click', function () {
        let url = $(this).attr('data-url');
        console.log($(this).attr('data-bs-target'));
        $('.modal input[id="_token"]').attr('value', $(this).attr('data-token'));
        $('.modal form').off().on('submit', function () {
            let form = $(this);
            $.post(url, form.serialize(), function (response) {
                form.trigger('reset');

                let data = response.json;
                let owner = data.id.toString();

                $(owner).append($('<option/>', {
                    value: data.option.id,
                    text: data.option.name,
                    selected: true,
                }));
            });

            $('.modal button[data-bs-dismiss="modal"]').click();
            return false;
        });
    });

    $('.load-categories').on('click', function (e) {
        e.preventDefault();
        $(this).parent('div').children('.sr-only').removeClass('sr-only');
        $(this).remove();
    });
});