<?php

namespace BooklyAdapter\Entities;

use \BooklyAdapter\Base;

class Location extends Base
{
    private static $_instance = null;

    protected $entityCache = 'location';

    private function __construct()
    {
        parent::__construct();
    }

    public function loadLocationBy($conditions)
    {
        if (!$this->checkBooklyLocationAddonActivated()) {
            return false;
        }

        $booklyLocation = new \BooklyLocations\Lib\Entities\Location();
        $loadBooklyLocation = $booklyLocation->loadBy($conditions);
        if ($loadBooklyLocation) {
            return $booklyLocation;
        }

        return false;
    }

    public function getListLocations(
        $args = array(
            'lat' => 0,
            'lng' => 0,
            'distance' => 0,
            'orderby' => 'name',
            'order' => 'asc'
        )
    )
    {
        if (!$this->checkBooklyLocationAddonActivated()) {
            return false;
        }

        $orderby = !empty($args['orderby']) ? $args['orderby'] : 'name';
        $order = !empty($args['order']) ? $args['order'] : 'asc';

        $identity = array(
            'list_locations',
            $orderby,
            $order
        );
        $identity = implode('_', $identity) . '.json';

        $locations = \BooklyLocations\Lib\Entities\Location::query();
        /*if (!empty($args['lat']) && !empty($args['lng']) && !empty($args['distance'])) {
            $select = 'id, name';
            $select .= ", (
                6371 *
                acos(
                    cos( radians( {$args['lat']} ) ) *
                    cos( radians( `lat` ) ) *
                    cos(
                        radians( `lng` ) - radians( {$args['lng']} )
                    ) +
                    sin(radians({$args['lat']})) *
                    sin(radians(`lat`))
                )
            ) as `distance`";
            $locations->select($select);
            $locations->havingRaw('distance <= %d', array($args['distance']));
        } else {
            $locations->select('id, name');
        }*/
        $locations->select('id, name');
        $locations->sortBy($orderby)
            ->order($order);

        $locations = $locations->fetchArray();

        return $locations;
    }

    public static function getInstance()
    {
        if (self::$_instance == null) {
            self::$_instance = new Location();
        }

        return self::$_instance;
    }
}