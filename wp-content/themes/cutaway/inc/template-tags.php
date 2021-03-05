<?php
/**
 * Custom template tags for this theme.
 *
 * Eventually, some of the functionality here could be replaced by core features.
 *
 * @package understrap
 */

function cutawayGenerateStripeEnterCardHTML()
{
    $html = '<div class="stripe-payment">';

    $html .= '
        <div class="stripe-payment__row">
            <label>' . __('Numero di carta', 'cutaway') . '</label>
            <div class="stripe-payment__row__input" id="stripe-card-number"></div>
        </div>
        <div class="stripe-payment__row">
            <label>' . __('Scadenza carta', 'cutaway') . ' (MM/YY)</label>
            <div class="stripe-payment__row__input" id="stripe-card-expiration"></div>
        </div>
        <div class="stripe-payment__row">
            <label>' . __('CVC di carta', 'cutaway') . '</label>
            <div class="stripe-payment__row__input" id="stripe-card-cvc"></div>
        </div>
        <div class="stripe-payment__row">
            <label>' . __('Codice postale', 'cutaway') . '</label>
            <div class="stripe-payment__row__input" id="stripe-postal-code"></div>
        </div>
    ';

    $html .= '</div>';

    return $html;
}