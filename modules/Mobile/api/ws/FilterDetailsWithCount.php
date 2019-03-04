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
include_once __DIR__ . '/FetchModuleFilters.php';
include_once 'modules/CustomView/CustomView.php';

class crmtogo_WS_FilterDetailsWithCount extends crmtogo_WS_FetchModuleFilters {

	public function process(crmtogo_API_Request $request) {
		$response = new crmtogo_API_Response();
		$filterid = $request->get('filterid');
		$current_user = $this->getActiveUser();
		$result = array();
		$result['filter'] = $this->getModuleFilterDetails($filterid);
		$response->setResult($result);
		return $response;
	}

	protected function getModuleFilterDetails($filterid) {
		$db = PearDatabase::getInstance();
		$result = $db->pquery('SELECT * FROM vtiger_customview WHERE cvid=?', array($filterid));
		if ($result && $db->num_rows($result)) {
			$resultrow = $db->fetch_array($result);
			$module = $resultrow['entitytype'];
			$view = new CustomView($module);
			$viewid = $resultrow['cvid'];
			$view->getCustomViewByCvid($viewid);
			$viewQuery = $view->getModifiedCvListQuery($viewid, getListQuery($module), $module);
			$countResult = $db->pquery(mkCountQuery($viewQuery), array());
			$count = 0;
			if ($countResult && $db->num_rows($countResult)) {
				$count = $db->query_result($countResult, 0, 'count');
			}
			$filter = $this->prepareFilterDetailUsingResultRow($resultrow);
			$filter['userName'] = getUserName($resultrow['userid']);
			$filter['count'] = $count;
			return $filter;
		}
	}
}