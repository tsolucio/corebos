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
	private $fbVerificationToken = '123';
	private $fbDestinationModule = '';

	// Configuration Keys
	const KEY_ISACTIVE = 'facebook_isactive';
	const KEY_FB_VERIFICATION_CODE= 'fb_verification_code';
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
		$this->fbVerificationToken = coreBOS_Settings::getSetting(self::KEY_FB_VERIFICATION_CODE, '');
		$this->fbDestinationModule = coreBOS_Settings::getSetting(self::KEY_FB_DESTINATION_MODULE, '');
	}

	public function saveSettings($isactive, $fbVerificationCode, $fbDestinationModule) {
		coreBOS_Settings::setSetting(self::KEY_ISACTIVE, $isactive);
		coreBOS_Settings::setSetting(self::KEY_FB_VERIFICATION_CODE, $fbVerificationCode);
		coreBOS_Settings::setSetting(self::KEY_FB_DESTINATION_MODULE, $fbDestinationModule);
	}

	public function getSettings() {
		return array(
			'isActive' => coreBOS_Settings::getSetting(self::KEY_ISACTIVE, ''),
			'fb_verification_code' => coreBOS_Settings::getSetting(self::KEY_FB_VERIFICATION_CODE, ''),
			'fb_destination_module' => coreBOS_Settings::getSetting(self::KEY_FB_DESTINATION_MODULE, ''),
		);
	}

	public function isActive() {
		$isactive = coreBOS_Settings::getSetting(self::KEY_ISACTIVE, '0');
		return ($isactive=='1');
	}

	public function getVerificationCode() {
		return coreBOS_Settings::getSetting(self::KEY_FB_VERIFICATION_CODE, '');
	}

	public function getDestinationModule() {
		return coreBOS_Settings::getSetting(self::KEY_FB_DESTINATION_MODULE, '');
	}
}
?>