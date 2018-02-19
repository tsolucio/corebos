<?php
/*************************************************************************************************
 * Copyright 2012-2014 JPL TSolucio, S.L.  --  This file is a part of coreBOSCP.
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
*************************************************************************************************/
include_once 'include/Webservices/AuthToken.php';

function vtws_loginportal($username, $password) {
	$uname = 'portal';
	$user = new Users();
	$userId = $user->retrieve_user_id($uname);

	if (empty($userId)) {
		throw new WebServiceException(WebServiceErrorCode::$INVALIDUSERPWD, "User $uname does not exist");
	}
	global $adb, $log;
	$log->debug('Entering LoginPortal function with parameter username: '.$username);

	$ctors = $adb->pquery(
		'select id
		from vtiger_portalinfo
		inner join vtiger_customerdetails on vtiger_portalinfo.id=vtiger_customerdetails.customerid
		inner join vtiger_crmentity on vtiger_crmentity.crmid=vtiger_portalinfo.id
		where vtiger_crmentity.deleted=0 and user_name=? and user_password=? and isactive=1 and vtiger_customerdetails.portal=1',
		array($username, $password)
	);
	if ($ctors && $adb->num_rows($ctors)==1) {
		$user = $user->retrieveCurrentUserInfoFromFile($userId);
		if ($user->status != 'Inactive') {
			$result = $adb->query("SELECT id FROM vtiger_ws_entity WHERE name = 'Contacts'");
			$ctowsid = $adb->query_result($result, 0, 'id');
			$ctocmrid = $adb->query_result($ctors, 0, 'id');
			$result = $adb->query("SELECT id FROM vtiger_ws_entity WHERE name = 'Users'");
			$wsid = $adb->query_result($result, 0, 'id');
			$accessinfo = vtws_getchallenge($uname);
			$sessionManager = new SessionManager();
			$sid = $sessionManager->startSession(null, false);
			if (!$sid) {
				throw new WebServiceException(WebServiceErrorCode::$SESSIONIDINVALID, 'Could not create session');
			}
			$sessionManager->set('authenticatedUserId', $userId);
			$accessinfo['sessionName'] = $sessionManager->getSessionId();
			$accessinfo['user'] = array(
				'id' => $wsid.'x'.$userId,
				'user_name' => $user->column_fields['user_name'],
				'accesskey' => $user->column_fields['accesskey'],
				'contactid' => $ctowsid.'x'.$ctocmrid,
			);
			return $accessinfo;
		} else {
			throw new WebServiceException(WebServiceErrorCode::$AUTHREQUIRED, 'Given user is inactive');
		}
	}
	throw new WebServiceException(WebServiceErrorCode::$AUTHREQUIRED, 'Given contact is inactive');
}
?>
