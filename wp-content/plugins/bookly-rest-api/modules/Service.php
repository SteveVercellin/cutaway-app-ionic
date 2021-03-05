<?php

class REST_Services_Controller extends BaseRestModule {
    // Here initialize our namespace and resource name.
    public function __construct() {
        $this->namespace     = '/cutaway/v1';
        $this->resource_name = 'services';
        $this->default_service_img = plugins_url( "bookly-rest-api/assets/images/default_service_img.png");
    }

    // Register our routes.
    public function register_routes() {
        $getAllServices = $this->getRestServiceGetAllServices();

        register_rest_route( $this->namespace, '/' . $this->resource_name . '/getAllServices', array(
            // Here we register the readable endpoint for collections.
            array(
                'methods'   => $getAllServices['method'],
                'callback'  => array( $this, 'getAllServices' ),
                'permission_callback' => array( $this, 'get_items_permissions_check' )
            )
        ) );
        register_rest_route( $this->namespace, '/' . $this->resource_name . '/getAllServices/test', array(
            // Here we register the readable endpoint for collections.
            array(
                'methods'   => $getAllServices['method'],
                'callback'  => array( $this, 'getAllServices' ),
                'permission_callback' => array( $this, 'get_items_permissions_check_test' )
            )
        ) );
    }

    public function getRestServiceGetAllServices()
    {
        return array(
            'method' => WP_REST_Server::READABLE,
            'url' => $this->namespace . '/' . $this->resource_name . '/getAllServices'
        );
    }

    public function getAllServices($request){
        $order = array(
            'orderby' => $request['orderby'],
            'order' => $request['order']
        );

        $services = $this->getListServices($order);

        $data = array();

        $data['services'] = $services;

        return wordpress_rest_format_response_success( $data );
    }

    public function getListServices($order = array())
    {
        if (!class_exists('\\BooklyAdapter\\Entities\\Service')) {
            return array();
        }

        $booklyServiceAdapter = \BooklyAdapter\Entities\Service::getInstance();
        $services = $booklyServiceAdapter->getListServices($order);

        if( !empty( $services ) ){
            foreach ($services as &$service) {
                $serviceImg = $this->default_service_img;
                $serviceGalleries = $this->getServiceGallery($service['id']);
                if (!empty($serviceGalleries)) {
                    $serviceImg = $serviceGalleries[0];
                }

                $service['image'] = $serviceImg;
            }
        }

        return $services;
    }

    private function getServiceGallery($service)
    {
        $gallery = array();

        if (function_exists('get_service_gallery')) {
            $servicePost = get_service_gallery($service);
            if (!empty($servicePost) && function_exists('get_field')) {
                $galImages = get_field('gallery', $servicePost);
                if (!empty($galImages)) {
                    foreach ($galImages as $image) {
                        $gallery[] = $image['sizes']['large'];
                    }
                }
            }
        }

        return $gallery;
    }
}

// Function to register our new routes from the controller.
function prefix_register_services_rest_routes() {
    $controller = new REST_Services_Controller();
    $controller->register_routes();
}

add_action( 'rest_api_init', 'prefix_register_services_rest_routes' );