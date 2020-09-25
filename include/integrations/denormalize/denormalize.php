<?php
/*************************************************************************************************
 * Copyright 2020 JPL TSolucio, S.L. -- This file is a part of TSOLUCIO coreBOS customizations.
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
 *  Module    : Denormalize Integration
 *  Version   : 1.0
 *  Author    : JPL TSolucio, S. L.
 *************************************************************************************************/
class corebos_denormalize {
	private $isactive = 0;
	private $denormodulelist;
	const KEY_ISACTIVE = 'denormalize_isactive';
	const KEY_DENOR_MODULELIST = 'denormodule_list';

	public function __construct() {
		$this->initGlobalScope();
	}

	public function initGlobalScope() {
		$this->isactive = coreBOS_Settings::getSetting(self::KEY_ISACTIVE, 0);
		$this->denormodulelist = coreBOS_Settings::getSetting(self::KEY_DENOR_MODULELIST, '');
	}

	public function saveSettings(
		$isactive,
		$denormodulelist
	) {
		coreBOS_Settings::setSetting(self::KEY_ISACTIVE, $isactive);
		coreBOS_Settings::setSetting(self::KEY_DENOR_MODULELIST, $denormodulelist);
	}

	public function getSettings() {
		return array(
			'denormalize_isactive' => coreBOS_Settings::getSetting(self::KEY_ISACTIVE, ''),
			'denormodule_list' => coreBOS_Settings::getSetting(self::KEY_DENOR_MODULELIST, ''),
		);
	}

	public function isActive() {
		$isactive = coreBOS_Settings::getSetting(self::KEY_ISACTIVE, 0);
		return ($isactive==1);
	}

	public function denormGetAllModules($todenormalize = false) {
		global $adb;
		$query = 'SELECT modulename FROM vtiger_entityname';
		$condition = array();
		if ($todenormalize) {
			$query .= ' WHERE isdenormalized = ?';
			$condition[] = '0';
		}
		$result=$adb->pquery($query, $condition);
		$totalmodules = $adb->num_rows($result);
		$module_arr = array();
		if ($result && $totalmodules > 0) {
			for ($z=0; $z < $totalmodules; $z++) {
				$module_arr[] = $adb->query_result($result, $z, 'modulename');
			}
		}
		return $module_arr;
	}

	public function getModulestoDernormalize($selectedModuleList) {
		$undernormalizedModules = $this->denormGetAllModules(true);
		$totalSelectedMods = count($selectedModuleList);
		for ($xz=0; $xz <$totalSelectedMods; $xz++) {
			if (!in_array($selectedModuleList[$xz], $undernormalizedModules, true)) {
				unset($selectedModuleList[$xz]);
			}
		}
		return array_values($selectedModuleList);
	}

	public function dernormalizeModules($selectedModuleList) {
		$modules = $this->getModulestoDernormalize($selectedModuleList);
		for ($yz=0; $yz < count($modules); $yz++) {
			try {
				$res = $this->denormalizeProcess($modules[$yz]);
			} catch (Exception $e) {
				$resMsg = $e->getMessage();
			}
		}
		return;
	}

	public function denormalizeProcess($module) {
		$smarty = new vtigerCRM_Smarty();
		$Vtiger_Utils_Log = true;
		include_once 'vtlib/Vtiger/Module.php';
		global $adb, $log;
		$msg = '';
		$query='SELECT tablename,entityidfield FROM vtiger_entityname WHERE modulename=?';
		$result=$adb->pquery($query, array($module));
		$tablename=$adb->query_result($result, 0, 'tablename');
		$entityidname=$adb->query_result($result, 0, 'entityidfield');
		$join=$tablename.'.'.$entityidname;
		$query2= "ALTER TABLE $tablename
			ADD  `crmid` INT( 19 ) NOT NULL DEFAULT 0 ,
			ADD  `cbuuid` char(40) NULL DEFAULT NULL,
			ADD  `smcreatorid` INT( 19 ) NOT NULL DEFAULT 0 ,
			ADD  `smownerid` INT( 19 ) NOT NULL DEFAULT 0 ,
			ADD  `modifiedby` INT( 19 ) NOT NULL DEFAULT 0 ,
			ADD  `createdtime` datetime NULL DEFAULT NULL,
			ADD  `modifiedtime` datetime NULL DEFAULT NULL,
			ADD  `viewedtime` datetime NULL DEFAULT NULL,
			ADD  `setype` varchar(100) NULL DEFAULT NULL,
			ADD  `description` text NULL DEFAULT NULL,
			ADD  `deleted` INT( 1 ) NOT NULL DEFAULT 0,
			ADD INDEX (`crmid`),
			ADD INDEX (`cbuuid`),
			ADD INDEX (`smcreatorid`),
			ADD INDEX (`modifiedby`),
			ADD INDEX (`deleted`),
			ADD INDEX (`smownerid`, `deleted`)";
		$result1=$adb->query($query2);
		if ($result1) {
			$msg .=  "Table ".$tablename." altered with the new crmentity fields.<br>";
		}
		$updfields = 'update vtiger_field set tablename=? where tabid=? and tablename=?';
		$result2=$adb->pquery($updfields, array($tablename, getTabid($module), 'vtiger_crmentity'));
		if ($result2) {
			$msg .= "Field meta-data updated.<br>";
		}
		$query3="UPDATE $tablename inner join vtiger_crmentity on vtiger_crmentity.crmid=$join
			set
			$tablename.crmid = vtiger_crmentity.crmid ,
			$tablename.cbuuid = vtiger_crmentity.cbuuid ,
			$tablename.smcreatorid = vtiger_crmentity.smcreatorid ,
			$tablename.smownerid = vtiger_crmentity.smownerid ,
			$tablename.modifiedby = vtiger_crmentity.modifiedby ,
			$tablename.createdtime = vtiger_crmentity.createdtime ,
			$tablename.modifiedtime = vtiger_crmentity.modifiedtime ,
			$tablename.viewedtime = vtiger_crmentity.viewedtime ,
			$tablename.setype = vtiger_crmentity.setype ,
			$tablename.description= vtiger_crmentity.description,
			$tablename.deleted = vtiger_crmentity.deleted";
		$result3=$adb->query($query3);
		$que="UPDATE $tablename left join vtiger_crmentity on vtiger_crmentity.crmid=$join set $tablename.deleted=1 WHERE $tablename.createdtime is NUll";
		$res=$adb->query($que);
		if ($result3) {
			$msg .= "Table ".$tablename." filled with the crmentity data.<br>";
		}
		$sqlupdentitytable = 'UPDATE vtiger_entityname SET isdenormalized = ?, denormtable = ? WHERE vtiger_entityname.tabid = ?';
		$result4=$adb->pquery($sqlupdentitytable, array('1',$tablename, getTabid($module)));
		// $smarty->display('Smarty/templates/modules/Utilities/denormalizefeedback.tpl'); // sending fedback to user screen
		return true;
	}
}
?>