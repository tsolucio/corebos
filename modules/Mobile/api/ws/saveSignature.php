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

class WS_saveSignature extends crmtogo_WS_Controller {

	public function process(crmtogo_API_Request $request) {
		return $this->getContent($request);
	}

	public function getContent(crmtogo_API_Request $request) {
		include_once 'include/Webservices/Create.php';
		include_once 'include/Webservices/Query.php';
		$signature = $request->get('signature');
		$parentid = $request->get('recordid');
		$parentrecordid = vtws_getIdComponents($parentid);
		$parentrecordid = $parentrecordid[1];
		global $adb;

		if (isset($signature) && !empty($signature)) {
			//$parentmodule = crmtogo_WS_Utils::detectModulenameFromRecordId($parentid);
			$current_user = $this->getActiveUser();
			$hdresult = $adb->pquery('SELECT ticket_no FROM vtiger_troubletickets WHERE ticketid = ?', array($parentrecordid));
			$ticket_no = $adb->query_result($hdresult, 0, 'ticket_no');

			$wsfolderid = vtws_getEntityId('DocumentFolders').'x';
			date_default_timezone_set($current_user->time_zone);
			$userid = crmtogo_WS_Utils::getEntityModuleWSId('Users').'x'.$current_user->id;
			$model_filename = array(
				'name' => 'firma_'.$ticket_no.'.png',
				'size' => 0,
				'type' => 'image/png',
				'content' => $signature
			);
			$module = 'Documents';
			$valuesmap = array(
				'assigned_user_id' => $userid,
				'notes_title' => 'Firma Incidencia '.$ticket_no,
				'notecontent' => 'Firma Incidencia '.$ticket_no,
				'filename' => $model_filename,
				'filetype' => 'image/png',
				'filesize' => 0,
				'filelocationtype' => 'I',
				'filedownloadcount' => 0,
				'filestatus' => 1,
				'folderid'  => $wsfolderid.'1',
				'relations' => $parentid
			);
			//Create Document
			vtws_create($module, $valuesmap, $current_user);
			//Get signature path
			$query_docs = 'SELECT vtiger_attachments.path, vtiger_attachments.name, vtiger_attachments.attachmentsid
					FROM vtiger_attachments
					INNER JOIN vtiger_crmentity ON vtiger_attachments.attachmentsid=vtiger_crmentity.crmid
					WHERE deleted=0 AND vtiger_attachments.name = ? ORDER BY vtiger_attachments.attachmentsid DESC';
			$res_docs = $adb->pquery($query_docs, array('firma_'.$ticket_no.'.png'));
			$doc_path = '';
			if ($adb->num_rows($res_docs) > 0) {
				$doc_path = $adb->query_result($res_docs, 0, 'path') . $adb->query_result($res_docs, 0, 'attachmentsid') . '_' . $adb->query_result($res_docs, 0, 'name');
			}
			//Update HelpDesk, close ticket

			$ticket_fields = array(
				'ticketstatus' => 'Closed',
			);
			crmtogo_WS_Utils::updateRecord($parentrecordid, $ticket_fields, 'HelpDesk', $current_user);
		}
		$response = new crmtogo_API_Response();
		$response->setResult(array('signpath'=>$doc_path));
		return $response;
	}
}