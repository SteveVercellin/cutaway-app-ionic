<?php

/**
 * Understrap enqueue scripts
 *
 * @package understrap
 */
if (!function_exists('cutaway_scripts')) {

    /**
     * Load theme's JavaScript and CSS sources.
     */
    function cutaway_scripts() {
        global $post;

        wp_enqueue_style('font-awesome-css', get_template_directory_uri() . '/css/font-awesome.min.css');
        wp_enqueue_style('jquery-ui-css', get_template_directory_uri() . '/css/jquery-ui.css');
        wp_enqueue_style('bootstrap-multiselect-css', get_template_directory_uri() . '/css/bootstrap-multiselect.css');
        wp_enqueue_style('bootstrap-switch-css', get_template_directory_uri() . '/css/bootstrap-switch.min.css');
        wp_enqueue_style('jquery-confirm-css', get_template_directory_uri() . '/css/jquery-confirm.min.css');


        // Get the theme data.
        $the_theme = wp_get_theme();
        $theme_version = $the_theme->get('Version');

        $css_version = $theme_version . '.' . filemtime(get_template_directory() . '/css/theme.css');
        wp_enqueue_style('understrap-styles', get_stylesheet_directory_uri() . '/css/theme.css', array(), $css_version);
        wp_enqueue_style('select2-css', get_stylesheet_directory_uri() . '/css/select2.min.css', array(), time());

        wp_enqueue_script('jquery');

        wp_enqueue_script('jquery-ui-js', get_template_directory_uri() . '/js/jquery-ui.js', array(), '1.12.1', true);
        wp_enqueue_script('popper-js', get_template_directory_uri() . '/js/popper.min.js', array(), time(), true);
        wp_enqueue_script('bootstrap-multiselect-js', get_template_directory_uri() . '/js/bootstrap-multiselect.js', array(), time(), true);
        wp_enqueue_script('bootstrap-switch-js', get_template_directory_uri() . '/js/bootstrap-switch.min.js', array(), '3.3.4', true);
        wp_enqueue_script('jquery-confirm-js', get_template_directory_uri() . '/js/jquery-confirm.min.js', array(), '3.3.0', true);

        wp_enqueue_script('jquery-adapter-js', get_template_directory_uri() . '/js/jquery.adapters.js', array(), '1.0', true);
        wp_localize_script( 'jquery-adapter-js', 'jquery_adapter_i18n', array(
            'waiting_title' => __('Attendere prego', 'cutaway'),
            'waiting_content' => __('In attesa di pagare', 'cutaway'),
            'ok_button' => __('Pagare', 'cutaway'),
            'cancel_button' => __('Annulla', 'cutaway'),
            'close_button' => __('Vicino', 'cutaway'),
            'error_box_title' => __('Errore', 'cutaway'),
            'success_box_title' => __('Riuscita', 'cutaway')
        ) );

        $paymentPage = cutawayGetThemeOption('identify_payment_page');
        $thankyouPage = cutawayGetThemeOption('identify_thank_you_page');
        $listOrdersPage = cutawayGetThemeOption('identify_list_orders_page');
        $orderDetailPage = cutawayGetThemeOption('identify_order_detail_page');
        $stripeConfig = cutawayGetThemeOption('stripe');
        if (is_page() && !empty($stripeConfig)) {
            $hasConfigLive = !empty($stripeConfig['live_publishable_key']) && !empty($stripeConfig['live_secret_key']);
            $hasConfigSandbox = !empty($stripeConfig['test_publishable_key']) && !empty($stripeConfig['test_secret_key']);
            if ($hasConfigLive || $hasConfigSandbox) {
                if ($post->ID == $paymentPage || $post->ID == $orderDetailPage) {
                    $key = $stripeConfig['payment_mode'] == 'sandbox' ? $stripeConfig['test_publishable_key'] : $stripeConfig['live_publishable_key'];

                    $paymentConfigJson = array(
                        'group_booking' => 0
                    );

                    if ($post->ID == $paymentPage) {
                        $thankyouPage = empty($thankyouPage) ? home_url('/') : get_permalink($thankyouPage);
                        $listOrdersPage = empty($listOrdersPage) ? home_url('/') : get_permalink($listOrdersPage);

                        $paymentConfigJson = array_merge($paymentConfigJson, array(
                            'ajax_create_pending_booking' => admin_url('admin-ajax.php/?action=create_pending_booking'),
                            'create_pending_booking_fail' => __('L\'acquisto non può essere pagato', 'cutaway'),
                            'created_pending_booking_fail' => __('La tua prenotazione è in attesa di essere pagata. Si prega di trovarlo nella tua lista in attesa di prenotazioni e pagare per questo', 'cutaway'),
                            'thank_you_page' => $thankyouPage,
                            'list_orders_page' => $listOrdersPage
                        ));
                    }

                    if ($post->ID == $orderDetailPage) {
                        $group = $_GET['group'];
                        $orderDetailPage = empty($orderDetailPage) ? home_url('/') : get_permalink($orderDetailPage);
                        $orderDetailPage = add_query_arg(array(
                            'group' => $group
                        ), $orderDetailPage);
                        $paymentConfigJson = array_merge($paymentConfigJson, array(
                            'thank_you_page' => $orderDetailPage,
                            'group_booking' => $group
                        ));
                    }

                    $stripeConfigJson = array(
                        'key' => $key,
                        'ajax_payment_url' => admin_url('admin-ajax.php/?action=pay_with_stripe'),
                        'payment_box_title' => __('Paga con carta di credito', 'cutaway'),
                        'payment_box_html' => cutawayGenerateStripeEnterCardHTML(),
                        'error_box_html' => __('Il pagamento con questa carta di credito non è andato a buon fine', 'cutaway'),
                        'success_box_html' => __('Il pagamento con questa carta di credito ha avuto successo', 'cutaway')
                    );

                    wp_enqueue_script('stripe-js','https://js.stripe.com/v3/');
                    $js_version = $theme_version . '.' . filemtime(get_template_directory() . '/js/stripe_payment.js');
                    wp_enqueue_script('cutaway-stripe-payment', get_template_directory_uri() . '/js/stripe_payment.js', array(), $js_version, true);
                    wp_localize_script('cutaway-stripe-payment', 'payment_config', $paymentConfigJson);
                    wp_localize_script('cutaway-stripe-payment', 'stripe_config', $stripeConfigJson);
                }
            }
        }

        $js_version = $theme_version . '.' . filemtime(get_template_directory() . '/js/theme.js');
        wp_enqueue_script('understrap-scripts', get_template_directory_uri() . '/js/theme.js', array(), $js_version, true);
        wp_enqueue_script('select2-js', get_template_directory_uri() . '/js/select2.min.js', array(), time(), true);

        wp_enqueue_script('swipe-js', get_template_directory_uri() . '/js/swipe.js', array(), time(), true);

        wp_enqueue_script('app-js', get_template_directory_uri() . '/js/app.js', array(), time(), true);

        wp_localize_script( 'app-js', 'cutaway_ajax', array( 'ajax_url' => admin_url('admin-ajax.php')) );
        wp_localize_script( 'app-js', 'cutaway_i18n', array(
            'load_bookly_services_fail' => __('I servizi di caricamento falliscono.', 'cutaway'),
            'mark_barber_favourite_success' => __('Marcato il barbiere come il tuo barbiere preferito.', 'cutaway'),
            'mark_barber_favourite_fail' => __('Mark barbiere come il tuo barbiere preferito fallire.', 'cutaway'),
            'mark_barber_unfavourite_success' => __('Marcato barbiere come barbiere sfortunato.', 'cutaway'),
            'mark_barber_unfavourite_fail' => __('Mark barbiere come il tuo barbiere sfortunato fallire.', 'cutaway'),
            'change_barber_available_booking_success' => __('Modificato lo stato della prenotazione disponibile nel barbiere', 'cutaway'),
            'change_barber_available_booking_fail' => __('Modificare lo stato della prenotazione disponibile nel barbiere non riuscita', 'cutaway'),
            'barber_config_available_time_slots_missing_data' => __('È necessario scegliere la data e i servizi per configurare il tempo di slot disponibile', 'cutaway'),
            'barber_config_available_time_slots_load_fail' => __('Impossibile ottenere l\'orario di lavoro', 'cutaway'),
            'barber_config_available_time_slots_no_time' => __('Impossibile trovare l\'orario di lavoro per la data e i servizi', 'cutaway'),
            'barber_config_available_time_slots_save_fail' => __('Impossibile aggiornare la configurazione del tempo di lavoro', 'cutaway'),
            'barber_config_available_time_slots_save_success' => __('Aggiorna la configurazione dell\'orario di lavoro con successo', 'cutaway'),
            'on' => __('Disponibile', 'cutaway'),
            'off' => __('Non Disponibile', 'cutaway'),
            'book' => __('Prenota', 'cutaway'),
            'booked' => __('Prenotato', 'cutaway')
        ) );
    }

} // endif function_exists( 'cutaway_scripts' ).

add_action('wp_enqueue_scripts', 'cutaway_scripts');
