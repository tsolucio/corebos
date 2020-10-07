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
require_once 'vendor/autoload.php';

function googlestorage($input) {
	global $adb, $site_URL;
	coreBOS_Session::init();
	$credentialid = coreBOS_Session::get('credentialid');
	$query = 'select * from vtiger_cbcredentials inner join vtiger_crmentity on crmid=cbcredentialsid where deleted=0 and adapter=? and cbcredentialsid=?';
	$result = $adb->pquery($query, array('GoogleCloudStorage', $credentialid));
	$data = $result->FetchRow();
	$client = new Google_Client();
	$client->setClientId($data['google_clientid']);
	$client->setClientSecret($data['google_client_secret']);
	$client->setRedirectUri($site_URL.'/notifications.php?type=googlestorage');
	$client->setDeveloperKey($data['google_developer_key']);
	$client->setAccessType('offline');
	$client->setApplicationName($data['google_application_name']);
	$client->setScopes(explode(',', $data['google_scopes']));

	if (!isset($input)) {
		$auth_url = $client->createAuthUrl();
		header('Location: ' . filter_var($auth_url, FILTER_SANITIZE_URL));
	} else {
		$client->authenticate($input);
		$token = $client->getAccessToken();
		$adb->pquery('update vtiger_cbcredentials set google_refresh_token=? where adapter=? and cbcredentialsid=?', array(
				json_encode($token, JSON_UNESCAPED_SLASHES),
				'GoogleCloudStorage',
				$credentialid
		));
		echo "<script>window.close()</script>";
	}
}
?>