<?php
namespace BooklyPro\Backend\Modules\Appearance\ProxyProviders;

use Bookly\Backend\Modules\Appearance\Proxy;

/**
 * Class Shared
 * @package BooklyPro\Backend\Modules\Appointments\ProxyProviders
 */
class Shared extends Proxy\Shared
{
    /**
     * @inheritdoc
     */
    public static function prepareOptions( array $options_to_save, array $options )
    {
        $options_to_save = array_merge( $options_to_save, array_intersect_key( $options, array_flip( array(
            'bookly_l10n_label_pay_paypal',
            'bookly_app_show_birthday',
            'bookly_app_show_address',
            'bookly_l10n_info_address',
            'bookly_l10n_info_payment_step_with_100percents_off_price',
            'bookly_l10n_invalid_day',
            'bookly_l10n_label_additional_address',
            'bookly_l10n_label_birthday_day',
            'bookly_l10n_label_birthday_month',
            'bookly_l10n_label_birthday_year',
            'bookly_l10n_label_city',
            'bookly_l10n_label_country',
            'bookly_l10n_label_postcode',
            'bookly_l10n_label_state',
            'bookly_l10n_label_street',
            'bookly_l10n_label_street_number',
            'bookly_l10n_required_additional_address',
            'bookly_l10n_required_city',
            'bookly_l10n_required_country',
            'bookly_l10n_required_day',
            'bookly_l10n_required_month',
            'bookly_l10n_required_postcode',
            'bookly_l10n_required_state',
            'bookly_l10n_required_street',
            'bookly_l10n_required_street_number',
            'bookly_l10n_required_year',
        ) ) ) );

        return $options_to_save;
    }
}