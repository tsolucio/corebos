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
 *  Module       : coreBOS Login Synchronization Extension
 *  Version      : 1.0
 *************************************************************************************************/

require_once 'include/wsClient/WSClient.php';

class cbwsLoginSyncHandler extends VTEventHandler {

	public function handleEvent($eventName, $params) {
		global $site_URL;
		switch ($eventName) {
			case 'corebos.login':
				if (count($params)==3 && $params[2]=='webservice') {
					$syncServers = explode(',', coreBOS_Settings::getSetting('cbwsLoginSyncServers', ''));
					foreach ($syncServers as $server) {
						if (empty($server)) {
							continue;
						}
						$cbconn = new Vtiger_WSClient($server);
						$serverID = preg_replace('/[^a-zA-Z0-9_]/', '', $server);
						$pkey = coreBOS_Settings::getSetting('cbwsLoginSync'.$serverID, '');
						if (!empty($pkey) && !empty($params[0]) && !empty($params[0]->column_fields['user_name']) && !empty($params[1])) {
							$cbconn->doLoginSession(
								$params[0]->column_fields['user_name'],
								$site_URL,
								$pkey,
								$params[1]->getSessionId()
							);
						}
					}
				}
				break;
			case 'corebos.logout':
				if (count($params)==3 && $params[2]=='webservice') {
					$syncServers = explode(',', coreBOS_Settings::getSetting('cbwsLoginSyncServers', ''));
					foreach ($syncServers as $server) {
						if (empty($server)) {
							continue;
						}
						$cbconn = new Vtiger_WSClient($server);
						$cbconn->_sessionid = $params[1]->getSessionId();
						$cbconn->doLogout();
					}
				}
				break;
			default:
				return true;
				break;
		}
	}
}
?>
