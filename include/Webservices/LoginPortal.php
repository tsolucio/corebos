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
		$entityType = 'E';
		$epflds = $adb->getColumnNames('vtiger_cbemployee');
		if (!in_array('portalpasswordtype', $epflds)) {
			throw new WebServiceException(WebServiceErrorCode::$INVALIDUSERPWD, 'Necessary portal login fields are not created on employee module');
		}
		$crmEntityTable = CRMEntity::getcrmEntityTableAlias('cbEmployee');
		$sql = 'select id, template_language, user_password, portalpasswordtype, portalloginuser
			from vtiger_portalinfo
			inner join vtiger_cbemployee on vtiger_portalinfo.id=vtiger_cbemployee.cbemployeeid
			inner join '.$crmEntityTable.' on vtiger_crmentity.crmid=vtiger_portalinfo.id
			where vtiger_crmentity.deleted=0 and user_name=? and isactive=1 and vtiger_cbemployee.portal=1
				and vtiger_cbemployee.support_start_date <= ? and vtiger_cbemployee.support_end_date >= ?';
	} else {
		$entityType = 'C';
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
		$crmEntityTable = CRMEntity::getcrmEntityTableAlias('Contacts');
		$sql = "select id, template_language, user_password $additionalfields
			from vtiger_portalinfo
			inner join vtiger_customerdetails on vtiger_portalinfo.id=vtiger_customerdetails.customerid
			inner join vtiger_contactdetails on vtiger_portalinfo.id=vtiger_contactdetails.contactid
			inner join ".$crmEntityTable.' on vtiger_crmentity.crmid=vtiger_portalinfo.id
			where vtiger_crmentity.deleted=0 and user_name=? and isactive=1 and vtiger_customerdetails.portal=1
				and vtiger_customerdetails.support_start_date <= ? and vtiger_customerdetails.support_end_date >= ?';
	}
	$ctors = $adb->pquery($sql, array($username, $current_date, $current_date));
	if ($ctors && $adb->num_rows($ctors)==1) {
		$token = vtws_getActiveToken(-$ctors->fields['id']);
		if ($token == null) {
			vtws_loginportalincfailed($ctors->fields['id'], $entityType);
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
				// deepcode ignore InsecureHash: backward compatibility, more secure methods are available
				$accessCrypt = md5($token.$pwd);
				break;
			case 'plaintext':
			default:
				$accessCrypt = $token.$pwd;
				break;
		}
		if (!hash_equals($accessCrypt, $password)) {
			vtws_loginportalincfailed($ctors->fields['id'], $entityType);
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
			if (vtws_loginportalgetfailed($ctors->fields['id'], $entityType)>GlobalVariable::getVariable('Application_MaxFailedLoginAttempts', 5)) {
				throw new WebServiceException(WebServiceErrorCode::$AUTHFAILURE, 'Maximum number of failed attempts reached.');
			}
			$sessionManager->set('authenticatedUserId', $userId);
			$sessionManager->set('authenticatedUserIsPortalUser', 1);
			$sessionManager->set('authenticatedUserPortalContact', $ctocmrid);
			vtws_loginportalsetfailed($ctors->fields['id'], $entityType);
			$accessinfo = array();
			$accessinfo['sessionName'] = $sessionManager->getSessionId();
			$accessinfo['user'] = array(
				'id' => vtws_getEntityId('Users').'x'.$userId,
				'user_name' => $user->column_fields['user_name'],
				//'accesskey' => $user->column_fields['accesskey'],
				'contactid' => vtws_getEntityId(getSalesEntityType($ctocmrid)).'x'.$ctocmrid,
				'language' => $ctors->fields ['template_language'],
			);
			return $accessinfo;
		} else {
			vtws_loginportalincfailed($ctors->fields['id'], $entityType);
			throw new WebServiceException(WebServiceErrorCode::$INVALIDUSER, 'Given user is inactive');
		}
	}
	if ($ctors && !empty($ctors->fields['id'])) {
		vtws_loginportalincfailed($ctors->fields['id'], $entityType);
	}
	throw new WebServiceException(WebServiceErrorCode::$AUTHREQUIRED, 'User incorrect or deactivated access.');
}

function vtws_loginportalincfailed($id, $type) {
	global $adb;
	$adb->pquery(
		'update vtiger_portalinfo set failed_login_attempts=failed_login_attempts+1 where id=? and type=?',
		array($id, $type)
	);
}
function vtws_loginportalgetfailed($id, $type) {
	global $adb;
	$fa = $adb->pquery(
		'select failed_login_attempts from vtiger_portalinfo where id=? and type=?',
		array($id, $type)
	);
	if ($fa && $adb->num_rows($fa)==1) {
		return $adb->query_result($fa, 0, 0);
	} else {
		return 10000; //big number to block access
	}
}
function vtws_loginportalsetfailed($id, $type) {
	global $adb;
	$adb->pquery(
		'update vtiger_portalinfo set failed_login_attempts=0 where id=? and type=?',
		array($id, $type)
	);
}
?>
