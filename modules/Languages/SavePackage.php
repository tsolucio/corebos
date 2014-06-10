<?php
/*********************************************************************************
 * $Header$
 * Description: Language Pack Wizard
 * Portions created by SugarCRM are Copyright (C) SugarCRM, Inc.
 * All Rights Reserved.
 * Contributor(s): Gaëtan KRONEISEN technique@expert-web.fr
 ********************************************************************************/

require_once('modules/Languages/Config.inc.php');
require_once('include/database/PearDatabase.php');
require_once('include/utils/utils.php');

global $log;
$db = new PearDatabase();

//Error function
function error_add($error_msg){
	header("Location:index.php?module=Languages&action=CreatePackage&parenttab=Settings&error=".$error_msg);
}
function error_edit($error_msg,$languageid){
	header("Location:index.php?module=Languages&action=EditPackage&parenttab=Settings&languageid=".$languageid."&error=".$error_msg);
}

if(isset($_REQUEST['languageid']) && $_REQUEST['languageid'] !=''){
	$sql="SELECT COUNT(languageid) AS nb FROM vtiger_languages WHERE prefix='".$_REQUEST['prefix']."' AND  languageid !='".$_REQUEST['languageid']."'";
	$rs=$adb->query($sql);
	$row=$adb->fetch_array($rs);
	if((int)$row["nb"]!=0){
		error_edit("ERROR_PREFIX_ALREADY_SET",$_REQUEST['languageid']);
	}
	else{
		$sql="SELECT COUNT(languageid) AS nb FROM vtiger_languages WHERE language='".$_REQUEST['lang']."' AND languageid !='".$_REQUEST['languageid']."'";
		$rs=$adb->query($sql);
		$row=$adb->fetch_array($rs);
		if((int)$row["nb"]!=0){
			error_edit("ERROR_LANGUAGE_ALREADY_SET",$_REQUEST['languageid']);
		}
		else{
			$sql = "update vtiger_languages set prefix = '".$_REQUEST['prefix']."',  encoding ='".$_REQUEST['encode']."', language ='".$_REQUEST['lang']."', version ='".$_REQUEST['version']."', author ='".$_REQUEST['author']."', license ='".addslashes($_REQUEST['license'])."', createddate ='".$_REQUEST['createddate']."', lockfor='".$_REQUEST['lockfor']."' where languageid =".$_REQUEST['languageid'];
			$adb->query($sql);
			$log->info("The language pack is updated");
			$log->info("about to invoke the DetailViewPackages file");
			header("Location:index.php?module=Languages&action=ListPackages&parenttab=Settings");
		}
	}
}
else{

	$sql="SELECT COUNT(languageid) AS nb FROM vtiger_languages WHERE prefix='".$_REQUEST['prefix']."'";
	$rs=$adb->query($sql);
	$row=$adb->fetch_array($rs);
	if((int)$row["nb"]!=0){
		error_add("ERROR_PREFIX_ALREADY_SET");
	}
	else{
		$sql="SELECT COUNT(languageid) AS nb FROM vtiger_languages WHERE language='".$_REQUEST['lang']."'";
		$rs=$adb->query($sql);
		$row=$adb->fetch_array($rs);
		if((int)$row["nb"]!=0){
			error_add("ERROR_LANGUAGE_ALREADY_SET");
		}
		else{
			$sql="INSERT INTO vtiger_languages  VALUES( NULL,'".$_REQUEST['prefix']."','".$_REQUEST['encode']."','".$_REQUEST['lang']."','".$_REQUEST['version']."','".$_REQUEST['author']."','".addslashes($_REQUEST['license'])."',NOW(),NOW(),".$_REQUEST['lockfor'].")";
			$adb->query($sql);
			$log->info("The language pack is added");
			$log->info("about to invoke the DetailViewPackages file");

			// add prefix to config.inc.php
			$filename='config.inc.php';
			if(file_exists($filename) && is_writable($filename)){
				$ConfigfileContent = file_get_contents($filename,FILE_TEXT);
				
				if (!preg_match("/'".$_REQUEST['prefix']."'\s*=>/",$ConfigfileContent)) {
					$add = "'".$_REQUEST['prefix']."'=>'".$_REQUEST['lang']."',";
					
					$ConfigfileContent = preg_replace('/(\$languages\s*=\s*Array\s*\(.*)\);/i', "$1".$add.");", $ConfigfileContent);
					
					if ($make_backups == true) {
						@unlink($filename.'.bak');
						@copy($filename, $filename.'.bak');
					}
					$fd = fopen($filename, 'w');
					fwrite($fd, $ConfigfileContent);
					fclose($fd);
				}
			}
			else $update_cfg = "ERROR_CONFIG_INC";

			header("Location:index.php?module=Languages&action=ListPackages&parenttab=Settings");
		}
	}
}
?>