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
 *  Module       : Languages
 *  Version      : 5.4.0
 *  Author       : Opencubed
 * the code is based on the work of Gaëtan KRONEISEN technique@expert-web.fr and  Pius Tschümperlin ep-t.ch
 *************************************************************************************************/

require_once('modules/Languages/Config.inc.php');
require_once('include/database/PearDatabase.php');
require_once('include/utils/utils.php');

function quote_replace($value){
        if(is_string($value)){
        	    $value = str_replace("\'", "#single-quote#", $value);
                $value = str_replace("'", "\'", $value);
                return str_replace("#single-quote#", "\\\\\'", $value);
        }
        return $value;

 }

if(get_magic_quotes_gpc() == 1){
        if(is_array($_REQUEST['translate_value'])) $_REQUEST['translate_value'] = array_map("stripslashes_checkstrings", $_REQUEST['translate_value']);
        if(is_array($_REQUEST['translate_list_value'])) $_REQUEST['translate_list_value'] = array_map("stripslashes_checkstrings", $_REQUEST['translate_list_value']);
        if(is_array($_REQUEST['translate_list_value2'])) $_REQUEST['translate_list_value2'] = array_map("stripslashes_checkstrings", $_REQUEST['translate_list_value2']);

}
global $log;
$db = new PearDatabase();
$line_break=chr(10);
if(isset($_REQUEST['languageid']) && $_REQUEST['languageid'] !='' && $_POST['pick_module']!='' ){
	//Get languguage info
	$dbQuery="SELECT * FROM vtiger_languages WHERE languageid=".$_REQUEST['languageid'];
	$result = $adb->query($dbQuery);
	$row = $adb->fetch_array($result);
	$now=date('Y-m-d H:i:s');
	$header='/*+********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ********************************************************************************/'.$line_break.$line_break.$line_break;

	if($_REQUEST['pick_module']=='General'){
		$filename='include/language/'.$row['prefix'].'.lang.php';
		if(file_exists($filename) && is_writable($filename)){
			if ($make_backups == true) {
				@unlink($filename.'.bak');
				@copy($filename, $filename.'.bak');
			}
			$fd = fopen($filename, 'w');
			fwrite($fd, '<?php'.$line_break.$header.'$app_strings = array ('.$line_break);
			if(is_array($_REQUEST['translate_value'])) $_REQUEST['translate_value'] = array_map("quote_replace", $_REQUEST['translate_value']);
			foreach($_REQUEST['translate_value'] as $key=>$string){
				$_key = ($key=='#empty#')?'':$key;
				fwrite($fd, '      \''.$_key.'\' => \''.iconv("UTF-8", $row['encoding'], $string).'\','.$line_break);
			}
                        if(is_array($_REQUEST['newLabels'])){ var_dump($_REQUEST['newLabels']);
                                    $_REQUEST['newLabels'] = array_map("quote_replace", $_REQUEST['newLabels']);                                 
                                    foreach($_REQUEST['newLabels'] as $key1=>$arr){
                                    if(is_array($arr)){
                                            $_key = $arr['key'];
                                            $value = $arr['value'];
                                            fwrite($fd, '      \''.$_key.'\' => \''.iconv("UTF-8", $row['encoding'], $value).'\','.$line_break);
                                        }                                   
                                    }
                                }
			fwrite($fd, ');'.$line_break.'$app_list_strings = array ('.$line_break);
			if(is_array($_REQUEST['translate_list_value2'])) $_REQUEST['translate_list_value2'] = array_map("quote_replace", $_REQUEST['translate_list_value2']);
			foreach($_REQUEST['translate_list_value2'] as $key1=>$arr){
				if(is_array($arr)){
					fwrite($fd, '      \''.$key1.'\' => array('.$line_break);
					foreach($arr as $key2=>$value){
						$_key = ($key2=='#empty#')?'':$key2;
						fwrite($fd, '            \''.$_key.'\' => \''.iconv("UTF-8", $row['encoding'], $value).'\','.$line_break);
					}
					fwrite($fd, '                             ),'.$line_break);
				}
			}                         
			fwrite($fd, ');'.$line_break.'?>');
			fclose($fd);
			$dbQuery="UPDATE vtiger_languages SET modifiedtime='".$now."' WHERE languageid=".$_REQUEST['languageid'];
			$result = $adb->query($dbQuery);
		}
	}
	if($_REQUEST['pick_module']=='JavaScript'){
	$filename='include/js/'.$row['prefix'].'.lang.js';
		if(file_exists($filename) && is_writable($filename)){
			if ($make_backups == true) {
				@unlink($filename.'.bak');
				@copy($filename, $filename.'.bak');
			}
			$fd = fopen($filename, 'w');
			fwrite($fd, $header.'var alert_arr = {'.$line_break);
			if(is_array($_REQUEST['translate_value'])) $_REQUEST['translate_value'] = array_map("quote_replace", $_REQUEST['translate_value']);
			$first_entry = 1;
			foreach($_REQUEST['translate_value'] as $key=>$string){
			    if ($first_entry == 1) {
					$_key = ($key=='#empty#')?'':$key;
					fwrite($fd, '      '.$_key.':\''.iconv("UTF-8", $row['encoding'], $string).'\'');
					$first_entry = 0;
				}
				else {
					$_key = ($key=='#empty#')?'':$key;
					fwrite($fd, ','.$line_break.'      '.$_key.':\''.iconv("UTF-8", $row['encoding'], $string).'\'');
				}
			}
			fwrite($fd, '};');
			fclose($fd);
			$dbQuery="UPDATE vtiger_languages SET modifiedtime='".$now."' WHERE languageid=".$_REQUEST['languageid'];
			$result = $adb->query($dbQuery);
		}
	}
	else{
		$filename=$modulesDirectory.'/'.$_REQUEST['pick_module'].'/language/'.$row['prefix'].'.lang.php';
		if(file_exists($filename) && is_writable($filename)){
			if ($make_backups == true) {
				@unlink($filename.'.bak');
				@copy($filename, $filename.'.bak');
			}
			$fd = fopen($filename, 'w');
			fwrite($fd, '<?php'.$line_break.$header.'$mod_strings = array ('.$line_break);
			$_REQUEST['translate_value'] = array_map("quote_replace", $_REQUEST['translate_value']);
			foreach($_REQUEST['translate_value'] as $key=>$string){
				$_key = ($key=='#empty#')?'':$key;
                fwrite($fd, '      \''.$_key.'\' => \''.iconv("UTF-8", $row['encoding'], $string).'\','.$line_break);
			}
            if(is_array($_REQUEST['translate_list_value'])){
            	$_REQUEST['translate_list_value'] = array_map("quote_replace", $_REQUEST['translate_list_value']);
                foreach($_REQUEST['translate_list_value'] as $key1=>$arr){
                    if(is_array($arr)){
                        fwrite($fd,  '      \''.$key1.'\' => array('.$line_break);
                        foreach($arr as $key2=>$value){
                            $_key = ($key2=='#empty#')?'':$key2;
                            fwrite($fd,  '            \''.$_key.'\' => \''.iconv("UTF-8", $row['encoding'], $value).'\','.$line_break);
                        }
                        fwrite($fd,  '                             ),'.$line_break);
                    }
                }
            }
                        if(is_array($_REQUEST['newLabels'])){ var_dump($_REQUEST['newLabels']);
                                    $_REQUEST['newLabels'] = array_map("quote_replace", $_REQUEST['newLabels']);                                 
                                    foreach($_REQUEST['newLabels'] as $key1=>$arr){
                                    if(is_array($arr)){
                                            $_key = $arr['key'];
                                            $value = $arr['value'];
                                            fwrite($fd, '      \''.$_key.'\' => \''.iconv("UTF-8", $row['encoding'], $value).'\','.$line_break);
                                        }                                   
                                    }
                                }
			fwrite($fd,  ');'.$line_break.'$mod_list_strings = array ('.$line_break);
			if(is_array($_REQUEST['translate_list_value2'])){
				$_REQUEST['translate_list_value2'] = array_map("quote_replace", $_REQUEST['translate_list_value2']);
                foreach($_REQUEST['translate_list_value2'] as $key1=>$arr){
                    if(is_array($arr)){
                        fwrite($fd,  '      \''.$key1.'\' => array('.$line_break);
                        foreach($arr as $key2=>$value){
							$_key = ($key2=='#empty#')?'':$key2;
                            fwrite($fd,  '            \''.$_key.'\' => \''.iconv("UTF-8", $row['encoding'], $value).'\','.$line_break);
                        }
                        fwrite($fd,  '                             ),'.$line_break);
                    }
                }
            }
             
			fwrite($fd,  ');'.$line_break.'?>');
			fclose($fd);
			$dbQuery="UPDATE vtiger_languages SET modifiedtime='".$now."' WHERE languageid=".$_REQUEST['languageid'];
			$result = $adb->query($dbQuery);
		}
	}
	header("Location:index.php?module=Languages&action=LanguageEdit&parenttab=Settings&pick_module=".$_POST['pick_module']."&languageid=".$_REQUEST['languageid']);
}
?>