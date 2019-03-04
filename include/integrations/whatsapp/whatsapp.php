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
 *  Module    : Whatsapp Integration
 *  Version   : 1.0
 *  Author    : JPL TSolucio, S. L.
 *************************************************************************************************/
include_once 'vtlib/Vtiger/Module.php';
require_once 'include/Webservices/Revise.php';
require_once 'include/Webservices/Create.php';
require "vendor/autoload.php";

// Use the REST API Client to make requests to the Twilio REST API
use Twilio\Rest\Client;

class corebos_whatsapp {
	// Configuration Properties
	private $sid = '123';
	private $token = 'abcde';
	public $senderphone = '11111111111';

	// Configuration Keys
	const KEY_ISACTIVE = 'whatsapp_isactive';
	const KEY_SID = 'sid';
	const KEY_TOKEN = 'token';
	const KEY_SENDERPHONE = 'senderphone';

	// Debug
	const DEBUG = true;

	// Errors
	public static $ERROR_NONE = 0;
	public static $ERROR_NOTCONFIGURED = 1;
	public static $ERROR_NOACCESSTOKEN = 2;

	// Utilities
	public $whatsappclient = null;

	public function __construct() {
		$this->initGlobalScope();
	}

	public function initGlobalScope() {
		$this->sid = coreBOS_Settings::getSetting(self::KEY_SID, '');
		$this->token = coreBOS_Settings::getSetting(self::KEY_TOKEN, '');
		$this->senderphone = coreBOS_Settings::getSetting(self::KEY_SENDERPHONE, '');
		if (!empty($this->token) && !empty($this->sid) && !empty($this->senderphone)) {
			$this->whatsappclient = new Client($this->sid, $this->token);
		}
	}

	public function saveSettings($isactive, $sid, $token, $senderphone) {
		coreBOS_Settings::setSetting(self::KEY_ISACTIVE, $isactive);
		coreBOS_Settings::setSetting(self::KEY_SID, $sid);
		coreBOS_Settings::setSetting(self::KEY_TOKEN, $token);
		coreBOS_Settings::setSetting(self::KEY_SENDERPHONE, $senderphone);
	}

	public function getSettings() {
		return array(
			'isActive' => coreBOS_Settings::getSetting(self::KEY_ISACTIVE, ''),
			'sid' => coreBOS_Settings::getSetting(self::KEY_SID, ''),
			'token' => coreBOS_Settings::getSetting(self::KEY_TOKEN, ''),
			'senderphone' => coreBOS_Settings::getSetting(self::KEY_SENDERPHONE, ''),
		);
	}

	public function isActive() {
		$isactive = coreBOS_Settings::getSetting(self::KEY_ISACTIVE, '0');
		return ($isactive=='1');
	}
}
?>