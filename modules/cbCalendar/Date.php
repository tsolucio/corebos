<?php
/*********************************************************************************
** The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ********************************************************************************/

class vt_DateTime {
	public $second = 0;
	public $minute = 0;
	public $hour = 0;
	public $z_hour = '00';
	public $day;
	public $z_day;
	public $week;
	public $month;
	public $z_month;
	public $year;
	public $dayofweek;
	public $dayofyear;
	public $daysinmonth;
	public $daysinyear;
	public $dayofweek_inshort;
	public $dayofweek_inlong;
	public $month_inshort;
	public $month_inlong;
	public $ts;
	public $offset;
	public $format;
	public $tz;
	public $ts_def;

	/**
	 * Constructor for vt_DateTime class
	 * @param array  $timearr - collection of string
	 * @param string $check   - check string
	 */
	public function __construct(&$timearr, $check) {
		if (empty($timearr)) {
			$this->setDateTime(null);
		} elseif (isset($timearr['ts'])) {
			$this->setDateTime($timearr['ts']);
		} else {
			if (isset($timearr['hour']) && $timearr['hour'] !== '') {
				$this->hour = $timearr['hour'];
			}
			if (isset($timearr['min']) && $timearr['min'] !== '') {
				$this->minute = $timearr['min'];
			}
			if (isset($timearr['sec']) && $timearr['sec']  !== '') {
				$this->second = $timearr['sec'];
			}
			if (isset($timearr['day']) && $timearr['day'] !== '') {
				$this->day = $timearr['day'];
			}
			if (isset($timearr['week']) && $timearr['week'] !== '') {
				$this->week = $timearr['week'];
			}
			if (isset($timearr['month']) && $timearr['month'] !== '') {
				$this->month = $timearr['month'];
			}
			if (isset($timearr['year']) && $timearr['year'] >= 1970) {
				$this->year = $timearr['year'];
			} else {
				return null;
			}
		}
		if ($check) {
			$this->getDateTime();
		}
	}

	/**
	 * function to get date and time using index
	 * @param integer $index - number between 0 to 23
	 * @param string  day
	 * @param string  month
	 * @param string  year
	 * @return object vt_DateTime datetime value
	 */
	public function getTodayDatetimebyIndex($index, $dy = '', $mnth = '', $yr = '') {
		if ($dy === '') {
			$dy = $this->day;
		}
		if ($mnth === '') {
			$mnth = $this->month;
		}
		if ($yr === '') {
			$yr = $this->year;
		}
		$day_array = array();

		if ($index < 0 || $index > 23) {
			$index = 0;
		}

		$day_array['hour'] = $index;
		$day_array['min'] = 0;
		$day_array['day'] = $dy;
		$day_array['month'] = $mnth;
		$day_array['year'] = $yr;
		return new vt_DateTime($day_array, true);
	}

	/**
	 * function to get days in week using index
	 * @param integer $index - number between 1 to 7
	 * @return object vt_DateTime datetime value
	 */
	public function getThisweekDaysbyIndex($index) {
		$week_array = array();
		if ($index < 1 || $index > 7) {
			$index = 1;
		}
		$week_array['day'] = $this->day + ($index - $this->dayofweek);
		$week_array['month'] = $this->month;
		$week_array['year'] = $this->year;
		return new vt_DateTime($week_array, true);
	}

	/**
	 * function to get days in month using index
	 *
	 * @deprecated use getThisMonthsDayByIndex()
	 * @param integer $index - number between 0 to 42
	 * @param string  day
	 * @param string  month
	 * @param string  year
	 * @return object vt_DateTime datetime value
	 */
	public function getThismonthDaysbyIndex($index, $dy = '', $mnth = '', $yr = '') {
		if ($dy == '') {
			$dy = $index+1;
		}
		if ($mnth == '') {
			$mnth = $this->month;
		}
		if ($yr == '') {
			$yr = $this->year;
		}
		$month_array = array();
		$month_array['day'] = $dy;
		$month_array['month'] = $mnth;
		$month_array['year'] = $yr;
		return new vt_DateTime($month_array, true);
	}

	/**
	 * function to get months in year using index
	 * @param integer  $index - number between 0 to 11
	 * @return object vt_DateTime datetime value
	 */
	public function getThisyearMonthsbyIndex($index) {
		$year_array = array();
		$year_array['day'] = 1;
		if ($index < 0 || $index > 11) {
			$index = 0;
		}
		$year_array['month'] = $index+1;
		$year_array['year'] = $this->year;
		return new vt_DateTime($year_array, true);
	}

	/**
	 * function to get hour end time
	 * @return object vt_DateTime datetime value
	 */
	public function getHourendtime() {
		$date_array = array();
		$date_array['hour'] = $this->hour;
		$date_array['min'] = 59;
		$date_array['day'] = $this->day;
		$date_array['sec'] = 59;
		$date_array['month'] = $this->month;
		$date_array['year'] = $this->year;
		return new vt_DateTime($date_array, true);
	}

	/**
	 * function to get day end time
	 * @return object vt_DateTime datetime value
	 */
	public function getDayendtime() {
		$date_array = array();
		$date_array['hour'] = 23;
		$date_array['min'] = 59;
		$date_array['sec'] = 59;
		$date_array['day'] = $this->day;
		$date_array['month'] = $this->month;
		$date_array['year'] = $this->year;
		return new vt_DateTime($date_array, true);
	}

	/**
	 * function to get month end time
	 * @return object vt_DateTime datetime value
	 */
	public function getMonthendtime() {
		$date_array = array();
		$date_array['hour'] = 23;
		$date_array['min'] = 59;
		$date_array['sec'] = 59;
		$date_array['day'] = $this->daysinmonth;
		$date_array['month'] = $this->month;
		$date_array['year'] = $this->year;
		return new vt_DateTime($date_array, true);
	}

	/**
	 * function to get day of week
	 * @return string $this->day  - day (eg: Monday)
	 */
	public function get_Date() {
		return $this->day;
	}

	/**
	 * function to get month name in short
	 * @return string $this->month_inshort  - month name (eg: Jan)
	 */
	public function getmonthName_inshort() {
		return $this->month_inshort;
	}

	/**
	 * function to get month
	 * @return string $this->month  - month name
	 */
	public function getMonth() {
		return $this->month;
	}

	/**
	 * function to get year
	 */
	public function getYear() {
		return $this->year;
	}

	/**
	 * function to get the number of days in a month
	 */
	public function getDaysInMonth() {
		return $this->daysinmonth;
	}

	/**
	 * function to get month name
	 * @return string $this->month_inlong  - month name
	 */
	public function getmonthName() {
		return $this->month_inlong;
	}

	/**
	 * function to get day of week
	 * @return string $this->dayofweek_inlong  - day of week
	 */
	public function getdayofWeek() {
		return $this->dayofweek_inlong;
	}

	/**
	 * function to get day of week in short
	 * @return string $this->dayofweek_inshort  - day of week (eg: Mon)
	 */
	public function getdayofWeek_inshort() {
		return $this->dayofweek_inshort;
	}

	/**
	 * function to set values for vt_DateTime object
	 * @param integer   $ts  - Time stamp
	 */
	public function setDateTime($ts) {
		global $current_language;
		if (empty($ts)) {
			$ts = time();
		}

		$this->ts = $ts;
		$this->ts_def = $this->ts;
		$date_string = date('i::G::H::j::d::t::N::z::L::W::n::m::Y::Z::T::s', $ts);

		list($this->minute,$this->hour,$this->z_hour,$this->day,$this->z_day,$this->daysinmonth,$this->dayofweek,$this->dayofyear,$is_leap,$this->week,$this->month,$this->z_month,$this->year,$this->offset,$this->tz,$this->second) = explode('::', $date_string);
		$cal_strings = return_module_language($current_language, 'cbCalendar');
		$this->dayofweek_inshort = $cal_strings['cal_weekdays_short'][$this->dayofweek-1];
		$this->dayofweek_inlong = $cal_strings['cal_weekdays_long'][$this->dayofweek-1];
		$this->month_inshort = $cal_strings['cal_month_short'][$this->month];
		$this->month_inlong = $cal_strings['cal_month_long'][$this->month];

		$this->daysinyear = 365;

		if ($is_leap == 1) {
			$this->daysinyear += 1;
		}
	}

	/**
	 * function to get values from vt_DateTime object
	 */
	public function getDateTime() {
		$hrs = 0;
		$minute = 0;
		$_second = 0;
		$dy = 1;
		$mnth = 1;
		$yr = 1970;

		if (isset($this->second) && $this->second !== '') {
			$_second = $this->second;
		}
		if (isset($this->minute) && $this->minute !== '') {
			$minute = $this->minute;
		}
		if (isset($this->hour) && $this->hour !== '') {
			$hrs = $this->hour;
		}
		if (isset($this->day) && $this->day !== '') {
			$dy= $this->day;
		}
		if (isset($this->month) && $this->month !== '') {
			$mnth = $this->month;
		}
		if (isset($this->year) && $this->year !== '') {
			$yr = $this->year;
		}
		if (empty($hrs) && $hrs !== 0) {
			$hrs = 0;
		}
		$this->ts = mktime($hrs, $minute, $_second, $mnth, $dy, $yr);
		$this->setDateTime($this->ts);
	}

	/**
	 * function to get mysql formatted date
	 * @return string formatted date
	 */
	public function get_formatted_date() {
		$date = $this->year.'-'.$this->z_month.'-'.$this->z_day;
		return DateTimeField::convertToUserFormat($date);
	}

	/**
	 *
	 * @return Date
	 */
	public function get_DB_formatted_date() {
		return $this->year.'-'.$this->z_month.'-'.$this->z_day;
	}

	/**
	 * function to get mysql formatted time
	 * @return string formatted time
	 */
	public function get_formatted_time() {
		return (empty($this->z_hour) ? '00' : $this->z_hour).':'.(empty($this->minute) ? '00' : $this->minute);
	}

	/**
	 * function to get date depends on mode value
	 * @param string $mode  - 'increment' or 'decrement'
	 * @return object vt_DateTime
	 */
	public function get_changed_day($mode) {
		if ($mode == 'increment') {
			$dy = $this->day + 1;
		} else {
			$dy = $this->day - 1;
		}
		$date_data = array('day'=>$dy, 'month'=>$this->month, 'year'=>$this->year);
		return new vt_DateTime($date_data, true);
	}

	/**
	 * function to get changed week depends on mode value
	 * @param string $mode  - 'increment' or 'decrement'
	 * @return object vt_DateTime
	 */
	public function get_first_day_of_changed_week($mode) {
		$first_day = $this->getThisweekDaysbyIndex(1);
		if ($mode == 'increment') {
			$dy = $first_day->day + 7;
		} else {
			$dy = $first_day->day - 7;
		}
		$date_data = array('day'=>$dy, 'month'=>$first_day->month, 'year'=>$first_day->year);
		return new vt_DateTime($date_data, true);
	}

	/**
	 * function to get month depends on mode value
	 * @param string $mode  - 'increment' or 'decrement'
	 * @return object vt_DateTime
	 */
	public function get_first_day_of_changed_month($mode) {
		$tmpDate['day'] = $this->day;
		$tmpDate['month'] = $this->month;
		$tmpDate['year'] = $this->year;

		if ($mode == 'increment') {
			$mnth = $tmpDate['month'] + 1;
			$yr = $tmpDate['year'] ;
		} else {
			if ($tmpDate['month'] == 1) {
				$mnth = 12;
				$yr = $tmpDate['year'] - 1;
			} else {
				$mnth = $tmpDate['month'] - 1;
				$yr = $tmpDate['year'] ;
			}
		}
		$date_data = array('day'=>1, 'month'=>$mnth, 'year'=>$yr);
		return new vt_DateTime($date_data, true);
	}

	/**
	 * function to get year depends on mode value
	 * @param string $mode  - 'increment' or 'decrement'
	 * @return object vt_DateTime
	 */
	public function get_first_day_of_changed_year($mode) {
		if ($mode == 'increment') {
			$yr = $this->year + 1;
		} else {
			$yr = $this->year - 1;
		}
		$date_data = array('day'=>1, 'month'=>1, 'year'=>$yr);
		return new vt_DateTime($date_data, true);
	}

	/**
	 * function to get date string
	 * @return string date
	 */
	public function get_date_str() {
		$array = array();
		if (isset($this->hour) && $this->hour != '') {
			$array[] = 'hour='.$this->hour;
		}
		if (isset($this->day) && $this->day != '') {
			$array[] = 'day='.$this->day;
		}
		if (isset($this->month) && $this->month) {
			$array[] = 'month='.$this->month;
		}
		if (isset($this->year) && $this->year != '') {
			$array[] = 'year='.$this->year;
		}
		return  ('&'.implode('&', $array));
	}

	/**
	 * function to get days in month using index
	 * @param integer $index - number between 0 to 42
	 * @param string  day
	 * @param string  month
	 * @param string  year
	 * @return object vt_DateTime datetime value
	 */
	public function getThisMonthsDayByIndex($index) {
		$month_array = array();
		$month_array['day'] = $index;
		$month_array['month'] = $this->month;
		$month_array['year'] = $this->year;
		return new vt_DateTime($month_array, true);
	}
}
?>
