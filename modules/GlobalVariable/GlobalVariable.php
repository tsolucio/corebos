<?php
/*************************************************************************************************
 * Copyright 2015 JPL TSolucio, S.L. -- This file is a part of TSOLUCIO coreBOS Customizations.
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
 *  Module       : GlobalVariable
 *  Version      : 5.4.0
 *  Author       : OpenCubed
 *************************************************************************************************/
require_once('data/CRMEntity.php');
require_once('data/Tracker.php');

class GlobalVariable extends CRMEntity {
	var $db, $log; // Used in class functions of CRMEntity

	var $table_name = 'vtiger_globalvariable';
	var $table_index= 'globalvariableid';
	var $column_fields = Array();

	/** Indicator if this is a custom module or standard module */
	var $IsCustomModule = true;
	var $HasDirectImageField = false;
	/**
	 * Mandatory table for supporting custom fields.
	 */
	var $customFieldTable = Array('vtiger_globalvariablecf', 'globalvariableid');

	/**
	 * Mandatory for Saving, Include tables related to this module.
	 */
	var $tab_name = Array('vtiger_crmentity', 'vtiger_globalvariable', 'vtiger_globalvariablecf');

	/**
	 * Mandatory for Saving, Include tablename and tablekey columnname here.
	 */
	var $tab_name_index = Array(
		'vtiger_crmentity' => 'crmid',
		'vtiger_globalvariable'   => 'globalvariableid',
		'vtiger_globalvariablecf' => 'globalvariableid');

	/**
	 * Mandatory for Listing (Related listview)
	 */
	var $list_fields = Array (
		/* Format: Field Label => Array(tablename => columnname) */
		// tablename should not have prefix 'vtiger_'
		'Globalno'=> Array('globalvariable' => 'globalno'),
		'Name'=> Array('globalvariable' => 'gvname'),
		'Value'=>Array('globalvariable' => 'value'),
		'Assigned To' => Array('crmentity' => 'smownerid'),
		'Default'=>Array('globalvariable' => 'default_check'),
		'Mandatory'=>Array('globalvariable' => 'mandatory')
	);
	var $list_fields_name = Array(
		/* Format: Field Label => fieldname */
		'Globalno'=> 'globalno',
		'Name'=> 'gvname',
		'Value'=>'value',
		'Assigned To' => 'assigned_user_id',
		'Default'=>'default_check',
		'Mandatory'=>'mandatory'
	);

	// Make the field link to detail view from list view (Fieldname)
	var $list_link_field = 'globalno';

	// For Popup listview and UI type support
	var $search_fields = Array(
		/* Format: Field Label => Array(tablename => columnname) */
		// tablename should not have prefix 'vtiger_'
		'Globalno'=> Array('globalvariable' => 'globalno'),
		'Name'=> Array('globalvariable' => 'gvname'),
		'Value'=>Array('globalvariable' => 'value'),
		'Assigned To' => Array('crmentity' => 'smownerid'),
		'Default'=>Array('globalvariable' => 'default_check'),
		'Mandatory'=>Array('globalvariable' => 'mandatory')
	);
	var $search_fields_name = Array(
		/* Format: Field Label => fieldname */
		'Globalno'=> 'globalno',
		'Name'=> 'gvname',
		'Value'=>'value',
		'Assigned To' => 'assigned_user_id',
		'Default'=>'default_check',
		'Mandatory'=>'mandatory'
	);

	// For Popup window record selection
	var $popup_fields = Array('globalno');

	// Placeholder for sort fields - All the fields will be initialized for Sorting through initSortFields
	var $sortby_fields = Array();

	// For Alphabetical search
	var $def_basicsearch_col = 'globalno';

	// Column value to use on detail view record text display
	var $def_detailview_recname = 'globalno';

	// Required Information for enabling Import feature
	var $required_fields = Array('globalno'=>1);

	// Callback function list during Importing
	var $special_functions = Array('set_import_assigned_user');

	var $default_order_by = 'globalno';
	var $default_sort_order='ASC';
	// Used when enabling/disabling the mandatory fields for the module.
	// Refers to vtiger_field.fieldname values.
	var $mandatory_fields = Array('createdtime', 'modifiedtime', 'gvname');

	function __construct() {
		global $log, $currentModule;
		$this->column_fields = getColumnFields($currentModule);
		$this->db = PearDatabase::getInstance();
		$this->log = $log;
		$sql = 'SELECT 1 FROM vtiger_field WHERE uitype=69 and tabid = ?';
		$tabid = getTabid($currentModule);
		$result = $this->db->pquery($sql, array($tabid));
		if ($result and $this->db->num_rows($result)==1) {
			$this->HasDirectImageField = true;
		}
	}

	function getSortOrder() {
		global $currentModule;
		$sortorder = $this->default_sort_order;
		if($_REQUEST['sorder']) $sortorder = $this->db->sql_escape_string($_REQUEST['sorder']);
		else if($_SESSION[$currentModule.'_Sort_Order'])
			$sortorder = $_SESSION[$currentModule.'_Sort_Order'];
		return $sortorder;
	}

	function getOrderBy() {
		global $currentModule;

		$use_default_order_by = '';
		if(PerformancePrefs::getBoolean('LISTVIEW_DEFAULT_SORTING', true)) {
			$use_default_order_by = $this->default_order_by;
		}

		$orderby = $use_default_order_by;
		if($_REQUEST['order_by']) $orderby = $this->db->sql_escape_string($_REQUEST['order_by']);
		else if($_SESSION[$currentModule.'_Order_By'])
			$orderby = $_SESSION[$currentModule.'_Order_By'];
		return $orderby;
	}

	function save_module($module) {
		if ($this->HasDirectImageField) {
			$this->insertIntoAttachment($this->id,$module);
		}
	}

	/* Validate values trying to be saved.
	 * @param array $_REQUEST input values. Note: column_fields array is already loaded
	 * @return array
	 *   saveerror: true if error false if not
	 *   errormessage: message to return to user if error, empty otherwise
	 *   error_action: action to redirect to inside the same module in case of error. if redirected to EditView (default action)
	 *                 all values introduced by the user will be preloaded
	 */
	function preSaveCheck($request) {
		global $adb;
		$found = false;
		$errmsg = '';
		if ($this->column_fields['mandatory'] == 'on' or $this->column_fields['mandatory'] == '1') {
			$recordid = (empty($this->id) ? 0 : $this->id);
			if (is_array($this->column_fields['module_list'])) {
				$modulelist = $this->column_fields['module_list'];
			} else {
				$modulelist = array_map('trim',explode('|##|',$this->column_fields['module_list']));
			}
			$inmodule = $this->column_fields['in_module_list'];
			$existmod = $adb->pquery('select module_list,in_module_list from vtiger_globalvariable
				left join vtiger_crmentity on vtiger_crmentity.crmid=vtiger_globalvariable.globalvariableid
				where gvname=? and deleted=0 and mandatory=1 and globalvariableid!=?',array($this->column_fields['gvname'],$recordid));
			$num = $adb->num_rows($existmod);
			$all_modules=vtws_getModuleNameList();
			$existmodul= array();
			for($j=0;$j<$num;$j++){
				$module_list = array_map('trim',explode('|##|',$adb->query_result($existmod,$j,'module_list')));
				if ($adb->query_result($existmod,$j,'in_module_list')==0) {
					$module_list = array_diff($all_modules, $module_list);
				}
				$existmodul = array_merge($existmodul,$module_list);
			}
			$existmodules = array_unique($existmodul);
			$other_modules=array_diff($all_modules,$modulelist);
			if ($inmodule == 'on' or $inmodule == '1') {
				$intersect = array_intersect($existmodul, $modulelist);
			} else {
				$intersect = array_intersect($existmodul, $other_modules);
			}
			if(count($intersect)>0){
				$found = true;
				if (isset($request['file']) and $request['file']=='DetailViewAjax' and $request['action']=='GlobalVariableAjax') {
					$errmsg = getTranslatedString('LBL_MANDATORY_VALUEJS','GlobalVariable');
				} else {
					$errmsg = getTranslatedString('LBL_MANDATORY_VALUE','GlobalVariable');
				}
			}
		}
		return array($found,$errmsg,'EditView','');
	}

	/**
	 * Return query to use based on given modulename, fieldname
	 * Useful to handle specific case handling for Popup
	 */
	function getQueryByModuleField($module, $fieldname, $srcrecord, $query='') {
		// $srcrecord could be empty
	}

	/**
	 * Get list view query (send more WHERE clause condition if required)
	 */
	function getListQuery($module, $usewhere='') {
		$query = "SELECT vtiger_crmentity.*, $this->table_name.*";

		// Keep track of tables joined to avoid duplicates
		$joinedTables = array();

		// Select Custom Field Table Columns if present
		if(!empty($this->customFieldTable)) $query .= ", " . $this->customFieldTable[0] . ".* ";

		$query .= " FROM $this->table_name";

		$query .= "	INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = $this->table_name.$this->table_index";

		$joinedTables[] = $this->table_name;
		$joinedTables[] = 'vtiger_crmentity';

		// Consider custom table join as well.
		if(!empty($this->customFieldTable)) {
			$query .= " INNER JOIN ".$this->customFieldTable[0]." ON ".$this->customFieldTable[0].'.'.$this->customFieldTable[1] .
				" = $this->table_name.$this->table_index";
			$joinedTables[] = $this->customFieldTable[0];
		}
		$query .= " LEFT JOIN vtiger_users ON vtiger_users.id = vtiger_crmentity.smownerid";
		$query .= " LEFT JOIN vtiger_groups ON vtiger_groups.groupid = vtiger_crmentity.smownerid";

		$joinedTables[] = 'vtiger_users';
		$joinedTables[] = 'vtiger_groups';

		$linkedModulesQuery = $this->db->pquery("SELECT distinct fieldname, columnname, relmodule FROM vtiger_field" .
				" INNER JOIN vtiger_fieldmodulerel ON vtiger_fieldmodulerel.fieldid = vtiger_field.fieldid" .
				" WHERE uitype='10' AND vtiger_fieldmodulerel.module=?", array($module));
		$linkedFieldsCount = $this->db->num_rows($linkedModulesQuery);

		for($i=0; $i<$linkedFieldsCount; $i++) {
			$related_module = $this->db->query_result($linkedModulesQuery, $i, 'relmodule');
			$fieldname = $this->db->query_result($linkedModulesQuery, $i, 'fieldname');
			$columnname = $this->db->query_result($linkedModulesQuery, $i, 'columnname');

			$other = CRMEntity::getInstance($related_module);
			vtlib_setup_modulevars($related_module, $other);

			if(!in_array($other->table_name, $joinedTables)) {
				$query .= " LEFT JOIN $other->table_name ON $other->table_name.$other->table_index = $this->table_name.$columnname";
				$joinedTables[] = $other->table_name;
			}
		}

		global $current_user;
		$query .= $this->getNonAdminAccessControlQuery($module,$current_user);
		$query .= "	WHERE vtiger_crmentity.deleted = 0 ".$usewhere;
		return $query;
	}

	/**
	 * Apply security restriction (sharing privilege) query part for List view.
	 */
	function getListViewSecurityParameter($module) {
		global $current_user;
		require('user_privileges/user_privileges_'.$current_user->id.'.php');
		require('user_privileges/sharing_privileges_'.$current_user->id.'.php');

		$sec_query = '';
		$tabid = getTabid($module);

		if($is_admin==false && $profileGlobalPermission[1] == 1 && $profileGlobalPermission[2] == 1
			&& $defaultOrgSharingPermission[$tabid] == 3) {

				$sec_query .= " AND (vtiger_crmentity.smownerid in($current_user->id) OR vtiger_crmentity.smownerid IN 
					(
						SELECT vtiger_user2role.userid FROM vtiger_user2role 
						INNER JOIN vtiger_users ON vtiger_users.id=vtiger_user2role.userid 
						INNER JOIN vtiger_role ON vtiger_role.roleid=vtiger_user2role.roleid 
						WHERE vtiger_role.parentrole LIKE '".$current_user_parent_role_seq."::%'
					) 
					OR vtiger_crmentity.smownerid IN 
					(
						SELECT shareduserid FROM vtiger_tmp_read_user_sharing_per 
						WHERE userid=".$current_user->id." AND tabid=".$tabid."
					) 
					OR (";

					// Build the query based on the group association of current user.
					if(sizeof($current_user_groups) > 0) {
						$sec_query .= " vtiger_groups.groupid IN (". implode(",", $current_user_groups) .") OR ";
					}
					$sec_query .= " vtiger_groups.groupid IN 
						(
							SELECT vtiger_tmp_read_group_sharing_per.sharedgroupid 
							FROM vtiger_tmp_read_group_sharing_per
							WHERE userid=".$current_user->id." and tabid=".$tabid."
						)";
				$sec_query .= ")
				)";
		}
		return $sec_query;
	}

	/**
	 * Create query to export the records.
	 */
	function create_export_query($where)
	{
		global $current_user;
		$thismodule = $_REQUEST['module'];

		include("include/utils/ExportUtils.php");

		//To get the Permitted fields query and the permitted fields list
		$sql = getPermittedFieldsQuery($thismodule, "detail_view");

		$fields_list = getFieldsListFromQuery($sql);

		$query = "SELECT $fields_list, vtiger_users.user_name AS user_name 
				FROM vtiger_crmentity INNER JOIN $this->table_name ON vtiger_crmentity.crmid=$this->table_name.$this->table_index";

		if(!empty($this->customFieldTable)) {
			$query .= " INNER JOIN ".$this->customFieldTable[0]." ON ".$this->customFieldTable[0].'.'.$this->customFieldTable[1] .
				" = $this->table_name.$this->table_index";
		}

		$query .= " LEFT JOIN vtiger_groups ON vtiger_groups.groupid = vtiger_crmentity.smownerid";
		$query .= " LEFT JOIN vtiger_users ON vtiger_crmentity.smownerid = vtiger_users.id and vtiger_users.status='Active'";

		$linkedModulesQuery = $this->db->pquery("SELECT distinct fieldname, columnname, relmodule FROM vtiger_field" .
				" INNER JOIN vtiger_fieldmodulerel ON vtiger_fieldmodulerel.fieldid = vtiger_field.fieldid" .
				" WHERE uitype='10' AND vtiger_fieldmodulerel.module=?", array($thismodule));
		$linkedFieldsCount = $this->db->num_rows($linkedModulesQuery);

		$rel_mods[$this->table_name] = 1;
		for($i=0; $i<$linkedFieldsCount; $i++) {
			$related_module = $this->db->query_result($linkedModulesQuery, $i, 'relmodule');
			$fieldname = $this->db->query_result($linkedModulesQuery, $i, 'fieldname');
			$columnname = $this->db->query_result($linkedModulesQuery, $i, 'columnname');

			$other = CRMEntity::getInstance($related_module);
			vtlib_setup_modulevars($related_module, $other);

			if($rel_mods[$other->table_name]) {
				$rel_mods[$other->table_name] = $rel_mods[$other->table_name] + 1;
				$alias = $other->table_name.$rel_mods[$other->table_name];
				$query_append = "as $alias";
			} else {
				$alias = $other->table_name;
				$query_append = '';
				$rel_mods[$other->table_name] = 1;
			}

			$query .= " LEFT JOIN $other->table_name $query_append ON $alias.$other->table_index = $this->table_name.$columnname";
		}

		$query .= $this->getNonAdminAccessControlQuery($thismodule,$current_user);
		$where_auto = " vtiger_crmentity.deleted=0";

		if($where != '') $query .= " WHERE ($where) AND $where_auto";
		else $query .= " WHERE $where_auto";

		return $query;
	}

	/**
	 * Initialize this instance for importing.
	 */
	function initImport($module) {
		$this->db = PearDatabase::getInstance();
		$this->initImportableFields($module);
	}

	/**
	 * Create list query to be shown at the last step of the import.
	 * Called From: modules/Import/UserLastImport.php
	 */
	function create_import_query($module) {
		global $current_user;
		$query = "SELECT vtiger_crmentity.crmid, case when (vtiger_users.user_name not like '') then vtiger_users.user_name else vtiger_groups.groupname end as user_name, $this->table_name.* FROM $this->table_name
			INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = $this->table_name.$this->table_index
			LEFT JOIN vtiger_users_last_import ON vtiger_users_last_import.bean_id=vtiger_crmentity.crmid
			LEFT JOIN vtiger_users ON vtiger_users.id = vtiger_crmentity.smownerid
			LEFT JOIN vtiger_groups ON vtiger_groups.groupid = vtiger_crmentity.smownerid
			WHERE vtiger_users_last_import.assigned_user_id='$current_user->id'
			AND vtiger_users_last_import.bean_type='$module'
			AND vtiger_users_last_import.deleted=0";
		return $query;
	}

	/**
	 * Delete the last imported records.
	 */
	function undo_import($module, $user_id) {
		global $adb;
		$count = 0;
		$query1 = "select bean_id from vtiger_users_last_import where assigned_user_id=? AND bean_type='$module' AND deleted=0";
		$result1 = $adb->pquery($query1, array($user_id)) or die("Error getting last import for undo: ".mysql_error());
		while ( $row1 = $adb->fetchByAssoc($result1))
		{
			$query2 = "update vtiger_crmentity set deleted=1 where crmid=?";
			$result2 = $adb->pquery($query2, array($row1['bean_id'])) or die("Error undoing last import: ".mysql_error());
			$count++;
		}
		return $count;
	}

	/**
	 * Transform the value while exporting
	 */
	function transform_export_value($key, $value) {
		return parent::transform_export_value($key, $value);
	}

	/**
	 * Function which will set the assigned user id for import record.
	 */
	function set_import_assigned_user()
	{
		global $current_user, $adb;
		$record_user = $this->column_fields["assigned_user_id"];

		if($record_user != $current_user->id){
			$sqlresult = $adb->pquery("select id from vtiger_users where id = ? union select groupid as id from vtiger_groups where groupid = ?", array($record_user, $record_user));
			if($this->db->num_rows($sqlresult)!= 1) {
				$this->column_fields["assigned_user_id"] = $current_user->id;
			} else {
				$row = $adb->fetchByAssoc($sqlresult, -1, false);
				if (isset($row['id']) && $row['id'] != -1) {
					$this->column_fields["assigned_user_id"] = $row['id'];
				} else {
					$this->column_fields["assigned_user_id"] = $current_user->id;
				}
			}
		}
	}

	/**
	 * Function which will give the basic query to find duplicates
	 */
	function getDuplicatesQuery($module,$table_cols,$field_values,$ui_type_arr,$select_cols='') {
		$select_clause = "SELECT ". $this->table_name .".".$this->table_index ." AS recordid, vtiger_users_last_import.deleted,".$table_cols;

		// Select Custom Field Table Columns if present
		if(isset($this->customFieldTable)) $query .= ", " . $this->customFieldTable[0] . ".* ";

		$from_clause = " FROM $this->table_name";

		$from_clause .= " INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = $this->table_name.$this->table_index";

		// Consider custom table join as well.
		if(isset($this->customFieldTable)) {
			$from_clause .= " INNER JOIN ".$this->customFieldTable[0]." ON ".$this->customFieldTable[0].'.'.$this->customFieldTable[1] .
				" = $this->table_name.$this->table_index";
		}
		$from_clause .= " LEFT JOIN vtiger_users ON vtiger_users.id = vtiger_crmentity.smownerid
						LEFT JOIN vtiger_groups ON vtiger_groups.groupid = vtiger_crmentity.smownerid";

		$where_clause = " WHERE vtiger_crmentity.deleted = 0";
		$where_clause .= $this->getListViewSecurityParameter($module);

		if (isset($select_cols) && trim($select_cols) != '') {
			$sub_query = "SELECT $select_cols FROM $this->table_name AS t " .
				" INNER JOIN vtiger_crmentity AS crm ON crm.crmid = t.".$this->table_index;
			// Consider custom table join as well.
			if(isset($this->customFieldTable)) {
				$sub_query .= " LEFT JOIN ".$this->customFieldTable[0]." tcf ON tcf.".$this->customFieldTable[1]." = t.$this->table_index";
			}
			$sub_query .= " WHERE crm.deleted=0 GROUP BY $select_cols HAVING COUNT(*)>1";
		} else {
			$sub_query = "SELECT $table_cols $from_clause $where_clause GROUP BY $table_cols HAVING COUNT(*)>1";
		}

		$query = $select_clause . $from_clause .
					" LEFT JOIN vtiger_users_last_import ON vtiger_users_last_import.bean_id=" . $this->table_name .".".$this->table_index .
					" INNER JOIN (" . $sub_query . ") AS temp ON ".get_on_clause($field_values,$ui_type_arr,$module) .
					$where_clause .
					" ORDER BY $table_cols,". $this->table_name .".".$this->table_index ." ASC";

		return $query;
	}

	/**
	 * Invoked when special actions are performed on the module.
	 * @param String Module name
	 * @param String Event Type (module.postinstall, module.disabled, module.enabled, module.preuninstall)
	 */
	function vtlib_handler($modulename, $event_type) {
		if($event_type == 'module.postinstall') {
			// TODO Handle post installation actions
			$this->setModuleSeqNumber('configure', $modulename, 'glb-', '0000001');
			// register webservice functionality
			require_once('include/Webservices/Utils.php');
			$operationInfo = array(
				'name'    => 'SearchGlobalVar',
				'include' => 'modules/GlobalVariable/SearchGlobalVarws.php',
				'handler' => 'cbws_SearchGlobalVar',
				'prelogin'=> 0,
				'type'    => 'GET',
				'parameters' => array(
					array('name' => 'gvname','type' => 'string'),
					array('name' => 'defaultvalue','type' => 'string'),
					array('name' => 'gvmodule','type' => 'string'),
				)
			);
			$rdo = registerWSAPI($operationInfo);
			if ($rdo)
				echo 'Registered WS Operation: <b>'.$operationInfo['name'].'</b><br>';
			else
				echo 'WS Operation: <b>'.$operationInfo['name'].'</b> already registered<br>';
		} else if($event_type == 'module.disabled') {
			// TODO Handle actions when this module is disabled.
		} else if($event_type == 'module.enabled') {
			// TODO Handle actions when this module is enabled.
		} else if($event_type == 'module.preuninstall') {
			// TODO Handle actions when this module is about to be deleted.
		} else if($event_type == 'module.preupdate') {
			// TODO Handle actions before this module is updated.
		} else if($event_type == 'module.postupdate') {
			// TODO Handle actions after this module is updated.
		}
	}

	function return_global_var_value($sql,$var,$module){
		global $log,$adb,$gvvalidationinfo;
		$list_of_modules=array();
		$list_of_modules['Default'] = '';
		$query=$adb->pquery($sql,array($var));
		$gvvalidationinfo[] = 'candidate variable records found: '.$adb->num_rows($query);
		for ($i=0;$i<$adb->num_rows($query);$i++) {
			$gvvalidationinfo[] = 'evaluate candidate <a href="index.php?action=DetailView&record='.$adb->query_result($query,$i,'globalvariableid').'&module=GlobalVariable">'.$adb->query_result($query,$i,'globalno').'</a>';
			if ($adb->query_result($query,$i,'module_list')=='') {
				$list_of_modules['Default']=$adb->query_result($query,$i,'value');
			} else {
				$in_module_list=$adb->query_result($query,$i,'in_module_list');
				$modules_list=array_map('trim', explode('|##|',$adb->query_result($query,$i,'module_list')));
				if ($in_module_list==1) {
					for($j=0;$j<sizeof($modules_list);$j++) {
						$list_of_modules[$modules_list[$j]]=$adb->query_result($query,$i,'value');
					}
				} else {
					$all_modules=vtws_getModuleNameList();
					$other_modules=array_diff($all_modules,$modules_list);
					for($l=0;$l<sizeof($other_modules);$l++){
						$list_of_modules[$other_modules[$l]]=$adb->query_result($query,$i,'value');
					}
				}
			}
		}
		$gvvalidationinfo[] = "candidate list of modules to look for $module: ".print_r($list_of_modules,true);
		if (sizeof($list_of_modules)>0) {
			if (array_key_exists($module,$list_of_modules)) {
				return $list_of_modules[$module];
			} else {
				return $list_of_modules['Default'];
			}
		}
		return '';
	}

	/* returns the value of a global variable depending on the different escalation options
	 * param $var: the name of variable
	 * param $defalt: default value in case the variable is not found in the module
	 * returns: value of the variable following these rules:
	 *   search for and return the first one found:
	 *   - $var + mandatory=true + ('In Module List' ? $current_module in Module : $current_module not in Module)
	 *   - $var + mandatory=true
	 *   - $var + $current_user + ('In Module List' ? $current_module in Module : $current_module not in Module)
	 *   - $var + $current_user
	 *   - $var + group (any group the $current_user belongs to, return the first one) + ('In Module List' ? $current_module in Module : $current_module not in Module)
	 *   - $var + group (any group the $current_user belongs to, return the first one)
	 *   - $var + default=true + ('In Module List' ? $current_module in Module : $current_module not in Module)
	 *   - $var + default=true
	 *   - return $default
	 */
	public static function getVariable($var,$default, $module='', $gvuserid='') {
		global $adb,$current_user, $gvvalidationinfo, $currentModule;
		$gvvalidationinfo[] = "search for variable '$var' with default value of '$default'";
		$key = md5('gvcache'.$var.$module.$gvuserid);
		list($value,$found) = VTCacheUtils::lookupCachedInformation($key);
		if ($found) {
			$gvvalidationinfo[] = "variable found in cache";
			return $value;
		}
		$value='';
		$list_of_modules=array();
		if (empty($module)) $module = $currentModule;
		if (empty($gvuserid)) $gvuserid = $current_user->id;
		$focus = CRMEntity::getInstance('GlobalVariable');
		$select = 'SELECT *
		 FROM vtiger_globalvariable
		 INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_globalvariable.globalvariableid ';
		$where = ' where vtiger_crmentity.deleted=0 and gvname=? ';

		$mandatory=" and mandatory='1'";
		$sql=$select.$where.$mandatory;
		$gvvalidationinfo[] = '---';
		$value=$focus->return_global_var_value($sql,$var,$module);
		$gvvalidationinfo[] = "search as mandatory in module $module: $value";
		if ($value!='') {
			VTCacheUtils::updateCachedInformation($key, $value);
			return $value;
		}

		$user = " and vtiger_crmentity.smownerid=$gvuserid";
		$sql=$select.$where.$user;
		$gvvalidationinfo[] = '---';
		$value=$focus->return_global_var_value($sql,$var,$module);
		$gvvalidationinfo[] = "search as set per user $gvuserid in module $module: $value";
		if ($value!='') {
			VTCacheUtils::updateCachedInformation($key, $value);
			return $value;
		}

		$gvvalidationinfo[] = '---';
		require_once('include/utils/GetUserGroups.php');
		$UserGroups = new GetUserGroups();
		$UserGroups->getAllUserGroups($gvuserid);
		if (count($UserGroups->user_groups)>0) {
			$groups=implode(',',$UserGroups->user_groups);
			$group=' and vtiger_crmentity.smownerid in ('.$groups.') ';
			$sql=$select.$where.$group;
			$value=$focus->return_global_var_value($sql,$var,$module);
			$gvvalidationinfo[] = "search as set per group $groups in module $module: $value";
			if ($value!='') {
				VTCacheUtils::updateCachedInformation($key, $value);
				return $value;
			}
		} else {
			$gvvalidationinfo[] = 'no groups to search in';
		}

		$sql=$select.$where." and default_check='1'";
		$gvvalidationinfo[] = '---';
		$value=$focus->return_global_var_value($sql,$var,$module);
		$gvvalidationinfo[] = "search as default variable in module $module: $value";
		if ($value!='') {
			VTCacheUtils::updateCachedInformation($key, $value);
			return $value;
		}
		$gvvalidationinfo[] = '---';
		$gvvalidationinfo[] = "return default value give: $default";
		return $default;
	}

	/** 
	 * Handle saving related module information.
	 * NOTE: This function has been added to CRMEntity (base class).
	 * You can override the behavior by re-defining it here.
	 */
	// function save_related_module($module, $crmid, $with_module, $with_crmid) { }

	/**
	 * Handle deleting related module information.
	 * NOTE: This function has been added to CRMEntity (base class).
	 * You can override the behavior by re-defining it here.
	 */
	//function delete_related_module($module, $crmid, $with_module, $with_crmid) { }

	/**
	 * Handle getting related list information.
	 * NOTE: This function has been added to CRMEntity (base class).
	 * You can override the behavior by re-defining it here.
	 */
	//function get_related_list($id, $cur_tab_id, $rel_tab_id, $actions=false) { }

	/**
	 * Handle getting dependents list information.
	 * NOTE: This function has been added to CRMEntity (base class).
	 * You can override the behavior by re-defining it here.
	 */
	//function get_dependents_list($id, $cur_tab_id, $rel_tab_id, $actions=false) { }
}
?>
