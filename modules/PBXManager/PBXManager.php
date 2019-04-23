<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/
require_once 'data/CRMEntity.php';
require_once 'data/Tracker.php';

class PBXManager extends CRMEntity {
	public $db;
	public $log;

	public $table_name = 'vtiger_pbxmanager';
	public $table_index= 'pbxmanagerid';
	public $column_fields = array();

	/** Indicator if this is a custom module or standard module */
	public $IsCustomModule = false;
	public $HasDirectImageField = false;
	// Mandatory for function getGroupName
	// array(groupTableName, groupColumnId)
	// groupTableName should have (groupname column)
	//var $groupTable = array('vtiger_pbxmanagergrouprel','pbxmanagerid');

	// Mandatory table for supporting custom fields
	public $customFieldTable = array();

	// Mandatory for Saving, Include tables related to this module.
	public $tab_name = array('vtiger_crmentity', 'vtiger_pbxmanager');
	// Mandatory for Saving, Include the table name and index column mapping here.
	public $tab_name_index = array(
		'vtiger_crmentity' => 'crmid',
		'vtiger_pbxmanager' => 'pbxmanagerid',
	);

	/**
	 * Mandatory for Listing (Related listview)
	 */
	public $list_fields = array(
		/* Format: Field Label => array(tablename => columnname) */
		'Call To'=> array('pbxmanager' => 'callto'),
		'Call From'=>array('pbxmanager' => 'callfrom'),
	);
	public $list_fields_name = array(
		/* Format: Field Label => fieldname */
		'Call To'=> 'callto',
		'Call From' => 'callfrom'
	);
	public $sortby_fields = array('callto', 'callfrom', 'callid', 'timeofcall', 'status');
	// Should contain field labels
	public $detailview_links = array();

	// Make the field link to detail view from list view (Fieldname)
	public $list_link_field = 'callfrom';

	// Column value to use on detail view record text display.
	public $def_detailview_recname = '';

	// Required information for enabling Import feature
	public $required_fields = array();

	// Callback function list during Importing
	public $special_functions = array();

	// For Alphabetical search
	public $def_basicsearch_col = 'callid';

	public $default_order_by = 'timeofcall';
	public $default_sort_order='DESC';

	public function __construct() {
		global $log, $currentModule;
		$this->column_fields = getColumnFields($currentModule);
		$this->db = PearDatabase::getInstance();
		$this->log = $log;
	}

	public function save_module($module) {
	}

	/**
	 * Get list view query.
	 */
	public function getListQuery($module, $usewhere = '') {
		$query = "SELECT $this->table_name.*, vtiger_crmentity.*";
		$query .= " FROM $this->table_name";

		$query .= " INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = $this->table_name.$this->table_index
					LEFT JOIN vtiger_groups ON vtiger_groups.groupid = vtiger_crmentity.smownerid";

		// Consider custom table join as well.
		if (!empty($this->customFieldTable)) {
			$query .= " INNER JOIN ".$this->customFieldTable[0]." ON ".$this->customFieldTable[0].'.'.$this->customFieldTable[1] .
				" = $this->table_name.$this->table_index";
		}
		$query .= ' LEFT JOIN vtiger_users ON vtiger_users.id = vtiger_crmentity.smownerid ';

		$query .= ' WHERE vtiger_crmentity.deleted = 0';
		$query .= $this->getListViewSecurityParameter($module);
		return $query;
	}

	/**
	 * Apply security restriction (sharing privilege) query part for List view.
	 */
	public function getListViewSecurityParameter($module) {
		global $current_user;
		require 'user_privileges/user_privileges_'.$current_user->id.'.php';
		require 'user_privileges/sharing_privileges_'.$current_user->id.'.php';

		$sec_query = '';
		$tabid = getTabid($module);

		if ($is_admin==false && $profileGlobalPermission[1] == 1 && $profileGlobalPermission[2] == 1 && $defaultOrgSharingPermission[$tabid] == 3) {
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
					OR ( vtiger_crmentity.smownerid in (0)";

			if (!empty($this->groupTable)) {
				$sec_query .= ' AND (';

				// Build the query based on the group association of current user.
				if (count($current_user_groups) > 0) {
					$sec_query .= " vtiger_groups.groupid IN (". implode(",", $current_user_groups) .") OR ";
				}
				$sec_query .= " vtiger_groups.groupid IN
						(
							SELECT vtiger_tmp_read_group_sharing_per.sharedgroupid
							FROM vtiger_tmp_read_group_sharing_per
							WHERE userid=".$current_user->id." and tabid=".$tabid."
						)";
				$sec_query .= ") ";
			}
				$sec_query .= ")
				)";
		}
		return $sec_query;
	}

	/**
	 * Create query to export the records.
	 */
	public function create_export_query($where) {
		global $current_user;
		$thismodule = $_REQUEST['module'];

		include 'include/utils/ExportUtils.php';

		//To get the Permitted fields query and the permitted fields list
		$sql = getPermittedFieldsQuery($thismodule, 'detail_view');

		$fields_list = getFieldsListFromQuery($sql);

		$query = "SELECT $fields_list, 'vtiger_groups_groupname as Assigned To Group',
				CASE WHEN (vtiger_users.user_name NOT LIKE '') THEN vtiger_users.user_name ELSE vtiger_groups.groupname END
				AS user_name FROM vtiger_crmentity INNER JOIN $this->table_name ON vtiger_crmentity.crmid=$this->table_name.$this->table_index";

		if (!empty($this->customFieldTable)) {
			$query.=" INNER JOIN ".$this->customFieldTable[0]." ON ".$this->customFieldTable[0].'.'.$this->customFieldTable[1]." = $this->table_name.$this->table_index";
		}

		$query .=
			//"LEFT JOIN " . $this->groupTable[0] . " ON " . $this->groupTable[0].'.'.$this->groupTable[1] . " = $this->table_name.$this->table_index
			"LEFT JOIN vtiger_groups ON vtiger_groups.groupid = vtiger_crmentity.smownerid";
		$query .= " LEFT JOIN vtiger_users ON vtiger_crmentity.smownerid = vtiger_users.id and vtiger_users.status='Active'";
		$query .= " LEFT JOIN vtiger_users as vtigerCreatedBy ON vtiger_crmentity.smcreatorid = vtigerCreatedBy.id and vtigerCreatedBy.status='Active'";

		$where_auto = ' vtiger_crmentity.deleted=0';

		if ($where != '') {
			$query .= " WHERE ($where) AND $where_auto";
		} else {
			$query .= " WHERE $where_auto";
		}

		require 'user_privileges/user_privileges_'.$current_user->id.'.php';
		require 'user_privileges/sharing_privileges_'.$current_user->id.'.php';

		// Security Check for Field Access
		if ($is_admin==false && $profileGlobalPermission[1] == 1 && $profileGlobalPermission[2] == 1 && $defaultOrgSharingPermission[7] == 3) {
			//Added security check to get the permitted records only
			$query = $query." ".getListViewSecurityParameter($thismodule);
		}
		return $query;
	}

	/**
	 * Handle getting related list information.
	 * NOTE: This function has been added to CRMEntity (base class).
	 * You can override the behavior by re-defining it here.
	 */
	//public function get_related_list($id, $cur_tab_id, $rel_tab_id, $actions=false) { }

	/**
	 * Handle saving related module information.
	 * NOTE: This function has been added to CRMEntity (base class).
	 * You can override the behavior by re-defining it here.
	 */
	// function save_related_module($module, $crmid, $with_module, $with_crmid) { }

	/**
	* Invoked when special actions are performed on the module.
	* @param String Module name
	* @param String Event Type
	*/
	public function vtlib_handler($moduleName, $eventType) {
		require_once 'include/utils/utils.php';
		global $adb;
		$tabid = getTabid('Users');
		if ($eventType == 'module.postinstall') {
			// Add a block and 2 fields for Users module
			$blockid = $adb->getUniqueID('vtiger_blocks');
			$adb->query("insert into vtiger_blocks(blockid,tabid,blocklabel,sequence,show_title,visible,create_view,edit_view,detail_view,display_status)" .
					" values ($blockid,$tabid,'Asterisk Configuration',6,0,0,0,0,0,1)");

			$adb->query("insert into vtiger_field(tabid,fieldid,columnname,tablename,generatedtype,uitype,fieldname,fieldlabel,readonly," .
					" presence,defaultvalue,maximumlength,sequence,block,displaytype,typeofdata,quickcreate,quickcreatesequence,info_type) " .
					" values ($tabid,".$adb->getUniqueID('vtiger_field').",'asterisk_extension','vtiger_asteriskextensions',1,1,'asterisk_extension'," .
					" 'Asterisk Extension',1,0,0,30,1,$blockid,1,'V~O',1,NULL,'BAS')");

			$adb->query("insert into vtiger_field(tabid,fieldid,columnname,tablename,generatedtype,uitype,fieldname,fieldlabel,readonly," .
					" presence,defaultvalue,maximumlength,sequence,block,displaytype,typeofdata,quickcreate,quickcreatesequence,info_type) " .
					" values ($tabid,".$adb->getUniqueID('vtiger_field').",'use_asterisk','vtiger_asteriskextensions',1,56,'use_asterisk'," .
					"'Receive Incoming Calls',1,0,0,30,2,$blockid,1,'C~O',1,NULL,'BAS')");

			// Mark the module as Standard module
			$adb->pquery('UPDATE vtiger_tab SET customized=0 WHERE name=?', array($moduleName));
		} elseif ($eventType == 'module.disabled') {
		// TODO Handle actions when this module is disabled.
			$em = new VTEventsManager($adb);
			$em->setHandlerInActive('PBXManagerAfterSaveCreateActivity');
		} elseif ($eventType == 'module.enabled') {
		// TODO Handle actions when this module is enabled.
			$em = new VTEventsManager($adb);
			$em->setHandlerActive('PBXManagerAfterSaveCreateActivity');
		} elseif ($eventType == 'module.preuninstall') {
		// TODO Handle actions when this module is about to be deleted.
		} elseif ($eventType == 'module.preupdate') {
		// TODO Handle actions before this module is updated.
		} elseif ($eventType == 'module.postupdate') {
		// TODO Handle actions after this module is updated.
		}
	}

	public function getListButtons($app_strings) {
		$list_buttons = array();
		if (isPermitted('PBXManager', 'Delete', '') == 'yes') {
			$list_buttons['del'] = $app_strings['LBL_MASS_DELETE'];
		}
		return $list_buttons;
	}
}
?>
