
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
//saveWebServiceMap.php

include_once "modules/cbMap/cbMap.php";
require_once 'data/CRMEntity.php';
require_once 'include/utils/utils.php';
require_once 'All_functions.php';
require_once 'Staticc.php';

global $root_directory, $log;
$Data = array();

$MapName = $_POST['MapName']; // stringa con tutti i campi scelti in selField1
$MapType = "WS"; // stringa con tutti i campi scelti in selField1
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
    $MapType = "WS";
}

if (!empty($Data)) {

    $jsondecodedata = json_decode($Data);

    if (strlen($MapID[1] == 0)) {
        $focust = new cbMap();
        $focust->column_fields['assigned_user_id'] = 1;
        // $focust->column_fields['mapname'] = $jsondecodedata[0]->temparray->FirstModule."_ListColumns";
        $focust->column_fields['mapname'] = $mapname;
        $focust->column_fields['content'] = add_content($jsondecodedata);
        $focust->column_fields['maptype'] = $MapType;
        $focust->column_fields['targetname'] = $jsondecodedata[0]->temparray->FirstModule;
        $focust->column_fields['description'] = add_content($jsondecodedata);
        $focust->column_fields['mvqueryid'] = $idquery2;
        $log->debug(" we inicialize value for insert in database ");
        if (!$focust->saveentity("cbMap")) //
        {

            if (Check_table_if_exist(TypeOFErrors::Tabele_name) > 0) {
                echo save_history(add_aray_for_history($jsondecodedata), $idquery2, add_content($jsondecodedata)) . "," . $focust->id;
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
        $focust->column_fields['targetname'] = $jsondecodedata[0]->temparray->FirstModule;
        $focust->column_fields['description'] = add_content($jsondecodedata);
        $focust->mode = "edit";
        $focust->save("cbMap");

        if (Check_table_if_exist(TypeOFErrors::Tabele_name) > 0) {
            echo save_history(add_aray_for_history($jsondecodedata), $idquery2, add_content($jsondecodedata)) . "," . $MapID[1];
        } else {
            echo "0,0";
            $log->debug("Error!! MIssing the history Table");
        }
    }

}

/**
 * function to convert to xml the array come from post
 *
 * @param      <type>  $DataDecode  The data decode
 * @param      DataDecode  {Array}  {This para is a array }
 *
 * @return     <type>  ( description_of_the_return_value )
 */
function add_content($DataDecode)
{
    $countarray = (count($DataDecode) - 1);
    $configuration = false;
    $Header = false;
    $Input = false;
    $Output = false;
    $ValueMap = false;
    $Errorhandler = false;
    $xml = new DOMDocument("1.0");
    $root = $xml->createElement("map");
    $xml->appendChild($root);

    // for configuration
    $wsconfigtag = $xml->createElement("wsconfig");
    foreach ($DataDecode as $value) {

        if ($value->temparray->JsonType == "Configuration") {

            $wsurl = $xml->createElement("wsurl");
            $wsurltext = $xml->createTextNode($value->temparray->{'fixed-text-addon-pre'} . $value->temparray->{'url-input'});
            $wsurl->appendChild($wsurltext);
            $wsconfigtag->appendChild($wsurl);

            $wshttpmethod = $xml->createElement("wshttpmethod");
            $wshttpmethodtext = $xml->createTextNode($value->temparray->urlMethod);
            $wshttpmethod->appendChild($wshttpmethodtext);
            $wsconfigtag->appendChild($wshttpmethod);

            $wsresponsetime = $xml->createElement("wsresponsetime");
            $wsresponsetimeText = $xml->createTextNode($value->temparray->{'ws-response-time'});
            $wsresponsetime->appendChild($wsresponsetimeText);
            $wsconfigtag->appendChild($wsresponsetime);

            $wsuser = $xml->createElement("wsuser");
            $wsuserText = $xml->createTextNode($value->temparray->{'ws-user'});
            $wsuser->appendChild($wsuserText);
            $wsconfigtag->appendChild($wsuser);

            $wspass = $xml->createElement("wspass");
            $wspassText = $xml->createTextNode($value->temparray->{'ws-password'});
            $wspass->appendChild($wspassText);
            $wsconfigtag->appendChild($wspass);

            $wsproxyhost = $xml->createElement("wsproxyhost");
            $wsproxyhostText = $xml->createTextNode($value->temparray->{'ws-proxy-host'});
            $wsproxyhost->appendChild($wsproxyhostText);
            $wsconfigtag->appendChild($wsproxyhost);

            $wsproxyport = $xml->createElement("wsproxyport");
            $wsproxyportText = $xml->createTextNode($value->temparray->{'ws-proxy-port'});
            $wsproxyport->appendChild($wsproxyportText);
            $wsconfigtag->appendChild($wsproxyport);

            $wsstarttag = $xml->createElement("wsstarttag");
            $wsstarttagText = $xml->createTextNode($value->temparray->{'ws-start-tag'});
            $wsstarttag->appendChild($wsstarttagText);
            $wsconfigtag->appendChild($wsstarttag);

            $wstype = $xml->createElement("wstype");
            $wstypeText = $xml->createTextNode("");
            $wstype->appendChild($wstypeText);
            $wsconfigtag->appendChild($wstype);

            $inputtype = $xml->createElement("inputtype");
            $inputtypeText = $xml->createTextNode($value->temparray->{'ws-input-type'});
            $inputtype->appendChild($inputtypeText);
            $wsconfigtag->appendChild($inputtype);

            $outputtype = $xml->createElement("outputtype");
            $outputtypeText = $xml->createTextNode($value->temparray->{'ws-output-type'});
            $outputtype->appendChild($outputtypeText);
            $wsconfigtag->appendChild($outputtype);

            $configuration = true;
        }
    }
    //for header
    $wsheadertag = $xml->createElement("wsheader");
    foreach ($DataDecode as $value) {

        if ($value->temparray->JsonType == "Header") {
            $headertag = $xml->createElement("header");

            $keyname = $xml->createElement("keyname");
            $keynameText = $xml->createTextNode($value->temparray->{'ws-key-name'});
            $keyname->appendChild($keynameText);
            $headertag->appendChild($keyname);

            $keyvalue = $xml->createElement("keyvalue");
            $keyvalueText = $xml->createTextNode($value->temparray->{'ws-key-value'});
            $keyvalue->appendChild($keyvalueText);
            $headertag->appendChild($keyvalue);

            $wsheadertag->appendChild($headertag);

            $Header = true;
        }
    }
    if ($Header == true) {$wsconfigtag->appendChild($wsheadertag);}

    // for input
    $inputtag = $xml->createElement("input");
    $fieldstag = $xml->createElement("fields");
    foreach ($DataDecode as $value) {

        if ($value->temparray->JsonType == "Input") {
            $fieldtag = $xml->createElement("field");

            $fieldname = $xml->createElement("fieldname");
            $fieldnameText = $xml->createTextNode($value->temparray->{'ws-input-name'});
            $fieldname->appendChild($fieldnameText);
            $fieldtag->appendChild($fieldname);

            if (strcmp($value->temparray->FirstModule, $value->temparray->{'ws-select-multipleoptionGroup'}) == 0) {
                $concatstring = "";
                if (!empty($value->temparray->{'ws-input-static'})) {$concatstring .= $value->temparray->{'ws-input-static'} . ",";}
                foreach ($value->temparray->Anotherdata as $values) {
                    $concatstring .= explode(":", $values->DataValues)[2] . ",";
                }
                $fieldvalue = $xml->createElement("fieldvalue");
                $fieldvalueText = $xml->createTextNode(substr($concatstring, 0, -1));
                $fieldvalue->appendChild($fieldvalueText);
                $fieldtag->appendChild($fieldvalue);
            } else {
                $concatstringrel = "";
                if (!empty($value->temparray->{'ws-input-static'})) {$concatstringrel .= $value->temparray->{'ws-input-static'} . ",";}
                foreach ($value->temparray->Anotherdata as $valuea) {
                    $concatstringrel .= explode(":", $valuea->DataValues)[2] . ",";
                }
                $Relfield = $xml->createElement("Relfield");
                $RelfieldName = $xml->createElement("RelfieldName");
                $RelfieldNameText = $xml->createTextNode(substr($concatstringrel, 0, -1));
                $RelfieldName->appendChild($RelfieldNameText);
                $Relfield->appendChild($RelfieldName);
                $RelModule = $xml->createElement("RelModule");
                $RelModuleText = $xml->createTextNode($value->temparray->{'ws-select-multipleoptionGroup'});
                $RelModule->appendChild($RelModuleText);
                $Relfield->appendChild($RelModule);
                $linkfield = $xml->createElement("linkfield");
                $linkfieldText = $xml->createTextNode(getModuleID($value->temparray->{'ws-select-multipleoptionGroup'}));
                $linkfield->appendChild($linkfieldText);
                $Relfield->appendChild($linkfield);
                $fieldtag->appendChild($Relfield);

                $fieldvalue = $xml->createElement("fieldvalue");
                $fieldvalueText = $xml->createTextNode(substr($concatstringrel, 0, -1));
                $fieldvalue->appendChild($fieldvalueText);
                $fieldtag->appendChild($fieldvalue);
            }

            $attribute = $xml->createElement("attribute");
            $attributeText = $xml->createTextNode($value->temparray->{'ws-input-attribute'});
            $attribute->appendChild($attributeText);
            $fieldtag->appendChild($attribute);

            $origin = $xml->createElement("origin");
            $originText = $xml->createTextNode($value->temparray->{'ws-input-Origin'});
            $origin->appendChild($originText);
            $fieldtag->appendChild($origin);

            $format = $xml->createElement("format");
            $formatText = $xml->createTextNode($value->temparray->{'ws-input-format'});
            $format->appendChild($formatText);
            $fieldtag->appendChild($format);

            $default = $xml->createElement("default");
            $defaultText = $xml->createTextNode($value->temparray->{'ws-input-default'});
            $default->appendChild($defaultText);
            $fieldtag->appendChild($default);

            $fieldstag->appendChild($fieldtag);

            $Input = true;
        }
    }
    $inputtag->appendChild($fieldstag);

    /// output tag

    $Outputtag = $xml->createElement("Output");
    $fieldstag = $xml->createElement("fields");
    foreach ($DataDecode as $value) {

        if ($value->temparray->JsonType == "Output") {
            $fieldtag = $xml->createElement("field");

            $fieldlabel = $xml->createElement("fieldlabel");
            $fieldlabelText = $xml->createTextNode($value->temparray->{'ws-label'});
            $fieldlabel->appendChild($fieldlabelText);
            $fieldtag->appendChild($fieldlabel);

            $fieldname = $xml->createElement("fieldname");
            $fieldnameText = $xml->createTextNode($value->temparray->{'ws-output-name'});
            $fieldname->appendChild($fieldnameText);
            $fieldtag->appendChild($fieldname);

            if (strcmp($value->temparray->FirstModule, $value->temparray->{'ws-output-select-multipleoptionGroup'}) == 0) {
                $concatstring = "";
                if (!empty($value->temparray->{'ws-output-static'})) {$concatstring .= $value->temparray->{'ws-output-static'} . ",";}
                foreach ($value->temparray->Anotherdata as $values) {
                    $concatstring .= explode(":", $values->DataValues)[2] . ",";
                }
                $fieldvalue = $xml->createElement("fieldvalue");
                $fieldvalueText = $xml->createTextNode(substr($concatstring, 0, -1));
                $fieldvalue->appendChild($fieldvalueText);
                $fieldtag->appendChild($fieldvalue);
            } else {
                $concatstringrel = "";
                if (!empty($value->temparray->{'ws-output-static'})) {$concatstringrel .= $value->temparray->{'ws-output-static'} . ",";}
                foreach ($value->temparray->Anotherdata as $valuea) {
                    $concatstringrel .= explode(":", $valuea->DataValues)[2] . ",";
                }
                $Relfield = $xml->createElement("Relfield");
                $RelfieldName = $xml->createElement("RelfieldName");
                $RelfieldNameText = $xml->createTextNode(substr($concatstringrel, 0, -1));
                $RelfieldName->appendChild($RelfieldNameText);
                $Relfield->appendChild($RelfieldName);
                $RelModule = $xml->createElement("RelModule");
                $RelModuleText = $xml->createTextNode($value->temparray->{'ws-output-select-multipleoptionGroup'});
                $RelModule->appendChild($RelModuleText);
                $Relfield->appendChild($RelModule);
                $linkfield = $xml->createElement("linkfield");
                $linkfieldText = $xml->createTextNode(getModuleID($value->temparray->{'ws-output-select-multipleoptionGroup'}));
                $linkfield->appendChild($linkfieldText);
                $Relfield->appendChild($linkfield);
                $fieldtag->appendChild($Relfield);

                $fieldvalue = $xml->createElement("fieldvalue");
                $fieldvalueText = $xml->createTextNode(substr($concatstringrel, 0, -1));
                $fieldvalue->appendChild($fieldvalueText);
                $fieldtag->appendChild($fieldvalue);
            }

            $attribute = $xml->createElement("attribute");
            $attributeText = $xml->createTextNode($value->temparray->{'ws-output-attribute'});
            $attribute->appendChild($attributeText);
            $fieldtag->appendChild($attribute);

            $fieldstag->appendChild($fieldtag);

            $Output = true;
        }
    }
    $Outputtag->appendChild($fieldstag);

    /// Value Map tag

    $valuemaptag = $xml->createElement("valuemap");
    $fieldstag = $xml->createElement("fields");
    foreach ($DataDecode as $value) {

        if ($value->temparray->JsonType == "Value Map") {
            $fieldtag = $xml->createElement("field");

            $fieldname = $xml->createElement("fieldname");
            $fieldnameText = $xml->createTextNode($value->temparray->{'ws-value-map-name'});
            $fieldname->appendChild($fieldnameText);
            $fieldtag->appendChild($fieldname);

            $fieldsrc = $xml->createElement("fieldsrc");
            $fieldsrcText = $xml->createTextNode($value->temparray->{'ws-value-map-source-input'});
            $fieldsrc->appendChild($fieldsrcText);
            $fieldtag->appendChild($fieldsrc);

            $fielddest = $xml->createElement("fielddest");
            $fielddestText = $xml->createTextNode($value->temparray->{'ws-value-map-destinamtion'});
            $fielddest->appendChild($fielddestText);
            $fieldtag->appendChild($fielddest);

            $fieldstag->appendChild($fieldtag);

            $ValueMap = true;
        }
    }
    $valuemaptag->appendChild($fieldstag);

    /// Error Handler tag

    $errorhandlertag = $xml->createElement("errorhandler");
    foreach ($DataDecode as $value) {

        if ($value->temparray->JsonType == "Error Handler") {
            $fieldtag = $xml->createElement("field");

            $fieldname = $xml->createElement("fieldname");
            $fieldnameText = $xml->createTextNode($value->temparray->{'ws-error-name'});
            $fieldname->appendChild($fieldnameText);
            $fieldtag->appendChild($fieldname);

            $valueg = $xml->createElement("value");
            $valueText = $xml->createTextNode($value->temparray->{'ws-error-value'});
            $valueg->appendChild($valueText);
            $fieldtag->appendChild($valueg);

            $errormessage = $xml->createElement("errormessage");
            $errormessageText = $xml->createTextNode($value->temparray->{'ws-error-message'});
            $errormessage->appendChild($errormessageText);
            $fieldtag->appendChild($errormessage);

            $errorhandlertag->appendChild($fieldtag);
            $Errorhandler = true;
        }
    }
    $root->appendChild($wsconfigtag);
    if ($Input == true) {$root->appendChild($inputtag);}
    if ($Output == true) {$root->appendChild($Outputtag);}
    if ($ValueMap == true) {$root->appendChild($valuemaptag);}
    if ($Errorhandler == true) {$root->appendChild($errorhandlertag);}
    $xml->formatOutput = true;
    return $xml->saveXML();
}

function add_aray_for_history($decodedata)
{
    return array
        (
        'Labels' => "",
        'FirstModuleval' => preg_replace('/\s+/', '', $decodedata[0]->temparray->FirstModule),
        'FirstModuletxt' => preg_replace('/\s+/', '', $decodedata[0]->temparray->FirstModuleText),
        'SecondModuleval' => "",
        'SecondModuletxt' => "",
        'firstmodulelabel' => getModuleID(preg_replace('/\s+/', '', $decodedata[0]->temparray->FirstModule)),
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
