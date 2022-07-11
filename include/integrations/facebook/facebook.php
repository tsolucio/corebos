<?php
/*************************************************************************************************
 * Copyright 2022 JPL TSolucio, S.L. -- This file is a part of TSOLUCIO coreBOS customizations.
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
 *  Module    : Facebook Integration
 *  Version   : 1.0
 *  Author    : JPL TSolucio, S. L.
 *************************************************************************************************/

class corebos_facebook {
	// Configuration Properties
	private $fbHubVerificationToken = '123';
	private $fbAccessToken = '123';
	private $fbDestinationModule = '';

	// Configuration Keys
	const KEY_ISACTIVE = 'facebook_isactive';
	const KEY_FB_HUB_VERIFICATION_TOKEN= 'fb_hub_verification_token';
	const KEY_FB_ACCESS_TOKEN= 'fb_access_token';
	const KEY_FB_DESTINATION_MODULE = 'fb_destination_module';

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
		$this->fbHubVerificationToken = coreBOS_Settings::getSetting(self::KEY_FB_HUB_VERIFICATION_TOKEN, '');
		$this->fbAccessToken = coreBOS_Settings::getSetting(self::KEY_FB_ACCESS_TOKEN, '');
		$this->fbDestinationModule = coreBOS_Settings::getSetting(self::KEY_FB_DESTINATION_MODULE, '');
	}

	public function saveSettings($isactive, $fbHubVerificationToken, $fbAccessToken, $fbDestinationModule) {
		global $adb;

		coreBOS_Settings::setSetting(self::KEY_ISACTIVE, $isactive);
		coreBOS_Settings::setSetting(self::KEY_FB_HUB_VERIFICATION_TOKEN, $fbHubVerificationToken);
		coreBOS_Settings::setSetting(self::KEY_FB_ACCESS_TOKEN, $fbAccessToken);
		coreBOS_Settings::setSetting(self::KEY_FB_DESTINATION_MODULE, $fbDestinationModule);

		if ($isactive == '1') {
			$checkrs = $adb->pquery(
				'select 1 from vtiger_notificationdrivers where path=? and functionname=?',
				array('include/integrations/facebook/facebooksync.php', 'facebooksync')
			);
			if ($checkrs && $adb->num_rows($checkrs)==0) {
				$adb->query(
					"INSERT INTO vtiger_notificationdrivers (type,path,functionname) VALUES ('facebook','include/integrations/facebook/facebooksync.php','facebooksync')"
				);
			}
		} else {
			$adb->pquery(
				'DELETE FROM vtiger_notificationdrivers WHERE path=? and functionname=?',
				array('include/integrations/facebook/facebooksync.php', 'facebooksync')
			);
		}
	}

	public function getSettings() {
		return array(
			'isActive' => coreBOS_Settings::getSetting(self::KEY_ISACTIVE, ''),
			'fb_hub_verification_token' => coreBOS_Settings::getSetting(self::KEY_FB_HUB_VERIFICATION_TOKEN, ''),
			'fb_access_token' => coreBOS_Settings::getSetting(self::KEY_FB_ACCESS_TOKEN, ''),
			'fb_destination_module' => coreBOS_Settings::getSetting(self::KEY_FB_DESTINATION_MODULE, ''),
		);
	}

	public function isActive() {
		$isactive = coreBOS_Settings::getSetting(self::KEY_ISACTIVE, '0');
		return ($isactive=='1');
	}

	public function getHubVerificationToken() {
		return coreBOS_Settings::getSetting(self::KEY_FB_HUB_VERIFICATION_TOKEN, '');
	}

	public function getAccessToken() {
		return coreBOS_Settings::getSetting(self::KEY_FB_ACCESS_TOKEN, '');
	}

	public function getDestinationModule() {
		return coreBOS_Settings::getSetting(self::KEY_FB_DESTINATION_MODULE, '');
	}
}
?>