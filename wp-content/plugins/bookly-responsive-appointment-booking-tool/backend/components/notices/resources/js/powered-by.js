jQuery(function ($) {
    var $notice = $('#bookly-js-powered-by');
    $notice.on('close.bs.alert', function () {
        $.post(ajaxurl, {action: $notice.data('action'), csrf_token: BooklySupportL10n.csrfToken});
    }).on('click', '#bookly-js-show-powered-by', function () {
        var ladda = Ladda.create(this);
        ladda.start();
        $.post(ajaxurl, {action: 'bookly_enable_show_powered_by', csrf_token: BooklySupportL10n.csrfToken}, function (response) {
            $notice.alert('close');
        });
    });
});