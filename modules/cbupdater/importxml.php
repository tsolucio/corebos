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
require_once 'vtlib/Vtiger/Unzip.php';
require_once 'Smarty_setup.php';

global $adb;
$cspath = 'build/changeSets/imported';
if (!is_dir($cspath)) {
	mkdir($cspath);
}

if (isOnDemandActive()) {
	$smarty = new vtigerCRM_Smarty();
	$smarty->assign('APP', $app_strings);
	$smarty->assign('OPERATION_MESSAGE', getTranslatedString('LBL_PERMISSION'));
	$smarty->display('modules/Vtiger/OperationNotPermitted.tpl');
	die();
}

$zipfile = '';
if (count($_FILES)==1 && !empty($_FILES['zipfile'])
 && !empty($_FILES['zipfile']['tmp_name']) && !empty($_FILES['zipfile']['type'])
 && $_FILES['zipfile']['type']=='application/zip') {
	$zipfile = $_FILES['zipfile']['tmp_name'];
}

if (empty($zipfile)) {
	cbupd_getfile();
} else {
	cbupd_import($zipfile);
}

function cbupd_getfile() {
	global $mod_strings, $app_strings, $currentModule;
	include 'modules/cbupdater/cbupdButtons.php';
	$smarty = new vtigerCRM_Smarty();
	$smarty->assign('APP', $app_strings);
	$smarty->assign('MOD', $mod_strings);
	$smarty->assign('MODULE', $currentModule);
	$smarty->assign('SINGLE_MOD', 'SINGLE_'.$currentModule);
	$smarty->assign('CHECK', $tool_buttons);
	$smarty->display('modules/cbupdater/importxml.tpl');
}

function cbupd_import($zipfile) {
	global $cspath;
	$unzip = new Vtiger_Unzip($zipfile);
	$unzip->unzipAll($cspath);
	$filelist = $unzip->getList();
	$csxmlfound = false;
	echo getTranslatedString('Importing', 'cbupdater').' '.vtlib_purify($zipfile).'<br>';
	$processing = getTranslatedString('Processing', 'cbupdater').' ';
	foreach ($filelist as $filename => $fileinfo) {
		echo $processing.$filename.'<br>';
		$pinfo = pathinfo($filename);
		if ($pinfo['extension']=='xml') {
			echo "XML File found: $filename <br>";
			$cbupdates= new DOMDocument();
			if ($cbupdates->load($cspath.'/'.$filename)) {
				echo 'XML File loaded!<br>';
				if ($cbupdates->schemaValidate('modules/cbupdater/cbupdater.xsd')) {
					echo 'XML File validated!<br>';
					$csxmlfound = true;
					$w=new XMLWriter();
					$w->openMemory();
					$w->setIndent(true);
					$w->startDocument('1.0', 'UTF-8');
					$w->startElement('updatesChangeLog');
					$root = $cbupdates->documentElement;
					foreach ($root->childNodes as $node) {
						if (get_class($node)=='DOMElement' && $node->nodeName=='changeSet') {
							$elems = $node->getElementsByTagName('*');
							$upd = array();
							foreach ($elems as $elem) {
								if ($elem->nodeName=='filename') {
									$bname = basename($elem->nodeValue);
									$upd[$elem->nodeName] = $cspath.'/'.$bname;
								} else {
									$upd[$elem->nodeName] = $elem->nodeValue;
								}
							}
							echo $processing.getTranslatedString('ChangeSet', 'cbupdater').' '.$upd['classname'].'<br>';
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
							$w->text($upd['filename']);
							$w->endElement();
							$w->startElement('classname');
							$w->text($upd['classname']);
							$w->endElement();
							$w->startElement('systemupdate');
							$w->text($upd['systemupdate'] == '1' ? 'true' : 'false');
							$w->endElement();
							if (isset($upd['continuous'])) {
								$w->startElement('continuous');
								$w->text($upd['continuous']);
								$w->endElement();
							}
							$w->endElement();
						}
					}
					$w->endElement();
					$cbupdate_file = 'modules/cbupdater/cbupdates/'.date('YmdHis').$filename;
					$fd = fopen($cbupdate_file, 'w');
					$cbxml = $w->outputMemory(true);
					fwrite($fd, $cbxml);
					fclose($fd);
					@unlink($cspath.'/'.$filename);
				}
			}
		}
	}
	echo getTranslatedString('ImportDone', 'cbupdater').'<br>';
	if (!$csxmlfound) {
		echo getTranslatedString('ImportError', 'cbupdater').'<br>';
		echo getTranslatedString('CleanUp', 'cbupdater').'<br>';
		cbupd_cleanup($filelist);
	} else {
		include_once 'modules/cbupdater/getupdates.php';
	}
}

function cbupd_cleanup($filelist) {
	foreach ($filelist as $filename => $fileinfo) {
		echo getTranslatedString('Deleting', 'cbupdater').' '.$filename.'<br>';
		@unlink($filename);
	}
}
?>
