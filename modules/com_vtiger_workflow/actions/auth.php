<?php
/*************************************************************************************************
 * Copyright 2020 JPL TSolucio, S.L. -- This file is a part of TSOLUCIO coreBOS Customizations.
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
require_once 'data/CRMEntity.php';
require_once 'include/utils/utils.php';

class auth_Action extends CoreBOS_ActionController {

	public function getAuthToken() {
		global $adb;
		coreBOS_Session::init();
		$adapter = vtlib_purify($_REQUEST['service']);
		$credentialid = vtlib_purify($_REQUEST['credentialid']);
		$query = 'select * from vtiger_cbcredentials inner join vtiger_crmentity on crmid=cbcredentialsid where deleted=0 and adapter=? and cbcredentialsid=?';
		$result = $adb->pquery($query, array($adapter, $credentialid));
		$data = $result->FetchRow();
		if ($adb->num_rows($result) == 0) {
			return;
		}
		if ($adapter == 'GoogleCloudStorage') {
			//enable access in the first time by the user
			coreBOS_Session::set('credentialid', $credentialid);
			$this->getClient($data);
		}
	}

	public function getServiceType() {
		$credentialid = vtlib_purify($_REQUEST['credentialid']);
		$focus = CRMEntity::getInstance('cbCredentials');
		$focus->retrieve_entity_info($credentialid, 'cbCredentials');
		echo json_encode(array('service' => $focus->column_fields['adapter']));
	}

	public function getClient($data) {
		global $site_URL;
		require_once 'vendor/autoload.php';
		$client = new Google_Client();
		$client->setClientId($data['google_clientid']);
		$client->setClientSecret($data['google_client_secret']);
		$client->setRedirectUri($site_URL.'/notifications.php?type=googlestorage');
		$client->setDeveloperKey($data['google_developer_key']);
		$client->setAccessType('offline');
		$client->setApplicationName($data['google_application_name']);
		$client->setScopes(explode(',', $data['google_scopes']));
		$redirect_uri = $site_URL.'/notifications.php?type=googlestorage';
		echo json_encode(array('session'=>$redirect_uri));
	}
}
?>