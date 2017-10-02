<?php
/*********************************************************************************
** The contents of this file are subject to the vtiger CRM Public License Version 1.0
* ("License"); You may not use this file except in compliance with the License
* The Original Code is:  vtiger CRM Open Source
* The Initial Developer of the Original Code is vtiger.
* Portions created by vtiger are Copyright (C) vtiger.
* All Rights Reserved.
********************************************************************************/
require_once('include/logging.php');
require_once('include/ListView/ListViewSession.php');

/**initializes Related ListViewSession */
class RelatedListViewSession {

	var $module = null;
	var $relstart = null;
	var $sorder = null;
	var $sortby = null;
	var $page_view = null;

	function __construct() {
		global $log,$currentModule;
		$log->debug("Entering RelatedListViewSession() method ...");
		$this->module = $currentModule;
		$this->relstart =1;
	}

	public static function addRelatedModuleToSession($relationId, $header) {
		global $currentModule;
		coreBOS_Session::set('relatedlist^'.$currentModule.'^'.$relationId, $header);
		$relstart = RelatedListViewSession::getRequestStartPage();
		RelatedListViewSession::saveRelatedModuleStartPage($relationId, $relstart);
	}

	public static function removeRelatedModuleFromSession($relationId, $header) {
		global $currentModule;
		coreBOS_Session::delete('relatedlist^'.$currentModule.'^'.$relationId);
	}

	public static function getRelatedModulesFromSession() {
		global $currentModule;
		$allRelatedModuleList = isPresentRelatedLists($currentModule);
		$moduleList = array();
		if(isset($_SESSION['relatedlist']) and isset($_SESSION['relatedlist'][$currentModule]) and is_array($_SESSION['relatedlist'][$currentModule])){
			foreach ($allRelatedModuleList as $relationId=>$label) {
				if(array_key_exists($relationId, $_SESSION['relatedlist'][$currentModule])){
					$moduleList[] = $_SESSION['relatedlist'][$currentModule][$relationId];
				}
			}
		}
		return $moduleList;
	}

	public static function saveRelatedModuleStartPage($relationId, $relstart) {
		global $currentModule;
		coreBOS_Session::set('rlvs^'.$currentModule.'^'.$relationId.'^relstart', $relstart);
	}

	public static function getCurrentPage($relationId) {
		global $currentModule;
		if(!empty($_SESSION['rlvs'][$currentModule][$relationId]['relstart'])){
			return $_SESSION['rlvs'][$currentModule][$relationId]['relstart'];
		}
		return 1;
	}

	public static function getRequestStartPage(){
		$relstart = isset($_REQUEST['relstart']) ? $_REQUEST['relstart'] : 1;
		if(!is_numeric($relstart)){
			$relstart = 1;
		}
		if($relstart < 1){
			$relstart = 1;
		}
		$relstart = ceil($relstart);
		return $relstart;
	}

	public static function getRequestCurrentPage($relationId, $query) {
		global $adb,$log,$currentModule;
		$list_max_entries_per_page = GlobalVariable::getVariable('Application_ListView_PageSize',20,$currentModule);
		$relstart = 1;
		if(!empty($_REQUEST['relstart'])){
			$relstart = $_REQUEST['relstart'];
			if($relstart == 'last'){
				$count_result = $adb->query( mkCountQuery( $query));
				$noofrows = $adb->query_result($count_result,0,'count');
				if($noofrows > 0){
					$relstart = ceil($noofrows/$list_max_entries_per_page);
				}
			}
			if(!is_numeric($relstart)){
				$relstart = 1;
			}elseif($relstart < 1){
				$relstart = 1;
			}
			$relstart = ceil($relstart);
		}else {
			$relstart = RelatedListViewSession::getCurrentPage($relationId);
		}
		return $relstart;
	}

}
?>