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
//require_once('include/utils/utils.php');
require_once('modules/Languages/inc/pclzip.lib.php');
global $log,$tmp_dir;
$db = new PearDatabase();

// Get DB language pack information
$dbQuery="SELECT * FROM vtiger_languages WHERE languageid=".$_REQUEST['languageid'];
$result = $adb->query($dbQuery);
$row = $adb->fetch_array($result);

if($row['prefix']!=''){

	// Generate current language pack working dir
	if(!is_dir($tmp_dir.$row['prefix'])){
		mkdir($tmp_dir.$row['prefix']);
	}

	//Create XML Definition file
	$entete='<?xml version="1.0" encoding="UTF-8"?>'."\n"
			.'<languagepack>'."\n"
			.'<name>'.$row['language'].'</name>'."\n"
			.'<version>'.$row['version'].'</version>'."\n"
			.'<creationDate>'.$row['createddate'].'</creationDate>'."\n"
			.'<author>'.$row['author'].'</author>'."\n"
			.'<prefix>'.$row['prefix'].'</prefix>'."\n"
			.'<lockfor>'.$row['lockfor'].'</lockfor>'."\n"
			.'<encoding>'.$row['encoding'].'</encoding>'."\n"
			.'<license><![CDATA['.$row['license'].']]></license>'."\n"
			.'</languagepack>'."\n";
	$fd=fopen($tmp_dir.$row['prefix'].'/packdata.xml','w');
	fwrite($fd,$entete);
	fclose($fd);

	//Generate file list to zip
	$i=3;
	$archive[0]=$tmp_dir.$row['prefix'].'/packdata.xml';
	$archive[1]='include/language/'.$row['prefix'].'.lang.php';
	$archive[2]='include/js/'.$row['prefix'].'.lang.js';

	//Get module lang file
	if ($dh = opendir($modulesDirectory)) {
		while (($folder = readdir($dh)) !== false) {
			if(is_dir($modulesDirectory.'/'.$folder) && $folder!='..' && $folder!='.' && $folder!='.svn' && file_exists($modulesDirectory.'/'.$folder.'/language/'.$row['prefix'].'.lang.php')) {
				$archive[$i]=$modulesDirectory.'/'.$folder.'/language/'.$row['prefix'].'.lang.php';
				$i++;
			}
		} 
		closedir($dh); 
	}
	
	// Delete zip file
	$zip_filename=$tmp_dir.$row['prefix'].'/LanguagePack_'.$row['prefix'].'.zip';
	if(file_exists($zip_filename)){
		unlink($zip_filename);
	}
	//Make zip file
	$arch_file = new PclZip($zip_filename);
	$arch_file ->create(implode(',',$archive),PCLZIP_OPT_REMOVE_PATH,$tmp_dir.$row['prefix']);
	unlink($tmp_dir.$row['prefix'].'/packdata.xml');

	header("Location: modules/Languages/GetPackage.php?zip_filename=".$zip_filename);
}
?>