<?php
/*************************************************************************************************
 * Copyright 2019 JPL TSolucio, S.L. -- This file is a part of TSOLUCIO coreBOS customizations.
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
 *************************************************************************************************/
require_once 'Smarty_setup.php';

class DeveloperBlock {
	// Implement widget functionality
	protected $widgetName = 'NameYourWidget';
	private $myModule = '';
	protected $context = false;

	public function title() {
		global $currentModule;
		return getTranslatedString($this->widgetName, $currentModule);
	}

	public function name() {
		return $this->widgetName;
	}

	public function uikey() {
		return $this->widgetName.'_DeveloperBlock';
	}

	public function setWidgetName($widgetName) {
		$this->widgetName = $widgetName;
	}

	public function getModuleToRender() {
		global $currentModule;
		if (empty($this->myModule)) {
			$this->myModule = $currentModule;
		}
		return $this->myModule;
	}

	public function setModuleToRender($module) {
		$this->myModule = $module;
	}

	// Helper method to setup Smarty
	public function getViewer() {
		global $theme, $app_strings, $current_language;
		$smarty = new vtigerCRM_Smarty();
		$smarty->assign('APP', $app_strings);
		$smarty->assign('MOD', return_module_language($current_language, $this->getModuleToRender()));
		$smarty->assign('THEME', $theme);
		$smarty->assign('IMAGE_PATH', "themes/$theme/images/");
		$smarty->assign('UIKEY', $this->uikey());
		$smarty->assign('WIDGET_TITLE', $this->title());
		$smarty->assign('WIDGET_NAME', $this->name());
		return $smarty;
	}

	// This one is called to get the contents to show on screen
	public function process($context = false) {
		global $adb;
		$smarty = $this->getViewer();
		$this->context = $context;
		$sourceRecordId =  $this->getFromContext('ID', true);
		if (!empty($sourceRecordId)) {
			return 'Hello World!';
		}
		return getTranslatedString('LBL_PERMISSION');
	}

	// Helper method
	public function getFromContext($key, $purify = true) {
		if ($this->context) {
			$value = isset($this->context[$key]) ? $this->context[$key] : '';
			if ($purify && !empty($value)) {
				$value = vtlib_purify($value);
			}
			return $value;
		}
		return false;
	}
}
