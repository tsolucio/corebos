<?php
/*************************************************************************************************
 * Copyright 2016 JPL TSolucio, S.L. -- This file is a part of TSOLUCIO coreBOS Customizations.
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
 *************************************************************************************************/
//saveFieldDependency.php

include_once "modules/cbMap/cbMap.php";
require_once 'data/CRMEntity.php';
require_once 'include/utils/utils.php';
require_once 'All_functions.php';
require_once 'Staticc.php';

global $root_directory, $log;
$Data = array();

$MapName = $_POST['MapName']; // stringa con tutti i campi scelti in selField1
$MapType = "GlobalSearchAutocomplete"; // stringa con tutti i campi scelti in selField1
$SaveasMapText = $_POST['SaveasMapText'];
$Data = $_POST['ListData'];
$MapID = explode(',', $_REQUEST['savehistory']);
$mapname = (!empty($SaveasMapText) ? $SaveasMapText : $MapName);
$idquery2 = !empty($MapID[0]) ? $MapID[0] : md5(date("Y-m-d H:i:s") . uniqid(rand(), true));

if (empty($SaveasMapText)) {
    if (empty($MapName)) {
        echo "Missing the name of map Can't save";
        return;
    }
}
if (empty($MapType)) {
    $MapType = "GlobalSearchAutocomplete";
}

if (!empty($Data)) {

    $jsondecodedata = json_decode($Data);

    if (strlen($MapID[1] == 0)) {

        $focust = new cbMap();
        $focust->column_fields['assigned_user_id'] = 1;
        // $focust->column_fields['mapname'] = $jsondecodedata[0]->temparray->FirstModule."_ListColumns";
        $focust->column_fields['mapname'] = $mapname;
        $focust->column_fields['content'] = add_content($jsondecodedata, $mapname);
        $focust->column_fields['maptype'] = $MapType;
        // $focust->column_fields['targetname'] =$jsondecodedata[0]->temparray->FirstModule;
        $focust->column_fields['description'] = add_content($jsondecodedata);
        $focust->column_fields['mvqueryid'] = $idquery2;
        $log->debug(" we inicialize value for insert in database ");
        if (!$focust->saveentity("cbMap")) //
        {

            if (Check_table_if_exist(TypeOFErrors::Tabele_name) > 0) {
                echo save_history(add_aray_for_history($jsondecodedata), $idquery2, add_content($jsondecodedata, $mapname)) . "," . $focust->id;
            } else {
                echo "0,0";
                $log->debug("Error!! MIssing the history Table");
            }

        } else {
            echo "Error!! something went wrong";
            $log->debug("Error!! something went wrong");
        }

    } else {

        include_once "modules/cbMap/cbMap.php";
        $focust = new cbMap();
        $focust->id = $MapID[1];
        $focust->retrieve_entity_info($MapID[1], "cbMap");
        $focust->column_fields['assigned_user_id'] = 1;
        // $focust->column_fields['mapname'] = $MapName;
        $focust->column_fields['content'] = add_content($jsondecodedata);
        $focust->column_fields['maptype'] = $MapType;
        $focust->column_fields['mvqueryid'] = $idquery2;
        // $focust->column_fields['targetname'] =$jsondecodedata[0]->temparray->FirstModule;
        $focust->column_fields['description'] = add_content($jsondecodedata, $mapname);
        $focust->mode = "edit";
        $focust->save("cbMap");

        if (Check_table_if_exist(TypeOFErrors::Tabele_name) > 0) {
            echo save_history(add_aray_for_history($jsondecodedata), $idquery2, add_content($jsondecodedata, $mapname)) . "," . $MapID[1];
        } else {
            echo "0,0";
            $log->debug("Error!! MIssing the history Table");
        }
    }

}

/**
 * @param DataDecode {Array} {This para is a array }
 */
function add_content($DataDecode)
{
    //$DataDecode = json_decode($dat, true);
    $countarray = (count($DataDecode) - 1);
    $xml = new DOMDocument("1.0");
    $root = $xml->createElement("map");
    $xml->appendChild($root);

    //put the mincharstosearch
    $mincharstosearch = $xml->createElement("mincharstosearch");
    $mincharstosearchtxt = $xml->createTextNode("2");
    $mincharstosearch->appendChild($mincharstosearchtxt);
    $root->appendChild($mincharstosearch);

    //put the maxresults
    $maxresults = $xml->createElement("maxresults");
    $maxresultstxt = $xml->createTextNode("10");
    $maxresults->appendChild($maxresultstxt);
    $root->appendChild($maxresults);

    //node searchin
    $searchin = $xml->createElement("searchin");

    foreach ($DataDecode as $value) {
        $searchField = "";
        $showfield = "";
        $module = $xml->createElement("module");
        $name = $xml->createElement("name");
        $nametxt = $xml->createTextNode($value->temparray->FirstModule);
        $name->appendChild($nametxt);

        foreach ($value->temparray->Firstfield as $searchFieldval) {
            $searchField .= "," . explode(":", $searchFieldval)[2];
        }

        foreach ($value->temparray->Firstfield2 as $showfieldval) {
            $showfield .= "," . explode(":", $showfieldval)[2];
        }

        $searchfields = $xml->createElement("searchfields");
        $searchfieldstxt = $xml->createTextNode(substr($searchField, 1));
        $searchfields->appendChild($searchfieldstxt);

        $searchcondition = $xml->createElement("searchcondition");
        $searchconditiontxt = $xml->createTextNode(($value->temparray->startwithchck == 1) ? "startswith" : "contains");
        $searchcondition->appendChild($searchconditiontxt);

        $showfields = $xml->createElement("showfields");
        $showfieldstxt = $xml->createTextNode(substr($showfield, 1));
        $showfields->appendChild($showfieldstxt);

        $module->appendChild($name);
        $module->appendChild($searchfields);
        $module->appendChild($searchcondition);
        $module->appendChild($showfields);

        $searchin->appendChild($module);
    }

    $root->appendChild($searchin);
    $xml->formatOutput = true;
    return $xml->saveXML();
}

function add_aray_for_history($decodedata)
{
    $Labels = "";
    $Modules = "";
    $Moduleslabels = "";
    $ModulesId = "";

    foreach ($decodedata->temparray->Firstfield as $searchFieldval) {
        $Labels .= "," . explode(":", $searchFieldval)[2];
    }

    foreach ($decodedata->temparray->Firstfield2 as $showfieldval) {
        $Labels .= "," . explode(":", $showfieldval)[2];
    }
    foreach ($decodedata as $showfieldval) {
        $Modules .= "," . $showfieldval->temparray->FirstModule;
    }
    foreach ($decodedata as $showfieldval) {
        $Moduleslabels .= "," . $showfieldval->temparray->DefaultText;
    }
    foreach ($decodedata as $showfieldval) {
        $ModulesId .= "," . getModuleID($showfieldval->temparray->FirstModule);
    }
    // return $labels;
    return array
        (
        'Labels' => substr($Labels, 1),
        'FirstModuleval' => substr($Modules, 1),
        'FirstModuletxt' => substr($Moduleslabels, 1),
        'SecondModuleval' => "",
        'SecondModuletxt' => "",
        'firstmodulelabel' => substr($ModulesId, 1),
        'secondmodulelabel' => "",
    );
}

/**
 * save history is a function which save in db the history of map
 * @param  [array] $datas   array
 * @param  [type] $queryid the id of qquery
 * @param  [type] $xmldata the xml data
 * @return [type]          boolean true or false
 */
function save_history($datas, $queryid, $xmldata)
{
    global $adb;
    $idquery2 = $queryid;
    $q = $adb->query("select sequence from " . TypeOFErrors::Tabele_name . " where id='$idquery2' order by sequence DESC");
    //$nr=$adb->num_rows($q);
    // echo "q=".$q;

    $seq = $adb->query_result($q, 0, 0);

    if (!empty($seq)) {
        $seq = $seq + 1;
        $adb->query("update " . TypeOFErrors::Tabele_name . " set active=0 where id='$idquery2'");
        //$seqmap=count($data);
        $adb->pquery("insert into " . TypeOFErrors::Tabele_name . " values (?,?,?,?,?,?,?,?,?,?,?)", array($idquery2, $datas["FirstModuleval"], $datas["FirstModuletxt"], $datas["SecondModuletxt"], $datas["SecondModuleval"], $xmldata, $seq, 1, $datas["firstmodulelabel"], $datas["secondmodulelabel"], $datas["Labels"]));
        //return $idquery;
    } else {

        $adb->pquery("insert into " . TypeOFErrors::Tabele_name . " values (?,?,?,?,?,?,?,?,?,?,?)", array($idquery2, $datas["FirstModuleval"], $datas["FirstModuletxt"], $datas["SecondModuletxt"], $datas["SecondModuleval"], $xmldata, 1, 1, $datas["firstmodulelabel"], $datas["secondmodulelabel"], $datas["Labels"]));
    }
    echo $idquery2;
}
