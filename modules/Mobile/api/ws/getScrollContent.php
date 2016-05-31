<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/
include_once dirname(__FILE__) . '/models/Alert.php';
include_once dirname(__FILE__) . '/models/SearchFilter.php';
include_once dirname(__FILE__) . '/models/Paging.php';

class Mobile_WS_getScrollContent extends Mobile_WS_Controller {
	
	function isCalendarModule($module) {
		return ($module == 'Events' || $module == 'Calendar');
	}
	
	function getPagingModel(Mobile_API_Request $request) {
		$page = $request->get('page', 0);
		return Mobile_WS_PagingModel::modelWithPageStart($page);
	}
	
	function process(Mobile_API_Request $request) {
		return $this->getContent($request);
	}
	
	function getContent(Mobile_API_Request $request) {
		global $current_user,$adb;
		$current_user = $this->getActiveUser();	
		
		$module = $request->get('module');
		$limit = $request->get('number');
		$offset = $request->get('offset');
		$search = (isset($_REQUEST['src_str'])) ? $request->get('src_str') : '';
		$customView = new CustomView($module);
		
		if(!empty($_REQUEST['view'])) {
			$viewid=$_REQUEST['view'];
		}
		else {
			$viewid = $customView->getViewId($module);
		}
		
		$queryGenerator = new QueryGenerator($module, $current_user);
		if ($viewid != "0") {
			$queryGenerator->initForCustomViewById($viewid);
		} 				
		else {
			$queryGenerator->initForDefaultCustomView();
		}
		$list_query = $queryGenerator->getQuery();

		//get entity fields for each module
		$entity_sql="select fieldname,tablename,entityidfield from vtiger_entityname where modulename =?";
		$ws_entity=$adb->pquery($entity_sql, array($module));
		$fieldname= $adb->query_result($ws_entity,0,'fieldname');
		$tablename= $adb->query_result($ws_entity,0,'tablename');
		
		//set the list and content order
		if ($module =='Contacts' || $module =='Leads') {
			$list_query .= " AND (lastname LIKE '%$search%' OR firstname LIKE '%$search%') ORDER BY lastname";
		}
		elseif ($module !='Calendar' AND $module !='Events') {
			$list_query .= " AND ".$tablename.".".$fieldname." LIKE '%$search%' ORDER BY ".$tablename.".".$fieldname;
		}
		//special handling for calendar (currently display tasks only)
		elseif ($module =='Calendar' || $module =='Events') {
			$calendarview_selected = $request->get('viewName');
			$list_query .= " AND vtiger_activity.activitytype!='Emails'";
			if ($calendarview_selected=='week') {
				$list_query .= " AND week(date_start) = week(NOW()) AND year(date_start) = year(NOW())";
			}
			elseif ($calendarview_selected=='month') {
				$list_query .= " AND month(date_start) = month(NOW()) AND year(date_start) = year(NOW())";
			}
			elseif ($calendarview_selected=='year') {
				$list_query .= " AND year(date_start) = year(NOW())";
			}
			elseif ($calendarview_selected=='today') {
				$list_query .= " AND DATE(date_start) = DATE(NOW())";
			}
			$list_query .= " AND subject LIKE '%$search%' ORDER BY date_start DESC";
		}
		else  {
			$list_query .= " AND ".$tablename.".".$fieldname." LIKE '%$search%' ORDER BY ".$tablename.".".$fieldname;
		}
		$list_query .= " LIMIT $offset, $limit;";
		$listview_entries = $adb->pquery($list_query ,array());
		
		$response = new Mobile_API_Response();
		$response->setResult(array('records'=>$listview_entries, 'module'=>$module));
		
		return $response;
	}
}