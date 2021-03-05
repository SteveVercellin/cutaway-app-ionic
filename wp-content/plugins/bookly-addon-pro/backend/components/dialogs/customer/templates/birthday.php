<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly ?>
<div class="form-group">
    <label for="birthday"><?php esc_html_e( 'Date of birth', 'bookly' ) ?></label>
    <input class="form-control" type="text" ng-model=form.birthday id="birthday"
           ui-date="datePickerOptions" ui-date-format="yy-mm-dd" autocomplete="off" />
</div>