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
            },
            placement: 'top'
        };
        $('[data-toggle="tooltip"]').tooltip(options);
    });

    let imgSrc = $('#imgProfile').attr('src');
    function readURL(input) {

        if (input.files && input.files[0]) {
            var reader = new FileReader();

            reader.onload = function (e) {
                $('#imgProfile').attr('src', e.target.result);
            };

            reader.readAsDataURL(input.files[0]);
        }
    }
    $('#btnChangePicture').on('click', function () {
        // document.getElementById('profilePicture').click();
        if (!$('#btnChangePicture').hasClass('changing')) {
            $('#profilePicture').click();
        } else {
            // change
        }
    });
    $('#profilePicture').on('change', function () {
        readURL(this);
        $('#btnChangePicture').addClass('changing');
        $('#btnChangePicture').attr('value', 'Confirm');
        $('#btnDiscard').removeClass('d-none');
        // $('#imgProfile').attr('src', '');
    });
    $('#btnDiscard').on('click', function () {
        // if ($('#btnDiscard').hasClass('d-none')) {
        $('#btnChangePicture').removeClass('changing');
        $('#btnChangePicture').attr('value', 'Change');
        $('#btnDiscard').addClass('d-none');
        $('#imgProfile').attr('src', imgSrc);
        $('#profilePicture').val('');
        // }
    });
});
