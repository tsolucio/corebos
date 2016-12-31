<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

require_once 'modules/Import/resources/Utils.php';
require_once 'modules/Import/ui/Viewer.php';
require_once 'include/QueryGenerator/QueryGenerator.php';

class Import_ListView_Controller {

	var $user;
	var $module;
	static $_cached_module_meta;

	public function  __construct() {
	}

	public static function getModuleMeta($moduleName, $user) {
		if(empty(self::$_cached_module_meta[$moduleName][$user->id])) {
			$moduleHandler = vtws_getModuleHandlerFromName($moduleName, $user);
			self::$_cached_module_meta[$moduleName][$user->id] = $moduleHandler->getMeta();
		}
		return self::$_cached_module_meta[$moduleName][$user->id];
	}

	public static function render($userInputObject, $user) {
		$adb = PearDatabase::getInstance();

		$viewer = new Import_UI_Viewer();

		$ownerId = $userInputObject->get('foruser');
		$owner = new Users();
		$owner->id = $ownerId;
		$owner->retrieve_entity_info($ownerId, 'Users');
		if(!is_admin($user) && $user->id != $owner->id) {
			$viewer->display('OperationNotPermitted.tpl', 'Vtiger');
			exit;
		}
		$userDBTableName = Import_Utils::getDbTableName($owner);

		$moduleName = $userInputObject->get('module');
		$moduleMeta = self::getModuleMeta($moduleName, $user);

		$result = $adb->query('SELECT recordid FROM '.$userDBTableName.' WHERE status is NOT NULL AND recordid IS NOT NULL');
		$noOfRecords = $adb->num_rows($result);

		$importedRecordIds = array();
		for($i=0; $i<$noOfRecords; ++$i) {
			$importedRecordIds[] = $adb->query_result($result, $i, 'recordid');
		}
		if(count($importedRecordIds) == 0) $importedRecordIds[] = 0;


		$focus = CRMEntity::getInstance($moduleName);
		$queryGenerator = new QueryGenerator($moduleName, $user);
		$customView = new CustomView($moduleName);
		$viewId = $customView->getViewIdByName('All', $moduleName);
		$queryGenerator->initForCustomViewById($viewId);
		$list_query = $queryGenerator->getQuery();

		// Fetch only last imported records
		$list_query .= ' AND '.$focus->table_name.'.'.$focus->table_index.' IN ('. implode(',', $importedRecordIds).')';

		if(PerformancePrefs::getBoolean('LISTVIEW_COMPUTE_PAGE_COUNT', false) === true){
			$count_result = $adb->query( mkCountQuery( $list_query));
			$noofrows = $adb->query_result($count_result,0,"count");
		}else{
			$noofrows = null;
		}

		$start = ListViewSession::getRequestCurrentPage($moduleName, $list_query, $viewId, false);
		$list_max_entries_per_page = GlobalVariable::getVariable('Application_ListView_PageSize',20,$moduleName);
		$navigation_array = VT_getSimpleNavigationValues($start,$list_max_entries_per_page,$noofrows);

		$limit_start_rec = ($start-1) * $list_max_entries_per_page;

		$list_result = $adb->pquery($list_query. " LIMIT $limit_start_rec, $list_max_entries_per_page", array());

		$recordListRangeMsg = getRecordRangeMessage($list_result, $limit_start_rec,$noofrows);
		$viewer->assign('recordListRange',$recordListRangeMsg);

		$controller = new ListViewController($adb, $user, $queryGenerator);
		$listview_header = $controller->getListViewHeader($focus,$moduleName,'','','',true);
		$listview_entries = $controller->getListViewEntries($focus,$moduleName,$list_result,$navigation_array,true);

		$viewer->assign('CURRENT_PAGE', $start);
		$viewer->assign('LISTHEADER', $listview_header);
		$viewer->assign('LISTENTITY', $listview_entries);

		$viewer->assign('FOR_MODULE', $moduleName);
		$viewer->assign('FOR_USER', $ownerId);

		$isAjax = $userInputObject->get('ajax');
		if(!empty($isAjax)) {
			echo $viewer->fetch('ListViewEntries.tpl');
		} else {
			$viewer->display('ImportListView.tpl');
		}
	}
}

?>
