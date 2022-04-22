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
 *  Module    : Mautic Integration
 *  Version   : 1.0
 *  Author    : JPL TSolucio, S. L.
 *************************************************************************************************/

include_once 'include/utils/utils.php';
require_once 'vtlib/Vtiger/Module.php';
include_once 'include/Webservices/Create.php';
include_once 'include/Webservices/Update.php';
require_once 'modules/cbupdater/cbupdaterWorker.php';
require 'vendor/autoload.php';

// Use Mautic API Library
use Mautic\Auth\ApiAuth;
use Mautic\MauticApi;

class mauticUpdaterWorker extends cbupdaterWorker {
	// stub to access the updater worker methods
	public function __construct($cbid = 0, $dieonerror = true) {
		parent::__construct(0, false);
	}
}

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
	private $companiesSync = '0';
	private $mauticUsername = '';
	private $mauticPassword = '';
	private $mauticWebhookSecret = '';

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
	const KEY_COMPANIESSYNC = 'macompaniessync';
	const KEY_MAUTICUSERNAME = 'mauticusername';
	const KEY_MAUTICPASSWORD = 'mauticpassword';
	const KEY_MAUTICWEBHOOKSECRET = 'mauticwebhooksecret';

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
		$this->companiesSync = coreBOS_Settings::getSetting(self::KEY_COMPANIESSYNC, '');
		$this->mauticUsername = coreBOS_Settings::getSetting(self::KEY_MAUTICUSERNAME, '');
		$this->mauticPassword = coreBOS_Settings::getSetting(self::KEY_MAUTICPASSWORD, '');
		$this->mauticWebhookSecret = coreBOS_Settings::getSetting(self::KEY_MAUTICWEBHOOKSECRET, '');
	}

	public function saveSettings($isactive, $baseurl, $version, $clientkey, $clientsecret, $callback, $leadsync, $companiessync, $username, $password, $mauticwebhooksecret) {
		coreBOS_Settings::setSetting(self::KEY_ISACTIVE, $isactive);
		coreBOS_Settings::setSetting(self::KEY_BASEURL, $baseurl);
		coreBOS_Settings::setSetting(self::KEY_VERSION, $version);
		coreBOS_Settings::setSetting(self::KEY_CLIENTKEY, $clientkey);
		coreBOS_Settings::setSetting(self::KEY_CLIENTSECRET, $clientsecret);
		coreBOS_Settings::setSetting(self::KEY_CALLBACK, $callback);
		coreBOS_Settings::setSetting(self::KEY_LEADSYNC, $leadsync);
		coreBOS_Settings::setSetting(self::KEY_COMPANIESSYNC, $leadsync);
		coreBOS_Settings::setSetting(self::KEY_MAUTICUSERNAME, $username);
		coreBOS_Settings::setSetting(self::KEY_MAUTICPASSWORD, $password);
		coreBOS_Settings::setSetting(self::KEY_MAUTICWEBHOOKSECRET, $mauticwebhooksecret);

		if ($isactive == '1') {
			if ($leadsync == '1') {
				$this->leadsToContacts();
			}
			if ($companiessync == '1') {
				$this->companiesToAccounts();
			}
			$this->activateFieldsProcess();
		} else {
			$this->deactivateFieldsProcess();
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
			'companiesSync' => coreBOS_Settings::getSetting(self::KEY_COMPANIESSYNC, ''),
			'userName' => coreBOS_Settings::getSetting(self::KEY_MAUTICUSERNAME, ''),
			'password' => coreBOS_Settings::getSetting(self::KEY_MAUTICPASSWORD, ''),
			'webhookSecret' => coreBOS_Settings::getSetting(self::KEY_MAUTICWEBHOOKSECRET, ''),
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

	public function activateFieldsProcess() {
		global $adb;

		$emm = new VTEntityMethodManager($adb);
		$emm->addEntityMethod('Accounts', 'mauticAccountCreate', 'include/integrations/mautic/workflowAccount.php', 'mauticAccountCreate');
		$emm->addEntityMethod('Accounts', 'mauticAccountUpdate', 'include/integrations/mautic/workflowAccount.php', 'mauticAccountUpdate');
		$emm->addEntityMethod('Accounts', 'mauticAccountDelete', 'include/integrations/mautic/workflowAccount.php', 'mauticAccountDelete');
		$emm->addEntityMethod('Contacts', 'mauticContactDelete', 'include/integrations/mautic/workflowContact.php', 'mauticContactDelete');
		$emm->addEntityMethod('Contacts', 'mauticContactCreate', 'include/integrations/mautic/workflowContact.php', 'mauticContactCreate');
		$emm->addEntityMethod('Contacts', 'mauticContactUpdate', 'include/integrations/mautic/workflowContact.php', 'mauticContactUpdate');

		$fieldLayout = array(
			'Contacts' => array(
				'LBL_CONTACT_INFORMATION' => array(
					'mautic_id' => array(
						'columntype'=>'varchar(200)',
						'typeofdata'=>'V~O',
						'uitype'=>'1',
						'displaytype'=>'1',
						'label'=>'Mautic ID',
					),
					'deleted_in_mautic' => array(
						'columntype'=>'varchar(3)',
						'typeofdata'=>'C~O',
						'uitype'=>'56',
						'displaytype'=>'1',
						'label'=>'Deleted in Mautic',
					),
					'contact_points' => array(
						'columntype'=>'integer(200)',
						'typeofdata'=>'N~O',
						'uitype'=>'7',
						'displaytype'=>'1',
						'label'=>'Contact Points',
					),
					'from_externalsource' => array(
						'columntype'=>'varchar(200)',
						'typeofdata'=>'V~O',
						'uitype'=>'1',
						'displaytype'=>'1',
						'label'=>'From External Source',
					),
				)
			),
			'Accounts' => array(
				'LBL_ACCOUNT_INFORMATION' => array(
					'mautic_id' => array(
						'columntype'=>'varchar(200)',
						'typeofdata'=>'V~O',
						'uitype'=>'1',
						'displaytype'=>'1',
						'label'=>'Mautic ID',
					),
					'deleted_in_mautic' => array(
						'columntype'=>'varchar(3)',
						'typeofdata'=>'C~O',
						'uitype'=>'56',
						'displaytype'=>'1',
						'label'=>'Deleted in Mautic',
					),
					'from_externalsource' => array(
						'columntype'=>'varchar(200)',
						'typeofdata'=>'V~O',
						'uitype'=>'1',
						'displaytype'=>'1',
						'label'=>'From External Source',
					),
				)
			)
		);
		$cbwrk = new mauticUpdaterWorker();
		$cbwrk->massCreateFields($fieldLayout);
		$webhookSecret = $this->getSettings('webhookSecret');
		$checkrs = $adb->pquery(
			'select 1 from vtiger_notificationdrivers where path=? and functionname=?',
			array('include/integrations/mautic/contactsync.php', 'contactsync')
		);
		if ($checkrs && $adb->num_rows($checkrs)==0) {
			$adb->query(
				"INSERT INTO vtiger_notificationdrivers (type,path,functionname, signedvalue, signedkey, signedvalidation) VALUES ('mauticcontact','include/integrations/mautic/contactsync.php','contactsync', '$webhookSecret', 'secret', 'validateMauticSecret')"
			);
		}
		$checkrs = $adb->pquery(
			'select 1 from vtiger_notificationdrivers where path=? and functionname=?',
			array('include/integrations/mautic/accountsync.php', 'accountsync')
		);
		if ($checkrs && $adb->num_rows($checkrs)==0) {
			$adb->query(
				"INSERT INTO vtiger_notificationdrivers (type,path,functionname, signedvalue, signedkey, signedvalidation) VALUES ('mauticaccount','include/integrations/mautic/accountsync.php','accountsync', '$webhookSecret', 'secret', 'validateMauticSecret')"
			);
		}
	}

	public function deactivateFieldsProcess() {
		global $adb;

		$fieldLayout = array(
			'Contacts' => array(
				'mautic_id',
				'deleted_in_mautic',
				'contact_points',
				'from_externalsource',
			),
			'Accounts' => array(
				'mautic_id',
				'deleted_in_mautic',
				'from_externalsource',
			)
		);
		$cbwrk = new mauticUpdaterWorker();
		$cbwrk->massHideFields($fieldLayout);
		$adb->pquery(
			'DELETE FROM vtiger_notificationdrivers WHERE path=? and functionname=?',
			array('include/integrations/mautic/contactsync.php', 'contactsync')
		);
		$adb->pquery(
			'DELETE FROM vtiger_notificationdrivers WHERE path=? and functionname=?',
			array('include/integrations/mautic/accountsync.php', 'accountsync')
		);
	}

	public function authenticate() {
		if ($this->isActive()) {
			@session_start();
			$iniAuth = new ApiAuth();
			$version = $this->getSettings('version');
			if ($version == 'BasicAuth') {
				return $iniAuth->newAuth($this->getSettings(), 'BasicAuth');
			} elseif ($version == 'OAuth2') {
				return $iniAuth->newAuth($this->getSettings());
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
			if (!empty($contacts['contacts'])) {
				foreach ($contacts['contacts'] as $contact_id => $contact) {
					$core_fields = $contact['fields']['core'];
					$social_fields = $contact['fields']['social'];
					$fields = array_merge($core_fields, $social_fields);

					$mauticdata = array();
					foreach ($fields as $field) {
						$mauticdata[$field['alias']] = $field['value'];
					}

					if (empty($mauticdata['corebos_id'])) {
						if (!empty($mauticdata['lastname']) && !empty($mauticdata['email'])) {
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
						$res = $adb->pquery('select 1 from vtiger_contactdetails where contactid=? limit 1', array($con_id));
						if ($adb->num_rows($res) == 1) {
							$this->updateCBContact($contact_id, $mauticdata);
						}
					}
				}
			}
		}
	}

	public function createCBContact($contact_id, $mauticdata) {
		global $adb;
		$current_user = Users::getActiveAdminUser();
		$usrwsid = vtws_getEntityId('Users').'x'.$current_user->id;
		$send2cb = array();
		$bmapname = 'MauticToContacts';
		$cbMapid = GlobalVariable::getVariable('BusinessMapping_'.$bmapname, cbMap::getMapIdByName($bmapname));
		if ($cbMapid) {
			$cbMap = cbMap::getMapByID($cbMapid);
			$send2cb = $cbMap->Mapping($mauticdata, $send2cb);
		} else {
			$send2cb['firstname'] = $mauticdata['firstname'];
			$send2cb['lastname'] = $mauticdata['lastname'];
			$send2cb['email'] = $mauticdata['email'];
		}
		$send2cb['mautic_id'] = $contact_id;
		$send2cb['assigned_user_id'] = $usrwsid;
		$send2cb['from_externalsource'] = 'mautic';

		$record = vtws_create('Contacts', $send2cb, $current_user);
		if ($record) {
			// Reset from_externalsource
			list($contact_tabid, $contact_crmid) = explode('x', $record['id']);
			$adb->pquery('UPDATE vtiger_contactdetails SET from_externalsource=? where contactid=?', array('', $contact_crmid));
			return $record;
		}
		return null;
	}

	public function updateCBContact($contact_id, $mauticdata) {
		global $adb;
		$current_user = Users::getActiveAdminUser();
		$usrwsid = vtws_getEntityId('Users').'x'.$current_user->id;
		$send2cb = array();
		$bmapname = 'MauticToContacts';
		$cbMapid = GlobalVariable::getVariable('BusinessMapping_'.$bmapname, cbMap::getMapIdByName($bmapname));
		if ($cbMapid) {
			$cbMap = cbMap::getMapByID($cbMapid);
			$send2cb = $cbMap->Mapping($mauticdata, $send2cb);
		} else {
			$send2cb['firstname'] = $mauticdata['firstname'];
			$send2cb['lastname'] = $mauticdata['lastname'];
			$send2cb['email'] = $mauticdata['email'];
		}
		$send2cb['mautic_id'] = $contact_id;
		$send2cb['assigned_user_id'] = $usrwsid;
		$send2cb['id'] = $mauticdata['corebos_id'];
		$send2cb['from_externalsource'] = 'mautic';

		$record = vtws_update($send2cb, $current_user);
		if ($record) {
			// Reset from_externalsource
			list($contact_tabid, $contact_crmid) = explode('x', $record['id']);
			$adb->pquery('UPDATE vtiger_contactdetails SET from_externalsource=? where contactid=?', array('', $contact_crmid));
		}
	}

	public function companiesToAccounts() {
		global $adb;
		$auth = $this->authenticate();
		if ($auth) {
			$apiUrl = $this->getSettings('baseUrl');
			$api = new MauticApi();
			$companyApi = $api->newApi('companies', $auth, $apiUrl);
			$companies = $companyApi->getList();
			if (!empty($companies['companies'])) {
				foreach ($companies['companies'] as $company_id => $company) {
					$core_fields = $company['fields']['core'];
					$professional_fields = $company['fields']['professional'];
					$fields = array_merge($core_fields, $professional_fields);

					$mauticdata = array();
					foreach ($fields as $field) {
						$mauticdata[$field['alias']] = $field['value'];
					}

					if (empty($mauticdata['company_corebos_id'])) {
						if (!empty($mauticdata['accountname'])) {
							$record = $this->createCBAccount($company_id, $mauticdata);
							// Update company_corebos_id
							if ($record) {
								$updatedData = [
									'company_corebos_id' => $record['id']
								];
								$companyApi->edit($company['id'], $updatedData);
							}
						}
					} else {
						$id = explode('x', $mauticdata['company_corebos_id']);
						$acc_id = (isset($id[1]) ? $id[1] : '');
						$res = $adb->pquery('select 1 from vtiger_account where accountid=? limit 1', array($acc_id));
						if ($adb->num_rows($res) == 1) {
							$this->updateCBAccount($company_id, $mauticdata);
						}
					}
				}
			}
		}
	}

	public function createCBAccount($company_id, $mauticdata) {
		global $adb;
		$current_user = Users::getActiveAdminUser();
		$usrwsid = vtws_getEntityId('Users').'x'.$current_user->id;
		$send2cb = array();
		$bmapname = 'MauticToAccounts';
		$cbMapid = GlobalVariable::getVariable('BusinessMapping_'.$bmapname, cbMap::getMapIdByName($bmapname));
		if ($cbMapid) {
			$cbMap = cbMap::getMapByID($cbMapid);
			$send2cb = $cbMap->Mapping($mauticdata, $send2cb);
		} else {
			$send2cb['accountname'] = $mauticdata['companyname'];
		}
		$send2cb['mautic_id'] = $company_id;
		$send2cb['assigned_user_id'] = $usrwsid;
		$send2cb['from_externalsource'] = 'mautic';

		$record = vtws_create('Accounts', $send2cb, $current_user);
		if ($record) {
			// Reset from_externalsource
			list($account_tabid, $account_crmid) = explode('x', $record['id']);
			$adb->pquery('UPDATE vtiger_account SET from_externalsource=? where accountid=?', array('', $account_crmid));
			return $record;
		}
		return null;
	}

	public function updateCBAccount($company_id, $mauticdata) {
		global $adb;
		$current_user = Users::getActiveAdminUser();
		$usrwsid = vtws_getEntityId('Users').'x'.$current_user->id;
		$send2cb = array();
		$bmapname = 'MauticToAccounts';
		$cbMapid = GlobalVariable::getVariable('BusinessMapping_'.$bmapname, cbMap::getMapIdByName($bmapname));
		if ($cbMapid) {
			$cbMap = cbMap::getMapByID($cbMapid);
			$send2cb = $cbMap->Mapping($mauticdata, $send2cb);
		} else {
			$send2cb['accountname'] = $mauticdata['companyname'];
		}
		$send2cb['mautic_id'] = $company_id;
		$send2cb['assigned_user_id'] = $usrwsid;
		$send2cb['id'] = $mauticdata['company_corebos_id'];
		$send2cb['from_externalsource'] = 'mautic';

		$record = vtws_update($send2cb, $current_user);
		if ($record) {
			// Reset from_externalsource
			list($account_tabid, $account_crmid) = explode('x', $record['id']);
			$adb->pquery('UPDATE vtiger_account SET from_externalsource=? where accountid=?', array('', $account_crmid));
		}
	}
}