<?php
/*********************************************************************************
* The content of this file is subject to the Calendar4You license.
* ("License"); You may not use this file except in compliance with the License
* The Initial Developer of the Original Code is IT-Solutions4You s.r.o.
* Portions created by IT-Solutions4You s.r.o. are Copyright(C) IT-Solutions4You s.r.o.
* All Rights Reserved.
********************************************************************************/
require_once 'include/fields/DateTimeField.php';
include_once 'modules/Calendar4You/GoogleSync4You.php';
require_once 'modules/Calendar4You/Calendar4You.php';
require_once 'modules/Calendar4You/CalendarUtils.php';

global $adb, $current_user, $default_timezone;

$currentModule = 'Calendar';

$controltime = strtotime('-6 month');
$controldate = date('Y-m-d', $controltime);

$c_time_zone = new DateTimeZone($default_timezone);

$where = "vtiger_activity.due_date > ? AND (vtiger_activity.status != 'Held' OR vtiger_activity.status IS NULL) AND (vtiger_activity.eventstatus != 'Held' OR vtiger_activity.eventstatus IS NULL)";

$sql1 = 'SELECT userid
	FROM its4you_googlesync4you_events
	INNER JOIN vtiger_activity ON vtiger_activity.activityid = its4you_googlesync4you_events.crmid
	WHERE '.$where.' GROUP BY userid';
$result1 = $adb->pquery($sql1, array($controldate));
$num_rows1 = $adb->num_rows($result1);

if ($num_rows1 > 0) {
	while ($row1 = $adb->fetchByAssoc($result1)) {
		$userid = $row1['userid'];

		$GoogleSync4You = new GoogleSync4You();
		$have_access_data = $GoogleSync4You->setAccessDataForUser($userid);
		if ($have_access_data) {
			$GoogleSync4You->connectToGoogle();

			if ($GoogleSync4You->isLogged()) {
				$sql2 = 'SELECT its4you_googlesync4you_events.*, vtiger_activity.activitytype
					FROM its4you_googlesync4you_events 
					INNER JOIN vtiger_activity ON vtiger_activity.activityid = its4you_googlesync4you_events.crmid
					WHERE '.$where.' AND userid = ?';
				$result2 = $adb->pquery($sql2, array($controldate,$userid));

				while ($row2 = $adb->fetchByAssoc($result2)) {
					$eventid = $row2['id'];
					$lastmodified = $row2['lastmodified'];
					$geventid = $row2['geventid'];

					$event = $GoogleSync4You->getGoogleCalEvent($geventid);

					if (!$event) {
						$adb->pquery('DELETE FROM its4you_googlesync4you_events WHERE id = ?', array($eventid));
					} else {
						if ($lastmodified != $event->updated->text) {
							$record = $row2['crmid'];
							$activitytype = $row2['activitytype'];

							if ($activitytype == 'Task') {
								$tab_type = 'Calendar';
							} else {
								$tab_type = 'Events';
							}

							$focus = CRMEntity::getInstance($currentModule);

							$focus->retrieve_entity_info($record, $tab_type);
							$focus->id = $record;
							$focus->mode = 'edit';

							$focus->column_fields['subject'] = $event->title->text;
							$focus->column_fields['description'] = $event->content->text;
							$focus->column_fields['location'] = $event->where->text;

							$recurrence = $event->recurrence;
							if ($recurrence == '') {
								$When = $event->getWhen();

								$start_time_lenght = strlen($When[0]->getStartTime());
								if ($start_time_lenght == 10) {
									$is_full_day_event = true;
								} else {
									$is_full_day_event = false;
								}

								$startdatetime = new DateTime($When[0]->getStartTime());
								$startdatetime->setTimeZone($c_time_zone);
								$new_time_start_time = $startdatetime->format('U');

								$user_date_start = DateTimeField::convertToUserFormat($startdatetime->format('Y-m-d'));
								if (!$is_full_day_event) {
									$user_time_start = $startdatetime->format('H:i');
								} else {
									$user_time_start = '00:00';
								}

								$enddatetime = new DateTime($When[0]->getEndTime());
								$enddatetime->setTimeZone($c_time_zone);
								$new_time_end_time = $enddatetime->format('U');

								$user_date_end = DateTimeField::convertToUserFormat($enddatetime->format('Y-m-d'));
								if (!$is_full_day_event) {
									$user_time_end = $enddatetime->format('H:i');
								} else {
									$user_time_end = '00:00';
								}

								$focus->column_fields['date_start'] = $user_date_start;
								$focus->column_fields['due_date'] = $user_date_end;
								$focus->column_fields['time_start'] = $user_time_start;
								$focus->column_fields['time_end'] = $user_time_end;

								$duration_time = $new_time_end_time - $new_time_start_time;

								$duration_hour = floor($duration_time / 3600);
								$duration_minutes = ($duration_time - ($duration_hour * 3600 ))  / 60;

								$focus->column_fields['duration_hours'] = $duration_hours;
								$focus->column_fields['duration_minutes'] = $duration_minutes;
							}

							$focus->save($currentModule);

							$adb->pquery('UPDATE its4you_googlesync4you_events SET lastmodified = ? WHERE id = ?', array($event->updated->text,$eventid));
						}
					}
				}
			}
		}
	}
}
?>