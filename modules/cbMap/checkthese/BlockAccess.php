<?php
/**
 *************************************************************************************************
 * Copyright 2015 OpenCubed -- This file is a part of OpenCubed coreBOS customizations.
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
 *  Module       : cbMap
 *  Version      : 5.5.0
 *  Author       : OpenCubed.
 *************************************************************************************************/
global $log,$adb, $mod_strings, $app_strings,$current_language;
//Default parameters

$defaultDelimiter = $_POST['delimiterVal'];
if(isset($_POST['orgmod']))
     $orgmod = explode("$$",$_POST['orgmod']);
else $orgmod = explode("$$",$_POST['orgmod']);

$orgmodID = $orgmod[0];
$mapid = $_REQUEST['mapid'];
$orgmodName = getTabModuleName($orgmodID);
if(isset($_POST['targetkeyconfig']))
$targetVal =implode(',', $_POST['targetkeyconfig']);
else
 $targetVal=$_POST['targetblock'];
$orgVal = $_POST['orgVal'];
$orgArr = explode(",",$orgVal);

global $adb;

$xml = new DOMDocument("1.0");
$root = $xml->createElement("map");
$xml->appendChild($root);
$origin = $xml->createElement("originmodule");
$originid = $xml->createElement("originid");
$originText = $xml->createTextNode($orgmodID);
$originid->appendChild($originText);
$originname = $xml->createElement("originname");
$originnameText = $xml->createTextNode($orgmodName);
$originname->appendChild($originnameText);
$origin->appendChild($originid);
$origin->appendChild($originname);
$blocks = $xml->createElement("blocks");
$targetarr=array();
if(strpos($targetVal,',')!=false)
 $targetarr=explode(',',$targetVal);
else 
  $targetarr[]=$targetVal;

for($i = 0;$i < sizeof($targetarr); $i++){
    $block = $xml->createElement("block");
    $blockID = $xml->createElement("blockID");
    $fieldideText = $xml->createTextNode($targetarr[$i]);
    $blockID->appendChild($fieldideText);
    $block->appendChild($blockID);
    $blockname = $xml->createElement("blockname");
    if($targetarr[$i]==1000)
     $label='Execute';
     else
     {$labelq=$adb->pquery("select blocklabel from vtiger_blocks where blockid=?",array($targetarr[$i]));
     $label=getTranslatedString($adb->query_result($labelq,0,'blocklabel'),$orgmodName);
     }
    $blocknameText = $xml->createTextNode($label);
    $blockname->appendChild($blocknameText);
    $block->appendChild($blockname);
    $blocklabel = $xml->createElement("blocklabel");
    if($targetarr[$i]==1000)
    $blocklabelText=$xml->createTextNode('Execute');
     else
    $blocklabelText = $xml->createTextNode($adb->query_result($labelq,0,'blocklabel'));
    $blocklabel->appendChild($blocklabelText);
    $block->appendChild($blocklabel);
    $blocks->appendChild($block);
}

$root->appendChild($origin);
$root->appendChild($blocks);
$xml->formatOutput = true;

echo $xml->saveXML();
include_once('modules/cbMap/cbMap.php');
$map_focus = new cbMap();
$map_focus->id = $mapid;
$map_focus->retrieve_entity_info($mapid,"cbMap");
$map_focus->column_fields['content']= $xml->saveXML();
$map_focus->mode = "edit";
$map_focus->save("cbMap");
?>
