jQuery.windowWaiting = function windowWaiting($args)
{
    $args = jQuery.type($args) != "undefined" ? $args : {};

    if (jQuery.type(jQuery.alert) != "undefined") {
        var $dataInit = {
            title: jquery_adapter_i18n.waiting_title,
            content: jquery_adapter_i18n.waiting_content,
            buttons: {
                close: {
                    text: jquery_adapter_i18n.close_button,
                    btnClass: 'popup-button-hide'
                }
            },
            useBootstrap: false,
            boxWidth: '80%'
        };
        if ($args.hasOwnProperty("onClose") && jQuery.type($args.onClose) === "function") {
            $dataInit.onClose = $args.onClose;
        }

        return jQuery.alert($dataInit);
    } else {
        alert(message);
    }

    return null;
}
jQuery.windowAlert = function windowAlert(title, message, $args)
{
    $args = jQuery.type($args) != "undefined" ? $args : {};

    if (jQuery.type(jQuery.alert) != "undefined") {
        var $dataInit = {
            title: title,
            content: message,
            buttons: {
                close: {
                    text: jquery_adapter_i18n.close_button
                }
            },
            useBootstrap: false,
            boxWidth: '80%'
        };
        if ($args.hasOwnProperty("onClose") && jQuery.type($args.onClose) === "function") {
            $dataInit.onClose = $args.onClose;
        }

        return jQuery.alert($dataInit);
    } else {
        alert(message);
    }

    return null;
}
jQuery.windowConfirm = function windowConfirm(title, message, $args)
{
    $args = jQuery.type($args) != "undefined" ? $args : {};

    if (jQuery.type(jQuery.confirm) != "undefined") {
        var $dataInit = {
            title: title,
            content: message,
            buttons: {
                ok: {
                    text: jquery_adapter_i18n.ok_button,
                    action: $args.hasOwnProperty("ok") && jQuery.type($args.ok) === "function" ? $args.ok : function () {}
                },
                cancel: {
                    text: jquery_adapter_i18n.cancel_button,
                    action: $args.hasOwnProperty("cancel") && jQuery.type($args.cancel) === "function" ? $args.cancel : function () {}
                }
            },
            useBootstrap: false,
            boxWidth: '80%'
        };

        return jQuery.confirm($dataInit);
    } else {
        confirm(message);
    }

    return null;
}