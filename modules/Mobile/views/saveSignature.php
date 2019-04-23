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
include_once __DIR__ . '/../api/ws/saveSignature.php';

class UI_saveSignature extends WS_saveSignature {

	public function process(crmtogo_API_Request $request) {
		$wsResponse = parent::process($request);
		$response = false;
		if ($wsResponse->hasError()) {
			$response = $wsResponse;
		} else {
			$wsResponseResult = $wsResponse->getResult();
			$viewer = new crmtogo_UI_Viewer();
			$viewer->assign('SIGNPATH', $wsResponseResult['signpath']);
			$response = $viewer->process('Signature.tpl');
			$response->setResult(json_encode(array('html' => $response->getResult())));
		}
		return $response;
	}
}
?>