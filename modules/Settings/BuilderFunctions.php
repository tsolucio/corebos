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
require_once 'include/utils/utils.php';
require_once 'modules/Settings/ModuleBuilder/builder.php';
$methodName = vtlib_purify($_REQUEST['methodName']);
$mb = new ModuleBuilder();
switch ($methodName) {
	case 'Save':
		require_once 'modules/Settings/ModuleBuilder/SaveModuleBuilder.php';
		$step = vtlib_purify($_REQUEST['step']);
		$ret = SaveModuleBuilder($step);
		break;
	case 'checkForModule':
		$modulename = vtlib_purify($_REQUEST['modulename']);
		$ret = $mb->checkForModule($modulename);
		break;
	case 'loadModules':
		$page = vtlib_purify($_REQUEST['page']);
		$perPage = vtlib_purify($_REQUEST['perPage']);
		$ret = $mb->loadModules($page, $perPage);
		break;
	case 'loadBlocks':
		$ret = $mb->loadBlocks();
		break;
	case 'loadFields':
		$ret = $mb->loadFields();
		break;
	case 'autocomplete':
		$query = vtlib_purify($_REQUEST['query']);
		$method = vtlib_purify($_REQUEST['method']);
		if ($method == 'name') {
			$ret = $mb->autocompleteName($query);
		} elseif ($method == 'module') {
			$ret = $mb->autocompleteModule($query);
		}
		break;
	case 'loadValues':
		$step = vtlib_purify($_REQUEST['step']);
		$moduleid = vtlib_purify($_REQUEST['moduleid']);
		$ret = $mb->loadValues($step, $moduleid);
		break;
	case 'removeBlock':
		$blockid = vtlib_purify($_REQUEST['blockid']);
		$ret = $mb->removeBlock($blockid);
		break;
	case 'removeField':
		$fieldsid = vtlib_purify($_REQUEST['fieldsid']);
		$ret = $mb->removeField($fieldsid);
		break;
	case 'loadDefaultBlocks':
		$ret = $mb->loadDefaultBlocks();
		break;
	case 'removeCustomView':
		$viewid = vtlib_purify($_REQUEST['viewid']);
		$ret = $mb->removeCustomView($viewid);
		break;
	case 'removeRelatedLists':
		$listid = vtlib_purify($_REQUEST['listid']);
		$ret = $mb->removeRelatedLists($listid);
		break;
	case 'generateManifest':
		$ret = $mb->generateManifest();
		break;
	case 'loadTemplate':
		$modId = vtlib_purify($_REQUEST['modId']);
		$ret = $mb->loadTemplate($modId);
		break;
	case 'VerifyModule':
		$modulename = vtlib_purify($_REQUEST['modulename']);
		$ret = $mb->VerifyModule($modulename);
		break;
	case 'installModule':
		$modulename = vtlib_purify($_REQUEST['modulename']);
		$ret = $mb->installModule($modulename);
		break;
	case 'getCountFilter':
		$modulename = vtlib_purify($_REQUEST['modulename']);
		$ret = $mb->getCountFilter($modulename);
		break;
	default:
		$ret = array();
		break;
}
echo json_encode($ret);
?>