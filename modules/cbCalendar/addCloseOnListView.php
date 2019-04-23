<?php
/*************************************************************************************************
 * Copyright 2017 JPL TSolucio, S.L. -- This file is a part of TSOLUCIO coreBOS Customizations.
 * Licensed under the vtiger CRM Public License Version 1.1 (the "License"); you may not use this
 * file except in compliance with the License. You can redistribute it and/or modify it
 * under the terms of the License. JPL TSolucio, S.L. reserves all rights not expressly
 * granted by the License. coreBOS distributed by JPL TSolucio S.L. is distributed in
 * the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied
 * warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. Unless required by
 * applicable law or agreed to in writing, software distributed under the License is
 * distributed on an "AS IS" BASIS, WITHOUT ANY WARRANTIES OR CONDITIONS OF ANY KIND,
 * either express or implied. See the License for the specific language governing
 * permissions and limitations under the License. You may obtain a copy of the License
 * at <http://corebos.org/documentation/doku.php?id=en:devel:vpl11>
 *************************************************************************************************/

class cbCalendar_addCloseOnListView extends VTEventHandler {

	public function handleEvent($handlerType, $entityData) {
	}

	public function handleFilter($handlerType, $parameter) {
		global $currentModule, $adb;
		$relatedList = (isset($_REQUEST['ajxaction']) && isset($_REQUEST['header']) && $_REQUEST['ajxaction']=='LOADRELATEDLIST' && $_REQUEST['header']=='Activities');
		if (($currentModule=='cbCalendar' && $handlerType=='corebos.filter.listview.render' &&
			(($_REQUEST['action']=='ListView' || $_REQUEST['action']=='index') || ($_REQUEST['action']=='cbCalendarAjax' && $_REQUEST['file']=='ListView') ||
				($_REQUEST['action']=='cbCalendarAjax' && $_REQUEST['file']=='calendarops' && $_REQUEST['op']=='changestatus'))) || $relatedList
		) {
			if (!empty($parameter[2])) {
				$evtrs = $adb->pquery('select eventstatus,activitytype from vtiger_activity where activityid=?', array($parameter[2]));
				$status = $adb->query_result($evtrs, 0, 'eventstatus');
				$activitytype = $adb->query_result($evtrs, 0, 'activitytype');
				if ($activitytype != 'Email' && !($status == 'Deferred' || $status == 'Completed' || $status == 'Held' || $status == '')) {
					if (isPermitted('cbCalendar', 'EditView', $parameter[2]) == 'yes') {
						if ($activitytype == 'Task') {
							$evt_status = 'Completed';
						} else {
							$evt_status = 'Held';
						}
						$actionpos = count($parameter[0])-1;
						if ($relatedList) {
							$rlModule = vtlib_purify($_REQUEST['module']);
							$rlRecord = vtlib_purify($_REQUEST['record']);
							$rlRelationID = vtlib_purify($_REQUEST['relation_id']);
							$rlCFilter = isset($_REQUEST['cbcalendar_filter']) ? vtlib_purify($_REQUEST['cbcalendar_filter']) : 'all';
							$lnk = "ajaxChangeCalendarStatus('" . $evt_status . "'," . $parameter[2] . ');';
							$lnk .= "loadRelatedListBlock('module=" . $rlModule . '&action=' . $rlModule . 'Ajax&file=DetailViewAjax&record=' . $rlRecord .
								'&ajxaction=LOADRELATEDLIST&header=Activities&relation_id=' . $rlRelationID . '&cbcalendar_filter=' . $rlCFilter .
								"&actions=add&parenttab=Support','tbl_" . $rlModule . "_Activities','" . $rlModule . "_Activities');";
							$parameter[0][$actionpos] = '<a href="javascript:void(0);" onclick="' . $lnk . 'return false;">' .
								getTranslatedString('LBL_CLOSE', 'cbCalendar') . '</a> | ' . $parameter[0][$actionpos];
						} else {
							$parameter[0][$actionpos] = '<a href="javascript:void(0);" onclick="ajaxChangeCalendarStatus(\''.$evt_status."',".$parameter[2].');">'.
								getTranslatedString('LBL_CLOSE', 'cbCalendar') . '</a> | ' . $parameter[0][$actionpos];
						}
					}
				}
			}
		}
		return $parameter;
	}
}
