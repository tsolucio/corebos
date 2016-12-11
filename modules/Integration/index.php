<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/
class Integration_Request {
	protected $valuemap;
	function __construct($values) {
		$this->valuemap = $values;
	}
	function get($key, $defvalue='') {
		$value = $defvalue;
		if (isset($this->valuemap[$key])) {
			$value = $this->valuemap[$key];
		}
		if (!empty($value)) {
			$value = vtlib_purify($value);
		}
		return $value;
	}
}

class Integration_Viewer extends vtigerCRM_Smarty {

	function __construct() {
		parent::__construct();

		global $app_strings, $mod_strings, $currentModule, $theme;

		$this->assign('CUSTOM_MODULE', true);
		$this->assign('APP', $app_strings);
		$this->assign('MOD', $mod_strings);
		$this->assign('MODULE', $currentModule);
		$this->assign('SINGLE_MOD', 'SINGLE_'.$currentModule);
		$this->assign('CATEGORY', getParentTab($currentModule));
		$this->assign('IMAGE_PATH', "themes/$theme/images/");
		$this->assign('THEME', $theme);
	}
}

class Integration_Controller {
	function process(Integration_Request $request) {
		global $currentModule, $site_URL;
		$viewer = new Integration_Viewer();
		$gmailBookmarklet = sprintf("javascript:(%s)();",
			"function()%7Bvar%20doc=document;var%20bodyElement=document.body;doc.vtigerURL=%22$site_URL/%22;" .
			"var%20scriptElement=document.createElement(%22script%22);scriptElement.type=%22text/javascript%22;".
			"scriptElement.src=doc.vtigerURL+%22modules/Emails/GmailBookmarkletTrigger.js%22;bodyElement.appendChild(scriptElement);%7D");
		$viewer->assign('GMAIL_BOOKMARKLET', $gmailBookmarklet);
		$viewer->display(vtlib_getModuleTemplate($currentModule, 'index.tpl'));
	}
}

$controller = new Integration_Controller();
$controller->process(new Integration_Request($_REQUEST));
?>
