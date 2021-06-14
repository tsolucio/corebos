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
 *  Module       : Whatsapp Integration
 *  Version      : 1.0
 *  Author       : JPL TSolucio, S. L.
 *************************************************************************************************/

include_once 'include/utils/utils.php';
include_once 'include/Webservices/Utils.php';
include_once 'include/Webservices/Create.php';
include_once 'include/Webservices/Update.php';
include_once 'include/integrations/mautic/mautic.php';

use Mautic\MauticApi;

function accountsync($input) {
	global $adb;

    $current_user = Users::getActiveAdminUser();
	$usrwsid = vtws_getEntityId('Users').'x'.$current_user->id;

	$data = json_decode($input, true);

    $createupdate = array_key_exists('mautic.company_post_save', $data);
	$delete = array_key_exists('mautic.company_post_delete', $data);

    if ($createupdate) {
        $company = $data['mautic.company_post_save'][0]['company'];
        $core_fields = $company['fields']['core'];
        $professional_fields = $company['fields']['professional'];
        $fields = array_merge($core_fields, $professional_fields);

        $mauticdata = array();
        foreach ($fields as $field) {
            $mauticdata[$field['alias']] = $field['value'];
        }

        $bmapname = 'MauticToAccounts';
        $cbMapid = GlobalVariable::getVariable('BusinessMapping_'.$bmapname, cbMap::getMapIdByName($bmapname));
        if ($cbMapid) {
            $send2cb = array();
            $cbMap = cbMap::getMapByID($cbMapid);
            $send2cb = $cbMap->Mapping($mauticdata, $send2cb);
            $send2cb['mautic_id'] = $company['id'];
            $send2cb['assigned_user_id'] = $usrwsid;
            $send2cb['from_externalsource'] = 'mautic';

            $record = null;
            if ($fields['company_corebos_id']['value'] == NULL || $fields['company_corebos_id']['value'] == '') {
                $record = vtws_create('Accounts', $send2cb, $current_user);
            } else {
                $send2cb['id'] = $fields['company_corebos_id']['value'];
                $record = vtws_update($send2cb, $current_user);
            }

            if ($record) {
                // Reset from_externalsource
                list($account_tabid, $account_crmid) = explode('x', $record['id']);
                $sql = 'UPDATE vtiger_account SET from_externalsource = ? where accountid = ?';
                $result = $adb->pquery($sql, array('', $account_crmid));

                if ($fields['company_corebos_id']['value'] == NULL || $fields['company_corebos_id']['value'] == '') {
                    // Update company_corebos_id
					$mautic = new corebos_mautic();
					$auth = $mautic->authenticate();
					if ($auth) {
						$apiUrl     = $mautic->getSettings('baseUrl');
						$api        = new MauticApi();
						$companyApi = $api->newApi('companies', $auth, $apiUrl);

						$updatedData = [
							'company_corebos_id' => $record['id']
						];
						$companyApi->edit($company['id'], $updatedData);
					}
                }
            }
        }
    } else if ($delete) {
        $company = $data['mautic.company_post_delete'][0]['company'];
        $core_fields = $company['fields']['core'];
        $professional_fields = $company['fields']['professional'];
        $fields = array_merge($core_fields, $professional_fields);

        if (isset($fields['company_corebos_id']['value']) && $fields['company_corebos_id']['value'] != '') {
			list($account_tabid, $account_crmid) = explode('x', $fields['company_corebos_id']['value']);
			$sql = 'UPDATE vtiger_account SET mautic_id = ?, deleted_in_mautic = ? where accountid = ?';
			$result = $adb->pquery($sql, array('', 1, $account_crmid));
		}
    }
}