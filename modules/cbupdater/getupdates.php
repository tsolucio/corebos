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

//error_reporting(E_ALL); ini_set('display_errors', 'on');
global $adb, $log, $mod_strings, $app_strings, $currentModule, $current_user, $theme;
include_once 'modules/cbupdater/cbupdater.php';
include_once 'modules/cbupdater/cbupdaterHelper.php';

$error = false;
$errmsg = '';
$cbupdatesfound = array();
$cbupdate_files = array();
if (!empty($_REQUEST['update_file'])) {
	$cbupdate_files[] = vtlib_purify($_REQUEST['update_file']);
} else {
	$cbupdate_files[] = 'modules/cbupdater/cbupdater.xml';
	foreach (glob('modules/cbupdater/cbupdates/*.{xml}', GLOB_BRACE) as $tcode) {
		$cbupdate_files[] = $tcode;
	}
}

if (count($cbupdate_files)>0) {
	libxml_use_internal_errors(true);
	foreach ($cbupdate_files as $cbupdate_file) {
		$cbupdate_file = realpath($cbupdate_file);
		if (!isInsideApplication($cbupdate_file)) {
			continue; // if the update file is not inside the application tree we do not load it
		}
		$cbupdates= new DOMDocument();
		if ($cbupdates->load($cbupdate_file)) {
			if ($cbupdates->schemaValidate('modules/cbupdater/cbupdater.xsd')) {
				$root = $cbupdates->documentElement;
				$execorder = cbupdater::getMaxExecutionOrder()+1;
				foreach ($root->childNodes as $node) {
					if (get_class($node)=='DOMElement' && $node->nodeName=='changeSet') {
						$elems = $node->getElementsByTagName('*');
						$cbupd = array();
						foreach ($elems as $elem) {
							$cbupd[$elem->nodeName] = $elem->nodeValue;
						}
						if (!cbupdater::exists($cbupd)) {
							if (file_exists($cbupd['filename'])) {
								$focus = new cbupdater();
								$focus->mode = ''; // create
								$_REQUEST['assigntype'] = 'U';
								$focus->column_fields['assigned_user_id'] = $current_user->id;
								$focus->column_fields['author'] = (empty($cbupd['author']) ? '' : $cbupd['author']);
								$focus->column_fields['description'] = (empty($cbupd['description']) ? '' : $cbupd['description']);
								$focus->column_fields['filename'] = basename($cbupd['filename'], '.php');
								$focus->column_fields['classname'] = $cbupd['classname'];
								$focus->column_fields['execstate']=(empty($cbupd['continuous']) ? 'Pending' : ($cbupd['continuous']=='true' ? 'Continuous' : 'Pending'));
								$focus->column_fields['systemupdate'] = (empty($cbupd['systemupdate']) ? '0' : ($cbupd['systemupdate']=='true' ? '1' : '0'));
								$focus->column_fields['blocked'] = (empty($cbupd['blocked']) ? '0' : ($cbupd['blocked']=='true' ? '1' : '0'));
								$focus->column_fields['perspective'] = (empty($cbupd['perspective']) ? '0' : ($cbupd['perspective']=='true' ? '1' : '0'));
								//$focus->column_fields['execdate'] = '';
								$focus->column_fields['execorder'] = $execorder++;
								$focus->save('cbupdater');
								$cbupd['cbupdaterid'] = $focus->id;
								$adb->pquery('update vtiger_cbupdater set pathfilename=? where cbupdaterid=?', array($cbupd['filename'],$focus->id));
								$cbupdatesfound[] = $cbupd;
							} else {
								$error = true;
								$errmsg = getTranslatedString('err_invalidchangeset', $currentModule).'<br>';
								$errmsg .= print_r($cbupd, true);
							}
						}
					}
				}
			} else {
				$error = true;
				$errmsg = getTranslatedString('err_invalidupdatefile', $currentModule).'<br>';
				foreach (libxml_get_errors() as $err) {
					$errmsg .= display_xml_error($err);
				}
				libxml_clear_errors();
			}
		} else {
			$error = true;
			$errmsg = getTranslatedString('err_invalidupdatefile', $currentModule).'<br>';
			foreach (libxml_get_errors() as $err) {
				$errmsg .= display_xml_error($err);
			}
			libxml_clear_errors();
		}
	} //foreach
} else {
	$error = true;
	$errmsg = getTranslatedString('err_noupdatefile', $currentModule);
}

$smarty = new vtigerCRM_Smarty();
$smarty->assign('APP', $app_strings);
$smarty->assign('MOD', $mod_strings);
$smarty->assign('MODULE', $currentModule);
$smarty->assign('CUSTOM_MODULE', true);
$smarty->assign('SINGLE_MOD', getTranslatedString('SINGLE_'.$currentModule));
$smarty->assign('IMAGE_PATH', "themes/$theme/images/");
$smarty->assign('THEME', $theme);
$smarty->assign('ERROR', $error);
$smarty->assign('ERRORMSG', $errmsg);
$smarty->assign('CBUPDATES', $cbupdatesfound);
include 'modules/cbupdater/forcedButtons.php';
$smarty->assign('CHECK', $tool_buttons);
$smarty->display('modules/cbupdater/getupdates.tpl');
?>