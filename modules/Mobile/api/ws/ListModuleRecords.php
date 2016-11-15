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
include_once dirname(__FILE__) . '/models/SearchFilter.php';

class crmtogo_WS_ListModuleRecords extends crmtogo_WS_Controller {
	
	function isCalendarModule($module) {
		return ($module == 'Events' || $module == 'Calendar');
	}
	
	function getSearchFilterModel($module, $search) {
		return crmtogo_WS_SearchFilterModel::modelWithCriterias($module, Zend_JSON::decode($search));
	}
	
	function process(crmtogo_API_Request $request) {
		return $this->processSearchRecordLabel($request);
	}
	
	function processSearchRecordLabel(crmtogo_API_Request $request) {
		$current_user = $this->getActiveUser();
		$module = $request->get('module');
		$alertid = $request->get('alertid');
		$filterid = $request->get('filterid');
		$search = $request->get('search');
		$filterOrAlertInstance = false;
		if(!empty($alertid)) {
			$filterOrAlertInstance = crmtogo_WS_AlertModel::modelWithId($alertid);
		}
		else if(!empty($filterid)) {
			$filterOrAlertInstance = crmtogo_WS_FilterModel::modelWithId($module, $filterid);
		}
		else if(!empty($search)) {
			$filterOrAlertInstance = $this->getSearchFilterModel($module, $search);
		}
		
		if($filterOrAlertInstance && strcmp($module, $filterOrAlertInstance->moduleName)) {
			$response = new crmtogo_API_Response();
			$response->setError(1001, 'Mismached module information.');
			return $response;
		}

		// Initialize with more information
		if($filterOrAlertInstance) {
			$filterOrAlertInstance->setUser($current_user);
		}

		if($this->isCalendarModule($module)) {
			if ($request->get('compact')== true) {
				//no limits for compact calendar
				return $this->processSearchRecordLabelForCalendar($request, false);
			}
			else {
				return $this->processSearchRecordLabelForCalendar($request, true);
			}
		}
		$records = $this->fetchRecordLabelsForModule($module, $current_user, array(), $filterOrAlertInstance, true);

		$modifiedRecords = array();
		foreach($records as $record) {
			if ($record instanceof SqlResultIteratorRow) {
				$record = $record->data;
				// Remove all integer indexed mappings
				for($index = count($record); $index > -1; --$index) {
					if(isset($record[$index])) {
						unset($record[$index]);
					}
				}
			}
			
			$recordid = $record['id'];
			unset($record['id']);
			
			$eventstart = '';
			if($this->isCalendarModule($module)) {
				$eventstart = $record['date_start'];
				unset($record['date_start']);
			}

			$values = array_values($record);
			$label = implode(' ', $values);
			
			$modifiedRecord = array('id' => $recordid, 'label'=>$label); 
			if(!empty($eventstart)) {
				$modifiedRecord['eventstart'] = $eventstart;
			}
			$modifiedRecords[] = $modifiedRecord;
		}
		
		$response = new crmtogo_API_Response();
		$response->setResult(array('records'=>$modifiedRecords, 'module'=>$module));
		
		return $response;
	}
	
	function processSearchRecordLabelForCalendar(crmtogo_API_Request $request, $paging = false) {
		$current_user = $this->getActiveUser();
		// Fetch both Calendar (Todo) and Event information
		if ($request->get('compact')== true) {
			//without paging per month
			$datetimeevent=$request->get('datetime');
			if (empty($datetimeevent)) {
				$datestoconsider ['start'] = date("Y-m").'-01';
				$datestoconsider ['end'] =date("Y-m-t");
			}
			else {
				$strDate = substr($datetimeevent,4,11);
				$datestoconsider ['start'] = date("Y-m",strtotime($strDate))."-01";
				$datestoconsider ['end'] =date("Y-m-t",strtotime($strDate));
			}
			
			$moreMetaFields = array('date_start', 'time_start', 'activitytype', 'location','time_end','due_date');
			$eventsRecords = $this->fetchRecordLabelsForModule('Events', $current_user, $moreMetaFields, false, false,$datestoconsider);
			$calendarRecords=$this->fetchRecordLabelsForModule('Calendar', $current_user, $moreMetaFields, false, false,$datestoconsider);
		}
		else {
			// with paging
			$moreMetaFields = array('date_start', 'time_start', 'activitytype', 'location','time_end','due_date');
			$eventsRecords = $this->fetchRecordLabelsForModule('Events', $current_user, $moreMetaFields, false, $paging);
			$calendarRecords=$this->fetchRecordLabelsForModule('Calendar', $current_user, $moreMetaFields, false, $paging);
		}
		// Merge the Calendar & Events information
		$records = array_merge($eventsRecords, $calendarRecords);
		
		$modifiedRecords = array();
		foreach($records as $record) {
			$modifiedRecord = array();
			$modifiedRecord['id'] = $record['id'];
			unset($record['id']);
			$modifiedRecord['eventstartdate'] = $record['date_start'];  
			unset($record['date_start']);
			$modifiedRecord['eventstarttime'] = $record['time_start'];  
			unset($record['time_start']);
			$modifiedRecord['eventtype'] = $record['activitytype'];     
			unset($record['activitytype']);
			$modifiedRecord['eventlocation'] = $record['location'];     
			unset($record['location']);
			$modifiedRecord['eventendtime'] = $record['time_end'];     
			unset($record['time_end']);
			$modifiedRecord['eventenddate'] = $record['due_date'];     
			unset($record['due_date']);
			
			$modifiedRecord['label'] = implode(' ',array_values($record));
			$modifiedRecords[] = $modifiedRecord;
		}
		
		$response = new crmtogo_API_Response();
		$response->setResult(array('records' =>$modifiedRecords, 'module'=>'Calendar'));
		
		return $response;
	}
	
	function fetchRecordLabelsForModule($module, $user, $morefields=array(), $filterOrAlertInstance=false, $paging = false, $calfilter='') {
		if($this->isCalendarModule($module)) {
			$fieldnames = crmtogo_WS_Utils::getEntityFieldnames('Calendar');
		} 
		else {
			$fieldnames = crmtogo_WS_Utils::getEntityFieldnames($module);
		}
		if(!empty($morefields)) {
			foreach($morefields as $fieldname) $fieldnames[] = $fieldname;
		}

		if($filterOrAlertInstance === false) {
			$filterOrAlertInstance = crmtogo_WS_SearchFilterModel::modelWithCriterias($module);
			$filterOrAlertInstance->setUser($user);
		}
		return $this->queryToSelectFilteredRecords($module, $fieldnames, $filterOrAlertInstance, $paging,$calfilter);
	}
	
	function queryToSelectFilteredRecords($module, $fieldnames, $filterOrAlertInstance, $paging,$calfilter='') {
		if ($filterOrAlertInstance instanceof crmtogo_WS_SearchFilterModel) {
			if (($module == 'Calendar' || $module == 'Events') and $calfilter !='') {
				return $filterOrAlertInstance->execute($fieldnames, $paging,$calfilter);
			}
			else {
				return $filterOrAlertInstance->execute($fieldnames, $paging);
			}
		}
		$moduleWSId = crmtogo_WS_Utils::getEntityModuleWSId($module);
		$columnByFieldNames = crmtogo_WS_Utils::getModuleColumnTableByFieldNames($module, $fieldnames);

		// Build select clause similar to Webservice query
		$selectColumnClause = "CONCAT('{$moduleWSId}','x',vtiger_crmentity.crmid) as id,";
		foreach($columnByFieldNames as $fieldname=>$fieldinfo) {
			$selectColumnClause .= sprintf("%s.%s as %s,", $fieldinfo['table'],$fieldinfo['column'],$fieldname);
		}
		$selectColumnClause = rtrim($selectColumnClause, ',');
		
		$query = $filterOrAlertInstance->query();
		$query = preg_replace("/SELECT.*FROM(.*)/i", "SELECT $selectColumnClause FROM $1", $query);
		
		if ($paging !== false) {
			$config = crmtogo_WS_Controller::getUserConfigSettings();
			$query .= " LIMIT ".$config['NavigationLimit'];
		}

		$db = PearDatabase::getInstance();
		$prequeryResult = $db->pquery($query, $filterOrAlertInstance->queryParameters());
		return new SqlResultIterator($db, $prequeryResult);
	}
	
}
