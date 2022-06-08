<?php
/*+*******************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ******************************************************************************/

/** Date difference between (input times) or (current time and input time)
 *
 * @param Array $a $a[0] - Input time1, $a[1] - Input time2
 * (if $a[1] is not available $a[0] = Current Time, $a[1] = Input time1)
 * @return int difference timestamp
 */
function __vt_time_diff($arr) {
	$time_operand1 = $time_operand2 = 0;
	if (count($arr) > 1) {
		$time_operand1 = $time1 = $arr[0];
		$time_operand2 = $time2 = $arr[1];
	} else {
		$time_operand1 = date('Y-m-d H:i:s'); // Current time
		$time_operand2 = $arr[0];
	}

	if (empty($time_operand1) || empty($time_operand2)) {
		return 0;
	}

	$time_operand1 = getValidDBInsertDateTimeValue($time_operand1);
	$time_operand2 = getValidDBInsertDateTimeValue($time_operand2);

	//to give the difference if it is only time field
	if (empty($time_operand1) && empty($time_operand2)) {
		$pattern = "/([01]?[0-9]|2[0-3]):[0-5][0-9]/";
		if (preg_match($pattern, $time1) && preg_match($pattern, $time2)) {
			$timeDiff = strtotime($time1) - strtotime($time2);
			return date('H:i:s', $timeDiff);
		}
	}
	return (strtotime($time_operand1) - strtotime($time_operand2));
}

function __cb_holidaydifference($arr) {
	if (count($arr) == 4) {
		$date1 = $arr[0];
		$date2 = $arr[1];
		$addsaturday = isset($arr[2]) ? $arr[2] : 1;
		$mapname = $arr[3];
	} else {
		return 0; // one or more parameter is missing
	}

	if (empty($date1) || empty($date2)) {
		return 0;
	}

	if ($addsaturday == 0) {
		$lastdow = 6;
	} else {
		$lastdow = 7;
	}

	$firstDate = new DateTime($date1);
	$lastDate = new DateTime($date2);
	if ($firstDate>$lastDate) {
		$h = $firstDate;
		$firstDate = $lastDate;
		$lastDate = $h;
	}
	$days = 0;
	$oneDay = new DateInterval('P1D');
	while ($firstDate->diff($lastDate)->days > 0) {
		$days += $firstDate->format('N') < $lastdow ? 1 : 0;
		$firstDate = $firstDate->add($oneDay);
	}

	if ($mapname != '') {
		$cbMapid = GlobalVariable::getVariable('BusinessMapping_'.$mapname, cbMap::getMapIdByName($mapname));
		if ($cbMapid != 0) {
			$cbMap = cbMap::getMapByID($cbMapid);
			$holidays = $cbMap->InformationMap()->readInformationValue();
		} else {
			$holidays = explode(',', $arr[3]);
		}
		//add holidays dates
		foreach ($holidays as $dateVal) {
			$holidayDate = new DateTime($dateVal);
			if (strtotime($dateVal) >= strtotime($date1) && strtotime($dateVal) <= strtotime($date2)) {
				$days -= $holidayDate->format('N') < $lastdow ? 1 : 0;
			}
		}
	}
	return $days;
}

function __cb_networkdays($arr) {
	$net_date1 = $arr[0];
	$net_date2 = empty($arr[1]) ? date('Y-m-d H:i:s') : $arr[1];
	$mapname = isset($arr[2]) ? $arr[2] : '';

	if (empty($net_date1) || empty($net_date2)) {
		return 0;
	}
	$firstDate = new DateTime($net_date1);
	$lastDate = new DateTime($net_date2);
	if ($firstDate>$lastDate) {
		$h = $firstDate;
		$firstDate = $lastDate;
		$lastDate = $h;
	}
	$days = 0;
	$oneDay = new DateInterval('P1D');
	while ($firstDate->diff($lastDate)->days >= 0) {
		if ($firstDate->diff($lastDate)->days == 0) {
			$days += $firstDate->format('N') < 6 ? 1 : 0;
			break;
		}
		$days += $firstDate->format('N') < 6 ? 1 : 0;
		$firstDate = $firstDate->add($oneDay);
	}

	if ($mapname != '') {
		$cbMapid = GlobalVariable::getVariable('BusinessMapping_'.$mapname, cbMap::getMapIdByName($mapname));
		if ($cbMapid != 0) {
			$cbMap = cbMap::getMapByID($cbMapid);
			$holidays = $cbMap->InformationMap()->readInformationValue();
		} else {
			$holidays = explode(',', $arr[2]);
		}
		//add holidays dates
		foreach ($holidays as $dateVal) {
			$holidayDate = new DateTime($dateVal);
			if (strtotime($dateVal) >= strtotime($net_date1) && strtotime($dateVal) <= strtotime($net_date2)) {
				$days -= $holidayDate->format('N') < 6 ? 1 : 0;
			}
		}
	}
	return $days;
}

function __cb_isHolidayDate($arr) {
	if (empty($arr[0])) {
		return false;
	}
	$saturdayisholiday = isset($arr[1]) ? $arr[1] : 1;
	$holidays = array();
	if (!empty($arr[2])) {
		$holidays = __cb_getHolidays($arr[2]);
	}
	$day_week = date('l', strtotime($arr[0]));
	if ($day_week == 'Sunday') {
		return true;
	}
	if ($saturdayisholiday == 1 && $day_week == 'Saturday') {
		return true;
	} else {
		return in_array($arr[0], $holidays);
	}
}

/**
 * Calculate the time difference (input times) or (current time and input time) and
 * convert it into number of days.
 * @param Array $a $a[0] - Input time1, $a[1] - Input time2
 * (if $a[1] is not available $a[0] = Current Time, $a[1] = Input time1)
 * @return int number of days
 */
function __vt_time_diffdays($arr) {
	$timediff  = __vt_time_diff($arr);
	return floor($timediff / (60 * 60 * 24));
}

function __cb_time_diffyears($arr) {
	$time_operand1 = $time_operand2 = 0;
	if (count($arr) > 1) {
		$time_operand1 = $arr[0];
		$time_operand2 = $arr[1];
	} else {
		$time_operand1 = date('Y-m-d H:i:s'); // Current time
		$time_operand2 = $arr[0];
	}

	if (empty($time_operand1) || empty($time_operand2)) {
		return 0;
	}

	$time_operand1 = getValidDBInsertDateTimeValue($time_operand1);
	$time_operand2 = getValidDBInsertDateTimeValue($time_operand2);

	$date1 = new DateTime($time_operand1);
	$date2 = new DateTime($time_operand2);
	$interval = $date2->diff($date1);
	return $interval->y;
}

function __cb_getWeekdayDifference($arr) {
	if (count($arr) > 1) {
		$time_operand1 = $arr[0];
		$time_operand2 = $arr[1];
	} else {
		$time_operand1 = date('Y-m-d H:i:s'); // Current time
		$time_operand2 = $arr[0];
	}

	if (empty($time_operand1) || empty($time_operand2)) {
		return 0;
	}
	$startDate = new DateTime($time_operand1);
	$endDate = new DateTime($time_operand2);
	if ($startDate>$endDate) {
		$h = $startDate;
		$startDate = $endDate;
		$endDate = $h;
	}
	$days = 0;
	$oneDay = new DateInterval('P1D');
	while ($startDate->diff($endDate)->days > 0) {
		$days += $startDate->format('N') < 6 ? 1 : 0;
		$startDate = $startDate->add($oneDay);
	}
	return $days;
}

function __vt_add_days($arr) {
	if (count($arr) > 1) {
		$baseDate = $arr[0];
		if (empty($baseDate)) {
			$baseDate = date('Y-m-d'); // Current date
		} else {
			$baseDate = DateTimeField::convertToDBFormat($baseDate);
		}
		$noOfDays = $arr[1];
	} else {
		$noOfDays = $arr[0];
		$baseDate = date('Y-m-d'); // Current date
	}
	$baseDate = strtotime($baseDate);
	return strftime('%Y-%m-%d', $baseDate + ($noOfDays * 24 * 60 * 60));
}

function __vt_sub_days($arr) {
	if (count($arr) > 1) {
		$baseDate = $arr[0];
		if (empty($baseDate)) {
			$baseDate = date('Y-m-d'); // Current date
		} else {
			$baseDate = DateTimeField::convertToDBFormat($baseDate);
		}
		$noOfDays = $arr[1];
	} else {
		$noOfDays = $arr[0];
		$baseDate = date('Y-m-d'); // Current date
	}
	$baseDate = strtotime($baseDate);
	return strftime('%Y-%m-%d', $baseDate - ($noOfDays * 24 * 60 * 60));
}

function __vt_add_months($arr) {
	if (count($arr) > 1) {
		$baseDate = $arr[0];
		if (empty($baseDate)) {
			$baseDate = date('Y-m-d'); // Current date
		} else {
			$baseDate = DateTimeField::convertToDBFormat($baseDate);
		}
		$noOfMonths = $arr[1];
	} else {
		$noOfMonths = $arr[0];
		$baseDate = date('Y-m-d'); // Current date
	}
	$baseDate = strtotime("+$noOfMonths months", strtotime($baseDate));
	return strftime('%Y-%m-%d', $baseDate);
}

function __vt_sub_months($arr) {
	if (count($arr) > 1) {
		$baseDate = $arr[0];
		if (empty($baseDate)) {
			$baseDate = date('Y-m-d'); // Current date
		} else {
			$baseDate = DateTimeField::convertToDBFormat($baseDate);
		}
		$noOfMonths = $arr[1];
	} else {
		$noOfMonths = $arr[0];
		$baseDate = date('Y-m-d'); // Current date
	}
	$baseDate = strtotime("-$noOfMonths months", strtotime($baseDate));
	return strftime('%Y-%m-%d', $baseDate);
}

function __vt_get_date($arr) {
	switch (strtolower($arr[0])) {
		case 'now':
			return date('Y-m-d H:i:s');
			break;
		case 'today':
			return date('Y-m-d');
			break;
		case 'tomorrow':
			return date('Y-m-d', strtotime('+1 day'));
			break;
		case 'yesterday':
			return date('Y-m-d', strtotime('-1 day'));
			break;
		case 'time':
			return date('H:i:s');
			break;
		default:
			return date('Y-m-d');
			break;
	}
}

function __cb_format_date($arr) {
	if (empty($arr[0])) {
		return '';
	}
	$fmt = empty($arr[1]) ? 'Y-m-d' : $arr[1];
	$arr[0] = trim($arr[0]);
	if (strpos($arr[0], ' ')>0) {
		list($dt, $ht) = explode(' ', $arr[0]);
		list($h, $i, $s) = explode(':', $ht);
	} elseif (strpos($arr[0], '-')===false) {
		$dt = date('Y-m-d');
		list($h, $i, $s) = explode(':', $arr[0]);
	} else {
		$dt = $arr[0];
		$h = $i = $s = 0;
	}
	list($y,$m,$d) = explode('-', $dt);
	$dt = mktime($h, $i, $s, $m, $d, $y);
	return date($fmt, $dt);
}

function __vt_add_time($arr) {
	if (count($arr) > 1) {
		$baseTime = $arr[0];
		$minutes = $arr[1];
	} else {
		$baseTime = date('H:i:s');
		$minutes = $arr[0];
	}
	$endTime = strtotime("+$minutes minutes", strtotime($baseTime));
	return date('H:i:s', $endTime);
}

function __vt_sub_time($arr) {
	if (count($arr) > 1) {
		$baseTime = $arr[0];
		$minutes = $arr[1];
	} else {
		$baseTime = date('H:i:s');
		$minutes = $arr[0];
	}
	$endTime = strtotime("-$minutes minutes", strtotime($baseTime));
	return date('H:i:s', $endTime);
}

/** get next date that falls on the closest given days
 * @param ISO start date "2017-06-16
 * @param comma separated string of month days "15,30"
 * @param comma separated string of ISO holiday dates
 * @param boolean 0 exclude saturday and sunday, 1 include them, default not included
 */
function __cb_next_date($arr) {
	$startDate = new DateTime($arr[0]);
	$endDate = new DateTime(__vt_add_days(array($arr[0],180))); // 180 days to make sure we catch next occurrence
	$nextDays = explode(',', $arr[1]);
	if (isset($arr[2]) && trim($arr[2])!='') { // list of holidays
		$holiday = explode(',', $arr[2]);
	} else {
		$holiday = array();
	}
	if (empty($arr[3])) { // include weekends or not
		$lastdow = 6;
	} else {
		$lastdow = 8;
	}
	$interval = new DateInterval('P1D'); // set the interval as 1 day
	$daterange = new DatePeriod($startDate, $interval, $endDate);
	$result = '';
	foreach ($daterange as $date) {
		if ($date->format('N') < $lastdow && !in_array($date->format('Y-m-d'), $holiday) && in_array($date->format('d'), $nextDays)) {
			$result = $date->format('Y-m-d');
			break;
		}
	}
	return $result;
}

/** get next laborable date that falls after the closest given days
 * @param ISO start date "2017-06-16
 * @param comma separated string of month days "15,30"
 * @param comma separated string of ISO holiday dates
 * @param boolean 0 saturday is not laborable, 1 it is, default it isn't
 */
function __cb_next_dateLaborable($arr) {
	$startDate = new DateTime($arr[0]);
	$endDate = new DateTime(__vt_add_days(array($arr[0],180))); // 180 days to make sure we catch next occurrence
	$nextDays = explode(',', $arr[1]);
	if (isset($arr[2]) && trim($arr[2])!='') { // list of holidays
		$holiday = explode(',', $arr[2]);
	} else {
		$holiday = array();
	}
	if (empty($arr[3])) { // saturday is not laborable
		$weekend = array(6,7);
	} else {
		$weekend = array(7);
	}
	$interval = new DateInterval('P1D'); // set the interval as 1 day
	$daterange = new DatePeriod($startDate, $interval, $endDate);
	$found = false;
	foreach ($daterange as $date) {
		if (in_array($date->format('d'), $nextDays)) {
			$found = $date;
			break;
		}
	}
	if ($found) {
		while (in_array($found->format('N'), $weekend) || in_array($found->format('Y-m-d'), $holiday)) {
			$found->add($interval);
		}
		return $found->format('Y-m-d');
	} else {
		return '';
	}
}

function __cb_add_workdays($arr) {
	$date = new DateTime($arr[0]);
	$numofdays = $arr[1];
	$addsaturday = isset($arr[2]) ? $arr[2] : 1;
	if ($addsaturday == 0) {
		$lastdow = 6;
	} else {
		$lastdow = 7;
	}
	if (empty($arr[3])) {
		$holidays = array();
	} else {
		$holidays = __cb_getHolidays($arr[3]);
	}
	$interval = new DateInterval('P1D');
	$x = 0;
	while ($x < $numofdays) {
		$date = $date->add($interval);
		if ($date->format('N') < $lastdow && !in_array($date->format('Y-m-d'), $holidays)) {
			$x++;
		}
	}
	return $date->format('Y-m-d');
}

function __cb_getHolidays($holidayspec) {
	$cbMapid = GlobalVariable::getVariable('BusinessMapping_'.$holidayspec, cbMap::getMapIdByName($holidayspec));
	if ($cbMapid != 0) {
		$cbMap = cbMap::getMapByID($cbMapid);
		$holidays = $cbMap->InformationMap()->readInformationValue();
	} else {
		$holidays = explode(',', $holidayspec);
	}
	return $holidays;
}

function __cb_sub_workdays($arr) {
	$date = new DateTime($arr[0]);
	$numofdays = $arr[1];
	$removesaturday = isset($arr[2]) ? $arr[2] : 0;
	if ($removesaturday == 1) {
		$lastdow = 7;
	} else {
		$lastdow = 6;
	}
	if (empty($arr[3])) {
		$holidays = array();
	} else {
		$holidays = __cb_getHolidays($arr[3]);
	}
	$interval = new DateInterval('P1D');
	$x = 0;
	while ($x < $numofdays) {
		$date = $date->sub($interval);
		if ($date->format('N') < $lastdow && !in_array($date->format('Y-m-d'), $holidays)) {
			$x++;
		}
	}
	return $date->format('Y-m-d');
}
?>