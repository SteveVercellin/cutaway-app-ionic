<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
use Bookly\Backend\Components;
?>
<div id="bookly-tbs" class="wrap">
    <div class="bookly-tbs-body">
        <div class="page-header text-right clearfix">
            <div class="bookly-page-title">
                <?php _e( 'Locations', 'bookly' ) ?>
            </div>
            <?php Components\Support\Buttons::render( $self::pageSlug() ) ?>
        </div>

        <div class="panel panel-default bookly-main">
            <div class="panel-body">
                <div class="form-inline bookly-margin-bottom-lg text-right">
                    <div class="form-group">
                        <button type="button" id="bookly-location-add" class="btn btn-success" data-toggle="modal" data-target="#bookly-location-modal">
                            <i class="glyphicon glyphicon-plus"></i> <?php _e( 'Add Location', 'bookly' ) ?>
                        </button>
                    </div>
                </div>
                <table class="table table-striped" id="bookly-locations" width="100%">
                    <thead>
                        <tr>
                            <th></th>
                            <th width="24"></th>
                            <th><?php _e( 'Name', 'bookly' ) ?></th>
                            <th><?php _e( 'Staff Members', 'bookly' ) ?></th>
                            <th></th>
                            <th width="16"><input type="checkbox" id="bookly-locations-check-all" /></th>
                        </tr>
                    </thead>
                </table>
                <div class="text-right">
                    <?php Components\Controls\Buttons::renderDelete() ?>
                </div>
            </div>
        </div>
    </div>

    <?php include '_modal.php' ?>
</div>