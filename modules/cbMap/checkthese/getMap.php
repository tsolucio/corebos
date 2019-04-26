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
require_once('Smarty_setup.php');
include_once('modules/cbMap/cbMap.php');
global $mod_strings, $app_strings, $adb, $log;
$mapTemplate = new vtigerCRM_Smarty();
$allModules = array();
$mapid = $_REQUEST["mapid"];
$mapInstance = CRMEntity::getInstance("cbMap");
$allModules = $mapInstance->initListOfModules();
$delimiters = array("",",",";","_","-");
$getMapQuery = $adb->pquery("Select * from vtiger_cbmap where cbmapid=?",array($mapid));
$nr_row = $adb->num_rows($getMapQuery);
if($nr_row != 0){
    $viewmode = "edit";
    $origin = $adb->query_result($getMapQuery,0,'origin');
    $originname = $adb->query_result($getMapQuery,0,'originname');
    $target = $adb->query_result($getMapQuery,0,'target');
    $targetname = $adb->query_result($getMapQuery,0,'targetname');
    $field1 = $adb->query_result($getMapQuery,0,'field1');
    $field2 = $adb->query_result($getMapQuery,0,'field2');
    $seldelimiter = $adb->query_result($getMapQuery,0,'delimiter');
    $maptype=$adb->query_result($getMapQuery,0,'maptype');
    $blocks=$adb->query_result($getMapQuery,0,'blocks');
    
    $targetFieldsArr = explode("::",$field2);
    $originFieldsArr = explode(",",$field1);
    
    $nrFields = count($originFieldsArr);

    $mapTemplate->assign("nrFields",$nrFields);
    $mapTemplate->assign("originFieldsArr",$field1);
    $mapTemplate->assign("targetFieldsArr",$field2);
    $mapTemplate->assign("originID",$origin);
    $mapTemplate->assign("targetID",$target);
    $mapTemplate->assign("originName",$originname);
    $mapTemplate->assign("targetName",$targetname);
    $mapTemplate->assign("seldelimiter",$seldelimiter);
    $mapTemplate->assign('maptype',$maptype);
}
else{
    $viewmode="create";
}
$mapTemplate->assign("MOD", $mod_strings);
$mapTemplate->assign("APP", $app_strings);
$mapTemplate->assign("module_list",json_encode($mapInstance->module_list));
$mapTemplate->assign("related_modules",json_encode($mapInstance->related_modules));
$mapTemplate->assign("rel_fields",json_encode($mapInstance->rel_fields));
$mapTemplate->assign("delimiters", $delimiters);
$mapTemplate->assign("mapid", $mapid);
$mapTemplate->assign("mode", $viewmode);
$mapTemplate->assign("module_id",$mapInstance->module_id);

switch(strtolower($maptype)){
    case 'block access':
        $blockids=explode(',',$blocks);
        $mapTemplate->assign('blocks',json_encode($mapInstance->module_list[$originname]));
        $mapTemplate->assign('blockid',$mapInstance->module_list[$originname]);
        $mapTemplate->assign("targetID",$blockids[0]);
        $mapTemplate->display('modules/cbMap/blockaccess.tpl');
        break;
    case 'metadata':
        $mapTemplate->display('modules/cbMap/metadataWindow.tpl');
        break;
    case 'search and update':
        $mapTemplate->display('modules/cbMap/SearchUpdateMap.tpl');
        break;
   default:
        $mapTemplate->display('modules/cbMap/mapWindow.tpl');
}
?>
