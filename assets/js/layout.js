import $ from 'jquery';

document.addEventListener('DOMContentLoaded', (event) => {
    setTimeout(() => {
        let preloader = document.getElementsByClassName('preloader');
        preloader.item(0).style.display = 'none';
    }, 700);
});

// let deleteEntry = document.getElementsByClassName('delete-entry') || undefined;
//
// if (typeof deleteEntry !== undefined) {
//     console.log(deleteEntry.length);
//     // categories.addEventListener('click', (event) => {
//     //     alert(event.currentTarget);
//     // });
// }

$(function () {
    let toast = $('.toast');
    let flash = $('form input[name="flash"]').attr('value');

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

    $('.delete-entry').on('click', function () {
        $('.modal input[name="_token"]').attr('value', $(this).attr('data-token'));
        $('.modal .confirm').attr('action', $(this).attr('data-url'));
    });

    $('.load-categories').on('click', function (e) {
        e.preventDefault();
        $(this).parent('div').children('.visually-hidden').removeClass('visually-hidden');
        $(this).remove();
    });

    $('.add-entry').on('click', function () {
        let url = $(this).attr('data-url');
        let xhr = $(this).attr('data-xhr');
        let owner = $(this).attr('data-bs-target');

        if (typeof xhr !== 'undefined') {
            $.get(xhr, function (response) {
                $.each(response.countries, function (key, text) {
                    $('.modal #suppler_country').append($('<option>', {
                        value: key,
                        text: text
                    }));
                });
            })
        }

        $('.modal input[id="_token"]').attr('value', $(this).attr('data-token'));
        $('.modal form').off().on('submit', function () {
            let form = $(this);
            $.post(url, form.serialize(), (response) => {
                let data = response.json;
                let owner = data.id.toString();
                setTimeout(() => {
                    form.trigger('reset');
                    $('.modal button[data-bs-dismiss="modal"]').click();
                }, 300);

                $(owner).append($('<option/>', {
                    value: data.option.id,
                    text: data.option.name,
                    selected: true,
                }));
            }).fail((xhr) => {
                console.log(xhr);
            });
            return false;
        });
    });
});