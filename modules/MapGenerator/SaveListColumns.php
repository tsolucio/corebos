<?php

// SaveListColumns.php

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
$MapType = "ListColumns"; // stringa con tutti i campi scelti in selField1
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
    $MapType = "ListColumns";
}

if (!empty($Data)) {

    $jsondecodedata = json_decode($Data);
    //print_r(save_history(add_aray_for_history($jsondecodedata),$MapID[0],add_content($jsondecodedata)));

// echo save_history(add_aray_for_history($jsondecodedata),$idquery2,add_content($jsondecodedata));
    // exit();

    if (strlen($MapID[1] == 0)) {

        $focust = new cbMap();
        $focust->column_fields['assigned_user_id'] = 1;
        // $focust->column_fields['mapname'] = $jsondecodedata[0]->temparray->FirstModule."_ListColumns";
        $focust->column_fields['mapname'] = $mapname;
        $focust->column_fields['content'] = add_content($jsondecodedata);
        $focust->column_fields['maptype'] = $MapType;
        $focust->column_fields['targetname'] = $jsondecodedata[0]->temparray->FirstModule;
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
        $focust->column_fields['content'] = add_content($jsondecodedata);
        $focust->column_fields['maptype'] = $MapType;
        $focust->column_fields['mvqueryid'] = $idquery2;
        $focust->column_fields['targetname'] = $jsondecodedata[0]->temparray->FirstModule;
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
 * @param DataDecode {Array} {This para is a array }
 */
function add_content($DataDecode)
{
    //$DataDecode = json_decode($dat, true);
    $countarray = (count($DataDecode) - 1);
    $xml = new DOMDocument("1.0");
    $root = $xml->createElement("map");
    $xml->appendChild($root);
    //$name = $xml->createElement("name");
    $target = $xml->createElement("originmodule");
    $targetid = $xml->createElement("originid");
    $targetidText = $xml->createTextNode("");
    $targetid->appendChild($targetidText);
    $targetname = $xml->createElement("originname");
    $targetnameText = $xml->createTextNode(trim(preg_replace('/\s*\([^)]*\)/', '', preg_replace("(many)", '', preg_replace('/\s+/', '', explode(";", $DataDecode[0]->temparray->secmodule)[0])))));
    $targetname->appendChild($targetnameText);
    $target->appendChild($targetid);
    $target->appendChild($targetname);

    $fields = $xml->createElement("relatedlists");

    // $hw=0;
    $popup = $xml->createElement("popup");
    $linkfield2 = $xml->createElement("linkfield");
    $linkfieldtext2 = $xml->createTextNode($DataDecode[0]->temparray->FirstfieldID);
    $linkfield2->appendChild($linkfieldtext2);
    $popup->appendChild($linkfield2);
    $columns2 = $xml->createElement("columns");
    for ($i = 0; $i <= $countarray; $i++) {
        //

        if ($DataDecode[$i]->temparray->JsonType == "Related List") {

            $relatedlist = $xml->createElement("relatedlist");
            $module = $xml->createElement("module");
            $moduletext = $xml->createTextNode($DataDecode[$i]->temparray->FirstModule);
            $module->appendChild($moduletext);
            $relatedlist->appendChild($module);

            $linkfield = $xml->createElement("linkfield");
            $linkfieldtext = $xml->createTextNode($DataDecode[$i]->temparray->SecondfieldID);
            $linkfield->appendChild($linkfieldtext);
            $relatedlist->appendChild($linkfield);

            $columns = $xml->createElement("columns");

            $field = $xml->createElement("field");

            $label = $xml->createElement("label");
            $labelText = $xml->createTextNode($DataDecode[$i]->temparray->DefaultValue);
            $label->appendChild($labelText);
            $field->appendChild($label);

            $name = $xml->createElement("name");
            $nameText = $xml->createTextNode(explode(":", $DataDecode[$i]->temparray->SecondField)[1]);
            $name->appendChild($nameText);
            $field->appendChild($name);

            $table = $xml->createElement("table");
            $tableText = $xml->createTextNode(explode(":", $DataDecode[$i]->temparray->SecondField)[0]);
            $table->appendChild($tableText);
            $field->appendChild($table);

            $columnname = $xml->createElement("columnname");
            $columnnameText = $xml->createTextNode(explode(":", $DataDecode[$i]->temparray->SecondField)[2]);
            $columnname->appendChild($columnnameText);
            $field->appendChild($columnname);

            $columns->appendChild($field);
            $relatedlist->appendChild($columns);
            $fields->appendChild($relatedlist);
        } else if ($DataDecode[$i]->temparray->JsonType == "Popup Screen") {
            $field2 = $xml->createElement("field");
            $label2 = $xml->createElement("label");
            $labelText2 = $xml->createTextNode($DataDecode[$i]->temparray->DefaultValueFirstModuleField);
            $label2->appendChild($labelText2);
            $field2->appendChild($label2);

            $name2 = $xml->createElement("name");
            $nameText2 = $xml->createTextNode(explode(":", $DataDecode[$i]->temparray->Firstfield)[1]);
            $name2->appendChild($nameText2);
            $field2->appendChild($name2);

            $table2 = $xml->createElement("table");
            $tableText2 = $xml->createTextNode(explode(":", $DataDecode[$i]->temparray->Firstfield)[0]);
            $table2->appendChild($tableText2);
            $field2->appendChild($table2);

            $columnname2 = $xml->createElement("columnname");
            $columnnameText2 = $xml->createTextNode(explode(":", $DataDecode[$i]->temparray->Firstfield)[2]);
            $columnname2->appendChild($columnnameText2);
            $field2->appendChild($columnname2);

            $columns2->appendChild($field2);
            $popup->appendChild($columns2);
        }
    }

    //$root->appendChild($name);
    $root->appendChild($target);
    $root->appendChild($fields);
    $root->appendChild($popup);
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

        $label = $xml->createElement("label");
        $labelText = $xml->createTextNode($DataDecode[$i]->temparray->DefaultValueText);
        $label->appendChild($labelText);
        $field->appendChild($label);

        $name = $xml->createElement("name");
        $nameText = $xml->createTextNode(explode(":", $DataDecode[$i]->temparray->SecondField)[1]);
        $name->appendChild($nameText);
        $field->appendChild($name);

        $table = $xml->createElement("table");
        $tableText = $xml->createTextNode(explode(":", $DataDecode[$i]->temparray->SecondField)[0]);
        $table->appendChild($tableText);
        $field->appendChild($table);

        $columnname = $xml->createElement("columnname");
        $columnnameText = $xml->createTextNode(explode(":", $DataDecode[$i]->temparray->SecondField)[2]);
        $columnname->appendChild($columnnameText);
        $field->appendChild($columnname);

        $fields->appendChild($field);
    }

    for ($i = 0; $i <= $countarray; $i++) {

        if ($i != 0) {

            if (explode(":", $DataDecode[$i]->temparray->Firstfield)[1] != explode(":", $DataDecode[$i - 1]->temparray->Firstfield)[1]) {
                $field2 = $xml->createElement("field");

                $label2 = $xml->createElement("label");
                $labelText2 = $xml->createTextNode($DataDecode[$i]->temparray->DefaultValueFirstModuleField);
                $label2->appendChild($labelText2);
                $field2->appendChild($label2);

                $name2 = $xml->createElement("name");
                $nameText2 = $xml->createTextNode(explode(":", $DataDecode[$i]->temparray->Firstfield)[1]);
                $name2->appendChild($nameText2);
                $field2->appendChild($name2);

                $table2 = $xml->createElement("table");
                $tableText2 = $xml->createTextNode(explode(":", $DataDecode[$i]->temparray->Firstfield)[0]);
                $table2->appendChild($tableText2);
                $field2->appendChild($table2);

                $columnname2 = $xml->createElement("columnname");
                $columnnameText2 = $xml->createTextNode(explode(":", $DataDecode[$i]->temparray->Firstfield)[2]);
                $columnname2->appendChild($columnnameText2);
                $field2->appendChild($columnname2);
            }
        } else {
            $field2 = $xml->createElement("field");

            $label2 = $xml->createElement("label");
            $labelText2 = $xml->createTextNode($DataDecode[$i]->temparray->DefaultValueFirstModuleField);
            $label2->appendChild($labelText2);
            $field2->appendChild($label2);

            $name2 = $xml->createElement("name");
            $nameText2 = $xml->createTextNode(explode(":", $DataDecode[$i]->temparray->Firstfield)[1]);
            $name2->appendChild($nameText2);
            $field2->appendChild($name2);

            $table2 = $xml->createElement("table");
            $tableText2 = $xml->createTextNode(explode(":", $DataDecode[$i]->temparray->Firstfield)[0]);
            $table2->appendChild($tableText2);
            $field2->appendChild($table2);

            $columnname2 = $xml->createElement("columnname");
            $columnnameText2 = $xml->createTextNode(explode(":", $DataDecode[$i]->temparray->Firstfield)[2]);
            $columnname2->appendChild($columnnameText2);
            $field2->appendChild($columnname2);
        }

        $fields->appendChild($field2);
    } //end for

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
        $labels .= explode(":", $value->temparray->Firstfield)[2] . "," . explode(":", $value->temparray->Firstfield)[2] . ",";
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

// function emptyStr($str) {
//     return is_string($str) && strlen($str) === 0;
// }

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
