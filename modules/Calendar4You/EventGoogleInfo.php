<?php
/*********************************************************************************
* The content of this file is subject to the Calendar4You Free license.
* ("License"); You may not use this file except in compliance with the License
* The Initial Developer of the Original Code is IT-Solutions4You s.r.o.
* Portions created by IT-Solutions4You s.r.o. are Copyright(C) IT-Solutions4You s.r.o.
* All Rights Reserved.
********************************************************************************/
include_once 'modules/Calendar4You/GoogleSync4You.php';
require_once 'modules/Calendar4You/Calendar4You.php';
require_once 'modules/Calendar4You/CalendarUtils.php';

global $app_strings, $current_user, $current_language, $app_strings;

$eventid = $_REQUEST['eventid'];
$userid = $_REQUEST['userid'];
$geventid = $_REQUEST['geventid'];
$typeid = $_REQUEST['typeid'];
error_reporting(0);

$GoogleSync4You = new GoogleSync4You();
$have_access_data = $GoogleSync4You->setAccessDataForUser($userid);
if ($have_access_data) {
	$GoogleSync4You->connectToGoogle();

	if ($GoogleSync4You->isLogged()) {
		$add_into_vtiger = true;
		$c_time_zone = new DateTimeZone($current_user->time_zone);
		if ($current_user->hour_format == '') {
			$format = '24';
		} else {
			$format = $current_user->hour_format;
		}
		$c_mod_strings = return_specified_module_language($current_language, 'Calendar');
		$event = $GoogleSync4You->getGoogleCalEvent($geventid);
		$recurrence = $event->recurrence;
		if ($recurrence != '') {
			$add_into_vtiger = false;
			$Lines = explode('<br />', nl2br($recurrence));
			foreach ($Lines as $line) {
				$isdate = false;
				$LA = explode(':', $line);
				$convert = trim($LA[0]);
				if (substr($convert, 0, 2) == 'DT') {
					list($n,$timezone) = explode('=', $convert);
					$is_full_day_event = false;
				}

				if ($timezone != '') {
					if (substr($convert, 0, 7) == 'DTSTART') {
						$startdatetime = new DateTime($LA[1], new DateTimeZone($timezone));
						$startdatetime->setTimeZone($c_time_zone);
						$user_date_start = DateTimeField::convertToUserFormat($startdatetime->format('Y-m-d'));

						if (!$is_full_day_event) {
							$user_time_start = $startdatetime->format('H:i');
						} else {
							$user_time_start = '00:00';
						}
					} elseif (substr($convert, 0, 5) == 'DTEND') {
						$enddatetime = new DateTime($LA[1], new DateTimeZone($timezone));
						$enddatetime->setTimeZone($c_time_zone);
						$user_date_end = DateTimeField::convertToUserFormat($enddatetime->format('Y-m-d'));

						if (!$is_full_day_event) {
							$user_time_end = $enddatetime->format('H:i');
						} else {
							$user_time_end = '00:00';
						}
					}
				}
			}
		} else {
			$When = $event->getWhen();

			$start_time_lenght = strlen($When[0]->getStartTime());
			if ($start_time_lenght == 10) {
				$is_full_day_event = true;
			} else {
				$is_full_day_event = false;
			}

			$startdatetime = new DateTime($When[0]->getStartTime());
			$startdatetime->setTimeZone($c_time_zone);

			$user_date_start = DateTimeField::convertToUserFormat($startdatetime->format('Y-m-d'));
			if (!$is_full_day_event) {
				$user_time_start = $startdatetime->format('H:i');
			} else {
				$user_time_start = '00:00';
			}

			$enddatetime = new DateTime($When[0]->getEndTime());
			$enddatetime->setTimeZone($c_time_zone);

			$user_date_end = DateTimeField::convertToUserFormat($enddatetime->format('Y-m-d'));
			if (!$is_full_day_event) {
				$user_time_end = $enddatetime->format('H:i');
			} else {
				$user_time_end = '00:00';
			}
		}

		$time_arr = getaddEventPopupTime($user_time_start, $user_time_end, $format);

		if ($typeid == 'task') {
			$typename = 'todo';
		} else {
			$typename = getActTypeForCalendar($typeid, false);
		}

		if ($add_into_vtiger) {
			echo "<span style='font-size:12px'>".$app_strings['LBL_ACTION'].': ';
			echo "<a href=\"javascript:insertIntoCRM('".$userid."','".$eventid."','".$typename."','".$event->id->text."','".$user_date_start."','".$user_date_end."','"
				.$time_arr['starthour']."','".$time_arr['startmin']."','".$time_arr['startfmt']."','".$time_arr['endhour']."','".$time_arr['endmin']."','"
				.$time_arr['endfmt']."')\">".$mod_strings['LBL_INSERT_INTO_CRM'].'</a>';
			echo '</span>';
		}
		echo "<div style='float:right'><img src='modules/Calendar4You/images/sync_icon_small2.png'></div>";
		if ($add_into_vtiger) {
			echo '<hr>';
		}

		echo "<div id='google_info_".$eventid."_title' style='font-size:12px'>" . $event->title->text . '</div>';
		echo $app_strings['Description'].": <span id='google_info_".$eventid."_desc'>" . $event->content->text . '</span><br />';
		echo $app_strings[($is_full_day_event?'LBL_START_DATE':'LBL_START_DATE_TIME')].': ' . $user_date_start;
		if (!$is_full_day_event) {
			echo ' '.$time_arr['starthour'].':'.$time_arr['startmin'].$time_arr['startfmt'];
		}
		echo '<br />';

			echo $app_strings[($is_full_day_event?'LBL_END_DATE':'LBL_END_DATE_TIME')].': ' . $user_date_end;
		if (!$is_full_day_event) {
			echo ' '.$time_arr['endhour'].':'.$time_arr['endmin'].$time_arr['endfmt'];
		}
		echo '<br />';
		echo $c_mod_strings['Location'].": <span id='google_info_".$eventid."_location'>" .implode(',', $event->where).'</span><br />'; //$Where[0];
	} else {
		echo $GoogleSync4You->getStatus();
	}
}
function extract_recurrence($ical_string) {
	$vevent_rawstr = '/(?ims)BEGIN:VEVENT(.*)END:VEVENT/';
	preg_match($vevent_rawstr, $ical_string, $matches);
	$vevent_str = $matches[1];
	# now look for DTSTART, DTEND, RRULE, RDATE, EXDATE, and EXRULE
	$rep_tags = array('DTSTART', 'DTEND', 'RRULE', 'RDATE', 'EXDATE', 'EXRULE');
	$recur_list = array();
	foreach ($rep_tags as $rep) {
		$rep_regexp = "/({$rep}(.*))/i";
		if (preg_match_all($rep_regexp, $vevent_str, $rmatches)) {
			foreach ($rmatches[0] as $match) {
				$recur_list[]= $match;
			}
		}
	} //foreach $rep
	return implode($recur_list, "\r\n");
}
?>