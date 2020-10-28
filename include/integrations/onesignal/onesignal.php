<?php
/*************************************************************************************************
 * Copyright 2017 JPL TSolucio, S.L. -- This file is a part of TSOLUCIO coreBOS customizations.
 * You can copy, adapt and distribute the work under the "Attribution-NonCommercial-ShareAlike"
 * Vizsage Public License (the "License"). You may not use this file except in compliance with the
 * License. Roughly speaking, non-commercial users may share and modify this code, but must give credit
 * and share improvements. However, for proper details please read the full License, available at
 * http://vizsage.com/license/Vizsage-License-BY-NC-SA.html and the handy reference for understanding
 * the full license at http://vizsage.com/license/Vizsage-Deed-BY-NC-SA.html. Unless required by
 * applicable law or agreed to in writing, any software distributed under the License is distributed
 * on an  "AS IS" BASIS, WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and limitations under the
 * License terms of Creative Commons Attribution-NonCommercial-ShareAlike 3.0 (the License).
 *************************************************************************************************
 *  Module    : OneSignal Integration
 *  Version   : 1.0
 *  Author    : JPL TSolucio, S. L.
 *************************************************************************************************/

class corebos_onesignal {
	// Configuration Properties
	private $oneSignalAppId = '123';
	private $oneSignalAPIKey = 'abcde';

	// Configuration Keys
	const KEY_ISACTIVE = 'onesignal_isactive';
	const KEY_APP_ID= 'onesignal_app_id';
	const KEY_API_KEY = 'onesignal_api_key';

	// Debug
	const DEBUG = true;

	// Errors
	public static $ERROR_NONE = 0;
	public static $ERROR_NOTCONFIGURED = 1;
	public static $ERROR_NOACCESSTOKEN = 2;


	public function __construct() {
		$this->initGlobalScope();
	}

	public function initGlobalScope() {
		$this->oneSignalAppId = coreBOS_Settings::getSetting(self::KEY_APP_ID, '');
		$this->oneSignalAPIKey = coreBOS_Settings::getSetting(self::KEY_API_KEY, '');
	}

	public function saveSettings($isactive, $oneSignalAppId, $oneSignalAPIKey) {
		coreBOS_Settings::setSetting(self::KEY_ISACTIVE, $isactive);
		coreBOS_Settings::setSetting(self::KEY_APP_ID, $oneSignalAppId);
		coreBOS_Settings::setSetting(self::KEY_API_KEY, $oneSignalAPIKey);
	}

	public function getSettings() {
		return array(
			'isActive' => coreBOS_Settings::getSetting(self::KEY_ISACTIVE, ''),
			'onesignal_app_id' => coreBOS_Settings::getSetting(self::KEY_APP_ID, ''),
			'onesignal_api_key' => coreBOS_Settings::getSetting(self::KEY_API_KEY, ''),
		);
	}

	public function isActive() {
		$isactive = coreBOS_Settings::getSetting(self::KEY_ISACTIVE, '0');
		return ($isactive=='1');
	}

	public function getAppId() {
		return coreBOS_Settings::getSetting(self::KEY_APP_ID, '');
	}

	public function getAPIKey() {
		return coreBOS_Settings::getSetting(self::KEY_API_KEY, '');
	}
}
?>