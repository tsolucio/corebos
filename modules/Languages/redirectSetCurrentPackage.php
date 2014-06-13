<?php
/*********************************************************************************
 * $Header$
 * Description: Language Pack Wizard
 * Portions created by SugarCRM are Copyright (C) SugarCRM, Inc.
 * All Rights Reserved.
 * Contributor(s): Pius Tschümperlin ep-t.ch
 ********************************************************************************/

require_once('modules/Languages/Config.inc.php');

if(isset($_REQUEST['languageid']) && $_REQUEST['languageid'] !=''){
	
	//Get prefix of selected languages
	$dbQuery="SELECT * FROM vtiger_languages WHERE languageid=".$_REQUEST['languageid'];
	$result = $adb->query($dbQuery);
	$row = $adb->fetch_array($result);
	
	$current_language = $row['prefix'];
	$_SESSION['authenticated_user_language'] = $current_language;

	$filename='config.inc.php';
	if(file_exists($filename) && is_writable($filename)){
		$ConfigfileContent = file_get_contents($filename,FILE_TEXT);
		
		$ConfigfileContent = preg_replace('/(default_charset\s*=\s*\')(.*)(\';)/', "$1".$row['encoding']."$3", $ConfigfileContent);
		
		if ($make_backups == true) {
			@unlink($filename.'.bak');
			@copy($filename, $filename.'.bak');
		}
		$fd = fopen($filename, 'w');
		fwrite($fd, $ConfigfileContent);
		fclose($fd);
		header("Location:index.php?module=Languages&action=ListPackages&parenttab=Settings");
	}
	else header("Location:index.php?module=Languages&action=ListPackages&parenttab=Settings&error=ERROR_CONFIG_INC");
}
?>