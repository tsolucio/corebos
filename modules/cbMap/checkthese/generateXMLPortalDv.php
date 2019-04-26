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
global $root_directory,$log;
//Default parameters
$mapid = $_REQUEST['mapid'];
$models=$_REQUEST['models'];
$mv=json_decode($models);
$count_col=0;
$xml = new DOMDocument("1.0");
$root = $xml->createElement("map");
$b = $xml->createElement("blocks");
$root->appendChild($b);

$xml->appendChild($root);
//$name = $xml->createElement("name");
$module1 = (int)$_POST['modulePortal'];
$module= getTabModuleName($module1);
$focus = CRMEntity::getInstance($module);
var_dump($mv);
for($i=0;$i<sizeof($mv);$i++){
        $target_block = $xml->createElement("block");
        $blockname_node = $xml->createElement("name");
        $blockname = $xml->createTextNode($mv[$i]->blockname);
        $blockname_node->appendChild($blockname);
        $target_block->appendChild($blockname_node);
        $all_fields=$mv[$i]->fields;
        $count_col=0;
        for($j=0;$j<sizeof($all_fields);$j++){
            if($count_col==2 || $count_col==0){
                $target_row = $xml->createElement("row");
                $count_col=0;
                $count_row++;
            }
            $fldname=$all_fields[$j];
            $target_column = $xml->createElement("column");
            $column_name = $xml->createTextNode($fldname);
            $target_column->appendChild($column_name);
            $target_row->appendChild($target_column);
            if($count_col==2 || $count_col==0){
                $target_block->appendChild($target_row);
            }
            $count_col++;
        }
        $count_row=0;
        $b->appendChild($target_block);
}
//$root->appendChild($name);
$root->appendChild($b);
$xml->formatOutput = true;

echo $xml->saveXML();
include_once('modules/cbMap/cbMap.php');
$map_focus = new cbMap();
$map_focus->id = $mapid;
$map_focus->retrieve_entity_info($mapid,'cbMap');
$map_focus->column_fields['content']= $xml->saveXML();
$map_focus->mode = 'edit';
$map_focus->save('cbMap');
?>
