<?php
<?php
/*+**********************************************************************************
- * The contents of this file are subject to the vtiger CRM Public License Version 1.0
- * ("License"); You may not use this file except in compliance with the License
- * The Original Code is:  vtiger CRM Open Source
- * The Initial Developer of the Original Code is vtiger.
- * Portions created by vtiger are Copyright (C) vtiger.
- * All Rights Reserved.
- ************************************************************************************/
global $adb;
require_once('Smarty_setup.php');

class Adocdetailng {
	// Get class name of the object that will implement the widget functionality
	static function getWidget($name) {
		return (new Adocdetailng_DetailViewBlock());
	}
}

class Adocdetailng_DetailViewBlock {
	// Implement widget functionality
	private $_name = 'Adocdetailng';
	protected $context = false;
	
	function title() {
		return getTranslatedString('Adocdetails', 'Adocdetails');
	}
	
	function name() {
		return $this->_name;
	}
	
	function uikey() {
		return "Adocdetailng_DetailViewBlock";
	}
	
	// Helper method to setup Smarty
	function getViewer() {
		global $theme, $app_strings, $current_language;
	
		$smarty = new vtigerCRM_Smarty();
		$smarty->assign('APP', $app_strings);
		$smarty->assign('MOD', return_module_language($current_language,'Contacts'));
		$smarty->assign('THEME', $theme);
		$smarty->assign('IMAGE_PATH', "themes/$theme/images/");
	
		$smarty->assign('UIKEY', $this->uikey());
		$smarty->assign('WIDGET_TITLE', $this->title());
		$smarty->assign('WIDGET_NAME', $this->name());
	
		return $smarty;
	}
	
	// This one is called to get the contents to show on screen
	function process($context = false) {
		global $adb;
		$smarty = $this->getViewer();
		$this->context = $context;
		$sourceRecordId =  $this->getFromContext('ID', true);
		
		// Special purchase order count and sum information
		// We get the info from database and send it to smarty
		
		
		return $smarty->fetch("modules/Adocmaster/ngTable.tpl");
	}
	
	// Helper method
	function getFromContext($key, $purify=false) {
		if ($this->context) {
			$value = $this->context[$key];
			if ($purify && !empty($value)) {
				$value = vtlib_purify($value);
			}
			return $value;
		}
		return false;
	}

}
?>
