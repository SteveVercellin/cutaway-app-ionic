<?php
namespace BooklyRatings\Backend\Components\Gutenberg\StaffRatings;

use Bookly\Lib as BooklyLib;

/**
 * Class Block
 * @package BooklyRatings\Backend\Components\Gutenberg\StaffRatings
 */
class Block extends BooklyLib\Base\Block
{
    /**
     * @inheritdoc
     */
    public static function registerBlockType()
    {
        self::enqueueScripts( array(
            'module' => array(
                'js/staff-ratings-block.js' => array( 'wp-blocks', 'wp-components', 'wp-element', 'wp-editor' ),
            ),
        ) );

        wp_localize_script( 'bookly-staff-ratings-block.js', 'BooklyStaffRatingsL10n', array(
            'block' => array(
                'title'       => 'Bookly - ' . __( 'Staff ratings', 'bookly' ),
                'description' => __( 'A custom block for displaying staff ratings', 'bookly' ),
            ),
            'comment'        => __( 'Hide comment', 'bookly' ),
        ) );

        register_block_type( 'bookly/staff-ratings-block', array(
            'editor_script' => 'bookly-staff-ratings-block.js',
        ) );
    }
}