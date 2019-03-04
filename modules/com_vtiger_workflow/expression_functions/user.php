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

function __getCurrentUserID() {
	$user = VTWorkflowUtils::previousUser();
	if ($user) {
		return vtws_getEntityId('Users').'x'.$user->id;
	} else {
		global $current_user;
		return vtws_getEntityId('Users').'x'.$current_user->id;
	}
}

function __getCurrentUserName($arr) {
	global $current_user;
	$user = VTWorkflowUtils::previousUser();
	$userid = ($user ? $user->id : $current_user->id);
	if (isset($arr[0]) && strtolower($arr[0])=='full') {
		return trim(getUserFullName($userid));
	} else {
		return trim(getUserName($userid));
	}
}

function __getCurrentUserField($arr) {
	global $current_user;
	VTWorkflowUtils::previousUser();
	if (isset($arr[0]) && isset($current_user->column_fields[$arr[0]])) {
		return $current_user->column_fields[$arr[0]];
	} else {
		return '';
	}
}
?>