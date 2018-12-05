<?php
/*************************************************************************************************
 * Copyright 2018 JPL TSolucio, S.L. -- This file is a part of TSOLUCIO coreBOS Customizations.
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

class validateEmailWithZB {
	private static $lastErrorStatus = array();

	public static function validateEmail($field, $email) {
		$api_key = GlobalVariable::getVariable('Zero_Bounce_API_KEY', '');
		if (empty($api_key) || empty($email)) {
			return true;
		}
		$IPToValidate = '';
		// use curl to make the request
		$url = 'https://api.zerobounce.net/v2/validate?api_key='.$api_key.'&email='.urlencode($email).'&ip_address='.urlencode($IPToValidate);

		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_SSLVERSION, 6);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
		curl_setopt($ch, CURLOPT_TIMEOUT, 150);
		$response = curl_exec($ch);
		$json = json_decode($response, true);
		$valueToReturn = false;
		$text = '';
		if (curl_errno($ch)) {
			self::$lastErrorStatus[$field] = 'Curl error: ' . curl_error($ch);
			curl_close($ch);
			return true;
		}
		curl_close($ch);
		if (isset($json['status'])) {
			if ($json['status'] == 'valid') {
				$valueToReturn = true;
			} elseif ($json['status'] == 'invalid') {
				$text = $json['sub_status'];
			}
		} elseif (isset($json['error'])) {
			self::$lastErrorStatus[$field] = $json['error'];
			return true;
		}
		self::$lastErrorStatus[$field] = $text;
		return $valueToReturn;
	}

	public static function getLastErrorMsg($field) {
		echo self::$lastErrorStatus[$field];
	}
}

function validate_ZeroBounce($field, $fieldval, $params, $fields) {
	return validateEmailWithZB::validateEmail($field, $fieldval);
}