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
 *  Module    : OneSignal Integration
 *  Version   : 1.0
 *  Author    : JPL TSolucio, S. L.
 *************************************************************************************************/
require_once 'vtlib/Vtiger/Module.php';
require "vendor/autoload.php";

// Use Mautic API Library
use Mautic\Auth\ApiAuth;

class corebos_mautic {
	// Configuration Properties
	private $baseUrl = 'https://mautic-server.com';
	private $version = 'OAuth2';
	private $clientKey = '';
	private $clientSecret = '';
	private $callback = '';
	private $accessToken = '';
	private $accessTokenSecret = '';
	private $accessTokenExpires = '';
	private $tokenType = '';
	private $refreshToken = '';

	// Configuration Keys
	const KEY_ISACTIVE = 'mautic_isactive';
	const KEY_BASEURL = 'mabaseurl';
	const KEY_VERSION = 'maversion';
	const KEY_CLIENTKEY = 'maclientkey';
	const KEY_CLIENTSECRET = 'maclientsecret';
	const KEY_CALLBACK = 'macallback';
	const KEY_ACCESSTOKEN = 'maaccesstoken';
	const KEY_ACCESSTOKENSECRET = 'maaccesstokensecret';
	const KEY_ACCESSTOKENEXPIRES = 'maaccesstokenexpires';
	const KEY_TOKENTYPE = 'matokentype';
	const KEY_REFRESHTOKEN = 'marefreshtoken';

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
		$this->baseUrl = coreBOS_Settings::getSetting(self::KEY_BASEURL, '');
		$this->version = coreBOS_Settings::getSetting(self::KEY_VERSION, '');
		$this->clientKey = coreBOS_Settings::getSetting(self::KEY_CLIENTKEY, '');
		$this->clientSecret = coreBOS_Settings::getSetting(self::KEY_CLIENTSECRET, '');
		$this->callback = coreBOS_Settings::getSetting(self::KEY_CALLBACK, '');
		$this->accessToken = coreBOS_Settings::getSetting(self::KEY_ACCESSTOKEN, '');
		$this->accessTokenSecret = coreBOS_Settings::getSetting(self::KEY_ACCESSTOKENSECRET, '');
		$this->accessTokenExpires = coreBOS_Settings::getSetting(self::KEY_ACCESSTOKENEXPIRES, '');
		$this->tokenType = coreBOS_Settings::getSetting(self::KEY_TOKENTYPE, '');
		$this->refreshToken = coreBOS_Settings::getSetting(self::KEY_REFRESHTOKEN, '');
	}

	public function saveSettings($isactive, $baseurl, $version, $clientkey, $clientsecret, $callback) {
		coreBOS_Settings::setSetting(self::KEY_ISACTIVE, $isactive);
		coreBOS_Settings::setSetting(self::KEY_BASEURL, $baseurl);
		coreBOS_Settings::setSetting(self::KEY_VERSION, $version);
		coreBOS_Settings::setSetting(self::KEY_CLIENTKEY, $clientkey);
		coreBOS_Settings::setSetting(self::KEY_CLIENTSECRET, $clientsecret);
		coreBOS_Settings::setSetting(self::KEY_CALLBACK, $callback);
	}

	public function getSettings($key = '') {
		$settings = array(
			'isActive' => coreBOS_Settings::getSetting(self::KEY_ISACTIVE, ''),
			'baseUrl' => coreBOS_Settings::getSetting(self::KEY_BASEURL, ''),
			'version' => coreBOS_Settings::getSetting(self::KEY_VERSION, ''),
			'clientKey' => coreBOS_Settings::getSetting(self::KEY_CLIENTKEY, ''),
			'clientSecret' => coreBOS_Settings::getSetting(self::KEY_CLIENTSECRET, ''),
			'callback' => coreBOS_Settings::getSetting(self::KEY_CALLBACK, ''),
			'accessToken' => coreBOS_Settings::getSetting(self::KEY_ACCESSTOKEN, ''),
			'accessTokenSecret' => coreBOS_Settings::getSetting(self::KEY_ACCESSTOKENSECRET, ''),
			'accessTokenExpires' => coreBOS_Settings::getSetting(self::KEY_ACCESSTOKENEXPIRES, ''),
			'refreshToken' => coreBOS_Settings::getSetting(self::KEY_REFRESHTOKEN, ''),
		);
		if (!empty($key)) {
			return $settings[$key];
		} else {
			return $settings;
		}
	}

	public function isActive() {
		$isactive = coreBOS_Settings::getSetting(self::KEY_ISACTIVE, '0');
		return ($isactive=='1');
	}

	public function authenticate() {
		if ($this->isActive()) {
			session_start();
			$iniAuth = new ApiAuth();
			$auth = $iniAuth->newAuth($this->getSettings());
			try {
				if ($auth->validateAccessToken()) {
					if ($auth->accessTokenUpdated()) {
						$accessTokenData = $auth->getAccessTokenData();
						coreBOS_Settings::setSetting(self::KEY_ACCESSTOKEN, $accessTokenData['access_token']);
						coreBOS_Settings::setSetting(self::KEY_ACCESSTOKENEXPIRES, $accessTokenData['expires']);
						coreBOS_Settings::setSetting(self::KEY_TOKENTYPE, $accessTokenData['token_type']);
						coreBOS_Settings::setSetting(self::KEY_REFRESHTOKEN, $accessTokenData['refresh_token']);
					}
				}
				return $auth;
			} catch (Exception $e) {
				// To do: Error handling
			}
		}
		return false;
	}
}