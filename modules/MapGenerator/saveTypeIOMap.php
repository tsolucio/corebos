<?php
/*
 * @Author: Edmond Kacaj
 * @Date: 2018-09-11 11:54:44
 * @Last Modified by: edmondikacaj@gmail.com
 * @Last Modified time: 2018-09-11 11:59:04
 */
//saveModuleSet.php

include_once "modules/cbMap/cbMap.php";
require_once 'data/CRMEntity.php';
require_once 'include/utils/utils.php';
require_once 'All_functions.php';
include_once 'Staticc.php';

global $root_directory, $log;
$Data = array();

$MapName = $_POST['MapName']; // stringa con tutti i campi scelti in selField1
$MapType = "IOMap"; // stringa con tutti i campi scelti in selField1
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
    $MapType = "Module Set Mapping";
}

if (!empty($Data)) {

    $jsondecodedata = json_decode($Data);

    if (strlen($MapID[1]) == 0) {

        $focust = new cbMap();
        $focust->column_fields['assigned_user_id'] = 1;
        // $focust->column_fields['mapname'] = $jsondecodedata[0]->temparray->FirstModule."_ListColumns";
        $focust->column_fields['mapname'] = $mapname;
        $focust->column_fields['content'] = add_content($jsondecodedata);
        $focust->column_fields['maptype'] = $MapType;
        // $focust->column_fields['selected_fields']=add_aray_for_history($jsondecodedata);
        // $focust->column_fields['targetname'] =$jsondecodedata[0]->temparray->FirstModule;
        $focust->column_fields['description'] = add_description($jsondecodedata);
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
            // echo "Edmondi save in map,hghghghghgh";
            //   exit();
            //echo focus->id;
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
        $focust->column_fields['selected_fields'] = add_aray_for_history($jsondecodedata)['Labels'];
        $focust->column_fields['content'] = add_content($jsondecodedata);
        $focust->column_fields['maptype'] = $MapType;
        $focust->column_fields['mvqueryid'] = $idquery2;
        // $focust->column_fields['targetname'] =$jsondecodedata[0]->temparray->FirstModule;
        $focust->column_fields['description'] = add_description($jsondecodedata);
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
 * Adds a content.
 *
 * @param      <type>  $DataDecode  The data decode
 *
 * @return     <type>  ( return the xml )
 */
function add_content($DataDecode)
{
    //$DataDecode = json_decode($dat, true);
    $countarray = (count($DataDecode) - 1);
    $xml = new DOMDocument("1.0");
    $root = $xml->createElement("map");
    $xml->appendChild($root);
    $input = $xml->createElement("input");
    $fields = $xml->createElement("fields");
    for ($i = 0; $i <= $countarray; $i++) {
        if ($DataDecode[$i]->temparray->JsonType === "Input") {
            $field = $xml->createElement("field");
            $fieldname = $xml->createElement("fieldname");
            if (strlen($DataDecode[$i]->temparray->AllFieldsInput) > 0) {
                $fieldnameText = $xml->createTextNode(explode(":", $DataDecode[$i]->temparray->AllFieldsInput)[2]);
            } else {
                $fieldnameText = $xml->createTextNode($DataDecode[$i]->temparray->AllFieldsInputByhand);
            }

            $fieldname->appendChild($fieldnameText);

            $field->appendChild($fieldname);
            $fields->appendChild($field);
        }

    }
    $input->appendChild($fields);
    //for output
    //
    $output = $xml->createElement("output");
    $outputfields = $xml->createElement("fields");
    for ($i = 0; $i <= $countarray; $i++) {
        if ($DataDecode[$i]->temparray->JsonType === "Output") {
            $field = $xml->createElement("field");
            $fieldname = $xml->createElement("fieldname");
            if (strlen($DataDecode[$i]->temparray->AllFieldsOutputselect) > 0) {
                $fieldnameText = $xml->createTextNode(explode(":", $DataDecode[$i]->temparray->AllFieldsOutputselect)[2]);
            } else {
                $fieldnameText = $xml->createTextNode($DataDecode[$i]->temparray->AllFieldsOutputbyHand);
            }

            $fieldname->appendChild($fieldnameText);

            $field->appendChild($fieldname);
            $outputfields->appendChild($field);
        }

    }

    $output->appendChild($outputfields);

    $root->appendChild($input);
    $root->appendChild($output);
    $xml->formatOutput = true;
    return $xml->saveXML();
}

/**
 * Adds a description.
 *
 * @param      <type>  $DataDecode  The data decode
 *
 * @return     <type>  ( description_of_the_return_value )
 */
function add_description($DataDecode)
{

    //$DataDecode = json_decode($datades, true);
    $countarray = (count($DataDecode) - 1);

    $xml = new DOMDocument("1.0");
    $root = $xml->createElement("map");
    $xml->appendChild($root);
    $fields = $xml->createElement("fields");
    for ($i = 0; $i <= $countarray; $i++) {
        if ($DataDecode[$i]->temparray->JsonType === "Input") {
            $field = $xml->createElement("field");
            $fieldname = $xml->createElement("fieldname");
            if (strlen($DataDecode[$i]->temparray->AllFieldsInput) > 0) {
                $fieldnameText = $xml->createTextNode(explode(":", $DataDecode[$i]->temparray->AllFieldsInput)[2]);
            } else {
                $fieldnameText = $xml->createTextNode($DataDecode[$i]->temparray->AllFieldsInputByhand);
            }

            $fieldname->appendChild($fieldnameText);

            $field->appendChild($fieldname);
            $fields->appendChild($field);
        }

    }
    for ($i = 0; $i <= $countarray; $i++) {
        if ($DataDecode[$i]->temparray->JsonType === "Output") {
            $field = $xml->createElement("field");
            $fieldname = $xml->createElement("fieldname");
            if (strlen($DataDecode[$i]->temparray->AllFieldsOutputselect) > 0) {
                $fieldnameText = $xml->createTextNode(explode(":", $DataDecode[$i]->temparray->AllFieldsOutputselect)[2]);
            } else {
                $fieldnameText = $xml->createTextNode($DataDecode[$i]->temparray->AllFieldsOutputbyHand);
            }

            $fieldname->appendChild($fieldnameText);

            $field->appendChild($fieldname);
            $fields->appendChild($field);
        }

    }
    $root->appendChild($fields);
    $xml->formatOutput = true;
    return $xml->saveXML();
}

/**
 * Adds an aray for history.
 *
 * @param      <type>  $decodedata  The decodedata is the array come from post
 *
 * @return     <type>  ( description_of_the_return_value )
 */
function add_aray_for_history($decodedata)
{
    //$countarray=(count($decodedata)-1);
    $labels = "";
    foreach ($decodedata as $value) {
        if ($value->temparray->JsonType === "Input") {
            if (!empty($value->temparray->AllFieldsInput)) {
                $labels .= $value->temparray->Moduli . "#" . explode(":", $value->temparray->AllFieldsInput)[2] . ",";
            } else {
                $labels .= $value->temparray->AllFieldsInputByhand . ",";
            }
        } else {
            if (!empty($value->temparray->AllFieldsOutputselect)) {
                $labels .= $value->temparray->Moduli . "#" . explode(":", $value->temparray->AllFieldsOutputselect)[2] . ",";
            } else {
                $labels .= $value->temparray->AllFieldsOutputbyHand . ",";
            }
        }

    }
    // return $labels;
    return array
        (
        'Labels' => $labels,
        'FirstModuleval' => " ",
        'FirstModuletxt' => " ",
        'SecondModuleval' => " ",
        'SecondModuletxt' => " ",
        'firstmodulelabel' => " ",
        'secondmodulelabel' => " ",
    );
}

/**
 * Saves a history.
 *
 * @param      string  $datas    The datas
 * @param      <type>  $queryid  The queryid
 * @param      <type>  $xmldata  The xmldata
 */
function save_history($datas, $queryid, $xmldata)
{
    global $adb;
    $idquery = $queryid;

    $q = $adb->query("select sequence from " . TypeOFErrors::Tabele_name . " where id='$idquery' order by sequence DESC");
    $seq = $adb->query_result($q, 0, 0);
    if (!empty($seq)) {
        $seq = $seq + 1;
        $adb->query("update " . TypeOFErrors::Tabele_name . " set active=0 where id='$idquery'");
        //$seqmap=count($data);
        $adb->pquery("insert into " . TypeOFErrors::Tabele_name . " values (?,?,?,?,?,?,?,?,?,?,?)", array($idquery, $datas["FirstModuleval"], $datas["FirstModuletxt"], $datas["SecondModuletxt"], $datas["SecondModuleval"], $xmldata, $seq, 1, $datas["firstmodulelabel"], $datas["secondmodulelabel"], $datas["Labels"]));
        //return $idquery;
    } else {

        //$idquery=md5(date("Y-m-d H:i:s").uniqid(rand(), true));
        $adb->pquery("insert into " . TypeOFErrors::Tabele_name . " values (?,?,?,?,?,?,?,?,?,?,?)", array($idquery, $datas["FirstModuleval"], $datas["FirstModuletxt"], $datas["SecondModuletxt"], $datas["SecondModuleval"], $xmldata, 1, 1, $datas["firstmodulelabel"], $datas["secondmodulelabel"], $datas["Labels"]));
    }
    echo $idquery;
}
