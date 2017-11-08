<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/
include_once __DIR__ . '/models/SearchFilter.php';

class crmtogo_WS_getScrollContent extends crmtogo_WS_Controller {
	
	function process(crmtogo_API_Request $request) {
		return $this->getContent($request);
	}
	
	function getContent(crmtogo_API_Request $request) {
		$db = PearDatabase::getInstance();
		$current_user = $this->getActiveUser();	
		
		$module = $request->get('module');
		$limit = $request->get('number');
		$offset = $request->get('offset');
		$search =  trim($request->get('src_str'));
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
		$ws_entity=$db->pquery($entity_sql, array($module));
		$fieldname= $db->query_result($ws_entity,0,'fieldname');
		$tablename= $db->query_result($ws_entity,0,'tablename');
		
		//set the list and content order
		if ($module =='Contacts' || $module =='Leads') {
			$list_query .= " AND (lastname LIKE '%$search%' OR firstname LIKE '%$search%') ORDER BY lastname";
		}
		elseif ($module != 'cbCalendar') {
			$list_query .= " AND ".$tablename.".".$fieldname." LIKE '%$search%' ORDER BY ".$tablename.".".$fieldname;
		}
		//special handling for calendar (currently display tasks only)
		elseif ($module == 'cbCalendar') {
			$list_query .= " AND vtiger_activity.activitytype!='Emails'";
			$list_query .= " AND subject LIKE '%$search%' ORDER BY date_start DESC";
		}
		else  {
			$list_query .= " AND ".$tablename.".".$fieldname." LIKE '%$search%' ORDER BY ".$tablename.".".$fieldname;
		}
		$list_query .= " LIMIT $offset, $limit;";
		$listview_entries = $db->pquery($list_query ,array());
		
		$response = new crmtogo_API_Response();
		$response->setResult(array('records'=>$listview_entries, 'module'=>$module));
		
		return $response;
	}
}