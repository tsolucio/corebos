<?php
/*************************************************************************************************
 * Copyright 2015 JPL TSolucio, S.L. -- This file is a part of TSOLUCIO coreBOS Customizations.
* Licensed under the vtiger CRM Public License Version 1.1 (the "License"); you may not use this
* file except in compliance with the License. You can redistribute it and/or modify it
* under the terms of the License. JPL TSolucio, S.L. reserves all rights not expressly
* granted by the License. coreBOS distributed by JPL TSolucio S.L. is distributed in
* the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied
* warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. Unless required by
* applicable law or agreed to in writing, software distributed under the License is
* distributed on an "AS IS" BASIS, WITHOUT ANY WARRANTIES OR CONDITIONS OF ANY KIND,
* either express or implied. See the License for the specific language governing
* permissions and limitations under the License. You may obtain a copy of the License
* at <http://corebos.org/documentation/doku.php?id=en:devel:vpl11>
*************************************************************************************************
*  Module       : cbupdater
*  Version      : 5.5.0
*  Author       : JPL TSolucio, S. L.
*************************************************************************************************/

if (count($argv)!=3) {
	echo "\nIncorrect number of parameters:\n";
	echo "  doworkcli.php [apply|undo] [all|comma_separated_list_of_ids]\n\n";
	die(1);
} else {
	$error = 0;
	$errmsg = '';

	require_once 'modules/cbupdater/cbupdater.php';
	require_once 'modules/cbupdater/cbupdaterWorker.php';
	$ids = strtolower(vtlib_purify($argv[2]));
	$whattodo = strtolower($argv[1]);

	if (!empty($ids) && ($whattodo=='undo' || $whattodo=='apply')) {
		global $adb, $log, $mod_strings, $app_strings, $currentModule, $current_user;
		$currentModule = 'cbupdater';
		$sql = 'select cbupdaterid,filename,pathfilename,classname, cbupd_no, description from vtiger_cbupdater
				inner join vtiger_crmentity on crmid=cbupdaterid
				where deleted=0 and ';
		if (strtolower($ids)=='all') {
			$sql .= "execstate in ('Pending','Continuous')";
		} else {
			$ids = str_replace(';', ',', $ids);
			$ids = trim($ids, ',');
			$sql .= $adb->sql_escape_string(" cbupdaterid in ($ids)");
		}
		// we do not process blocked changesets
		$cbacc=$adb->getColumnNames('vtiger_cbupdater');
		if (in_array('blocked', $cbacc)) {
			$sql .= " and blocked != '1' ";
		}
		$rs = $adb->query($sql);
		$totalops = $adb->num_rows($rs);
		$totalopsok = 0;
		while ($upd = $adb->fetch_array($rs)) {
			if (file_exists($upd['pathfilename'])) {
				include $upd['pathfilename'];
				if (class_exists($upd['classname'])) {
					$updobj = new $upd['classname'];
					if (method_exists($updobj, ($whattodo == 'undo' ? 'undoChange' : 'applyChange'))) {
						try {
							$msg = getTranslatedString('ChangeSet', $currentModule).' '.$upd['cbupd_no']."\n";
							$msg.= $upd['filename'].'::'.$upd['classname'];
							if (isset($upd['description'])) {
								$msg.= "\n".$upd['description'];
							}
							echo $msg;
							if ($whattodo == 'undo') {
								$updobj->undoChange();
							} else {
								$updobj->applyChange();
							}
							if (!$updobj->updError) {
								$totalopsok++;
							}
						} catch (Exception $e) {
							$error = 1;
							$errmsg = $e->getMessage();
						}
					} else {
						$error = 2;
						$errmsg = getTranslatedString('err_nomethod', $currentModule);
					}
				} else {
					$error = 3;
					$errmsg = getTranslatedString('err_invalidclass', $currentModule);
				}
			} else {
				$error = 4;
				$errmsg = getTranslatedString('err_noupdatefile', $currentModule);
			}
		}
	} else {
		$error = 5;
		$errmsg = getTranslatedString('err_noupdatesselected', $currentModule);
	}
}
echo "Total updates: $totalops\n";
echo "Total updates correct: $totalopsok\n";
if ($error) {
	echo "** ERROR: $error **\n";
	echo $errmsg."\n";
}
?>