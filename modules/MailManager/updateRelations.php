<?php
/*+********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ********************************************************************************/
require_once 'modules/MailManager/src/controllers/DraftController.php' ;
require_once 'data/CRMEntity.php';
require_once 'include/Zend/Json.php';

$draft = new MailManager_Model_DraftEmail();

if(!empty($_REQUEST['entityid']) && !empty($_REQUEST['parentid'])) {
	global $current_user, $adb, $currentModule;
	$entityId = vtlib_purify($_REQUEST['entityid']);

	if(!MailManager::checkModuleReadAccessForCurrentUser('Documents'))  {
		$errorMessage = getTranslatedString('LBL_READ_ACCESS_FOR', $currentModule)." ".getTranslatedString('Documents')." ".getTranslatedString('LBL_MODULE_DENIED', $currentModule);
		$res = $adb->pquery("SELECT filename, filesize FROM vtiger_notes WHERE notesid = ?", array($entityId));
		$fileName = $adb->query_result($res, 0, 'filename');
		$size = $adb->query_result($res, 0, 'filesize');
		echo Zend_Json::encode(array('success'=>true, 'error'=>$errorMessage, 'name'=>$fileName, 'size'=>$size));
		exit;
	}

	$document = CRMEntity::getInstance('Documents');
	$document->retrieve_entity_info($entityId, 'Documents');
	$parentId = vtlib_purify($_REQUEST['parentid']);
	
	$draft->saveEmailDocumentRel($parentId, $entityId);

	//link the attachment to emails
	$attachRes = $adb->pquery("SELECT attachmentsid FROM vtiger_seattachmentsrel WHERE crmid = ?", array($entityId));
	$attachId = $adb->query_result($attachRes, 0, 'attachmentsid');
	$draft->saveAttachmentRel($parentId, $attachId);

	$res['name'] = $document->column_fields['filename'];
	$res['size'] = $document->column_fields['filesize'];
	$res['docid'] = $entityId;
	$res['emailid'] = $parentId;
	$response = Zend_Json::encode($res);
	echo $response;
}
?>