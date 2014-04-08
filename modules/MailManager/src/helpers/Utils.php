<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/

define('XML_HTMLSAX3', dirname(__FILE__) . '/../../third-party/XML/');
include_once dirname(__FILE__) . '/../../third-party/HTML.Safe.php';

class MailManager_Utils {
	function safe_html_string( $string) {
		$htmlSafe = new HTML_Safe();
		return $htmlSafe->parse($string);
	}
	
	function allowedFileExtension($filename) {
		global $upload_badext;
		$parts = explode('.', $filename);
		if (count($parts) > 1) {
			$extension = $parts[count($parts)-1];
			return (in_array(strtolower($extension), $upload_badext) === false);
		}
		return false;
	}
	
	function emitJSON($object) {
		Zend_Json::$useBuiltinEncoderDecoder = true;		
		echo Zend_Json::encode($object);
	}
}

?>