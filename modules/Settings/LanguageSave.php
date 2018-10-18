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
 * the code is based on the work of Gaëtan KRONEISEN technique@expert-web.fr and Pius Tschümperlin ep-t.ch
 *************************************************************************************************/
require_once 'include/utils/utils.php';
global $log;

//General Directory structure information
$modulesDirectory='modules';
//Make backups when changing filecontents
$make_backups =false;
// Line Break
$line_break=chr(10);
// Language Encoding
$langencoding = 'UTF-8';

function quote_replace($value) {
	if (is_string($value)) {
		$value = str_replace("\'", '#single-quote#', $value);
		$value = str_replace("'", "\'", $value);
		return str_replace('#single-quote#', "\\\\\'", $value);
	}
	return $value;
}

// If file contains license header we respect it, if not we return the default header
function cbGetHeaderToUseInLanguageFile($filename) {
	global $line_break;
	$filehdr = file_get_contents($filename, false, null, 0, 4000);  // should be enough to get very big headers
	$startcomm = strpos($filehdr, '/*');
	$stopcomm = strpos($filehdr, '*/');
	if (!($startcomm===false || $stopcomm===false)) {
		$header = substr($filehdr, $startcomm, $stopcomm-$startcomm+2).$line_break.$line_break;
	} else {
		$header='/*************************************************************************************************
 * Copyright '.date('Y').' JPL TSolucio, S.L. -- This file is a part of TSOLUCIO coreBOS Customizations.
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
 *************************************************************************************************/'.$line_break.$line_break;
	}
	return $header;
}

$langid = vtlib_purify($_REQUEST['languageid']);
$pmodule = vtlib_purify($_REQUEST['pick_module']);
if (isset($langid) && $langid !='' && $pmodule!='') {
	//Get languguage info
	$dbQuery='SELECT prefix FROM vtiger_language WHERE id=?';
	$result = $adb->pquery($dbQuery, array($langid));
	if ($result && $adb->num_rows($result)==1) {
		$langprefix = $adb->query_result($result, 0, 0);
		$now=date('Y-m-d H:i:s');

		if ($pmodule=='General') {
			$filename='include/language/'.$langprefix.'.lang.php';
			if (file_exists($filename) && is_writable($filename)) {
				if ($make_backups == true) {
					@unlink($filename.'.bak');
					@copy($filename, $filename.'.bak');
				}
				$header = cbGetHeaderToUseInLanguageFile($filename);
				// we have to save the $app_currency_strings array which is constant
				include 'include/language/en_us.lang.php';
				$fd = fopen($filename, 'w');
				fwrite($fd, '<?php'.$line_break.$header.'$app_strings = array('.$line_break);
				if (is_array($_REQUEST['translate_value'])) {
					$_REQUEST['translate_value'] = array_map('quote_replace', $_REQUEST['translate_value']);
				}
				foreach ($_REQUEST['translate_value'] as $key => $string) {
					$_key = ($key=='#empty#')?'':$key;
					fwrite($fd, "	'$_key' => '".iconv('UTF-8', $langencoding, $string)."',".$line_break);
				}
				if (is_array($_REQUEST['newLabels'])) {
					$_REQUEST['newLabels'] = array_map('quote_replace', $_REQUEST['newLabels']);
					foreach ($_REQUEST['newLabels'] as $key1 => $arr) {
						if (is_array($arr)) {
							$_key = $arr['key'];
							$value = $arr['value'];
							fwrite($fd, "	'$_key' => '".iconv('UTF-8', $langencoding, $value)."',".$line_break);
						}
					}
				}
			  // Translation for currency names :: $app_currency_strings
				fwrite($fd, ');'.$line_break.'// Translation for currency names'.$line_break.'$app_currency_strings = array('.$line_break);
				foreach ($app_currency_strings as $key1 => $value) {
					fwrite($fd, "	'$key1' => '$value',".$line_break);
				}
				fwrite($fd, ');'.$line_break.'?>');
				fclose($fd);
				$dbQuery='UPDATE vtiger_language SET lastupdated=? WHERE languageid=?';
				$result = $adb->pquery($dbQuery, array($now,$langid));
			}
		} elseif ($pmodule=='JavaScript') {
			$filename='include/js/'.$langprefix.'.lang.js';
			if (file_exists($filename) && is_writable($filename)) {
				if ($make_backups == true) {
					@unlink($filename.'.bak');
					@copy($filename, $filename.'.bak');
				}
				$header = cbGetHeaderToUseInLanguageFile($filename);
				$fd = fopen($filename, 'w');
				fwrite($fd, $header.'var alert_arr = {'.$line_break);
				if (is_array($_REQUEST['translate_value'])) {
					$_REQUEST['translate_value'] = array_map('quote_replace', $_REQUEST['translate_value']);
				}
				$first_entry = 1;
				foreach ($_REQUEST['translate_value'] as $key => $string) {
					if ($first_entry == 1) {
						$_key = ($key=='#empty#')?'':$key;
						fwrite($fd, "	'$_key':'".iconv('UTF-8', $langencoding, $string)."'");
						$first_entry = 0;
					} else {
						$_key = ($key=='#empty#')?'':$key;
						fwrite($fd, ','.$line_break."	'$_key':'".iconv('UTF-8', $langencoding, $string)."'");
					}
				}
				fwrite($fd, $line_break.'};');
				fclose($fd);
				$dbQuery='UPDATE vtiger_language SET lastupdated=? WHERE languageid=?';
				$result = $adb->pquery($dbQuery, array($now,$langid));
			}
		} else {
			$filename=$modulesDirectory.'/'.$pmodule.'/language/'.$langprefix.'.lang.php';
			if (file_exists($filename) && is_writable($filename)) {
				if ($make_backups == true) {
					@unlink($filename.'.bak');
					@copy($filename, $filename.'.bak');
				}
				$header = cbGetHeaderToUseInLanguageFile($filename);
				$fd = fopen($filename, 'w');
				fwrite($fd, '<?php'.$line_break.$header.'$mod_strings = array('.$line_break);
				$_REQUEST['translate_value'] = array_map('quote_replace', $_REQUEST['translate_value']);
				foreach ($_REQUEST['translate_value'] as $key => $string) {
					$_key = ($key=='#empty#')?'':$key;
					if ($string=='') {
						$string = $key;
					}
					fwrite($fd, "	'$_key' => '".iconv('UTF-8', $langencoding, $string)."',".$line_break);
				}
				if (is_array($_REQUEST['translate_list_value'])) {
					$_REQUEST['translate_list_value'] = array_map('quote_replace', $_REQUEST['translate_list_value']);
					foreach ($_REQUEST['translate_list_value'] as $key1 => $arr) {
						if (is_array($arr)) {
							fwrite($fd, "	'$key1' => array(".$line_break);
							foreach ($arr as $key2 => $value) {
								$_key = ($key2=='#empty#')?'':$key2;
								if ($value=='') {
									$value = $key2;
								}
								fwrite($fd, "		'$_key' => '".iconv('UTF-8', $langencoding, $value)."',".$line_break);
							}
							fwrite($fd, '	),'.$line_break);
						}
					}
				}
				if (is_array($_REQUEST['newLabels'])) {
					$_REQUEST['newLabels'] = array_map('quote_replace', $_REQUEST['newLabels']);
					foreach ($_REQUEST['newLabels'] as $key1 => $arr) {
						if (is_array($arr)) {
							$_key = $arr['key'];
							$value = $arr['value'];
							fwrite($fd, "	'$_key' => '".iconv('UTF-8', $langencoding, $value)."',".$line_break);
						}
					}
				}
				fwrite($fd, ');'.$line_break.'?>');
				fclose($fd);
				$dbQuery='UPDATE vtiger_language SET lastupdated=? WHERE languageid=?';
				$result = $adb->pquery($dbQuery, array($now,$langid));
			}
		} // if no language prefix
	}
	header('Location:index.php?module=Settings&action=LanguageEdit&parenttab=Settings&pick_module='.urlencode($pmodule).'&languageid='.urlencode($langid));
}
?>