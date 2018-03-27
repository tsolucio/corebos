<?php
/*************************************************************************************************
 * Copyright 2016 TSolucio -- This file is a part of TSolucio coreBOS customizations.
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
 *  Author       : TSolucio.
 *************************************************************************************************/
include_once 'modules/cbMap/cbMap.php';
include_once 'modules/cbMap/generatemap/generateMap.php';
$mapid = vtlib_purify($_REQUEST['mapid']);
$mapInstance = CRMEntity::getInstance('cbMap');
$mapInstance->retrieve_entity_info($mapid, 'cbMap');
$maptype = $mapInstance->column_fields['maptype'];
if (file_exists('modules/cbMap/generatemap/'.$maptype.'.php')) {
	include_once 'modules/cbMap/generatemap/'.$maptype.'.php';
	$genmap = new $maptype($mapInstance);
} else {
	$genmap = new generatecbMap($mapInstance);
}
$genmap->generateMap();
?>
