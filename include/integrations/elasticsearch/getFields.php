<?php
/*************************************************************************************************
 * Copyright 2019 JPL TSolucio, S.L. -- This file is a part of TSOLUCIO coreBOS customizations.
 * You can copy, adapt and distribute the work under the "Attribution-NonCommercial-ShareAlike"
 * Vizsage Public License (the "License"). You may not use this file except in compliance with the
 * License. Roughly speaking, non-commercial users may share and modify this code, but must give credit
 * and share improvements. However, for proper details please read the full License, available at
 * http://vizsage.com/license/Vizsage-License-BY-NC-SA.html and the handy reference for understanding
 * the full license at http://vizsage.com/license/Vizsage-Deed-BY-NC-SA.html. Unless required by
 * applicable law or agreed to in writing, any software distributed under the License is distributed
 * on an  "AS IS" BASIS, WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and limitations under the
 * License terms of Creative Commons Attribution-NonCommercial-ShareAlike 3.0 (the License).
 *************************************************************************************************
 *  Module    : Elasticsearch Settings
 *  Version   : 1.0
 *  Author    : AT Consulting
 *************************************************************************************************/

function getFields($module, $fieldsselected = array(), $types = array(), $analyzed = array(), $labels = array()) {
	global $adb;
	$tabid = getTabid($module);
	$sqlQueryfields = $adb->pquery("select * from vtiger_field where tabid=? and presence in (0,2)", array($tabid));
	$nr = $adb->num_rows($sqlQueryfields);
	$a = "";
	for ($k=0; $k<$nr; $k++) {
		$i = $k+1;
		$fldLabel = $adb->query_result($sqlQueryfields, $k, 'fieldlabel');
		$fldType= $adb->query_result($sqlQueryfields, $k, 'typeofdata');
		$colname= $adb->query_result($sqlQueryfields, $k, 'columnname');
		$fldTypessarr = array("Varchar","Number","Date","Date Time");
		$fldTypessarrcodes=array("V","N","D","DT");
		$fldTypessqlhtml = "";
		$fldchecked = '';
		$isanalyzed = '';
		$fldtypesel = '';
		$labeltext = $colname;

		if (in_array($colname, $fieldsselected)) {
			$arrindex = array_search($colname, $fieldsselected);
			if ($analyzed[$arrindex]=='on') {
				$isanalyzed = 'checked';
			}
			$fldtypesel = $types[$arrindex];
			$fldchecked = 'checked';
			$labeltext = $labels[$arrindex];
		}

		for ($j=0; $j<count($fldTypessarr); $j++) {
			$fldtypevalcode = $fldTypessarrcodes[$j];
			$fldtypeval = $fldTypessarr[$j];
			if ($fldtypesel == '' && ($fldtypevalcode == substr($fldType, 0, 1) || $fldtypevalcode == substr($fldType, 0, 2))) {
				$selected = 'selected';
			} elseif ($fldtypesel == $fldtypevalcode) {
				$selected = 'selected';
			} else {
				$selected = '';
			}
			$fldTypessqlhtml.="<option value='".$fldtypevalcode."' $selected>".$fldtypeval."</option>";
		}
		$fieldlabel = getTranslatedString($fldLabel);
		if ($fieldlabel == "") {
			$fieldlabel = $fldLabel;
		}
		if ($i%2 == 1) {
				$a.="<tr height=\"35\" id=\"row$i\" class=\"d0\">";
		} else {
				$a.="<tr height=\"35\" id=\"row$i\" class=\"d1\">";
		}

		$a.= "<td  align='left'><input type='hidden' name='countfields' value='$nr'>";
		$a.= "<input type='checkbox'  id='checkf$i' name='checkf$i'  class='k-checkbox' $fldchecked> ";
		$a.= "<label class='k-checkbox-label' for='checkf$i' id='mapfldname$i' >".$fldLabel."</label>";
		$a.= "<input type='hidden' value='$colname' id='colname$i' name='colname$i'></td>";
		$a.= "<td><input type='text' id='modulfieldlabel$i' name='modulfieldlabel$i' value='$labeltext'></td>";
		$a.= "<td><select id=\"modulfieldtype$i\" name=\"modulfieldtype$i\">".$fldTypessqlhtml."</select></td>";
		$a.= "<td><input type='checkbox'  id='checkanalyzed$i' name='checkanalyzed$i' $isanalyzed class='k-checkbox'>";
		$a.= "</tr>";
	}
	return $a;
}

function createindexmapping($ip, $indexname, $labels, $arrtypes, $arranalyzed, $module) {
	include_once "modules/$module/$module.php";
	$focus = new $module;
	$entityidfield = $focus->table_index;

	$cnt = count($labels);
	$indexfields[$entityidfield] = array("type"=>"text");

	for ($i = 0; $i < $cnt; $i++) {
		$analyzedChecked = $arranalyzed[$i];
		if ($analyzedChecked == "on") {
			$analyzed = "true";
		} else {
			$analyzed = "false";
		}

		$label = $labels[$i];
		$fldtype = $arrtypes[$i];

		if ($fldtype=='N') {
			$coltype='double';
			$indexfields[$label] = array("type"=>$coltype);
		} elseif ($fldtype=='D') {
			$coltype='date';
			$format='yyyy-MM-dd||epoch_millis';
			$indexfields[$label] = array("type"=>$coltype,"format"=>"$format");
		} elseif ($fldtype=='DT') {
			$format='yyyy-MM-dd HH:mm:ss||epoch_millis';
			$coltype = 'date';
			$indexfields[$label] = array("type"=>$coltype,"format"=>"$format");
		} else {
			$coltype='text';
			$indexfields[$label] = array("type"=>$coltype,"index"=>"$analyzed");
		}
		//add listofactivities and keywordses double fields
		if ($label == 'keywordses' || $label == 'listofactivities') {
			$coltype='keyword';
			$analyzed = "true";
			$indexfields[$label.'keyw'] = array("type"=>$coltype,"index"=>"$analyzed");
		}
	}

	$file = 'logs/elasticsearch.log';
	$msg = "===========".date("Y-m-d H:i:s")."===========";
	error_log($msg."\n", 3, $file);
	error_log("Create Index $indexname \n", 3, $file);
	error_log(json_encode($indexfields)." \n", 3, $file);
	$fields1 = array("mappings"=>array("import"=>array("properties"=>$indexfields, "dynamic_date_formats" => '[\'yyyy-MM-dd HH:mm:ss\',\'yyyy-MM-dd\', \'dd-MM-yyyy\', \'date_optional_time\', \'epoch_millis\']')));
	$endpointUrl = "http://$ip:9200/$indexname";
	$username = GlobalVariable::getVariable('esusername', '');
	$password = GlobalVariable::getVariable('espassword', '');
	$channel = curl_init();
	curl_setopt($channel, CURLOPT_HTTPHEADER, array("Content-Type: application/json"));
	curl_setopt($channel, CURLOPT_USERPWD, $username . ":" . $password);
	curl_setopt($channel, CURLOPT_URL, $endpointUrl);
	curl_setopt($channel, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($channel, CURLOPT_CUSTOMREQUEST, "PUT");
	curl_setopt($channel, CURLOPT_POSTFIELDS, json_encode($fields1));
	curl_setopt($channel, CURLOPT_CONNECTTIMEOUT, 100);
	curl_setopt($channel, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($channel, CURLOPT_TIMEOUT, 1000);
	$response = curl_exec($channel);
	error_log($response." \n", 3, $file);
}

function deleteindex($ip, $index) {
	$file = 'logs/elasticsearch.log';
	$msg = "===========".date("Y-m-d H:i:s")."===========";
	error_log($msg."\n", 3, $file);
	error_log("Delete Index $index \n", 3, $file);
	$endpointUrl = "http://$ip:9200/$index";
	$username = GlobalVariable::getVariable('esusername', '');
	$password = GlobalVariable::getVariable('espassword', '');
	$channel = curl_init();
	curl_setopt($channel, CURLOPT_URL, $endpointUrl);
	curl_setopt($channel, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($channel, CURLOPT_USERPWD, $username . ":" . $password);
	curl_setopt($channel, CURLOPT_CUSTOMREQUEST, "DELETE");
	curl_setopt($channel, CURLOPT_CONNECTTIMEOUT, 100);
	curl_setopt($channel, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($channel, CURLOPT_TIMEOUT, 1000);
	$response = curl_exec($channel);
	error_log($response."\n", 3, $file);
}