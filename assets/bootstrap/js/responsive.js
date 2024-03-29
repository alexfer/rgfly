import $ from 'jquery';

import ('jquery.cookie');

let container = $('#container');
let label = $('label[for="mode"]');
let responsive = $('#responsive').find('.visually-hidden');

$(window).on('load', () => {
    let mode = $('#mode');
    if ($.cookie('container')) {
        $('#container').removeClass('container').addClass($.cookie('container'));
        mode.attr('checked', 'checked');
        label.html(label.data('responsive-on'));
        responsive.toggleClass('show');
    } else {
        responsive.removeClass('show');
        label.html(label.data('responsive-off'));
    }
});

$(() => {
    $('#mode').on('change', function (e) {
        let mode = $(this);
        if (mode.is(':checked')) {
            label.html(label.data('responsive-on'));
            mode.attr('checked', 'checked');
            $.cookie('container', 'container-fluid', {expires: 7, path: '/'});
            container.attr('class', 'container-fluid');
            responsive.toggleClass('show');
        } else {
            label.html(label.data('responsive-off'));
            mode.removeAttr('checked');
            $.removeCookie('container', {path: '/'});
            container.attr('class', 'container');
            responsive.removeClass('show');
        }
    });
});