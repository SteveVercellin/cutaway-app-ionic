<?php

add_shortcode('sc_barber_config_available_time_slots', 'cutaway_sc_barber_config_available_time_slots');
function cutaway_sc_barber_config_available_time_slots( $atts ) {
	global $user_info;

	if (!is_user_logged_in() || empty($user_info['type']) || $user_info['type'] != 'staff') {
		return;
	}

	ob_start(); ?>
        <div class="top-content">
    		<div class="wrap-page-title">
                <a href="#" class="top-left-icon show-menu-icon"><img src="<?php echo get_template_directory_uri();?>/images/registrati-icon.png"></a>
    			<h1 class="page-title"><?php echo __('Slot di tempo disponibili', 'cutaway') ?></h1>
    		</div>
        </div>

        <div class="form-your-address">
            <p><?php esc_html_e('Seleziona la data', 'cutaway'); ?></p>
            <label class="input-text your-address calendar"><input type="hidden" class="barber_config_time_slots_date" id="home-datepicker" placeholder="2018-11-20"></label>
            <div id="div_datepicker"></div>

            <div class="wrap-submit-button">
                <button type="button" class="submit-button object-global-style open-list-services"><?php echo __('Servizi di lista aperta', 'cutaway'); ?></button>
            </div>

            <div class="modal fade" id="list_services_modal" tabindex="-1" role="dialog" aria-labelledby="ListServicesModal" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLongTitle"><?php echo __('List Services', 'cutaway'); ?></h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <!-- <span aria-hidden="true">&times;</span> -->
                            </button>
                        </div>
                        <div class="modal-body"></div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-primary" data-dismiss="modal"><?php echo __('Ok', 'cutaway'); ?></button>
                            <button type="button" class="btn btn-secondary button-cancle" data-dismiss="modal"><?php echo __('Annulla', 'cutaway'); ?></button>
                        </div>
                    </div>
                </div>
            </div>
            <input type="hidden" name="services" class="list_services_selected" value="" />

            <div class="modal fade" id="staff_time_slots_modal" tabindex="-1" role="dialog" aria-labelledby="ListServicesModal" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLongTitle"><?php echo __('Configura il tempo di slot non disponibile', 'cutaway') ?></h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <!-- <span aria-hidden="true">&times;</span> -->
                            </button>
                        </div>
                        <div class="modal-body"></div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-primary" data-dismiss="modal"><?php echo __('Salvare', 'cutaway') ?></button>
                            <button type="button" class="btn btn-secondary button-cancle" data-dismiss="modal"><?php echo __('Annulla', 'cutaway') ?></button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="wrap_services_selected">
                <h3 class="page-title"><?php echo __('Servizio Selezionato', 'cutaway'); ?></h3>
                <ul></ul>
            </div>

            <div class="wrap-submit-button center-content">
                <button type="button" class="submit-button object-global-style barber-get-available-time" name="submit_find_barber_btn"><?php esc_html_e('Ottieni tempo di lavoro', 'cutaway'); ?></button>
            </div>
        </div>

	<?php
	$output = ob_get_contents();
	ob_end_clean();

	return $output;
}