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

class DocumentFolders extends CRMEntity {
	public $table_name = 'vtiger_documentfolders';
	public $table_index= 'documentfoldersid';
	public $column_fields = array();

	/** Indicator if this is a custom module or standard module */
	public $IsCustomModule = true;
	public $HasDirectImageField = false;
	public $moduleIcon = array('library' => 'standard', 'containerClass' => 'slds-icon_container slds-icon-standard-account', 'class' => 'slds-icon', 'icon'=>'folder');

	/**
	 * Mandatory table for supporting custom fields.
	 */
	public $customFieldTable = array('vtiger_documentfolderscf', 'documentfoldersid');
	// related_tables variable should define the association (relation) between dependent tables
	// FORMAT: related_tablename => array(related_tablename_column[, base_tablename, base_tablename_column[, related_module]] )
	// Here base_tablename_column should establish relation with related_tablename_column
	// NOTE: If base_tablename and base_tablename_column are not specified, it will default to modules (table_name, related_tablename_column)
	// Uncomment the line below to support custom field columns on related lists
	// public $related_tables = array('vtiger_documentfolderscf' => array('documentfoldersid', 'vtiger_documentfolders', 'documentfoldersid', 'documentfolders'));

	/**
	 * Mandatory for Saving, Include tables related to this module.
	 */
	public $tab_name = array('vtiger_crmentity', 'vtiger_documentfolders', 'vtiger_documentfolderscf');

	/**
	 * Mandatory for Saving, Include tablename and tablekey columnname here.
	 */
	public $tab_name_index = array(
		'vtiger_crmentity' => 'crmid',
		'vtiger_documentfolders'   => 'documentfoldersid',
		'vtiger_documentfolderscf' => 'documentfoldersid',
	);

	/**
	 * Mandatory for Listing (Related listview)
	 */
	public $list_fields = array(
		/* Format: Field Label => array(tablename => columnname) */
		// tablename should not have prefix 'vtiger_'
		'foldername'=> array('documentfolders' => 'foldername'),
		'parentfolder'=> array('documentfolders' => 'parentfolder'),
		'sequence'=> array('documentfolders' => 'sequence'),
		'Assigned To' => array('crmentity' => 'smownerid')
	);
	public $list_fields_name = array(
		/* Format: Field Label => fieldname */
		'foldername'=> 'foldername',
		'parentfolder'=> 'parentfolder',
		'sequence'=> 'sequence',
		'Assigned To' => 'assigned_user_id'
	);

	// Make the field link to detail view from list view (Fieldname)
	public $list_link_field = 'foldername';

	// For Popup listview and UI type support
	public $search_fields = array(
		/* Format: Field Label => array(tablename => columnname) */
		// tablename should not have prefix 'vtiger_'
		'foldername'=> array('documentfolders' => 'foldername'),
		'parentfolder'=> array('documentfolders' => 'parentfolder'),
		'sequence'=> array('documentfolders' => 'sequence'),
	);
	public $search_fields_name = array(
		/* Format: Field Label => fieldname */
		'foldername'=> 'foldername',
		'parentfolder'=> 'parentfolder',
		'sequence'=> 'sequence',
	);

	// For Popup window record selection
	public $popup_fields = array('foldername');

	// Placeholder for sort fields - All the fields will be initialized for Sorting through initSortFields
	public $sortby_fields = array();

	// For Alphabetical search
	public $def_basicsearch_col = 'foldername';

	// Column value to use on detail view record text display
	public $def_detailview_recname = 'foldername';

	// Required Information for enabling Import feature
	public $required_fields = array('foldername'=>1);

	// Callback function list during Importing
	public $special_functions = array('set_import_assigned_user');

	public $default_order_by = 'foldername';
	public $default_sort_order='ASC';
	// Used when enabling/disabling the mandatory fields for the module.
	// Refers to vtiger_field.fieldname values.
	public $mandatory_fields = array('createdtime', 'modifiedtime', 'foldername');

	public function save_module($module) {
		if ($this->HasDirectImageField) {
			$this->insertIntoAttachment($this->id, $module);
		}
	}

	/**
	 * Invoked when special actions are performed on the module.
	 * @param string Module name
	 * @param string Event Type (module.postinstall, module.disabled, module.enabled, module.preuninstall)
	 */
	public function vtlib_handler($modulename, $event_type) {
		if ($event_type == 'module.postinstall') {
			// Handle post installation actions
			global $adb, $current_user, $log;
			$this->setModuleSeqNumber('configure', $modulename, 'folder-', '0000001');
			$docfInstance = Vtiger_Module::getInstance($modulename);
			$docInstance = Vtiger_Module::getInstance('Documents');
			$docInstance->setRelatedlist($docfInstance, $modulename, array('ADD', 'SELECT'), 'get_related_list');
			$adb->pquery(
				'update vtiger_ws_entity set handler_path=?, handler_class=?, ismodule=? where name=?',
				array('include/Webservices/VtigerModuleOperation.php', 'VtigerModuleOperation', 1, $modulename)
			);
			$adb->pquery(
				'delete from vtiger_ws_entity_name where entity_id=(select id from vtiger_ws_entity where name=?)',
				array($modulename)
			);
			$adb->pquery(
				'delete from vtiger_ws_entity_tables where webservice_entity_id=(select id from vtiger_ws_entity where name=?)',
				array($modulename)
			);
		} elseif ($event_type == 'module.disabled') {
			// Handle actions when this module is disabled.
		} elseif ($event_type == 'module.enabled') {
			// Handle actions when this module is enabled.
		} elseif ($event_type == 'module.preuninstall') {
			// Handle actions when this module is about to be deleted.
		} elseif ($event_type == 'module.preupdate') {
			// Handle actions before this module is updated.
		} elseif ($event_type == 'module.postupdate') {
			// Handle actions after this module is updated.
		}
	}

	public static function createFolder($fname, $fid = 0) {
		global $adb, $current_user;
		$dbQuery = 'select 1 from vtiger_documentfolders inner join vtiger_crmentity on crmid=documentfoldersid where foldername=? and deleted=0';
		if ($fid > 0) {
			$dbQuery .= $adb->convert2Sql(' and parentfolder=?', array($fid));
		}
		$rs = $adb->pquery($dbQuery, array($fname));
		if ($rs && $adb->num_rows($rs)==0) {
			$focus = new DocumentFolders();
			$focus->column_fields['foldername'] = $fname;
			if ($fid > 0) {
				$focus->column_fields['parentfolder'] = $fid;
			}
			$focus->save('DocumentFolders');
			return true;
		}
		return false;
	}
}
?>
