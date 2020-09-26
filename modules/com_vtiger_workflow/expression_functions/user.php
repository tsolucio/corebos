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
	global $current_user;
	return vtws_getEntityId('Users').'x'.$current_user->id;
}

function __getCurrentUserName($arr) {
	global $current_user;
	if (isset($arr[0]) && strtolower($arr[0])=='full') {
		return trim(getUserFullName($current_user->id));
	} else {
		return trim(getUserName($current_user->id));
	}
}

function __getCurrentUserField($arr) {
	global $current_user;
	switch (strtolower($arr[0])) {
		case 'parentrole':
			$rinfo = getRoleInformation($current_user->column_fields['roleid']);
			$roles = explode('::', $rinfo[$current_user->column_fields['roleid']][1]);
			end($roles);
			return prev($roles);
			break;
		case 'parentrolename':
			$rinfo = getRoleInformation($current_user->column_fields['roleid']);
			$roles = explode('::', $rinfo[$current_user->column_fields['roleid']][1]);
			end($roles);
			return getRoleName(prev($roles));
			break;
		case 'rolename':
			return getRoleName($current_user->column_fields['roleid']);
			break;
		default:
			if (isset($current_user->column_fields[$arr[0]])) {
				return $current_user->column_fields[$arr[0]];
			} else {
				return '';
			}
			break;
	}
}
?>