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

class cbProcessInfo extends CRMEntity {
	public $table_name = 'vtiger_cbprocessinfo';
	public $table_index= 'cbprocessinfoid';
	public $column_fields = array();

	/** Indicator if this is a custom module or standard module */
	public $IsCustomModule = true;
	public $HasDirectImageField = false;
	public $moduleIcon = array('library' => 'utility', 'containerClass' => 'slds-icon_container slds-icon-standard-account', 'class' => 'slds-icon', 'icon'=>'edit_form');

	/**
	 * Mandatory table for supporting custom fields.
	 */
	public $customFieldTable = array('vtiger_cbprocessinfocf', 'cbprocessinfoid');
	// related_tables variable should define the association (relation) between dependent tables
	// FORMAT: related_tablename => array(related_tablename_column[, base_tablename, base_tablename_column[, related_module]] )
	// Here base_tablename_column should establish relation with related_tablename_column
	// NOTE: If base_tablename and base_tablename_column are not specified, it will default to modules (table_name, related_tablename_column)
	// Uncomment the line below to support custom field columns on related lists
	// public $related_tables = array('vtiger_cbprocessinfocf' => array('cbprocessinfoid', 'vtiger_cbprocessinfo', 'cbprocessinfoid', 'cbprocessinfo'));

	/**
	 * Mandatory for Saving, Include tables related to this module.
	 */
	public $tab_name = array('vtiger_crmentity', 'vtiger_cbprocessinfo', 'vtiger_cbprocessinfocf');

	/**
	 * Mandatory for Saving, Include tablename and tablekey columnname here.
	 */
	public $tab_name_index = array(
		'vtiger_crmentity' => 'crmid',
		'vtiger_cbprocessinfo'   => 'cbprocessinfoid',
		'vtiger_cbprocessinfocf' => 'cbprocessinfoid',
	);

	/**
	 * Mandatory for Listing (Related listview)
	 */
	public $list_fields = array(
		/* Format: Field Label => array(tablename => columnname) */
		// tablename should not have prefix 'vtiger_'
		'infodesc'=> array('cbprocessinfo' => 'infodesc'),
		'active'=> array('cbprocessinfo' => 'active'),
		'semodule'=> array('cbprocessinfo' => 'semodule'),
		'fieldmap'=> array('cbprocessinfo' => 'fieldmap'),
		'valmap'=> array('cbprocessinfo' => 'valmap'),
		'depmap'=> array('cbprocessinfo' => 'depmap'),
		'Assigned To' => array('crmentity' => 'smownerid')
	);
	public $list_fields_name = array(
		/* Format: Field Label => fieldname */
		'infodesc'=> 'infodesc',
		'active'=> 'active',
		'semodule'=> 'semodule',
		'fieldmap'=> 'fieldmap',
		'valmap'=> 'valmap',
		'depmap'=> 'depmap',
		'Assigned To' => 'assigned_user_id'
	);

	// Make the field link to detail view from list view (Fieldname)
	public $list_link_field = 'infodesc';

	// For Popup listview and UI type support
	public $search_fields = array(
		/* Format: Field Label => array(tablename => columnname) */
		// tablename should not have prefix 'vtiger_'
		'infodesc'=> array('cbprocessinfo' => 'infodesc'),
		'active'=> array('cbprocessinfo' => 'active'),
		'semodule'=> array('cbprocessinfo' => 'semodule'),
		'fieldmap'=> array('cbprocessinfo' => 'fieldmap'),
		'valmap'=> array('cbprocessinfo' => 'valmap'),
		'depmap'=> array('cbprocessinfo' => 'depmap'),
	);
	public $search_fields_name = array(
		/* Format: Field Label => fieldname */
		'infodesc'=> 'infodesc',
		'active'=> 'active',
		'semodule'=> 'semodule',
		'fieldmap'=> 'fieldmap',
		'valmap'=> 'valmap',
		'depmap'=> 'depmap',
	);

	// For Popup window record selection
	public $popup_fields = array('infodesc');

	// Placeholder for sort fields - All the fields will be initialized for Sorting through initSortFields
	public $sortby_fields = array();

	// For Alphabetical search
	public $def_basicsearch_col = 'infodesc';

	// Column value to use on detail view record text display
	public $def_detailview_recname = 'infodesc';

	// Required Information for enabling Import feature
	public $required_fields = array('infodesc'=>1);

	// Callback function list during Importing
	public $special_functions = array('set_import_assigned_user');

	public $default_order_by = 'infodesc';
	public $default_sort_order='ASC';
	// Used when enabling/disabling the mandatory fields for the module.
	// Refers to vtiger_field.fieldname values.
	public $mandatory_fields = array('createdtime', 'modifiedtime', 'infodesc');

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
			$modInfo=Vtiger_Module::getInstance($modulename);
			$modPS=Vtiger_Module::getInstance('cbProcessStep');
			if ($modPS) {
				$blockInstance = Vtiger_Block::getInstance('LBL_CBPROCESSSTEP_INFORMATION', $modPS);
				$field = new Vtiger_Field();
				$field->name = 'moreinfomapid';
				$field->label= 'moreinfomapid';
				$field->table = $modPS->basetable;
				$field->column = 'moreinfomapid';
				$field->columntype = 'INT(11)';
				$field->uitype = 10;
				$field->displaytype = 1;
				$field->typeofdata = 'V~O';
				$field->presence = 0;
				$blockInstance->addField($field);
				$field->setRelatedModules(array($modulename, 'cbMap'));
				$modInfo->setRelatedList($modPS, $modulename, array('ADD'), 'get_dependents_list');
			}
			$this->setModuleSeqNumber('configure', $modulename, 'BPMInfo-', '00000001');
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
