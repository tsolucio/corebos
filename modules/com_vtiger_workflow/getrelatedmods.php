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
 *************************************************************************************************
 *  Author       : JPL TSolucio, S. L.
 *************************************************************************************************/
require_once 'include/Webservices/getRelatedModules.php';
global $current_user;
$listres = '';
if (isset($_REQUEST['currentmodule'])) {
	$module = vtlib_purify($_REQUEST['currentmodule']);
	$reltype = empty($_REQUEST['reltype']) ? 'N:N' : vtlib_purify($_REQUEST['reltype']);
	$relatedMods = getRelatedModulesInfomation($module, $current_user);
	foreach ($relatedMods as $modval) {
		if ($reltype=='*') {
			$isGoodRelType = ($modval['relationtype']=='N:N' || $modval['relationtype']=='1:N');
		} else {
			$isGoodRelType = $modval['relationtype']==$reltype;
		}
		if ($isGoodRelType && $modval['related_tabid']!=0 && $modval['related_module'] != 'Emails') {
			$listres = $listres .'<option value="'.$modval['related_module'].'">'.$modval['labeli18n'].'</option>';
		}
	}
}
echo $listres;
?>