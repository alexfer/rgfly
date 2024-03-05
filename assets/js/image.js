import $ from 'jquery';
import swal from 'sweetalert';

$(function () {
    let imgSrc = $('#imgProfile').attr('src');
    let changeBtn = $('#btnChangePicture');
    let formData = new FormData();
    let info = $('.card-footer .picture-info');
    let profile = $('input[id="profile_picture"]') || undefined;
    let entry = $('input[id="picture"]') || undefined;
    let attachments = $('.entry-attachments');
    let loader = $('.loader');
    let readURL = function (input) {
        if (input.files && input.files[0]) {
            let reader = new FileReader();
            reader.onload = (e) => {
                $('#imgProfile').attr('src', e.target.result);
            };
            reader.readAsDataURL(input.files[0]);
        }
    }

    let formatBytes = function (bytes, decimals = 2) {
        if (!+bytes) return '0 Bytes';

        const k = 1024
        const dm = decimals < 0 ? 0 : decimals
        const sizes = ['Bytes', 'KiB', 'MiB', 'GiB', 'TiB', 'PiB', 'EiB', 'ZiB', 'YiB']
        const i = Math.floor(Math.log(bytes) / Math.log(k))

        return `${parseFloat((bytes / Math.pow(k, i)).toFixed(dm))} ${sizes[i]}`
    }

    let upload = (file) => {
        $('button[id="attach"]').on('click', function (e) {
            if (file) {
                formData.append('file', file);
                loader.toggleClass('show');
                attachments.toggleClass('blur');
                $.ajax({
                    url: $(this).data('url'),
                    type: 'post',
                    data: formData,
                    contentType: false,
                    processData: false,
                    success: function (response) {
                        if (response) {
                            let toast = $('.toast .toast-body');

                            if (!response.picture) {
                                toast.toggleClass('error');
                                toast.text(response.message);
                            } else {
                                toast.removeClass('error').text(response.message);
                            }

                            entry.text(entry.data('label'));
                            info.text('');
                            $('.toast').removeClass('hide').toggleClass('show');
                            // TODO: fix it
                            attachments.prepend(
                                $('<div/>')
                                    .attr('class', 'd-inline-block me-3 mb-3').append(
                                    $('<img/>')
                                        .attr('class', 'attach')
                                        .attr('src', response.picture)
                                ).append(
                                    $('<div/>').attr('class', 'handler').attr('data-id', response.id))).delay(3000).show('slow');
                            if (attachments.length > 6) {
                                setTimeout(() => {
                                    attachments.children().last().remove();
                                }, 2000);
                            }
                        }
                    },
                    complete: function () {
                        setTimeout(() => {
                            loader.removeClass('show');
                            attachments.removeClass('blur');
                            if (Object.keys(profile).length === 3) {
                                profile.wrap('<form>').closest('form').get(0).reset();
                                profile.unwrap();
                            }
                            if (Object.keys(entry).length === 3) {
                                entry.wrap('<form>').closest('form').get(0).reset();
                                entry.unwrap();
                                file = null;
                            }
                        }, 3000);
                    }
                });
            }
        });
    };

    attachments.find('.handler a').off().on('click', function (event) {
        event.preventDefault();

        const url = $(this).attr('href');
        const id = $(this).parent('div').data('id');
        const action = $(this).data('action');
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
            if (action === 'remove') {
                $(this).parent('div').parent('div').remove();
            }
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
        info.html(file.name + ', ' + formatBytes(file.size) + ', ' + file.type);
        profile.html(file.name);
        entry.html(file.name);
        upload(file);
    });

    changeBtn.on('click', () => {
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
                success: (response) => {
                    btnChange.show();
                    loading.hide();
                    if (response !== 0) {
                        btnChange.html(btnChange.attr('data-change')).removeClass('changing').show();
                        $('#btnDiscard').hide();
                        $('#imgProfile').attr('src', response.picture);
                        $('.toast .toast-body').text(response.message);
                        $('.toast').show();
                    }
                },
                complete: () => {
                    $('.toast').show();
                }
            });
        }
    });

    $('#profilePicture').on('change', () => {
        readURL(this);
        let btnChange = $('#btnChangePicture');
        btnChange.html(btnChange.attr('data-confirm')).addClass('changing');
        $('#btnDiscard').removeClass('d-none');
    });

    $('#btnDiscard').on('click', () => {
        let btnChange = $('#btnChangePicture');
        btnChange.html(btnChange.attr('data-change')).removeClass('changing');
        $('#btnDiscard').addClass('d-none');
        $('#imgProfile').attr('src', imgSrc);
        $('#profilePicture').val('');
    });

});