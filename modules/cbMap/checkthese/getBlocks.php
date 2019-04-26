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
global $log,$mod_strings, $app_strings,$adb;
include_once('modules/cbMap/cbMap.php');
require_once('data/CRMEntity.php');
require_once('include/utils/utils.php');
require_once('include/database/PearDatabase.php');
require_once('Smarty_setup.php');
require_once('include/utils/utils.php');

$mapInstance = CRMEntity::getInstance("cbMap");
$modtype = $_REQUEST['modtype'];
$module_list[] = json_decode($_REQUEST['module_list']);
$related_modules = json_decode($_REQUEST['related_modules']);
$rel_fields = json_decode($_REQUEST['rel_fields']);
$moduleid =  $_REQUEST['pmodule'];
//$moduleName = getTabModuleName($moduleid);

$mapInstance->module_list=$module_list;
$blockinfo=$mapInstance->getBlockInfo($moduleid);
echo $mapInstance->getBlockHTML($blockinfo,$moduleid);
