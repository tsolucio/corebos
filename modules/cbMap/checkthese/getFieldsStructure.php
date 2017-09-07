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
include_once('modules/cbMap/cbMap.php');
include_once('include/utils/CommonUtils.php');
include_once('include/utils/utils.php');

global $log, $app_strings, $mod_strings, $current_language, $currentModule, $theme;

$mapInstance = CRMEntity::getInstance('cbMap');
$module1 = (int)$_REQUEST['pmodule'];
$module2= getTabName($module1);
$focus = CRMEntity::getInstance($module2);
$blocks=$mapInstance->getBlocksPortal1($module2, 'edit_view', 'edit', $focus->column_fields,'','5');
$t2=array();

foreach($blocks as $block=>$fields)
{ 
    $fields_ret=array();
    foreach($fields as $field=>$arr_field)
    {
        foreach($arr_field as $each_arr_fields=>$each_arr_field )
        {
            $fldname=$blocks[$block][$field][$each_arr_fields][2][0];
            $fldlabel=$blocks[$block][$field][$each_arr_fields][1][0];
            if($fldname!='')
            $fields_ret[]=array('name'=>$fldname,'label'=>$fldlabel);
        }
    }
    $t2["$block"]=$fields_ret;
}
echo json_encode($t2);
?>