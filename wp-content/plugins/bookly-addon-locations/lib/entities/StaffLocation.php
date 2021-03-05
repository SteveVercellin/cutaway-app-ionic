<?php
namespace BooklyLocations\Lib\Entities;

use Bookly\Lib;

/**
 * Class StaffLocation
 * @package BooklyLocations\Lib\Entities
 */
class StaffLocation extends Lib\Base\Entity
{
    /** @var  int */
    protected $staff_id;
    /** @var  int */
    protected $location_id;
    /** @var  int */
    protected $custom_services = 0;
    /** @var  int */
    protected $custom_schedule = 0;

    protected static $table = 'bookly_staff_locations';

    protected static $schema = array(
        'id'              => array( 'format' => '%d' ),
        'staff_id'        => array( 'format' => '%d', 'reference' => array( 'entity' => 'Staff', 'namespace' => '\Bookly\Lib\Entities' ) ),
        'location_id'     => array( 'format' => '%d', 'reference' => array( 'entity' => 'Location' ) ),
        'custom_services' => array( 'format' => '%d' ),
        'custom_schedule' => array( 'format' => '%d' ),
    );

    /**************************************************************************
     * Entity Fields Getters & Setters                                        *
     **************************************************************************/

    /**
     * Gets staff_id
     *
     * @return int
     */
    public function getStaffId()
    {
        return $this->staff_id;
    }

    /**
     * Sets staff
     *
     * @param Lib\Entities\Staff $staff
     * @return $this
     */
    public function setStaff( Lib\Entities\Staff $staff )
    {
        return $this->setStaffId( $staff->getId() );
    }

    /**
     * Sets staff_id
     *
     * @param int $staff_id
     * @return $this
     */
    public function setStaffId( $staff_id )
    {
        $this->staff_id = $staff_id;

        return $this;
    }

    /**
     * Gets location_id
     *
     * @return int
     */
    public function getLocationId()
    {
        return $this->location_id;
    }

    /**
     * Sets location_id
     *
     * @param int $location_id
     * @return $this
     */
    public function setLocationId( $location_id )
    {
        $this->location_id = $location_id;

        return $this;
    }

    /**
     * Gets custom_services
     *
     * @return int
     */
    public function getCustomServices()
    {
        return $this->custom_services;
    }

    /**
     * Sets custom_services
     *
     * @param int $custom_services
     * @return $this
     */
    public function setCustomServices( $custom_services )
    {
        $this->custom_services = $custom_services;

        return $this;
    }

    /**
     * Gets custom_schedule
     *
     * @return int
     */
    public function getCustomSchedule()
    {
        return $this->custom_schedule;
    }

    /**
     * Sets custom_schedule
     *
     * @param int $custom_schedule
     * @return $this
     */
    public function setCustomSchedule( $custom_schedule )
    {
        $this->custom_schedule = $custom_schedule;

        return $this;
    }

}