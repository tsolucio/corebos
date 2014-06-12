<?php
/*********************************************************************************
 * $Header$
 * Description: Language Pack Wizard
 * Portions created by SugarCRM are Copyright (C) SugarCRM, Inc.
 * All Rights Reserved.
 * Contributor(s): Gaï¿½tan KRONEISEN technique@expert-web.fr
 *                 Pius Tschï¿½mperlin ep-t.ch
 ********************************************************************************/

require_once('modules/Languages/Config.inc.php');
require_once('include/database/PearDatabase.php');
require_once('include/utils/utils.php');
require_once('modules/Languages/inc/pclzip.lib.php');

//Definition of XML tags in package definition
$tag_value=array('NAME','VERSION','CREATIONDATE','AUTHOR','PREFIX','LOCKFOR','ENCODING','LICENSE');

//Xml parser suff 
function debutElement($parser, $name, $attrs){
	global $currentTag, $currentAttribs;
	$currentTag = $name;
	$currentAttribs = $attribs; 
}

function characterData($parser, $data){
	global $currentTag,$PackageElm,$tag_value;
	if(in_array($currentTag,$tag_value)){
		$PackageElm[$currentTag].=$data;
	}
}

function finElement($parser, $name){
	global $currentTag, $currentAttribs; 
	$currentTag = "";
	$currentAttribs = "";
}

//Error function
function error($error_msg){
	header("Location:index.php?module=Languages&action=ListPackages&parenttab=Settings&error=".$error_msg);
}
//Success function
function success($success_msg){
	header("Location:index.php?module=Languages&action=ListPackages&parenttab=Settings&success=".$success_msg);
}
//Uploaded file
if (move_uploaded_file($_FILES['newpack']['tmp_name'], $tmp_dir.$_FILES['newpack']['name'])) {

	//Unzip just xml definition file
	$package = new PclZip($tmp_dir.$_FILES['newpack']['name']);
	$list = $package->extract(PCLZIP_OPT_BY_NAME,'packdata.xml',PCLZIP_OPT_EXTRACT_AS_STRING);
	if ($list == 0) {
		unlink($tmp_dir.$_FILES['newpack']['name']);
		error("ERROR_WRONG_FORMAT");
	}
	else{
	//Get XML definition
	$xml_parser = xml_parser_create();
	xml_set_element_handler($xml_parser, "debutElement", "finElement");
	xml_set_character_data_handler($xml_parser, "characterData");
	xml_parse($xml_parser, $list[0]['content'], 1);
	xml_parser_free($xml_parser);
	
	if($PackageElm['PREFIX']=='' || $PackageElm['ENCODING']=='' || $PackageElm['NAME']==''){
		unlink($tmp_dir.$_FILES['newpack']['name']);
		error("ERROR_WRONG_FORMAT");
	}
	else{
	//Add info to DB
	$sql="SELECT COUNT(languageid) AS nb FROM vtiger_languages WHERE prefix='".$PackageElm['PREFIX']."'";
	$rs=$adb->query($sql);
	$row=$adb->fetch_array($rs);
	if((int)$row["nb"]!=0){
		unlink($tmp_dir.$_FILES['newpack']['name']);
		error("ERROR_ALREADY_SET");
	}
	else{
		$sql="INSERT INTO vtiger_languages  VALUES( '','".$PackageElm['PREFIX']."','".$PackageElm['ENCODING']."','".$PackageElm['NAME']."','".$PackageElm['VERSION']."','".$PackageElm['AUTHOR']."','".addslashes($PackageElm['LICENSE'])."','".$PackageElm['CREATIONDATE']."','".$PackageElm['CREATIONDATE']."','".$PackageElm['LOCKFOR']."')";
		$adb->query($sql);
		
		// Extract Zip file and unlink it
		if ($package->extract() == 0) {
			unlink($tmp_dir.$_FILES['newpack']['name']);
			error("ERROR_WRONG_FORMAT");
		}
		else {
			// add prefix to config.inc.php
			$filename='config.inc.php';
			if(file_exists($filename) && is_writable($filename)){
				$ConfigfileContent = file_get_contents($filename,FILE_TEXT);
				
				if (!preg_match("/'".$PackageElm['PREFIX']."'\s*=>/",$ConfigfileContent)) {
					$add = "'".$PackageElm['PREFIX']."'=>'".$PackageElm['NAME']."',";
					
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
			@unlink('packdata.xml');
			
		}
		unlink($tmp_dir.$_FILES['newpack']['name']);
		#header("Location:index.php?module=Languages&action=ListPackages&parenttab=Settings");
				# We are successful in installing new language, let us send a message to user 
				# to inform the update required in config.inc.php
				success("SUCCESS&lockfor=". $PackageElm['LOCKFOR']."&error=". $update_cfg);
				return;
	}
	}
	}
}
else error("ERROR_SELECT_FILE");
?>