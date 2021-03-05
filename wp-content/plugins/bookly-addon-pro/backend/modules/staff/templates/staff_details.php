<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
use Bookly\Lib as BooklyLib;
?>
<div class="form-group">
    <label for="bookly-category"><?php esc_html_e( 'Category', 'bookly' ) ?></label>
    <select name="category_id" class="form-control" id="bookly-category">
        <?php foreach ( $categories as $category ) : ?>
            <option value="<?php echo $category['id'] ?>" <?php selected( $category['id'], $staff->getCategoryId() ) ?>><?php echo $category['name'] ?></option>
        <?php endforeach ?>
    </select>
</div>
<div class="form-group">
    <label for="bookly-working-time-limit"><?php esc_html_e( 'Limit working hours per day', 'bookly' ) ?></label>
    <p class="help-block"><?php esc_html_e( 'This setting allows limiting the total time occupied by bookings per day for staff member. Padding time is not included.', 'bookly' ) ?></p>
    <select name="working_time_limit" class="form-control" id="bookly-working-time-limit">
        <option value=""><?php esc_html_e( 'Unlimited', 'bookly' ) ?></option>
        <?php for ( $i = 1; $i < 24; $i ++ ) : ?>
            <option value="<?php echo $i * 3600 ?>" <?php selected( $i * 3600, $staff->getWorkingTimeLimit() ) ?>><?php echo BooklyLib\Utils\DateTime::secondsToInterval( $i * 3600 ) ?></option>
        <?php endfor ?>
    </select>
</div>
