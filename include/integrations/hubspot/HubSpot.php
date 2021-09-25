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
 *  Module    : Hubspot Integration
 *  Version   : 1.0
 *  Author    : JPL TSolucio, S. L.
 *************************************************************************************************/
include_once 'vtlib/Vtiger/Module.php';
require_once 'include/Webservices/Revise.php';
require_once 'include/Webservices/Create.php';
require_once 'include/integrations/hubspot/vendor/autoload.php';
use SevenShores\Hubspot\Http\Client;
use SevenShores\Hubspot\Resources\Contacts;
use SevenShores\Hubspot\Resources\Companies;
use SevenShores\Hubspot\Resources\Deals;

global $current_user, $adb, $log;
if (empty($current_user)) {
	$current_user = Users::getActiveAdminUser();
}
$adb = new PearDatabase();
$log = LoggerManager::getLogger('HubSpot');

class corebos_hubspot {

	// Configuration Properties
	private $clientId = '';
	private $oauthclientId = '';
	private $clientSecret = '';
	private $API_URL = '';
	private $accessCode = '';
	private $updateTime = '';
	private $accessToken = '';
	private $refreshToken = '';
	private $pollFrequency = 360;

	// Configuration Keys
	const KEY_ISACTIVE = 'hubspot_isactive';
	const KEY_CLIENTID = 'hubspot_clientid';
	const KEY_OAUTHCLIENTID = 'hubspot_oauthclientid';
	const KEY_CLIENTSECRET = 'hubspot_secret';
	const KEY_API_URL = 'hubspot_apiurl';
	const KEY_POLLLASTSYNC = 'hubspot_polllastsync';
	const KEY_POLLFREQUENCY = 'hubspot_pollfrequency';
	const KEY_ACCESSCODE = 'hubspot_accessCode';
	const KEY_UPDATETIME = 'hubspot_updateTime';
	const KEY_ACCESSTOKEN = 'hubspot_accessToken';
	const KEY_REFRESHTOKEN = 'hubspot_refreshToken';
	const KEY_RELATEDEALWITH = 'hubspot_relateDealWith';
	const KEY_MASTERSLAVESYNC = 'hubspot_masterslaveSync';
	const IDFIELD = 'hubspotid';
	const LASTSYNCFIELD = 'hubspotlastsync';
	const SYNCWITHFIELD = 'hubspotsyncwith';
	public static $supportedModules = array('Accounts','Contacts','Leads','Potentials');
	const HUBSPOT_APPURL = 'https://app.hubspot.com';

	// Debug
	const DEBUG = true;

	// Errors
	public static $ERROR_NONE = 0;
	public static $ERROR_NOTCONFIGURED = 1;
	public static $ERROR_NOACCESSTOKEN = 2;

	// Utilities
	private $messagequeue = null;
	private $hubspotapi = null;
	private $AccountWSID;
	private $ContactWSID;
	private $LeadWSID;
	private $PotentialWSID;
	private $UserWSID;
	private $accountMeta;
	private $contactMeta;
	private $leadMeta;
	private $potentialMeta;
	private $lastSync;

	public function __construct() {
		$this->initGlobalScope();
	}

	public function initGlobalScope() {
		global $current_user, $adb, $log;
		if (empty($current_user)) {
			$current_user = Users::getActiveAdminUser();
		}
		$adb = new PearDatabase();
		$log = LoggerManager::getLogger('HubSpot');
		$this->clientId = coreBOS_Settings::getSetting(self::KEY_CLIENTID, '');
		$this->oauthclientId = coreBOS_Settings::getSetting(self::KEY_OAUTHCLIENTID, '');
		$this->clientSecret = coreBOS_Settings::getSetting(self::KEY_CLIENTSECRET, '');
		$this->API_URL = coreBOS_Settings::getSetting(self::KEY_API_URL, '');
		$this->accessToken = coreBOS_Settings::getSetting(self::KEY_ACCESSTOKEN, '');
		if ($this->accessToken!='') {
			$this->updateTime = coreBOS_Settings::getSetting(self::KEY_UPDATETIME, '');
			if (time()>=$this->updateTime) {
				$this->refreshOAuthTokens();
				$this->accessToken = coreBOS_Settings::getSetting(self::KEY_ACCESSTOKEN, '');
			}
			$hskey = $this->accessToken;
			$oauth = true;
		} else {
			$hskey = $this->clientSecret;
			$oauth = false;
		}
		$this->pollFrequency = coreBOS_Settings::getSetting(self::KEY_POLLFREQUENCY, $this->pollFrequency);
		$this->messagequeue = coreBOS_MQTM::getInstance();
		if (!empty($hskey)) {
			$this->hubspotapi = new Client(array('key' => $hskey, 'oauth2' => $oauth));
		}
		$this->AccountWSID = vtws_getEntityId('Accounts').'x';
		$this->ContactWSID = vtws_getEntityId('Contacts').'x';
		$this->LeadWSID = vtws_getEntityId('Leads').'x';
		$this->UserWSID = vtws_getEntityId('Users').'x';
		$this->PotentialWSID = vtws_getEntityId('Potentials').'x';
	}

	public function HSChangeSync() {
		$this->initGlobalScope();
		if (!$this->isActive()) {
			return;
		}
		$cbmq = $this->messagequeue;
		while ($msg = $cbmq->getMessage('HubSpotChangeChannel', 'HSChangeSync', 'HSChangeHandler')) {
			$change = unserialize($msg['information']);
			$moduleName = $change['module'];
			if (in_array($moduleName, self::$supportedModules)) {
				switch ($moduleName) {
					case 'Accounts':
						$this->sendCompany2HubSpot($change);
						break;
					case 'Contacts':
					case 'Leads':
						$this->sendContact2HubSpot($change);
						break;
					case 'Potentials':
						$this->sendDeal2HubSpot($change);
						break;
					default:
						$cbmq->rejectMessage($msg, 'Module not supported: '.$moduleName);
				}
			}
		}
	}

	public function sendContact2HubSpot($change) {
		$send2hs = $this->getPropertiesToHubSpot($change);
		if (count($send2hs)>0) {
			$email = $this->getEmailFromEntity($change['module'], $change['record_id']);
			if ($email!='') {
				$contacts = new Contacts($this->hubspotapi);
				try {
					$rdo = $contacts->createOrUpdate($email, $send2hs);
					if (isset($rdo->status) && $rdo->status=='error') {
						$this->logMessage('sendContact2HubSpot', $rdo->message, $send2hs, $rdo);
					} else {
						// {"vid":3234574,"isNew":false}
						$this->updateControlFields($change['module'], $change['record_id'], $rdo->data->vid);
						$this->addContact2Company($change['record_id'], $rdo->data->vid);
					}
				} catch (Exception $e) {
					$this->logMessage('sendContact2HubSpot', $e->getMessage(), $send2hs, 0);
				}
			}
		}
	}

	public function addContact2Company($contactid, $contactvid) {
		global $adb;
		$rs = $adb->pquery('select vtiger_account.hubspotid
			from vtiger_account
			inner join vtiger_contactdetails on vtiger_contactdetails.accountid=vtiger_account.accountid
			where contactid=?', array($contactid));
		if ($rs && $adb->num_rows($rs)>0) {
			$accvid = $adb->query_result($rs, 0, 0);
			if (!empty($accvid)) {
				$company = new Companies($this->hubspotapi);
				try {
					$company->addContact($contactvid, $accvid);
				} catch (Exception $e) {
					$this->logMessage('addContact2Company', $e->getMessage(), array($contactvid, $accvid), 0);
				}
			}
		}
	}

	public function sendCompany2HubSpot($change) {
		$send2hs = $this->getPropertiesToHubSpot($change);
		if (count($send2hs)>0) {
			$company = new Companies($this->hubspotapi);
			if ($change['operation']=='CREATED') {
				try {
					$rdo = $company->create($send2hs);
					if (isset($rdo->status) && $rdo->status=='error') {
						$this->logMessage('sendCompany2HubSpot create', $rdo->message, $send2hs, $rdo);
					} else {
						$this->updateControlFields($change['module'], $change['record_id'], $rdo->data->companyId);
					}
				} catch (Exception $e) {
					$this->logMessage('sendCompany2HubSpot create', $e->getMessage(), $send2hs, 0);
				}
			} else {
				$vid = $this->getIDFromEntity($change['module'], $change['record_id']);
				if (!empty($vid)) {
					try {
						$rdo = $company->update($vid, $send2hs);
						if (isset($rdo->status) && $rdo->status=='error') {
							$this->logMessage('sendCompany2HubSpot update', $rdo->message, $send2hs, $rdo);
						} else {
							$this->updateControlFields($change['module'], $change['record_id'], $rdo->data->companyId);
						}
					} catch (Exception $e) {
						$this->logMessage('sendCompany2HubSpot update', $e->getMessage(), $send2hs, 0);
					}
				}
			}
		}
	}

	public function sendDeal2HubSpot($change) {
		global $adb;
		$send2hs = $props = array();
		$props= $this->getPropertiesToHubSpot($change);
		if (count($props)>0) {
			$reltors = $adb->pquery('select related_to from vtiger_potential where potentialid=?', array($change['record_id']));
			if ($reltors && $adb->num_rows($reltors)>0) {
				$relto = $adb->query_result($reltors, 0, 0);
			} else {
				$relto = '';
			}
			if ($relto!='') {
				$setypert = getSalesEntityType($relto);
				$vid = (int)$this->getIDFromEntity($setypert, $relto);
				if (!empty($vid)) {
					if ($setypert=='Contacts') {
						$send2hs['associations'] = array(
							'associatedVids' => array($vid)
						);
					} else {
						$send2hs['associations'] = array(
							'associatedCompanyIds' => array($vid)
						);
					}
				}
			}
			$send2hs['properties'] = $props;
			$deal = new Deals($this->hubspotapi);
			if ($change['operation']=='CREATED') {
				try {
					$rdo = $deal->create($send2hs);
					if (isset($rdo->status) && $rdo->status=='error') {
						$this->logMessage('sendDeal2HubSpot create', $rdo->message, $send2hs, $rdo);
					} else {
						$this->updateControlFields($change['module'], $change['record_id'], $rdo->dealId);
					}
				} catch (Exception $e) {
					$this->logMessage('sendDeal2HubSpot create', $e->getMessage(), $send2hs, 0);
				}
			} else {
				$vid = $this->getIDFromEntity($change['module'], $change['record_id']);
				try {
					$rdo = $deal->update($vid, $send2hs);
					if (isset($rdo->status) && $rdo->status=='error') {
						$this->logMessage('sendDeal2HubSpot update', $rdo->message, $send2hs, $rdo);
					} else {
						$this->updateControlFields($change['module'], $change['record_id'], $rdo->dealId);
					}
				} catch (Exception $e) {
					$this->logMessage('sendDeal2HubSpot update', $e->getMessage(), $send2hs, 0);
				}
			}
		}
	}

	public function getPropertiesToHubSpot($change) {
		$send2hs = array();
		$cbfrommodule = $change['module'];
		$bmapname = $cbfrommodule . '2HubSpot';
		$cbMapid = GlobalVariable::getVariable('BusinessMapping_'.$bmapname, cbMap::getMapIdByName($bmapname));
		if ($cbMapid) {
			$hsprops = array();
			$cbfrom = CRMEntity::getInstance($cbfrommodule);
			$cbfromid = $change['record_id'];
			$cbfrom->retrieve_entity_info($cbfromid, $cbfrommodule);
			$cbMap = cbMap::getMapByID($cbMapid);
			$hsprops = $cbMap->Mapping($cbfrom->column_fields, $hsprops);
			// mandatory
			switch ($cbfrommodule) {
				case 'Accounts':
					if (!isset($hsprops['name'])) {
						$hsprops['name'] = $cbfrom->column_fields['accountname'];
					}
					break;
				case 'Contacts':
				case 'Leads':
					if (!isset($hsprops['firstname'])) {
						$hsprops['firstname'] = $cbfrom->column_fields['firstname'];
					}
					if (!isset($hsprops['lastname'])) {
						$hsprops['lastname'] = $cbfrom->column_fields['lastname'];
					}
					if (!isset($hsprops['email'])) {
						$hsprops['email'] = $cbfrom->column_fields['email'];
					}
					break;
				case 'Potentials':
					if (!isset($hsprops['dealname'])) {
						$hsprops['name'] = $cbfrom->column_fields['potentialname'];
					}
					break;
			}
			foreach ($hsprops as $prop => $value) {
				if ($cbfrommodule=='Accounts' || $cbfrommodule=='Potentials') {
					$prop = array(
						'name' => $prop,
						'value' => $value,
					);
				} else {
					$prop = array(
						'property' => $prop,
						'value' => $value,
					);
				}
				$send2hs[] = $prop;
			}
			if (coreBOS_Settings::getSetting(self::KEY_MASTERSLAVESYNC, '')) {
				$msinfo = $cbMap->getMapArray();
				$send2hs = $this->syncMasterSlaveCB2HS($cbfrom, $send2hs, $msinfo);
			}
		}
		return $send2hs;
	}

	public function getPropertiesFromHubSpot($change) {
		global $current_user;
		$send2hs = array();
		$cbfrommodule = $change['module'];
		$bmapname = 'HubSpot2' . $cbfrommodule;
		$cbMapid = GlobalVariable::getVariable('BusinessMapping_'.$bmapname, cbMap::getMapIdByName($bmapname));
		if ($cbMapid) {
			foreach ($change['properties'] as $field => $value) {
				$send2hs[$field] = $value['value'];
			}
			$cbfromid = $change['record_id'];
			if (!empty($cbfromid)) {
				$cbfrom = CRMEntity::getInstance($cbfrommodule);
				$cbfrom->retrieve_entity_info($cbfromid, $cbfrommodule);
				$cbMap = cbMap::getMapByID($cbMapid);
				$send2hs = $cbMap->Mapping($send2hs, $cbfrom->column_fields);
				if (empty($send2hs['assigned_user_id'])) {
					$send2hs['assigned_user_id'] = getUserId($cbfromid);
				}
				if (coreBOS_Settings::getSetting(self::KEY_MASTERSLAVESYNC, '')) {
					$msinfo = $cbMap->getMapArray();
					$send2hs = $this->syncMasterSlaveHS2CB($cbfrom, $send2hs, $msinfo);
				}
			} else {
				$cbMap = cbMap::getMapByID($cbMapid);
				$send2hs = $cbMap->Mapping($send2hs, $send2hs);
				if (empty($send2hs['assigned_user_id'])) {
					$send2hs['assigned_user_id'] = $current_user->id;
				}
			}
		}
		return $send2hs;
	}

	private function syncMasterSlaveCB2HS($cbfrom, $send2hs, $msinfo) {
		return $send2hs;
	}

	private function syncMasterSlaveHS2CB($cbfrom, $send2hs, $msinfo) {
		return $send2hs;
	}

	public function getPropertyFieldNames($cbfrommodule) {
		global $adb;
		$fields = array('firstname','lastname','phone','email','lastmodifieddate'); // default fields
		$bmapname = 'HubSpot2' . $cbfrommodule;
		$cbMapid = GlobalVariable::getVariable('BusinessMapping_'.$bmapname, cbMap::getMapIdByName($bmapname));
		if ($cbMapid) {
			$cbMap = cbMap::getMapByID($cbMapid);
			$xmlcontent=html_entity_decode($cbMap->column_fields['content']);
			$xml=simplexml_load_string($xmlcontent);
			$allmergeFields = array();
			foreach ($xml->fields->field as $v) {
				foreach ($v->Orgfields->Orgfield as $value) {
					$allmergeFields[] = (string)$value->OrgfieldName;
				}
			}
			$fields = array_unique(array_merge($fields, $allmergeFields));
		}
		return $fields;
	}

	public function updateControlFields($setype, $crmid, $hubspotid) {
		global $adb;
		$endpoint = '';
		switch ($setype) {
			case 'Accounts':
				$table = 'vtiger_account';
				$idcol = 'accountid';
				$endpoint = self::HUBSPOT_APPURL.'/sales/'.$this->clientId.'/company/'.$hubspotid;
				break;
			case 'Contacts':
				$table = 'vtiger_contactdetails';
				$idcol = 'contactid';
				$endpoint = self::HUBSPOT_APPURL.'/sales/'.$this->clientId.'/contact/'.$hubspotid;
				break;
			case 'Leads':
				$table = 'vtiger_leaddetails';
				$idcol = 'leadid';
				$endpoint = self::HUBSPOT_APPURL.'/sales/'.$this->clientId.'/contact/'.$hubspotid;
				break;
			case 'Potentials':
				$table = 'vtiger_potential';
				$idcol = 'potentialid';
				$crmEntityTable = CRMEntity::getcrmEntityTableAlias($setype);
				$reltors = $adb->pquery('select related_to,setype
					from vtiger_potential
					inner join '.$crmEntityTable.' on vtiger_crmentity.crmid = related_to
					where potentialid=?', array($crmid));
				if ($reltors && $adb->num_rows($reltors)>0) {
					$relto = $adb->query_result($reltors, 0, 'related_to');
					$setypert = $adb->query_result($reltors, 0, 'setype');
					if ($setypert=='Contacts') {
						$contactvid = $this->getIDFromEntity($setypert, $relto);
						$endpoint = self::HUBSPOT_APPURL.'/sales/'.$this->clientId.'/deal/'.$hubspotid;
					}
				} else {
					$endpoint = '';
				}
				break;
		}
		$upd = 'update ' . $table ." set hubspotlastsync=?, hubspotsyncwith='1'";
		$params = array(date('Y-m-d H:i:s'));
		if (!empty($hubspotid)) {
			$upd .= ', hubspotid=?, hubspotrecord=?';
			$params[] = $hubspotid;
			$params[] = $endpoint;
		}
		$upd .= ' where ' . $idcol . '=?';
		$params[] = $crmid;
		$adb->pquery($upd, $params);
	}

	public function HSDeleteSync() {
		$this->initGlobalScope();
		if (!$this->isActive()) {
			return;
		}
		$cbmq = $this->messagequeue;
		while ($msg = $cbmq->getMessage('HubSpotChangeChannel', 'HSDeleteSync', 'HSChangeHandler')) {
			$change = unserialize($msg['information']);
			$moduleName = $change['module'];
			if (in_array($moduleName, self::$supportedModules)) {
				switch ($moduleName) {
					case 'Accounts':
						$this->deleteCompanyInHubSpot($change);
						break;
					case 'Contacts':
					case 'Leads':
						$this->deleteContactInHubSpot($change);
						break;
					case 'Potentials':
						$this->deleteDealInHubSpot($change);
						break;
					default:
						$cbmq->rejectMessage($msg, 'Module not supported: '.$moduleName);
				}
			}
		}
	}

	public function deleteCompanyInHubSpot($change) {
		$vid = $this->getIDFromEntity($change['module'], $change['record_id']);
		if (!empty($vid)) {
			$company = new Companies($this->hubspotapi);
			try {
				$company->delete($vid);
			} catch (Exception $e) {
				$this->logMessage('deleteCompanyInHubSpot', $e->getMessage(), $vid, 0);
			}
		}
	}

	public function deleteContactInHubSpot($change) {
		$vid = $this->getIDFromEntity($change['module'], $change['record_id']);
		if (!empty($vid)) {
			$contact = new Contacts($this->hubspotapi);
			try {
				$contact->delete($vid);
			} catch (Exception $e) {
				$this->logMessage('deleteContactInHubSpot', $e->getMessage(), $vid, 0);
			}
		}
	}

	public function deleteDealInHubSpot($change) {
		$vid = $this->getIDFromEntity($change['module'], $change['record_id']);
		if (!empty($vid)) {
			$deal = new Deals($this->hubspotapi);
			try {
				$deal->delete($vid);
			} catch (Exception $e) {
				$this->logMessage('deleteDealInHubSpot', $e->getMessage(), $vid, 0);
			}
		}
	}

	public function deleteRecordIncoreBOS($module, $crmid) {
		global $adb;
		$now = date('Y-m-d H:i:s', time());
		switch ($module) {
			case 'Accounts':
				$adb->pquery('update vtiger_account set hubspotsyncwith=0, hubspotdeleted=1, hubspotdeletedon=? where accountid=?', array($now,$crmid));
				break;
			case 'Contacts':
				$adb->pquery('update vtiger_contactdetails set hubspotsyncwith=0, hubspotdeleted=1, hubspotdeletedon=? where contactid=?', array($now,$crmid));
				break;
			case 'Leads':
				$adb->pquery('update vtiger_leaddetails set hubspotsyncwith=0, hubspotdeleted=1, hubspotdeletedon=? where leadid=?', array($now,$crmid));
				break;
			case 'Potentials':
				$adb->pquery('update vtiger_potential set hubspotsyncwith=0, hubspotdeleted=1, hubspotdeletedon=? where potentialid=?', array($now,$crmid));
				break;
			default:
		}
	}

	public function pollHubSpot() {
		$this->initGlobalScope();
		$msg = $this->messagequeue->getMessage('HubSpotPollChannel', 'HubSpotPoll', 'HubSpotPoll'); // consume message
		if (!$this->isActive()) {
			return;
		}
		global $adb,$current_user,$log;
		$this->lastSync = (int)coreBOS_Settings::getSetting(self::KEY_POLLLASTSYNC, time());
		$currentSync = time();
		$this->updateContacts();
		$metamodule = 'Accounts';
		$webserviceObject = VtigerWebserviceObject::fromName($adb, $metamodule);
		$handlerPath = $webserviceObject->getHandlerPath();
		$handlerClass = $webserviceObject->getHandlerClass();
		require_once $handlerPath;
		$handler = new $handlerClass($webserviceObject, $current_user, $adb, $log);
		$this->accountMeta = $handler->getMeta();
		$this->createCompanies();
		$this->updateCompanies();
		$metamodule = 'Potentials';
		$webserviceObject = VtigerWebserviceObject::fromName($adb, $metamodule);
		$handlerPath = $webserviceObject->getHandlerPath();
		$handlerClass = $webserviceObject->getHandlerClass();
		require_once $handlerPath;
		$handler = new $handlerClass($webserviceObject, $current_user, $adb, $log);
		$this->potentialMeta = $handler->getMeta();
		$this->createDeals();
		$this->updateDeals();
		coreBOS_Settings::setSetting(self::KEY_POLLLASTSYNC, $currentSync);
		$this->sendPollWakeupMessage();
	}

	public function updateContacts() {
		global $adb,$current_user,$log;
		$contact = new Contacts($this->hubspotapi);
		$props = $this->getPropertyFieldNames('Contacts');
		$response = $contact->recent(['count' => 100,'property'=>$props]);
		if (isset($response->data->contacts) && is_array($response->data->contacts) && count($response->data->contacts)>0) {
			$metamodule = 'Contacts';
			$webserviceObject = VtigerWebserviceObject::fromName($adb, $metamodule);
			$handlerPath = $webserviceObject->getHandlerPath();
			$handlerClass = $webserviceObject->getHandlerClass();
			require_once $handlerPath;
			$handler = new $handlerClass($webserviceObject, $current_user, $adb, $log);
			$this->contactMeta = $handler->getMeta();
			$metamodule = 'Leads';
			$webserviceObject = VtigerWebserviceObject::fromName($adb, $metamodule);
			$handlerPath = $webserviceObject->getHandlerPath();
			$handlerClass = $webserviceObject->getHandlerClass();
			require_once $handlerPath;
			$handler = new $handlerClass($webserviceObject, $current_user, $adb, $log);
			$this->leadMeta = $handler->getMeta();
			$done = false;
			while (!$done && ($response->data->{'has-more'} || count($response->data->contacts)>0)) {
				foreach ($response->data->contacts as $cto) {
					$cont = json_decode(json_encode($cto), true);
					$done = !$this->upsertContact($cont);
					if ($done) {
						break;
					}
				}
				$response->data->contacts = array();
				if (!$done && $response->data->{'has-more'}) {
					$response = $contact->recent(
						['count' => 100,
						'vid-offset'=>$response->data->{'vid-offset'},
						'time-offset'=>$response->data->{'time-offset'},
						'property'=>$props]
					);
				}
			}
		}
	}

	public function createCompanies() {
		$company = new Companies($this->hubspotapi);
		$response = $company->getRecentlyCreated(['count' => 100]);
		if (isset($response->data->results) && is_array($response->data->results) && count($response->data->results)>0) {
			$done = false;
			while (!$done && ($response->data->{'has-more'} || count($response->data->results)>0)) {
				foreach ($response->data->results as $cmp) {
					$comp = json_decode(json_encode($cmp), true);
					$done = !$this->createCompany($comp);
					if ($done) {
						break;
					}
				}
				if (!$done) {
					$response = $company->getRecentlyCreated(['count' => 100,'offset'=>$response->data->{'offset'}]);
				}
			}
		}
	}

	public function updateCompanies() {
		$company = new Companies($this->hubspotapi);
		$response = $company->getRecentlyModified(['count' => 100]);
		if (isset($response->data->results) && is_array($response->data->results) && count($response->data->results)>0) {
			$done = false;
			while (!$done && ($response->data->{'has-more'} || count($response->data->results)>0)) {
				foreach ($response->data->results as $cmp) {
					$comp = json_decode(json_encode($cmp), true);
					$done = !$this->updateCompany($comp);
					if ($done) {
						break;
					}
				}
				$response->data->results = array();
				if (!$done) {
					$response = $company->getRecentlyModified(['count' => 100,'offset'=>$response->data->offset]);
				}
			}
		}
	}

	public function createDeals() {
		$deal = new Deals($this->hubspotapi);
		$response = $deal->getRecentlyCreated(['count' => 100]);
		if (isset($response->data->results) && is_array($response->data->results) && count($response->data->results)>0) {
			$done = false;
			while (!$done && ($response->data->{'hasMore'} || count($response->data->results)>0)) {
				foreach ($response->data->results as $dal) {
					$dl = json_decode(json_encode($dal), true);
					$done = !$this->createDeal($dl);
					if ($done) {
						break;
					}
				}
				if (!$done) {
					$response = $deal->getRecentlyCreated(['count' => 100,'offset'=>$response->data->offset]);
				}
			}
		}
	}

	public function updateDeals() {
		$deal = new Deals($this->hubspotapi);
		$response = $deal->getRecentlyModified(['count' => 100]);
		if (isset($response->data->results) && is_array($response->data->results) && count($response->data->results)>0) {
			$done = false;
			while (!$done && ($response->data->{'hasMore'} || count($response->data->results)>0)) {
				foreach ($response->data->results as $dal) {
					$dl = json_decode(json_encode($dal), true);
					$done = !$this->updateDeal($dl);
					if ($done) {
						break;
					}
				}
				$response->data->results = array();
				if (!$done) {
					$response = $deal->getRecentlyModified(['count' => 100,'offset'=>$response->data->offset]);
				}
			}
		}
	}

	public function upsertContact($cto) {
		global $adb, $current_user;
		if ($cto['is-contact']) {
			$table = 'vtiger_contactdetails';
			$idcol = 'contactid';
			$module = 'Contacts';
			$wsid = $this->ContactWSID;
			$meta = $this->contactMeta;
		} else {
			$table = 'vtiger_leaddetails';
			$idcol = 'leadid';
			$module = 'Leads';
			$wsid = $this->LeadWSID;
			$meta = $this->leadMeta;
		}
		$rscto = $adb->pquery('select '.self::LASTSYNCFIELD.','.$idcol.','.self::SYNCWITHFIELD.' from '.$table.' where '.self::IDFIELD.'=?', array($cto['vid']));
		if ($adb->num_rows($rscto)==0) { // create new
			$cto['module'] = $module;
			$cto['record_id'] = 0;
			$wsinfo = $this->getPropertiesFromHubSpot($cto);
			if (!empty($wsinfo)) {
				$wsinfo = DataTransform::sanitizeReferences($wsinfo, $meta);
				$wsinfo['hubspotcreated'] = 1;
				$wsinfo['hubspotsyncwith'] = 1;
				try {
					coreBOS_Settings::setSetting('hubspot_pollsyncing', 'creating');
					$newcto = vtws_create($module, $wsinfo, $current_user);
					coreBOS_Settings::delSetting('hubspot_pollsyncing');
					$crmid = str_replace($wsid, '', $newcto['id']);
					$this->updateControlFields($module, $crmid, $cto['vid']);
				} catch (Exception $e) {
					$this->logMessage('upsertContact: create', $e->getMessage(), $wsinfo, 0);
				}
				return true;
			}
			return false; // error
// 		} elseif ($cto['isDeleted']) {
// 			$crmid = $adb->query_result($rscto, 0, $idcol);
// 			if (!empty($crmid)) {
// 				$this->deleteRecordIncoreBOS($module, $crmid);
// 			}
		} else {
			$SyncWith = $adb->query_result($rscto, 0, self::SYNCWITHFIELD);
			if ($SyncWith!='1') {
				return true;
			}
			$LastSync = $adb->query_result($rscto, 0, self::LASTSYNCFIELD);
			$LastSync = strtotime($LastSync);
			$lastmodifieddate = (int)(reset($cto['properties']['lastmodifieddate'])/1000);
			if ($LastSync < $lastmodifieddate) { // update
				$crmid = $adb->query_result($rscto, 0, $idcol);
				$cto['module'] = $module;
				$cto['record_id'] = $crmid;
				$wsinfo = $this->getPropertiesFromHubSpot($cto);
				if (!empty($wsinfo)) {
					$wsinfo = DataTransform::sanitizeReferences($wsinfo, $meta);
					$wsinfo['id'] = $wsid.$crmid;
					try {
						coreBOS_Settings::setSetting('hubspot_pollsyncing', $crmid);
						vtws_revise($wsinfo, $current_user);
						coreBOS_Settings::delSetting('hubspot_pollsyncing');
						$this->updateControlFields($module, $crmid, 0);
					} catch (Exception $e) {
						$this->logMessage('upsertContact: update', $e->getMessage(), $wsinfo, 0);
					}
					return true;
				}
				return false; // error
			} else {
				return false;
			}
		}
	}

	public function createCompany($cmp) {
		global $adb, $current_user;
		$rscto = $adb->pquery('select 1 from vtiger_account where '.self::IDFIELD.'=?', array($cmp['companyId']));
		if ($adb->num_rows($rscto)==0) { // create new
			$cmp['module'] = 'Accounts';
			$cmp['record_id'] = 0;
			$wsinfo = $this->getPropertiesFromHubSpot($cmp);
			if (!empty($wsinfo)) {
				$wsinfo = DataTransform::sanitizeReferences($wsinfo, $this->accountMeta);
				$wsinfo['hubspotcreated'] = 1;
				$wsinfo['hubspotsyncwith'] = 1;
				try {
					coreBOS_Settings::setSetting('hubspot_pollsyncing', 'creating');
					$newcto = vtws_create('Accounts', $wsinfo, $current_user);
					coreBOS_Settings::delSetting('hubspot_pollsyncing');
					$crmid = str_replace($this->AccountWSID, '', $newcto['id']);
					$this->updateControlFields('Accounts', $crmid, $cmp['companyId']);
				} catch (Exception $e) {
					$this->logMessage('createCompany', $e->getMessage(), $wsinfo, 0);
				}
				return true;
			}
			return false; // error
		} else {
			return false;
		}
	}

	public function updateCompany($cmp) {
		global $adb, $current_user;
		$rscto = $adb->pquery('select '.self::LASTSYNCFIELD.','.self::SYNCWITHFIELD.',accountid from vtiger_account where '.self::IDFIELD.'=?', array($cmp['companyId']));
		if ($adb->num_rows($rscto)==0) { // does not exist > we cannot update
			return false;
		} elseif ($cmp['isDeleted']) {
			$crmid = $adb->query_result($rscto, 0, 'accountid');
			if (!empty($crmid)) {
				$this->deleteRecordIncoreBOS('Accounts', $crmid);
			}
		} else {
			$SyncWith = $adb->query_result($rscto, 0, self::SYNCWITHFIELD);
			if ($SyncWith!='1') {
				return true;
			}
			$LastSync = $adb->query_result($rscto, 0, self::LASTSYNCFIELD);
			$LastSync = strtotime($LastSync);
			$lastmodifieddate = (int)(reset($cmp['properties']['hs_lastmodifieddate'])/1000);
			if ($LastSync < $lastmodifieddate) { // update
				$crmid = $adb->query_result($rscto, 0, 'accountid');
				$cmp['module'] = 'Accounts';
				$cmp['record_id'] = $crmid;
				$wsinfo = $this->getPropertiesFromHubSpot($cmp);
				if (!empty($wsinfo)) {
					$wsinfo = DataTransform::sanitizeReferences($wsinfo, $this->accountMeta);
					$wsinfo['id'] = $this->AccountWSID.$crmid;
					$wsinfo['assigned_user_id'] = $this->UserWSID.$wsinfo['assigned_user_id'];
					try {
						coreBOS_Settings::setSetting('hubspot_pollsyncing', $crmid);
						vtws_revise($wsinfo, $current_user);
						coreBOS_Settings::delSetting('hubspot_pollsyncing');
						$this->updateControlFields('Accounts', $crmid, 0);
					} catch (Exception $e) {
						$this->logMessage('updateCompany', $e->getMessage(), $wsinfo, 0);
					}
					return true;
				}
				return false; // error
			} else {
				return false;
			}
		}
	}

	public function createDeal($dal) {
		global $adb, $current_user;
		if (!isset($dal['associations'])) {
			$this->logMessage('createDeal', 'No related account or contact', $dal, 0);
			return false;
		}
		$rscto = $adb->pquery('select 1 from vtiger_potential where '.self::IDFIELD.'=?', array($dal['dealId']));
		if ($adb->num_rows($rscto)==0) { // create new
			$dal['module'] = 'Potentials';
			$dal['record_id'] = 0;
			$wsinfo = $this->getPropertiesFromHubSpot($dal);
			$relWith = coreBOS_Settings::getSetting(self::KEY_RELATEDEALWITH, 'Contacts');
			$relId = $sql = '';
			if ($relWith=='Contacts') {
				if (isset($dal['associations']['associatedVids']) && isset($dal['associations']['associatedVids'][0])) {
					$relId = $dal['associations']['associatedVids'][0];
					$sql = 'select contactid from vtiger_contactdetails where '.self::IDFIELD.'=?';
					$wsid = $this->ContactWSID;
				} elseif (isset($dal['associations']['associatedCompanyIds']) && isset($dal['associations']['associatedCompanyIds'][0])) {
					$relId = $dal['associations']['associatedCompanyIds'][0];
					$sql = 'select accountid from vtiger_account where '.self::IDFIELD.'=?';
					$wsid = $this->AccountWSID;
				}
			} else {
				if (isset($dal['associations']['associatedCompanyIds']) && isset($dal['associations']['associatedCompanyIds'][0])) {
					$relId = $dal['associations']['associatedCompanyIds'][0];
					$sql = 'select accountid from vtiger_account where '.self::IDFIELD.'=?';
					$wsid = $this->AccountWSID;
				} elseif (isset($dal['associations']['associatedVids']) && isset($dal['associations']['associatedVids'][0])) {
					$relId = $dal['associations']['associatedVids'][0];
					$sql = 'select contactid from vtiger_contactdetails where '.self::IDFIELD.'=?';
					$wsid = $this->ContactWSID;
				}
			}
			if (empty($relId)) {
				$this->logMessage('createDeal', 'No related account or contact', $dal, 0);
				return false;
			} else {
				$rscto = $adb->pquery($sql, array($relId));
				if ($rscto && $adb->num_rows($rscto)>0) {
					$relId = $wsid.$adb->query_result($rscto, 0, 0);
				} else {
					$this->logMessage('createDeal', 'No related account or contact found', $dal, 0);
				}
			}
			if (!empty($wsinfo)) {
				$wsinfo = DataTransform::sanitizeReferences($wsinfo, $this->potentialMeta);
				$wsinfo['hubspotcreated'] = 1;
				$wsinfo['hubspotsyncwith'] = 1;
				$wsinfo['related_to'] = $relId;
				try {
					coreBOS_Settings::setSetting('hubspot_pollsyncing', 'creating');
					$newcto = vtws_create('Potentials', $wsinfo, $current_user);
					coreBOS_Settings::delSetting('hubspot_pollsyncing');
					$crmid = str_replace($this->PotentialWSID, '', $newcto['id']);
					$this->updateControlFields('Potentials', $crmid, $dal['dealId']);
				} catch (Exception $e) {
					$this->logMessage('createDeal', $e->getMessage(), $wsinfo, 0);
				}
				return true;
			}
			return false; // error
		} else {
			return false;
		}
	}

	public function updateDeal($dal) {
		global $adb, $current_user;
		$rscto = $adb->pquery(
			'select '.self::LASTSYNCFIELD.','.self::SYNCWITHFIELD.',potentialid from vtiger_potential where '.self::IDFIELD.'=?',
			array($dal['dealId'])
		);
		if ($adb->num_rows($rscto)==0) { // does not exist > we cannot update
			return false;
		} elseif ($dal['isDeleted']) {
			$crmid = $adb->query_result($rscto, 0, 'potentialid');
			if (!empty($crmid)) {
				$this->deleteRecordIncoreBOS('Potentials', $crmid);
			}
		} else {
			$SyncWith = $adb->query_result($rscto, 0, self::SYNCWITHFIELD);
			if ($SyncWith!='1') {
				return true;
			}
			$LastSync = $adb->query_result($rscto, 0, self::LASTSYNCFIELD);
			$LastSync = strtotime($LastSync);
			$lastmodifieddate = (int)(reset($dal['properties']['lastmodifieddate'])/1000);
			if ($LastSync < $lastmodifieddate) { // update
				$crmid = $adb->query_result($rscto, 0, 'potentialid');
				$dal['module'] = 'Potentials';
				$dal['record_id'] = $crmid;
				$wsinfo = $this->getPropertiesFromHubSpot($dal);
				if (!empty($wsinfo)) {
					$wsinfo = DataTransform::sanitizeReferences($wsinfo, $this->potentialMeta);
					$wsinfo['id'] = $this->PotentialWSID.$crmid;
					try {
						coreBOS_Settings::setSetting('hubspot_pollsyncing', $crmid);
						vtws_revise($wsinfo, $current_user);
						coreBOS_Settings::delSetting('hubspot_pollsyncing');
						$this->updateControlFields('Potentials', $crmid, 0);
					} catch (Exception $e) {
						$this->logMessage('updateDeal', $e->getMessage(), $wsinfo, 0);
					}
					return true;
				}
				return false; // error
			} else {
				return false;
			}
		}
	}

	public function sendPollWakeupMessage() {
		$this->messagequeue->sendMessage(
			'HubSpotPollChannel',
			'HubSpotPoll',
			'HubSpotPoll',
			'Command',
			'1:M',
			0,
			floor($this->pollFrequency/2),
			$this->pollFrequency,
			0,
			'launch_poll'
		);
	}

	public function saveSettings($isactive, $clientId, $oauthclientId, $clientSecret, $API_URL, $pollFrequency, $relateDealWith, $msSync) {
		coreBOS_Settings::setSetting(self::KEY_ISACTIVE, $isactive);
		coreBOS_Settings::setSetting(self::KEY_CLIENTID, $clientId);
		coreBOS_Settings::setSetting(self::KEY_OAUTHCLIENTID, $oauthclientId);
		coreBOS_Settings::setSetting(self::KEY_CLIENTSECRET, $clientSecret);
		coreBOS_Settings::setSetting(self::KEY_API_URL, $API_URL);
		coreBOS_Settings::setSetting(self::KEY_POLLFREQUENCY, $pollFrequency);
		coreBOS_Settings::setSetting(self::KEY_RELATEDEALWITH, $relateDealWith);
		coreBOS_Settings::setSetting(self::KEY_MASTERSLAVESYNC, $msSync);
	}

	public function getSettings() {
		return array(
			'isActive' => coreBOS_Settings::getSetting(self::KEY_ISACTIVE, ''),
			'clientId' => coreBOS_Settings::getSetting(self::KEY_CLIENTID, ''),
			'oauthclientId' => coreBOS_Settings::getSetting(self::KEY_OAUTHCLIENTID, ''),
			'clientSecret' => coreBOS_Settings::getSetting(self::KEY_CLIENTSECRET, ''),
			'API_URL' => coreBOS_Settings::getSetting(self::KEY_API_URL, 'https://api.hubapi.com'),
			'pollFrequency' => coreBOS_Settings::getSetting(self::KEY_POLLFREQUENCY, $this->pollFrequency),
			'relateDealWith' => coreBOS_Settings::getSetting(self::KEY_RELATEDEALWITH, 'Contacts'),
			'masterslaveSync' => coreBOS_Settings::getSetting(self::KEY_MASTERSLAVESYNC, '0'),
		);
	}

	public function isActive() {
		$isactive = coreBOS_Settings::getSetting(self::KEY_ISACTIVE, '0');
		return ($isactive=='1');
	}

	public function registerEvents() {
		global $adb;
		$em = new VTEventsManager($adb);
		$em->registerHandler('vtiger.entity.afterdelete', 'include/integrations/hubspot/change2message.php', 'hubspotchange2message');
		$em->registerHandler('vtiger.entity.aftersave.final', 'include/integrations/hubspot/change2message.php', 'hubspotchange2message');
		$cbmq = coreBOS_MQTM::getInstance();
		$cbmq->subscribeToChannel('HubSpotChangeChannel', 'HSChangeHandler', 'HSChangeSync', array(
			'file'=>'include/integrations/hubspot/HubSpot.php',
			'class'=>'corebos_hubspot',
			'method'=>'HSChangeSync'
		));
		$cbmq->subscribeToChannel('HubSpotChangeChannel', 'HSChangeHandler', 'HSDeleteSync', array(
			'file'=>'include/integrations/hubspot/HubSpot.php',
			'class'=>'corebos_hubspot',
			'method'=>'HSDeleteSync'
		));
		$cbmq->subscribeToChannel('HubSpotPollChannel', 'HubSpotPoll', 'HubSpotPoll', array(
			'file'=>'include/integrations/hubspot/HubSpot.php',
			'class'=>'corebos_hubspot',
			'method'=>'pollHubSpot'
		));
		$this->sendPollWakeupMessage();
	}

	public function unregisterEvents() {
		global $adb;
		$em = new VTEventsManager($adb);
		$em->unregisterHandler('hubspotchange2message');
		$cbmq = coreBOS_MQTM::getInstance();
		$cbmq->unsubscribeToChannel('HubSpotChangeChannel', 'HSChangeHandler', 'HSChangeSync', array(
			'file'=>'include/integrations/hubspot/HubSpot.php',
			'class'=>'corebos_hubspot',
			'method'=>'HSChangeSync'
		));
		$cbmq->unsubscribeToChannel('HubSpotChangeChannel', 'HSChangeHandler', 'HSDeleteSync', array(
			'file'=>'include/integrations/hubspot/HubSpot.php',
			'class'=>'corebos_hubspot',
			'method'=>'HSDeleteSync'
		));
		$cbmq->unsubscribeToChannel('HubSpotPollChannel', 'HubSpotPoll', 'HubSpotPoll', array(
			'file'=>'include/integrations/hubspot/HubSpot.php',
			'class'=>'corebos_hubspot',
			'method'=>'pollHubSpot'
		));
	}

	public function activateFields() {
		global $adb;
		foreach (self::$supportedModules as $modulename) {
			$module = Vtiger_Module::getInstance($modulename);

			$blockInstance = Vtiger_Block::getInstance('LBL_HUBSPOT_INFORMATION', $module);
			if (!$blockInstance) {
				$blockInstance = new Vtiger_Block();
				$blockInstance->label = 'LBL_HUBSPOT_INFORMATION';
				$module->addBlock($blockInstance);
			}

			$field = Vtiger_Field::getInstance('hubspotid', $module);
			if ($field) {
				$adb->query('update vtiger_field set presence=2 where fieldid='.$field->id);
			} else {
				$field = new Vtiger_Field();
				$field->name = 'hubspotid';
				$field->label = 'HubSpot ID';
				$field->table = $module->basetable;
				$field->column = 'hubspotid';
				$field->columntype = 'varchar(25)';
				$field->typeofdata = 'V~O';
				$field->uitype = 1;
				$field->masseditable = '0';
				$blockInstance->addField($field);
			}
			$field = Vtiger_Field::getInstance('hubspotcreated', $module);
			if ($field) {
				$adb->query('update vtiger_field set presence=2 where fieldid='.$field->id);
			} else {
				$field = new Vtiger_Field();
				$field->name = 'hubspotcreated';
				$field->label= 'Created by HubSpot';
				$field->table = $module->basetable;
				$field->column = 'hubspotcreated';
				$field->columntype = 'varchar(3)';
				$field->uitype = 56;
				$field->displaytype = 1;
				$field->typeofdata = 'C~O';
				$field->presence = 0;
				$blockInstance->addField($field);
			}
			$field = Vtiger_Field::getInstance('hubspotrecord', $module);
			if ($field) {
				$adb->query('update vtiger_field set presence=2 where fieldid='.$field->id);
			} else {
				$field = new Vtiger_Field();
				$field->name = 'hubspotrecord';
				$field->label= 'HubSpot Record';
				$field->table = $module->basetable;
				$field->column = 'hubspotrecord';
				$field->columntype = 'varchar(250)';
				$field->uitype = 17;
				$field->displaytype = 4;
				$field->typeofdata = 'V~O';
				$field->presence = 0;
				$blockInstance->addField($field);
			}
			$field = Vtiger_Field::getInstance('hubspotdeleted', $module);
			if ($field) {
				$adb->query('update vtiger_field set presence=2 where fieldid='.$field->id);
			} else {
				$field = new Vtiger_Field();
				$field->name = 'hubspotdeleted';
				$field->label= 'Deleted in HubSpot';
				$field->table = $module->basetable;
				$field->column = 'hubspotdeleted';
				$field->columntype = 'varchar(3)';
				$field->uitype = 56;
				$field->displaytype = 1;
				$field->typeofdata = 'C~O';
				$field->presence = 0;
				$blockInstance->addField($field);
			}
			$field = Vtiger_Field::getInstance('hubspotlastsync', $module);
			if ($field) {
				$adb->query('update vtiger_field set presence=2 where fieldid='.$field->id);
			} else {
				$field = new Vtiger_Field();
				$field->name = 'hubspotlastsync';
				$field->label= 'HubSpot Last Sync';
				$field->table = $module->basetable;
				$field->column = 'hubspotlastsync';
				$field->columntype = 'DATETIME';
				$field->uitype = 70;
				$field->displaytype = 2;
				$field->typeofdata = 'DT~O';
				$field->presence = 0;
				$blockInstance->addField($field);
			}
			$field = Vtiger_Field::getInstance('hubspotdeletedon', $module);
			if ($field) {
				$adb->query('update vtiger_field set presence=2 where fieldid='.$field->id);
			} else {
				$field = new Vtiger_Field();
				$field->name = 'hubspotdeletedon';
				$field->label= 'HubSpot Deleted On';
				$field->table = $module->basetable;
				$field->column = 'hubspotdeletedon';
				$field->columntype = 'DATETIME';
				$field->uitype = 70;
				$field->displaytype = 2;
				$field->typeofdata = 'DT~O';
				$field->presence = 0;
				$blockInstance->addField($field);
			}
			$field = Vtiger_Field::getInstance('hubspotsyncwith', $module);
			if ($field) {
				$adb->query('update vtiger_field set presence=2 where fieldid='.$field->id);
			} else {
				$field = new Vtiger_Field();
				$field->name = 'hubspotsyncwith';
				$field->label= 'Sync with HubSpot';
				$field->table = $module->basetable;
				$field->column = 'hubspotsyncwith';
				$field->columntype = 'varchar(3)';
				$field->uitype = 56;
				$field->displaytype = 1;
				$field->typeofdata = 'C~O';
				$field->presence = 0;
				$blockInstance->addField($field);
			}
		}
	}

	public function deactivateFields() {
		global $adb;
		$fields = array('hubspotid','hubspotcreated','hubspotrecord','hubspotdeleted','hubspotlastsync','hubspotdeletedon', 'hubspotsyncwith');
		foreach (self::$supportedModules as $modulename) {
			$module = Vtiger_Module::getInstance($modulename);
			foreach ($fields as $fieldname) {
				$field = Vtiger_Field::getInstance($fieldname, $module);
				if ($field) {
					$adb->query('update vtiger_field set presence=1 where fieldid='.$field->id);
				}
			}
		}
	}

	public function getAPIURL() {
		return $this->API_URL;
	}

	public function getIntegrationAuthorizationURL($scope = 'contacts') {
		return self::HUBSPOT_APPURL.'/oauth/authorize?client_id='.$this->oauthclientId.'&scope='.urlencode($scope).'&redirect_uri='.urlencode(
			$this->getcoreBOSAuthorizationURL()
		);
	}

	public function getcoreBOSAuthorizationURL() {
		global $site_URL;
		return $site_URL.'/include/integrations/hubspot/saveauth.php';
	}

	public function getOAuthTokens($accessCode) {
		if (is_null($this->getAPIURL())) {
			return self::$ERROR_NOTCONFIGURED;
		}
		$fields='grant_type=authorization_code&client_id='.$this->oauthclientId.'&client_secret='.$this->clientSecret.'&redirect_uri='.$this->getcoreBOSAuthorizationURL();
		$fields.= '&code='.$accessCode;
		return $this->getAccessToken($fields);
	}

	public function refreshOAuthTokens() {
		if (is_null($this->getAPIURL())) {
			return self::$ERROR_NOTCONFIGURED;
		}
		$fields = 'grant_type=refresh_token&client_id='.$this->oauthclientId.'&client_secret='.$this->clientSecret.'&redirect_uri='.$this->getcoreBOSAuthorizationURL();
		$refreshCode = coreBOS_Settings::getSetting(self::KEY_REFRESHTOKEN, '');
		$fields.= '&refresh_token='.$refreshCode;
		return $this->getAccessToken($fields);
	}

	private function getAccessToken($fields) {
		$channel = curl_init();
		$endpoint = $this->getAPIURL().'/oauth/v1/token';
		curl_setopt($channel, CURLOPT_URL, $endpoint);
		curl_setopt($channel, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($channel, CURLOPT_POST, true);
		curl_setopt($channel, CURLOPT_POSTFIELDS, $fields);
		curl_setopt($channel, CURLOPT_CONNECTTIMEOUT, 100);
		curl_setopt($channel, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($channel, CURLOPT_TIMEOUT, 1000);
		$response = curl_exec($channel);
		$jsonResponse = json_decode($response);
		coreBOS_Settings::delSetting(self::KEY_ACCESSCODE);
		if (isset($jsonResponse->error) || (isset($jsonResponse->status) && $jsonResponse->status=='BAD_CLIENT_ID')) {
			coreBOS_Settings::delSetting(self::KEY_UPDATETIME);
			coreBOS_Settings::delSetting(self::KEY_ACCESSTOKEN);
			coreBOS_Settings::delSetting(self::KEY_REFRESHTOKEN);
			if (isset($jsonResponse->error)) {
				$information = $jsonResponse->error.': '.$jsonResponse->error_description;
			} else {
				$information = $jsonResponse->status.': '.$jsonResponse->message;
			}
			if (self::DEBUG) {
				$this->messagequeue->sendMessage('errorlog', 'hubspot', 'logmanager', 'Event', 'P:S', 0, 32000000, 0, 0, $information);
			}
			return self::$ERROR_NOACCESSTOKEN;
		} else {
			coreBOS_Settings::setSetting(self::KEY_UPDATETIME, time()+$jsonResponse->expires_in);
			coreBOS_Settings::setSetting(self::KEY_ACCESSTOKEN, $jsonResponse->access_token);
			coreBOS_Settings::setSetting(self::KEY_REFRESHTOKEN, $jsonResponse->refresh_token);
			return self::$ERROR_NONE;
		}
	}

	public function getIDFromEntity($setype, $crmid) {
		global $adb;
		$id = '';
		switch ($setype) {
			case 'Accounts':
				$rs = $adb->pquery('select '.self::IDFIELD.' from vtiger_account where accountid=?', array($crmid));
				break;
			case 'Contacts':
				$rs = $adb->pquery('select '.self::IDFIELD.' from vtiger_contactdetails where contactid=?', array($crmid));
				break;
			case 'Leads':
				$rs = $adb->pquery('select '.self::IDFIELD.' from vtiger_leaddetails where leadid=?', array($crmid));
				break;
			case 'Potentials':
				$rs = $adb->pquery('select '.self::IDFIELD.' from vtiger_potential where potentialid=?', array($crmid));
				break;
			default:
				$rs = false;
		}
		if ($rs && $adb->num_rows($rs)>0) {
			$id = $adb->query_result($rs, 0, 0);
		}
		return $id;
	}

	public function getEmailFromEntity($setype, $crmid) {
		global $adb;
		switch ($setype) {
			case 'Accounts':
				$sql = 'select email1 from vtiger_account where accountid=?';
				break;
			case 'Contacts':
				$sql = 'select email from vtiger_contactdetails where contactid=?';
				break;
			case 'Leads':
				$sql = 'select email from vtiger_leaddetails where leadid=?';
				break;
		}
		$rs = $adb->pquery($sql, array($crmid));
		if ($rs && $adb->num_rows($rs)>0) {
			$email = $adb->query_result($rs, 0, 0);
		} else {
			$email = '';
		}
		return $email;
	}

	public function logMessage($operation, $message, $data, $result) {
		if (self::DEBUG) {
			$information = array(
				'error' => '['.$operation.']: ' . $message,
				'info' => $data
			);
			if (!empty($result)) {
				$information['response'] = $result;
			}
			$information = print_r($information, true);
			$this->messagequeue->sendMessage('errorlog', 'hubspot', 'logmanager', 'Event', 'P:S', 0, 32000000, 0, 0, $information);
		}
	}
}
?>
