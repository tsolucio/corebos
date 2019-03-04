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
require_once 'data/CRMEntity.php';
require_once 'data/Tracker.php';

class GlobalVariable extends CRMEntity {
	public $db;
	public $log;

	public $table_name = 'vtiger_globalvariable';
	public $table_index= 'globalvariableid';
	public $column_fields = array();

	/** Indicator if this is a custom module or standard module */
	public $IsCustomModule = true;
	public $HasDirectImageField = false;
	/**
	 * Mandatory table for supporting custom fields.
	 */
	public $customFieldTable = array('vtiger_globalvariablecf', 'globalvariableid');

	/**
	 * Mandatory for Saving, Include tables related to this module.
	 */
	public $tab_name = array('vtiger_crmentity', 'vtiger_globalvariable', 'vtiger_globalvariablecf');

	/**
	 * Mandatory for Saving, Include tablename and tablekey columnname here.
	 */
	public $tab_name_index = array(
		'vtiger_crmentity' => 'crmid',
		'vtiger_globalvariable'   => 'globalvariableid',
		'vtiger_globalvariablecf' => 'globalvariableid',
	);

	/**
	 * Mandatory for Listing (Related listview)
	 */
	public $list_fields = array (
		/* Format: Field Label => array(tablename => columnname) */
		// tablename should not have prefix 'vtiger_'
		'Globalno'=> array('globalvariable' => 'globalno'),
		'Name'=> array('globalvariable' => 'gvname'),
		'Value'=>array('globalvariable' => 'value'),
		'Assigned To' => array('crmentity' => 'smownerid'),
		'Default'=>array('globalvariable' => 'default_check'),
		'Mandatory'=>array('globalvariable' => 'mandatory'),
	);
	public $list_fields_name = array(
		/* Format: Field Label => fieldname */
		'Globalno'=> 'globalno',
		'Name'=> 'gvname',
		'Value'=>'value',
		'Assigned To' => 'assigned_user_id',
		'Default'=>'default_check',
		'Mandatory'=>'mandatory',
	);

	// Make the field link to detail view from list view (Fieldname)
	public $list_link_field = 'globalno';

	// For Popup listview and UI type support
	public $search_fields = array(
		/* Format: Field Label => array(tablename => columnname) */
		// tablename should not have prefix 'vtiger_'
		'Globalno'=> array('globalvariable' => 'globalno'),
		'Name'=> array('globalvariable' => 'gvname'),
		'Value'=>array('globalvariable' => 'value'),
		'Assigned To' => array('crmentity' => 'smownerid'),
		'Default'=>array('globalvariable' => 'default_check'),
		'Mandatory'=>array('globalvariable' => 'mandatory'),
	);
	public $search_fields_name = array(
		/* Format: Field Label => fieldname */
		'Globalno'=> 'globalno',
		'Name'=> 'gvname',
		'Value'=>'value',
		'Assigned To' => 'assigned_user_id',
		'Default'=>'default_check',
		'Mandatory'=>'mandatory',
	);

	// For Popup window record selection
	public $popup_fields = array('globalno');

	// Placeholder for sort fields - All the fields will be initialized for Sorting through initSortFields
	public $sortby_fields = array();

	// For Alphabetical search
	public $def_basicsearch_col = 'globalno';

	// Column value to use on detail view record text display
	public $def_detailview_recname = 'globalno';

	// Required Information for enabling Import feature
	public $required_fields = array('globalno'=>1);

	// Callback function list during Importing
	public $special_functions = array('set_import_assigned_user');

	public $default_order_by = 'globalno';
	public $default_sort_order='ASC';
	// Used when enabling/disabling the mandatory fields for the module.
	// Refers to vtiger_field.fieldname values.
	public $mandatory_fields = array('createdtime', 'modifiedtime', 'gvname');

	private static $validationinfo = array();

	public function save_module($module) {
		global $adb;
		if ($this->HasDirectImageField) {
			$this->insertIntoAttachment($this->id, $module);
		}
		if (!empty($this->column_fields['rolegv'])) {
			foreach ($this->column_fields['rolegv'] as $role) {
				$user2role_result = $adb->pquery('select userid from vtiger_user2role where roleid =?', array($role));
				if ($adb->num_rows($user2role_result)> 0) {
					$userid = $adb->query_result($user2role_result, 0, 0);
					$adb->pquery('Update vtiger_crmentity set smownerid=? where crmid=?', array($userid, $this->id));
					break;
				}
			}
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
	public function preSaveCheck($request) {
		global $adb;
		$found = false;
		$errmsg = '';
		if ($this->column_fields['mandatory'] == 'on' || $this->column_fields['mandatory'] == '1') {
			$recordid = (empty($this->id) ? 0 : $this->id);
			if (is_array($this->column_fields['module_list'])) {
				$modulelist = $this->column_fields['module_list'];
			} else {
				$modulelist = array_map('trim', explode('|##|', $this->column_fields['module_list']));
			}
			$inmodule = $this->column_fields['in_module_list'];
			$existmod = $adb->pquery('select module_list,in_module_list from vtiger_globalvariable
				left join vtiger_crmentity on vtiger_crmentity.crmid=vtiger_globalvariable.globalvariableid
				where gvname=? and deleted=0 and mandatory=1 and globalvariableid!=?', array($this->column_fields['gvname'],$recordid));
			$num = $adb->num_rows($existmod);
			$all_modules=vtws_getModuleNameList();
			$existmodul= array();
			for ($j=0; $j<$num; $j++) {
				$module_list = array_map('trim', explode('|##|', $adb->query_result($existmod, $j, 'module_list')));
				if ($adb->query_result($existmod, $j, 'in_module_list')==0) {
					$module_list = array_diff($all_modules, $module_list);
				}
				$existmodul = array_merge($existmodul, $module_list);
			}
			$existmodul = array_unique($existmodul);
			$other_modules=array_diff($all_modules, $modulelist);
			if ($inmodule == 'on' || $inmodule == '1') {
				$intersect = array_intersect($existmodul, $modulelist);
			} else {
				$intersect = array_intersect($existmodul, $other_modules);
			}
			if (count($intersect)>0) {
				$found = true;
				if (isset($request['file']) && $request['file']=='DetailViewAjax' && $request['action']=='GlobalVariableAjax') {
					$errmsg = getTranslatedString('LBL_MANDATORY_VALUEJS', 'GlobalVariable');
				} else {
					$errmsg = getTranslatedString('LBL_MANDATORY_VALUE', 'GlobalVariable');
				}
			}
		}
		return array($found,$errmsg,'EditView','');
	}

	/**
	 * Invoked when special actions are performed on the module.
	 * @param String Module name
	 * @param String Event Type (module.postinstall, module.disabled, module.enabled, module.preuninstall)
	 */
	public function vtlib_handler($modulename, $event_type) {
		if ($event_type == 'module.postinstall') {
			// TODO Handle post installation actions
			$this->setModuleSeqNumber('configure', $modulename, 'glb-', '0000001');
			// register webservice functionality
			require_once 'include/Webservices/Utils.php';
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
			if ($rdo) {
				echo 'Registered WS Operation: <b>'.$operationInfo['name'].'</b><br>';
			} else {
				echo 'WS Operation: <b>'.$operationInfo['name'].'</b> already registered<br>';
			}
		} elseif ($event_type == 'module.disabled') {
			// TODO Handle actions when this module is disabled.
		} elseif ($event_type == 'module.enabled') {
			// TODO Handle actions when this module is enabled.
		} elseif ($event_type == 'module.preuninstall') {
			// TODO Handle actions when this module is about to be deleted.
		} elseif ($event_type == 'module.preupdate') {
			// TODO Handle actions before this module is updated.
		} elseif ($event_type == 'module.postupdate') {
			// TODO Handle actions after this module is updated.
		}
	}

	private static function returnGVValue($sql, $var, $module) {
		global $adb;
		$list_of_modules=array();
		$list_of_modules['Default'] = '';
		$isBusinessMapping = (substr($var, 0, 16) == 'BusinessMapping_');
		$query=$adb->pquery($sql, array($var));
		self::$validationinfo[] = 'candidate variable records found: '.$adb->num_rows($query);
		for ($i=0; $i<$adb->num_rows($query); $i++) {
			self::$validationinfo[] = 'evaluate candidate <a href="index.php?action=DetailView&record='.$adb->query_result($query, $i, 'globalvariableid').
				'&module=GlobalVariable">'.$adb->query_result($query, $i, 'globalno').'</a>';
			if ($adb->query_result($query, $i, 'module_list')=='') {
				if ($isBusinessMapping) {
					$value = $adb->query_result($query, $i, 'bmapid');
				} else {
					$value = $adb->query_result($query, $i, 'value');
					if ($value=='[[Use Description]]') {
						$value = $adb->query_result($query, $i, 'description');
					}
				}
				$list_of_modules['Default']=$value;
			} else {
				$in_module_list=$adb->query_result($query, $i, 'in_module_list');
				$modules_list=array_map('trim', explode('|##|', $adb->query_result($query, $i, 'module_list')));
				if ($in_module_list==1) {
					$nummods = count($modules_list);
					for ($j=0; $j < $nummods; $j++) {
						if ($isBusinessMapping) {
							$value = $adb->query_result($query, $i, 'bmapid');
						} else {
							$value = $adb->query_result($query, $i, 'value');
							if ($value=='[[Use Description]]') {
								$value = $adb->query_result($query, $i, 'description');
							}
						}
						$list_of_modules[$modules_list[$j]]=$value;
					}
				} else {
					$all_modules=vtws_getModuleNameList();
					$other_modules=array_diff($all_modules, $modules_list);
					$nummods = count($other_modules);
					foreach ($other_modules as $omod) {
						if ($isBusinessMapping) {
							$value = $adb->query_result($query, $i, 'bmapid');
						} else {
							$value = $adb->query_result($query, $i, 'value');
							if ($value=='[[Use Description]]') {
								$value = $adb->query_result($query, $i, 'description');
							}
						}
						$list_of_modules[$omod]=$value;
					}
				}
			}
		}
		self::$validationinfo[] = "candidate list of modules to look for $module: ".print_r($list_of_modules, true);
		if (count($list_of_modules) > 0) {
			if (array_key_exists($module, $list_of_modules)) {
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
	public static function getVariable($var, $default, $module = '', $gvuserid = '') {
		global $adb, $current_user, $currentModule, $installationStrings;
		if (!is_object($adb) || is_null($adb->database)) {
			return $default;
		}
		if (isset($installationStrings)) {
			return $default;
		}
		self::$validationinfo = array();
		self::$validationinfo[] = "search for variable '$var' with default value of '$default'";
		if (empty($gvuserid) && !empty($current_user)) {
			$gvuserid = $current_user->id;
		}
		if (empty($gvuserid)) {
			return $default;
		}
		if (empty($module)) {
			$module = $currentModule;
		}
		$key = 'gvcache'.$var.$module.$gvuserid;
		list($value,$found) = VTCacheUtils::lookupCachedInformation($key);
		if ($found) {
			self::$validationinfo[] = "variable found in cache";
			return $value;
		}
		$value='';
		$join = ' FROM vtiger_globalvariable INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_globalvariable.globalvariableid ';
		$select = 'select * '.$join;
		$where = ' where vtiger_crmentity.deleted=0 and gvname=? ';

		$sql = 'select 1 '.$join.$where.' limit 1';
		$rs = $adb->pquery($sql, array($var));
		if (!$rs || $adb->num_rows($rs)==0) {
			self::$validationinfo[] = "no records for this variable exist, so default returned: $default";
			return $default;
		}

		$mandatory=" and mandatory='1'";
		$sql=$select.$where.$mandatory;
		self::$validationinfo[] = '---';
		$value=self::returnGVValue($sql, $var, $module);
		self::$validationinfo[] = "search as mandatory in module $module: $value";
		if ($value!='') {
			VTCacheUtils::updateCachedInformation($key, $value);
			return $value;
		}

		if (!is_numeric($gvuserid) && $gvuserid>0) {
			return $default;
		}

		$userrole = $adb->convert2Sql('inner join vtiger_user2role on vtiger_user2role.userid=?', array($gvuserid));
		$sql=$select.$userrole.$where."and rolegv like concat('%', vtiger_user2role.roleid, '%')";
		self::$validationinfo[] = '---';
		$value=self::returnGVValue($sql, $var, $module);
		self::$validationinfo[] = "search as set per user $gvuserid ROLE in module $module: $value";
		if ($value!='') {
			VTCacheUtils::updateCachedInformation($key, $value);
			return $value;
		}

		$user = $adb->convert2Sql(' and vtiger_crmentity.smownerid=?', array($gvuserid));
		$sql=$select.$where.$user;
		self::$validationinfo[] = '---';
		$value=self::returnGVValue($sql, $var, $module);
		self::$validationinfo[] = "search as set per user $gvuserid in module $module: $value";
		if ($value!='') {
			VTCacheUtils::updateCachedInformation($key, $value);
			return $value;
		}

		self::$validationinfo[] = '---';
		require_once 'include/utils/GetUserGroups.php';
		$UserGroups = new GetUserGroups();
		$UserGroups->getAllUserGroups($gvuserid);
		if (count($UserGroups->user_groups)>0) {
			$groups=implode(',', $UserGroups->user_groups);
			$group=' and vtiger_crmentity.smownerid in ('.$groups.') ';
			$sql=$select.$where.$group;
			$value=self::returnGVValue($sql, $var, $module);
			self::$validationinfo[] = "search as set per group $groups in module $module: $value";
			if ($value!='') {
				VTCacheUtils::updateCachedInformation($key, $value);
				return $value;
			}
		} else {
			self::$validationinfo[] = 'no groups to search in';
		}

		$sql=$select.$where." and default_check='1'";
		self::$validationinfo[] = '---';
		$value=self::returnGVValue($sql, $var, $module);
		self::$validationinfo[] = "search as default variable in module $module: $value";
		if ($value!='') {
			VTCacheUtils::updateCachedInformation($key, $value);
			return $value;
		}
		self::$validationinfo[] = '---';
		self::$validationinfo[] = "return default value give: $default";
		return $default;
	}

	public static function getValidationInfo() {
		return self::$validationinfo;
	}

	/**
	 * Handle saving related module information.
	 * NOTE: This function has been added to CRMEntity (base class).
	 * You can override the behavior by re-defining it here.
	 */
	// public function save_related_module($module, $crmid, $with_module, $with_crmid) { }

	/**
	 * Handle deleting related module information.
	 * NOTE: This function has been added to CRMEntity (base class).
	 * You can override the behavior by re-defining it here.
	 */
	//public function delete_related_module($module, $crmid, $with_module, $with_crmid) { }

	/**
	 * Handle getting related list information.
	 * NOTE: This function has been added to CRMEntity (base class).
	 * You can override the behavior by re-defining it here.
	 */
	//public function get_related_list($id, $cur_tab_id, $rel_tab_id, $actions=false) { }

	/**
	 * Handle getting dependents list information.
	 * NOTE: This function has been added to CRMEntity (base class).
	 * You can override the behavior by re-defining it here.
	 */
	//public function get_dependents_list($id, $cur_tab_id, $rel_tab_id, $actions=false) { }
}
?>
