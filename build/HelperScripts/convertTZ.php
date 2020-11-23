<?php
/*************************************************************************************************
 * Copyright 2019 JPL TSolucio, S.L. -- This file is a part of TSOLUCIO coreBOS Customizations.
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
*************************************************************************************************/
$Vtiger_Utils_Log = true;
include_once 'vtlib/Vtiger/Module.php';

if (empty($argv[1]) || empty($argv[2])) {
	echo "\n";
	echo $argv[0]." sourceTimezone destinationTimezone crmentitytable\n";
	echo "\n";
	echo "Missing parameters!\n";
	echo 'For example, '.$argv[0]." UTC CET vtiger_account\n";
	die();
}
$sourceTimeZoneName = $argv[1];
$sourceTimeZone = new DateTimeZone($sourceTimeZoneName);
$targetTimeZoneName = $argv[2];
$targetTimeZone = new DateTimeZone($targetTimeZoneName);
$crmtable = (empty($argv[3]) ? 'vtiger_crmentity' : $argv[3]);
// crmentity
$crme = $adb->pquery('select crmid,createdtime,modifiedtime,viewedtime from '.$crmtable, array());
while ($rec = $adb->fetch_array($crme)) {
	$cTime = new DateTime($rec['createdtime'], $sourceTimeZone);
	$cTime->setTimeZone($targetTimeZone);
	$mTime = new DateTime($rec['modifiedtime'], $sourceTimeZone);
	$mTime->setTimeZone($targetTimeZone);
	$vTime = new DateTime($rec['viewedtime'], $sourceTimeZone);
	$vTime->setTimeZone($targetTimeZone);
	$adb->pquery(
		'update '.$crmtable.' set createdtime=?, modifiedtime=?, viewedtime=? where crmid=?',
		array($cTime->format('Y-m-d H:i:s'), $mTime->format('Y-m-d H:i:s'), $vTime->format('Y-m-d H:i:s'), $rec['crmid'])
	);
	$adb->pquery('update vtiger_crmobject set modifiedtime=? where crmid=?', array($mTime->format('Y-m-d H:i:s')));
	echo $adb->convert2Sql(
		'update '.$crmtable.' set createdtime=?, modifiedtime=?, viewedtime=? where crmid=?',
		array($cTime->format('Y-m-d H:i:s'), $mTime->format('Y-m-d H:i:s'), $vTime->format('Y-m-d H:i:s'), $rec['crmid'])
	)."\n";
}
// uitype 50
$flds = $adb->pquery('select tabid,columnname,tablename from vtiger_field where uitype=50 order by tabid,tablename', array());
$tabid = 0;
$tname = '';
$updfields = array();
while ($rec = $adb->fetch_array($flds)) {
	if ($tabid!=$rec['tabid'] || $tname!=$rec['tablename']) {
		if (count($updfields)>0) {
			// update
			$rs = $adb->pquery('select '.implode(',', $updfields).','.$mod->tab_name_index[$tname].' from '.$tname, array());
			$updsql = 'update '.$tname.' set '.implode('=?,', $updfields).'=? where '.$mod->tab_name_index[$tname].'=?';
			while ($updrec = $adb->fetch_array($rs)) {
				$params = array();
				foreach ($updfields as $field) {
					if (empty($updrec[$field])) {
						$params[] = '';
					} else {
						$fTime = new DateTime($updrec[$field], $sourceTimeZone);
						$fTime->setTimeZone($targetTimeZone);
						$params[] = $fTime->format('Y-m-d H:i:s');
					}
				}
				$params[] = $updrec[$mod->tab_name_index[$tname]];
				$adb->pquery($updsql, $params);
				echo $adb->convert2Sql($updsql, $params)."\n";
			}
		}
		$tabid = $rec['tabid'];
		$tname = $rec['tablename'];
		$updfields = array();
		$mod = CRMEntity::getInstance(getTabModuleName($tabid));
	};
	$updfields[] = $rec['columnname'];
}
// timecontrol
$flds = $adb->pquery('select timecontrolid,date_start,time_start,date_end,time_end from vtiger_timecontrol', array());
while ($rec = $adb->fetch_array($flds)) {
	if ($rec['date_start']!='' && $rec['time_start']!='') {
		$sTime = new DateTime($rec['date_start'].' '.$rec['time_start'], $sourceTimeZone);
		$sTime->setTimeZone($targetTimeZone);
		$dstart = $sTime->format('Y-m-d');
		$tstart = $sTime->format('H:i:s');
	} else {
		$dstart = $rec['date_start'];
		$tstart = $rec['time_start'];
	}
	if ($rec['date_end']!='' && $rec['time_end']!='') {
		$sTime = new DateTime($rec['date_end'].' '.$rec['time_end'], $sourceTimeZone);
		$sTime->setTimeZone($targetTimeZone);
		$dend = $sTime->format('Y-m-d');
		$tend = $sTime->format('H:i:s');
	} else {
		$dend = $rec['date_end'];
		$tend = $rec['time_end'];
	}
	$adb->pquery(
		'update vtiger_timecontrol set date_start=?, time_start=?, date_end=?, time_end=? where timecontrolid=?',
		array($dstart, $tstart, $dend, $tend, $rec['timecontrolid'])
	);
	echo $adb->convert2Sql(
		'update vtiger_timecontrol set date_start=?, time_start=?, date_end=?, time_end=? where timecontrolid=?',
		array($dstart, $tstart, $dend, $tend, $rec['timecontrolid'])
	)."\n";
}
echo "\n";
echo "********************************\n";
echo 'Now change the $default_timezone in your config.inc.php to '.$targetTimeZoneName."\n";
echo "********************************\n";
echo "\n";
?>