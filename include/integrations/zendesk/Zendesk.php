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
require_once 'include/integrations/zendesk/vendor/autoload.php';

use Zendesk\API\HttpClient as ZendeskAPI;

class corebos_zendesk {
	// Configuration Properties
	private $API_URL = 'tsolucio';
	private $accessCode = 'ia7pIkZTDziNhqwOvm9ecNo75LjxHdpUS3whj0LY';
	private $username = 'joe@tsolucio.com';

	// Configuration Keys
	const KEY_ISACTIVE = 'zendesk_isactive';
	const KEY_API_URL = 'zendesk_apiurl';
	const KEY_ACCESSCODE = 'zendesk_accessCode';
	const KEY_USERNAME = 'zendesk_accessToken';

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
		$this->accessCode = coreBOS_Settings::getSetting(self::KEY_ACCESSCODE, '');
		$this->username = coreBOS_Settings::getSetting(self::KEY_USERNAME, '');
		if (!empty($this->accessCode) && !empty($this->username)) {
			$this->zendeskapi = new ZendeskAPI($this->API_URL);
			$this->zendeskapi->setAuth('basic', ['username' => $this->username, 'token' => $this->accessCode]);
		}
	}

	public function saveSettings($isactive, $API_URL, $accessCode, $username) {
		coreBOS_Settings::setSetting(self::KEY_ISACTIVE, $isactive);
		coreBOS_Settings::setSetting(self::KEY_API_URL, $API_URL);
		coreBOS_Settings::setSetting(self::KEY_ACCESSCODE, $accessCode);
		coreBOS_Settings::setSetting(self::KEY_USERNAME, $username);
	}

	public function getSettings() {
		return array(
			'isActive' => coreBOS_Settings::getSetting(self::KEY_ISACTIVE, ''),
			'API_URL' => coreBOS_Settings::getSetting(self::KEY_API_URL, ''),
			'accessCode' => coreBOS_Settings::getSetting(self::KEY_ACCESSCODE, ''),
			'username' => coreBOS_Settings::getSetting(self::KEY_USERNAME, ''),
		);
	}

	public function isActive() {
		$isactive = coreBOS_Settings::getSetting(self::KEY_ISACTIVE, '0');
		return ($isactive=='1');
	}

	public function searchTickets($where) {
		$where = str_ireplace(array(' and ', ' or '), '', $where);
		$where = preg_replace('/\s+id\s*=\s*/', '', $where);
		$where = trim($where, ' ;');
		$where = preg_replace('/\s*=\s*/', ':', $where);
		$results = $this->zendeskapi->search()->find($where);
		$output = array();
		foreach ($results->results as $ticket) {
			$newrow = array();
			foreach ((array)$ticket as $field => $value) {
				if (is_array($value)) {
					$newrow[$field] = implode(',', $value);
				} elseif (is_object($value)) {
					$newrow[$field] = json_encode($value);
				} else {
					$newrow[$field] = $value;
				}
			}
			$output[] = $newrow;
		}
		return $output;
	}

	public function getTicketsRaw() {
		return $this->zendeskapi->tickets()->sideload(['users', 'groups','organizations'])->findAll();
	}

	public function getTickets() {
		$tickets = $this->getTicketsRaw();
		$users = $this->getIdNameFromArray($tickets->users);
		$groups = $this->getIdNameFromArray($tickets->groups);
		$orgs = $this->getIdNameFromArray($tickets->organizations);
		// $next = $tickets->next_page;
		// $prev = $tickets->previous_page;
		// $count = $tickets->count;
		$output = array();
		foreach ($tickets->tickets as $ticket) {
			$newrow = array();
			foreach ((array)$ticket as $field => $value) {
				if (is_array($value)) {
					$newrow[$field] = implode(',', $value);
				} elseif (is_object($value)) {
					$newrow[$field] = json_encode($value);
				} else {
					$newrow[$field] = $value;
				}
				if ($field=='requester_id') {
					$newrow['requester_id_name'] = $users[$newrow[$field]];
				}
				if ($field=='submitter_id') {
					$newrow['submitter_id_name'] = $users[$newrow[$field]];
				}
				if ($field=='assignee_id') {
					$newrow['assignee_id_name'] = isset($users[$newrow[$field]]) ? $users[$newrow[$field]] : $groups[$newrow[$field]];
				}
				if ($field=='organization_id') {
					$newrow['organization_id_name'] = isset($orgs[$newrow[$field]]) ? $orgs[$newrow[$field]] : '';
				}
				if ($field=='group_id') {
					$newrow['group_id_name'] = $groups[$newrow[$field]];
				}
			}
			$output[] = $newrow;
		}
		return $output;
	}

	public static function getIdNameFromArray($entities) {
		$ent = array();
		foreach ($entities as $entity) {
			$ent[$entity->id] = $entity->name;
		}
		return $ent;
	}
}
?>