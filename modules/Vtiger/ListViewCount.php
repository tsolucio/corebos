<?php
/*************************************************************************************************
 * Copyright 2017 JPL TSolucio, S.L. -- This file is a part of TSOLUCIO coreBOS Customizations.
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
require_once 'modules/Users/Users.php';
require_once 'include/QueryGenerator/QueryGenerator.php';
require_once 'include/utils/utils.php';

$idlist = (isset($_REQUEST['idlist'])? vtlib_purify($_REQUEST['idlist']) : '');
$viewid = (isset($_REQUEST['viewname']) ? vtlib_purify($_REQUEST['viewname']) :'');
$module = (isset($_REQUEST['module']) ? vtlib_purify($_REQUEST['module']) :'');
$related_module = (isset($_REQUEST['related_module']) ? vtlib_purify($_REQUEST['related_module']) :'');

global $adb;
if (isset($_REQUEST['mode'])) {
	if (vtlib_purify($_REQUEST['mode'])=='relatedlist') {
		if ($related_module == 'Accounts') {
			$result = getCampaignAccountIds($idlist);
		}
		if ($related_module == 'Contacts') {
			$result = getCampaignContactIds($idlist);
		}
		if ($related_module == 'Leads') {
			$result = getCampaignLeadIds($idlist);
		}
	}
} else {
	$result = getSelectAllQuery($_REQUEST, $module);
}
$numRows = $adb->num_rows($result);
echo $numRows;
?>
