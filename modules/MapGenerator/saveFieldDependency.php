<?php
/*
 * @Author: Edmond Kacaj
 * @Date: 2018-09-11 11:54:44
 * @Last Modified by: edmondikacaj@gmail.com
 * @Last Modified time: 2018-09-11 15:01:57
 */
//saveFieldDependency.php

include_once "modules/cbMap/cbMap.php";
require_once 'data/CRMEntity.php';
require_once 'include/utils/utils.php';
require_once 'All_functions.php';
require_once 'Staticc.php';

global $root_directory, $log;
$Data = array();

$MapName = $_POST['MapName']; // stringa con tutti i campi scelti in selField1
$MapType = "FieldDependency"; // stringa con tutti i campi scelti in selField1
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
    $MapType = "FieldDependency";
}

if (!empty($Data)) {

    $jsondecodedata = json_decode($Data);
    $myDetails = array();
    // print_r($jsondecodedata);
    //    echo add_content($jsondecodedata);
    //    exit();

    if (strlen($MapID[1] == 0)) {

        $focust = new cbMap();
        $focust->column_fields['assigned_user_id'] = 1;
        // $focust->column_fields['mapname'] = $jsondecodedata[0]->temparray->FirstModule."_ListColumns";
        $focust->column_fields['mapname'] = $mapname;
        $focust->column_fields['content'] = add_content($jsondecodedata, $mapname);
        $focust->column_fields['maptype'] = $MapType;
        $focust->column_fields['targetname'] = $jsondecodedata[0]->temparray->FirstModule;
        $focust->column_fields['description'] = add_content($jsondecodedata, $mapname);
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
        $focust->column_fields['content'] = add_content($jsondecodedata);
        $focust->column_fields['maptype'] = $MapType;
        $focust->column_fields['mvqueryid'] = $idquery2;
        $focust->column_fields['targetname'] = $jsondecodedata[0]->temparray->FirstModule;
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
function add_content($DataDecode, $mapname)
{
    //$DataDecode = json_decode($dat, true);
    $countarray = (count($DataDecode) - 1);
    $xml = new DOMDocument("1.0");
    $root = $xml->createElement("map");
    $xml->appendChild($root);

    //put the map name
    $name = $xml->createElement("name");
    $nametxt = $xml->createTextNode($mapname);
    $name->appendChild($nametxt);
    $root->appendChild($name);

    // put the target module
    $target = $xml->createElement("targetmodule");
    $targetid = $xml->createElement("targetid");
    $targetidText = $xml->createTextNode(getModuleID($DataDecode[0]->temparray->FirstModule, "tabid"));
    $targetid->appendChild($targetidText);
    $targetname = $xml->createElement("targetname");
    $targetnameText = $xml->createTextNode(trim($DataDecode[0]->temparray->FirstModule));
    $targetname->appendChild($targetnameText);
    $target->appendChild($targetid);
    $target->appendChild($targetname);
    $root->appendChild($target);
    // put the origin module
    $originmodule = $xml->createElement("originmodule");
    $originmoduleid = $xml->createElement("originid");
    $originmoduleidText = $xml->createTextNode(getModuleID($DataDecode[0]->temparray->FirstModule, "tabid"));
    $originmoduleid->appendChild($originmoduleidText);
    $originmodulename = $xml->createElement("originname");
    $originmoduleText = $xml->createTextNode(trim($DataDecode[0]->temparray->FirstModule));
    $originmodulename->appendChild($originmoduleText);
    $originmodule->appendChild($originmoduleid);
    $originmodule->appendChild($originmodulename);
    $root->appendChild($originmodule);

    //put the fields
    $fields = $xml->createElement("fields");

    $field = $xml->createElement("field");

    $fieldid = $xml->createElement("fieldid");
    $fieldidtxt = $xml->createTextNode(getModuleID($DataDecode[0]->temparray->FirstModule));
    $fieldid->appendChild($fieldidtxt);
    $field->appendChild($fieldid);

    //put first the responsibile fields
    foreach ($DataDecode as $value) {

        if ($value->temparray->JsonType === "Responsible") {

            $Responsiblefield = $xml->createElement("Responsiblefield");

            $fieldname = $xml->createElement("fieldname");
            $fieldnameText = $xml->createTextNode(explode(":", $value->temparray->Firstfield)[2]);
            $fieldname->appendChild($fieldnameText);
            $Responsiblefield->appendChild($fieldname);

            if (!empty($value->temparray->DefaultValueResponsibel)) {
                $fieldvalue = $xml->createElement("fieldvalue");
                $fieldvalueText = $xml->createTextNode($value->temparray->DefaultValueResponsibel);
                $fieldvalue->appendChild($fieldvalueText);
                $Responsiblefield->appendChild($fieldvalue);
            }

            $comparison = $xml->createElement("comparison");
            $comparisonText = $xml->createTextNode($value->temparray->Conditionalfield);
            $comparison->appendChild($comparisonText);

            $Responsiblefield->appendChild($comparison);

            $field->appendChild($Responsiblefield);

        }
    }

    //  //put the Fields
    foreach ($DataDecode as $value) {

        if ($value->temparray->JsonType === "Field") {
            $Orgfield = $xml->createElement("Orgfield");

            $Orgfieldfieldname = $xml->createElement("fieldname");
            $OrgfieldfieldnameText = $xml->createTextNode(explode(":", $value->temparray->Firstfield2)[2]);
            $Orgfieldfieldname->appendChild($OrgfieldfieldnameText);
            $Orgfield->appendChild($Orgfieldfieldname);

            //  $fieldactionShohide = $xml->createElement("fieldaction");
            //  $fieldactionShohideText = $xml->createTextNode(($value->temparray->fieldaction===0)?"hide":"show");
            //  $fieldactionShohide->appendChild($fieldactionShohideText);
            //  $Orgfield->appendChild($fieldactionShohide);

            if ($value->temparray->Readonlycheck != 0) {
                $fieldactionreadonly = $xml->createElement("fieldaction");
                $fieldactionreadonlyText = $xml->createTextNode("readonly");
                $fieldactionreadonly->appendChild($fieldactionreadonlyText);
                $Orgfield->appendChild($fieldactionreadonly);
            } else {
                if ($value->temparray->ShowHidecheck != 0) {
                    $fieldactionreadonly = $xml->createElement("fieldaction");
                    $fieldactionreadonlyText = $xml->createTextNode("hide");
                    $fieldactionreadonly->appendChild($fieldactionreadonlyText);
                    $Orgfield->appendChild($fieldactionreadonly);
                } else {
                    $fieldactionreadonly = $xml->createElement("fieldaction");
                    $fieldactionreadonlyText = $xml->createTextNode("show");
                    $fieldactionreadonly->appendChild($fieldactionreadonlyText);
                    $Orgfield->appendChild($fieldactionreadonly);
                }
            }

            $Orgfieldfieldvalue = $xml->createElement("fieldvalue");
            $OrgfieldfieldvalueText = $xml->createTextNode("");
            $Orgfieldfieldvalue->appendChild($OrgfieldfieldvalueText);
            $Orgfield->appendChild($Orgfieldfieldvalue);

            if ($value->temparray->mandatorychk != 0) {
                $mandatory = $xml->createElement("mandatory");
                $mandatoryText = $xml->createTextNode("mandatory");
                $mandatory->appendChild($mandatoryText);
                $Orgfield->appendChild($mandatory);
            }

            $field->appendChild($Orgfield);

        }
    }

    //  //put the Picklist
    foreach ($DataDecode as $value) {

        if ($value->temparray->JsonType === "Picklist") {
            $Picklist = $xml->createElement("Picklist");

            $Picklistname = $xml->createElement("fieldname");
            $PicklistnameText = $xml->createTextNode(explode(':', $value->temparray->PickListFields)[2]);
            $Picklistname->appendChild($PicklistnameText);
            $Picklist->appendChild($Picklistname);
            $i = 1;
            foreach ($value->temparray as $key => $valua) {
                if (!empty($valua)) {
                    preg_match_all('!\d+!', $key, $matches);
                    $stringtocheck = "DefaultValueFirstModuleField_" . $matches[0][0];
                    if ($key === $stringtocheck) {
                        $values = $xml->createElement("values");
                        $valuesText = $xml->createTextNode($valua);
                        $values->appendChild($valuesText);
                        $Picklist->appendChild($values);
                    }
                }
            }

            $field->appendChild($Picklist);

        }
    }

    $fields->appendChild($field);
    $root->appendChild($fields);
    $xml->formatOutput = true;
    return $xml->saveXML();
}

function add_aray_for_history($decodedata)
{

    // return $labels;
    return array
        (
        'Labels' => "",
        'FirstModuleval' => preg_replace('/\s+/', '', $decodedata[0]->temparray->FirstModule),
        'FirstModuletxt' => preg_replace('/\s+/', '', $decodedata[0]->temparray->FirstModule),
        'SecondModuleval' => preg_replace('/\s+/', '', $decodedata[0]->temparray->FirstModule),
        'SecondModuletxt' => preg_replace('/\s+/', '', $decodedata[0]->temparray->FirstModule),
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
