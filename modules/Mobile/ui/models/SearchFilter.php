<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/
include_once dirname(__FILE__) . '/../../api/ws/models/SearchFilter.php';

class Mobile_UI_SearchFilterModel extends Mobile_WS_SearchFilterModel {
	
	function prepareWhereClause($fieldnames = false) {
		$whereClause = '';
		
		$searchString = $this->criterias['search'];
		$fieldnames = (isset($this->criterias['fieldnames']))? $this->criterias['fieldnames'] : false;
		
		include_once 'include/Webservices/DescribeObject.php';
		$describeInfo = vtws_describe($this->moduleName, $this->getUser());
		
		$fieldinfos = array();
		if ($fieldnames === false) {
			foreach($describeInfo['fields'] as $fieldinfo) {
				$fieldmodel = new Mobile_UI_FieldModel();
				$fieldmodel->initData($fieldinfo);
				
				if (!$fieldmodel->isReferenceType()) {
					$fieldinfos[$fieldinfo['name']] = $fieldmodel;
				}
			}
			
		} else {
			foreach($describeInfo['fields'] as $fieldinfo) {
				if(in_array($fieldinfo['name'], $fieldnames)) {
					$fieldmodel = new Mobile_UI_FieldModel();
					$fieldmodel->initData($fieldinfo);
				
					if (!$fieldmodel->isReferenceType()) {
						$fieldinfos[$fieldinfo['name']] = $fieldmodel;
					}
				}
			}
		}
		
		if(isset($fieldinfos['id'])) unset($fieldinfos['id']);
		if(!empty($fieldinfos)) {
			$fieldinfos['_'] = ''; // Hack to build the where clause at once
			$whereClause = sprintf("WHERE %s", implode(" LIKE '%{$searchString}%' OR ", array_keys($fieldinfos)));
			$whereClause = rtrim($whereClause, 'OR _');
		}
		
		return $whereClause;
	}
	
	function execute($fieldnames, $pagingModel = false) {
			if($this->moduleName == 'Project')
				{  
					// Custom View
				include_once 'modules/CustomView/CustomView.php';
				include_once 'include/QueryGenerator/QueryGenerator.php';
				include_once 'modules/Mobile/api/ws/Controller.php';
				include_once('include/DatabaseUtil.php');
		
				$customView = new CustomView($this->moduleName);
				$viewid = $customView->getViewId($this->moduleName);
				$customview_html = $customView->getCustomViewCombo($viewid);
				$viewinfo = $customView->getCustomViewByCvid($viewid);
		         
				global $current_user; // Required for vtws_update API
				$userid = $_SESSION['_authenticated_user_id'];
				$current_user = CRMEntity::getInstance('Users');
				$current_user = $current_user->retrieveCurrentUserInfoFromFile($userid);
		
				$queryGenerator = new QueryGenerator($this->moduleName, $current_user);
				if ($viewid != "0") {
					$queryGenerator->initForCustomViewById($viewid);
				} else {
					$queryGenerator->initForDefaultCustomView();
					}
		
		
				$selectClause = sprintf("SELECT %s", implode(',', $fieldnames).",vtiger_project.projectid");
				$fromClause = $queryGenerator->getFromClause();
				$whereClause = $queryGenerator->getWhereClause();
				$orderClause = "";
				$groupClause = "";
				$limitClause = $pagingModel? " LIMIT {$pagingModel->currentCount()},{$pagingModel->limit()}" : "" ;
		
				if (!empty($this->criterias)) {
						$_sortCriteria = $this->criterias['_sort'];
							if(!empty($_sortCriteria)) {
							$orderClause = $_sortCriteria;
						}
				}
		
				$query = sprintf("%s %s %s %s %s %s;", $selectClause, $fromClause, $whereClause, $orderClause, $groupClause, $limitClause);
				
				global $adb;
				$result = $adb->pquery($query,array());
				$noofrows = $adb->num_rows($result);
				$lstresult = array();
				for($i=0;$i<$noofrows;$i++)
				{
					$lstresult[$i]['firstname'] = $adb->query_result($result,$i,'projectname');
					$lstresult[$i]['id']= "31x".$adb->query_result($result,$i,'projectid');
				
				}
				
				return $lstresult;
			} 
			else
			   {
				$selectClause = sprintf("SELECT %s", implode(',', $fieldnames));
				$fromClause = sprintf("FROM %s", $this->moduleName);
		
				$whereClause = "";
				$orderClause = "";
				$groupClause = "";
				$limitClause = $pagingModel? " LIMIT {$pagingModel->currentCount()},{$pagingModel->limit()}" : "" ;
		
				if (!empty($this->criterias)) {
					$_sortCriteria = $this->criterias['_sort'];
					if(!empty($_sortCriteria)) {
						$orderClause = $_sortCriteria;
					}
				}
		 
				$query = sprintf("%s %s %s %s %s %s;", $selectClause, $fromClause, $whereClause, $orderClause, $groupClause, $limitClause);
		
				return vtws_query($query, $this->getUser()); 
		
				}	  
			
	}
	

	static function modelWithCriterias($moduleName, $criterias = false) {
		$model = new Mobile_UI_SearchFilterModel($moduleName);
		$model->setCriterias($criterias);
		return $model;
	}
}