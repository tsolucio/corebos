<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/
include_once 'Smarty_setup.php';

class crmtogo_UI_Viewer {

	private $parameters = array();
	public function assign($key, $value) {
		$this->parameters[$key] = $value;
	}

	public function viewController() {
		$smarty = new vtigerCRM_Smarty();
		foreach ($this->parameters as $k => $v) {
			$smarty->assign($k, $v);
		}
		$smarty->assign('IS_SAFARI', Mobile::isSafari());
		$smarty->assign('SKIN', Mobile::config('Default.Skin'));
		return $smarty;
	}

	public function process($templateName) {
		$smarty = $this->viewController();
		$response = new crmtogo_API_Response();
		$response->setResult($smarty->fetch(vtlib_getModuleTemplate('Mobile', $templateName)));
		return $response;
	}
}
?>