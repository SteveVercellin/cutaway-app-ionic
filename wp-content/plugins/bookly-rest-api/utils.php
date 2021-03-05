<?php

function addTime($start_time, $end_time){
	$start_time = array_map('intval', explode(':', $start_time));
	$end_time = array_map('intval', explode(':', $end_time));
	$result = array();
	$result[0] = $start_time[0] + $end_time[0];
	$result[1] = $start_time[1] + $end_time[1];
	$result[2] = "00";
	while($result[1] >= 60){
		$result[0] += 1;
		$result[1] -= 60;
	}
	return implode(':', $result);
}

function addMinute($time, $minute){
	if(is_array($time)){
		$time[1] += $minute;
		while($time[1] >= 60){
			$time[0] += 1;
			$time[1] -= 60;
		}
		return $time;
	}
	return null;
}

function formatTime($time){
	$abbreviation = "";
	$hour = "";
	$minute = "";
	if(is_array($time)){
		if($time[0] >= 12){
			$abbreviation = " pm";
			$time[0] -= 12;
		} else {
			$abbreviation = " am";
		}
		// Fix 12 hour clock
		// Digital watches: Midnight -> 12:00 AM ; Noon -> 12:00 PM
		if ($time[0] == 0) {
			$time[0] = 12;
		}

		if($time[0] < 10){
			$hour =  "0" . (string) $time[0];
		} else {
			$hour = (string) $time[0];
		}
		if($time[1] < 10){
			$minute = "0" . (string) $time[1];
		} else {
			$minute = (string) $time[1];
		}
		return $hour . ":" . $minute . $abbreviation;
	}
	return "";
}

function convertTimeFrom12To24($timeString)
{
	if (empty($timeString)) {
		return "";
	}

	$timeParse = preg_split('~\s+~', $timeString);
	if (count($timeParse) != 2) {
		return $timeString;
	}

	$time = $timeParse[0];
	$timeArr = array_map('intval', explode(':', $time));
	$abbreviation = $timeParse[1];

	$hour = "";
	$minute = "";

	if ($abbreviation == 'pm' && $timeArr[0] < 12) {
		$timeArr[0] += 12;
	} elseif ($abbreviation == 'am' && $timeArr[0] == 12) {
		$timeArr[0] -= 12;
	}

	if($timeArr[0] < 10){
		$hour =  "0" . (string) $timeArr[0];
	} else {
		$hour = (string) $timeArr[0];
	}
	if($timeArr[1] < 10){
		$minute = "0" . (string) $timeArr[1];
	} else {
		$minute = (string) $timeArr[1];
	}

	return $hour . ":" . $minute;
}

function parseDataPaginationFromRequest($request)
{
	$dataPaginate = array(
		'page' => $request['page'],
		'limit' => $request['limit']
	);
	$dataPaginate = array_filter($dataPaginate);
	$dataPaginate = wp_parse_args($dataPaginate, array(
		'page' => 1,
		'limit' => 20
	));

	return $dataPaginate;
}

function parseMysqlDateTime( $mysqlDateTime )
{
	if ( empty( $mysqlDateTime ) ) {
		return $mysqlDateTime;
	}

	$parse = explode( ' ', $mysqlDateTime );

	$return = array(
		'day' => 0,
		'month' => 0,
		'year' => 0,
		'time' => ''
	);

	$parseDate = explode( '-', $parse[0] );
	if ( !empty( $parseDate ) && count( $parseDate ) == 3 ) {
		$return['day'] = (int) $parseDate[2];
		$return['month'] = (int) $parseDate[1];
		$return['year'] = (int) $parseDate[0];
	}

	if ( !empty( $parse[1] ) ) {
		$return['time'] = formatTime( array_map( function ( $item ) {
			return (int) $item;
		}, explode( ':', $parse[1] ) ) );
	}

	return $return;
}