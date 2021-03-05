<?php
class REST_Locations_Controller extends BaseRestModule {


    // Here initialize our namespace and resource name.
    public function __construct() {
        $this->namespace     = '/cutaway/v1';
        $this->resource_name = 'locations';
    }

    // Register our routes.
    public function register_routes() {
        $getLocationsService = $this->getRestServiceGetLocations();

        register_rest_route( $this->namespace, '/' . $this->resource_name . '/getLocations', array(
            // Here we register the readable endpoint for collections.
            array(
                'methods'   => $getLocationsService['method'],
                'callback'  => array( $this, 'getLocations' ),
                'permission_callback' => array( $this, 'get_items_permissions_check' )
            )
        ) );
        register_rest_route( $this->namespace, '/' . $this->resource_name . '/getLocations/test', array(
            // Here we register the readable endpoint for collections.
            array(
                'methods'   => $getLocationsService['method'],
                'callback'  => array( $this, 'getLocations' ),
                'permission_callback' => array( $this, 'get_items_permissions_check_test' )
            )
        ) );
    }

    public function getRestServiceGetLocations()
    {
        return array(
            'method' => WP_REST_Server::READABLE,
            'url' => $this->namespace . '/' . $this->resource_name . '/getLocations'
        );
    }

    public function getLocations($request){
        $currentUser = $this->getCurrentUser();
        if (empty($currentUser->ID)) {
            return $this->generateAuthenticateFailError();
        }

        $order = $request['order'];
        if(empty($order)){
            $order = "asc";
        }

        $lat = !empty($request['lat']) ? $request['lat'] : 0;
        $lng = !empty($request['lng']) ? $request['lng'] : 0;
        $distance = !empty($request['distance']) ? $request['distance'] : 0;

        $locations = array();
        if (class_exists('\\BooklyAdapter\\Entities\\Location')) {
            $booklyLocationAdapter = \BooklyAdapter\Entities\Location::getInstance();
            $locations = $booklyLocationAdapter->getListLocations(
                array('lat' => $lat, 'lng' => $lng, 'distance' => $distance, 'order' => $order)
            );
        }

        $data['locations'] = $locations;

        return wordpress_rest_format_response_success( $data );
    }
}

// Function to register our new routes from the controller.
function prefix_register_locations_rest_routes() {
    $controller = new REST_Locations_Controller();
    $controller->register_routes();
}

add_action( 'rest_api_init', 'prefix_register_locations_rest_routes' );