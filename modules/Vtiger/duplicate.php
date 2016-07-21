<?php
 /*************************************************************************************************
 * Copyright 2016 JPL TSolucio, S.L. -- This file is a part of TSOLUCIO coreBOS Customizations.
 * Licensed under the vtiger CRM Public License Version 1.1 (the "License"); you may not use this
 * file except in compliance with the License. You can redistribute it and/or modify it
 * under the terms of the License. JPL TSolucio, S.L. reserves all rights not expressly
 * granted by the License. coreBOS distributed by JPL TSolucio S.L. is distributed in
 * the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied
 * warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. Unless required by
 * applicable law or agreed to in writing, software distributed under the License is
 * distributed on an "AS IS" BASIS, WITHOUT ANY WARRANTIES OR CONDITIONS OF ANY KIND,
 * either express or implied. See the License for the specific language governing
 * permissions and limitations under the License. You may obtain a copy of the License
 * at <http://corebos.org/documentation/doku.php?id=en:devel:vpl11>
 *************************************************************************************************
 *  Module       : 
 *  Version      : 5.4.0
 *  Author       : JPL TSolucio, S. L.
 *************************************************************************************************/

error_reporting("E_ALL & ~E_NOTICE & ~E_STRICT & ~E_DEPRECATED & ~E_WARNING");
require_once 'include/utils/utils.php';
require_once 'include/utils/CommonUtils.php';
global $current_user;

if(isset($_REQUEST['module_name']) && isset($_REQUEST['record_id']))
{
	$currentModule = vtlib_purify($_REQUEST['module_name']);
	$record_id = vtlib_purify($_REQUEST['record_id']);
	
	require_once 'modules/'.$currentModule.'/'.$currentModule.'.php';
	$focus = new $currentModule();
	$focus->retrieve_entity_info($record_id, $currentModule);

	// Retrieve relations map
 	$bmapname = $currentModule.'_DuplicateRelations';
	$cbMapid = GlobalVariable::getVariable('BusinessMapping_'.$bmapname, cbMap::getMapIdByName($bmapname));
	if ($cbMapid) {
		$cbMap = cbMap::getMapByID($cbMapid);
		$maped_relations = $cbMap->DuplicateRelations()->getRelatedModules();
	}

	// Duplicate Records that this Record is dependent of 
	if($cbMapid && $cbMap->DuplicateRelations()->DuplicateDirectRelations() ) {
		foreach ($focus->column_fields as $fieldname => $value) {
			$sql = "SELECT 	* FROM vtiger_field WHERE columnname = ? AND uitype IN (10,50,51,57,58,59,73,68,76,75,81,78,80)";
			$result = $adb->pquery($sql , array($fieldname));
			if($adb->num_rows($result) == 1 && $value !=0)
			{
				$sql = "SELECT setype FROM vtiger_crmentity WHERE crmid = ?";
				$get_module = $adb->pquery($sql , array($value));
				$module = $adb->query_result($get_module , 0 , "setype");
				require_once "modules/" . $module ."/". $module .".php";
				$entity = new $module();
				$entity->retrieve_entity_info($value,$module);

				sanitizeModuleFields($entity,$module);
				$entity->save($module);
				$new_entity_id = $entity->id;
				$focus->column_fields[$fieldname] = $new_entity_id;
			}
		}
	}

	sanitizeModuleFields($focus,$currentModule);
	$focus->save($currentModule);
 	$new_record_id = $focus->id;
 	$curr_tab_id = gettabid($currentModule);
 	$dependents_list = array();
 	$related_list = array();

	// Get related list
	$sql = "select related_tabid from vtiger_relatedlists where tabid=? and name=?";
	$result = $adb->pquery($sql, array($curr_tab_id,"get_related_list"));
	$noofrows = $adb->num_rows($result);
	if($noofrows){
		while( $r = $adb->fetch_array($result) ){
			$related_list[] = getTabModuleName( $r['related_tabid'] );
		}
	}

	// Get dependents list  
 	$sql = "select related_tabid from vtiger_relatedlists where tabid=? and name=?";
	$result = $adb->pquery($sql, array($curr_tab_id,"get_dependents_list"));
	$noofrows = $adb->num_rows($result);
	if($noofrows){
		while( $r = $adb->fetch_array($result) ){
			$moduleName = getTabModuleName( $r['related_tabid'] );
			if(isset($maped_relations[$moduleName]))
			{
				$dependents_list[] = $moduleName;
			}
		}
	}

	// Dependents table
	$dependent_tables = array();
	foreach ($dependents_list as $module) {
		$sql = "SELECT * FROM vtiger_fieldmodulerel JOIN vtiger_field  ON vtiger_fieldmodulerel.fieldid =  vtiger_field.fieldid WHERE module=? AND relmodule=?";
		$result = $adb->pquery($sql, array($module,$currentModule));
		$noofrows = $adb->num_rows($result);
		if($noofrows){
			while ($r = $adb->fetch_array($result)) {
				$dependent_row['tablename'] = $r['tablename'];
				$dependent_row['columname'] = $r['columnname'];
				$dependent_tables[$module] = $dependent_row;
			}
		}
	}

	// Get dependent records
	$dependent_records = array();
	foreach ($dependent_tables as $key => $value) {
		$sql = ' SELECT * FROM ' . $dependent_tables[$key]['tablename'] . ' WHERE ' . $dependent_tables[$key]['columname']. '=?';
		$result = $adb->pquery($sql, array($record_id));
		
		//Check for deleted records
		while($r = $adb->fetch_array($result))
		{
			$crmid_query = "SELECT crmid FROM vtiger_crmentity WHERE crmid=? AND deleted=?";
			$res = $adb->pquery($crmid_query, array($r[0],0));
			if($adb->num_rows($res) == 1)
			{
				$dependent_records[$key][] = $r[0];
			}
		}
	}

	// Duplicate dependent records
	 foreach ($dependent_records as $module => $records) 
	 {
	 	require_once "modules/".$module."/".$module.".php";
	 	$related_field = $dependent_tables[$module]['columname'];
		
	 	foreach ($records as $key => $record) {
	 		$entity = new $module();
	 		$entity->retrieve_entity_info($record,$module); 
			
	 		$entity->column_fields[$related_field] = $new_record_id;
	 		sanitizeModuleFields($entity,$module);
	 		$entity->save($module);
	 	}
	 }

	// Related list
	foreach ($related_list as $rel_module) {
		
		$sql = "SELECT * FROM vtiger_crmentityrel WHERE crmid=? AND relmodule=?";
		$result = $adb->pquery($sql,array($record_id,$rel_module));
		
		while ($r = $adb->fetch_array($result)) {
			$rel_crmid = $r['relcrmid'];
			$sql = "INSERT INTO vtiger_crmentityrel VALUES(?,?,?,?)";
			$adb->pquery($sql,array($new_record_id,$currentModule,$rel_crmid,$rel_module));
		}
	}

	echo json_encode(array("module"=>$currentModule, "record_id"=>$new_record_id));
	exit();
}

function sanitizeModuleFields($module,$module_name)
{
	global $adb , $current_user;

	$handler = vtws_getModuleHandlerFromName($module_name, $current_user);
	$meta = $handler->getMeta();
	$module->column_fields = DataTransform::sanitizeForInsert($module->column_fields,$meta);
	$module->column_fields = DataTransform::sanitizeTextFieldsForInsert($module->column_fields,$meta);
}

?>