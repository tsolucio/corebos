<?php
include_once 'include/utils/utils.php';
include_once 'include/Webservices/Utils.php';
include_once 'include/Webservices/Create.php';
include_once 'include/Webservices/Update.php';
include_once 'include/integrations/mautic/mautic.php';

use Mautic\MauticApi;

global $adb;

$current_user = Users::getActiveAdminUser();
$usrwsid = vtws_getEntityId('Users').'x'.$current_user->id;

$HTTP_RAW_POST_DATA = file_get_contents('php://input');
$data = json_decode($HTTP_RAW_POST_DATA, true);

$create = array_key_exists('mautic.lead_post_save_new', $data);
$update = array_key_exists('mautic.lead_post_save_update', $data);

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
		$bmapname = 'MauticTOContacts';
		$cbMapid = GlobalVariable::getVariable('BusinessMapping_'.$bmapname, cbMap::getMapIdByName($bmapname));
		if ($cbMapid) {
			$send2cb = array();
			$cbMap = cbMap::getMapByID($cbMapid);
			$send2cb = $cbMap->Mapping($mauticdata, $send2cb);
			$send2cb['mautic_id'] = $contact['id'];
			$send2cb['assigned_user_id'] = $usrwsid;
			$send2cb['from_externalsource'] = 'mautic';

			$record = vtws_create('Contacts', $send2cb, $current_user);
			if ($record) {
				// Reset from_externalsource
				list($contact_tabid, $contact_crmid) = explode('x', $record['id']);
				$sql = 'UPDATE vtiger_contactdetails SET from_externalsource = ? where contactid = ?';
				$result = $adb->pquery($sql, array('', $contact_crmid));

				// Update corebos_id
				$mautic = new corebos_mautic();
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
	$bmapname = 'MauticTOContacts';
	$cbMapid = GlobalVariable::getVariable('BusinessMapping_'.$bmapname, cbMap::getMapIdByName($bmapname));
	if ($cbMapid) {
		$send2cb = array();
		$cbMap = cbMap::getMapByID($cbMapid);
		$send2cb = $cbMap->Mapping($mauticdata, $send2cb);
		$send2cb['mautic_id'] = $contact['id'];
		$send2cb['assigned_user_id'] = $usrwsid;
		$send2cb['id'] = $fields['corebos_id']['value'];
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