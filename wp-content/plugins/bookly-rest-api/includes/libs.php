<?php

use \BooklyAdapter\Classes\StaffMeta;

function getStaffAvailableTimes($staff, $date)
{
    if (empty($staff) || empty($date)) {
        return array();
    }

    $staffMeta = new StaffMeta($staff);
    $staffAvailableTime = $staffMeta->getStaffMeta('available_times');

    if (empty($staffAvailableTime)) {
        return array();
    }

    if (!is_array($staffAvailableTime)) {
        $staffAvailableTime = array($staffAvailableTime);
    }

    if (empty($staffAvailableTime[$date])) {
        return array();
    }

    return $staffAvailableTime[$date];
}

function updateStaffAvailableTimes($staff, $date, $availableTimes)
{
    global $staffsOptionMetaName;

    if (empty($staff) || empty($date)) {
        return false;
    }

    $staffMeta = new StaffMeta($staff);
    $staffAvailableTime = $staffMeta->getStaffMeta('available_times');

    if (empty($staffAvailableTime)) {
        $staffAvailableTime = array();
    }

    if (!is_array($staffAvailableTime)) {
        $staffAvailableTime = array($staffAvailableTime);
    }

    $staffAvailableTime[$date] = $availableTimes;

    return $staffMeta->updateStaffMeta('available_times', $staffAvailableTime);
}

function initStaffAvailableTimes($staff, $date, $staffStartTime, $staffEndTime)
{
    if (empty($staff) || empty($date) || empty($staffStartTime) || empty($staffEndTime)) {
        return false;
    }

    $start = strtotime($date . ' ' . $staffStartTime);
    $end = strtotime($date . ' ' . $staffEndTime);

    $availableTimes = array(
        $start => $end
    );

    return updateStaffAvailableTimes($staff, $date, $availableTimes);
}

function calculateStaffAvailableTimes($availableTimes, $newBegin, $newEnd)
{
    if (empty($availableTimes) || empty($newBegin) || empty($newEnd)) {
        return false;
    }

    $step = Bookly\Lib\Config::getTimeSlotLength();

    if (strlen($newBegin) <= 8) {
        $newBegin = $date . ' ' . $newBegin;
    }
    if (strlen($newEnd) <= 8) {
        $newEnd = $date . ' ' . $newEnd;
    }

    $newBegin = strtotime($newBegin);
    $newEnd = strtotime($newEnd);

    $availableTimes = array_reverse($availableTimes, true);
    $newAvailableTimes = array();

    $processed = false;
    foreach ($availableTimes as $begin => $end) {
        if ($begin <= $newBegin && !$processed) {
            if ($end > $newEnd + $step) {
                $newAvailableTimes[$newEnd + $step] = $end;
            }

            if ($newBegin - $step > $begin) {
                $newAvailableTimes[$begin] = $newBegin - $step;
            }

            $processed = true;
        } else {
            $newAvailableTimes[$begin] = $end;
        }
    }

    $newAvailableTimes = array_reverse($newAvailableTimes, true);

    return $newAvailableTimes;
}

function checkStaffCanBookOnTimeRange($staff, $date, $begin, $end)
{
    if (empty($staff) || empty($date) || empty($begin) || empty($end)) {
        return false;
    }

    $availableTimes = getStaffAvailableTimes($staff, $date);

    if (empty($availableTimes)) {
        return true;
    }

    if (strlen($begin) <= 8) {
        $begin = $date . ' ' . $begin;
    }
    if (strlen($end) <= 8) {
        $end = $date . ' ' . $end;
    }

    $begin = strtotime($begin);
    $end = strtotime($end);

    foreach ($availableTimes as $avaiBegin => $avaiEnd) {
        if ($avaiBegin <= $begin && $end <= $avaiEnd) {
            return true;
        }
    }

    return false;
}