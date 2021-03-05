var waitingBox = null;
var promptBox = null;
var stripe = Stripe(stripe_config.key);
var cardNumber = null;
var cardExpiry = null;
var cardCvc = null;
var cardPostalCode = null;
var elements = stripe.elements();
var elementClasses = {
    base:		'cf7pp_details_input',
    focus: 		'focus',
    empty: 		'empty',
    invalid: 	'invalid',
};
var processingPay = false;
var createdPendingBooking = false;
var groupPendingBooking = 0;
var ignoreCreatePendingBooking = false;
(function($) {

    $(document).ready(function() {
        if (payment_config.group_booking != 0) {
            ignoreCreatePendingBooking = true;
            groupPendingBooking = payment_config.group_booking;
        }

        $('.payment-method').click(function (e) {
            e.preventDefault();
            var $button = $(this);

            if (!checkIsProcessingPay()) {
                if (ignoreCreatePendingBooking) {
                    if ($button.hasClass('credit-card')) {
                        generateEnterCardPopup();
                    } else if($button.hasClass('paypal')) {
                        $formPaypal = $('.paypal-form form');
                        $formPaypal.submit();
                    }
                } else if (!createdPendingBooking) {
                    $.ajax({
                        url: payment_config.ajax_create_pending_booking,
                        data: {},
                        dataType: 'json',
                        method: 'post',
                        beforeSend: function(jqXHR, settings) {
                            makePageIsProcessingPay();
                            generateWaitingPopup();
                        },
                        success: function(res, textStatus, jqXHR) {
                            makePageStopProcessPay();
                            waitingBox.close();

                            if (res.status == 'ok' && res.group != 0) {
                                createdPendingBooking = true;
                                groupPendingBooking = res.group;

                                if ($button.hasClass('credit-card')) {
                                    generateEnterCardPopup();
                                } else if($button.hasClass('paypal')) {
                                    var paypalNotifyLink = res.paypal_notify_link;
                                    $formPaypal = $('.paypal-form form');
                                    $('input[name="notify_url"]', $formPaypal).attr('value', paypalNotifyLink);

                                    $formPaypal.submit();
                                }
                            } else {
                                generateCreatePendingBookingFailWarning();
                            }
                        },
                        error: function(jqXHR, textStatus, errorThrown) {
                            makePageStopProcessPay();
                            waitingBox.close();
                            generateCreatePendingBookingFailWarning();
                        }
                    });
                } else {
                    generateCreatedPendingBookingWarning();
                }
            }
        });
    });

    function generateWaitingPopup()
    {
        waitingBox = $.windowWaiting();
    }

    function generateCreatePendingBookingFailWarning()
    {
        $.windowAlert(jquery_adapter_i18n.error_box_title, payment_config.create_pending_booking_fail);
    }

    function generateCreatedPendingBookingWarning()
    {
        $.windowAlert(jquery_adapter_i18n.error_box_title, payment_config.created_pending_booking_fail, {
            onClose: function () {
                window.location.href = payment_config.list_orders_page;
            }
        });
    }

    function generateErrorPopup()
    {
        $.windowAlert(jquery_adapter_i18n.error_box_title, stripe_config.error_box_html, {
            onClose: function () {
                if (!ignoreCreatePendingBooking) {
                    generateCreatedPendingBookingWarning();
                }
            }
        });
    }

    function generateSuccessPopup()
    {
        $.windowAlert(jquery_adapter_i18n.success_box_title, stripe_config.success_box_html, {
            onClose: function () {
                afterCompletedPayment();
            }
        });
    }

    function generateEnterCardPopup()
    {
        makePageIsProcessingPay();

        promptBox = $.confirm({
            title: stripe_config.payment_box_title,
            content: '' + stripe_config.payment_box_html,
            lazyOpen: true,
            useBootstrap: false,
            boxWidth: '80%',
            onContentReady: function () {
                if (!cardNumber) {
                    cardNumber = elements.create('cardNumber', {
                        classes: 	elementClasses,
                        placeholder:  "\u2022\u2022\u2022\u2022 \u2022\u2022\u2022\u2022 \u2022\u2022\u2022\u2022 \u2022\u2022\u2022\u2022",
                    });
                }
                cardNumber.mount('#stripe-card-number');

                if (!cardExpiry) {
                    cardExpiry = elements.create('cardExpiry', {
                        classes: elementClasses,
                        placeholder:  "\u2022\u2022 / \u2022\u2022",
                    });
                }
                cardExpiry.mount('#stripe-card-expiration');

                if (!cardCvc) {
                    cardCvc = elements.create('cardCvc', {
                        classes: elementClasses,
                        placeholder:  "\u2022\u2022\u2022",
                    });
                }
                cardCvc.mount('#stripe-card-cvc');

                if (!cardPostalCode) {
                    cardPostalCode = elements.create('postalCode', {
                        classes: elementClasses,
                        placeholder:  "\u2022\u2022\u2022\u2022\u2022",
                    });
                }
                cardPostalCode.mount('#stripe-postal-code');
            },
            buttons: {
                confirm: {
                    text: jquery_adapter_i18n.ok_button,
                    action: function () {
                        generateWaitingPopup();
                        stripe.createToken(cardNumber).then(function(result) {
                            console.log(result);
                            unMountStripeElements();
                            if (result.error) {
                                makePageStopProcessPay();
                                waitingBox.close();
                                generateErrorPopup();
                            } else {
                                stripeCompletePayment(result);
                            }
                        });
                    }
                },
                cancel: {
                    text: jquery_adapter_i18n.cancel_button,
                    action: function () {
                        unMountStripeElements();
                        makePageStopProcessPay();
                        promptBox.close();
                        generateCreatedPendingBookingWarning();
                    }
                }
            }
        });
        promptBox.open();
    }

    function stripeCompletePayment(result)
    {
        var dataAjax = {
            'token': result.token.id,
            'group': groupPendingBooking
        };
        $.ajax({
            url: stripe_config.ajax_payment_url,
            data: dataAjax,
            dataType: 'json',
            method: 'post',
            beforeSend: function(jqXHR, settings) {},
            success: function(res, textStatus, jqXHR) {
                makePageStopProcessPay();
                waitingBox.close();

                if (res.status == 'ok') {
                    generateSuccessPopup();
                } else {
                    generateErrorPopup();
                }
            },
            error: function(jqXHR, textStatus, errorThrown) {
                makePageStopProcessPay();
                waitingBox.close();
                generateErrorPopup();
            }
        });
    }

    function afterCompletedPayment()
    {
        var thankyouPage = payment_config.thank_you_page;
        window.location.href = thankyouPage;
    }

    function unMountStripeElements()
    {
        if (cardNumber) {
            cardNumber.unmount();
        }
        if (cardExpiry) {
            cardExpiry.unmount();
        }
        if (cardCvc) {
            cardCvc.unmount();
        }
        if (cardPostalCode) {
            cardPostalCode.unmount();
        }
    }

    function checkIsProcessingPay()
    {
        return processingPay;
    }

    function makePageIsProcessingPay()
    {
        processingPay = true;
        $('.payment-method').addClass('processing');
    }

    function makePageStopProcessPay()
    {
        processingPay = false;
        $('.payment-method').removeClass('processing');
    }

})(jQuery);