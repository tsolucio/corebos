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

class cbupdater extends CRMEntity {
	public $table_name = 'vtiger_cbupdater';
	public $table_index= 'cbupdaterid';
	public $column_fields = array();

	/** Indicator if this is a custom module or standard module */
	public $IsCustomModule = true;
	public $HasDirectImageField = false;
	public $moduleIcon = array('library' => 'standard', 'containerClass' => 'slds-icon_container slds-icon-standard-loop', 'class' => 'slds-icon', 'icon'=>'loop');

	/**
	 * Mandatory table for supporting custom fields.
	 */
	public $customFieldTable = array('vtiger_cbupdatercf', 'cbupdaterid');

	/**
	 * Mandatory for Saving, Include tables related to this module.
	 */
	public $tab_name = array('vtiger_crmentity', 'vtiger_cbupdater', 'vtiger_cbupdatercf');

	/**
	 * Mandatory for Saving, Include tablename and tablekey columnname here.
	 */
	public $tab_name_index = array(
		'vtiger_crmentity' => 'crmid',
		'vtiger_cbupdater'   => 'cbupdaterid',
		'vtiger_cbupdatercf' => 'cbupdaterid');

	/**
	 * Mandatory for Listing (Related listview)
	 */
	public $list_fields = array(
		/* Format: Field Label => array(tablename => columnname) */
		// tablename should not have prefix 'vtiger_'
		'cbupd_no'=> array('cbupdater' => 'cbupd_no'),
		'execdate'=> array('cbupdater' => 'execdate'),
		'author'=> array('cbupdater' => 'author'),
		'filename'=> array('cbupdater' => 'filename'),
		'execstate'=> array('cbupdater' => 'execstate'),
		'systemupdate'=> array('cbupdater' => 'systemupdate'),
		'Assigned To' => array('crmentity' => 'smownerid')
	);
	public $list_fields_name = array(
		/* Format: Field Label => fieldname */
		'cbupd_no'=> 'cbupd_no',
		'execdate'=> 'execdate',
		'author'=> 'author',
		'filename'=> 'filename',
		'execstate'=> 'execstate',
		'systemupdate'=> 'systemupdate',
		'Assigned To' => 'assigned_user_id'
	);

	// Make the field link to detail view from list view (Fieldname)
	public $list_link_field = 'cbupd_no';

	// For Popup listview and UI type support
	public $search_fields = array(
		/* Format: Field Label => array(tablename => columnname) */
		// tablename should not have prefix 'vtiger_'
		'cbupd_no'=> array('cbupdater' => 'cbupd_no'),
		'execdate'=> array('cbupdater' => 'execdate'),
		'author'=> array('cbupdater' => 'author'),
		'filename'=> array('cbupdater' => 'filename'),
		'execstate'=> array('cbupdater' => 'execstate'),
		'systemupdate'=> array('cbupdater' => 'systemupdate'),
	);
	public $search_fields_name = array(
		/* Format: Field Label => fieldname */
		'cbupd_no'=> 'cbupd_no',
		'execdate'=> 'execdate',
		'author'=> 'author',
		'filename'=> 'filename',
		'execstate'=> 'execstate',
		'systemupdate'=> 'systemupdate',
	);

	// For Popup window record selection
	public $popup_fields = array('cbupd_no');

	// Placeholder for sort fields - All the fields will be initialized for Sorting through initSortFields
	public $sortby_fields = array();

	// For Alphabetical search
	public $def_basicsearch_col = 'cbupd_no';

	// Column value to use on detail view record text display
	public $def_detailview_recname = 'cbupd_no';

	// Required Information for enabling Import feature
	public $required_fields = array('cbupd_no'=>1);

	// Callback function list during Importing
	public $special_functions = array('set_import_assigned_user');

	public $default_order_by = 'cbupd_no';
	public $default_sort_order='ASC';
	// Used when enabling/disabling the mandatory fields for the module.
	// Refers to vtiger_field.fieldname values.
	public $mandatory_fields = array('createdtime', 'modifiedtime', 'cbupd_no');

	/**
	 * Function to Listview buttons
	 * return array  $list_buttons - for module (eg: 'Accounts')
	 */
	public function getListButtons($app_strings) {
		if ($this->column_fields['appcs']=='1') {
			return array();
		} else {
			return parent::getListButtons($app_strings);
		}
	}

	public static function exists($cbinfo) {
		global $adb;
		if (empty($cbinfo['filename']) || empty($cbinfo['classname'])) {
			return false;
		}
		$crmEntityTable = CRMEntity::getcrmEntityTableAlias('cbupdater');
		$sql = 'select 1 from vtiger_cbupdater inner join '.$crmEntityTable." on crmid=cbupdaterid
			where deleted=0 and (pathfilename=? or pathfilename='' or pathfilename is null) and filename=? and classname=?";
		$rs = $adb->pquery($sql, array($cbinfo['filename'], basename($cbinfo['filename'], '.php'), $cbinfo['classname']));
		return ($rs && $adb->num_rows($rs)==1);
	}

	public static function getMaxExecutionOrder() {
		global $adb;
		$rs = $adb->pquery('select coalesce(max(execorder),0) from vtiger_cbupdater', array());
		return $adb->query_result($rs, 0, 0);
	}

	public function save_module($module) {
		if ($this->HasDirectImageField) {
			$this->insertIntoAttachment($this->id, $module);
		}
	}

	public function afterImportRecord($rowId, $entityInfo) {
		global $adb;
		if (!empty($entityInfo['id'])) {
			list($wsid, $crmid) = explode('x', $entityInfo['id']);
			$adb->pquery('update vtiger_cbupdater set filename=?,appcs=? where cbupdaterid=?', array(uniqid(), '0', $crmid));
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
			$this->setModuleSeqNumber('configure', $modulename, 'cbupd-', '0000001');
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
}
?>
