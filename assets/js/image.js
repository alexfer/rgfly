import $ from 'jquery';
import swal from 'sweetalert';

$(function () {
    let imgSrc = $('#imgProfile').attr('src');
    let changeBtn = $('#btnChangePicture');
    let formData = new FormData();
    let info = $('.card-footer .picture-info');
    let profile = $('label[for="profile_picture"]');
    let entry = $('label[for="picture"]');
    let attachments = $('.entry-attachments');
    let loader = $('.loader');
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

    let upload = function (file) {
        $('.input-group-append button').on('click', function (e) {
            e.preventDefault();
            formData.append('file', file);

            if (file) {
                loader.toggleClass('show');
                attachments.toggleClass('blur');
                $.ajax({
                    url: $(this).attr('data-url'),
                    type: 'post',
                    data: formData,
                    contentType: false,
                    processData: false,
                    success: function (response) {
                        if (response) {
                            let toats = $('.toast .toast-body');

                            if (!response.picture) {
                                toats.toggleClass('error');
                                toats.text(response.message);
                            } else {
                                toats.removeClass('error').text(response.message);
                            }

                            profile.text(profile.attr('data-label'));
                            entry.text(entry.attr('data-label'));
                            info.text('');
                            $('.toast').toast('show');

                            attachments.empty();

                            $.each(response.attachments, function (key, attachment) {
                                attachments.append(
                                    $('<div/>')
                                        .attr('class', 'd-inline-block  mr-3 mb-3').append(
                                        $('<img/>')
                                            .attr('class', 'attach lazy')
                                            .attr('src', attachment.url)
                                    ).append($('<div/>').attr('class', 'text-right').css('display', 'none')
                                        .append($('<small/>').html(formatBytes(attachment.size))
                                            .append(
                                                $('<i>').attr('class', 'bi bi-person-bounding-box')
                                                    .append($('<i>').attr('class', 'bi bi-trash-fill trash')))
                                        ))
                                );
                                attachments.delay(10000);
                            });
                        }
                    },
                    complete: function () {
                        setTimeout(() => {
                            loader.removeClass('show');
                            attachments.removeClass('blur');
                        }, 7000);
                    }
                });
                file = null;
            } else {
                info.html(profile.attr('data-label'));
            }
        });
    };

    attachments.find('.handlers a').on('click', function (e) {
        e.preventDefault();

        const url = $(this).attr('href');
        const id = $(this).parent('div').attr('data-id');
        const action = $(this).attr('data-action');
        const args = JSON.parse(attachments.attr('data-params'));
        let params = {};

        if (action === 'remove') {
            params = {
                title: args.remove.title,
                text: args.remove.text,
                icon: args.remove.icon,
                buttons: true,
                dangerMode: true
            };
        } else {
            params = {
                title: args.default.title,
                text: args.default.text,
                icon: args.default.icon,
                buttons: true
            };
        }

        swal(params).then(name => {
            if (!name) throw null;
            return fetch(url, {
                method: 'post',
                body: JSON.stringify({id: id})
            }).catch(swal.noop);
        }).then(response => {
            return response.json();
        }).then(json => {
            $(this).parent('div').parent('div').remove();
            swal({
                title: args.success.title,
                text: json.message,
                icon: args.success.icon
            });
        }, function (dismiss) {
            if (dismiss === 'cancel' || dismiss === 'close') {
            }
        });
    });

    $('input[name="profile[picture]"], input[name="entry[picture]"]').on('change', function (e) {
        e.preventDefault();
        let file = this.files[0];
        let input = document.getElementById('profile_picture');
        info.html(file.name + ', ' + formatBytes(file.size) + ', ' + file.type);
        profile.html(file.name);
        entry.html(file.name);
        upload(file);

        input.parentNode.innerHTML = input.parentNode.innerHTML;
    });

    changeBtn.on('click', function () {
        if (!changeBtn.hasClass('changing')) {
            $('#profilePicture').click();
        } else {
            let url = $('#route').attr('value');
            let loading = $('#loading');
            let btnChange = $('#btnChangePicture');
            let files = $('#profilePicture')[0].files[0];

            formData.append('file', files);
            btnChange.hide();
            loading.show();
            $.ajax({
                url: url,
                type: 'post',
                data: formData,
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