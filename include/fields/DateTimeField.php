<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/
require_once 'include/utils/utils.php';

class DateTimeField {

	protected static $databaseTimeZone = null;
	protected $datetime;

	/**
	 *
	 * @param datetime
	 */
	public function __construct($value = '') {
		if (empty($value)) {
			$value = date('Y-m-d H:i:s');
		}
		$this->date = null;
		$this->time = null;
		$this->datetime = $value;
	}

	/** Function to set date values compatible to database (YY_MM_DD)
	 * @param Users
	 * @return string insert date
	 */
	public function getDBInsertDateValue($user = null) {
		global $log;
		$log->debug('> getDBInsertDateValue '.$this->datetime);
		$value = explode(' ', $this->datetime);
		if (count($value) == 2) {
			$value[0] = self::convertToUserFormat($value[0], $user);
		}

		if (!empty($value[1])) {
			$date = self::convertToDBTimeZone($this->datetime, $user);
			$insert_date = $date->format('Y-m-d');
		} else {
			$insert_date = self::convertToDBFormat($value[0], $user);
		}
		$log->debug('< getDBInsertDateValue');
		return $insert_date;
	}

	/**
	 *
	 * @param Users
	 * @return string
	 */
	public function getDBInsertDateTimeValue($user = null) {
		return $this->getDBInsertDateValue($user) . ' ' . $this->getDBInsertTimeValue($user);
	}

	private function getDTComponents($datetime, $user) {
		$format = $user->date_format;
		if (empty($format)) {
			$format = 'dd-mm-yyyy';
		}
		if (strpos($datetime, ' ')>0) {
			list($date,$time) = explode(' ', $datetime);
		} else {
			$date = $datetime;
			$time = '';
		}
		list($y, $m, $d) = explode('-', $date);
		if (strlen($y) != 4) {
			if ($format == 'dd-mm-yyyy') {
				list($d, $m, $y) = explode('-', $date);
			} elseif ($format == 'mm-dd-yyyy') {
				list($m, $d, $y) = explode('-', $date);
			} elseif ($format == 'yyyy-mm-dd') {
				list($y, $m, $d) = explode('-', $date);
			}
		}
		$components = array(
			'year' => $y,
			'month' => $m,
			'day' => $d,
		);

		$tcomponents = explode(':', $time);
		if (isset($tcomponents[0])) {
			$components['hour'] = $tcomponents[0];
		}
		if (isset($tcomponents[1])) {
			$components['minute'] = $tcomponents[1];
		}
		if (isset($tcomponents[2])) {
			$components['second'] = $tcomponents[2];
		}
		return $components;
	}

	public function getDBInsertDateTimeValueComponents($user = null) {
		global $current_user;
		if (empty($user)) {
			$user = $current_user;
		}
		return $this->getDTComponents($this->getDBInsertDateTimeValue($user), $user);
	}

	public function getDisplayDateTimeValue($user = null) {
		return $this->getDisplayDate($user) . ' ' . $this->getDisplayTime($user);
	}

	public function getDisplayDateTimeValueComponents($user = null) {
		global $current_user;
		if (empty($user)) {
			$user = $current_user;
		}
		return $this->getDTComponents($this->getDisplayDateTimeValue($user), $user);
	}

	/**
	 * @param mixed $datetime in 24 hour format
	 * @param string $fmt 12 or 24
	 * @return string time formatted as indicated by $fmt
	 */
	public static function formatUserTimeString($datetime, $fmt) {
		if (empty($fmt)) {
			$fmt = '24';
		}
		if (is_object($datetime)) {
			$hr = $datetime->hour;
			$min = $datetime->minute;
		} elseif (is_array($datetime)) {
			$hr = $datetime['hour'];
			$min = $datetime['minute'];
		} else {
			if (strpos($datetime, ' ')>0) {
				list($dt,$tm) = explode(' ', $datetime);
			} else {
				$tm = $datetime;
			}
			list($hr,$min) = explode(':', $tm);
		}
		if ($fmt != '24') {
			$am_pm = array('AM', 'PM');
			$hour = self::twoDigit($hr%12);
			if ($hour == 0) {
				$hour = 12;
			}
			$timeStr = $hour.':'.self::twoDigit($min).$am_pm[($hr/12)%2];
		} else {
			$timeStr = self::twoDigit($hr).':'.self::twoDigit($min);
		}
		return $timeStr;
	}

	/**
	 * @param string $datetime in $fmt hour format
	 * @param string $fmt am | pm | 24
	 * @return string time formatted as 24h
	 */
	public static function formatDatebaseTimeString($datetime, $fmt) {
		if (empty($datetime) || trim($datetime)=='') {
			return '';
		}
		if (strpos($datetime, ' ')>0) {
			list($dt,$tm) = explode(' ', $datetime);
		} else {
			$dt = '';
			$tm = $datetime;
		}
		if (substr_count($tm, ':')==1) {
			list($hr,$min) = explode(':', $tm);
			$sec = 0;
		} else {
			list($hr,$min,$sec) = explode(':', $tm);
		}
		$fmt = strtolower(trim($fmt));
		if (empty($fmt) || $fmt == '24' || ($fmt != 'am' && $fmt != 'pm')) {
			return trim($dt . ' ' . self::twoDigit($hr) . ':' . self::twoDigit($min) . ':' . self::twoDigit($sec));
		}
		if ($fmt == 'am') {
			if ($hr == '12') {
				$hour = '00';
			} else {
				$hour = self::twoDigit($hr);
			}
		} else {
			if ((int)$hr > 12) {
				$hr = $hr - 12;
			}
			if ($hr != '12') {
				$hour = self::twoDigit((int)$hr+12);
			} else {
				$hour = self::twoDigit((int)$hr);
			}
		}
		return trim($dt . ' ' . $hour . ':' . self::twoDigit($min) . ':' . self::twoDigit($sec));
	}

	/**
	 * @param number
	 * @return string
	 */
	public static function twoDigit($no) {
		$no = trim($no);
		if ((int)$no < 10 && strlen($no) < 2) {
			$no = '0'.$no;
		}
		return substr($no, 0, 2);
	}

	/**
	 *
	 * @global Users $current_user
	 * @param string $date
	 * @param Users $user
	 * @return string
	 */
	public static function convertToDBFormat($date, $user = null) {
		global $current_user;
		if (empty($user)) {
			$user = $current_user;
		}

		$format = $user->date_format;
		if (empty($format)) {
			$format = 'dd-mm-yyyy';
		}

		return self::__convertToDBFormat($date, $format);
	}

	/**
	 *
	 * @param string $date
	 * @param string $format
	 * @return string
	 */
	public static function __convertToDBFormat($date, $format) {
		if (empty($date)) {
			return $date;
		}
		list($y, $m, $d) = explode('-', $date);
		if (strlen($y) == 4) {
			return $date;
		}
		if ($format == '') {
			$format = 'dd-mm-yyyy';
		}
		$dbDate = '';
		if ($format == 'dd-mm-yyyy') {
			list($d, $m, $y) = explode('-', $date);
		} elseif ($format == 'mm-dd-yyyy') {
			list($m, $d, $y) = explode('-', $date);
		} elseif ($format == 'yyyy-mm-dd') {
			list($y, $m, $d) = explode('-', $date);
		}

		if (!$y && !$m && !$d) {
			$dbDate = '';
		} else {
			$dbDate = $y . '-' . $m . '-' . $d;
		}
		return $dbDate;
	}

	/**
	 *
	 * @param Mixed $date
	 * @return array
	 */
	public static function convertToInternalFormat($date) {
		if (!is_array($date)) {
			$date = explode(' ', $date);
		}
		return $date;
	}

	/**
	 *
	 * @global Users $current_user
	 * @param datetime $date in ISO Y-m-d format
	 * @param Users $user
	 * @return string
	 */
	public static function convertToUserFormat($date, $user = null) {
		global $current_user;
		if (empty($user)) {
			$user = $current_user;
		}
		$format = $user->date_format;
		if (empty($format)) {
			$format = 'dd-mm-yyyy';
		}
		return self::__convertToUserFormat($date, $format);
	}

	/**
	 *
	 * @param datetime $date in ISO Y-m-d format
	 * @param string $format
	 * @return string
	 */
	public static function __convertToUserFormat($date, $format) {
		if (empty($date)) {
			return $date;
		}
		$date = self::convertToInternalFormat($date);
		list($y, $m, $d) = explode('-', $date[0]);

		if ($format == 'dd-mm-yyyy') {
			$date[0] = $d . '-' . $m . '-' . $y;
		} elseif ($format == 'mm-dd-yyyy') {
			$date[0] = $m . '-' . $d . '-' . $y;
		} elseif ($format == 'yyyy-mm-dd') {
			$date[0] = $y . '-' . $m . '-' . $d;
		}
		if (isset($date[1]) && $date[1] != '') {
			$userDate = $date[0] . ' ' . $date[1];
		} else {
			$userDate = $date[0];
		}
		return $userDate;
	}

	/**
	 *
	 * @global Users $current_user
	 * @param datetime $value
	 * @param Users $user
	 */
	public static function convertToUserTimeZone($value, $user = null) {
		global $current_user, $default_timezone;
		if (empty($user)) {
			$user = $current_user;
		}
		$timeZone = $user->time_zone ? $user->time_zone : $default_timezone;
		return DateTimeField::convertTimeZone($value, self::getDBTimeZone(), $timeZone);
	}

	/**
	 *
	 * @global Users $current_user
	 * @param datetime date/time $value
	 * @param Users $user
	 */
	public static function convertToDBTimeZone($value, $user = null) {
		global $current_user, $default_timezone;
		if (empty($user)) {
			$user = $current_user;
		}
		$timeZone = $user->time_zone ? $user->time_zone : $default_timezone;
		$value = self::sanitizeDate($value, $user);
		return DateTimeField::convertTimeZone($value, $timeZone, self::getDBTimeZone());
	}

	/**
	 *
	 * @param datetime $time
	 * @param string $sourceTimeZoneName
	 * @param string $targetTimeZoneName
	 * @return DateTime
	 */
	public static function convertTimeZone($time, $sourceTimeZoneName, $targetTimeZoneName) {
		$sourceTimeZone = new DateTimeZone($sourceTimeZoneName);
		preg_match('/(\d{1,2}\:\d{2}:\d{2}$|\d{1,2}\:\d{2}$)/', $time, $matches);
		if ($matches) {
			$timefield = $matches[0];
			$postime = strpos($time, $timefield);
			$date = trim(substr($time, 0, $postime));
			if (strlen($date)<8) {
				$date = ''; // will set today
			}
			if (strlen($date)>10) {
				$date = substr($date, 0, 10);
			}
			$hour = $timefield;
		} else {
			$date = $time;
			$hour = '00:00';
		}
		if ($hour >= '24:00') {
			$time = $date.' 00:00';
		}
		try {
			$myDateTime = new DateTime($time, $sourceTimeZone);
		} catch (Exception $e) {
			$cleantime = self::sanitizeDate($time, '');
			$myDateTime = new DateTime($cleantime, $sourceTimeZone);
		}

		// convert this to target timezone using the DateTimeZone object
		$targetTimeZone = new DateTimeZone($targetTimeZoneName);
		$myDateTime->setTimeZone($targetTimeZone);
		return $myDateTime;
	}

	/** Function to set time values compatible to database (GMT)
	 * @param Users
	 * @return string insert date
	 */
	public function getDBInsertTimeValue($user = null) {
		global $log;
		$log->debug('> getDBInsertTimeValue '.$this->datetime);
		$date = self::convertToDBTimeZone($this->datetime, $user);
		$log->debug('< getDBInsertTimeValue');
		return $date->format('H:i:s');
	}

	/**
	 * This function returns the date in user specified format.
	 * @global Object $log
	 * @global Users $current_user
	 * @return string
	 */
	public function getDisplayDate($user = null) {
		global $log;
		$log->debug('> getDisplayDate '.$this->datetime);

		$date_value = explode(' ', $this->datetime);
		if (isset($date_value[1]) && $date_value[1] != '') {
			$date = self::convertToUserTimeZone($this->datetime, $user);
			$date_value = $date->format('Y-m-d');
		}

		$display_date = self::convertToUserFormat($date_value, $user);
		$log->debug('< getDisplayDate');
		return $display_date;
	}

	public function getDisplayTime($user = null) {
		global $log;
		$log->debug('> getDisplayTime '.$this->datetime);
		$date = self::convertToUserTimeZone($this->datetime, $user);
		$time = $date->format("H:i:s");
		$log->debug('< getDisplayTime');
		return $time;
	}

	public static function getDBTimeZone() {
		if (empty(self::$databaseTimeZone)) {
			$defaultTimeZone = date_default_timezone_get();
			if (empty($defaultTimeZone)) {
				$defaultTimeZone = 'UTC';
			}
			self::$databaseTimeZone = $defaultTimeZone;
		}
		return self::$databaseTimeZone;
	}

	public static function getPHPDateFormat($user = null) {
		global $current_user;
		if (empty($user)) {
			$user = $current_user;
		}
		return str_replace(array('yyyy', 'mm','dd'), array('Y', 'm', 'd'), $user->date_format);
	}

	public static function sanitizeTime($time) {
		if (empty($time)) {
			return '00:00:00';
		}
		$len = strlen($time);
		$colon = substr_count($time, ':');
		if ($len < 3) { // minutes
			return '00:'.substr('0'.$time, -2).':00';
		}
		if ($len < 6 && $colon == 1) { // hour:minutes
			list($h, $m) = explode(':', $time);
			return substr('0'.$h, -2).':'.substr('0'.$m, -2).':00';
		}
		list($h, $m, $s) = explode(':', $time);
		return substr('0'.$h, -2).':'.substr('0'.$m, -2).':'.substr('0'.$s, -2);
	}

	private static function sanitizeDate($value, $user) {
		global $current_user;
		if (empty($user)) {
			$user = $current_user;
		}

		// No need to modify dd-mm-yyyy nor yyyy-mm-dd because PHP knows how to resolve those correctly.
		if ($user->date_format == 'mm-dd-yyyy') {
			if (strpos($value, ' ')>0) {
				list($date, $time) = explode(' ', $value);
			} else {
				$date = $value;
			}
			if (!empty($date)) {
				list($m, $d, $y) = explode('-', $date);
				if (strlen($m) < 3) {
					if (isset($time)) {
						$time = ' '.$time;
					} else {
						$time = '';
					}
					$value = "$y-$m-$d".rtrim($time);
				}
			}
		}
		return $value;
	}

/**
 * @param string $startFrom startFrom date
 * @param string $endFrom endFrom date
 * @param string $format of the date
 * @return array Return an array with all weekend dates between $startFrom and $endFrom
 */
	public static function getWeekendDates($startFrom, $endFrom, $format) {
		$weekendDates = array();
		$emptyArray = [];
		//Check if format is not empty
		if (trim($format) === '') {
			$format = 'Y-m-d';
		}
		$checkStartDate = self::validateDate($startFrom, $format);
		$checkEndDate = self::validateDate($endFrom, $format);

		//First we check if startFrom or endFrom is empty
		if ((trim($startFrom) === '') || (trim($endFrom) === '')) {
			return $emptyArray;
		} elseif (($checkStartDate === false) || ($checkEndDate === false)) {
			return $emptyArray;
		} else {
			$interval_d1 = new DateInterval('P1D');
			$interval_d6 = new DateInterval('P6D');
			$startDate = new DateTime($startFrom);
			$endDate = new DateTime($endFrom);
			$start_time = strtotime($startFrom);
			$end_time = strtotime($endFrom);
			if (strtotime($startFrom) > strtotime($endFrom)) {
				$weekendDates = [];
			} else {
				$dt1 = strtotime($startDate->format($format));
				$dt2 = date("l", $dt1);
				$dt3 = strtolower($dt2);
				if ($dt3 == "saturday") {
					while ($start_time <= $end_time) {
						//Add Saturday
						$weekendDates[] = $startDate->format($format);
						//Increment of 1 day
						$startDate->add($interval_d1);
						$current_time = strtotime($startDate->format($format));

						if ($current_time <= $end_time) {
							//Add Sunday
							$weekendDates[] = $startDate->format($format);
							//Increment of 6 days
							$startDate->add($interval_d6);
							$current_time = strtotime($startDate->format($format));
						}
						$start_time = $current_time;
					}
				} elseif ($dt3 == "sunday") {
					while ($start_time <= $end_time) {
						//Add Sunday
						$weekendDates[] = $startDate->format($format);
						//Increment of 6 day
						$startDate->add($interval_d6);
						$current_time = strtotime($startDate->format($format));
						if ($current_time <= $end_time) {
							//Add next Saturday
							$weekendDates[] = $startDate->format($format);
							//Increment of 6 days
							$startDate->add($interval_d1);
							$current_time = strtotime($startDate->format($format));
						}
						$start_time = $current_time;
					}
				} else {
					$date = $startDate->modify('next Saturday');
					while ($start_time <= $end_time) {
						$start = strtotime($date->format($format));
						if ($start <= $end_time) {
							//Add Saturday
							$weekendDates[] = $date->format($format);
							//Incremento by one day
							$date->add($interval_d1);
							$start = strtotime($date->format($format));
							if ($start <= $end_time) {
								//Add Sunday
								$weekendDates[] = $date->format($format);
								//Increment by 6 days
								$date->add($interval_d6);
							}
						}
						$start_time = $start;
					}
				}
			}
			return $weekendDates;
		}
	}

/**
 * @param string $date
 * @param string $format of the date
 * @return boolean Return true if $date is a valid date format false otherwise
 */
	public static function validateDate($date, $format = 'Y-m-d') {
		$d = DateTime::createFromFormat($format, $date);
		return $d && $d->format($format) === $date;
	}
}
