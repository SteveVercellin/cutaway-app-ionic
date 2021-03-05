<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly ?>
<script type="text/javascript">
    jQuery(function ($) {
        $('#add-cancellation-confirmation').on('click', function (e) {
            e.preventDefault();
            window.send_to_editor('[bookly-cancellation-confirmation]');
            return false;
        });
    });
</script>