<?php
/*************************************************************************************************
 * Copyright 2012 JPL TSolucio, S.L.  --  This file is a part of SIGPAC.
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
include_once 'include/Webservices/VtigerModuleOperation.php';
include_once 'modules/Settings/MailScanner/core/MailAttachmentMIME.php';

/**
 * Save the attachment to the database
 */
function SaveAttachmentDB($element) {
	global $adb;
	$attachid = $adb->getUniqueId('vtiger_crmentity');
	$filename = $element['name'];
	$description = $filename;
	$date_var = $adb->formatDate(date('YmdHis'), true);
	$usetime = $adb->formatDate($date_var, true);
	$userid = vtws_getIdComponents($element['assigned_user_id']);
	$userid = $userid[1];
	$setype = $element['setype'];
	$adb->pquery(
		'INSERT INTO vtiger_crmentity(crmid, smcreatorid, smownerid, modifiedby, setype, description, createdtime, modifiedtime, presence, deleted)
			VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)',
		array($attachid, $userid, $userid, $userid, $setype, $description, $usetime, $usetime, 1, 0)
	);
	SaveAttachmentFile($attachid, $filename, $element['content']);
	return $attachid;
}

/**
 * Save the attachment to the file
 */
function SaveAttachmentFile($attachid, $filename, $filecontent) {
	global $adb;

	$dirname = decideFilePath();
	if (!is_dir($dirname)) {
		mkdir($dirname);
	}

	$description = $filename;
	$filename = str_replace(' ', '_', $filename);
	$saveasfile = $dirname . $attachid . '_' . $filename;
	if (!file_exists($saveasfile)) {
		$fh = @fopen($saveasfile, 'wb');
		if (!$fh) {
			throw new WebServiceException(WebServiceErrorCode::$ACCESSDENIED, 'Permission denied, could not open file to save attachment: '.$saveasfile);
		}
		if (substr($filecontent, 0, strlen('data:image/png;base64,'))=='data:image/png;base64,') {
			// Base64 Encoded HTML5 Canvas image
			$filecontent = str_replace('data:image/png;base64,', '', $filecontent);
			$filecontent = str_replace(' ', '+', $filecontent);
		}
		fwrite($fh, base64_decode($filecontent));
		fclose($fh);
	}

	$mimetype = MailAttachmentMIME::detect($saveasfile);

	$adb->pquery(
		'INSERT INTO vtiger_attachments SET attachmentsid=?, name=?, description=?, type=?, path=?',
		array($attachid, $filename, $description, $mimetype, $dirname)
	);
}
?>
