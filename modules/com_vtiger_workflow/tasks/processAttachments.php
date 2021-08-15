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
unset($_FILES);
foreach ($focusrel->column_fields as $fieldname => $fieldvalue) {
	if (is_object($fieldvalue) || is_array($fieldvalue)) {
		$fieldvalue = (array) $fieldvalue;
		if (empty($fieldvalue['name']) || empty($fieldvalue['content'])) {
			continue;
		}
		$filepath = $root_directory.'cache/'.$fieldvalue['name'];
		file_put_contents($filepath, base64_decode($fieldvalue['content']));
		$fileinfo = array(
			'name' => $fieldvalue['name'],
			'type' => $fieldvalue['type'],
			'tmp_name' => $filepath,
			'error' => 0,
			'size' => $fieldvalue['size']
		);
		$wsAttachments[] = $filepath;
		if (validateImageFile($fileinfo) == 'true' && !validateImageContents($filepath)) {
			continue;
		}
		$_FILES[$fieldname] = $fileinfo;
		$focusrel->column_fields[$fieldname] = $fieldvalue['name'];
		if ($attmodule=='Documents') {
			$focusrel->column_fields['filetype'] = $fieldvalue['type'];
			$focusrel->column_fields['filesize'] = $fieldvalue['size'];
		}
	}
}
