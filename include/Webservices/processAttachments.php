<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************ */

global $root_directory;
$wsAttachments = array();
if (!empty($element['attachments'])) {
	foreach ($element['attachments'] as $fieldname => $attachment) {
		if (empty($attachment['name']) || empty($attachment['content'])) {
			continue;
		}
		$filepath = $root_directory.'cache/'.$attachment['name'];
		file_put_contents($filepath, base64_decode($attachment['content']));
		$_FILES[$fieldname] = array(
			'name' => $attachment['name'],
			'type' => $attachment['type'],
			'tmp_name' => $filepath,
			'error' => 0,
			'size' => $attachment['size']
		);
		$wsAttachments[] = $filepath;
		if (validateImageFile($_FILES[$fieldname]) == 'true' && !validateImageContents($filepath)) {
			throw new WebServiceException(WebServiceErrorCode::$VALIDATION_FAILED, getTranslatedString('LBL_IMAGESECURITY_ERROR'));
		}
	}
	unset($element['attachments']);
}
