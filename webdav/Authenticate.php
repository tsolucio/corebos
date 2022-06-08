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
 *************************************************************************************************
 *  Module       : WEBDAV
 *************************************************************************************************/

class Authenticate extends Sabre\DAV\Auth\Backend\AbstractDigest {

	public function getDigestHash($realm, $username) {
		return md5($username.':'.$realm.':'.$this->getAccessKey($username));
	}

	private function getAccessKey($username) {
		global $adb;
		$result = $adb->pquery('SELECT accesskey FROM vtiger_users WHERE user_name=?', array(htmlentities(strip_tags($username))));
		return $adb->query_result($result, 0, 'accesskey');
	}

	public function check(Sabre\HTTP\RequestInterface $request, Sabre\HTTP\ResponseInterface $response) {
		global $adb, $current_user, $default_language, $current_language, $app_strings;
		$check = parent::check($request, $response);
		if (!$check[0]) {
			$reqHeaders = apache_request_headers();
			if (isset($reqHeaders['Authorization']) && substr($reqHeaders['Authorization'], 0, 5)=='Basic') {
				$authInfo = explode(':', base64_decode(substr($reqHeaders['Authorization'], 6)));
				if (count($authInfo)==2 && $authInfo[1]==$this->getAccessKey($authInfo[0])) {
					$check = array(true, $this->principalPrefix.$authInfo[0]);
				}
			}
		}
		if ($check[0] && substr($check[1], 0, strlen($this->principalPrefix))==$this->principalPrefix) {
			list($void, $username) = explode('/', $check[1]);
			$result = $adb->pquery('SELECT id FROM vtiger_users WHERE user_name=?', array(htmlentities(strip_tags($username))));
			$data = $adb->fetch_array($result);
			$current_user = new Users();
			$current_user->id = $data['id'];
			$current_user = $current_user->retrieve_entity_info($data['id'], 'Users');
			if (GlobalVariable::getVariable('WEBDAV_Enabled', 0)==0) {
				return [false, 'WEBDAV support is disabled.'];
			}
			if (!empty($current_user->column_fields['language'])) {
				$authenticated_user_language = $current_user->column_fields['language'];
			} else {
				$authenticated_user_language = $default_language;
			}
			coreBOS_Session::set('authenticated_user_language', $authenticated_user_language);
			$current_language = $_SESSION['authenticated_user_language'];
			$app_strings = return_application_language($current_language);
		}
		return $check;
	}
}