<?php

include_once "modules/cbMap/cbMap.php";
require_once 'data/CRMEntity.php';
require_once 'include/utils/utils.php';
require_once 'All_functions.php';
include_once 'Staticc.php';

global $root_directory, $log;
$Data = array();

//  var_dump($_REQUEST, true);
// exit();

$MapName = $_POST['MapName']; // stringa con tutti i campi scelti in selField1
$SaveasMapText = $_POST['SaveasMapText'];
$MapType = "MasterDetailLayout"; // stringa con tutti i campi scelti in selField1
// $Data = $_POST['alldata'];
$Data = $_POST['ListData'];
$MapID = explode(',', $_REQUEST['savehistory']);
$mapname = (!empty($SaveasMapText) ? $SaveasMapText : $MapName);
$idquery = !empty($MapID[0]) ? $MapID[0] : md5(date("Y-m-d H:i:s") . uniqid(rand(), true));

if (empty($SaveasMapText)) {
    if (empty($MapName)) {
        echo "Missing the name of map Can't save";
        return;
    }
}
if (empty($MapType)) {
    $MapType = "MasterDetailLayout";
}

if (!empty($Data)) {

    $jsondecodedata = json_decode($Data);

    // print_r(save_history(add_aray_for_history($jsondecodedata),$idquery,""));
    // exit();
    // include_once('modules/cbMap/cbMap.php');
    if (strlen($MapID[1] == 0)) {
        include_once "modules/cbMap/cbMap.php";
        $focust = new cbMap();
        $focust->column_fields['assigned_user_id'] = 1;
        $focust->column_fields['mapname'] = $mapname;
        $focust->column_fields['content'] = add_content($jsondecodedata);
        $focust->column_fields['maptype'] = $MapType;
        $focust->column_fields['targetname'] = $jsondecodedata[0]->temparray->FirstModule;
        $focust->column_fields['description'] = add_description($jsondecodedata);
        $focust->column_fields['mvqueryid'] = $idquery;
        $log->debug(" we inicialize value for insert in database ");
        if (!$focust->saveentity("cbMap")) //
        {

            if (Check_table_if_exist(TypeOFErrors::Tabele_name) > 0) {
                echo save_history(add_aray_for_history($jsondecodedata), $idquery, add_content($jsondecodedata)) . "," . $focust->id;
            } else {
                echo "0,0";
                $log->debug("Error!! MIssing the history Table");
            }

        } else {
            // echo "Edmondi save in map,hghghghghgh";
            exit();
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
        $focust->column_fields['mvqueryid'] = $idquery;
        $focust->column_fields['targetname'] = $jsondecodedata[0]->temparray->FirstModule;
        $focust->column_fields['description'] = add_description($jsondecodedata);
        $focust->mode = "edit";
        $focust->save("cbMap");

        if (Check_table_if_exist(TypeOFErrors::Tabele_name) > 0) {
            echo save_history(add_aray_for_history($jsondecodedata), $idquery, add_content($jsondecodedata)) . "," . $MapID[1];
        } else {
            echo "0,0";
            $log->debug("Error!! MIssing the history Table");
        }
    }

}

//
function add_content($DataDecode)
{
    //$DataDecode = json_decode($dat, true);
    $countarray = (count($DataDecode) - 1);
    $xml = new DOMDocument("1.0");
    $root = $xml->createElement("map");
    $xml->appendChild($root);

    $originmodule = $xml->createElement("originmodule");
    $originmoduletxt = $xml->createTextNode(explode(";", $DataDecode[0]->temparray->secmodule)[0]);
    $originmodule->appendChild($originmoduletxt);
    $root->appendChild($originmodule);

    $target = $xml->createElement("targetmodule");
    $targettxt = $xml->createTextNode($DataDecode[0]->temparray->FirstModule);
    $target->appendChild($targettxt);
    $root->appendChild($target);

    $linkfields = $xml->createElement("linkfields");

    $OrgRelfieldName = $xml->createElement("originfield");
    $OrgRelfieldNameText = $xml->createTextNode($DataDecode[0]->temparray->SecondfieldID);
    $OrgRelfieldName->appendChild($OrgRelfieldNameText);
    $linkfields->appendChild($OrgRelfieldName);
    // $root->appendChild($linkfields);

    $targetfield = $xml->createElement("targetfield");
    $targetfieldText = $xml->createTextNode($DataDecode[0]->temparray->FirstfieldID);
    $targetfield->appendChild($targetfieldText);
    $linkfields->appendChild($targetfield);

    $root->appendChild($linkfields);

    foreach ($DataDecode as $value) {
        if ($value->temparray->sortt6ablechk === "1") {

            $sortfield = $xml->createElement("sortfield");
            $sortfieldtxt = $xml->createTextNode(explode(":", $value->temparray->Firstfield)[2]);
            $sortfield->appendChild($sortfieldtxt);
            $root->appendChild($sortfield);

        }
    }

    $detailview = $xml->createElement("detailview");

    $fields = $xml->createElement("fields");

    foreach ($DataDecode as $value) {

        $field = $xml->createElement("field");
        $fieldtype = $xml->createElement("fieldtype");
        $fieldtypeText = $xml->createTextNode("corebos");
        $fieldtype->appendChild($fieldtypeText);
        $field->appendChild($fieldtype);

        $fieldname = $xml->createElement("fieldname");
        $fieldnameText = $xml->createTextNode(explode(":", $value->temparray->Firstfield)[2]);
        $fieldname->appendChild($fieldnameText);
        $field->appendChild($fieldname);

        $editable = $xml->createElement("editable");
        $editableText = $xml->createTextNode($value->temparray->editablechk);
        $editable->appendChild($editableText);
        $field->appendChild($editable);

        $mandatory = $xml->createElement("mandatory");
        $mandatoryText = $xml->createTextNode($value->temparray->mandatorychk);
        $mandatory->appendChild($mandatoryText);
        $field->appendChild($mandatory);

        $hidden = $xml->createElement("hidden");
        $hiddenText = $xml->createTextNode($value->temparray->hiddenchk);
        $hidden->appendChild($hiddenText);
        $field->appendChild($hidden);

        $fields->appendChild($field);

    }
    $detailview->appendChild($fields);
    $root->appendChild($detailview);
    $xml->formatOutput = true;
    return $xml->saveXML();

}

function add_description($DataDecode)
{

    //$DataDecode = json_decode($datades, true);
    $countarray = (count($DataDecode) - 1);

    $xml = new DOMDocument("1.0");
    $root = $xml->createElement("map");
    $xml->appendChild($root);
    //strt create the first module
    $Fmodule = $xml->createElement("Fmodule");

    $Fmoduleid = $xml->createElement("FmoduleID");
    $FmoduleText = $xml->createTextNode("");
    $Fmoduleid->appendChild($FmoduleText);

    $Fmodulename = $xml->createElement("Fmodulename");
    $FmodulenameText = $xml->createTextNode(preg_replace('/\s+/', '', $DataDecode[0]->temparray->FirstModule));
    $Fmodulename->appendChild($FmodulenameText);

    $Fmodule->appendChild($Fmoduleid);
    $Fmodule->appendChild($Fmodulename);

    //second module
    $Secmodule = $xml->createElement("Secmodule");

    $Secmoduleid = $xml->createElement("SecmoduleID");
    $SecmoduleText = $xml->createTextNode(preg_replace('/\s+/', '', explode(";", $DataDecode[0]->temparray->Secmodule)[1]));
    $Secmoduleid->appendChild($SecmoduleText);
    $Secmodulename = $xml->createElement("Secmodulename");
    $SecmodulenameText = $xml->createTextNode(trim(preg_replace('/\s*\([^)]*\)/', '', preg_replace("(many)", '', preg_replace('/\s+/', '', explode(";", $DataDecode[0]->temparray->secmodule)[0])))));
    $Secmodulename->appendChild($SecmodulenameText);
    $Secmodule->appendChild($Secmoduleid);
    $Secmodule->appendChild($Secmodulename);
    $fields = $xml->createElement("fields");

    for ($i = 0; $i <= $countarray; $i++) {
        //     //get target field name
        $field = $xml->createElement("field");

        $label = $xml->createElement("fieldID");
        $labelText = $xml->createTextNode(explode(":", $DataDecode[$i]->temparray->Firstfield)[1]);
        $label->appendChild($labelText);
        $field->appendChild($label);

        $name = $xml->createElement("fieldname");
        $nameText = $xml->createTextNode($DataDecode[$i]->temparray->DefaultText);
        $name->appendChild($nameText);
        $field->appendChild($name);
        $fields->appendChild($field);
    }

    //$root->appendChild($name);
    $root->appendChild($Fmodule);
    $root->appendChild($Secmodule);
    $root->appendChild($fields);
    $xml->formatOutput = true;
    return $xml->saveXML();
}

function add_aray_for_history($decodedata)
{
    //$countarray=(count($decodedata)-1);
    // $labels="";
    foreach ($decodedata as $value) {
        $labels .= explode(":", $value->temparray->Firstfield)[2] . ",";
    }
    // return $labels;
    return array
        (
        'Labels' => $labels,
        'FirstModuleval' => preg_replace('/\s+/', '', $decodedata[0]->temparray->FirstModule),
        'FirstModuletxt' => preg_replace('/\s+/', '', $decodedata[0]->temparray->FirstModule),
        'SecondModuleval' => trim(preg_replace('/\s*\([^)]*\)/', '', preg_replace("(many)", '', preg_replace('/\s+/', '', explode(";", $decodedata[0]->temparray->secmodule)[0])))),
        'SecondModuletxt' => trim(preg_replace('/\s*\([^)]*\)/', '', preg_replace("(many)", '', preg_replace('/\s+/', '', explode(";", $decodedata[0]->temparray->secmodule)[0])))),
        'firstmodulelabel' => preg_replace('/\s+/', '', $decodedata[0]->temparray->FirstfieldID),
        'secondmodulelabel' => preg_replace('/\s+/', '', $decodedata[0]->temparray->SecondfieldID),
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
// function emptyStr($str) {
//     return is_string($str) && strlen($str) === 0;
// }
