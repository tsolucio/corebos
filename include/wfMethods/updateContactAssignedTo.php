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
*  Author       : JPL TSolucio, S. L.
*************************************************************************************************/

function updateContactAssignedTo($entity) {
	global $adb;

	list($acc,$acc_id) = explode('x', $entity->data['id']);  // separate webservice ID
	if (getSalesEntityType($acc_id)=='Accounts') {
		list($usr,$usr_id) = explode('x', $entity->data['assigned_user_id']);
		$query = 'update vtiger_crmentity set smownerid=? where crmid in (select contactid from vtiger_contactdetails where accountid=?)';
		$params = array($usr_id, $acc_id);
		$adb->pquery($query, $params);
	}
	if (getSalesEntityType($acc_id)=='Contacts') {
		list($void,$accountid) = explode('x', $entity->data['account_id']);
		if (!empty($accountid)) {
			$accassigrs = $adb->pquery('select smownerid from vtiger_crmentity where crmid=?', array($accountid));
			$usr_id = $adb->query_result($accassigrs, 0, 0);
			$query = 'update vtiger_crmentity set smownerid=? where crmid=?';
			$params = array($usr_id, $acc_id);
			$adb->pquery($query, $params);
		}
	}
}
?>