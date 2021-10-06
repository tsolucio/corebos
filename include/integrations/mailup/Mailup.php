<?php
/*************************************************************************************************
 * Copyright 2021 JPL TSolucio, S.L. -- This file is a part of TSOLUCIO coreBOS customizations.
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
 *  Module    : Hubspot Integration
 *  Version   : 1.0
 *  Author    : JPL TSolucio, S. L.
 *************************************************************************************************/
include_once 'vtlib/Vtiger/Module.php';
require_once 'include/Webservices/Revise.php';
require_once 'include/Webservices/Create.php';
require_once 'include/integrations/mailup/lib/MailUpClient.php';
require 'vendor/autoload.php';


class corebos_mailup {
	// Configuration Properties
	private $API_URL = 'tsolucio';
	private $clientId = '';
	private $clientSecret = '';
	private $mailup_password = '';
	private $mailup_username = '';
	private $mailup;

	// Configuration Keys
	const KEY_ISACTIVE = 'mailup_isactive';
	const KEY_API_URL = 'mailup_apiurl';
	const USERNAME = 'mailup_username';
	const PASSWORD = 'mailup_password';
	const KEY_ACCESSID = 'mailup_client_id';
	const KEY_ACCESSSECRET = 'mailup_client_secret';

	// Debug
	const DEBUG = true;

	// Errors
	public static $ERROR_NONE = 0;
	public static $ERROR_NOTCONFIGURED = 1;
	public static $ERROR_NOACCESSTOKEN = 2;

	// Utilities
	private $zendeskapi = null;

	public function __construct() {
		$this->initGlobalScope();
	}



	public function initGlobalScope() {
		$this->API_URL = coreBOS_Settings::getSetting(self::KEY_API_URL, '');
		$this->mailup_client_id = coreBOS_Settings::getSetting(self::KEY_ACCESSID, '');
		$this->mailup_client_secret = coreBOS_Settings::getSetting(self::KEY_ACCESSSECRET, '');
		$this->mailup_username = coreBOS_Settings::getSetting(self::USERNAME, '');
		$this->mailup_password = coreBOS_Settings::getSetting(self::PASSWORD, '');
	}

	public function saveSettings($isactive, $API_URL, $clientId, $clientSecret, $mailup_username, $mailup_password) {
		coreBOS_Settings::setSetting(self::KEY_ISACTIVE, $isactive);
		coreBOS_Settings::setSetting(self::KEY_API_URL, $API_URL);
		coreBOS_Settings::setSetting(self::KEY_ACCESSID, $clientId);
		coreBOS_Settings::setSetting(self::KEY_ACCESSSECRET, $clientSecret);
		coreBOS_Settings::setSetting(self::USERNAME, $mailup_username);
		coreBOS_Settings::setSetting(self::PASSWORD, $mailup_password);

		$mailUp = new MailUpClient($this->authData($clientId, $clientSecret));
		$mailUp->retrieveTokenByPassword($mailup_username, $mailup_password);

		$this->sendEMail($clientId, $clientSecret);
		// global $adb;
		// $em = new VTEventsManager($adb);
		// if (self::useEmailHook()) {
		// 	$em->registerHandler('corebos.filter.systemEmailClass.getname', 'include/integrations/mailup/Mailup.php', 'corebos_mailup');
		// } else {
		// 	$em->unregisterHandler('corebos_mailup');
		// }
	}

	public function getSettings() {
		return array(
			'isActive' => coreBOS_Settings::getSetting(self::KEY_ISACTIVE, ''),
			'API_URL' => coreBOS_Settings::getSetting(self::KEY_API_URL, 'https://services.mailup.com/Authorization/OAuth/LogOn'),
			'mailup_client_id' => coreBOS_Settings::getSetting(self::KEY_ACCESSID, ''),
			'mailup_client_secret' => coreBOS_Settings::getSetting(self::KEY_ACCESSSECRET, ''),
			'mailup_username' => coreBOS_Settings::getSetting(self::USERNAME, ''),
			'mailup_password' => coreBOS_Settings::getSetting(self::PASSWORD, '')
		);
	}

	public function isActive() {
		$isactive = coreBOS_Settings::getSetting(self::KEY_ISACTIVE, '0');
		return ($isactive=='1');
	}


	public function sendEMail($clientId, $clientSecret) {
		// Not format this string, otherwise the server will return 400 error.
		$message = "<html><body><p>Hello<\\/p><\\/body><\\/html>";
		$email = '{
            "Subject": "Test Message Objective-COREBOS cc",
            "idList": "1",
            "Content": "' . $message . '",
            "Embed": true,
            "IsConfirmation": true,
            "Fields": [],
            "Notes": "Some notes",
            "Tags": [],
            "TrackingInfo": {
                "CustomParams": "",
                "Enabled": true,
                "Protocols": [
                    "http"
                ]
            }
        }';

		$mailUp = new MailUpClient($this->authData($clientId, $clientSecret));

		$result = $mailUp->getResult(
			"POST",
			"JSON",
			$email,
			"Console",
			"/Console/List/1/Email",
			"Create and save \"hello\" message"
		);
	}

	private function authData($clientId, $clientSecret) {
		return array(
			'client_id' => $clientId,
			'secret_key' => $clientSecret,
			'callback_url' => $this->callBackUrl()
		);
	}

	private function callBackUrl() {
		return (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS']
			=== 'on' ? "https" : "http") . "://" .
			$_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'];
	}
}
?>