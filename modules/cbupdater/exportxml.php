<?php
/*************************************************************************************************
 * Copyright 2014 JPL TSolucio, S.L. -- This file is a part of TSOLUCIO coreBOS Customizations.
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
include_once 'vtlib/Vtiger/Zip.php';

global $adb;

if (isOnDemandActive()) {
	require_once 'Smarty_setup.php';
	$smarty = new vtigerCRM_Smarty();
	$smarty->assign('APP', $app_strings);
	$smarty->assign('OPERATION_MESSAGE', getTranslatedString('LBL_PERMISSION'));
	$smarty->display('modules/Vtiger/OperationNotPermitted.tpl');
	die();
}

$ids = vtlib_purify($_REQUEST['idstring']);

if (!empty($ids)) {
	// Export as Zip
	if (empty($todir)) {
		$todir = 'cache';
	}
	if (empty($xmlfilename)) {
		$xmlfilename = 'coreBOSUpdates.xml';
	} else {
		$xmlfilename = basename($xmlfilename);
	}
	if (empty($xmlfilename) || $xmlfilename=='.' || $xmlfilename=='..') {
		$xmlfilename = 'coreBOSUpdates.xml';
	}
	$xmlcfn = 'cache/'.$xmlfilename;
	$zipfilename = "$todir/cbupdates-" . date('YmdHis') . '.zip';
	$zip = new Vtiger_Zip($zipfilename);

	$sql = 'select * from vtiger_cbupdater
			inner join vtiger_crmentity on crmid=cbupdaterid
			where deleted=0 ';
	if ($ids!='all') {
		$ids = str_replace(';', ',', $ids);
		$ids = trim($ids, ',');
		$sql .= $adb->sql_escape_string(" and cbupdaterid in ($ids)");
	}
	$rs = $adb->query($sql);
	if ($rs && $adb->num_rows($rs)>0) {
		$w=new XMLWriter();
		$w->openMemory();
		$w->setIndent(true);
		$w->startDocument('1.0', 'UTF-8');
		$w->startElement('updatesChangeLog');
		while ($upd = $adb->fetch_array($rs)) {
			$w->startElement('changeSet');
			if (!empty($upd['author'])) {
				$w->startElement('author');
				$w->text($upd['author']);
				$w->endElement();
			}
			if (!empty($upd['description'])) {
				$w->startElement('description');
				$w->text($upd['description']);
				$w->endElement();
			}
				$w->startElement('filename');
				$w->text($upd['pathfilename']);
				$w->endElement();
				$w->startElement('classname');
				$w->text($upd['classname']);
				$w->endElement();
				$w->startElement('systemupdate');
				$w->text($upd['systemupdate'] == '1' ? 'true' : 'false');
				$w->endElement();
				$w->startElement('continuous');
				$w->text($upd['execstate'] == 'Continuous' ? 'true' : 'false');
				$w->endElement();
			$w->endElement();
			$bname = basename($upd['pathfilename']);
			$zip->addFile($upd['pathfilename'], $bname);
		}
		$w->endElement();
		$fd = fopen($xmlcfn, 'w');
		$cbxml = $w->outputMemory(true);
		fwrite($fd, $cbxml);
		fclose($fd);
		$zip->addFile($xmlcfn, $xmlfilename);
		$zip->save();
		$zip->forceDownload($zipfilename);
		unlink($zipfilename);
	} else {
		echo getTranslatedString('LBL_RECORD_NOT_FOUND');
	}
} else {
	echo getTranslatedString('LBL_RECORD_NOT_FOUND');
}
?>
