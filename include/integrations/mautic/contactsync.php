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
 *************************************************************************************************
 *  Module       : Mautic Integration
 *  Version      : 1.0
 *  Author       : JPL TSolucio, S. L.
 *************************************************************************************************/

include_once 'include/utils/utils.php';
include_once 'include/Webservices/Utils.php';
include_once 'include/Webservices/Create.php';
include_once 'include/Webservices/Update.php';
include_once 'include/integrations/mautic/mautic.php';

use Mautic\MauticApi;

function contactsync($input) {
	global $adb;
	$mautic = new corebos_mautic();
	if (!$mautic->isActive()) {
		return;
	}

	$current_user = Users::getActiveAdminUser();
	$usrwsid = vtws_getEntityId('Users').'x'.$current_user->id;

	$data = json_decode($input, true);

	$create = array_key_exists('mautic.lead_post_save_new', $data);
	$update = array_key_exists('mautic.lead_post_save_update', $data);
	$delete = array_key_exists('mautic.lead_post_delete', $data);

	if ($create) {
		$contact = $data['mautic.lead_post_save_new'][0]['contact'];
		$core_fields = $contact['fields']['core'];
		$social_fields = $contact['fields']['social'];

		if ($core_fields['corebos_id']['value'] == '') {
			$fields = array_merge($core_fields, $social_fields);

			$mauticdata = array();
			foreach ($fields as $field) {
				$mauticdata[$field['alias']] = $field['value'];
			}
			$send2cb = array();
			$bmapname = 'MauticToContacts';
			$cbMapid = GlobalVariable::getVariable('BusinessMapping_'.$bmapname, cbMap::getMapIdByName($bmapname));
			if ($cbMapid) {
				$cbMap = cbMap::getMapByID($cbMapid);
				$send2cb = $cbMap->Mapping($mauticdata, $send2cb);
			} else {
				$send2cb['firstname'] = $mauticdata['firstname'];
				$send2cb['lastname'] = $mauticdata['lastname'];
				$send2cb['email'] = $mauticdata['email'];
			}
			$send2cb['mautic_id'] = $contact['id'];
			$send2cb['assigned_user_id'] = $usrwsid;
			$send2cb['from_externalsource'] = 'mautic';

			$record = vtws_create('Contacts', $send2cb, $current_user);
			if ($record) {
				// Reset from_externalsource
				list($contact_tabid, $contact_crmid) = explode('x', $record['id']);
				$adb->pquery('UPDATE vtiger_contactdetails SET from_externalsource=? where contactid=?', array('', $contact_crmid));
				// Update corebos_id
				$auth = $mautic->authenticate();
				if ($auth) {
					$apiUrl     = $mautic->getSettings('baseUrl');
					$api        = new MauticApi();
					$contactApi = $api->newApi('contacts', $auth, $apiUrl);
					$updatedData = [
						'corebos_id' => $record['id']
					];
					$contactApi->edit($contact['id'], $updatedData);
				}
			}
		}
	} elseif ($update) {
		$contact = $data['mautic.lead_post_save_update'][0]['contact'];
		$core_fields = $contact['fields']['core'];
		$social_fields = $contact['fields']['social'];
		$fields = array_merge($core_fields, $social_fields);

		$mauticdata = array();
		foreach ($fields as $field) {
			$mauticdata[$field['alias']] = $field['value'];
		}
		$send2cb = array();
		$bmapname = 'MauticToContacts';
		$cbMapid = GlobalVariable::getVariable('BusinessMapping_'.$bmapname, cbMap::getMapIdByName($bmapname));
		if ($cbMapid) {
			$cbMap = cbMap::getMapByID($cbMapid);
			$send2cb = $cbMap->Mapping($mauticdata, $send2cb);
		} else {
			$send2cb['firstname'] = $mauticdata['firstname'];
			$send2cb['lastname'] = $mauticdata['lastname'];
			$send2cb['email'] = $mauticdata['email'];
		}
		$send2cb['mautic_id'] = $contact['id'];
		$send2cb['assigned_user_id'] = $usrwsid;
		$send2cb['id'] = $fields['corebos_id']['value'];
		$send2cb['from_externalsource'] = 'mautic';

		$record = vtws_update($send2cb, $current_user);
		if ($record) {
			// Reset from_externalsource
			list($contact_tabid, $contact_crmid) = explode('x', $record['id']);
			$adb->pquery('UPDATE vtiger_contactdetails SET from_externalsource=? where contactid=?', array('', $contact_crmid));
		}
	} elseif ($delete) {
		$contact = $data['mautic.lead_post_delete'][0]['contact'];
		$core_fields = $contact['fields']['core'];
		$social_fields = $contact['fields']['social'];
		$fields = array_merge($core_fields, $social_fields);

		if (isset($fields['corebos_id']['value']) && $fields['corebos_id']['value'] != '') {
			list($contact_tabid, $contact_crmid) = explode('x', $fields['corebos_id']['value']);
			$adb->pquery('UPDATE vtiger_contactdetails SET mautic_id=?, deleted_in_mautic=? where contactid=?', array('', 1, $contact_crmid));
		}
	}
}