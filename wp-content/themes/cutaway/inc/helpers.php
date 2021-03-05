<?php

function cutawayGetThemeOption($optionName, $default = '')
{
    if (empty($optionName) || !function_exists('get_field')) {
        return $default;
    }

    return get_field($optionName, 'option');
}

function cutawayGetBooklyCurrencySetting()
{
    return get_option('bookly_pmt_currency', 'EUR');
}

function cutawayGetBooklyCurrencySymbol()
{
    if (!class_exists('\\Bookly\\Lib\\Utils\\Price')) {
        return '&euro;';
    }

    $currency = cutawayGetBooklyCurrencySetting();
    $currencies = \Bookly\Lib\Utils\Price::getCurrencies();
    if (empty($currencies) || empty($currencies[$currency])) {
        return '&euro;';
    }

    return $currencies[$currency]['symbol'];
}

function cutawayGetCurrentBookSummary()
{
    $staffBookSummary = array();
    $dataBook = cutawayGetCustomerDataActiveBook();

    if ( class_exists('REST_Shop_Controller') && !empty($dataBook) ) {
        $staff = $dataBook['staff'];
        $date = $dataBook['date'];
        $services = $dataBook['services'];
        $time = $dataBook['time'];

        $controller = new REST_Shop_Controller();
        $staffBookSummary = $controller->processGetStaffBookSummary(array(
            'staff' => $staff,
            'service' => $services,
            'date' => $date,
            'time' => $time
        ));
    }

    return $staffBookSummary;
}

function cutawaySetCustomerDataActiveBook($dataBook)
{
    $_SESSION['data_book'] = $dataBook;

    return true;
}

function cutawayGetCustomerDataActiveBook()
{
    return !empty($_SESSION['data_book']) ? $_SESSION['data_book'] : array();
}

function cutawayRemoveCustomerDataActiveBook()
{
    unset($_SESSION['data_book']);
}

function cutawayGetBooklyTotalPriceServices($services)
{
    if (empty($services) || !class_exists('\\BooklyAdapter\\Entities\\Service')) {
        return 0;
    }

    $booklyAdapterService = \BooklyAdapter\Entities\Service::getInstance();
    $data = $booklyAdapterService->getServicesTimeAndPrice($services);
    if (empty($data) || empty($data['price'])) {
        return 0;
    }

    return $data['price'];
}

function cutawayGenerateDataBookFromAppointmentGroup($appGroup)
{
    if (empty($appGroup) || !function_exists('formatTime')) {
        return array();
    }

    $staff = $appGroup['barber']['id'];
    $date = $appGroup['date'];
    $date = str_replace('-', '/', $date);
    $time = $appGroup['time'];
    $time = explode(':', $time);
    $time = array_map(function ($item) {
        return intval($item);
    }, $time);
    $time = formatTime($time);

    $services = array();
    $price = 0;
    foreach ($appGroup['services'] as $service) {
        $services[] = $service['id'];
        $price += (int) $service['price'];
    }
    $services = implode(',', $services);

    return compact('staff', 'date', 'time', 'services', 'price');
}

function cutawayGenerateDescriptionPayment($dataBook = array())
{
    $dataBook = !empty($dataBook) ? $dataBook : cutawayGetCustomerDataActiveBook();
    if (empty($dataBook) || !class_exists('\\BooklyAdapter\\Entities\\Service') || !class_exists('\\BooklyAdapter\\Entities\\Staff')) {
        return '';
    }

    $booklyAdapterStaff = \BooklyAdapter\Entities\Staff::getInstance();
    $booklyAdapterService = \BooklyAdapter\Entities\Service::getInstance();

    $services = explode(',', $dataBook['services']);
    $staff = $dataBook['staff'];
    $date = $dataBook['date'];
    $time = $dataBook['time'];

    $services = $booklyAdapterService->getServicesDetail($services);
    $barber = $booklyAdapterStaff->getStaffsDetail($staff);

    if (empty($services) || empty($barber)) {
        return '';
    }

    $barber = $barber[0];

    $description = array();
    $servicesTitle = array();
    foreach ($services as $service) {
        $servicesTitle[] = $service['title'];
    }
    $description[] = implode(' + ', $servicesTitle);
    $description[] = $barber['full_name'];
    $dateParse = explode('/', $date);
    $description[] = $dateParse[1] . '/' . $dateParse[2] . '/' . $dateParse[0];
    $description[] = $time;
    $description = implode(' * ', $description);

    return $description;
}

function cutawayGeneratePaypalForm($override = array()) {
    $dataBook = cutawayGetCustomerDataActiveBook();
    $description = cutawayGenerateDescriptionPayment();
    $paypalConfig = cutawayGetThemeOption('paypal');
    $listOrdersPage = cutawayGetThemeOption('identify_list_orders_page');
    $thankyouPage = cutawayGetThemeOption('identify_thank_you_page');
    $currentUser = wp_get_current_user();

    if (
        empty($paypalConfig) ||
        !class_exists('CutawayEncryptDecrypt') ||
        empty($currentUser->ID)
    ) {
        return '';
    }

    $dataBook = !empty($override['data_book']) ? $override['data_book'] : cutawayGetCustomerDataActiveBook();
    if (empty($dataBook)) {
        return;
    }
    $description = cutawayGenerateDescriptionPayment($dataBook);
    if (empty($description)) {
        return;
    }

    $callback = cutaway_generate_http_cron_link('handle_paypal_ipn');
    if (!empty($override['callback'])) {
        $callback = $override['callback'];
    }

    $return = home_url('/');
    if (!empty($override['return'])) {
        $return = $override['return'];
    } elseif (!empty($thankyouPage)) {
        $return = get_permalink($thankyouPage);
    }

    $cancelReturn = home_url('/');
    if (!empty($override['cancel_return'])) {
        $cancelReturn = $override['cancel_return'];
    } elseif (!empty($listOrdersPage)) {
        $cancelReturn = get_permalink($listOrdersPage);
    }

    $atts = array();
    $paypalEmail = '';
    $id = uniqid();

    if ($paypalConfig['payment_mode'] == 'sandbox') {
        $atts['env'] = "sandbox";
        $paypalEmail = $paypalConfig['test_merchant_email'];
    } else {
        $paypalEmail = $paypalConfig['live_merchant_email'];
    }
    $atts['callback'] = $callback;
    $atts['return'] = $return;
    $atts['cancel_return'] = $cancelReturn;
    $atts['currency'] = cutawayGetBooklyCurrencySetting();
    $atts['name'] = $description;
    $atts['amount'] = $dataBook['price'];

    $button_code = '<div id="'.$id.'" style="display: none">';
    $button_code .= '<script async src="' . get_template_directory_uri() . '/js/paypal-button.min.js?merchant=' . $paypalEmail . '"';
    foreach ($atts as $key => $value) {
        $button_code .= ' data-' . $key . '="' . $value . '"';
    }
    $button_code .= '></script>';
    $button_code .= '</div>';

    return $button_code;
}

function cutaway_barber_detail_box($params) {
    extract($params);
    $html = '
        <a href="' . $url . '" >
            <div class="barber-detail">
                <div class="barber-thumbnail">
                    <img src="' . $avatar . '">
                </div>
                <div class="barber-info">
                    <div class="name">' . $full_name . '</div>
                    <div class="location">' . $location . '</div>
                    <div class="price">' . $price . '</div>
                    <div class="rating star-' . $starNumber . '">
                        <span>&#9733;</span><span>&#9733;</span><span>&#9733;</span><span>&#9733;</span><span>&#9733;</span>
                        <em> (' . $ratings . ' ratings)</em>
                    </div>
                </div>
            </div>
        </a>
    ';
    return $html;
}

function cutaway_subBlockContent($params) {
    extract($params);
    $html = $url ? '<a href="' . $url . '">' : '';
    $html .= '<div class="sub-block-content object-global-style ' . $class . '">';
    $html .= $left ? '<div class="left">' . $left_content . '</div>' : '';
    $html .= $right ? '<div class="right">' . $right_content . '</div>' : '';
    $html .= '</div>';
    $html .= $url ? '</a>' : '';

    return $html;
}

function cutaway_accordion($params) {
    extract($params);
    $i = 1;

    $html = '<div class="wrap-accordion ' . $class . '">';
    $html .= '<div id="accordion">';

    foreach ($accordions as $card) {
        $html .= '
            <div class="card">
                <div class="card-header">
                    <a class="card-link collapsed" data-toggle="collapse" href="#collapse-' . $i . '">' . $card['header'] . '</a>
                </div>
                <div id="collapse-' . $i . '" class="collapse" data-parent="#accordion">
                    <div class="card-body">' . $card['content'] . '</div>
                </div>
            </div>';

        $i++;
    }

    $html .= '</div>';
    $html .= '</div>';

    return $html;
}

function cutawayGetCurrentUserBooklyType()
{
    global $user_info;

    if (!is_user_logged_in() || empty($user_info['type'])) {
        return '';
    }

    return $user_info['type'];
}