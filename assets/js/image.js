import $ from 'jquery';

$(function () {
    $('#pills-tab .nav-link').on('click', function (e) {
        e.preventDefault();
        let nav = $(this);
        let id = nav.next().children('i').attr('data-id');
        let url = nav.next().children('i').attr('data-url');
        let first = $('.tech-form .pictures li:nth-child(2)').children().children('img');
        let current = nav.children('img');

        if (typeof id !== 'undefined') {
            $.post(url, {id: id}, function (response) {
                $('.toast .toast-body').text(response.message);
            }).fail(function () {
                $('.toast .toast-body').text('Something went wrong');
            }).always(function () {
                $('.toast').toast('show');
            });

            first.attr('src', current.attr('src'));
            current.attr('src', first.attr('src'));
        }
    });

    let readURL = function (input) {

        if (input.files && input.files[0]) {
            let reader = new FileReader();

            reader.onload = function (e) {
                $('#imgProfile').attr('src', e.target.result);
            };

            reader.readAsDataURL(input.files[0]);
        }
    }

    let formatBytes = function (bytes, decimals = 2) {
        if (!+bytes) return '0 Bytes'

        const k = 1024
        const dm = decimals < 0 ? 0 : decimals
        const sizes = ['Bytes', 'KiB', 'MiB', 'GiB', 'TiB', 'PiB', 'EiB', 'ZiB', 'YiB']
        const i = Math.floor(Math.log(bytes) / Math.log(k))

        return `${parseFloat((bytes / Math.pow(k, i)).toFixed(dm))} ${sizes[i]}`
    }

    let imgSrc = $('#imgProfile').attr('src');
    let changeBtn = $('#btnChangePicture');
    let form_data = new FormData();
    let info = $('.card-footer .picture-info');
    let profile = $('label[for="profile_picture"]');
    let upload = function (file) {
        $('.input-group-append button').on('click', function (e) {
            e.preventDefault();
            form_data.append('file', file);

            if (file) {
                $('.tech-form .pictures .blank').hide();
                $('.tech-form .pictures .spinner-grow').fadeIn('slow');
                $.ajax({
                    url: $(this).attr('data-url'),
                    type: 'post',
                    data: form_data,
                    contentType: false,
                    processData: false,
                    success: function (response) {
                        if (response !== 0) {
                            profile.text(profile.attr('data-label'));
                            info.text('');
                            $('.toast .toast-body').text(response.message);
                            $('.toast').toast('show');
                            let wrapper = $('.tech-form .pictures .wrapper');
                            wrapper.find('img').attr('src', response.picture);
                            wrapper.after(wrapper.html());
                            $('.tech-form .pictures a.first').toggleClass('inactive').removeClass('first');
                        }
                    },
                    complete: function () {
                        setTimeout(() => {
                            $('.tech-form .pictures .spinner-grow').fadeOut('slow');
                        }, 2000);
                    }
                });
                file = null;
            } else {
                info.html(profile.attr('data-label'));
            }
        });
    };

    $('input[name="profile[picture]"]').on('change', function (e) {
        e.preventDefault();
        let file = this.files[0];
        info.html(file.name + ', ' + formatBytes(file.size) + ', ' + file.type);
        profile.html(file.name);
        upload(file);
    });

    changeBtn.on('click', function () {
        if (!changeBtn.hasClass('changing')) {
            $('#profilePicture').click();
        } else {
            let url = $('#route').attr('value');
            let loading = $('#loading');
            let btnChange = $('#btnChangePicture');
            let files = $('#profilePicture')[0].files[0];

            form_data.append('file', files);
            btnChange.hide();
            loading.show();
            $.ajax({
                url: url,
                type: 'post',
                data: form_data,
                contentType: false,
                processData: false,
                success: function (response) {
                    btnChange.show();
                    loading.hide();
                    if (response !== 0) {
                        btnChange.html(btnChange.attr('data-change')).removeClass('changing').show();
                        $('#btnDiscard').hide();
                        $('#imgProfile').attr('src', response.picture);
                        $('.toast .toast-body').text(response.message);
                        $('.toast').toast('show');
                    }
                },
                complete: function () {
                    $('.toast').toast('show');
                }
            });
        }
    });

    $('#profilePicture').on('change', function () {
        readURL(this);
        let btnChange = $('#btnChangePicture');
        btnChange.html(btnChange.attr('data-confirm')).addClass('changing');
        $('#btnDiscard').removeClass('d-none');
    });

    $('#btnDiscard').on('click', function () {
        let btnChange = $('#btnChangePicture');
        btnChange.html(btnChange.attr('data-change')).removeClass('changing');
        $('#btnDiscard').addClass('d-none');
        $('#imgProfile').attr('src', imgSrc);
        $('#profilePicture').val('');
    });

});