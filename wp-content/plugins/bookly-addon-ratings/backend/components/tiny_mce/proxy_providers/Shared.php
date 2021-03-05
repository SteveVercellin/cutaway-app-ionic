<?php
namespace BooklyRatings\Backend\Components\TinyMce\ProxyProviders;

use Bookly\Backend\Components\TinyMce\Proxy;

/**
 * Class Shared
 * @package BooklyRatings\Backend\Components\TinyMce\ProxyProviders
 */
class Shared extends Proxy\Shared
{
    /**
     * @inheritdoc
     */
    public static function renderMediaButtons( $version )
    {
        if ( $version < 3.5 ) {
            // show button for v 3.4 and below
            echo '<a href="#TB_inline?width=640&inlineId=bookly-tinymce-ratings-popup&height=650" id="add-staff-rating" title="' . esc_attr__( 'Add staff rating form', 'bookly' ) . '">' . __( 'Add staff rating form', 'bookly' ) . '</a>';
        } else {
            // display button matching new UI
            $img = '<span class="bookly-media-icon"></span> ';
            echo '<a href="#TB_inline?width=640&inlineId=bookly-tinymce-ratings-popup&height=650" id="add-staff-rating" class="thickbox button bookly-media-button" title="' . esc_attr__( 'Add staff rating form', 'bookly' ) . '">' . $img . __( 'Add staff rating form', 'bookly' ) . '</a>';
        }
    }

    /**
     * @inheritdoc
     */
    public static function renderPopup()
    {
        self::renderTemplate( 'popup' );
    }
}