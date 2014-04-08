<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/
require_once('Smarty_setup.php');

class MailManager_Viewer extends vtigerCRM_Smarty {
	
	function MailManager_Viewer() {
		parent::vtigerCRM_Smarty();
		
		global $app_strings, $mod_strings, $currentModule, $theme;
		
		$this->assign('CUSTOM_MODULE', true);

		$this->assign('APP', $app_strings);
		$this->assign('MOD', $mod_strings);
		$this->assign('MODULE', $currentModule);
		// TODO: Update Single Module Instance name here.
		$this->assign('SINGLE_MOD', 'SINGLE_'.$currentModule); 
		$this->assign('CATEGORY', getParentTab());
		$this->assign('IMAGE_PATH', "themes/$theme/images/");
		$this->assign('THEME', $theme);
		return parent::vtigerCRM_Smarty();
	}
}
?>