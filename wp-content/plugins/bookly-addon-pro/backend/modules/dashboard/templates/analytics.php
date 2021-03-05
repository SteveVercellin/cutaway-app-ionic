<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
use Bookly\Backend\Components;
use Bookly\Lib\Utils\Common;
?>

<h4><?php esc_html_e( 'Analytics', 'bookly' ) ?></h4>

<div class="form-inline bookly-margin-bottom-lg text-right">
    <div class="pull-left">
        <div class="form-group">
            <ul id="bookly-js-filter-staff"
                data-txt-select-all="<?php esc_attr_e( 'All staff', 'bookly' ) ?>"
                data-txt-all-selected="<?php esc_attr_e( 'All staff', 'bookly' ) ?>"
                data-txt-nothing-selected="<?php esc_attr_e( 'No staff selected', 'bookly' ) ?>"
            >
                <?php foreach ( $dropdown_data['staff'] as $category_id => $category ): ?>
                    <li<?php if ( ! $category_id ) : ?> data-flatten-if-single<?php endif ?>><?php echo esc_html( $category['name'] ) ?>
                        <ul>
                            <?php foreach ( $category['items'] as $staff ) : ?>
                                <li data-value="<?php echo $staff['id'] ?>" data-selected="1">
                                    <?php echo esc_html( $staff['full_name'] ) ?>
                                </li>
                            <?php endforeach ?>
                        </ul>
                    </li>
                <?php endforeach ?>
            </ul>
        </div>
        <div class="form-group">
            <ul id="bookly-js-filter-services"
                data-icon-class="glyphicon glyphicon-tag"
                data-txt-select-all="<?php esc_attr_e( 'All services', 'bookly' ) ?>"
                data-txt-all-selected="<?php esc_attr_e( 'All services', 'bookly' ) ?>"
                data-txt-nothing-selected="<?php esc_attr_e( 'No service selected', 'bookly' ) ?>"
            >
                <li data-value="0" data-selected="1">
                    <?php esc_html_e( 'Custom', 'bookly' ) ?>
                </li>
                <?php foreach ( $dropdown_data['service'] as $category_id => $category ): ?>
                    <li<?php if ( ! $category_id ) : ?> data-flatten-if-single<?php endif ?>><?php echo esc_html( $category['name'] ) ?>
                        <ul>
                            <?php foreach ( $category['items'] as $service ) : ?>
                                <li data-value="<?php echo $service['id'] ?>" data-selected="1">
                                    <?php echo esc_html( $service['title'] ) ?>
                                </li>
                            <?php endforeach ?>
                        </ul>
                    </li>
                <?php endforeach ?>
            </ul>
        </div>
    </div>
    <div class="form-group">
        <button type="button" class="btn btn-default bookly-btn-block-xs" data-toggle="modal" data-target="#bookly-export-dialog"><i class="glyphicon glyphicon-export"></i> <?php _e( 'Export to CSV', 'bookly' ) ?></button>
    </div>
    <div class="form-group">
        <button type="button" class="btn btn-default bookly-btn-block-xs" data-toggle="modal" data-target="#bookly-print-dialog"><i class="glyphicon glyphicon-print"></i> <?php _e( 'Print', 'bookly' ) ?></button>
    </div>
</div>

<table id="bookly-analytics-table" class="table table-striped" width="100%">
    <thead>
        <tr>
            <th rowspan="2"><?php echo esc_html( Common::getTranslatedOption( 'bookly_l10n_label_employee' ) ) ?></th>
            <th rowspan="2"><?php echo esc_html( Common::getTranslatedOption( 'bookly_l10n_label_service' ) ) ?></th>
            <th colspan="5"><?php esc_html_e( 'Appointments', 'bookly' ) ?></th>
            <th colspan="2"><?php esc_html_e( 'Customers', 'bookly' ) ?></th>
            <th rowspan="2"><?php esc_html_e( 'Revenue', 'bookly' ) ?></th>
        </tr>
        <tr>
            <th><?php esc_html_e( 'Total', 'bookly' ) ?></th>
            <th><?php esc_html_e( 'Approved', 'bookly' ) ?></th>
            <th><?php esc_html_e( 'Pending', 'bookly' ) ?></th>
            <th><?php esc_html_e( 'Rejected', 'bookly' ) ?></th>
            <th><?php esc_html_e( 'Cancelled', 'bookly' ) ?></th>
            <th><?php esc_html_e( 'Total', 'bookly' ) ?></th>
            <th><?php esc_html_e( 'New', 'bookly' ) ?></th>
        </tr>
    </thead>
    <tfoot>
        <tr>
            <th colspan="2"><?php esc_html_e( 'Total', 'bookly' ) ?>:</th>
            <th></th>
            <th></th>
            <th></th>
            <th></th>
            <th></th>
            <th></th>
            <th></th>
            <th></th>
        </tr>
    </tfoot>
</table>

<div class="bookly-margin-top-md">
    <?php esc_html_e( 'Note: If payment is made for several services, then for each service you will see the entire amount paid as revenue.', 'bookly' ) ?>
</div>

<?php include '_export_dialog.php' ?>
<?php include '_print_dialog.php' ?>