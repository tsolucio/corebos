<?php
/* * *******************************************************************************
 * * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 * ****************************************************************************** */
require_once 'include/utils/utils.php';
require_once 'modules/Calendar/Date.php';

class RecurringType {

	public $recur_type;
	public $startdate;
	public $enddate;
	public $recur_freq;
	public $dayofweek_to_rpt = array();
	public $repeat_monthby;
	public $rptmonth_datevalue;
	public $rptmonth_daytype;
	public $recurringdates = array();
	public $reminder;
	public $cal_month_long = array(
		1=>'January',
		2=>'February',
		3=>'March',
		4=>'April',
		5=>'May',
		6=>'June',
		7=>'July',
		8=>'August',
		9=>'September',
		10=>'October',
		11=>'November',
		12=>'December',
	);
	public $cal_weekdays_short = array(
		1=>'Mon',
		2=>'Tue',
		3=>'Wed',
		4=>'Thu',
		5=>'Fri',
		6=>'Sat',
		7=>'Sun',
	);

	/**
	 * Constructor for class RecurringType
	 * @param array  $repeat_arr - array contains recurring info
	 */
	public function __construct($repeat_arr) {
		$st_date = explode("-", $repeat_arr["startdate"]);
		$st_time = explode(":", $repeat_arr["starttime"]);
		$end_date = explode("-", $repeat_arr["enddate"]);
		$end_time = explode(":", $repeat_arr['endtime']);

		$start_date = array(
			'day' => $st_date[2],
			'month' => $st_date[1],
			'year' => $st_date[0],
			'hour' => $st_time[0],
			'min' => $st_time[1]
		);
		$end_date = array(
			'day' => $end_date[2],
			'month' => $end_date[1],
			'year' => $end_date[0],
			'hour' => $end_time[0],
			'min' => $end_time[1]
		);
		$this->startdate = new vt_DateTime($start_date, true);
		$this->enddate = new vt_DateTime($end_date, true);

		$this->recur_type = $repeat_arr['type'];
		$this->recur_freq = $repeat_arr['repeat_frequency'];
		if (empty($this->recur_freq)) {
			$this->recur_freq = 1;
		}
		$this->dayofweek_to_rpt = isset($repeat_arr['dayofweek_to_repeat']) ? $repeat_arr['dayofweek_to_repeat'] : '';
		$this->repeat_monthby = isset($repeat_arr['repeatmonth_type']) ? $repeat_arr['repeatmonth_type'] : '';
		$this->rptmonth_datevalue = isset($repeat_arr['repeatmonth_date']) ? $repeat_arr['repeatmonth_date'] : '';
		$this->rptmonth_daytype = isset($repeat_arr['repeatmonth_daytype']) ? $repeat_arr['repeatmonth_daytype'] : '';
		$this->recurringdates = $this->_getRecurringDates();
	}

	public static function fromUserRequest($requestArray) {
		// All the information from the user is received in User Time zone
		// Convert Start date and Time to DB Time zone
		$startDateObj = DateTimeField::convertToDBTimeZone($requestArray["startdate"] . ' ' . $requestArray['starttime']);
		$requestArray['startdate'] = $startDate = $startDateObj->format('Y-m-d');
		$requestArray['starttime'] = $startTime = $startDateObj->format('H:i');
		$endDateObj = DateTimeField::convertToDBTimeZone($requestArray["enddate"] . ' ' . $requestArray['endtime']);
		$requestArray['enddate'] = $endDateObj->format('Y-m-d');
		$requestArray['endtime'] = $endDateObj->format('H:i');

		if (isset($requestArray['sun_flag'])) {
			$requestArray['dayofweek_to_repeat'][] = 0;
		}
		if (isset($requestArray['mon_flag'])) {
			$requestArray['dayofweek_to_repeat'][] = 1;
		}
		if (isset($requestArray['tue_flag'])) {
			$requestArray['dayofweek_to_repeat'][] = 2;
		}
		if (isset($requestArray['wed_flag'])) {
			$requestArray['dayofweek_to_repeat'][] = 3;
		}
		if (isset($requestArray['thu_flag'])) {
			$requestArray['dayofweek_to_repeat'][] = 4;
		}
		if (isset($requestArray['fri_flag'])) {
			$requestArray['dayofweek_to_repeat'][] = 5;
		}
		if (isset($requestArray['sat_flag'])) {
			$requestArray['dayofweek_to_repeat'][] = 6;
		}

		if ($requestArray['type'] == 'Weekly') {
			if ($requestArray['dayofweek_to_repeat'] != null) {
				$userStartDateTime = DateTimeField::convertToUserTimeZone($startDate . ' ' . $startTime);
				$dbDaysOfWeek = array();
				foreach ($requestArray['dayofweek_to_repeat'] as $selectedDayOfWeek) {
					$currentDayOfWeek = $userStartDateTime->format('w');
					$newDate = $userStartDateTime->format('d') + ($selectedDayOfWeek - $currentDayOfWeek);
					$userStartDateTime->setDate($userStartDateTime->format('Y'), $userStartDateTime->format('m'), $newDate);
					$dbDaysOfWeek[] = $userStartDateTime->format('w');
				}
				$requestArray['dayofweek_to_repeat'] = $dbDaysOfWeek;
			}
		} elseif ($requestArray['type'] == 'Monthly') {
			$userStartDateTime = DateTimeField::convertToUserTimeZone($startDate . ' ' . $startTime);
			if ($requestArray['repeatmonth_type'] == 'date') {
				$dayOfMonth = $requestArray['repeatmonth_date'];
				$userStartDateTime->setDate($userStartDateTime->format('Y'), $userStartDateTime->format('m'), $dayOfMonth);
				$userStartDateTime->setTimezone(new DateTimeZone(DateTimeField::getDBTimeZone()));
				$requestArray['repeatmonth_date'] = $userStartDateTime->format('d');
			} else {
				$dayOfWeek = $requestArray['dayofweek_to_repeat'][0];
				if ($requestArray['repeatmonth_daytype'] == 'first' ||
					$requestArray['repeatmonth_daytype'] == 'second' ||
					$requestArray['repeatmonth_daytype'] == 'third'
				) {
					$userStartDateTime->setDate($userStartDateTime->format('Y'), $userStartDateTime->format('m'), 1);
					$dayOfWeekForFirstDay = $userStartDateTime->format('N');
					if ($dayOfWeekForFirstDay < $dayOfWeek) {
						$date = $dayOfWeek - $dayOfWeekForFirstDay + 1;
					} else {
						$date = (7 - $dayOfWeekForFirstDay) + $dayOfWeek + 1;
					}
					if ($requestArray['repeatmonth_daytype'] == 'second') {
						$date+=7;
					}
					if ($requestArray['repeatmonth_daytype'] == 'third') {
						$date+=14;
					}
				} elseif ($requestArray['repeatmonth_daytype'] == 'last') {
					$daysInMonth = $userStartDateTime->format('t');
					$userStartDateTime->setDate($userStartDateTime->format('Y'), $userStartDateTime->format('m'), $daysInMonth);
					$dayOfWeekForLastDay = $userStartDateTime->format('N');
					if ($dayOfWeekForLastDay < $dayOfWeek) {
						$date = $daysInMonth - 7 + ($dayOfWeek - $dayOfWeekForLastDay);
					} else {
						$date = $daysInMonth - ($dayOfWeekForLastDay - $dayOfWeek);
					}
				}
				$userStartDateTime->setDate($userStartDateTime->format('Y'), $userStartDateTime->format('m'), $date);
				$userStartDateTime->setTimezone(new DateTimeZone(DateTimeField::getDBTimeZone()));
				$requestArray['dayofweek_to_repeat'][0] = $userStartDateTime->format('N');
			}
		}
		return new RecurringType($requestArray);
	}

	public static function fromDBRequest($resultRow) {
		// All the information from the database is received in DB Time zone
		$repeatInfo = array();

		$repeatInfo['startdate'] = $resultRow['date_start'];
		$repeatInfo['starttime'] = $resultRow['time_start'];
		$repeatInfo['enddate'] = $resultRow['due_date'];
		$repeatInfo['endtime'] = $resultRow['time_end'];

		$repeatInfo['type'] = $resultRow['recurringtype'];
		$repeatInfo['repeat_frequency'] = $resultRow['recurringfreq'];

		$recurringInfoString = $resultRow['recurringinfo'];
		$recurringInfo = explode('::', $recurringInfoString);

		if ($repeatInfo['type'] == 'Weekly') {
			$startIndex = 1; // 0 is for Recurring Type
			$length = count($recurringInfo);
			$j = 0;
			for ($i = $startIndex; $i < $length; ++$i) {
				$repeatInfo['dayofweek_to_repeat'][$j++] = $recurringInfo[$i];
			}
		} elseif ($repeatInfo['type'] == 'Monthly') {
			$repeatInfo['repeatmonth_type'] = $recurringInfo[1];
			if ($repeatInfo['repeatmonth_type'] == 'date') {
				$repeatInfo['repeatmonth_date'] = $recurringInfo[2];
			} else {
				$repeatInfo['repeatmonth_daytype'] = $recurringInfo[2];
				$repeatInfo['dayofweek_to_repeat'][0] = $recurringInfo[3];
			}
		}
		return new RecurringType($repeatInfo);
	}

	public function getRecurringType() {
		return $this->recur_type;
	}

	public function getRecurringFrequency() {
		return $this->recur_freq;
	}

	public function getDBRecurringInfoString() {
		$recurringType = $this->getRecurringType();
		$recurringInfo = '';
		if ($recurringType == 'Daily' || $recurringType == 'Yearly') {
			$recurringInfo = $recurringType;
		} elseif ($recurringType == 'Weekly') {
			if ($this->dayofweek_to_rpt != null) {
				$recurringInfo = $recurringType . '::' . implode('::', $this->dayofweek_to_rpt);
			} else {
				$recurringInfo = $recurringType;
			}
		} elseif ($recurringType == 'Monthly') {
			$recurringInfo = $recurringType . '::' . $this->repeat_monthby;
			if ($this->repeat_monthby == 'date') {
				$recurringInfo = $recurringInfo . '::' . $this->rptmonth_datevalue;
			} else {
				$recurringInfo = $recurringInfo . '::' . $this->rptmonth_daytype . '::' . $this->dayofweek_to_rpt[0];
			}
		}
		return $recurringInfo;
	}

	public function getUserRecurringInfo() {
		$recurringType = $this->getRecurringType();
		$recurringInfo = array();

		if ($recurringType == 'Weekly') {
			if ($this->dayofweek_to_rpt != null) {
				$dbStartDateTime = new DateTime($this->startdate->get_DB_formatted_date() . ' ' . $this->startdate->get_formatted_time());
				$userDaysOfWeek = array();
				foreach ($this->dayofweek_to_rpt as $selectedDayOfWeek) {
					$currentDayOfWeek = $dbStartDateTime->format('w');
					$newDate = $dbStartDateTime->format('d') + ($selectedDayOfWeek - $currentDayOfWeek);
					$dbStartDateTime->setDate($dbStartDateTime->format('Y'), $dbStartDateTime->format('m'), $newDate);
					$userStartDateTime = DateTimeField::convertToUserTimeZone($dbStartDateTime->format('Y-m-d') . ' ' . $dbStartDateTime->format('H:i'));
					$userDaysOfWeek[] = $userStartDateTime->format('w');
				}
				$recurringInfo['dayofweek_to_repeat'] = $userDaysOfWeek;
			}
		} elseif ($recurringType == 'Monthly') {
			$dbStartDateTime = new DateTime($this->startdate->get_DB_formatted_date() . ' ' . $this->startdate->get_formatted_time());
			$recurringInfo['repeatmonth_type'] = $this->repeat_monthby;
			if ($this->repeat_monthby == 'date') {
				$dayOfMonth = $this->rptmonth_datevalue;
				$dbStartDateTime->setDate($dbStartDateTime->format('Y'), $dbStartDateTime->format('m'), $dayOfMonth);
				$userStartDateTime = DateTimeField::convertToUserTimeZone($dbStartDateTime->format('Y-m-d') . ' ' . $dbStartDateTime->format('H:i'));
				$recurringInfo['repeatmonth_date'] = $userStartDateTime->format('d');
			} else {
				$dayOfWeek = $this->dayofweek_to_rpt[0];
				$recurringInfo['repeatmonth_daytype'] = $this->rptmonth_daytype;
				if ($this->rptmonth_daytype == 'first' || $this->rptmonth_daytype == 'second' || $this->rptmonth_daytype == 'third') {
					$dbStartDateTime->setDate($dbStartDateTime->format('Y'), $dbStartDateTime->format('m'), 1);
					$dayOfWeekForFirstDay = $dbStartDateTime->format('N');
					if ($dayOfWeekForFirstDay < $dayOfWeek) {
						$date = $dayOfWeek - $dayOfWeekForFirstDay + 1;
					} else {
						$date = (7 - $dayOfWeekForFirstDay) + $dayOfWeek + 1;
					}
					if ($this->rptmonth_daytype == 'second') {
						$date+=7;
					}
					if ($this->rptmonth_daytype == 'third') {
						$date+=14;
					}
				} elseif ($this->rptmonth_daytype == 'last') {
					$daysInMonth = $dbStartDateTime->format('t');
					$dbStartDateTime->setDate($dbStartDateTime->format('Y'), $dbStartDateTime->format('m'), $daysInMonth);
					$dayOfWeekForLastDay = $dbStartDateTime->format('N');
					if ($dayOfWeekForLastDay < $dayOfWeek) {
						$date = $daysInMonth - 7 + ($dayOfWeek - $dayOfWeekForLastDay);
					} else {
						$date = $daysInMonth - ($dayOfWeekForLastDay - $dayOfWeek);
					}
				}
				$dbStartDateTime->setDate($dbStartDateTime->format('Y'), $dbStartDateTime->format('m'), $date);
				$userStartDateTime = DateTimeField::convertToUserTimeZone($dbStartDateTime->format('Y-m-d') . ' ' . $dbStartDateTime->format('H:i'));
				$recurringInfo['dayofweek_to_repeat'][0] = $userStartDateTime->format('N');
			}
		}
		return $recurringInfo;
	}

	public function getDisplayRecurringInfo() {
		$i18nModule = 'Calendar';

		$displayRecurringData = array();

		$recurringInfo = $this->getUserRecurringInfo();

		$displayRecurringData['recurringcheck'] = getTranslatedString('LBL_YES', $i18nModule);
		$displayRecurringData['repeat_frequency'] = $this->getRecurringFrequency();
		$displayRecurringData['recurringtype'] = $this->getRecurringType();

		switch ($this->getRecurringType()) {
			case 'Weekly':
				$translatedRepeatDays = array();
				foreach ($recurringInfo['dayofweek_to_repeat'] as $day) {
					$translatedRepeatDays[] = getTranslatedString('LBL_DAY' . $day, $i18nModule);
				}
				$displayRecurringData['repeat_str'] = implode(',', $translatedRepeatDays);
				break;
			case 'Monthly':
				$translatedRepeatDays = array();
				$displayRecurringData['repeatMonth'] = $recurringInfo['repeatmonth_type'];
				if ($recurringInfo['repeatmonth_type'] == 'date') {
					$displayRecurringData['repeatMonth_date'] = $recurringInfo['repeatmonth_date'];
					$displayRecurringData['repeat_str'] = getTranslatedString('on', $i18nModule)
						. ' ' . $recurringInfo['repeatmonth_date']
						. ' ' . getTranslatedString('day of the month', $i18nModule);
				} else {
					$displayRecurringData['repeatMonth_daytype'] = $recurringInfo['repeatmonth_daytype'];
					$displayRecurringData['repeatMonth_day'] = $recurringInfo['dayofweek_to_repeat'][0];
					$translatedRepeatDay = getTranslatedString('LBL_DAY' . $recurringInfo['dayofweek_to_repeat'][0], $i18nModule);

					$displayRecurringData['repeat_str'] = getTranslatedString('on', $i18nModule)
						. ' ' . getTranslatedString($recurringInfo['repeatmonth_daytype'], $i18nModule)
						. ' ' . $translatedRepeatDay;
				}
				break;
			case 'Daily':
			case 'Yearly':
				$displayRecurringData['repeat_str'] = '';
				break;
		}
		return $displayRecurringData;
	}

	/**
	 *  Function to get recurring dates depending on the recurring type
	 *  return  array   $recurringDates     -  Recurring Dates in format
	 * 	Recurring date will be returned in DB Time Zone, as well as DB format
	 */
	public function _getRecurringDates() {
		$startdateObj = $this->startdate;
		$startdate = $startdateObj->get_DB_formatted_date();
		$recurringDates[] = $startdate;
		$tempdateObj = $startdateObj;
		$tempdate = $startdate;
		$enddate = $this->enddate->get_DB_formatted_date();

		while ($tempdate <= $enddate) {
			$date = $tempdateObj->get_Date();
			$month = $tempdateObj->getMonth();
			$year = $tempdateObj->getYear();

			if ($this->recur_type == 'Daily') {
				if (isset($this->recur_freq)) {
					$index = $date + $this->recur_freq - 1;
				} else {
					$index = $date;
				}
				$tempdateObj = $this->startdate->getThismonthDaysbyIndex($index, '', $month, $year);
				$tempdate = $tempdateObj->get_DB_formatted_date();
				if ($tempdate <= $enddate) {
					$recurringDates[] = $tempdate;
				}
			} elseif ($this->recur_type == 'Weekly') {
				if (count($this->dayofweek_to_rpt) == 0) {
					$this->dayofweek_to_rpt[] = $this->startdate->dayofweek;
				}

				foreach ($this->dayofweek_to_rpt as $day) {
					$repeatDay = $tempdateObj->getThisweekDaysbyIndex($day);
					$repeatDate = $repeatDay->get_DB_formatted_date();
					if ($repeatDate > $startdate && $repeatDate <= $enddate) {
						$recurringDates[] = $repeatDate;
					}
				}

				if (isset($this->recur_freq)) {
					$index = $this->recur_freq * 7;
				} else {
					$index = 7;
				}
				$date_arr = array(
					'day' => $date + $index,
					'month' => $month,
					'year' => $year
				);
				$tempdateObj = new vt_DateTime($date_arr, true);
				$tempdate = $tempdateObj->get_DB_formatted_date();
			} elseif ($this->recur_type == 'Monthly') {
				if ($this->repeat_monthby == 'date') {
					if ($this->rptmonth_datevalue <= $date) {
						$index = $this->rptmonth_datevalue - 1;
						$day = $this->rptmonth_datevalue;
						if (isset($this->recur_freq)) {
							$month = $month + $this->recur_freq;
						} else {
							$month = $month + 1;
						}
						$tempdateObj = $tempdateObj->getThismonthDaysbyIndex($index, $day, $month, $year);
					} else {
						$index = $this->rptmonth_datevalue - 1;
						$day = $this->rptmonth_datevalue;
						$tempdateObj = $tempdateObj->getThismonthDaysbyIndex($index, $day, $month, $year);
					}
				} elseif ($this->repeat_monthby == 'day') {
					if ($this->rptmonth_daytype == 'first') {
						$date_arr = array(
							'day' => 1,
							'month' => $month,
							'year' => $year
						);
						$tempdateObj = new vt_DateTime($date_arr, true);
						$firstdayofmonthObj = $this->getFistdayofmonth($this->dayofweek_to_rpt[0], $tempdateObj);
						if ($firstdayofmonthObj->get_DB_formatted_date() <= $tempdate) {
							if (isset($this->recur_freq)) {
								$month = $firstdayofmonthObj->getMonth() + $this->recur_freq;
							} else {
								$month = $firstdayofmonthObj->getMonth() + 1;
							}
							$dateObj = $firstdayofmonthObj->getThismonthDaysbyIndex(0, 1, $month, $firstdayofmonthObj->getYear());
							$nextmonthObj = $this->getFistdayofmonth($this->dayofweek_to_rpt[0], $dateObj);
							$tempdateObj = $nextmonthObj;
						} else {
							$tempdateObj = $firstdayofmonthObj;
						}
					} elseif ($this->rptmonth_daytype == 'second' || $this->rptmonth_daytype == 'third') {
						$position = ($this->rptmonth_daytype == 'second' ? 2 : 3);
						$date_arr = array(
							'day' => $this->getDayOfWeekPerWeekPositionInMonth($this->dayofweek_to_rpt[0], $year, $month, $position),
							'month' => $month,
							'year' => $year
						);
						$nextdayofmonthObj = new vt_DateTime($date_arr, true);
						if ($nextdayofmonthObj->get_DB_formatted_date() <= $tempdate) {
							if (isset($this->recur_freq)) {
								$month = $nextdayofmonthObj->getMonth() + $this->recur_freq;
							} else {
								$month = $nextdayofmonthObj->getMonth() + 1;
							}
							$date_arr = array(
								'day' => $this->getDayOfWeekPerWeekPositionInMonth($this->dayofweek_to_rpt[0], $year, $month, $position),
								'month' => $month,
								'year' => $year
							);
							$nextdayofmonthObj = new vt_DateTime($date_arr, true);
						}
						$tempdateObj = $nextdayofmonthObj;
					} elseif ($this->rptmonth_daytype == 'last') {
						$date_arr = array(
							'day' => $tempdateObj->getDaysInMonth(),
							'month' => $tempdateObj->getMonth(),
							'year' => $tempdateObj->getYear()
						);
						$tempdateObj = new vt_DateTime($date_arr, true);
						$lastdayofmonthObj = $this->getLastdayofmonth($this->dayofweek_to_rpt[0], $tempdateObj);
						if ($lastdayofmonthObj->get_DB_formatted_date() <= $tempdate) {
							if (isset($this->recur_freq)) {
								$month = $lastdayofmonthObj->getMonth() + $this->recur_freq;
							} else {
								$month = $lastdayofmonthObj->getMonth() + 1;
							}
							$dateObj = $lastdayofmonthObj->getThismonthDaysbyIndex(0, 1, $month, $lastdayofmonthObj->getYear());
							$dateObj = $dateObj->getThismonthDaysbyIndex(
								$dateObj->getDaysInMonth() - 1,
								$dateObj->getDaysInMonth(),
								$month,
								$lastdayofmonthObj->getYear()
							);
							$nextmonthObj = $this->getLastdayofmonth($this->dayofweek_to_rpt[0], $dateObj);
							$tempdateObj = $nextmonthObj;
						} else {
							$tempdateObj = $lastdayofmonthObj;
						}
					}
				} else {
					$date_arr = array(
						'day' => $date,
						'month' => $month + 1,
						'year' => $year
					);
					$tempdateObj = new vt_DateTime($date_arr, true);
				}
				$tempdate = $tempdateObj->get_DB_formatted_date();
				if ($tempdate <= $enddate) {
					$recurringDates[] = $tempdate;
				}
			} elseif ($this->recur_type == 'Yearly') {
				if (isset($this->recur_freq)) {
					$index = $year + $this->recur_freq;
				} else {
					$index = $year + 1;
				}
				if ($index > 2037 || $index < 1970) {
					print("<font color='red'>" . getTranslatedString('LBL_CAL_LIMIT_MSG') . '</font>');
					exit;
				}
				$date_arr = array(
					'day' => $date,
					'month' => $month,
					'year' => $index
				);
				$tempdateObj = new vt_DateTime($date_arr, true);
				$tempdate = $tempdateObj->get_DB_formatted_date();
				if ($tempdate <= $enddate) {
					$recurringDates[] = $tempdate;
				}
			} else {
				die('Recurring Type ' . $this->recur_type . ' is not defined');
			}
		}
		return $recurringDates;
	}

	/** Function to get first day of the month(like first Monday or Friday and etc.)
	 *  @param $dayofweek   -- day of the week to repeat the event :: Type string
	 *  @param $dateObj     -- date object  :: Type vt_DateTime Object
	 *  return $dateObj -- the date object on which the event repeats :: Type vt_DateTime Object
	 */
	public function getFistdayofmonth($dayofweek, & $dateObj) {
		if ($dayofweek < $dateObj->dayofweek) {
			$index = (7 - $dateObj->dayofweek) + $dayofweek;
			$day = 1 + $index;
			$month = $dateObj->month;
			$year = $dateObj->year;
			$dateObj = $dateObj->getThismonthDaysbyIndex($index, $day, $month, $year);
		} else {
			$index = $dayofweek - $dateObj->dayofweek;
			$day = 1 + $index;
			$month = $dateObj->month;
			$year = $dateObj->year;
			$dateObj = $dateObj->getThismonthDaysbyIndex($index, $day, $month, $year);
		}
		return $dateObj;
	}

	/** Function to get last day of the month(like last Monday or Friday and etc.)
	 *  @param $dayofweek   -- day of the week to repeat the event :: Type string
	 *  @param $dateObj     -- date object  :: Type vt_DateTime Object
	 *  return $dateObj -- the date object on which the event repeats :: Type vt_DateTime Object
	 */
	public function getLastdayofmonth($dayofweek, & $dateObj) {
		if ($dayofweek == $dateObj->dayofweek) {
			return $dateObj;
		} else {
			if ($dayofweek > $dateObj->dayofweek) {
				$day = $dateObj->day - 7 + ($dayofweek - $dateObj->dayofweek);
			} else {
				$day = $dateObj->day - ($dateObj->dayofweek - $dayofweek);
			}
			$index = $day - 1;
			$month = $dateObj->month;
			$year = $dateObj->year;
			$dateObj = $dateObj->getThismonthDaysbyIndex($index, $day, $month, $year);
			return $dateObj;
		}
	}

	public function getDayOfWeekPerWeekPositionInMonth($dow, $year, $month, $position) {
		$list = array(1=>'first',2=>'second',3=>'third',4=>'last');
		return date('j', strtotime($list[$position] .' '.$this->cal_weekdays_short[$dow] . ' of ' . $this->cal_month_long[$month] . ' ' . $year));
	}
}
?>
