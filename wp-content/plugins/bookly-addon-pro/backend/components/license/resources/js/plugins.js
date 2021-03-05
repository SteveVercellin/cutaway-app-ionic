jQuery(function ($) {
    $('.bookly-js-plugin').each(function () {
        let $plugin_tr = $(this).prev();
            $plugin_tr.addClass('update');

        $('[data-bookly-plugin]',$(this)).on('click', function (e) {
            e.preventDefault();
            let $spinner = $(this).siblings('.spinner');
            $spinner.addClass('is-active');
            let $update_link = $('a[href*="puc_check_for_updates=1&puc_slug=bookly-addon-"]', $plugin_tr),
                data = {
                    action    : 'bookly_pro_re_check_support',
                    csrf_token: $(this).data('csrf'),
                    plugin    : $(this).data('bookly-plugin')
                };

            $.ajax({
                url  : ajaxurl,
                type : 'POST',
                data : data,
                dataType : 'json',
                success  : function(response) {
                    if (response.valid) {
                        window.location.href = $update_link.attr('href');
                    } else {
                        $spinner.removeClass('is-active');
                        alert(response.message);
                    }
                }
            });
        })
    })
});
