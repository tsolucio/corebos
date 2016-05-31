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
include_once dirname(__FILE__) . '/../api/ws/Controller.php';
include_once dirname(__FILE__) . '/../api/ws/Utils.php';

class Mobile_UI_Delete extends Mobile_WS_Controller{
	function process(Mobile_API_Request $request) {
		$response = new Mobile_API_Response();
		$current_language = $this->sessionGet('language') ;
		include_once dirname(__FILE__) . '/../language/'.$current_language.'.lang.php';
		$module = $request->get('module');
		$record = $request->get('record');
		$viewer = new Mobile_UI_Viewer();
		$viewer->assign('LANGUAGE', $current_language);
		$viewer->assign('MOD', $mod_strings);
		$viewer->assign('_MODULE', $module);
        $viewer->assign('id', $record);
		$response = $viewer->process('generic/deleteConfirmation.tpl');
		return $response;
	}
}
?>