<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/
include_once __DIR__ . '/../../api/ws/models/SearchFilter.php';

class crmtogo_UI_SearchFilterModel extends crmtogo_WS_SearchFilterModel {

	public function execute($fieldnames, $paging = false, $calwhere = '') {
		global $current_user;
		if ($this->moduleName == 'Project') {
			// Custom View
			include_once 'modules/CustomView/CustomView.php';
			include_once 'include/QueryGenerator/QueryGenerator.php';
			include_once 'modules/crmtogo/api/ws/Controller.php';
			include_once 'include/DatabaseUtil.php';
			$customView = new CustomView($this->moduleName);
			$viewid = $customView->getViewId($this->moduleName);
			$customview_html = $customView->getCustomViewCombo($viewid);
			$viewinfo = $customView->getCustomViewByCvid($viewid);

			$userid = coreBOS_Session::get('_authenticated_user_id');
			$current_user = CRMEntity::getInstance('Users');
			$current_user = $current_user->retrieveCurrentUserInfoFromFile($userid);

			$queryGenerator = new QueryGenerator($this->moduleName, $current_user);
			if ($viewid != '0') {
				$queryGenerator->initForCustomViewById($viewid);
			} else {
				$queryGenerator->initForDefaultCustomView();
			}
			$selectClause = sprintf('SELECT %s', implode(',', $fieldnames).',vtiger_project.projectid');
			$fromClause = $queryGenerator->getFromClause();
			$whereClause = $queryGenerator->getWhereClause();
			$orderClause = '';
			$groupClause = '';
			if ($paging) {
				$config = crmtogo_WS_Controller::getUserConfigSettings();
				$limitClause = 'LIMIT 0,'.$config['NavigationLimit'];
			}

			if (!empty($this->criterias)) {
				$_sortCriteria = $this->criterias['_sort'];
				if (!empty($_sortCriteria)) {
					$orderClause = $_sortCriteria;
				}
			}

			$query = sprintf('%s %s %s %s %s %s;', $selectClause, $fromClause, $whereClause, $orderClause, $groupClause, $limitClause);
			$db = PearDatabase::getInstance();
			$result = $db->pquery($query, array());
			$noofrows = $db->num_rows($result);
			$lstresult = array();
			$entityId = vtws_getEntityId($this->moduleName)."x";
			for ($i=0; $i<$noofrows; $i++) {
				$lstresult[$i]['firstname'] = $db->query_result($result, $i, 'projectname');
				$lstresult[$i]['id']= $entityId.$db->query_result($result, $i, 'projectid');
			}
			return $lstresult;
		} else {
			$selectClause = sprintf('SELECT %s', implode(',', $fieldnames));
			$fromClause = sprintf('FROM %s', $this->moduleName);
			$whereClause = '';
			$orderClause = '';
			$groupClause = '';
			if ($paging) {
				$config = crmtogo_WS_Controller::getUserConfigSettings();
				$limitClause = 'LIMIT 0,'.$config ['NavigationLimit'];
			}

			if (!empty($this->criterias)) {
				$_sortCriteria = $this->criterias['_sort'];
				if (!empty($_sortCriteria)) {
					$orderClause = $_sortCriteria;
				}
			}

			$query = sprintf('%s %s %s %s %s %s;', $selectClause, $fromClause, $whereClause, $orderClause, $groupClause, $limitClause);
			return vtws_query($query, $this->getUser());
		}
	}
}