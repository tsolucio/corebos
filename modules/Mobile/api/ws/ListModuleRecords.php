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
include_once __DIR__ . '/models/SearchFilter.php';

class crmtogo_WS_ListModuleRecords extends crmtogo_WS_Controller {

	public function isCalendarModule($module) {
		return ($module == 'cbCalendar');
	}

	public function getSearchFilterModel($module, $search) {
		return crmtogo_WS_SearchFilterModel::modelWithCriterias($module, json_decode($search, true));
	}

	public function process(crmtogo_API_Request $request) {
		return $this->processSearchRecordLabel($request);
	}

	public function processSearchRecordLabel(crmtogo_API_Request $request) {
		$current_user = $this->getActiveUser();
		$module = $request->get('module');
		$filterid = $request->get('filterid');
		$search = $request->get('search');
		$filterOrAlertInstance = false;
		if (!empty($filterid)) {
			$filterOrAlertInstance = crmtogo_WS_FilterModel::modelWithId($module, $filterid);
		} elseif (!empty($search)) {
			$filterOrAlertInstance = $this->getSearchFilterModel($module, $search);
		}

		if ($filterOrAlertInstance && strcmp($module, $filterOrAlertInstance->moduleName)) {
			$response = new crmtogo_API_Response();
			$response->setError(1001, 'Mismached module information.');
			return $response;
		}

		// Initialize with more information
		if ($filterOrAlertInstance) {
			$filterOrAlertInstance->setUser($current_user);
		}
		if ($this->isCalendarModule($module)) {
			if ($request->get('compact')== true) {
				//no limits for compact calendar
				return $this->processSearchRecordLabelForCalendar($request, false);
			} else {
				return $this->processSearchRecordLabelForCalendar($request, true);
			}
		}
		$records = $this->fetchRecordLabelsForModule($module, $current_user, array(), $filterOrAlertInstance, true);

		$modifiedRecords = array();
		foreach ($records as $record) {
			if ($record instanceof SqlResultIteratorRow) {
				$record = $record->data;
				// Remove all integer indexed mappings
				for ($index = count($record); $index > -1; --$index) {
					if (isset($record[$index])) {
						unset($record[$index]);
					}
				}
			}

			$recordid = $record['id'];
			unset($record['id']);

			$eventstart = '';
			if ($this->isCalendarModule($module)) {
				$eventstart = $record['date_start'];
				unset($record['date_start']);
			}

			$values = array_values($record);
			$label = implode(' ', $values);

			$modifiedRecord = array('id' => $recordid, 'label'=>$label);
			if (!empty($eventstart)) {
				$modifiedRecord['eventstart'] = $eventstart;
			}
			$modifiedRecords[] = $modifiedRecord;
		}

		$response = new crmtogo_API_Response();
		$response->setResult(array('records'=>$modifiedRecords, 'module'=>$module));

		return $response;
	}

	public function processSearchRecordLabelForCalendar(crmtogo_API_Request $request, $paging = false) {
		$current_user = $this->getActiveUser();

		// Fetch both Calendar (Todo) and Event information
		if ($request->get('compact')== true) {
			//without paging per month
			$datetimeevent=$request->get('datetime');
			if (empty($datetimeevent)) {
				$datestoconsider ['start'] = date('Y-m').'-01';
				$datestoconsider ['end'] =date('Y-m-t');
			} else {
				$strDate = substr($datetimeevent, 4, 11);
				if ($request->get('inweek')== true) {
					$tsDate = strtotime($strDate);
					$daysAfterWeekStart = (date('w', $tsDate)+6)%7; // +6%7 is for monday as first day of week
					$datestoconsider ['start'] = date('Y-m-d', strtotime('-'.$daysAfterWeekStart.' days', $tsDate));
					$datestoconsider ['end'] = date('Y-m-d', strtotime('+'.(6-$daysAfterWeekStart).' days', $tsDate));
				} else {
					$datestoconsider ['start'] = date('Y-m', strtotime($strDate)).'-01';
					$datestoconsider ['end'] =date('Y-m-t', strtotime($strDate));
				}
			}

			$moreMetaFields = array('date_start', 'time_start', 'activitytype', 'location','time_end','due_date');
			$calendarRecords=$this->fetchRecordLabelsForModule('cbCalendar', $current_user, $moreMetaFields, false, false, $datestoconsider);
		} else {
			// with paging
			$moreMetaFields = array('date_start', 'time_start', 'activitytype', 'location','time_end','due_date');
			$calendarRecords=$this->fetchRecordLabelsForModule('cbCalendar', $current_user, $moreMetaFields, false, $paging);
		}
		// Merge the Calendar & Events information
		$records = $calendarRecords;

		$modifiedRecords = array();
		foreach ($records as $record) {
			$modifiedRecord = array();
			$modifiedRecord['id'] = $record['id'];
			$modifiedRecord['eventstartdate'] = $record['date_start'];
			$modifiedRecord['eventstarttime'] = $record['time_start'];
			$modifiedRecord['eventtype'] = $record['activitytype'];
			$modifiedRecord['eventlocation'] = $record['location'];
			$modifiedRecord['eventendtime'] = $record['time_end'];
			$modifiedRecord['eventenddate'] = $record['due_date'];
			unset($record['id'], $record['date_start'], $record['time_start'], $record['activitytype'], $record['location'], $record['time_end'], $record['due_date']);

			$modifiedRecord['label'] = implode(' ', array_values($record));
			$modifiedRecords[] = $modifiedRecord;
		}

		$response = new crmtogo_API_Response();
		$response->setResult(array('records' =>$modifiedRecords, 'module'=>'cbCalendar'));

		return $response;
	}

	public function fetchRecordLabelsForModule($module, $user, $morefields = array(), $filterOrAlertInstance = false, $paging = false, $calfilter = '') {
		if ($this->isCalendarModule($module)) {
			$fieldnames = crmtogo_WS_Utils::getEntityFieldnames('cbCalendar');
		} else {
			$fieldnames = crmtogo_WS_Utils::getEntityFieldnames($module);
		}
		if (!empty($morefields)) {
			foreach ($morefields as $fieldname) {
				$fieldnames[] = $fieldname;
			}
		}

		if ($filterOrAlertInstance === false) {
			$filterOrAlertInstance = crmtogo_WS_SearchFilterModel::modelWithCriterias($module);
			$filterOrAlertInstance->setUser($user);
		}
		return $this->queryToSelectFilteredRecords($module, $fieldnames, $filterOrAlertInstance, $paging, $calfilter);
	}

	public function queryToSelectFilteredRecords($module, $fieldnames, $filterOrAlertInstance, $paging, $calfilter = '') {
		if ($filterOrAlertInstance instanceof crmtogo_WS_SearchFilterModel) {
			if ($module == 'cbCalendar' && $calfilter !='') {
				return $filterOrAlertInstance->execute($fieldnames, $paging, $calfilter);
			} else {
				return $filterOrAlertInstance->execute($fieldnames, $paging);
			}
		}
		$moduleWSId = crmtogo_WS_Utils::getEntityModuleWSId($module);
		$columnByFieldNames = crmtogo_WS_Utils::getModuleColumnTableByFieldNames($module, $fieldnames);

		// Build select clause similar to Webservice query
		$selectColumnClause = "CONCAT('{$moduleWSId}','x',vtiger_crmentity.crmid) as id,";
		foreach ($columnByFieldNames as $fieldname => $fieldinfo) {
			$selectColumnClause .= sprintf('%s.%s as %s,', $fieldinfo['table'], $fieldinfo['column'], $fieldname);
		}
		$selectColumnClause = rtrim($selectColumnClause, ',');

		$query = $filterOrAlertInstance->query();
		$query = preg_replace('/SELECT.*FROM(.*)/i', "SELECT $selectColumnClause FROM $1", $query);

		if ($paging !== false) {
			$config = crmtogo_WS_Controller::getUserConfigSettings();
			$query .= ' LIMIT '.$config['NavigationLimit'];
		}

		$db = PearDatabase::getInstance();
		$prequeryResult = $db->pquery($query, $filterOrAlertInstance->queryParameters());
		return new SqlResultIterator($db, $prequeryResult);
	}
}
