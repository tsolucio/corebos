<?php
/*************************************************************************************************
 * Copyright 2022 JPL TSolucio, S.L. -- This file is a part of TSOLUCIO coreBOS customizations.
 * You can copy, adapt and distribute the work under the "Attribution-NonCommercial-ShareAlike"
 * Vizsage Public License (the "License"). You may not use this file except in compliance with the
 * License. Roughly speaking, non-commercial users may share and modify this code, but must give credit
 * and share improvements. However, for proper details please read the full License, available at
 * http://vizsage.com/license/Vizsage-License-BY-NC-SA.html and the handy reference for understanding
 * the full license at http://vizsage.com/license/Vizsage-Deed-BY-NC-SA.html. Unless required by
 * applicable law or agreed to in writing, any software distributed under the License is distributed
 * on an  "AS IS" BASIS, WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and limitations under the
 * License terms of Creative Commons Attribution-NonCommercial-ShareAlike 3.0 (the License).
 *************************************************************************************************/
// block://pendingactionswidget:modules/Utilities/pendingActions.php:relwith=$RECORD$&limit=5&showascards=1
// top://pendingactionswidget:modules/Utilities/pendingActions.php:relwith=$RECORD$&limit=5&showascards=1
// module=Utilities&action=UtilitiesAjax&file=pendingActions&relwith=$RECORD$&limit=5&showascards=1

require_once 'modules/Vtiger/DeveloperWidget.php';
require_once 'modules/cbCalendar/cbCalendar.php';
require_once 'modules/cbCalendar/CalendarCommon.php';
global $currentModule;

class pendingactionswidget {
	// Get class name of the object that will implement the widget functionality
	public static function getWidget($name) {
		return (new pendingactionswidget_DetailViewBlock());
	}
}

class pendingactionswidget_DetailViewBlock extends DeveloperBlock {

	protected $widgetName = 'pendingActionsWidget';

	public function process($context = false) {
		global $adb, $current_user;
		$this->context = $context;
		$smarty = $this->getViewer();
		$relwith = $this->getFromContext('relwith');
		$relwith = preg_replace('/[^0-9]/', '', $relwith);
		$relwith = is_numeric($relwith) ? $relwith : 0;
		$limit = $this->getFromContext('limit');
		$limit = preg_replace('/[^0-9]/', '', $limit);
		$limit = is_numeric($limit) ? $limit : 5;
		$Calendar_PopupReminder_DaysPast = GlobalVariable::getVariable('Calendar_PopupReminder_DaysPast', 7, 'cbCalendar');
		$active = $adb->pquery('select reminder_interval from vtiger_users where id=?', array($current_user->id));
		$interval = $adb->query_result($active, 0, 'reminder_interval');
		$intervalInMinutes = $interval=='None' ? 15 : ConvertToMinutes($interval);
		$time = time();
		$date = date('Y-m-d', strtotime("+$intervalInMinutes minutes", $time));
		$date_inpast = date('Y-m-d', strtotime('-'.$Calendar_PopupReminder_DaysPast.' day', $time));
		$time = date('H:i', strtotime("+$intervalInMinutes minutes", $time));
		$callback_query = cbCalendar::getActionsQuery($current_user, $date, $date_inpast, $time, $limit, 1, $relwith);
		$result = $adb->query($callback_query);
		$cbrows = $adb->num_rows($result);
		if ($cbrows==0) {
			$smarty->assign('NOTASKSInfo', getTranslatedString('TASKS_FINISHED', 'Calendar4You'));
			$smarty->assign('NOTASKSSize', 'small');
			return $smarty->fetch('Components/NoTasks.tpl');
		}
		$activities = array();
		for ($index = 0; $index < $cbrows; $index++) {
			$reminderid = $adb->query_result($result, $index, 'reminderid');
			$cbrecord = $adb->query_result($result, $index, 'recordid');
			$cbmodule = $adb->query_result($result, $index, 'semodule');
			$cbreaded = $adb->query_result($result, $index, 'readed');
			$moreinfo = $adb->query_result($result, $index, 'moreinfo');
			$cbdate = $adb->query_result($result, $index, 'date_start');
			$cbtime = $adb->query_result($result, $index, 'time_start');
			$cbaction = $adb->query_result($result, $index, 'moreaction');
			$activities[] = cbCalendar::getActionElement($reminderid, $cbmodule, $cbrecord, $moreinfo, $cbdate, $cbtime, $cbaction, $cbreaded);
		}
		$showascards = $this->getFromContext('showascards');
		if ($showascards) {
			return '<ul>'.cbCalendar::printToDoListCards($activities).'</ul>';
		} else {
			return cbCalendar::printToDoListTable($activities);
		}
	}
}

if (isset($_REQUEST['action']) && $_REQUEST['action']==$currentModule.'Ajax') {
	$smq = new pendingactionswidget_DetailViewBlock();
	echo $smq->process($_REQUEST);
}
