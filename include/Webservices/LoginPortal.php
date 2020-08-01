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
include_once 'include/Webservices/Login.php';

function vtws_loginportal($username, $password, $entity = 'Contacts', $SessionManagerClass = 'SessionManager') {
	global $adb;
	$user = new Users();
	$uname = GlobalVariable::getVariable('CustomerPortal_Default_User', 'portal');
	$userId = $user->retrieve_user_id($uname);

	$current_date = date('Y-m-d');
	if (strtolower($entity)=='employee') {
		$epflds = $adb->getColumnNames('vtiger_cbemployee');
		if (!in_array('portalpasswordtype', $epflds)) {
			throw new WebServiceException(WebServiceErrorCode::$INVALIDUSERPWD, 'Necessary portal login fields are not created on employee module');
		}
		$sql = 'select id, template_language, user_password, portalpasswordtype, portalloginuser
			from vtiger_portalinfo
			inner join vtiger_cbemployee on vtiger_portalinfo.id=vtiger_cbemployee.cbemployeeid
			inner join vtiger_crmentity on vtiger_crmentity.crmid=vtiger_portalinfo.id
			where vtiger_crmentity.deleted=0 and user_name=? and isactive=1 and vtiger_cbemployee.portal=1
				and vtiger_cbemployee.support_start_date <= ? and vtiger_cbemployee.support_end_date >= ?';
	} else {
		$additionalfields = '';
		$cnflds = $adb->getColumnNames('vtiger_contactdetails');
		if (in_array('portalpasswordtype', $cnflds)) {
			$additionalfields = ', portalpasswordtype, portalloginuser';
		} else {
			if (empty($userId)) {
				throw new WebServiceException(WebServiceErrorCode::$INVALIDUSERPWD, "User $uname does not exist");
			}
			$additionalfields = ", 'md5' as portalpasswordtype, $userId as portalloginuser";
		}
		$sql = "select id, template_language, user_password $additionalfields
			from vtiger_portalinfo
			inner join vtiger_customerdetails on vtiger_portalinfo.id=vtiger_customerdetails.customerid
			inner join vtiger_contactdetails on vtiger_portalinfo.id=vtiger_contactdetails.contactid
			inner join vtiger_crmentity on vtiger_crmentity.crmid=vtiger_portalinfo.id
			where vtiger_crmentity.deleted=0 and user_name=? and isactive=1 and vtiger_customerdetails.portal=1
				and vtiger_customerdetails.support_start_date <= ? and vtiger_customerdetails.support_end_date >= ?";
	}
	$ctors = $adb->pquery($sql, array($username, $current_date, $current_date));
	if ($ctors && $adb->num_rows($ctors)==1) {
		$token = vtws_getActiveToken(-$ctors->fields['id']);
		if ($token == null) {
			throw new WebServiceException(WebServiceErrorCode::$INVALIDTOKEN, 'Specified token is invalid or expired');
		}
		$pwdtype = $ctors->fields ['portalpasswordtype'];
		$pwd = $ctors->fields ['user_password'];
		switch ($pwdtype) {
			case 'sha256':
				$accessCrypt = hash('sha256', $token.$pwd);
				break;
			case 'sha512':
				$accessCrypt = hash('sha512', $token.$pwd);
				break;
			case 'md5':
				$accessCrypt = md5($token.$pwd);
				break;
			case 'plaintext':
			default:
				$accessCrypt = $token.$pwd;
				break;
		}
		if (!hash_equals($accessCrypt, $password)) {
			throw new WebServiceException(WebServiceErrorCode::$INVALIDUSERPWD, 'Invalid username or password');
		}
		if (!empty($ctors->fields ['portalloginuser'])) {
			$userId = $ctors->fields ['portalloginuser'];
		}
		if (empty($userId)) {
			throw new WebServiceException(WebServiceErrorCode::$INVALIDUSERPWD, "User $uname does not exist");
		}
		$user = $user->retrieveCurrentUserInfoFromFile($userId);
		if ($user->status != 'Inactive') {
			$ctocmrid = $ctors->fields ['id'];
			if (gettype($SessionManagerClass)=='string') {
				$sessionManager = new $SessionManagerClass();
			} else {
				$sessionManager = $SessionManagerClass;
			}
			$sid = @$sessionManager->startSession(null, false);
			if (!$sid) {
				throw new WebServiceException(WebServiceErrorCode::$SESSIONIDINVALID, 'Could not create session');
			}
			$sessionManager->set('authenticatedUserId', $userId);
			$accessinfo = array();
			$accessinfo['sessionName'] = $sessionManager->getSessionId();
			$accessinfo['user'] = array(
				'id' => vtws_getEntityId('Users').'x'.$userId,
				'user_name' => $user->column_fields['user_name'],
				'accesskey' => $user->column_fields['accesskey'],
				'contactid' => vtws_getEntityId(getSalesEntityType($ctocmrid)).'x'.$ctocmrid,
				'language' => $ctors->fields ['template_language'],
			);
			return $accessinfo;
		} else {
			throw new WebServiceException(WebServiceErrorCode::$INVALIDUSER, 'Given user is inactive');
		}
	}
	throw new WebServiceException(WebServiceErrorCode::$AUTHREQUIRED, 'User incorrect or deactivated access.');
}
?>
