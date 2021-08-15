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
	const KEY_DENOR_MODULELIST = 'denormodule_list';
	const DENORM_OPERATION = 'denorm_mod';
	const UNDO_DENORM_OPERATION = 'undo_denorm';

	public function saveSettings($denormodulelist) {
		coreBOS_Settings::setSetting(self::KEY_DENOR_MODULELIST, $denormodulelist);
	}

	public function getSettings() {
		return array(
			'denormodule_list' => coreBOS_Settings::getSetting(self::KEY_DENOR_MODULELIST, ''),
		);
	}

	public function denormGetAllModules($operation = '') {
		global $adb;
		$query = 'SELECT modulename FROM vtiger_entityname';
		$condition = array();
		if ($operation == self::DENORM_OPERATION) {
			$query .= ' WHERE isdenormalized = ?';
			$condition[] = '0';
		}
		if ($operation == self::UNDO_DENORM_OPERATION) {
			$query .= ' WHERE isdenormalized = ?';
			$condition[] = '1';
		}
		$result=$adb->pquery($query, $condition);
		$module_arr = array();
		while ($modinfo = $adb->fetch_array($result)) {
			$module_arr[$modinfo['modulename']] = getTranslatedString($modinfo['modulename'], $modinfo['modulename']);
		}
		uasort($module_arr, function ($a, $b) {
			return (strtolower($a) < strtolower($b)) ? -1 : 1;
		});
		return $module_arr;
	}

	public function getModulestoDernormalize($selectedModuleList, $operation) {
		$modulestoprocess = $this->denormGetAllModules($operation);
		$totalSelectedMods = count($selectedModuleList);
		for ($xz=0; $xz <$totalSelectedMods; $xz++) {
			if (!isset($modulestoprocess[$selectedModuleList[$xz]])) {
				unset($selectedModuleList[$xz]);
			}
		}
		return array_values($selectedModuleList);
	}

	public function dernormalizeModules($selectedModuleList) {
		$smarty = new vtigerCRM_Smarty();
		$msg = '';
		$modules = $this->getModulestoDernormalize($selectedModuleList, 'denorm_mod');
		for ($yz=0; $yz < count($modules); $yz++) {
			try {
				$msg .= $this->denormalizeProcess($modules[$yz]);
			} catch (Exception $e) {
				$msg .= $e->getMessage();
			}
		}
		$smarty->assign('DENORM_RESPONSE', $msg);
		$smarty->display('modules/Utilities/denormalizefeedback.tpl');
	}

	public function denormalizeProcess($module) {
		global $adb, $dbconfig;
		$Vtiger_Utils_Log = true;
		include_once 'vtlib/Vtiger/Module.php';
		$msg = '';
		$query='SELECT tablename,entityidfield FROM vtiger_entityname WHERE modulename=?';
		$result=$adb->pquery($query, array($module));
		$tablename=$adb->query_result($result, 0, 'tablename');
		$entityidname=$adb->query_result($result, 0, 'entityidfield');
		$join=$tablename.'.'.$entityidname;
		$fkey = $adb->pquery(
			"SELECT constraint_name
			FROM information_schema.key_column_usage
			WHERE table_name=? and referenced_table_name='vtiger_crmentity' and constraint_schema=?",
			array($tablename, $dbconfig['db_name'])
		);
		if ($fkey && $adb->num_rows($fkey)>0) {
			$fcst = $adb->query_result($fkey, 0, 'constraint_name');
			$adb->query('ALTER TABLE '.$tablename.' DROP FOREIGN KEY '.$fcst);
			$msg .= 'ALTER TABLE '.$tablename.' DROP FOREIGN KEY '.$fcst;
			$msg .= '<br>Foreign Key constraint deleted.<br>';
		}
		// the constraints on seactivityrel and seattachmentsrel must be eliminate always as any record can be related to them
		// seactivityrel
		$fkey = $adb->pquery(
			"SELECT constraint_name
			FROM information_schema.key_column_usage
			WHERE table_name='vtiger_seactivityrel' and referenced_table_name='vtiger_crmentity' and constraint_schema=?",
			array($dbconfig['db_name'])
		);
		if ($fkey && $adb->num_rows($fkey)>0) {
			$fcst = $adb->query_result($fkey, 0, 'constraint_name');
			$adb->query('ALTER TABLE vtiger_seactivityrel DROP FOREIGN KEY '.$fcst);
			$msg .= 'ALTER TABLE vtiger_seactivityrel DROP FOREIGN KEY '.$fcst;
			$msg .= '<br>Foreign Key constraint on seactivityrel deleted.<br>';
		}
		// seattachmentsrel
		$fkey = $adb->pquery(
			"SELECT constraint_name
			FROM information_schema.key_column_usage
			WHERE table_name='vtiger_seattachmentsrel' and referenced_table_name='vtiger_crmentity' and constraint_schema=?",
			array($dbconfig['db_name'])
		);
		if ($fkey && $adb->num_rows($fkey)>0) {
			$fcst = $adb->query_result($fkey, 0, 'constraint_name');
			$adb->query('ALTER TABLE vtiger_seattachmentsrel DROP FOREIGN KEY '.$fcst);
			$msg .= 'ALTER TABLE vtiger_seattachmentsrel DROP FOREIGN KEY '.$fcst;
			$msg .= '<br>Foreign Key constraint on seattachmentsrel deleted.<br>';
		}
		$cncrm = $adb->getColumnNames($tablename);
		$descfield = 'ADD `description` text NULL DEFAULT NULL,';
		if (in_array('description', $cncrm)) {
			$descfield = '';
		}
		$query2= "ALTER TABLE $tablename
			ADD `crmid` INT( 19 ) NOT NULL DEFAULT 0 ,
			ADD `cbuuid` char(40) NULL DEFAULT NULL,
			ADD `smcreatorid` INT( 19 ) NOT NULL DEFAULT 0 ,
			ADD `smownerid` INT( 19 ) NOT NULL DEFAULT 0 ,
			ADD `modifiedby` INT( 19 ) NOT NULL DEFAULT 0 ,
			ADD `createdtime` datetime NULL DEFAULT NULL,
			ADD `modifiedtime` datetime NULL DEFAULT NULL,
			ADD `viewedtime` datetime NULL DEFAULT NULL,
			ADD `setype` varchar(100) NULL DEFAULT NULL,
			$descfield
			ADD `deleted` INT( 1 ) NOT NULL DEFAULT 0,
			ADD INDEX (`crmid`),
			ADD INDEX (`cbuuid`),
			ADD INDEX (`smcreatorid`),
			ADD INDEX (`modifiedby`),
			ADD INDEX (`deleted`),
			ADD INDEX (`smownerid`, `deleted`)";
		$result1=$adb->query($query2);
		if ($result1) {
			$msg .=  "Table $tablename altered with the new crmentity fields.<br>";
		} else {
			$msg .= '<span style="color:red;">Table '.$tablename.' COULD NOT be altered with the new crmentity fields.</span><br>';
			return $msg;
		}
		$updfields = 'update vtiger_field set tablename=? where tabid=? and tablename=?';
		$result2=$adb->pquery($updfields, array($tablename, getTabid($module), 'vtiger_crmentity'));
		if ($result2) {
			$msg .= 'Field meta-data updated.<br>';
		} else {
			$msg .= '<span style="color:red;">Field meta-data COULD NOT be updated.</span><br>';
			return $msg;
		}
		$query3="UPDATE $tablename inner join vtiger_crmentity on vtiger_crmentity.crmid=$join set
			$tablename.crmid = vtiger_crmentity.crmid,
			$tablename.cbuuid = vtiger_crmentity.cbuuid,
			$tablename.smcreatorid = vtiger_crmentity.smcreatorid,
			$tablename.smownerid = vtiger_crmentity.smownerid,
			$tablename.modifiedby = vtiger_crmentity.modifiedby,
			$tablename.createdtime = vtiger_crmentity.createdtime,
			$tablename.modifiedtime = vtiger_crmentity.modifiedtime,
			$tablename.viewedtime = vtiger_crmentity.viewedtime,
			$tablename.setype = vtiger_crmentity.setype,
			$tablename.description= vtiger_crmentity.description,
			$tablename.deleted = vtiger_crmentity.deleted";
		$result3=$adb->query($query3);
		$adb->query("UPDATE $tablename left join vtiger_crmentity on vtiger_crmentity.crmid=$join set $tablename.deleted=1 WHERE $tablename.createdtime is NUll");
		if ($result3) {
			$msg .= "Table $tablename filled with the crmentity data.<br>";
		} else {
			$msg .= '<span style="color:red;">Table '.$tablename.' COULD NOT be filled with the crmentity data.</span><br>';
			return $msg;
		}
		$sqlupdentitytable = 'UPDATE vtiger_entityname SET isdenormalized=?, denormtable=? WHERE vtiger_entityname.tabid=?';
		$result4=$adb->pquery($sqlupdentitytable, array('1',$tablename, getTabid($module)));
		if ($result4) {
			$msg .= 'Table entityname updated.<br>';
		} else {
			$msg .= '<span style="color:red;">Table entityname COULD NOT be updated.</span><br>';
			return $msg;
		}
		$result5=$adb->pquery('DELETE FROM vtiger_crmentity WHERE setype=?', array($module));
		if ($result5) {
			$msg .= 'CRMEntity rows deleted.<br>';
		} else {
			$msg .= '<span style="color:red;">CRMEntity rows COULD NOT be deleted.</span><br>';
		}
		return $msg;
	}

	public function undoDernormalizeModules($selectedModuleList) {
		$smarty = new vtigerCRM_Smarty();
		$msg = '';
		$modules = $this->getModulestoDernormalize($selectedModuleList, 'undo_denorm');
		for ($yz=0; $yz < count($modules); $yz++) {
			try {
				$msg .= $this->undoDenormalizeProcess($modules[$yz]);
			} catch (Exception $e) {
				$msg .= $e->getMessage();
			}
		}
		$smarty->assign('DENORM_RESPONSE', $msg);
		$smarty->display('modules/Utilities/denormalizefeedback.tpl');
	}

	public function undoDenormalizeProcess($module) {
		$Vtiger_Utils_Log = true;
		include_once 'vtlib/Vtiger/Module.php';
		global $adb;
		$msg = '';
		$result=$adb->pquery('SELECT denormtable FROM vtiger_entityname WHERE modulename=?', array($module));
		$denormtable=$adb->query_result($result, 0, 'denormtable');
		$undo_denormsql = "INSERT INTO vtiger_crmentity(
			crmid,
			cbuuid,
			smcreatorid,
			smownerid,
			modifiedby,
			createdtime,
			modifiedtime,
			viewedtime,
			setype,
			description,
			deleted
		)
		SELECT
			crmid,
			cbuuid,
			smcreatorid,
			smownerid,
			modifiedby,
			createdtime,
			modifiedtime,
			viewedtime,
			setype,
			description,
			deleted
		FROM $denormtable
		ON DUPLICATE KEY
		UPDATE
			`cbuuid` = vtiger_crmentity.cbuuid,
			`smcreatorid` = vtiger_crmentity.smcreatorid,
			`smownerid`= vtiger_crmentity.smownerid,
			`modifiedby`= vtiger_crmentity.modifiedby,
			`createdtime`= vtiger_crmentity.createdtime,
			`modifiedtime`= vtiger_crmentity.modifiedtime,
			`viewedtime`= vtiger_crmentity.viewedtime,
			`setype`= vtiger_crmentity.setype,
			`description`= vtiger_crmentity.description,
			`deleted` = vtiger_crmentity.deleted";
		$result = $adb->pquery($undo_denormsql, array());
		$updfields = 'update vtiger_field set tablename=?
			where tabid=? and columnname in ("cbuuid","smcreatorid","smownerid","modifiedby","createdtime","modifiedtime","viewedtime","setype","description","deleted")';
		$result1=$adb->pquery($updfields, array('vtiger_crmentity', getTabid($module)));
		$sqlupdentitytable = 'UPDATE vtiger_entityname SET isdenormalized=?, denormtable=? WHERE vtiger_entityname.tabid=?';
		$result2=$adb->pquery($sqlupdentitytable, array('0','vtiger_crmentity', getTabid($module)));
		if ($result && $result1 && $result2) {
			$msg .= "Process completed for Module $module<br>";
			$adb->query("ALTER TABLE $denormtable
				DROP COLUMN crmid,
				DROP COLUMN cbuuid,
				DROP COLUMN smcreatorid,
				DROP COLUMN smownerid,
				DROP COLUMN modifiedby,
				DROP COLUMN createdtime,
				DROP COLUMN modifiedtime,
				DROP COLUMN viewedtime,
				DROP COLUMN setype,
				DROP COLUMN description,
				DROP COLUMN deleted");
		}
		return $msg;
	}
}
?>