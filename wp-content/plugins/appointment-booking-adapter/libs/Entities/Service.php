<?php

namespace BooklyAdapter\Entities;

use \BooklyAdapter\Base;

class Service extends Base
{
    private static $_instance = null;

    protected $entityCache = 'service';

    private function __construct()
    {
        parent::__construct();
    }

    public function loadServiceBy($conditions)
    {
        if (!$this->checkBooklyActivated()) {
            return false;
        }

        $booklyService = new \Bookly\Lib\Entities\Service();
        $loadBooklyService = $booklyService->loadBy($conditions);
        if ($loadBooklyService) {
            return $booklyService;
        }

        return false;
    }

    public function getListServices($order = array('orderby' => 'position', 'order' => 'asc'))
    {
        if (!$this->checkBooklyActivated()) {
            return false;
        }

        if (!is_array($order)) {
            $order = array($order);
        }
        $order['orderby'] = !empty($order['orderby']) ? $order['orderby'] : 'position';
        $order['order'] = !empty($order['order']) ? $order['order'] : 'asc';

        $identity = array(
            'list_services',
            $order['orderby'],
            $order['order']
        );
        $identity = implode('_', $identity) . '.json';

        $services = \Bookly\Lib\Entities\Service::query()
                ->select('id, title, info')
                ->sortBy($order['orderby'])
                ->order($order['order'])
                ->fetchArray();

        return $services;
    }

    public function getServicesDetail($services)
    {
        if (!$this->checkBooklyActivated()) {
            return array();
        }

        $services = !is_array($services) ? array($services) : $services;

        $service_query = \Bookly\Lib\Entities\Service::query()
            ->select('id, title, duration, price')
            ->sortBy('title');
        foreach($services as $s){
            $service_query->where('id', $s, 'OR');
        }

        $detail = $service_query->fetchArray();
        $detail = !empty($detail) ? $detail : array();

        return $detail;
    }

    public function calculateEndDate($service, $startDate)
    {
        if (!$this->checkBooklyActivated()) {
            return '';
        }

        $endDate = \Bookly\Lib\Slots\DatePoint::fromStr($startDate)->modify($service->getDuration())->format('Y-m-d H:i:s');

        return $endDate;
    }

    public function getServicesTimeAndPrice($services)
    {
        if (!$this->checkBooklyActivated()) {
            return array();
        }

        $service_time_query = \Bookly\Lib\Entities\Service::query()
            ->select('SUM(duration) as service_time, SUM(price) as price');

        foreach($services as $s){
            $service_time_query->where('id', $s, 'OR');
        }

        $time_service = $service_time_query->fetchRow();
        $service_price = (int) $time_service['price'];
        $time_service = (int) $time_service['service_time'] / 60;

        return array(
            'time' => $time_service,
            'price' => $service_price
        );
    }

    public static function getInstance()
    {
        if (self::$_instance == null) {
            self::$_instance = new Service();
        }

        return self::$_instance;
    }
}