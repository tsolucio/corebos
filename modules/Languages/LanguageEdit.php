<?php
/*********************************************************************************
 * $Header$
 * Description: Language Pack Wizard
 * Portions created by SugarCRM are Copyright (C) SugarCRM, Inc.
 * All Rights Reserved.
 * Contributor(s): Ga�tan KRONEISEN technique@expert-web.fr
 * 				   Pius Tsch�mperlin ep-t.ch
 ********************************************************************************/

require_once('modules/Languages/Config.inc.php');
require_once('Smarty_setup.php');
require_once('include/database/PearDatabase.php');
require_once('include/CustomFieldUtil.php');

global $mod_strings,$app_strings,$current_language,$theme;
$smarty=new vtigerCRM_Smarty;
$smarty->assign("UMOD", $mod_strings);
$smod_strings = return_module_language($current_language,'Settings');
$smarty->assign("MOD", $smod_strings);
$smarty->assign("MODULE", 'Settings');
$smarty->assign("IMAGE_PATH", $image_path);
$smarty->assign("PARENTTAB", $_REQUEST['parenttab']);
$smarty->assign("APP",$app_strings);
$theme_path="themes/".$theme."/";
$image_path=$theme_path."images/";
$smarty->assign("IMAGE_PATH", $image_path);

$smarty->assign("LANGUAGEID",$_REQUEST['languageid']);
$smarty->assign("MODULE",($_REQUEST['pick_module']!='')?$_REQUEST['pick_module']:'General');

//REF_LANGUAGE
$ref_language = $default_language;
$dbQuery = "SELECT prefix,language,encoding FROM vtiger_languages WHERE prefix='".$ref_language."'";
$result = $adb->query($dbQuery);
$row = $adb->fetch_array($result);
$ref_encoding = $row['encoding'];
$smarty->assign("REF_LANGUAGE",$row['language']);

//Get languguage info
$dbQuery="SELECT prefix,language,encoding FROM vtiger_languages WHERE languageid=".$_REQUEST['languageid'];
$result = $adb->query($dbQuery);
$row = $adb->fetch_array($result);
$trans_encoding = $row['encoding'];
$smarty->assign("LANGUAGE",$row['language']);
$module_array=Array('General'=> $mod_strings['General']);
$module_array['JavaScript']= $mod_strings['JavaScript'];


//Get Modules and languages files
if ($dh = opendir($modulesDirectory)) {
	while (($folder = readdir($dh)) !== false) { 
		if(is_dir($modulesDirectory.'/'.$folder)&&$folder!='..'&&$folder!='.'&&file_exists($modulesDirectory.'/'.$folder.'/language/'.$ref_language.'.lang.php')) {
			if($app_strings[$folder]!=''){
				$module_array[$folder]=$app_strings[$folder];
			}
			elseif($mod_strings[$folder]!=''){
				$module_array[$folder]=$mod_strings[$folder];
			}else{
				$module_array[$folder]=ucfirst($folder);
			}
      }
    } 
    closedir($dh); 
}
asort($module_array);
$smarty->assign("MODULES",$module_array);

//General language strings
if($_REQUEST['pick_module']=='' || $_REQUEST['pick_module']=='General'){

	//Get Refernce Strings
	include 'include/language/'.$ref_language.'.lang.php';
	$ref_app_strings = $app_strings;
	$ref_app_list_strings=$app_list_strings;
	
	//Get translated Strings
	if(!file_exists('include/language/'.$row['prefix'].'.lang.php')){
		$handle=fopen('include/language/'.$row['prefix'].'.lang.php','w');
		fclose($handle);
	}
	$error=(is_writable('include/language/'.$row['prefix'].'.lang.php'))?'':$mod_strings['ERROR_GENERAL_FILE_WRITE'];
	include 'include/language/'.$row['prefix'].'.lang.php';
	
	//Merge the two languages stings in array and make some pour stats :)
	$total_strings=0;
	$translated_string=0;
	foreach($ref_app_strings As $key=>$tr_string){
		$result2[$key][0]=htmlentities($tr_string, ENT_QUOTES, $ref_encoding);
		$result2[$key][1]=htmlentities($app_strings[$key], ENT_QUOTES, $trans_encoding);
		$result2[$key][2]=($key=='')?'#empty#':$key;
		$result2[$key][3]='not_translated';
		if(!isset($app_strings[$key])){
			$result2[$key][3]='new';
		}
		elseif($tr_string!=$app_strings[$key]){
			$result2[$key][3]='translated';
			$translated_string++;
		}
		$total_strings++;
	}

		//Same for list strings
	foreach($ref_app_list_strings As $key=>$list_tr_string){
		if(is_array($list_tr_string)){
			foreach($list_tr_string As $skey=>$str_string){
					$tr_list2[$key][$skey][0]=htmlentities($str_string, ENT_QUOTES, $ref_encoding);
					$tr_list2[$key][$skey][1]=htmlentities($app_list_strings[$key][$skey], ENT_QUOTES, $trans_encoding);
					$tr_list2[$key][$skey][2]=($skey=='')?'#empty#':$skey;
					$tr_list2[$key][$skey][3]='not_translated';
					if(!isset($app_list_strings[$key][$skey])){
						$tr_list2[$key][$skey][3]='new';
					}
					elseif($str_string!=$app_list_strings[$key][$skey]){
						$tr_list2[$key][$skey][3]='translated';
						$translated_string++;
					}
					$total_strings++;
			}
		}
	}
}
//JavaScript strings
elseif($_REQUEST['pick_module']=='JavaScript'){

	//js to php
	$patterns[0] = '/var\s*alert_arr\s*=\s*{/';
	$patterns[1] = '/(\S*)(\s*)(:)(\s*.*,?)/';
	$patterns[2] = '/(".*"|\'.*\')\s*\+/';
	$patterns[3] = '/};/';
	$replacements[0] = '$app_strings = array (';
	$replacements[1] = "'$1'"." => $4";
	$replacements[2] = "$1.";
	$replacements[3] = ');';

	//Get Default Strings
	$filename='include/js/'.$ref_language.'.lang.js';
	if(file_exists($filename)){
		$jsfileContent = file_get_contents($filename,FILE_TEXT);
		$jsfileContent = preg_replace($patterns, $replacements, $jsfileContent);
		eval($jsfileContent);
		$ref_app_strings = $app_strings;
	}
	
	//Get your languague strings
	$filename='include/js/'.$row['prefix'].'.lang.js';
	$error=(is_writable($filename))?'':$mod_strings['ERROR_GENERAL_FILE_WRITE'];
    if(!file_exists($filename)){
		$handle=fopen($filename,'w');
		fclose($handle);
	}
	if(file_exists($filename)){
		$jsfileContent = file_get_contents($filename,FILE_TEXT);
		$jsfileContent = preg_replace($patterns, $replacements, $jsfileContent);
		eval($jsfileContent);
	}
	
	//Merge the two languages stings in array and make some pour stats :)
	$total_strings=0;
	$translated_string=0;
	foreach($ref_app_strings As $key=>$tr_string){
		$result2[$key][0]=htmlentities($tr_string, ENT_QUOTES, $ref_encoding);
		$result2[$key][1]=htmlentities($app_strings[$key], ENT_QUOTES, $trans_encoding);
		$result2[$key][2]=($key=='')?'#empty#':$key;
		$result2[$key][3]='not_translated';
		if(!isset($app_strings[$key])){
			$result2[$key][3]='new';
		}
		elseif($tr_string!=$app_strings[$key]){
			$result2[$key][3]='translated';
			$translated_string++;
		}
		$total_strings++;
	}

}
//Modules language strings
else{ global $adb;
	$error_msg=$mod_strings['ERROR_MODULE_FILE_WRITE'];
	//Get Default Strings
	include 'modules/'.$_REQUEST['pick_module'].'/language/'.$ref_language.'.lang.php';
	$ref_mod_strings = $mod_strings;
	$ref_mod_list_strings = $mod_list_strings;

	//Get your languague strings
	if(!file_exists('modules/'.$_REQUEST['pick_module'].'/language/'.$row['prefix'].'.lang.php')){
		$handle=fopen('modules/'.$_REQUEST['pick_module'].'/language/'.$row['prefix'].'.lang.php','w');
		fclose($handle);
	}
	$error=(is_writable('modules/'.$_REQUEST['pick_module'].'/language/'.$row['prefix'].'.lang.php'))?'':$error_msg;
	include 'modules/'.$_REQUEST['pick_module'].'/language/'.$row['prefix'].'.lang.php';

        $tabid=getTabid($_REQUEST['pick_module']);
        $query=$adb->pquery("Select fieldlabel from vtiger_field where tabid=?",array($tabid));
        $nrfields=$adb->num_rows($query);
        $queryRelatedList=$adb->pquery("Select label from vtiger_relatedlists where tabid=?",array($tabid));
        $nrofrelations=$adb->num_rows($queryRelatedList);
	//Merge the two languages strings in array and make some pour stats :)
	$total_strings=0;
	$translated_string=0;
	foreach($ref_mod_strings As $key=>$tr_string){
		if(is_array($tr_string)){
			foreach($tr_string As $skey=>$str_string){
				$tr_list[$key][$skey][0]=htmlentities($str_string, ENT_QUOTES, $ref_encoding);
				$tr_list[$key][$skey][1]=htmlentities($mod_strings[$key][$skey], ENT_QUOTES, $trans_encoding);
				$tr_list[$key][$skey][2]=($skey=='')?'#empty#':$skey;
				$tr_list[$key][$skey][3]='not_translated';
				if(!isset($mod_strings[$key][$skey])){
					$tr_list[$key][$skey][3]='new';
				}
				elseif($str_string!=$mod_strings[$key][$skey]){
					$tr_list[$key][$skey][3]='translated';
					$translated_string++;
				}
				$total_strings++;
			}
		}
		else{
			$result2[$key][0]=htmlentities($tr_string, ENT_QUOTES, $ref_encoding);
			$result2[$key][1]=htmlentities($mod_strings[$key], ENT_QUOTES, $trans_encoding);
			$result2[$key][2]=($key=='')?'#empty#':$key;
			$result2[$key][3]='not_translated';
			if(!isset($mod_strings[$key])){
				$result2[$key][3]='new';
			}
			elseif($tr_string!=$mod_strings[$key]){
				$result2[$key][3]='translated';
				$translated_string++;
			}
			$total_strings++;
		}
	}
        for($i=0;$i<$nrfields;$i++){
                        $key=$adb->query_result($query,$i);
                        $tr_string=$key;
                        $result2[$key][0]=htmlentities($tr_string, ENT_QUOTES, $ref_encoding);
			$result2[$key][1]=htmlentities($mod_strings[$key], ENT_QUOTES, $trans_encoding);
			$result2[$key][2]=($key=='')?'#empty#':$key;
			$result2[$key][3]='fieldsnontranslated';
			if(!isset($mod_strings[$key])){
				$result2[$key][3]='fieldsnontranslated';
			}
			elseif($tr_string!=$mod_strings[$key]){
				$result2[$key][3]='fieldstranslated';
				$translated_string++;
			}
			$total_strings++;
        }
        for($i=0;$i<$nrofrelations;$i++){
                        $key=$adb->query_result($queryRelatedList,$i);
                        $tr_string=$key;
                        $result2[$key][0]=htmlentities($tr_string, ENT_QUOTES, $ref_encoding);
			$result2[$key][1]=htmlentities($mod_strings[$key], ENT_QUOTES, $trans_encoding);
			$result2[$key][2]=($key=='')?'#empty#':$key;
			$result2[$key][3]='rlnontranslated';
			if(!isset($mod_strings[$key])){
				$result2[$key][3]='rlnontranslated';
			}
			elseif($tr_string!=$mod_strings[$key]){
				$result2[$key][3]='rltranslated';
				$translated_string++;
			}
			$total_strings++;
        }
	//Same for list strings
	if(is_array($ref_mod_list_strings)){
		foreach($ref_mod_list_strings As $key=>$list_tr_string){
			if(is_array($list_tr_string)){
				foreach($list_tr_string As $skey=>$str_string){
					$tr_list2[$key][$skey][0]=htmlentities($str_string, ENT_QUOTES, $ref_encoding);
					$tr_list2[$key][$skey][1]=htmlentities($mod_list_strings[$key][$skey], ENT_QUOTES, $trans_encoding);
					$tr_list2[$key][$skey][2]=($skey=='')?'#empty#':$skey;
					$tr_list2[$key][$skey][3]='not_translated';
					if(!isset($mod_list_strings[$key][$skey])){
						$tr_list2[$key][$skey][3]='new';
					}
					elseif($str_string!=$mod_list_strings[$key][$skey]){
						$tr_list2[$key][$skey][3]='translated';
						$translated_string++;
					}
					$total_strings++;
				}
			}
		}
	}
}

$smarty->assign("ERROR",$error);
$smarty->assign("PERC_TRANSALTED",number_format($translated_string*100/$total_strings,2).'%');
$smarty->assign("TRANSLATION_STRING",$result2);
$smarty->assign("TRANSLATION_LIST_STRING",$tr_list);
$smarty->assign("TRANSLATION_LIST_STRING2",$tr_list2);
$smarty->display('Settings/Languages/LanguageEdit.tpl');
?>