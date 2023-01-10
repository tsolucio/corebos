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

class Bank extends CRMEntity {
	public $table_name = 'vtiger_bank';
	public $table_index= 'bankid';
	public $column_fields = array();

	/** Indicator if this is a custom module or standard module */
	public $IsCustomModule = true;
	public $HasDirectImageField = false;
	public $moduleIcon = array('library' => 'custom', 'containerClass' => 'slds-icon_container slds-icon-standard-account', 'class' => 'slds-icon', 'icon'=>'custom16');

	/**
	 * Mandatory table for supporting custom fields.
	 */
	public $customFieldTable = array('vtiger_bankcf', 'bankid');
	// Uncomment the line below to support custom field columns on related lists
	// public $related_tables = array('vtiger_bankcf'=>array('bankid','vtiger_bank', 'bankid'));

	/**
	 * Mandatory for Saving, Include tables related to this module.
	 */
	public $tab_name = array('vtiger_crmentity', 'vtiger_bank', 'vtiger_bankcf');

	/**
	 * Mandatory for Saving, Include tablename and tablekey columnname here.
	 */
	public $tab_name_index = array(
		'vtiger_crmentity' => 'crmid',
		'vtiger_bank'      => 'bankid',
		'vtiger_bankcf'    => 'bankid',
	);

	/**
	 * Mandatory for Listing (Related listview)
	 */
	public $list_fields = array(
		/* Format: Field Label => array(tablename => columnname) */
		// tablename should not have prefix 'vtiger_'
		'bank_name'     => array('bank' => 'bank_name'),
		'manager_name'  => array('bank' => 'manager_name'),
		'officer_name'  => array('bank' => 'officer_name'),
		'main_phone'    => array('bank' => 'main_phone'),
		'main_email'    => array('bank' => 'main_email'),
		'business_hours'=> array('bank' => 'business_hours'),
		'bank_website'  => array('bank' => 'bank_website')
	);
	public $list_fields_name = array(
		/* Format: Field Label => fieldname */
		'bank_name'     => 'bank_name',
		'manager_name'  => 'manager_name',
		'officer_name'  => 'officer_name',
		'main_phone'    => 'main_phone',
		'main_email'    => 'main_email',
		'business_hours'=> 'business_hours',
		'bank_website'  => 'bank_website'
	);

	// Make the field link to detail view from list view (Fieldname)
	public $list_link_field = 'bank_name';

	// For Popup listview and UI type support
	public $search_fields = array(
		/* Format: Field Label => array(tablename => columnname) */
		// tablename should not have prefix 'vtiger_'
		'bank_name'     => array('bank' => 'bank_name'),
		'manager_name'  => array('bank' => 'manager_name'),
		'officer_name'  => array('bank' => 'officer_name'),
		'main_phone'    => array('bank' => 'main_phone'),
		'main_email'    => array('bank' => 'main_email'),
		'business_hours'=> array('bank' => 'business_hours'),
		'bank_website'  => array('bank' => 'bank_website')
	);
	public $search_fields_name = array(
		/* Format: Field Label => fieldname */
		'bank_name'     => 'bank_name',
		'manager_name'  => 'manager_name',
		'officer_name'  => 'officer_name',
		'main_phone'    => 'main_phone',
		'main_email'    => 'main_email',
		'business_hours'=> 'business_hours',
		'bank_website'  => 'bank_website'
	);

	// For Popup window record selection
	public $popup_fields = array('bank_name');

	// Placeholder for sort fields - All the fields will be initialized for Sorting through initSortFields
	public $sortby_fields = array();

	// For Alphabetical search
	public $def_basicsearch_col = 'bank_name';

	// Column value to use on detail view record text display
	public $def_detailview_recname = 'bank_name';

	// Required Information for enabling Import feature
	public $required_fields = array('bank_name'=>1);

	// Callback function list during Importing
	public $special_functions = array('set_import_assigned_user');

	public $default_order_by = 'bank_name';
	public $default_sort_order='ASC';
	// Used when enabling/disabling the mandatory fields for the module.
	// Refers to vtiger_field.fieldname values.
	public $mandatory_fields = array('createdtime', 'modifiedtime', 'bank_name');

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
			$this->setModuleSeqNumber('configure', $modulename, 'BANK-', '000001');
			$mod = Vtiger_Module::getInstance('ModComments');
			$field_mcom = VTiger_Field::getInstance('related_to', $mod);
			$field_mcom->setRelatedModules(array($modulename));
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
