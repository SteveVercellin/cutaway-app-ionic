jQuery(function ($) {
    $('.board-backdrop').on('click', '[data-trigger]', function () {
        switch ($(this).data('trigger')) {
            case 'temporary-hide':
                var $dialog = $(this).closest('.board-backdrop');
                $.get(LicenseL10n.ajaxurl, {action: 'bookly_pro_hide_grace_notice', csrf_token: LicenseL10n.csrfToken})
                    .done(function () {
                        $dialog.remove();
                    });
                break;
            case 'request_code':
                var $body = $(this).closest('.bookly-board-body');
                $.post(LicenseL10n.ajaxurl, {action: 'bookly_pro_verify_purchase_code_form', csrf_token : LicenseL10n.csrfToken}, function (response) {
                    $body.html(response.data.html);
                    var $proceed_link = $body.find('.bookly-verified');
                    if ($body.find('#Bookly').length == 0) {
                        $proceed_link.show();
                    }
                    $('.purchase-code').on('keyup', function (e) {
                        var $input = $(this);
                        if ($input.val().length == 36 && e.which > 47 /* key 0 */) {
                            var $group = $input.closest('.has-feedback');
                            $group.removeClass('has-warning has-error').addClass('has-ajax');
                            $.post(LicenseL10n.ajaxurl, {action: 'bookly_pro_verify_purchase_code', plugin: $input.attr('id'), purchase_code: $input.val(), csrf_token: LicenseL10n.csrfToken}, function (response) {
                                $group.removeClass('has-ajax');
                                if (response.success) {
                                    $group.addClass('has-success');
                                    $all_valid = true;
                                    $('.has-feedback').each(function (index, elem) {
                                        if (!$(elem).hasClass('has-success')) {
                                            $all_valid = false;
                                        }
                                    });
                                    if ($all_valid) {
                                        $.post(LicenseL10n.ajaxurl, {action: 'bookly_pro_verification_succeeded', csrf_token: LicenseL10n.csrfToken}, function (response) {
                                            $body.closest('.board-backdrop').html(response.data.html);
                                        });
                                    }
                                    if ($input.attr('id') == 'Bookly') {
                                        $proceed_link.show();
                                    }
                                } else {
                                    if (response.data.message) {
                                        booklyAlert({error: [response.data.message]});
                                    }
                                    $group.addClass('has-error');
                                }
                            });
                        }
                    });
                });
                break;
        }
    });

    // Deactivate add-on Bookly Pro from *_grace_ended
    $('.bookly-js-deactivate-pro').on('click', function () {
        var ladda = Ladda.create(this),
            $button = $(this);
        ladda.start();
        $.post(ajaxurl, {action: 'bookly_pro_deactivate', csrf_token: LicenseL10n.csrfToken}, function (response) {
            if (response.success) {
                if ($button.data('redirect')){
                    window.location.href = response.data.target;
                } else {
                    $button.closest('.alert.alert-info').remove();
                }
            }
        });
    });

    $('.alert.bookly-tbs-body').on('click', '[data-trigger=temporary-hide]', function () {
        $.get(LicenseL10n.ajaxurl, {action: 'bookly_pro_hide_grace_notice', csrf_token : LicenseL10n.csrfToken});
    });
});
