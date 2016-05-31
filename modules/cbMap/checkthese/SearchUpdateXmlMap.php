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
global $root_directory,$log,$adb;
ini_set("display_errors",'off');
$search_module_id=$_REQUEST['search_module_id'];
$update_module_id=$_REQUEST['update_module_id'];
$rules=$_REQUEST['rules'];
$fields_expected_values=$_REQUEST['fields_expected_values'];
$mapid = $_REQUEST['mapid'];
$update_fields=$_REQUEST['update_fields'];
$log->debug($_REQUEST);

/*
$mapid=10485961;
$rules='[[{"field":"vtiger_statussettings.startsubstatus;=;vtiger_project.substatus","operator":"and"},{"field":"vtiger_statussettings.linkstarttemp;=;vtiger_project.commessa","operator":"and"},{"field":"vtiger_statussettings.linkto_supplier;=;vtiger_project.linktosupplier","operator":""}],[{"field":"vtiger_statussettings.startsubstatus;=;vtiger_project.substatus","operator":"and"},{"field":"vtiger_statussettings.linkstarttemp;=;vtiger_project.commessa","operator":"or"},{"field":"vtiger_statussettings.linkto_supplier;equal;123","operator":""}]]';
$search_module_id=61;
$update_module_id=49;
$fields_expected_values=' [{"ROW_ID":1,"fieldname":"vtiger_statussettings:startsubstatus:StatusSettings:startsubstatus:1018:direct","operator":"equal","expectedvalue":"vtiger_project:substatus:Project:substatus:778:direct"},{"ROW_ID":2,"fieldname":"vtiger_statussettings:linkstarttemp:StatusSettings:linkstarttemp:1021:direct","operator":"equal","expectedvalue":"vtiger_project:commessa:Project:commessa:790:direct"},{"ROW_ID":3,"fieldname":"vtiger_statussettings:linkto_supplier:StatusSettings:linkto_supplier:1765:direct","operator":"equal","expectedvalue":"vtiger_project:linktosupplier:Project:linktosupplier:920:direct"}]';
$update_fields='';*/
$search_module_query=$adb->pquery("SELECT tablename,modulename FROM vtiger_entityname en join vtiger_tab tab on tab.tabid=en.tabid where tab.tabid=?",array($search_module_id));
$search_module_name=$adb->query_result($search_module_query,0,'modulename');
$search_module_table=$adb->query_result($search_module_query,0,'tablename');

$update_module_query=$adb->pquery("SELECT tablename,modulename FROM vtiger_entityname en join vtiger_tab tab on tab.tabid=en.tabid where tab.tabid=?",array($update_module_id));
$update_module_name=$adb->query_result($update_module_query,0,'modulename');
$update_module_table=$adb->query_result($update_module_query,0,'tablename');

$xml = new DOMDocument('1.0');
// we want a nice output
$xml->formatOutput = true;

$root = $xml->createElement('map');
$root = $xml->appendChild($root);

$search = $xml->createElement('search');
$root->appendChild($search);

$module_node = $xml->createElement('module');
$search->appendChild($module_node);

$modulename_node = $xml->createElement('modulename');
$module_node->appendChild($modulename_node);
$text_modulename = $xml->createTextNode($search_module_name);
$modulename_node->appendChild($text_modulename);

$tablename_node = $xml->createElement('tablename');
$module_node->appendChild($tablename_node);
$text_tablename = $xml->createTextNode($search_module_table);
$tablename_node->appendChild($text_tablename);

$fields_node=$xml->createElement('fields');
$search->appendChild($fields_node);

$field_data=json_decode($fields_expected_values);
for($i=0;$i<sizeof($field_data);$i++){
$field_node=$xml->createElement('field');
$fields_node->appendChild($field_node);

$fieldname_node=$xml->createElement('fieldname');
$field_node->appendChild($fieldname_node);

$field_array=explode(':',$field_data[$i]->fieldname);
$fieldname=$field_array[0].'.'.$field_array[1];
$text_fieldname = $xml->createTextNode($fieldname);
$fieldname_node->appendChild($text_fieldname);
if($field_data[$i]->operator=='equal')
    $operator='=';
else if($field_data[$i]->operator=='notlike')
    $operator='not like';
else if($field_data[$i]->operator=='like')
     $operator='like';
else if($field_data[$i]->operator=='biggerthen')
     $operator='>';
else if($field_data[$i]->operator=='smallerthen')
     $operator='<';

$field_operator_node=$xml->createElement('operator');
$field_node->appendChild($field_operator_node);
$text_operator = $xml->createTextNode($operator);
$field_operator_node->appendChild($text_operator);

$expectedvalue_array=explode(':',$field_data[$i]->expectedvalue);
$expectedvalue=$expectedvalue_array[0].'.'.$expectedvalue_array[1];

$expectedvalue_node=$xml->createElement('expectedvalue');
$field_node->appendChild($expectedvalue_node);
$text_expectedvalue = $xml->createTextNode($expectedvalue);
$expectedvalue_node->appendChild($text_expectedvalue);

}

$rules_node=$xml->createElement('rules');
$search->appendChild($rules_node);
if(!empty($rules) && $rules!='')
$rules_data=json_decode($rules);
else {
    $rule_node=$xml->createElement('rule');
    $rules_node->appendChild($rule_node);
    $rule_data=$rules_data[$j];
  for($r=0;$r<sizeof($field_data);$r++){
   $searchfield_node=$xml->createElement('searchfield');
   $rule_node->appendChild($searchfield_node);

   $field_array=explode(':',$field_data[$r]->fieldname);
   $fieldname_search=$field_array[0].'.'.$field_array[1];
   $text_fieldname_search = $xml->createTextNode($fieldname_search);
   $searchfield_node->appendChild($text_fieldname_search);
   
   $alter_expectedvalue=$xml->createElement('alter_expectedvalue');
   $rule_node->appendChild($alter_expectedvalue);
   
   $alter_operator=$xml->createElement('operator');
   $alter_expectedvalue->appendChild($alter_operator);
   
   $alter_value=$xml->createElement('value');
   $alter_expectedvalue->appendChild($alter_value);
   if($r<sizeof($field_data)-1){
       $rule_operator=$xml->createElement('operator');
       $rule_node->appendChild($rule_operator);
       $rule_operator_text=$xml->createTextNode('and');
       $rule_operator->appendChild($rule_operator_text);
   }
    }

}
for($j=0;$j<sizeof($rules_data);$j++){
    $rule_node=$xml->createElement('rule');
    $rules_node->appendChild($rule_node);
    $rule_data=$rules_data[$j];
    
  for($r=0;$r<sizeof($rule_data);$r++){
   $searchfield_node=$xml->createElement('searchfield');
   $rule_node->appendChild($searchfield_node);
      
   $searchfield_array=explode(";",$rule_data[$r]->field);
   $searchfield_text=$xml->createTextNode($searchfield_array[0]);
   $searchfield_node->appendChild($searchfield_text);
   
   $alter_expectedvalue=$xml->createElement('alter_expectedvalue');
   $rule_node->appendChild($alter_expectedvalue);
   
   $alter_operator=$xml->createElement('operator');
   $alter_expectedvalue->appendChild($alter_operator);
   
   $alter_value=$xml->createElement('value');
   $alter_expectedvalue->appendChild($alter_value);
   
   if(stristr($searchfield_array[2],'vtiger_')==''){
       $alter_operator=$xml->createTextNode($searchfield_array[1]);
       $alter_operator->appendChild($alter_operator);
       
       $alter_value=$xml->createTextNode($searchfield_array[2]);
       $alter_expectedvalue->appendChild($alter_value);
   }
   if($rule_data[$r]->operator!=''){
       $rule_operator=$xml->createElement('operator');
       $rule_node->appendChild($rule_operator);
       $rule_operator_text=$xml->createTextNode($rule_data[$r]->operator);
       $rule_operator->appendChild($rule_operator_text);
   }
}
}
$update = $xml->createElement('update');
$root->appendChild($update);

$update_modules_node = $xml->createElement('modules');
$update->appendChild($update_modules_node);

$update_module_node = $xml->createElement('module');
$update_modules_node->appendChild($update_module_node);

$update_modulename_node = $xml->createElement('modulename');
$update_module_node->appendChild($update_modulename_node);
$text_update_modulename = $xml->createTextNode($update_module_name);
$update_modulename_node->appendChild($text_update_modulename);

$update_tablename_node = $xml->createElement('tablename');
$update_module_node->appendChild($update_tablename_node);
$text_update_tablename = $xml->createTextNode($update_module_table);
$update_tablename_node->appendChild($text_update_tablename);

$update_data=json_decode($update_fields);

$update_fields_node=$xml->createElement('fields');
$update->appendChild($update_fields_node);
if($update_data==''){
$update_field_node=$xml->createElement('field');
$update_fields_node->appendChild($update_field_node);

$update_fieldname_node=$xml->createElement('fieldname');
$update_field_node->appendChild($update_fieldname_node); 

$update_field_operator_node=$xml->createElement('operator');
$update_field_node->appendChild($update_field_operator_node);

$update_expectedvalue_node=$xml->createElement('expectedvalue');
$update_field_node->appendChild($update_expectedvalue_node);
}

for($i=0;$i<sizeof($update_data);$i++){

$update_field_node=$xml->createElement('field');
$update_fields_node->appendChild($update_field_node);

$update_fieldname_node=$xml->createElement('fieldname');
$update_field_node->appendChild($update_fieldname_node);

$update_array=explode(':',$update_data[$i]->fieldname);
$update_fieldname=$update_array[0].'.'.$update_array[1];
$update_text_fieldname = $xml->createTextNode($update_fieldname);
$update_fieldname_node->appendChild($update_text_fieldname);

$update_field_operator_node=$xml->createElement('operator');
$update_field_node->appendChild($update_field_operator_node);
$update_text_operator = $xml->createTextNode($update_data[$i]->operator);
$update_field_operator_node->appendChild($update_text_operator);

$update_expectedvalue_node=$xml->createElement('expectedvalue');
$update_field_node->appendChild($update_expectedvalue_node);
$update_expectedvalue=$update_data[$i]->expectedvalue;
if(stristr($update_expectedvalue,':')!=''){
    $update_expectedvalue_arr=explode(':',$update_expectedvalue);
    $update_value=$update_expectedvalue_arr[0].'.'.$update_expectedvalue_arr[1];
}else
    $update_value=$update_data[$i]->expectedvalue;
$update_text_expectedvalue = $xml->createTextNode($update_value);
$update_expectedvalue_node->appendChild($update_text_expectedvalue);

}

include_once('modules/cbMap/cbMap.php');
echo 'teszt';
$map_focus = new cbMap();
$xml->formatOutput = true;

echo $xml->saveXML();

$map_focus->id = $mapid;
$map_focus->retrieve_entity_info($mapid,'cbMap');
$map_focus->column_fields['content']= $xml->saveXML();
$map_focus->mode = "edit";
$map_focus->save('cbMap');
/*
//Default parameters
$defaultDelimiter = $_POST['delimiterVal'];
$rec = $_POST['accid'];
if(isset($_POST['orgmodH']))
     $orgmod = explode("$$",$_POST['orgmodH']);
else $orgmod = explode("$$",$_POST['orgmod']);

$orgmodID = $orgmod[0];

//$name = $xml->createElement("name");
$target = $xml->createElement("originmodule");
$targetid = $xml->createElement("originid");
$targetidText = $xml->createTextNode($targetmodID);
$targetid->appendChild($targetidText);
$targetname = $xml->createElement("originname");
$targetnameText = $xml->createTextNode($targetmodName);
$targetname->appendChild($targetnameText);
$target->appendChild($targetid);
$target->appendChild($targetname);

$origin = $xml->createElement("targetmodule");
$originid = $xml->createElement("targetid");
$originText = $xml->createTextNode($orgmodID);
$originid->appendChild($originText);
$originname = $xml->createElement("targetname");
$originnameText = $xml->createTextNode($orgmodName);
$originname->appendChild($originnameText);
$origin->appendChild($originid);
$origin->appendChild($originname);
$fields = $xml->createElement("fields");

for($i = 0;$i < $nrmaps; $i++){
    //get target field name
    $orgFields = explode(":",$orgArr[$i]);
    $field = $xml->createElement("field"); 
    $fieldname = $xml->createElement("fieldname");
    $fieldnameText = $xml->createTextNode($orgFields[1]);
    $fieldname->appendChild($fieldnameText);
    $field->appendChild($fieldname);
    $fieldID = $xml->createElement("fieldID");
    $fieldideText = $xml->createTextNode($orgFields[6]);
    $fieldID->appendChild($fieldideText);
    $field->appendChild($fieldID);
    //target module fields
    $Orgfields = $xml->createElement("Orgfields");
    $field->appendChild($Orgfields);
    // $targetArr[$i] mban fushat perkatese
    $trFields = explode(",",$targetArr[$i]);
//    if(count($trFields) > 1) {
        $fldnamearr = array();
        $fldidarr = array();
        for($j=0;$j<count($trFields);$j++){
        $fld = explode(":",$trFields[$j]);
        $fldnamearr[] = $fld[1];
        $modid = $fld[2];
        $fldidarr = $fld[4];  
        $type = $fld[5];
        if($type == "related"){
        $OrgRelfield= $xml->createElement("Relfield");
        $OrgRelfieldName = $xml->createElement("RelfieldName");
        $OrgRelfieldNameText= $xml->createTextNode($fld[1]);
        $OrgRelModule= $xml->createElement("RelModule");
        $OrgRelModuleText= $xml->createTextNode($fld[2]);
        $OrgRelfieldName->appendChild($OrgRelfieldNameText);
        $OrgRelModule->appendChild($OrgRelModuleText);
        $OrgRelfield->appendChild($OrgRelfieldName);
        $OrgRelfield->appendChild($OrgRelModule);
        
        $linkfield = $xml->createElement("linkfield");
        $linkfieldText= $xml->createTextNode($fld[6]);
        $linkfield->appendChild($linkfieldText);
        $OrgRelfield->appendChild($linkfield);
        $Orgfields->appendChild($OrgRelfield);
        }
        else
        {
        $Orgfield= $xml->createElement("Orgfield");
        $OrgfieldName = $xml->createElement("OrgfieldName");
        $OrgfieldNameText= $xml->createTextNode($fld[1]);
        $OrgfieldName->appendChild($OrgfieldNameText);
        $Orgfield->appendChild($OrgfieldName);
        
        $OrgfieldID = $xml->createElement("OrgfieldID");
        $OrgfieldIDText= $xml->createTextNode($fld[4]);
        $OrgfieldID->appendChild($OrgfieldIDText);
        $Orgfield->appendChild($OrgfieldID);
        $Orgfields->appendChild($Orgfield);
        }
        }
        $del = $xml->createElement("delimiter");
        $delText= $xml->createTextNode($delimArr[$i]);
        $del->appendChild($delText);
        $Orgfields->appendChild($del);
//    }
   $fields->appendChild($field);
   $strTarField = implode($delimArr[$i],$fldnamearr);
   $strTarFldId = implode(",",$fldidarr);
}*/
//$root->appendChild($name);

?>
