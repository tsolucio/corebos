<?php

include_once "modules/cbMap/cbMap.php";
require_once 'data/CRMEntity.php';
require_once 'include/utils/utils.php';
require_once 'All_functions.php';
require_once 'Staticc.php';

global $root_directory, $log;
$Data = array();

$MapName = $_POST['MapName']; // stringa con tutti i campi scelti in selField1
$MapType = "Record Access Control"; // stringa con tutti i campi scelti in selField1
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
    $MapType = "Record Access Control";
}

if (!empty($Data)) {

    $jsondecodedata = json_decode($Data);
    // echo add_content($jsondecodedata);
    // print_r($jsondecodedata);
    if (strlen($MapID[1] == 0)) {

        $focust = new cbMap();
        $focust->column_fields['assigned_user_id'] = 1;
        $focust->column_fields['mapname'] = $mapname;
        $focust->column_fields['content'] = add_content($jsondecodedata);
        $focust->column_fields['targetname'] = $jsondecodedata[0]->temparray->FirstModule;
        $focust->column_fields['maptype'] = $MapType;
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
        $focust->column_fields['targetname'] = $jsondecodedata[0]->temparray->FirstModule;
        $focust->column_fields['maptype'] = $MapType;
        $focust->column_fields['mvqueryid'] = $idquery2;
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
    $a = array();

    foreach ($DataDecode as $value) {
        $a[] = $value->temparray->LabelName;
    }
    $a = array_unique($a);

    $xml = new DOMDocument("1.0");
    $root = $xml->createElement("map");
    $xml->appendChild($root);
    //put the menus
    $originmodule = $xml->createElement("originmodule");
    $originmoduleid = $xml->createElement("originid");
    $originmoduleidText = $xml->createTextNode("");
    $originmoduleid->appendChild($originmoduleidText);
    $originmodulename = $xml->createElement("originname");
    $originmodulenameText = $xml->createTextNode($DataDecode[0]->temparray->FirstModule);
    $originmodulename->appendChild($originmodulenameText);
    $originmodule->appendChild($originmoduleid);
    $originmodule->appendChild($originmodulename);
    $root->appendChild($originmodule);

    //for listview

    $listview = $xml->createElement("listview");
    $c = $xml->createElement("c");
    $ctext = $xml->createTextNode($_POST['AddcheckListview']);
    $c->appendChild($ctext);

    $r = $xml->createElement("r");
    $rtext = $xml->createTextNode($_POST['viewcheckListview']);
    $r->appendChild($rtext);

    $u = $xml->createElement("u");
    $utext = $xml->createTextNode($_POST['editcheckListview']);
    $u->appendChild($utext);

    $d = $xml->createElement("d");
    $dtext = $xml->createTextNode($_POST['deletecheckListview']);
    $d->appendChild($dtext);

    $listview->appendChild($c);
    $listview->appendChild($r);
    $listview->appendChild($u);
    $listview->appendChild($d);
    $root->appendChild($listview);

    $detailview = $xml->createElement("detailview");
    $c = $xml->createElement("c");
    $ctext = $xml->createTextNode($_POST['duplicatecheckDetailView']);
    $c->appendChild($ctext);

    $r = $xml->createElement("r");
    $rtext = $xml->createTextNode($_POST['viewcheckDetailView']);
    $r->appendChild($rtext);

    $u = $xml->createElement("u");
    $utext = $xml->createTextNode($_POST['editcheckDetailView']);
    $u->appendChild($utext);

    $d = $xml->createElement("d");
    $dtext = $xml->createTextNode($_POST['deletecheckDetailView']);
    $d->appendChild($dtext);

    $detailview->appendChild($c);
    $detailview->appendChild($r);
    $detailview->appendChild($u);
    $detailview->appendChild($d);
    $root->appendChild($detailview);

    $relatedlists = $xml->createElement("relatedlists");

    foreach ($DataDecode as $values) {
        $relatedlist = $xml->createElement("relatedlist");

        $modulename = $xml->createElement("modulename");
        $modulenametext = $xml->createTextNode($values->temparray->relatedModule);
        $modulename->appendChild($modulenametext);

        $c = $xml->createElement("c");
        $ctext = $xml->createTextNode($values->temparray->addcheckRelatetlist);
        $c->appendChild($ctext);

        $r = $xml->createElement("r");
        $rtext = $xml->createTextNode($values->temparray->viewcheckRelatedlist);
        $r->appendChild($rtext);

        $u = $xml->createElement("u");
        $utext = $xml->createTextNode($values->temparray->editcheckrelatetlist);
        $u->appendChild($utext);

        $d = $xml->createElement("d");
        $dtext = $xml->createTextNode($values->temparray->deletecheckrelatedlist);
        $d->appendChild($dtext);

        $s = $xml->createElement("s");
        $stext = $xml->createTextNode($values->temparray->selectcheckrelatedlist);
        $s->appendChild($stext);

        $relatedlist->appendChild($modulename);
        $relatedlist->appendChild($c);
        $relatedlist->appendChild($r);
        $relatedlist->appendChild($u);
        $relatedlist->appendChild($d);
        $relatedlist->appendChild($s);

        $relatedlists->appendChild($relatedlist);

    }

    $root->appendChild($relatedlists);
    $xml->formatOutput = true;
    return $xml->saveXML();
}

function add_aray_for_history($decodedata)
{
    $Labels = "";
    return array
        (
        'Labels' => $Labels,
        'FirstModuleval' => $decodedata[0]->temparray->FirstModule,
        'FirstModuletxt' => "",
        'SecondModuleval' => "",
        'SecondModuletxt' => "",
        'firstmodulelabel' => "",
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
