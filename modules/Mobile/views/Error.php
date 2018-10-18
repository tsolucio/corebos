<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/

class crmtogo_UI_Error extends crmtogo_WS_Controller {
	protected $error;

	public function setError($e) {
		$this->error = $e;
	}

	public function process(crmtogo_API_Request $request) {
		$viewer = new crmtogo_UI_Viewer();
		$config = $this->getUserConfigSettings();
		$viewer->assign('COLOR_HEADER_FOOTER', $config['theme']);
		$viewer->assign('errorcode', $this->error['code']);
		$viewer->assign('errormsg', $this->error['message']);
		return $viewer->process('Error.tpl');
	}
}