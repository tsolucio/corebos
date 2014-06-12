<?php
/*********************************************************************************
 * $Header$
 * Description: Language Pack Wizard
 * Portions created by SugarCRM are Copyright (C) SugarCRM, Inc.
 * All Rights Reserved.
 * Contributor(s): Pius Tsch�mperlin ep-t.ch
 ********************************************************************************/

require_once('modules/Languages/Config.inc.php');
require_once('include/database/PearDatabase.php');
require_once('include/utils/utils.php');
require_once('modules/Languages/inc/pclzip.lib.php');

//Error function
function error($error_msg){
	header("Location:index.php?module=Languages&action=ListPackages&parenttab=Settings&error=".$error_msg);
}

if(isset($_REQUEST['languageid']) && $_REQUEST['languageid'] !=''){
	
	//Get prefix of selected languages
	$dbQuery="SELECT * FROM vtiger_languages WHERE languageid=".$_REQUEST['languageid'];
	$result = $adb->query($dbQuery);
	$row = $adb->fetch_array($result);
	
	$filename='config.inc.php';
	if(file_exists($filename) && is_writable($filename)){
		$ConfigfileContent = file_get_contents($filename,FILE_TEXT);
		
		$ConfigfileContent = preg_replace('/(default_language\s*=\s*\')(.*)(\';)/', "$1".$row['prefix']."$3", $ConfigfileContent);
		
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