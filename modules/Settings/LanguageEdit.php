<?php
/*************************************************************************************************
 * Copyright 2014 JPL TSolucio, S.L. -- This file is a part of TSOLUCIO coreBOS Customizations.
 * Licensed under the vtiger CRM Public License Version 1.1 (the "License"); you may not use this
 * file except in compliance with the License. You can redistribute it and/or modify it
 * under the terms of the License. JPL TSolucio, S.L. reserves all rights not expressly
 * granted by the License. coreBOS distributed by JPL TSolucio S.L. is distributed in
 * the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied
 * warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. Unless required by
 * applicable law or agreed to in writing, software distributed under the License is
 * distributed on an "AS IS" BASIS, WITHOUT ANY WARRANTIES OR CONDITIONS OF ANY KIND,
 * either express or implied. See the License for the specific language governing
 * permissions and limitations under the License. You may obtain a copy of the License
 * at <http://corebos.org/documentation/doku.php?id=en:devel:vpl11>
 *************************************************************************************************
 *  Extension    : Language Editor
 *  Version      : 5.5.0
 *  Author       : Opencubed
 * the code is based on the work of Gaëtan KRONEISEN technique@expert-web.fr and  Pius Tschümperlin ep-t.ch
 *************************************************************************************************/
require_once 'Smarty_setup.php';
require_once 'include/CustomFieldUtil.php';
//General Directory structure information
$modulesDirectory='modules';
//Make backups when changing filecontents
$make_backups =false;

global $mod_strings,$app_strings,$current_language,$theme,$default_language,$adb;
$smarty=new vtigerCRM_Smarty;
$smarty->assign('UMOD', $mod_strings);
$smod_strings = return_module_language($current_language, 'Settings');
$smarty->assign('MOD', $smod_strings);
$smarty->assign('MODULE', 'Settings');
$smarty->assign('IMAGE_PATH', $image_path);
$smarty->assign('PARENTTAB', $_REQUEST['parenttab']);
$smarty->assign('APP', $app_strings);
$theme_path='themes/'.$theme.'/';
$image_path=$theme_path.'images/';
$smarty->assign('IMAGE_PATH', $image_path);
$langid = vtlib_purify($_REQUEST['languageid']);
$pmodule = isset($_REQUEST['pick_module']) ? vtlib_purify($_REQUEST['pick_module']) : 'General';
$smarty->assign('LANGUAGEID', $langid);
$smarty->assign('MODULE', ($pmodule!='')?$pmodule:'General');

//REF_LANGUAGE
$ref_language = $default_language;
$dbQuery = 'SELECT prefix,label FROM vtiger_language WHERE prefix=?';
$result = $adb->pquery($dbQuery, array($ref_language));
$row = $adb->fetch_array($result);
$ref_encoding = 'UTF-8';
$smarty->assign('REF_LANGUAGE', $row['label']);

//Get languguage info
$dbQuery='SELECT prefix,label FROM vtiger_language WHERE id=?';
$result = $adb->pquery($dbQuery, array($langid));
$row = $adb->fetch_array($result);
$trans_encoding = 'UTF-8';
$smarty->assign('LANGUAGE', $row['label']);
$module_array=array('General'=> $mod_strings['General']);
$module_array['JavaScript']= $mod_strings['JavaScript'];

//Get Modules and languages files
if ($dh = opendir($modulesDirectory)) {
	while (($folder = readdir($dh)) !== false) {
		if (is_dir($modulesDirectory.'/'.$folder)&&$folder!='..'&&$folder!='.'&&file_exists($modulesDirectory.'/'.$folder.'/language/'.$ref_language.'.lang.php')) {
			if (!empty($app_strings[$folder])) {
				$module_array[$folder]=$app_strings[$folder];
			} elseif (!empty($mod_strings[$folder])) {
				$module_array[$folder]=$mod_strings[$folder];
			} else {
				$module_array[$folder]=ucfirst($folder);
			}
		}
	}
	closedir($dh);
}
asort($module_array);
$smarty->assign('MODULES', $module_array);
$tr_list = array();
//General language strings
if ($pmodule=='' || $pmodule=='General') {
	//Get Reference Strings
	include 'include/language/'.$ref_language.'.lang.php';
	$ref_app_strings = $app_strings;

	//Get translated Strings
	if (!file_exists('include/language/'.$row['prefix'].'.lang.php')) {
		$handle=fopen('include/language/'.$row['prefix'].'.lang.php', 'w');
		fclose($handle);
	}
	$error=(is_writable('include/language/'.$row['prefix'].'.lang.php'))?'':$mod_strings['ERROR_GENERAL_FILE_WRITE'];
	include 'include/language/'.$row['prefix'].'.lang.php';

	//Merge the two languages stings in array and make some poor stats :)
	$total_strings=0;
	$translated_string=0;
	foreach ($ref_app_strings as $key => $tr_string) {
		$result2[$key][0]=htmlentities($tr_string, ENT_QUOTES, $ref_encoding);
		$result2[$key][1]=htmlentities((isset($app_strings[$key]) ? $app_strings[$key] : ''), ENT_QUOTES, $trans_encoding);
		$result2[$key][2]=($key=='')?'#empty#':$key;
		$result2[$key][3]='not_translated';
		if (!isset($app_strings[$key])) {
			$result2[$key][3]='new';
		} elseif ($tr_string!=$app_strings[$key]) {
			$result2[$key][3]='translated';
			$translated_string++;
		}
		$total_strings++;
	}
} //JavaScript strings
elseif ($pmodule=='JavaScript') {
	//js to php
	$patterns[0] = '/var\s*alert_arr\s*=\s*{/';
	$patterns[1] = '/(\S*)(\s*)(:)(\s*.*,?)/';
	$patterns[2] = '/(".*"|\'.*\')\s*\+/';
	$patterns[3] = '/};/';
	$replacements[0] = '$app_strings = array(';
	$replacements[1] = "$1 => $4";
	$replacements[2] = "$1.";
	$replacements[3] = ');';

	//Get Default Strings
	$filename='include/js/'.$ref_language.'.lang.js';
	if (file_exists($filename)) {
		$jsfileContent = file_get_contents($filename, FILE_TEXT);
		$jsfileContent = preg_replace($patterns, $replacements, $jsfileContent);
		eval($jsfileContent);
		$ref_app_strings = $app_strings;
	}

	//Get your languague strings
	$filename='include/js/'.$row['prefix'].'.lang.js';
	$error=(is_writable($filename))?'':$mod_strings['ERROR_GENERAL_FILE_WRITE'];
	if (!file_exists($filename)) {
		$handle=fopen($filename, 'w');
		fclose($handle);
	}
	if (file_exists($filename)) {
		$jsfileContent = file_get_contents($filename, FILE_TEXT);
		$jsfileContent = preg_replace($patterns, $replacements, $jsfileContent);
		eval($jsfileContent);
	}

	//Merge the two languages stings in array and make some pour stats :)
	$total_strings=0;
	$translated_string=0;
	foreach ($ref_app_strings as $key => $tr_string) {
		$result2[$key][0]=htmlentities($tr_string, ENT_QUOTES, $ref_encoding);
		$result2[$key][1]=htmlentities($app_strings[$key], ENT_QUOTES, $trans_encoding);
		$result2[$key][2]=($key=='')?'#empty#':$key;
		$result2[$key][3]='not_translated';
		if (!isset($app_strings[$key])) {
			$result2[$key][3]='new';
		} elseif ($tr_string!=$app_strings[$key]) {
			$result2[$key][3]='translated';
			$translated_string++;
		}
		$total_strings++;
	}
} //Modules language strings
else {
	$error_msg=$mod_strings['ERROR_MODULE_FILE_WRITE'];
	//Get Default Strings
	include 'modules/'.$pmodule.'/language/'.$ref_language.'.lang.php';
	$ref_mod_strings = $mod_strings;

	//Get your languague strings
	if (!file_exists('modules/'.$pmodule.'/language/'.$row['prefix'].'.lang.php')) {
		$handle=fopen('modules/'.$pmodule.'/language/'.$row['prefix'].'.lang.php', 'w');
		fclose($handle);
	}
	$error=(is_writable('modules/'.$pmodule.'/language/'.$row['prefix'].'.lang.php'))?'':$error_msg;
	include 'modules/'.$pmodule.'/language/'.$row['prefix'].'.lang.php';

	$tabid=getTabid($pmodule);
	$query=$adb->pquery('Select fieldlabel from vtiger_field where tabid=?', array($tabid));
	$nrfields=$adb->num_rows($query);
	$queryRelatedList=$adb->pquery('Select label from vtiger_relatedlists where tabid=?', array($tabid));
	$nrofrelations=$adb->num_rows($queryRelatedList);
	//Merge the two languages strings in array and make some poor stats :)
	$total_strings=0;
	$translated_string=0;
	foreach ($ref_mod_strings as $key => $tr_string) {
		if (is_array($tr_string)) {
			foreach ($tr_string as $skey => $str_string) {
				$tr_list[$key][$skey][0]=htmlentities($str_string, ENT_QUOTES, $ref_encoding);
				$tr_list[$key][$skey][1]=htmlentities($mod_strings[$key][$skey], ENT_QUOTES, $trans_encoding);
				$tr_list[$key][$skey][2]=($skey=='')?'#empty#':$skey;
				$tr_list[$key][$skey][3]='not_translated';
				if (!isset($mod_strings[$key][$skey])) {
					$tr_list[$key][$skey][3]='new';
				} elseif ($str_string!=$mod_strings[$key][$skey]) {
					$tr_list[$key][$skey][3]='translated';
					$translated_string++;
				}
				$total_strings++;
			}
		} else {
			$result2[$key][0]=htmlentities($tr_string, ENT_QUOTES, $ref_encoding);
			$result2[$key][1]=htmlentities($mod_strings[$key], ENT_QUOTES, $trans_encoding);
			$result2[$key][2]=($key=='')?'#empty#':$key;
			$result2[$key][3]='not_translated';
			if (!isset($mod_strings[$key])) {
				$result2[$key][3]='new';
			} elseif ($tr_string!=$mod_strings[$key]) {
				$result2[$key][3]='translated';
				$translated_string++;
			}
			$total_strings++;
		}
	}
	for ($i=0; $i<$nrfields; $i++) {
			$key=$adb->query_result($query, $i);
			$tr_string=$key;
			$result2[$key][0]=htmlentities($tr_string, ENT_QUOTES, $ref_encoding);
			$result2[$key][1]=htmlentities($mod_strings[$key], ENT_QUOTES, $trans_encoding);
			$result2[$key][2]=($key=='')?'#empty#':$key;
			$result2[$key][3]='fieldsnontranslated';
		if (!isset($mod_strings[$key])) {
			$result2[$key][3]='fieldsnontranslated';
		} elseif ($tr_string!=$mod_strings[$key]) {
			$result2[$key][3]='fieldstranslated';
			$translated_string++;
		}
			$total_strings++;
	}
	for ($i=0; $i<$nrofrelations; $i++) {
			$key=$adb->query_result($queryRelatedList, $i);
			$tr_string=$key;
			$result2[$key][0]=htmlentities($tr_string, ENT_QUOTES, $ref_encoding);
			$result2[$key][1]=htmlentities($mod_strings[$key], ENT_QUOTES, $trans_encoding);
			$result2[$key][2]=($key=='')?'#empty#':$key;
			$result2[$key][3]='rlnontranslated';
		if (!isset($mod_strings[$key])) {
			$result2[$key][3]='rlnontranslated';
		} elseif ($tr_string!=$mod_strings[$key]) {
			$result2[$key][3]='rltranslated';
			$translated_string++;
		}
			$total_strings++;
	}
}
$filter = isset($_REQUEST['filter_translate']) ? vtlib_purify($_REQUEST['filter_translate']) : '';
$hidden_fields = array();
if (!empty($filter)) {
	foreach ($result2 as $key => $resulttrl) {
		if ($filter=='not_translated') {
			if ($resulttrl[3]=='not_translated') {
				$resultnt[$key][0]=$resulttrl[0];
				$resultnt[$key][1]=$resulttrl[1];
				$resultnt[$key][2]=$resulttrl[2];
				$resultnt[$key][3]=$resulttrl[3];
			} else {
				$hidden_fields[$key][0]=$resulttrl[0];
				$hidden_fields[$key][1]=$resulttrl[1];
				$hidden_fields[$key][2]=$resulttrl[2];
				$hidden_fields[$key][3]=$resulttrl[3];
			}
		} elseif ($filter=='translated') {
			if ($resulttrl[3]=='translated') {
				$resultt[$key][0]=$resulttrl[0];
				$resultt[$key][1]=$resulttrl[1];
				$resultt[$key][2]=$resulttrl[2];
				$resultt[$key][3]=$resulttrl[3];
			} else {
				$hidden_fields[$key][0]=$resulttrl[0];
				$hidden_fields[$key][1]=$resulttrl[1];
				$hidden_fields[$key][2]=$resulttrl[2];
				$hidden_fields[$key][3]=$resulttrl[3];
			}
		} elseif ($filter=='fieldsnontranslated') {
			if ($resulttrl[3]=='fieldsnontranslated') {
				$resultfnt[$key][0]=$resulttrl[0];
				$resultfnt[$key][1]=$resulttrl[1];
				$resultfnt[$key][2]=$resulttrl[2];
				$resultfnt[$key][3]=$resulttrl[3];
			} else {
				$hidden_fields[$key][0]=$resulttrl[0];
				$hidden_fields[$key][1]=$resulttrl[1];
				$hidden_fields[$key][2]=$resulttrl[2];
				$hidden_fields[$key][3]=$resulttrl[3];
			}
		} elseif (($filter=='fieldstranslated')) {
			if ($resulttrl[3]=='fieldsnontranslated') {
				$resultft[$key][0]=$resulttrl[0];
				$resultft[$key][1]=$resulttrl[1];
				$resultft[$key][2]=$resulttrl[2];
				$resultft[$key][3]=$resulttrl[3];
			} else {
				$hidden_fields[$key][0]=$resulttrl[0];
				$hidden_fields[$key][1]=$resulttrl[1];
				$hidden_fields[$key][2]=$resulttrl[2];
				$hidden_fields[$key][3]=$resulttrl[3];
			}
		} elseif ($filter=='new') {
			if ($resulttrl[3]=='new') {
				$resultft[$key][0]=$resulttrl[0];
				$resultft[$key][1]=$resulttrl[1];
				$resultft[$key][2]=$resulttrl[2];
				$resultft[$key][3]=$resulttrl[3];
			} else {
				$hidden_fields[$key][0]=$resulttrl[0];
				$hidden_fields[$key][1]=$resulttrl[1];
				$hidden_fields[$key][2]=$resulttrl[2];
				$hidden_fields[$key][3]=$resulttrl[3];
			}
		} elseif ($filter=='rltranslated') {
			if ($resulttrl[3]=='rltranslated') {
				$resultrt[$key][0]=$resulttrl[0];
				$resultrt[$key][1]=$resulttrl[1];
				$resultrt[$key][2]=$resulttrl[2];
				$resultrt[$key][3]=$resulttrl[3];
			} else {
				$hidden_fields[$key][0]=$resulttrl[0];
				$hidden_fields[$key][1]=$resulttrl[1];
				$hidden_fields[$key][2]=$resulttrl[2];
				$hidden_fields[$key][3]=$resulttrl[3];
			}
		} elseif ($filter=='rlnontranslated') {
			if ($resulttrl[3]=='rlnontranslated') {
				$resultrnt[$key][0]=$resulttrl[0];
				$resultrnt[$key][1]=$resulttrl[1];
				$resultrnt[$key][2]=$resulttrl[2];
				$resultrnt[$key][3]=$resulttrl[3];
			} else {
				$hidden_fields[$key][0]=$resulttrl[0];
				$hidden_fields[$key][1]=$resulttrl[1];
				$hidden_fields[$key][2]=$resulttrl[2];
				$hidden_fields[$key][3]=$resulttrl[3];
			}
		}
	}
}

if ($filter=='not_translated') {
	$smarty->assign('TRANSLATION_STRING', $resultnt);
} elseif ($filter=='translated') {
	$smarty->assign('TRANSLATION_STRING', $resultt);
} elseif ($filter=='fieldsnontranslated') {
	$smarty->assign('TRANSLATION_STRING', $resultfnt);
} elseif ($filter=='fieldstranslated') {
	$smarty->assign('TRANSLATION_STRING', $resultft);
} elseif ($filter=='rltranslated') {
	$smarty->assign('TRANSLATION_STRING', $resultrt);
} elseif ($filter=='rlnontranslated') {
	$smarty->assign('TRANSLATION_STRING', $resultrnt);
} else {
	$smarty->assign('TRANSLATION_STRING', $result2);
}
$smarty->assign('HIDDEN_FIELDS', $hidden_fields);
$smarty->assign('FILTER', $filter);
$smarty->assign('ERROR', $error);
$smarty->assign('PERC_TRANSALTED', number_format($translated_string*100/$total_strings, 2).'%');
$smarty->assign('TRANSLATION_LIST_STRING', $tr_list);
$smarty->display('Settings/LanguageEdit.tpl');
?>