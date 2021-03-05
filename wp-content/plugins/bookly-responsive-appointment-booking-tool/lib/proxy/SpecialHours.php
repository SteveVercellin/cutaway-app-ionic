<?php
namespace Bookly\Lib\Proxy;

use Bookly\Lib;

/**
 * Class SpecialHours
 * @package Bookly\Lib\Proxy
 *
 * @method static float adjustPrice( float $price, int $staff_id, int $service_id, int $location_id, $start_time, int $units ) Adjust price for given staff, service and time.
 * @method static bool  isNotInSpecialHour( $start_time, $end_time, int $service_id, int $staff_id, int $location_id ) Shows if period between start_date and end_date intersected with any special hour period
 */
abstract class SpecialHours extends Lib\Base\Proxy
{
}