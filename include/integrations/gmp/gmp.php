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
 *************************************************************************************************
 *  Module    : GMP Integration
 *  Version   : 1.0
 *  Author    : JPL TSolucio, S. L.
 *************************************************************************************************/
include_once 'vtlib/Vtiger/Module.php';

class corebos_gmp {
	// Configuration Properties
	private $gid = '123';
	private $gversion = '1';

	// Configuration Keys
	const KEY_ISACTIVE = 'gmp_isactive';
	const KEY_GID = 'gmp_id';
	const KEY_GVERSION = 'gmp_version';

	public function __construct() {
		$this->initGlobalScope();
	}

	public function initGlobalScope() {
		$this->gid = coreBOS_Settings::getSetting(self::KEY_GID, '');
		$this->gversion = coreBOS_Settings::getSetting(self::KEY_GVERSION, '');
	}

	public function saveSettings($isactive, $gid, $gversion) {
		if ($isactive=='1') {
			$this->activateGMP();
			$ad = true;
		} else {
			$ad = $this->deactivateGMP();
		}
		if ($ad) {
			coreBOS_Settings::setSetting(self::KEY_ISACTIVE, $isactive);
			coreBOS_Settings::setSetting(self::KEY_GID, $gid);
			coreBOS_Settings::setSetting(self::KEY_GVERSION, $gversion);
		}
		return $ad;
	}

	public function getSettings() {
		return array(
			'isActive' => coreBOS_Settings::getSetting(self::KEY_ISACTIVE, ''),
			'gid' => coreBOS_Settings::getSetting(self::KEY_GID, ''),
			'gversion' => coreBOS_Settings::getSetting(self::KEY_GVERSION, ''),
		);
	}

	public function isActive() {
		$isactive = coreBOS_Settings::getSetting(self::KEY_ISACTIVE, '0');
		return ($isactive=='1');
	}

	public function activateGMP() {
		global $adb;
		$mod_acc = Vtiger_Module::getInstance('Accounts');
		$block_acc = Vtiger_Block::getInstance('LBL_ACCOUNT_INFORMATION', $mod_acc);
		$f = Vtiger_Field::getInstance('ga_clientid', $mod_acc);
		if ($f) {
			$adb->pquery('update vtiger_field set presence=2 where fieldid=?', array($f->id));
		} else {
			$field = new Vtiger_Field();
			$field->name = 'ga_clientid';
			$field->label= 'Google Analytics ClientID';
			$field->columntype = 'VARCHAR(70)';
			$field->uitype = 1;
			$field->displaytype = 1;
			$field->typeofdata = 'V~O';
			$block_acc->addField($field);
		}
		require_once 'modules/com_vtiger_workflow/VTTaskManager.inc';
		$defaultModules = array('include' => array(), 'exclude' => array());
		$taskType = array(
			'name'=>'CBGMPTask',
			'label'=>'Send to Google Measurement Protocol',
			'classname'=>'CBGMPTask',
			// 'classpath'=>'modules/cbgmp/workflow/CBGMPTask.inc',
			// 'templatepath'=>'modules/cbgmp/CBGMPTask.tpl',
			'classpath'=>'modules/com_vtiger_workflow/tasks/CBGMPTask.php',
			'templatepath'=>'com_vtiger_workflow/taskforms/CBGMPTask.tpl',
			'modules'=>$defaultModules,
			'sourcemodule'=>''
		);
		VTTaskType::registerTaskType($taskType);
	}

	public function deactivateGMP() {
		global $adb;
		$mod_acc = Vtiger_Module::getInstance('Accounts');
		$f = Vtiger_Field::getInstance('ga_clientid', $mod_acc);
		if ($f) {
			$adb->pquery('update vtiger_field set presence=1 where fieldid=?', array($f->id));
		}
		require_once 'modules/com_vtiger_workflow/VTTaskManager.inc';
		$result = $adb->pquery("SELECT * FROM `com_vtiger_workflowtasks` WHERE `task` like '%CBGMPTask%'", array());
		if ($result && $adb->num_rows($result)>0) {
			return false;
		} else {
			$adb->pquery(
				"DELETE FROM com_vtiger_workflow_tasktypes WHERE tasktypename='CBGMPTask' and label='Send to Google Measurement Protocol' and classname='CBGMPTask'",
				array()
			);
		}
		return true;
	}
}
?>