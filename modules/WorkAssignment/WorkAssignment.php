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

class WorkAssignment extends CRMEntity {
	public $db;

	public $table_name = 'vtiger_workassignment';
	public $table_index= 'workassignmentid';
	public $column_fields = array();

	/** Indicator if this is a custom module or standard module */
	public $IsCustomModule = true;
	public $HasDirectImageField = false;
	public $moduleIcon = array('library' => 'standard', 'containerClass' => 'slds-icon_container slds-icon-standard-work-order', 'class' => 'slds-icon', 'icon'=>'work_order');
	/**
	 * Mandatory table for supporting custom fields.
	 */
	public $customFieldTable = array('vtiger_workassignmentcf', 'workassignmentid');
	// related_tables variable should define the association (relation) between dependent tables
	// FORMAT: related_tablename => array(related_tablename_column[, base_tablename, base_tablename_column[, related_module]] )
	// Here base_tablename_column should establish relation with related_tablename_column
	// NOTE: If base_tablename and base_tablename_column are not specified, it will default to modules (table_name, related_tablename_column)
	// Uncomment the line below to support custom field columns on related lists
	// public $related_tables = array('vtiger_workassignmentcf' => array('workassignmentid', 'vtiger_workassignment', 'workassignmentid', 'workassignment'));

	/**
	 * Mandatory for Saving, Include tables related to this module.
	 */
	public $tab_name = array('vtiger_crmentity', 'vtiger_workassignment', 'vtiger_workassignmentcf');

	/**
	 * Mandatory for Saving, Include tablename and tablekey columnname here.
	 */
	public $tab_name_index = array(
		'vtiger_crmentity' => 'crmid',
		'vtiger_workassignment' => 'workassignmentid',
		'vtiger_workassignmentcf' => 'workassignmentid',
	);

	/**
	 * Mandatory for Listing (Related listview)
	 */
	public $list_fields = array(
		/* Format: Field Label => array(tablename => columnname) */
		// tablename should not have prefix 'vtiger_'
		'workassignment_no'=> array('workassignment' => 'workassignment_no'),
		'workassignmentname'=> array('workassignment' => 'workassignmentname'),
		'Assigned To' => array('crmentity' => 'smownerid')
	);
	public $list_fields_name = array(
		/* Format: Field Label => fieldname */
		'workassignment_no'=> 'workassignment_no',
		'workassignmentname'=> 'workassignmentname',
		'Assigned To' => 'assigned_user_id'
	);

	// Make the field link to detail view from list view (Fieldname)
	public $list_link_field = 'workassignment_no';

	// For Popup listview and UI type support
	public $search_fields = array(
		/* Format: Field Label => array(tablename => columnname) */
		// tablename should not have prefix 'vtiger_'
		'workassignment_no'=> array('workassignment' => 'workassignment_no'),
		'workassignmentname'=> array('workassignment' => 'workassignmentname'),
	);
	public $search_fields_name = array(
		/* Format: Field Label => fieldname */
		'workassignment_no'=> 'workassignment_no',
		'workassignmentname'=> 'workassignmentname',
	);

	// For Popup window record selection
	public $popup_fields = array('workassignment_no','workassignmentname');

	// Placeholder for sort fields - All the fields will be initialized for Sorting through initSortFields
	public $sortby_fields = array();

	// For Alphabetical search
	public $def_basicsearch_col = 'workassignment_no';

	// Column value to use on detail view record text display
	public $def_detailview_recname = 'workassignmentname';

	// Required Information for enabling Import feature
	public $required_fields = array('workassignmentname'=>1);

	// Callback function list during Importing
	public $special_functions = array('set_import_assigned_user');

	public $default_order_by = 'workassignment_no';
	public $default_sort_order='ASC';
	// Used when enabling/disabling the mandatory fields for the module.
	// Refers to vtiger_field.fieldname values.
	public $mandatory_fields = array('createdtime', 'modifiedtime', 'workassignmentname');

	public function save_module($module) {
		if ($this->HasDirectImageField) {
			$this->insertIntoAttachment($this->id, $module);
		}
	}

	/**
	 * Invoked when special actions are performed on the module.
	 * @param String Module name
	 * @param String Event Type (module.postinstall, module.disabled, module.enabled, module.preuninstall)
	 */
	public function vtlib_handler($modulename, $event_type) {
		if ($event_type == 'module.postinstall') {
			// Handle post installation actions
			$this->setModuleSeqNumber('configure', $modulename, 'WA', '0000001');
			$this->installTaxFields();
			$this->installTaxHandlers();
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

	private function installTaxFields() {
		$blocks = array(
			array(
				'name' => 'LBL_BLOCK_TAXES',
				'table' => 'vtiger_inventorytaxinfo',
				'labelprefix' => '',
			),
			array(
				'name' => 'LBL_BLOCK_SH_TAXES',
				'table' => 'vtiger_shippingtaxinfo',
				'labelprefix' => 'SH ',
			),
		);
		foreach ($blocks as $block) {
			$this->createTaxFields($block['name'], $block['table'], $block['labelprefix']);
		}
	}

	private function createTaxFields($blocklabel, $tablename, $labelprefix = '') {
		global $adb;
		require_once 'vtlib/Vtiger/Module.php';

		$module = VTiger_Module::getInstance(get_class($this));
		$block = Vtiger_Block::getInstance($blocklabel, $module);

		$rstax=$adb->query('SELECT `taxname`, `taxlabel`, `percentage` FROM ' . $tablename . ' WHERE deleted = 0');
		while ($tx = $adb->fetch_array($rstax)) {
			$field = new Vtiger_Field();
			$field->name = 'sum_' . $tx['taxname'];
			$field->label= $labelprefix . $tx['taxlabel'];
			$field->column = 'sum_' . $tx['taxname'];
			$field->columntype = 'DECIMAL(25,6)';
			$field->uitype = 7;
			$field->typeofdata = 'NN~O';
			$field->displaytype = 2;
			$field->presence = 0;
			$block->addField($field);

			$field = new Vtiger_Field();
			$field->name = $tx['taxname'] . '_perc';
			$field->label= $labelprefix . $tx['taxlabel'] . ' (%)';
			$field->column = $tx['taxname'] . '_perc';
			$field->columntype = 'DECIMAL(7,3)';
			$field->uitype = 7;
			$field->typeofdata = 'N~O';
			$field->displaytype = 2;
			$field->presence = 0;
			$block->addField($field);
		}
	}

	private function installTaxHandlers() {
		global $adb;
		require 'include/events/include.inc';
		$em = new VTEventsManager($adb);

		$em->registerHandler('corebos.add.tax', 'modules/WorkAssignment/handlers/handleNewTax.php', 'NewTaxHandler');
		$em->registerHandler('corebos.changestatus.tax', 'modules/WorkAssignment/handlers/handleChangedTax.php', 'ChangeTaxHandler');
		$em->registerHandler('corebos.changelabel.tax', 'modules/WorkAssignment/handlers/handleChangedTax.php', 'ChangeTaxHandler');
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
