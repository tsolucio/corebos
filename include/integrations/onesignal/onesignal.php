<?php
/*************************************************************************************************
 * Copyright 2020 JPL TSolucio, S.L. -- This file is a part of TSOLUCIO coreBOS customizations.
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
	public static $APIURL = 'https://onesignal.com/api/v1/notifications';
	public static $DEBUG = false;

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

	public function sendTestMessage() {
		global $current_user, $site_URL;
		self::sendDesktopNotification(
			array('en' => 'This is a test message from '.GlobalVariable::getVariable('Application_UI_Name', 'coreBOS')),
			array('en' => 'Test Message'),
			array('en' => 'Hello'),
			array(),
			array($current_user->id),
			$site_URL,
			array()
		);
	}

	/** Function to Send Push notification to corebos user
	 * @param array $contents -- Content Displayed on Notification
	 * @param array $headings -- Notification Head
	 * @param array $subtitle -- Notification Header Subtitile
	 * @param array $filters -- Condition for user to Receive Notification
	 * @param array $external_user_id -- Condition for user to Receive Notification
	 * @return boolean $sendStatus -- Notification Sent Status
	*/
	public static function sendDesktopNotification($contents, $headings, $subtitle, $filters, $external_user_id, $web_url, $web_buttons) {
		$sendStatus = false;

		$clientclass = new corebos_onesignal();
		$isactive = $clientclass->isActive();
		$appid = $clientclass->getAppId();
		$apikey = $clientclass->getAPIKey();

		if ($isactive) {
			$fields = array(
				'app_id' => $appid
			);

			if (!empty($contents)) {
				$fields['contents'] = $contents;
			}

			if (!empty($headings)) {
				$fields['headings'] = $headings;
			}

			if (!empty($filters)) {
				$fields['filters'] = $filters;
			}

			if (!empty($subtitle)) {
				$fields['subtitle'] = $subtitle;
			}

			if (!empty($external_user_id)) {
				$fields['include_external_user_ids'] = $external_user_id;
			}

			if (!empty($web_buttons)) {
				$fields['web_buttons'] = $web_buttons;
			}

			if ($web_url != '') {
				$fields['web_url'] = $web_url;
			}

			$fields = json_encode($fields);

			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $clientclass::$APIURL);
			curl_setopt($ch, CURLOPT_HTTPHEADER, array(
				'Content-Type: application/json; charset=utf-8',
				'Authorization: Basic '.$apikey
			));
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_HEADER, false);
			curl_setopt($ch, CURLOPT_POST, true);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

			$r = curl_exec($ch);
			if (self::$DEBUG) {
				global $log;
				$log->fatal([$fields, $r]);
			}
			curl_close($ch);
		}
		return $sendStatus;
	}
}
?>