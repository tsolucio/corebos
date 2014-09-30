<?php
/*************************************************************************************************
 * Copyright 2014 Opencubed -- This file is a part of TSOLUCIO coreBOS customizations.
 * You can copy, adapt and distribute the work under the "Attribution-NonCommercial-ShareAlike"
 * Vizsage Public License (the "License"). You may not use this file except in compliance with the
 * License. Roughly speaking, non-commercial users may share and modify this code, but must give credit
 * and share improvements. However, for proper details please read the full License, available at
 * http://vizsage.com/license/Vizsage-License-BY-NC-SA.html and the handy reference for understanding
 * the full license at http://vizsage.com/license/Vizsage-Deed-BY-NC-SA.html. Unless required by
 * applicable law or agreed to in writing, any software distributed under the License is distributed
 * on an  "AS IS" BASIS, WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and limitations under the
 * License terms of Creative Commons Attribution-NonCommercial-ShareAlike 3.0 (the License).
 *************************************************************************************************
 *  Module       : Adocdetail
 *  Version      : 5.4.0
 *  Author       : Opencubed
 *************************************************************************************************/
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
