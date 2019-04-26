<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 * Modified by crm-now GmbH, www.crm-now.com
 ************************************************************************************/

class crmtogo_WS_AddComment extends crmtogo_WS_Controller {

	public function process(crmtogo_API_Request $request) {
		return $this->getContent($request);
	}

	public function getContent(crmtogo_API_Request $request) {
		$comment = $request->get('comment');
		$parentid = $request->get('parentid');
		if (isset($comment) && !empty($comment)) {
			$parentmodule = crmtogo_WS_Utils::detectModulenameFromRecordId($parentid);
			$current_user = $this->getActiveUser();
			date_default_timezone_set($current_user->time_zone);
			if ($parentmodule != 'HelpDesk') {
				include_once 'include/Webservices/Create.php';
				$userid = crmtogo_WS_Utils::getEntityModuleWSId('Users')."x".$current_user->id;
				$arr_comment = array('commentcontent' => $comment, 'related_to' => $parentid, 'creator' => $userid, 'assigned_user_id'=> $userid);
				$ele = vtws_create('ModComments', $arr_comment, $current_user);
				$ele['createdtime'] = DateTimeField::convertToUserFormat($ele['createdtime']);
			} else {
				$parentrecordid = vtws_getIdComponents($parentid);
				$parentrecordid = $parentrecordid[1];

				//there is currently no vtws service available for ticket comments
				$current_user_id = $current_user->id;
				$arr_comment = array('commentcontent' => $comment, 'related_to' => $parentrecordid, 'creator' => $current_user_id);
				//$ele = vtws_create('ModComments', $arr_comment, $current_user);
				$saverecord = crmtogo_WS_Utils::createTicketComment($parentrecordid, $comment, $current_user);
				$current_date_time = date('Y-m-d H:i:s');
				if ($saverecord == true) {
					$userid = crmtogo_WS_Utils::getEntityModuleWSId('Users')."x".$current_user_id;
					$ele['commentcontent'] = $arr_comment['commentcontent'];
					$ele['creator'] = $userid;
					$ele['assigned_user_id'] = $userid;
					$ele['related_to'] = $parentid;
					$ele['id'] = '';
					$ele['createdtime'] = DateTimeField::convertToUserFormat(date('Y-m-d H:i:s'));
				}
			}
		}
		$response = new crmtogo_API_Response();
		$ele['assigned_user_id'] = vtws_getName($ele['creator'], $current_user);
		$response->setResult(array('comment'=>$ele));
		return $response;
	}
}