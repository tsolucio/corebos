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
require_once 'vtlib/Vtiger/Module.php';

class ModCommentsCore extends CRMEntity {
	var $db, $log; // Used in class functions of CRMEntity

	var $table_name = 'vtiger_modcomments';
	var $table_index= 'modcommentsid';

	/** Indicator if this is a custom module or standard module */
	var $IsCustomModule = false;
	var $HasDirectImageField = false;
	/**
	 * Mandatory table for supporting custom fields.
	 */
	var $customFieldTable = Array('vtiger_modcommentscf', 'modcommentsid');

	/**
	 * Mandatory for Saving, Include tables related to this module.
	 */
	var $tab_name = Array('vtiger_crmentity', 'vtiger_modcomments', 'vtiger_modcommentscf');

	/**
	 * Mandatory for Saving, Include tablename and tablekey columnname here.
	 */
	var $tab_name_index = Array(
		'vtiger_crmentity' => 'crmid',
		'vtiger_modcomments' => 'modcommentsid',
		'vtiger_modcommentscf'=>'modcommentsid');

	/**
	 * Mandatory for Listing (Related listview)
	 */
	var $list_fields = Array (
		/* Format: Field Label => Array(tablename => columnname) */
		// tablename should not have prefix 'vtiger_'
		'Comment' => Array('modcomments' => 'commentcontent'),
		'Assigned To' => Array('crmentity' => 'smownerid')
	);
	var $list_fields_name = Array(
		/* Format: Field Label => fieldname */
		'Comment' => 'commentcontent',
		'Assigned To' => 'assigned_user_id'
	);

	// Make the field link to detail view
	var $list_link_field = 'commentcontent';

	// For Popup listview and UI type support
	var $search_fields = Array(
		/* Format: Field Label => Array(tablename => columnname) */
		// tablename should not have prefix 'vtiger_'
		'Comment' => Array('modcomments' => 'commentcontent')
	);
	var $search_fields_name = Array (
		/* Format: Field Label => fieldname */
		'Comment' => 'commentcontent'
	);

	// For Popup window record selection
	var $popup_fields = Array ('commentcontent');

	// Allow sorting on the following (field column names)
	var $sortby_fields = Array ('commentcontent');

	// Should contain field labels
	//var $detailview_links = Array ('Comment');

	// For Alphabetical search
	var $def_basicsearch_col = 'commentcontent';

	// Column value to use on detail view record text display
	var $def_detailview_recname = 'commentcontent';

	// Required Information for enabling Import feature
	var $required_fields = Array ('assigned_user_id'=>1);

	// Callback function list during Importing
	var $special_functions = Array('set_import_assigned_user');

	var $default_order_by = 'vtiger_modcomments.modcommentsid';
	var $default_sort_order='DESC';

	// Used when enabling/disabling the mandatory fields for the module.
	// Refers to vtiger_field.fieldname values.
	var $mandatory_fields = Array('createdtime', 'modifiedtime', 'commentcontent');

	function save_module($module) {
		global $adb;
		if ($this->HasDirectImageField) {
			$this->insertIntoAttachment($this->id,$module);
		}
		$relto = $this->column_fields['related_to'];
		if (!empty($relto)) {
			// update related assigned to email read only field
			$relemailrs = $adb->pquery('SELECT email1
				FROM vtiger_modcomments
				INNER JOIN vtiger_crmentity on crmid=related_to
				INNER JOIN vtiger_users on id = smownerid
				WHERE modcommentsid=?',array($this->id));
			$relemail = $adb->query_result($relemailrs,0,0);
			$this->column_fields['relatedassignedemail'] = $relemail;
			$adb->pquery('UPDATE vtiger_modcomments SET relatedassignedemail=? WHERE modcommentsid=?',array($relemail,$this->id));
		}
	}

	/**
	 * Get list view query (send more WHERE clause condition if required)
	 */
	public function getListQuery($module, $usewhere='') {
		$query = parent::getListQuery($module, $usewhere);
		$query .= $this->getListViewSecurityParameter($module);
		return $query;
	}

	public function getNonAdminAccessControlQuery($module, $current_user) {
		return ''; // so getListQuery doesn't get permission restriction joins added
	}

	/**
	 * Invoked when special actions are performed on the module.
	 * @param String Module name
	 * @param String Event Type (module.postinstall, module.disabled, module.enabled, module.preuninstall)
	 */
	function vtlib_handler($modulename, $event_type) {
		if($event_type == 'module.postinstall') {
			// TODO Handle post installation actions
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
}
?>
