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

include_once 'include/utils/utils.php';
require_once 'vtlib/Vtiger/Module.php';
include_once 'include/Webservices/Create.php';
include_once 'include/Webservices/Update.php';
require "vendor/autoload.php";

// Use Mautic API Library
use Mautic\Auth\ApiAuth;
use Mautic\MauticApi;

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
	private $leadSync = '0';
	private $mauticUsername = '';
	private $mauticPassword = '';

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
	const KEY_LEADSYNC = 'maleadsync';
	const KEY_MAUTICUSERNAME = 'mauticusername';
	const KEY_MAUTICPASSWORD = 'mauticpassword';

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
		$this->leadSync = coreBOS_Settings::getSetting(self::KEY_LEADSYNC, '');
		$this->mauticUsername = coreBOS_Settings::getSetting(self::KEY_MAUTICUSERNAME, '');
		$this->mauticPassword = coreBOS_Settings::getSetting(self::KEY_MAUTICPASSWORD, '');
	}

	public function saveSettings($isactive, $baseurl, $version, $clientkey, $clientsecret, $callback, $leadsync, $username, $password) {
		coreBOS_Settings::setSetting(self::KEY_ISACTIVE, $isactive);
		coreBOS_Settings::setSetting(self::KEY_BASEURL, $baseurl);
		coreBOS_Settings::setSetting(self::KEY_VERSION, $version);
		coreBOS_Settings::setSetting(self::KEY_CLIENTKEY, $clientkey);
		coreBOS_Settings::setSetting(self::KEY_CLIENTSECRET, $clientsecret);
		coreBOS_Settings::setSetting(self::KEY_CALLBACK, $callback);
		coreBOS_Settings::setSetting(self::KEY_LEADSYNC, $leadsync);
		coreBOS_Settings::setSetting(self::KEY_MAUTICUSERNAME, $username);
		coreBOS_Settings::setSetting(self::KEY_MAUTICPASSWORD, $password);

		if ($isactive == '1' && $leadsync == '1') {
			$this->leadsToContacts();
		}
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
			'leadSync' => coreBOS_Settings::getSetting(self::KEY_LEADSYNC, ''),
			'userName' => coreBOS_Settings::getSetting(self::KEY_MAUTICUSERNAME, ''),
			'password' => coreBOS_Settings::getSetting(self::KEY_MAUTICPASSWORD, ''),
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
			$version = $this->getSettings('version');
			if ($version == 'BasicAuth') {
				$auth = $iniAuth->newAuth($this->getSettings(), 'BasicAuth');
				return $auth;
			} elseif ($version == 'OAuth2') {
				$auth = $iniAuth->newAuth($this->getSettings());
				return $auth;
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
		}
		return false;
	}

	public function leadsToContacts() {
		global $adb;
		$auth = $this->authenticate();
		if ($auth) {
			$apiUrl = $this->getSettings('baseUrl');
			$api = new MauticApi();
			$contactApi = $api->newApi('contacts', $auth, $apiUrl);
			$contacts = $contactApi->getList();

			foreach ($contacts['contacts'] as $contact_id => $contact) {
				$core_fields = $contact['fields']['core'];
				$social_fields = $contact['fields']['social'];
				$fields = array_merge($core_fields, $social_fields);

				$mauticdata = array();
				foreach ($fields as $field) {
					$mauticdata[$field['alias']] = $field['value'];
				}

				if ($mauticdata['corebos_id'] == '') {
					if ($mauticdata['lastname'] != '' && $mauticdata['email'] != '') {
						$record = $this->createCBContact($contact_id, $mauticdata);
						// Update corebos_id
						if ($record) {
							$updatedData = [
								'corebos_id' => $record['id']
							];
							$contactApi->edit($contact['id'], $updatedData);
						}
					}
				} else {
					$id = explode('x', $mauticdata['corebos_id']);
					$con_id = (isset($id[1]) ? $id[1] : '');
					$sql = 'select 1 from vtiger_contacts where id = ? limit 1';
					$res = $adb->pquery($sql, array($con_id));
					if ($adb->num_rows($res) == 1) {
						$this->updateCBContact($contact_id, $mauticdata);
					}
				}
			}
		}
	}

	public function createCBContact($contact_id, $mauticdata) {
		global $adb;
		$current_user = Users::getActiveAdminUser();
		$usrwsid = vtws_getEntityId('Users').'x'.$current_user->id;
		$bmapname = 'MauticTOContacts';
		$cbMapid = GlobalVariable::getVariable('BusinessMapping_'.$bmapname, cbMap::getMapIdByName($bmapname));
		if ($cbMapid) {
			$send2cb = array();
			$cbMap = cbMap::getMapByID($cbMapid);
			$send2cb = $cbMap->Mapping($mauticdata, $send2cb);
			$send2cb['mautic_id'] = $contact_id;
			$send2cb['assigned_user_id'] = $usrwsid;
			$send2cb['from_externalsource'] = 'mautic';

			$record = vtws_create('Contacts', $send2cb, $current_user);
			if ($record) {
				// Reset from_externalsource
				list($contact_tabid, $contact_crmid) = explode('x', $record['id']);
				$sql = 'UPDATE vtiger_contactdetails SET from_externalsource = ? where contactid = ?';
				$result = $adb->pquery($sql, array('', $contact_crmid));
			}
			return $record;
		}
		return null;
	}

	public function updateCBContact($contact_id, $mauticdata) {
		global $adb;
		$current_user = Users::getActiveAdminUser();
		$usrwsid = vtws_getEntityId('Users').'x'.$current_user->id;
		$bmapname = 'MauticTOContacts';
		$cbMapid = GlobalVariable::getVariable('BusinessMapping_'.$bmapname, cbMap::getMapIdByName($bmapname));
		if ($cbMapid) {
			$send2cb = array();
			$cbMap = cbMap::getMapByID($cbMapid);
			$send2cb = $cbMap->Mapping($mauticdata, $send2cb);
			$send2cb['mautic_id'] = $contact_id;
			$send2cb['assigned_user_id'] = $usrwsid;
			$send2cb['id'] = $mauticdata['corebos_id'];
			$send2cb['from_externalsource'] = 'mautic';

			$record = vtws_update($send2cb, $current_user);
			if ($record) {
				// Reset from_externalsource
				list($contact_tabid, $contact_crmid) = explode('x', $record['id']);
				$sql = 'UPDATE vtiger_contactdetails SET from_externalsource = ? where contactid = ?';
				$result = $adb->pquery($sql, array('', $contact_crmid));
			}
		}
	}
}