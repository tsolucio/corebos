<?php
/*************************************************************************************************
 * Copyright 2021 JPL TSolucio, S.L. -- This file is a part of TSOLUCIO coreBOS Customizations.
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

include_once 'include/integrations/mautic/mautic.php';
include_once 'include/Webservices/Update.php';

use Mautic\MauticApi;

function mauticContactCreate($entity) {
	global $adb, $current_user, $log;

	if ($entity->data['mautic_id'] == '') {
		$bmapname = 'ContactsToMautic';
		$cbMapid = GlobalVariable::getVariable('BusinessMapping_'.$bmapname, cbMap::getMapIdByName($bmapname));
		if ($cbMapid) {
			$send2mautic = array();
			$cbMap = cbMap::getMapByID($cbMapid);
			$send2mautic = $cbMap->Mapping($entity->data, $send2mautic);
			$send2mautic['ipAddress'] = $_SERVER['REMOTE_ADDR'];
			$send2mautic['overwriteWithBlank'] = true;

			$mautic = new corebos_mautic();
			$auth = $mautic->authenticate();

			if ($auth) {
				$apiUrl     = $mautic->getSettings('baseUrl');
				$api        = new MauticApi();
				$contactApi = $api->newApi('contacts', $auth, $apiUrl);
				$response = $contactApi->create($send2mautic);
				$contact  = $response[$contactApi->itemName()];
				if (!empty($contact['id'])) {
					$core_fields = $contact['fields']['core'];
					$social_fields = $contact['fields']['social'];
					$fields = array_merge($core_fields, $social_fields);

					$mauticdata = array();
					foreach ($fields as $field) {
						$mauticdata[$field['alias']] = $field['value'];
					}
					$usrwsid = vtws_getEntityId('Users').'x'.$current_user->id;

					$bmapname = 'MauticTOContacts';
					$cbMapid = GlobalVariable::getVariable('BusinessMapping_'.$bmapname, cbMap::getMapIdByName($bmapname));
					if ($cbMapid) {
						$send2cb = array();
						$cbMap = cbMap::getMapByID($cbMapid);
						$send2cb = $cbMap->Mapping($mauticdata, $send2cb);
						$send2cb['mautic_id'] = $contact['id'];
						$send2cb['assigned_user_id'] = $usrwsid;
						$send2cb['id'] = $entity->data['id'];
						$send2cb['from_externalsource'] = 'mautic';

						$record = vtws_update($send2cb, $current_user);
						if ($record) {
							// Reset from_externalsource
							list($contact_tabid, $contact_crmid) = explode('x', $record['id']);
							$sql = 'UPDATE vtiger_contactdetails SET from_externalsource = ? where contactid = ?';
							$result = $adb->pquery($sql, array('', $contact_crmid));
						}
					}
				}
			}
		}
	}
}

function mauticContactUpdate($entity) {
	global $adb, $current_user, $log;

	$bmapname = 'ContactsToMautic';
	$cbMapid = GlobalVariable::getVariable('BusinessMapping_'.$bmapname, cbMap::getMapIdByName($bmapname));
	if ($cbMapid) {
		$send2mautic = array();
		$cbMap = cbMap::getMapByID($cbMapid);
		$send2mautic = $cbMap->Mapping($entity->data, $send2mautic);
		$send2mautic['ipAddress'] = $_SERVER['REMOTE_ADDR'];
		$send2mautic['overwriteWithBlank'] = true;

		$mautic = new corebos_mautic();
		$auth = $mautic->authenticate();

		if ($auth) {
			$apiUrl     = $mautic->getSettings('baseUrl');
			$api        = new MauticApi();
			$contactApi = $api->newApi('contacts', $auth, $apiUrl);
			$response = $contactApi->edit($entity->data['mautic_id'], $send2mautic);
		}
	}
}
?>