<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/
require_once('data/CRMEntity.php');
require_once('data/Tracker.php');
require_once('modules/cbMap/processmap/processMap.php');

class cbMap extends CRMEntity {
	var $db, $log; // Used in class functions of CRMEntity

	var $table_name = 'vtiger_cbmap';
	var $table_index= 'cbmapid';
	var $column_fields = Array();

	/** Indicator if this is a custom module or standard module */
	var $IsCustomModule = true;
	var $HasDirectImageField = false;
	/**
	 * Mandatory table for supporting custom fields.
	 */
	var $customFieldTable = Array('vtiger_cbmapcf', 'cbmapid');
	// Uncomment the line below to support custom field columns on related lists
	// var $related_tables = Array('vtiger_payslipcf'=>array('payslipid','vtiger_payslip', 'payslipid'));

	/**
	 * Mandatory for Saving, Include tables related to this module.
	 */
	var $tab_name = Array('vtiger_crmentity', 'vtiger_cbmap', 'vtiger_cbmapcf');

	/**
	 * Mandatory for Saving, Include tablename and tablekey columnname here.
	 */
	var $tab_name_index = Array(
		'vtiger_crmentity' => 'crmid',
		'vtiger_cbmap'   => 'cbmapid',
		'vtiger_cbmapcf' => 'cbmapid');

	/**
	 * Mandatory for Listing (Related listview)
	 */
	var $list_fields = Array (
		/* Format: Field Label => Array(tablename => columnname) */
		// tablename should not have prefix 'vtiger_'
		'Map Number'=> Array('cbmap'=> 'mapnumber'),
		'Map Name'=> Array('cbmap'=> 'mapname'),
		'Map Type'=> Array('cbmap'=> 'maptype'),
		'Target Module'=> Array('cbmap'=> 'targetname'),
		'Description' => Array('crmentity'=>'description')
	);
	var $list_fields_name = Array(
		/* Format: Field Label => fieldname */
		'Map Number'=> 'mapnumber',
		'Map Name'=> 'mapname',
		'Map Type'=> 'maptype',
		'Target Module'=> 'targetname',
		'Description' => 'description'
	);

	// Make the field link to detail view from list view (Fieldname)
	var $list_link_field = 'mapname';

	// For Popup listview and UI type support
	var $search_fields = Array(
		/* Format: Field Label => Array(tablename => columnname) */
		// tablename should not have prefix 'vtiger_'
		'Map Number'=> Array('cbmap'=> 'mapnumber'),
		'Map Name'=> Array('cbmap'=> 'mapname'),
		'Map Type'=> Array('cbmap'=> 'maptype'),
		'Target Module'=> Array('cbmap'=> 'targetname'),
		'Description' => Array('crmentity'=>'description')
	);
	var $search_fields_name = Array(
		/* Format: Field Label => fieldname */
		'Map Number'=> 'mapnumber',
		'Map Name'=> 'mapname',
		'Map Type'=> 'maptype',
		'Target Module'=> 'targetname',
		'Description' => 'description'
	);

	// For Popup window record selection
	var $popup_fields = Array('mapname');

	// Placeholder for sort fields - All the fields will be initialized for Sorting through initSortFields
	var $sortby_fields = Array();

	// For Alphabetical search
	var $def_basicsearch_col = 'mapname';

	// Column value to use on detail view record text display
	var $def_detailview_recname = 'mapname';

	// Required Information for enabling Import feature
	var $required_fields = Array('mapname'=>1);

	// Callback function list during Importing
	var $special_functions = Array('set_import_assigned_user');

	var $default_order_by = 'mapname';
	var $default_sort_order='ASC';
	// Used when enabling/disabling the mandatory fields for the module.
	// Refers to vtiger_field.fieldname values.
	var $mandatory_fields = Array('createdtime', 'modifiedtime', 'mapname');

	function save_module($module) {
		if ($this->HasDirectImageField) {
			$this->insertIntoAttachment($this->id,$module);
		}
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
			$modGV=Vtiger_Module::getInstance('GlobalVariable');
			$modMap=Vtiger_Module::getInstance('cbMap');
			if ($modGV) {
				$blockInstance = VTiger_Block::getInstance('LBL_GLOBAL_VARIABLE_INFORMATION',$modGV);
				$field = new Vtiger_Field();
				$field->name = 'bmapid';
				$field->label= 'cbMap';
				$field->table = $module->basetable;
				$field->column = 'bmapid';
				$field->columntype = 'INT(11)';
				$field->uitype = 10;
				$field->displaytype = 1;
				$field->typeofdata = 'V~O';
				$field->presence = 0;
				$blockInstance->addField($field);
				$field->setRelatedModules(Array('cbMap'));
				$modMap->setRelatedList($modGV, 'GlobalVariable', Array('ADD'),'get_dependents_list');
			}
			$this->setModuleSeqNumber('configure', $modulename, 'BMAP-', '0000001');
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

	public function __call($name, $arguments) {
		require_once 'modules/cbMap/processmap/'.$name.'.php';
		$processmap = new $name($this);
		$ret = $processmap->processMap($arguments);
		return $ret;
	}

	public static function getMapByID($cbmapid) {
		$cbmap = new cbMap();
		$cbmap->retrieve_entity_info($cbmapid, 'cbMap');
		return $cbmap;
	}

	public static function getMapByName($name,$type='') {
		global $adb;
		$sql = 'select cbmapid
			from vtiger_cbmap
			inner join vtiger_crmentity on crmid=cbmapid
			where deleted=0 and mapname=?';
		$prm = array($name);
		if ($type!='') {
			$sql .= ' and maptype=?';
			$prm[] = $type;
		}
		$mrs = $adb->pquery($sql, $prm);
		if ($mrs and $adb->num_rows($mrs)>0) {
			$cbmapid = $adb->query_result($mrs, 0, 0);
			$cbmap = new cbMap();
			$cbmap->retrieve_entity_info($cbmapid, 'cbMap');
			return $cbmap;
		} else {
			return null;
		}
	}

	public static function getMapIdByName($name) {
		global $adb;
		$mrs = $adb->pquery('select cbmapid
			from vtiger_cbmap
			inner join vtiger_crmentity on crmid=cbmapid
			where deleted=0 and mapname=?', array($name));
		if ($mrs and $adb->num_rows($mrs)>0) {
			$cbmapid = $adb->query_result($mrs, 0, 0);
			return $cbmapid;
		} else {
			return 0;
		}
	}

}
?>
