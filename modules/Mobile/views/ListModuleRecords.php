<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 * Modified by crm-now GmbH, www.crm-now.com
 ************************************************************************************/
include_once __DIR__ . '/../api/ws/ListModuleRecords.php';
include_once __DIR__ . '/../api/ws/DeleteRecords.php';
include_once __DIR__ . '/../views/models/SearchFilter.php';

class crmtogo_UI_ListModuleRecords extends crmtogo_WS_ListModuleRecords {

	public function cachedModule($moduleName) {
		$modules = $this->sessionGet('_MODULES'); // Should be available post login
		foreach ($modules as $module) {
			if ($module->name() == $moduleName) {
				return $module;
			}
		}
		return false;
	}

	/** For search capability */
	public function cachedSearchFields($module) {
		$cachekey = "_MODULE.{$module}.SEARCHFIELDS";
		return $this->sessionGet($cachekey, false);
	}

	public function process(crmtogo_API_Request $request) {
		global $current_user,$default_timezone;
		$wsResponse = parent::process($request);
		//delete?
		if ($request->get('delaction')== 'deleteEntity') {
			$recordid = $request->get('record');
			if (trim($recordid) !='') {
				//delete record
				crmtogo_WS_DeleteRecords::process($request);
			}
		}
		$current_user = $this->getActiveUser();
		$current_language = $this->sessionGet('language') ;
		$current_module_strings = return_module_language($current_language, 'Mobile');

		$response = false;
		if ($wsResponse->hasError()) {
			$response = $wsResponse;
		} else {
			$wsResponseResult = $wsResponse->getResult();
			//$tabid = getTabid($wsResponseResult['module']);
			$CATEGORY = getParentTabFromModule($wsResponseResult['module']);
			if (($request->get('compact') !='true')) {
				$customView = new CustomView($wsResponseResult['module']);
				$id1=$request->get('viewName');
				$id2=$request->get('view');
				if ($id2!=='' && $id2=='1') {
					$viewid=$id1;
				} else {
					$_REQUEST['action'] = 'ListView';
					$viewid = $customView->getViewId($wsResponseResult['module']);
				}
				$customview_html = $customView->getCustomViewCombo($viewid);
				$customview_html = str_replace('></option>', '>'.$current_module_strings['LBL_FILTER'].'</option>', $customview_html);

				$viewinfo = $customView->getCustomViewByCvid($viewid);
			} else {
				//compact calendar
				$response = new crmtogo_API_Response();
				if ($request->get('module')== 'cbCalendar' && $request->get('compact')== true) {
					$datetime_displayed = $request->get('datetime');
					$month = date('m', strtotime($datetime_displayed));
				}
				//prepare calendar format
				$calendar_records = array ();
				foreach ($wsResponseResult['records'] as $calarray) {
					$activity_type =  $calarray['eventtype'];
					$cal_id =  $calarray['id'];
					// format start date and time
					$cal_startdate = $calarray['eventstartdate'];
					$cal_starttime = $calarray['eventstarttime'];
					//consider time zone
					$DBstart_datetime = $cal_startdate.' '.$cal_starttime;
					if ($default_timezone != 'UTC') {
						$convertdatetime = DateTimeField::convertTimeZone($DBstart_datetime, $default_timezone, 'UTC');
						$start_datetime = $convertdatetime->format('Y-m-d H:i');
					} else {
						$start_datetime = $DBstart_datetime;
					}
					$tmp_date_arr = explode(' ', $start_datetime);
					$formated_date = $tmp_date_arr[0];
					$userStartDate =date('Y-m-d', strtotime($tmp_date_arr[0])).'T'.$tmp_date_arr[1].'Z';
					// format end date and time
						$cal_enddate = $calarray['eventenddate'];
					if (isset($calarray['eventendtime'])) {
						$cal_endtime = $calarray['eventendtime'];
					} else {
						$cal_endtime = '00:00:00';
					}
					//consider time zone
					$DBend_datetime = $cal_enddate.' '.$cal_endtime;
					if ($default_timezone != 'UTC') {
						$convertdatetime = DateTimeField::convertTimeZone($DBend_datetime, $default_timezone, 'UTC');
						$end_datetime = $convertdatetime->format('Y-m-d H:i');
					} else {
						$end_datetime = $DBend_datetime;
					}
					$tmp_date_arr = explode(' ', $end_datetime);
					$formated_date = $tmp_date_arr[0];
					$userEndDate =date('Y-m-d', strtotime($tmp_date_arr[0])).'T'.$tmp_date_arr[1].'Z';
					$record_infos = array(
						'summary' => $calarray['label'],
						'begin' =>$userStartDate,
						'end' => $userEndDate,
						'id' => $cal_id,
						'activity_type' => $activity_type
					);
					$calendar_records[] = $record_infos;
				}
				$response->addToResult('records', $calendar_records);
				$response->addToResult('type', 'json');
				return $response;
			}
			$viewer = new crmtogo_UI_Viewer();

			if ($viewinfo['viewname'] == 'All' || $viewinfo['viewname'] == '') {
				$viewer->assign('_ALL', 'ALL');
			}
			//for compact calendar start day
			if (isset($current_user->dayoftheweek)) {
				$startday = $current_user->dayoftheweek;
			} else {
				global $adb;
				$result = $adb->pquery('SELECT dayoftheweek FROM its4you_calendar4you_settings WHERE userid=?', array($current_user->id));
				if ($adb && $adb->num_rows($result)>0) {
					$startday = $adb->query_result($result, 0, 0);
				} else {
					$startday = 'Monday';
				}
			}
			if ($startday == 'Monday') {
				$startday_code = 1;
			} else {
				$startday_code = 0;
			}

			global $current_user;

			$current_user = $this->getActiveUser();
			$config = $this->getUserConfigSettings();
			$viewer->assign('MOD', $this->getUsersLanguage());
			$viewer->assign('COLOR_HEADER_FOOTER', $config['theme']);
			$viewer->assign('PAGELIMIT', $config['NavigationLimit']);
			$viewer->assign('_MODULE', $this->cachedModule($wsResponseResult['module']));
			$viewer->assign('_MODE', $request->get('mode'));
			$viewer->assign('_CATEGORY', $CATEGORY);
			$viewer->assign('_CUSTOMVIEW_OPTION', $customview_html);
			$viewer->assign('_SEARCH', $request->get('search'));
			$viewer->assign('LANGUAGE', $current_language);
			$viewer->assign('_VIEW', $viewid);
			$viewer->assign('CALSTARTDAY', $startday_code);
			$viewer->assign('CALENDARSELECT', $config['compactcalendar']);
			//Get PanelMenu data
			$modules = $this->sessionGet('_MODULES');
			$viewer->assign('_MODULES', $modules);

			$response = $viewer->process('ListView.tpl');
		}
		return $response;
	}
}
?>