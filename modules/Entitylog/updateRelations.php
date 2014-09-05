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
 *  Module       : EntittyLog
 *  Version      : 5.4.0
 *  Author       : OpenCubed
 *************************************************************************************************/
require_once('include/database/PearDatabase.php');
@include_once('user_privileges/default_module_view.php');

global $adb, $singlepane_view, $currentModule;
$idlist            = vtlib_purify($_REQUEST['idlist']);
$destinationModule = vtlib_purify($_REQUEST['destination_module']);
$parenttab         = getParentTab();

$forCRMRecord = vtlib_purify($_REQUEST['parentid']);
$mode = $_REQUEST['mode'];

if($singlepane_view == 'true')
	$action = "DetailView";
else
	$action = "CallRelatedList";

$focus = CRMEntity::getInstance($currentModule);

if($mode == 'delete') {
	// Split the string of ids
	$ids = explode (";",$idlist);
	if(!empty($ids)) {
		$focus->delete_related_module($currentModule, $forCRMRecord, $destinationModule, $ids);
	}
} else {
	if(!empty($_REQUEST['idlist'])) {
		// Split the string of ids
		$ids = explode (";",trim($idlist,";"));
	} else if(!empty($_REQUEST['entityid'])){
		$ids = $_REQUEST['entityid'];
	}
	if(!empty($ids)) {
		$focus->save_related_module($currentModule, $forCRMRecord, $destinationModule, $ids);
	}
}
header("Location: index.php?module=$currentModule&record=$forCRMRecord&action=$action&parenttab=$parenttab");
?>