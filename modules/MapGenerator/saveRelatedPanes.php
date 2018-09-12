<?php
//saveRelatedPanes.php


include_once "modules/cbMap/cbMap.php";
require_once 'data/CRMEntity.php';
require_once 'include/utils/utils.php';
include_once 'All_functions.php';
require_once 'Staticc.php';

global $root_directory, $log;
$Data = array();

$MapName = $_POST['MapName']; // stringa con tutti i campi scelti in selField1
$MapType = "RelatedPanes"; // stringa con tutti i campi scelti in selField1
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
    $MapType = "RelatedPanes";
}

if (!empty($Data)) {

    $jsondecodedata = json_decode($Data);
    $myDetails = array();

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
    //$DataDecode = json_decode($dat, true);
    $countarray = (count($DataDecode) - 1);
    $xml = new DOMDocument("1.0");
    $root = $xml->createElement("map");
    $xml->appendChild($root);

    $originmodule = $xml->createElement("originmodule");
    $originmodulename = $xml->createElement("originname");
    $originmoduleText = $xml->createTextNode(trim($DataDecode[0]->temparray->FirstModule));
    $originmodulename->appendChild($originmoduleText);
    $originmodule->appendChild($originmodulename);
    $root->appendChild($originmodule);

    //put the fields
    $panes = $xml->createElement("panes");
    $tempvalue = "";
    foreach ($DataDecode as $value) {

        if ($value->temparray->{'rp-label'} !== "More information") {
            if ($value->temparray->{'rp-label'} !== $tempvalue) {
                $pane = $xml->createElement("pane");
                $blocks = $xml->createElement("blocks");
            }
            if ($value->temparray->{'rp-label'} !== $tempvalue) {
                $label = $xml->createElement("label");
                $labeltext = $xml->createTextNode($value->temparray->{'rp-label'});
                $label->appendChild($labeltext);
                $pane->appendChild($label);

                $sequence = $xml->createElement("sequence");
                $sequencetext = $xml->createTextNode($value->temparray->{'rp-sequence'});
                $sequence->appendChild($sequencetext);
                $pane->appendChild($sequence);

                $block = $xml->createElement("block");

                $blabel = $xml->createElement("label");
                $blabeltext = $xml->createTextNode($value->temparray->{'rp-block-label'});
                $blabel->appendChild($blabeltext);
                $block->appendChild($blabel);

                $bsequence = $xml->createElement("sequence");
                $bsequencetext = $xml->createTextNode($value->temparray->{'rp-block-sequence'});
                $bsequence->appendChild($bsequencetext);
                $block->appendChild($bsequence);

                $btype = $xml->createElement("type");
                $btypetext = $xml->createTextNode($value->temparray->{'blockType'});
                $btype->appendChild($btypetext);
                $block->appendChild($btype);

                $bloadfrom = $xml->createElement("loadfrom");
                $bloadfromtext = $xml->createTextNode($value->temparray->{'rp-block-loadfrom'});
                $bloadfrom->appendChild($bloadfromtext);
                $block->appendChild($bloadfrom);

                $blocks->appendChild($block);
                $pane->appendChild($blocks);
                $panes->appendChild($pane);

            } else {
                $block = $xml->createElement("block");

                $blabel = $xml->createElement("label");
                $blabeltext = $xml->createTextNode($value->temparray->{'rp-block-label'});
                $blabel->appendChild($blabeltext);
                $block->appendChild($blabel);

                $bsequence = $xml->createElement("sequence");
                $bsequencetext = $xml->createTextNode($value->temparray->{'rp-block-sequence'});
                $bsequence->appendChild($bsequencetext);
                $block->appendChild($bsequence);

                $btype = $xml->createElement("type");
                $btypetext = $xml->createTextNode($value->temparray->{'blockType'});
                $btype->appendChild($btypetext);
                $block->appendChild($btype);

                $bloadfrom = $xml->createElement("loadfrom");
                $bloadfromtext = $xml->createTextNode($value->temparray->{'rp-block-loadfrom'});
                $bloadfrom->appendChild($bloadfromtext);
                $block->appendChild($bloadfrom);

                $blocks->appendChild($block);
                $pane->appendChild($blocks);
                $panes->appendChild($pane);
            }
        }
        $panes->appendChild($pane);
        $tempvalue = $value->temparray->{'rp-label'};
    }

    foreach ($DataDecode as $value) {
        if ($value->temparray->{'rp-label'} === "More information") {
            $paneMoreInformation;
            $blocksMoreInformation;

            if (!$paneMoreInformation) {
                $paneMoreInformation = $xml->createElement("pane");
                $blocksMoreInformation = $xml->createElement("blocks");

                $MIlabel = $xml->createElement("label");
                $MIlabeltext = $xml->createTextNode($value->temparray->{'rp-label'});
                $MIlabel->appendChild($MIlabeltext);
                $paneMoreInformation->appendChild($MIlabel);

                $MIsequence = $xml->createElement("sequence");
                $MIsequencetext = $xml->createTextNode($value->temparray->{'rp-sequence'});
                $MIsequence->appendChild($MIsequencetext);
                $paneMoreInformation->appendChild($MIsequence);

                $MIdefaultMoreInformation = $xml->createElement("defaultMoreInformation");
                $MIdefaultMoreInformationtext = $xml->createTextNode('1');
                $MIdefaultMoreInformation->appendChild($MIdefaultMoreInformationtext);
                $paneMoreInformation->appendChild($MIdefaultMoreInformation);
                $panes->appendChild($paneMoreInformation);
                //  if(!empty($value->temparray->{'rp-block-label'}) && !empty($value->temparray->{'rp-block-loadfrom'}))
                //  {
                //     $block = $xml->createElement("block");

                //    $blabel = $xml->createElement("label");
                //    $blabeltext = $xml->createTextNode($value->temparray->{'rp-block-label'});
                //    $blabel->appendChild($blabeltext);
                //    $block->appendChild($blabel);

                //    $bsequence = $xml->createElement("sequence");
                //    $bsequencetext = $xml->createTextNode($value->temparray->{'rp-block-sequence'});
                //    $bsequence->appendChild($bsequencetext);
                //    $block->appendChild($bsequence);

                //    $btype = $xml->createElement("type");
                //    $btypetext = $xml->createTextNode($value->temparray->{'blockType'});
                //    $btype->appendChild($btypetext);
                //    $block->appendChild($btype);

                //    $bloadfrom = $xml->createElement("loadfrom");
                //    $bloadfromtext = $xml->createTextNode($value->temparray->{'rp-block-loadfrom'});
                //    $bloadfrom->appendChild($bloadfromtext);
                //    $block->appendChild($bloadfrom);

                //    $blocksMoreInformation->appendChild($block);
                //    $paneMoreInformation->appendChild($blocksMoreInformation);
                //    $panes->appendChild($paneMoreInformation);
                //  }
            } else {
                $block = $xml->createElement("block");

                $blabel = $xml->createElement("label");
                $blabeltext = $xml->createTextNode($value->temparray->{'rp-block-label'});
                $blabel->appendChild($blabeltext);
                $block->appendChild($blabel);

                $bsequence = $xml->createElement("sequence");
                $bsequencetext = $xml->createTextNode($value->temparray->{'rp-block-sequence'});
                $bsequence->appendChild($bsequencetext);
                $block->appendChild($bsequence);

                $btype = $xml->createElement("type");
                $btypetext = $xml->createTextNode($value->temparray->{'blockType'});
                $btype->appendChild($btypetext);
                $block->appendChild($btype);

                $bloadfrom = $xml->createElement("loadfrom");
                $bloadfromtext = $xml->createTextNode($value->temparray->{'rp-block-loadfrom'});
                $bloadfrom->appendChild($bloadfromtext);
                $block->appendChild($bloadfrom);

                $blocksMoreInformation->appendChild($block);
                $paneMoreInformation->appendChild($blocksMoreInformation);
                $panes->appendChild($paneMoreInformation);
            }

        }
    }
    $root->appendChild($panes);
    $xml->formatOutput = true;
    return $xml->saveXML();
}

function add_aray_for_history($decodedata)
{
    $Labels = "";
    return array
        (
        'Labels' => $Labels,
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
