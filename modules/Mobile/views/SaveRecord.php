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
include_once __DIR__ . '/../api/ws/SaveRecord.php';

class crmtogo_UI_ProcessRecordCreation  extends crmtogo_WS_SaveRecord {
	
	function process(crmtogo_API_Request $request) {
		$wsResponse = parent::process($request);
		$response = false;
		if($wsResponse->hasError()) {
			$response = $wsResponse;
		}
		$wsResponseResult = $wsResponse->getResult();
		$recordid = $wsResponseResult['record']['id'];
		header("Location:index.php?_operation=fetchRecord&record=$recordid");
		exit;
	}
}