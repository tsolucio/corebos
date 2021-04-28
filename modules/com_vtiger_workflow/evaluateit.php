<?php
/*************************************************************************************************
* Copyright 2020 JPL TSolucio, S.L. -- This file is a part of TSOLUCIO coreBOS Customizations.
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
require_once 'Smarty_setup.php';
require_once 'modules/com_vtiger_workflow/VTEntityCache.inc';
require_once 'modules/com_vtiger_workflow/expression_engine/VTParser.inc';
require_once 'modules/com_vtiger_workflow/expression_engine/VTTokenizer.inc';
require_once 'modules/com_vtiger_workflow/expression_engine/VTExpressionEvaluater.inc';
error_reporting(0);
ini_set('display_errors', 0);
global $currentModule;

$exp = vtlib_purify($_REQUEST['exp']);
$exptype = vtlib_purify($_REQUEST['exptype']);
$crmid = vtlib_purify($_REQUEST['crmid']);
$crmmod = vtlib_purify($_REQUEST['crmmod']);
$msgtype = 'cb-alert-success';
switch ($exptype) {
	case 'rawtext':
		$msg = $exp;
		break;
	case 'fieldname':
	case 'expression':
		if (empty($crmid)) {
			$msgtype = 'cb-alert-error';
			$msg = getTranslatedString('ERR_NoCRMIDforEvaluate', 'com_vtiger_workflow');
		} else {
			$holdModule = $currentModule;
			$currentModule = $crmmod;
			$adminUser = Users::getActiveAdminUser();
			$entityId = vtws_getEntityId($crmmod).'x'.$crmid;
			$entity = new VTWorkflowEntity($adminUser, $entityId);
			try {
				$parser = new VTExpressionParser(new VTExpressionSpaceFilter(new VTExpressionTokenizer(rawurldecode($exp))));
				$expression = $parser->expression();
				$exprEvaluater = new VTFieldExpressionEvaluater($expression);
				$msg = $exprEvaluater->evaluate($entity);
				if (gettype($msg)=='boolean') {
					$msg = $msg ? 'bool(true)' : 'bool(false)';
				}
				if (empty($msg)) {
					$msg = 'empty: '.$msg;
				}
			} catch (Exception $e) {
				$msg = $e->getMessage();
				$msgtype = 'cb-alert-error';
			}
			$currentModule = $holdModule;
		}
		break;
	default:
		$msgtype = 'cb-alert-error';
		$msg = getTranslatedString('ERR_ExpTypeUndefined', 'com_vtiger_workflow');
		break;
}
$smarty = new vtigerCRM_Smarty();
$smarty->assign('ERROR_MESSAGE_CLASS', $msgtype);
if (is_array($msg)) {
	$smarty->assign('ERROR_MESSAGE', var_export($msg, true));
} else {
	$smarty->assign('ERROR_MESSAGE', $msg);
}
$smarty->display('applicationmessage.tpl');
?>
