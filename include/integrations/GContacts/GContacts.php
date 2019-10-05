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
 *  Module    : Google Contacts Integration
 *  Version   : 1.0
 *  Author    : JPL TSolucio, S. L.
 *************************************************************************************************/

class corebos_gcontacts {

	// Configuration Properties
	private $clientId = '';
	private $clientSecret = '';
	private $API_URL = '';
	private $accessCode = '';
	private $updateTime = '';
	private $accessToken = '';
	private $refreshToken = '';

	// Configuration Keys
	const key_isActive = 'gcontacts_isactive';
	const key_clientId = 'gcontacts_clientid';
	const key_clientSecret = 'gcontacts_secret';
	const key_API_URL = 'gcontacts_apiurl';
	const key_accessCode = 'gcontacts_accessCode';
	const key_updateTime = 'gcontacts_updateTime';
	const key_accessToken = 'gcontacts_accessToken';
	const key_refreshToken = 'gcontacts_refreshToken';
	const key_relateDealWith = 'gcontacts_relateDealWith';
	const IDField = 'gcontactsid';
	const LastSyncField = 'gcontactslastsync';
	const SyncWithField = 'gcontactssyncwith';
	const gcontacts_appurl = 'https://app.gcontacts.com';

	// Debug
	const Debug = true;

	// Errors
	public static $ERROR_NONE = 0;
	public static $ERROR_NOTCONFIGURED = 1;
	public static $ERROR_NOACCESSTOKEN = 2;

	public function __construct() {
		$this->initGlobalScope();
	}

	public function initGlobalScope() {
		$this->clientId = coreBOS_Settings::getSetting(self::key_clientId, '');
		$this->clientSecret = coreBOS_Settings::getSetting(self::key_clientSecret, '');
	}

	public function saveSettings($isactive, $clientId, $clientSecret) {
		coreBOS_Settings::setSetting(self::key_isActive, $isactive);
		coreBOS_Settings::setSetting(self::key_clientId, $clientId);
		coreBOS_Settings::setSetting(self::key_clientSecret, $clientSecret);
	}

	public function getSettings() {
		return array(
			'isActive' => coreBOS_Settings::getSetting(self::key_isActive, ''),
			'clientId' => coreBOS_Settings::getSetting(self::key_clientId, ''),
			'clientSecret' => coreBOS_Settings::getSetting(self::key_clientSecret, ''),
		);
	}

	public function isActive() {
		$isactive = coreBOS_Settings::getSetting(self::key_isActive, '0');
		return ($isactive=='1');
	}

	public function registerEvents() {
		global $adb;
		$adb->query("CREATE TABLE IF NOT EXISTS vtiger_google_sync_settings (user int(11) DEFAULT NULL,
			module varchar(50) DEFAULT NULL , clientgroup varchar(255) DEFAULT NULL,
			direction varchar(50) DEFAULT NULL)");
		$adb->query("CREATE TABLE IF NOT EXISTS vtiger_google_sync_fieldmapping ( vtiger_field varchar(255) DEFAULT NULL,
			google_field varchar(255) DEFAULT NULL, google_field_type varchar(255) DEFAULT NULL,
			google_custom_label varchar(255) DEFAULT NULL, user int(11) DEFAULT NULL)");
		// WSApp methods
		$adb->query("INSERT INTO `vtiger_wsapp_handlerdetails` (`type`, `handlerclass`, `handlerpath`) VALUES ('vtigerSyncLib', 'WSAPP_VtigerSyncEventHandler', 'modules/WSAPP/synclib/handlers/VtigerSyncEventHandler.php');");
		$adb->query("INSERT INTO `vtiger_wsapp_handlerdetails` (`type`, `handlerclass`, `handlerpath`) VALUES ('Google_vtigerHandler', 'Google_Vtiger_Handler', 'modules/Contacts/handlers/Vtiger.php');");
		$adb->query("INSERT INTO `vtiger_wsapp_handlerdetails` (`type`, `handlerclass`, `handlerpath`) VALUES ('Google_vtigerSyncHandler', 'Google_VtigerSync_Handler', 'modules/Contacts/handlers/VtigerSync.php');");
		// Button on List View
		$contactsModuleInstance = Vtiger_Module::getInstance('Contacts');
		$contactsModuleInstance->addLink('LISTVIEWBASIC', 'GOOGLE_CONTACTS', "return googleSynch('\$MODULE\$',this);");
	}

	public function unregisterEvents() {
		global $adb;
		// WSApp methods
		$adb->query("DELETE FROM `vtiger_wsapp_handlerdetails` where `type`='vtigerSyncLib' and `handlerclass`='WSAPP_VtigerSyncEventHandler' and `handlerpath`='modules/WSAPP/synclib/handlers/VtigerSyncEventHandler.php';");
		$adb->query("DELETE FROM `vtiger_wsapp_handlerdetails` where `type`='Google_vtigerHandler' and `handlerclass`='Google_Vtiger_Handler' and `handlerpath`='modules/Contacts/handlers/Vtiger.php';");
		$adb->query("DELETE FROM `vtiger_wsapp_handlerdetails` where `type`='Google_vtigerSyncHandler' and `handlerclass`='Google_VtigerSync_Handler' and `handlerpath`='modules/Contacts/handlers/VtigerSync.php';");
				$adb->query("DELETE FROM `vtiger_wsapp_sync_state`;");
		$adb->query("DELETE FROM `vtiger_wsapp`;");
		// Button on List View
		$contactsModuleInstance = Vtiger_Module::getInstance('Contacts');
		$contactsModuleInstance->deleteLink('LISTVIEWBASIC', 'GOOGLE_CONTACTS');
	}

	public function activateFields() {
	}

	public function deactivateFields() {
	}

	public function getIntegrationAuthorizationURL($scope = 'contacts automation') {
		return self::gcontacts_appurl.'/oauth/authorize?client_id='.$this->clientId.'&scope='.urlencode($scope).'&redirect_uri='.urlencode($this->getcoreBOSAuthorizationURL());
	}

	public function getcoreBOSAuthorizationURL() {
		global $site_URL;
		return $site_URL.'/include/integrations/gcontacts/saveauth.php';
	}
}
?>
