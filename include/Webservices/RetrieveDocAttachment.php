<?php
/*************************************************************************************************
 * Copyright 2012-2014 JPL TSolucio, S.L.  --  This file is a part of coreBOS.
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

function vtws_retrievedocattachment($all_ids, $returnfile, $user) {
	global $log, $adb;
	$entities = array();
	$docWSId = vtws_getEntityId('Documents').'x';
	$log->debug('Entering function vtws_retrievedocattachment');
	$all_ids='('.str_replace($docWSId, '', $all_ids).')';
	$query = "SELECT n.notesid, n.filename, n.filelocationtype, n.filetype
		FROM vtiger_notes n
		INNER JOIN vtiger_crmentity c ON c.crmid=n.notesid
		WHERE n.notesid in $all_ids and n.filelocationtype in ('I','E') and c.deleted=0";
	$result = $adb->query($query);
	$nr=$adb->num_rows($result);
	for ($i=0; $i<$nr; $i++) {
		$id=$docWSId.$adb->query_result($result, $i, 'notesid');
		$webserviceObject = VtigerWebserviceObject::fromId($adb, $id);
		$handlerPath = $webserviceObject->getHandlerPath();
		$handlerClass = $webserviceObject->getHandlerClass();

		require_once $handlerPath;

		$handler = new $handlerClass($webserviceObject, $user, $adb, $log);
		$meta = $handler->getMeta();
		$entityName = $meta->getObjectEntityName($id);
		$types = vtws_listtypes(null, $user);
		if (!in_array($entityName, $types['types'])) {
			throw new WebServiceException(WebServiceErrorCode::$ACCESSDENIED, 'Permission to perform the operation is denied');
		}
		if ($meta->hasReadAccess()!==true) {
			throw new WebServiceException(WebServiceErrorCode::$ACCESSDENIED, 'Permission to write is denied');
		}

		if ($entityName !== $webserviceObject->getEntityName()) {
			throw new WebServiceException(WebServiceErrorCode::$INVALIDID, 'Id specified is incorrect');
		}

		if (!$meta->hasPermission(EntityMeta::$RETRIEVE, $id)) {
			throw new WebServiceException(WebServiceErrorCode::$ACCESSDENIED, "Permission to read given object ($id) is denied");
		}

		$ids = vtws_getIdComponents($id);
		if (!$meta->exists($ids[1])) {
			throw new WebServiceException(WebServiceErrorCode::$RECORDNOTFOUND, 'Document Record you are trying to access is not found');
		}

		$document_id = $ids[1];
		$filetype=$adb->query_result($result, $i, 'filelocationtype');
		if ($filetype=='E') {
			$entity['recordid'] = $adb->query_result($result, $i, 'notesid');
			$entity['filetype'] = $adb->query_result($result, $i, 'filetype');
			$entity['filename'] = $adb->query_result($result, $i, 'filename');
			$entity['filesize'] = 0;
			$entity['attachment'] = base64_encode('') ;
		} elseif ($filetype=='I') {
			$entity = vtws_retrievedocattachment_get_attachment($document_id, true, $returnfile);
		}
		$entities[$id]=$entity;
		VTWS_PreserveGlobal::flush();
	} // end for ids
	$log->debug('Leaving function vtws_retrievedocattachment');
	return $entities;
}

function vtws_retrievedocattachment_get_attachment($fileid, $nr = false, $returnfile = true) {
	global $adb, $log, $default_charset;
	$log->debug("Entering function vtws_retrievedocattachment_get_attachment($fileid)");

	$recordpdf=array();

	$query = 'SELECT vtiger_attachments.attachmentsid,path,filename,filesize,filetype,name FROM vtiger_attachments
	INNER JOIN vtiger_seattachmentsrel ON vtiger_seattachmentsrel.attachmentsid = vtiger_attachments.attachmentsid
	INNER JOIN vtiger_notes ON vtiger_notes.notesid = vtiger_seattachmentsrel.crmid
	WHERE vtiger_notes.notesid = ?';
	$result = $adb->pquery($query, array($fileid));
	if ($adb->num_rows($result)==0 && $nr==false) {
		throw new WebServiceException(WebServiceErrorCode::$RECORDNOTFOUND, "Attachment Record you are trying to access is not found ($fileid)");
	}
	if ($adb->num_rows($result) == 1) {
		$fileType = @$adb->query_result($result, 0, 'filetype');
		$name = @$adb->query_result($result, 0, 'filename');
		$name = html_entity_decode($name, ENT_QUOTES, $default_charset);
		$filepath = $adb->query_result($result, 0, 'path');
		$attachid = $adb->query_result($result, 0, 'attachmentsid');

		$saved_filename = $attachid.'_'.$name;
		if (!file_exists($filepath.$saved_filename)) {
			$saved_filename = $attachid.'_'.@html_entity_decode($adb->query_result($result, 0, 'name'), ENT_QUOTES, $default_charset);
		}
		$fileContent = '';
		$filesize = filesize($filepath.$saved_filename);
		if (!fopen($filepath.$saved_filename, "r")) {
			$log->debug('Unable to open file');
			throw new WebServiceException(WebServiceErrorCode::$ACCESSDENIED, "Unable to open file $saved_filename. Object is denied");
		} else {
			$fileContent = $returnfile ? fread(fopen($filepath.$saved_filename, "r"), $filesize) : '';
		}
		if ($fileContent != '') {
			$log->debug('About to update download count');
			$rsn = $adb->pquery('select filedownloadcount from vtiger_notes where notesid= ?', array($fileid));
			$download_count = $adb->query_result($rsn, 0, 'filedownloadcount') + 1;
			$adb->pquery('update vtiger_notes set filedownloadcount= ? where notesid= ?', array($download_count, $fileid));
		}
		$recordpdf['recordid'] = $fileid;
		$recordpdf['filetype'] = $fileType;
		$recordpdf['filename'] = $name;
		$recordpdf['filesize'] = $filesize;
		$recordpdf['attachment'] = base64_encode($fileContent);
	}

	$log->debug("Leaving function vtws_retrievedocattachment_get_attachment($fileid)");
	return $recordpdf;
}
?>