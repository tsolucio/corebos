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

class cbSOMaster extends CRMEntity {
	public $table_name = 'vtiger_cbsomaster';
	public $table_index= 'cbsomasterid';
	public $column_fields = array();

	/** Indicator if this is a custom module or standard module */
	public $IsCustomModule = true;
	public $HasDirectImageField = false;
	public $moduleIcon = array('library' => 'standard', 'containerClass' => 'slds-icon_container slds-icon-standard-account', 'class' => 'slds-icon', 'icon'=>'utility-capacity_plan');

	/**
	 * Mandatory table for supporting custom fields.
	 */
	public $customFieldTable = array('vtiger_cbsomastercf', 'cbsomasterid');
	// related_tables variable should define the association (relation) between dependent tables
	// FORMAT: related_tablename => array(related_tablename_column[, base_tablename, base_tablename_column[, related_module]] )
	// Here base_tablename_column should establish relation with related_tablename_column
	// NOTE: If base_tablename and base_tablename_column are not specified, it will default to modules (table_name, related_tablename_column)
	// Uncomment the line below to support custom field columns on related lists
	// public $related_tables = array('vtiger_cbsomastercf' => array('cbsomasterid', 'vtiger_cbsomaster', 'cbsomasterid', 'cbsomaster'));

	/**
	 * Mandatory for Saving, Include tables related to this module.
	 */
	public $tab_name = array('vtiger_crmentity', 'vtiger_cbsomaster', 'vtiger_cbsomastercf');

	/**
	 * Mandatory for Saving, Include tablename and tablekey columnname here.
	 */
	public $tab_name_index = array(
		'vtiger_crmentity' => 'crmid',
		'vtiger_cbsomaster'   => 'cbsomasterid',
		'vtiger_cbsomastercf' => 'cbsomasterid',
	);

	/**
	 * Mandatory for Listing (Related listview)
	 */
	public $list_fields = array(
		/* Format: Field Label => array(tablename => columnname) */
		// tablename should not have prefix 'vtiger_'
		'Sales Order Master no'=> array('cbsomaster' => 'cbsomasterno'),
		'Soggetto'=> array('cbsomaster' => 'subject'),
		'Nr. Ordine di Vendita'=> array('cbsomaster' => 'salesorder_no'),
		'Ordine di acquisto'=> array('cbsomaster' => 'purchase_order'),
		'Nome Azienda'=> array('cbsomaster' => 'account_id'),
		
		
	);
	public $list_fields_name = array(
		/* Format: Field Label => fieldname */
		'Sales Order Master no'=> 'cbsomasterno',
		'Soggetto'=> 'subject',
		'Nr. Ordine di Vendita'=> 'salesorder_no',
		'Ordine di acquisto'=> 'purchase_order',
		'Nome Azienda'=> 'account_id',
		
		
	);

	// Make the field link to detail view from list view (Fieldname)
	public $list_link_field = 'cbsomasterno';

	// For Popup listview and UI type support
	public $search_fields = array(
		/* Format: Field Label => array(tablename => columnname) */
		// tablename should not have prefix 'vtiger_'
		'Sales Order Master no'=> array('cbsomaster' => 'cbsomasterno'),
		'Soggetto'=> array('cbsomaster' => 'subject'),
		'Nr. Ordine di Vendita'=> array('cbsomaster' => 'salesorder_no'),
		'Ordine di acquisto'=> array('cbsomaster' => 'purchase_order'),
		'Nome Azienda'=> array('cbsomaster' => 'account_id'),
		
	);
	public $search_fields_name = array(
		/* Format: Field Label => fieldname */
		'Sales Order Master no'=> 'cbsomasterno',
		'Soggetto'=> 'subject',
		'Nr. Ordine di Vendita'=> 'salesorder_no',
		'Ordine di acquisto'=> 'purchase_order',
		'Nome Azienda'=> 'account_id',
		
	);

	// For Popup window record selection
	public $popup_fields = array('cbsomasterno');

	// Placeholder for sort fields - All the fields will be initialized for Sorting through initSortFields
	public $sortby_fields = array();

	// For Alphabetical search
	public $def_basicsearch_col = 'cbsomasterno';

	// Column value to use on detail view record text display
	public $def_detailview_recname = 'cbsomasterno';

	// Required Information for enabling Import feature
	public $required_fields = array('cbsomasterno'=>1);

	// Callback function list during Importing
	public $special_functions = array('set_import_assigned_user');

	public $default_order_by = 'cbsomasterno';
	public $default_sort_order='ASC';
	// Used when enabling/disabling the mandatory fields for the module.
	// Refers to vtiger_field.fieldname values.
	public $mandatory_fields = array('createdtime', 'modifiedtime', 'cbsomasterno');

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
			$moduleInstance = Vtiger_Module::getInstance("cbSOMaster");
			$modAccounts = Vtiger_Module::getInstance("Accounts");
			if ($modAccounts) {
				$modAccounts->setRelatedList($moduleInstance, "cbSOMaster", array("ADD"), "get_dependents_list");
			}
			$modcbOffers = Vtiger_Module::getInstance("cbOffers");
			if ($modcbOffers) {
				$modcbOffers->setRelatedList($moduleInstance, "cbSOMaster", array("ADD"), "get_dependents_list");
			}
			$modContacts = Vtiger_Module::getInstance("Contacts");
			if ($modContacts) {
				$modContacts->setRelatedList($moduleInstance, "cbSOMaster", array("ADD"), "get_dependents_list");
			}
			$modProducts = Vtiger_Module::getInstance("Products");
			if ($modProducts) {
				$modProducts->setRelatedList($moduleInstance, "cbSOMaster", array("ADD"), "get_dependents_list");
			}
			$modcbSODetails = Vtiger_Module::getInstance("cbSODetails");
			if ($modcbSODetails) {
				$blockInstance = Vtiger_Block::getInstance("LBL_CBSODETAILS_INFORMATION", $modcbSODetails);
				$field = new Vtiger_Field();
				$field->name = "cbsomaster_relation";
				$field->label= "cbSOMaster";
				$field->column = "cbsomaster_relation";
				$field->columntype = "INT(20)";
				$field->uitype = 10;
				$field->displaytype = 1;
				$field->typeofdata = "V~O";
				$field->presence = 0;
				$blockInstance->addField($field);
				$field->setRelatedModules(array("cbSOMaster"));
			}
				
			$this->setModuleSeqNumber('configure', $modulename, $modulename.'-', '0000001');
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
