<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/
include_once dirname(__FILE__) . '/models/Alert.php';

class Mobile_WS_AddComment extends Mobile_WS_Controller {
	
	function process(Mobile_API_Request $request) {
		return $this->getContent($request);
	}
	
	function getContent(Mobile_API_Request $request) {
		$comment = $request->get('comment');
		$parentid = $request->get('parentid');
		
		if (isset($comment) && !empty($comment)) {
			$parentmodule = Mobile_WS_Utils::detectModulenameFromRecordId($parentid);
			if ($parentmodule != 'HelpDesk') {
				include_once 'include/Webservices/Create.php';
				
				$current_user = $this->getActiveUser();
				$userid = Mobile_WS_Utils::getEntityModuleWSId('Users')."x".$current_user->id;
				$arr_comment = array('commentcontent' => $comment, 'related_to' => $parentid, 'creator' => $userid, 'assigned_user_id'=> $userid);
				$ele = vtws_create('ModComments', $arr_comment, $current_user);
			}
			else {
				$parentrecordid = vtws_getIdComponents($parentid);
				$parentrecordid = $parentrecordid[1];

				//there is currently no vtws service available for ticket comments
				$current_user = $this->getActiveUser();
				$current_user_id = $current_user ->id;
				$userrecordid = vtws_getIdComponents($current_user_id);
				$userrecordid = $userrecordid[1];
				$arr_comment = array('commentcontent' => $comment, 'related_to' => $parentrecordid, 'creator' => $current_user_id);
				//$ele = vtws_create('ModComments', $arr_comment, $current_user);
				$saverecord = Mobile_WS_Utils::createTicketComment($arr_comment);
				if ($saverecord == true) {
					$userid = Mobile_WS_Utils::getEntityModuleWSId('Users')."x".$current_user_id;
					$ele['commentcontent'] = $arr_comment['commentcontent'];
					$ele['creator'] = $userid;
					$ele['assigned_user_id'] = $userid;
					$ele['related_to'] = $parentid;
					$ele['id'] = '';
					$ele['createdtime'] = DateTimeField::convertToUserFormat(date('Y-m-d H:i:s'));
				}
			}
		}
		
		$response = new Mobile_API_Response();
		$ele['assigned_user_id'] = vtws_getName($ele['creator'], $current_user);
		$response->setResult(array('comment'=>$ele));
		
		return $response;
	}
}