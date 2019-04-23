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

require_once 'include/Webservices/Revise.php';

/* Function used to add comments to Tickets and Faq
 * Parameters
 *   $id: webservice id of the trouble ticket or faq to which we must attach the comment
 *   $values: array with the parameters of the comment. these parameters can be:
 * 		'from_portal' 0|1: 0 = 'user',  1 = 'customer'
 * 		'parent_id' webservice id of the contact creating the comment from the portal
 * 		'comments' string, comment to add
*/
function vtws_addTicketFaqComment($id, $values, $user) {
	global $log,$adb,$current_user;

	$webserviceObject = VtigerWebserviceObject::fromId($adb, $id);
	$handlerPath = $webserviceObject->getHandlerPath();
	$handlerClass = $webserviceObject->getHandlerClass();

	require_once $handlerPath;

	$handler = new $handlerClass($webserviceObject, $user, $adb, $log);
	$meta = $handler->getMeta();
	$entityName = $meta->getObjectEntityName($id);

	if ($entityName !== 'HelpDesk' && $entityName !== 'Faq') {
		throw new WebServiceException(WebServiceErrorCode::$INVALIDID, 'Invalid module specified. Must be HelpDesk or Faq');
	}

	if ($meta->hasReadAccess()!==true) {
		throw new WebServiceException(WebServiceErrorCode::$ACCESSDENIED, 'Permission to write is denied');
	}

	if ($entityName !== $webserviceObject->getEntityName()) {
		throw new WebServiceException(WebServiceErrorCode::$INVALIDID, 'Id specified is incorrect');
	}

	if (!$meta->hasPermission(EntityMeta::$RETRIEVE, $id)) {
		throw new WebServiceException(WebServiceErrorCode::$ACCESSDENIED, 'Permission to read given object is denied');
	}

	$idComponents = vtws_getIdComponents($id);
	if (!$meta->exists($idComponents[1])) {
		throw new WebServiceException(WebServiceErrorCode::$RECORDNOTFOUND, 'Record you are trying to access is not found');
	}

	$comment = trim($values['comments']);
	if (empty($comment)) {
		throw new WebServiceException(WebServiceErrorCode::$MANDFIELDSMISSING, 'Comment empty.');
	}

	vtws_revise(array('id'=>$id, 'comments'=>$comment, 'from_portal'=>vtlib_purify($values['from_portal'])), $current_user);
	VTWS_PreserveGlobal::flush();
	return array('success'=>true);
}
?>
