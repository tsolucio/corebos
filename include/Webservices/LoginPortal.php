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

	function vtws_loginportal($username,$pwd){
		
		$user = new Users();
		$userId = $user->retrieve_user_id('portal');
		
		if (empty($userId)) {
			throw new WebServiceException(WebServiceErrorCode::$INVALIDUSERPWD,"User portal does not exist");
			//throw new WebServiceException(WebServiceErrorCode::$AUTHREQUIRED,'Given user cannot be found');
		}
		global $adb, $log;
		$log->debug("Entering LoginPortal function with parameter username: ".$username." password:".$pwd);
		
		$ctors = $adb->pquery("select id
			from vtiger_portalinfo
			inner join vtiger_customerdetails on vtiger_portalinfo.id=vtiger_customerdetails.customerid
			inner join vtiger_crmentity on vtiger_crmentity.crmid=vtiger_portalinfo.id
			where vtiger_crmentity.deleted=0 and user_name=? and user_password=?
			  and isactive=1 and vtiger_customerdetails.portal=1",array($username,$pwd));
		if ($ctors and $adb->num_rows($ctors)==1) {
			$crmid = $adb->query_result($ctors,0,0);
			$wsid = $vtyiicpng_getWSEntityId('Contacts').$crmid;
			$user = $user->retrieveCurrentUserInfoFromFile($userId);
			if($user->status != 'Inactive') {
				return $user;
			}
		}
		throw new WebServiceException(WebServiceErrorCode::$AUTHREQUIRED,'Given user is inactive');
	}
	
	function vtws_getActiveToken($userId){
		global $adb;
		
		$sql = "select * from vtiger_ws_userauthtoken where userid=? and expiretime >= ?";
		$result = $adb->pquery($sql,array($userId,time()));
		if($result != null && isset($result)){
			if($adb->num_rows($result)>0){
				return $adb->query_result($result,0,"token");
			}
		}
		return null;
	}
	
	function vtws_getUserAccessKey($userId){
		global $adb;
		
		$sql = "select * from vtiger_users where id=?";
		$result = $adb->pquery($sql,array($userId));
		if($result != null && isset($result)){
			if($adb->num_rows($result)>0){
				return $adb->query_result($result,0,"accesskey");
			}
		}
		return null;
	}
	
?>
