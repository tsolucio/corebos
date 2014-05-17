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
*************************************************************************************************
*  Author       : JPL TSolucio, S. L.
*************************************************************************************************/

require_once 'modules/Emails/mail.php';

/* Function used to add comments to Tickets and Faq
 * Parameters
 *   $id: webservice id of the trouble ticket or faq to which we must attach the comment
 *   $values: array with the parameters of the comment. these parameters can be:
 * 		'from_portal' 0|1: 0 = 'user',  1 = 'customer'
 * 		'parent_id' webservice id of the contact creating the comment from the portal
 * 		'comments' string, comment to add
*/
	function vtws_addTicketFaqComment($id, $values, $user){
		
		global $log,$adb,$current_user;
		
		$webserviceObject = VtigerWebserviceObject::fromId($adb,$id);
		$handlerPath = $webserviceObject->getHandlerPath();
		$handlerClass = $webserviceObject->getHandlerClass();

		require_once $handlerPath;
		
		$handler = new $handlerClass($webserviceObject,$user,$adb,$log);
		$meta = $handler->getMeta();
		$entityName = $meta->getObjectEntityName($id);

		if($entityName !== 'HelpDesk' and $entityName !== 'Faq'){
			throw new WebServiceException(WebServiceErrorCode::$INVALIDID,"Invalid module specified. Must be HelpDesk or Faq");
		}

		if($meta->hasReadAccess()!==true){
			throw new WebServiceException(WebServiceErrorCode::$ACCESSDENIED,"Permission to write is denied");
		}

		if($entityName !== $webserviceObject->getEntityName()){
			throw new WebServiceException(WebServiceErrorCode::$INVALIDID,"Id specified is incorrect");
		}
		
		if(!$meta->hasPermission(EntityMeta::$RETRIEVE,$id)){
			throw new WebServiceException(WebServiceErrorCode::$ACCESSDENIED,"Permission to read given object is denied");
		}
		
		$idComponents = vtws_getIdComponents($id);
		if(!$meta->exists($idComponents[1])){
			throw new WebServiceException(WebServiceErrorCode::$RECORDNOTFOUND,"Record you are trying to access is not found");
		}

		$comment = trim($values['comments']);
		if (empty($comment)) {
			throw new WebServiceException(WebServiceErrorCode::$MANDFIELDSMISSING,"Comment empty.");
		}

		$current_time = $adb->formatDate(date('Y-m-d H:i:s'), true);
		if($entityName == 'HelpDesk'){
			if ($values['from_portal'] != 1) {
				$ownertype = 'user';
				if (!empty($user))
					$ownerId = $user->id;
				elseif (!empty($current_user))
					$ownerId = $current_user->id;
				else
					$ownerId = 1;
				//get the user email
				$result = $adb->pquery("SELECT email1 FROM vtiger_users WHERE id=?", array($ownerId));
				$fromname = getUserFullName($ownerId);
			} else {
				$ownertype = 'customer';
				$webserviceObject = VtigerWebserviceObject::fromId($adb,$values['parent_id']);
				$handlerPath = $webserviceObject->getHandlerPath();
				$handlerClass = $webserviceObject->getHandlerClass();
				
				require_once $handlerPath;
				
				$handler = new $handlerClass($webserviceObject,$user,$adb,$log);
				$meta = $handler->getMeta();
				$entityName = $meta->getObjectEntityName($values['parent_id']);
				
				if($entityName !== 'Contacts'){
					throw new WebServiceException(WebServiceErrorCode::$INVALIDID,"Invalid owner module specified. Must be Contacts");
				}
				
				if($entityName !== $webserviceObject->getEntityName()){
					throw new WebServiceException(WebServiceErrorCode::$INVALIDID,"Id specified is incorrect");
				}
				
				$pidComponents = vtws_getIdComponents($values['parent_id']);
				if(!$meta->exists($pidComponents[1])){
					throw new WebServiceException(WebServiceErrorCode::$RECORDNOTFOUND,"Record you are trying to access is not found");
				}
				$ownerId = $pidComponents[1];
				//get the contact email id who creates the ticket from portal and use this email as from email id in email
				$result = $adb->pquery("SELECT email FROM vtiger_contactdetails WHERE contactid=?", array($ownerId));
				$ename = getEntityName('Contacts', $ownerId);
				$fromname = $ename[$ownerId];
			}
			$sql = "insert into vtiger_ticketcomments values(?,?,?,?,?,?)";
			$params = array('', $idComponents[1], $comment, $ownerId, $ownertype, $current_time);
			//send mail to the assigned to user when customer add comment
			$toresult = $adb->pquery("SELECT email1,first_name
					FROM vtiger_users
					INNER JOIN vtiger_crmentity on smownerid=id
					INNER JOIN vtiger_troubletickets on ticketid=crmid
					WHERE ticketid=?", array($idComponents[1]));
			$to_email = $adb->query_result($toresult,0,0);
			$ownerName = $adb->query_result($toresult,0,1);
			$moduleName = 'HelpDesk';
			$subject = getTranslatedString('LBL_RESPONDTO_TICKETID', $moduleName)."##".$idComponents[1]."##". getTranslatedString('LBL_CUSTOMER_PORTAL', $moduleName);
			$contents = getTranslatedString('Dear', $moduleName)." ".$ownerName.","."<br><br>"
					.getTranslatedString('LBL_CUSTOMER_COMMENTS', $moduleName)."<br><br>
					<b>".$comment."</b><br><br>"
					.getTranslatedString('LBL_RESPOND', $moduleName)."<br><br>"
					.getTranslatedString('LBL_REGARDS', $moduleName)."<br>"
					.getTranslatedString('LBL_SUPPORT_ADMIN', $moduleName);
			$from_email = $adb->query_result($result,0,0);
			//send mail to assigned to user
			$mail_status = send_mail('HelpDesk',$to_email,$fromname,$from_email,$subject,$contents);
		} else {
			$sql = "insert into vtiger_faqcomments values(?, ?, ?, ?)";
			$params = array('', $idComponents[1], $comment, $current_time);
		}
		$adb->pquery($sql, $params);
		VTWS_PreserveGlobal::flush();
		return array('success'=>true);
	}
?>
