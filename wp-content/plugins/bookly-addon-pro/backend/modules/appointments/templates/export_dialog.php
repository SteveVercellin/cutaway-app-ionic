<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
use Bookly\Backend\Components\Controls\Buttons;
use Bookly\Backend\Components\Controls\Inputs;
use Bookly\Lib\Utils\Common;
use Bookly\Lib\Config;
use Bookly\Backend\Modules\Appointments\Proxy as AppointmentsProxy;
?>
<div id="bookly-export-dialog" class="modal fade" tabindex=-1 role="dialog">
    <div class="modal-dialog">
        <form action="<?php echo admin_url( 'admin-ajax.php?action=bookly_pro_export_appointments' ) ?>" method="POST">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                    <div class="modal-title h2"><?php esc_html_e( 'Export to CSV', 'bookly' ) ?></div>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="bookly-csv-delimiter"><?php esc_html_e( 'Delimiter', 'bookly' ) ?></label>
                        <select id="bookly-csv-delimiter" class="form-control" name="delimiter">
                            <option value=","><?php esc_html_e( 'Comma (,)', 'bookly' ) ?></option>
                            <option value=";"><?php esc_html_e( 'Semicolon (;)', 'bookly' ) ?></option>
                        </select>
                    </div>
                    <div class="form-group">
                        <div class="checkbox"><label><input checked name="exp[id]" type="checkbox"/><?php esc_html_e( 'No.', 'bookly' ) ?></label></div>
                        <div class="checkbox"><label><input checked name="exp[start_date]" type="checkbox"/><?php esc_html_e( 'Appointment Date', 'bookly' ) ?></label></div>
                        <div class="checkbox"><label><input checked name="exp[staff_name]" type="checkbox"/><?php echo Common::getTranslatedOption( 'bookly_l10n_label_employee' ) ?></label></div>
                        <div class="checkbox"><label><input checked name="exp[customer_full_name]" type="checkbox"/><?php esc_html_e( 'Customer Name', 'bookly' ) ?></label></div>
                        <div class="checkbox"><label><input checked name="exp[customer_phone]" type="checkbox"/><?php esc_html_e( 'Customer Phone', 'bookly' ) ?></label></div>
                        <div class="checkbox"><label><input checked name="exp[customer_email]" type="checkbox"/><?php esc_html_e( 'Customer Email', 'bookly' ) ?></label></div>
                        <?php AppointmentsProxy\GroupBooking::renderExport() ?>
                        <div class="checkbox"><label><input checked name="exp[service_title]" type="checkbox"/><?php echo Common::getTranslatedOption( 'bookly_l10n_label_service' ) ?></label></div>
                        <div class="checkbox"><label><input checked name="exp[service_duration]" type="checkbox"/><?php esc_html_e( 'Duration', 'bookly' ) ?></label></div>
                        <div class="checkbox"><label><input checked name="exp[status]" type="checkbox"/><?php esc_html_e( 'Status', 'bookly' ) ?></label></div>
                        <div class="checkbox"><label><input checked name="exp[payment_raw_title]" type="checkbox"/><?php esc_html_e( 'Payment', 'bookly' ) ?></label></div>
                        <?php AppointmentsProxy\Ratings::renderExport() ?>
                        <?php if ( Config::showNotes() ) : ?>
                            <div class="checkbox"><label><input checked name="exp[notes]" type="checkbox"/><?php echo esc_html( Common::getTranslatedOption( 'bookly_l10n_label_notes' ) ) ?></label></div>
                        <?php endif ?>
                        <?php foreach ( $custom_fields as $custom_field ) : ?>
                            <div class="checkbox"><label><input checked name="exp[<?php echo $custom_field->id ?>]" type="checkbox"/><?php echo $custom_field->label ?></label></div>
                        <?php endforeach ?>
                    </div>
                </div>
                <div class="modal-footer">
                    <input type="hidden" name="filter"/>
                    <?php Inputs::renderCsrf() ?>
                    <?php Buttons::renderSubmit( null, null, __( 'Export to CSV', 'bookly' ) ) ?>
                </div>
            </div>
        </form>
    </div>
</div>