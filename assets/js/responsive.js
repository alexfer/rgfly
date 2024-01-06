import $ from 'jquery';
import ('jquery.cookie');

$(window).on('load', function () {
    let label = $('label[for="mode"]'), mode = $('#mode'), responsive = $('#responsive').find('.sr-only');
    if($.cookie('container')) {
        $('#container').removeClass('container').addClass('p-0 ' + $.cookie('container'));
        mode.attr('checked', 'checked');
        label.html(label.data('responsive-on'));
        responsive.toggleClass('show');
    } else {
        responsive.removeClass('show');
        label.html(label.data('responsive-off'));
    }
});

$(function () {
    $('#mode').on('change', function (e) {

        let mode = $(this);
        let container = $('#container');
        let label = $('label[for="mode"]');
        let responsive = $('#responsive').find('.sr-only')

        if (mode.is(':checked')) {
            label.html(label.data('responsive-on'));
            mode.attr('checked', 'checked');
            $.cookie('container', 'container-fluid', {expires: 7, path: '/'});
            container.attr('class', 'container-fluid p-0');
            responsive.toggleClass('show');
        } else {
            label.html(label.data('responsive-off'));
            mode.removeAttr('checked');
            $.removeCookie('container');
            container.attr('class', 'container');
            responsive.removeClass('show');
        }
    });
});