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
include_once 'include/Webservices/Delete.php';

class crmtogo_WS_DeleteRecords extends crmtogo_WS_Controller {

	public function process(crmtogo_API_Request $request) {
		$current_user = $this->getActiveUser();
		$records = $request->get('records');
		if (empty($records)) {
			$records = array($request->get('record'));
		} else {
			$records = json_decode($records, true);
		}

		$deleted = array();
		foreach ($records as $record) {
			try {
				vtws_delete($record, $current_user);
				$result = true;
			} catch (Exception $e) {
				$result = false;
			}
			$deleted[$record] = $result;
		}

		$response = new crmtogo_API_Response();
		$response->setResult(array('deleted' => $deleted));
		return $response;
	}
}
