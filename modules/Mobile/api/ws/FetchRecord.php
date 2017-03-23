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
include_once 'include/Webservices/Retrieve.php';

class crmtogo_WS_FetchRecord extends crmtogo_WS_Controller {
	
	private $module = false;
	
	protected $resolvedValueCache = array();
	
	protected function detectModuleName($recordid) {
		if($this->module === false) {
			$this->module = crmtogo_WS_Utils::detectModulenameFromRecordId($recordid);
		}
		return $this->module;
	}
	
	protected function processRetrieve(crmtogo_API_Request $request,$module) {
		global $current_language;
		if(empty($current_language))
			$current_language = crmtogo_WS_Controller::sessionGet('language');
		$current_user = $this->getActiveUser();
		$recordid = $request->get('record');
		$record = vtws_retrieve($recordid, $current_user);
		//on v6.4.0 for products, taxclass information are not retrieved by vtws_retrieve
		if (!empty ($record) and $module =='Products') {
			$record['taxclass'] = crmtogo_WS_Utils::gettaxclassInformation($record['id']);
		}
		//on v6.4.0 for documents, detailed file information are not retrieved by vtws_retrieve
		if (!empty ($record) and $module =='Documents') {
			$record = crmtogo_WS_Utils::getDetailedDocumentInformation($record);
		}
		return $record;
	}
	
	function process(crmtogo_API_Request $request) {
		$response = new crmtogo_API_Response();
		$current_user = $this->getActiveUser();
		//$module = $request->get('module');
		$module = $this->detectModuleName($request->get('record'));
		$record = $this->processRetrieve($request, $module);
		$record['createdtime'] = DateTimeField::convertToUserFormat($record['createdtime']);
		$record['modifiedtime'] = DateTimeField::convertToUserFormat($record['modifiedtime']);
		//set related values
		$this->resolveRecordValues($record, $current_user);
		$ret_arr = array('record' => $record);
		if ($request->get('module')) {
			$module = $request->get('module');
			$moduleWSFieldNames =  crmtogo_WS_Utils::getEntityFieldnames($module);
			foreach ($moduleWSFieldNames as $key=>$value) {
				$relatedlistcontent[$key]=$record[$value];
			}
			$relatedlistcontent['id']=$record['id'];
			$ret_arr['relatedlistcontent'] = $relatedlistcontent;
		}
		//crm-now: fetch ModComments if active, but not for trouble tickets
		elseif (vtlib_isModuleActive('ModComments') AND $module!='HelpDesk') {
			include_once 'include/Webservices/Query.php';
			$comments = vtws_query("SELECT * FROM ModComments WHERE related_to = '".$record['id']."' ORDER BY createdtime DESC LIMIT 5;", $current_user);
			if (count($comments) > 0) {
				foreach ($comments AS &$comment) {
					$comment['assigned_user_id'] = vtws_getName($comment['assigned_user_id'], $current_user);
					$comment['createdtime'] = DateTimeField::convertToUserFormat($comment['createdtime']);
				}
				$ret_arr['comments'] = $comments;
			} 
			else {
				$ret_arr['comments'] = array();
			}
		}
		//crm-now: fetch Comments for trouble tickets
		elseif ($module =='HelpDesk') {
			//there is currently no vtws service for ticket comments
			$comments = crmtogo_WS_Utils::getTicketComments($record);
			if (!empty($comments)) {
				foreach ($comments AS &$comment) {
					$comment['assigned_user_id'] = vtws_getName($comment['assigned_user_id'], $current_user);
					$comment['createdtime'] = DateTimeField::convertToUserFormat($comment['createdtime']);
				}
				$ret_arr['comments'] = $comments;
			} 
			else {
				$ret_arr['comments'] = array();
			}
		}
		$response->setResult($ret_arr);
		return $response;
	}
	
	function resolveRecordValues(&$record, $user, $ignoreUnsetFields=false) {
		if(empty($record)) {
			return $record;
		}
		$fieldnamesToResolve = crmtogo_WS_Utils::detectFieldnamesToResolve(
			$this->detectModuleName($record['id']) );
		
		if(!empty($fieldnamesToResolve)) {
			foreach($fieldnamesToResolve as $resolveFieldname) {
				if ($ignoreUnsetFields === false || isset($record[$resolveFieldname])) {
					$fieldvalueid = $record[$resolveFieldname];
					$fieldvalue = $this->fetchRecordLabelForId($fieldvalueid, $user);
					$record[$resolveFieldname] = array('value' => $fieldvalueid, 'label'=>$fieldvalue);
				}
			}
		}
	}
	
	function fetchRecordLabelForId($id, $user) {
		$value = null;
		
		if (isset($this->resolvedValueCache[$id])) {
			$value = $this->resolvedValueCache[$id];
		} else if(!empty($id)) {
			$value = trim(vtws_getName($id, $user));
			$this->resolvedValueCache[$id] = $value;
		} else {
			$value = $id;
		}
		return $value;
	}
}