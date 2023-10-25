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

    let flash = $('.d-tech-form input[name="flash"]').attr('value');

    if (typeof flash !== 'undefined') {
        let messages = $.parseJSON(flash);

        if (messages.message !== undefined) {
            $('.toast .toast-body').text(messages.message);
            $('.toast').toast('show');
        }
    }

    $('ul[role="tablist"] a[data-toggle="tab"]').on('click', function(e) {
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
            },
            placement: 'top'
        };
        $('[data-toggle="tooltip"]').tooltip(options);
    });

    let imgSrc = $('#imgProfile').attr('src');
    let changeBtn = $('#btnChangePicture').attr('value');
    
    function readURL(input) {

        if (input.files && input.files[0]) {
            let reader = new FileReader();

            reader.onload = function (e) {
                $('#imgProfile').attr('src', e.target.result);
            };

            reader.readAsDataURL(input.files[0]);
        }
    }
    $('#btnChangePicture').on('click', function () {

        if (!$('#btnChangePicture').hasClass('changing')) {
            $('#profilePicture').click();
        } else {
            let form_data = new FormData();
            let url = $('#route').attr('value');
            let files = $('#profilePicture')[0].files[0];

            form_data.append('file', files);

            $.ajax({
                url: url,
                type: 'post',
                data: form_data,
                contentType: false,
                processData: false,
                success: function (response) {
                    if (response !== 0) {
                        $('#btnChangePicture').removeClass('changing').attr('value', changeBtn);
                        $('#btnDiscard').hide();
                        $('#imgProfile').attr('src', response.picture);
                        $('.toast .toast-body').text(response.message);
                        $('.toast').toast('show');
                    }
                }
            });
        }
    });
    $('#profilePicture').on('change', function () {
        readURL(this);
        $('#btnChangePicture').attr('value', $('#btnChangePicture').attr('data-confirm')).addClass('changing');
        $('#btnDiscard').removeClass('d-none');
    });
    
    $('#btnDiscard').on('click', function () {        
        $('#btnChangePicture').removeClass('changing').attr('value', changeBtn);
        $('#btnDiscard').addClass('d-none');
        $('#imgProfile').attr('src', imgSrc);
        $('#profilePicture').val('');        
    });
});
