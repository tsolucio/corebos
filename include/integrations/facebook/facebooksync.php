<?php
/*************************************************************************************************
 * Copyright 2022 JPL TSolucio, S.L. -- This file is a part of TSOLUCIO coreBOS Customizations.
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
 *  Module       : Facebook Integration
 *  Version      : 1.0
 *  Author       : JPL TSolucio, S. L.
 *************************************************************************************************/

include_once 'include/utils/utils.php';
include_once 'include/Webservices/Utils.php';
include_once 'include/Webservices/Create.php';
include_once 'include/integrations/facebook/facebook.php';

function facebooksync($input) {
	global $adb;
	$facebook = new corebos_facebook();
	if (!$facebook->isActive()) {
		return;
	}

	$challenge = $_REQUEST['hub_challenge'];
	$verify_token = $_REQUEST['hub_verify_token'];

	if ($verify_token == $facebook->getHubVerificationToken()) {
		echo $challenge;
	}

	$input = file_get_contents('php://input');
	$data = json_decode($input, true);

	$field = $data['entry'][0]['changes'][0]['field'];
	if ($field == 'leadgen') {
		$leadgen_id = $data['entry'][0]['changes'][0]['value']['leadgen_id'];
		if ($leadgen_id) {
			$access_token = $facebook->getAccessToken();
			$curl_handle = curl_init();
			$url = "https://graph.facebook.com/v14.0/" . $leadgen_id . "?access_token=" . $access_token;
			curl_setopt($curl_handle, CURLOPT_URL, $url);
			curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, true);
			$curl_data = curl_exec($curl_handle);
			curl_close($curl_handle);

			if ($curl_data) {
				$response_data = json_decode($curl_data);
				if ($response_data) {
					$facebookdata = array();
					foreach ($response_data as $field => $value) {
						$facebookdata[$key] = $value;
					}

					// Send to corebos
					$destinationModule = $facebook->getDestinationModule();
					$current_user = Users::getActiveAdminUser();
					$usrwsid = vtws_getEntityId('Users').'x'.$current_user->id;

					$send2cb = array();
					$bmapname = 'Facebook2' . $destinationModule;
					$cbMapid = GlobalVariable::getVariable('BusinessMapping_'.$bmapname, cbMap::getMapIdByName($bmapname));
					if ($cbMapid) {
						$cbMap = cbMap::getMapByID($cbMapid);
						$send2cb = $cbMap->Mapping($facebookdata, $send2cb);
						$send2cb['assigned_user_id'] = $usrwsid;
						vtws_create($destinationModule, $send2cb, $current_user);
					}
				}
			}
		}
	}
}