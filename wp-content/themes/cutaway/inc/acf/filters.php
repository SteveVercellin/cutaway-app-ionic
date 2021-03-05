<?php

function cutaway_load_bookly_services_in_gallery_service( $field )
{
    // Check bookly activated
    if ( is_plugin_active( 'appointment-booking/main.php' ) ) {
        $services = \Bookly\Lib\Entities\Service::query()
                ->select( 'id, title' )
                ->sortBy( 'title' )
                ->order( 'asc' )
                ->fetchArray();

        if ( !empty( $services ) ) {
            $field['choices'] = array();

            foreach ( $services as $service ) {
                $field['choices'][$service['id']] = $service['title'];
            }
        }
    }

    return $field;
}
add_filter( 'acf/load_field/name=service', 'cutaway_load_bookly_services_in_gallery_service' );

function cutaway_load_bookly_staffs_in_gallery_staff( $field )
{
    // Check bookly activated
    if ( is_plugin_active( 'appointment-booking/main.php' ) ) {
        $staffs = \Bookly\Lib\Entities\Staff::query()
                ->select( 'id, full_name' )
                ->sortBy( 'full_name' )
                ->order( 'asc' )
                ->fetchArray();

        if ( !empty( $staffs ) ) {
            $field['choices'] = array();

            foreach ( $staffs as $staff ) {
                $field['choices'][$staff['id']] = $staff['full_name'];
            }
        }
    }

    return $field;
}
add_filter( 'acf/load_field/name=staff', 'cutaway_load_bookly_staffs_in_gallery_staff' );