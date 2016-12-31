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

	public static function return_global_var_value($sql,$var,$module){
		global $log,$adb,$gvvalidationinfo;
		$list_of_modules=array();
		$list_of_modules['Default'] = '';
		$isBusinessMapping = (substr($var, 0, 16) == 'BusinessMapping_');
		$query=$adb->pquery($sql,array($var));
		$gvvalidationinfo[] = 'candidate variable records found: '.$adb->num_rows($query);
		for ($i=0;$i<$adb->num_rows($query);$i++) {
			$gvvalidationinfo[] = 'evaluate candidate <a href="index.php?action=DetailView&record='.$adb->query_result($query,$i,'globalvariableid').'&module=GlobalVariable">'.$adb->query_result($query,$i,'globalno').'</a>';
			if ($adb->query_result($query,$i,'module_list')=='') {
				if ($isBusinessMapping) {
					$value = $adb->query_result($query,$i,'bmapid');
				} else {
					$value = $adb->query_result($query,$i,'value');
					if ($value=='[[Use Description]]') {
						$value = $adb->query_result($query,$i,'description');
					}
				}
				$list_of_modules['Default']=$value;
			} else {
				$in_module_list=$adb->query_result($query,$i,'in_module_list');
				$modules_list=array_map('trim', explode('|##|',$adb->query_result($query,$i,'module_list')));
				if ($in_module_list==1) {
					for($j=0;$j<sizeof($modules_list);$j++) {
						if ($isBusinessMapping) {
							$value = $adb->query_result($query,$i,'bmapid');
						} else {
							$value = $adb->query_result($query,$i,'value');
							if ($value=='[[Use Description]]') {
								$value = $adb->query_result($query,$i,'description');
							}
						}
						$list_of_modules[$modules_list[$j]]=$value;
					}
				} else {
					$all_modules=vtws_getModuleNameList();
					$other_modules=array_diff($all_modules,$modules_list);
					for($l=0;$l<sizeof($other_modules);$l++){
						if ($isBusinessMapping) {
							$value = $adb->query_result($query,$i,'bmapid');
						} else {
							$value = $adb->query_result($query,$i,'value');
							if ($value=='[[Use Description]]') {
								$value = $adb->query_result($query,$i,'description');
							}
						}
						$value = ($isBusinessMapping ? $adb->query_result($query,$i,'bmapid') : $adb->query_result($query,$i,'value'));
						$list_of_modules[$other_modules[$l]]=$value;
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
		global $adb, $current_user, $gvvalidationinfo, $currentModule, $installationStrings;
		if (!is_object($adb) or is_null($adb->database)) return $default;
		if (isset($installationStrings)) return $default;
		$gvvalidationinfo[] = "search for variable '$var' with default value of '$default'";
		if (empty($gvuserid) and !empty($current_user)) $gvuserid = $current_user->id;
		if (empty($gvuserid)) return $default;
		if (empty($module)) $module = $currentModule;
		$key = md5('gvcache'.$var.$module.$gvuserid);
		list($value,$found) = VTCacheUtils::lookupCachedInformation($key);
		if ($found) {
			$gvvalidationinfo[] = "variable found in cache";
			return $value;
		}
		$value='';
		$list_of_modules=array();
		$select = 'SELECT *
		 FROM vtiger_globalvariable
		 INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_globalvariable.globalvariableid ';
		$where = ' where vtiger_crmentity.deleted=0 and gvname=? ';

		$mandatory=" and mandatory='1'";
		$sql=$select.$where.$mandatory;
		$gvvalidationinfo[] = '---';
		$value=self::return_global_var_value($sql,$var,$module);
		$gvvalidationinfo[] = "search as mandatory in module $module: $value";
		if ($value!='') {
			VTCacheUtils::updateCachedInformation($key, $value);
			return $value;
		}

		if (!is_numeric($gvuserid) and $gvuserid>0) return $default;
		$user = $adb->convert2Sql(' and vtiger_crmentity.smownerid=?', array($gvuserid));
		$sql=$select.$where.$user;
		$gvvalidationinfo[] = '---';
		$value=self::return_global_var_value($sql,$var,$module);
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
			$value=self::return_global_var_value($sql,$var,$module);
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
		$value=self::return_global_var_value($sql,$var,$module);
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
