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
require_once 'modules/com_vtiger_workflow/include.inc';
require_once 'modules/com_vtiger_workflow/tasks/VTEntityMethodTask.inc';
require_once 'modules/com_vtiger_workflow/VTEntityMethodManager.inc';
require_once 'modules/InventoryDetails/InventoryDetails.php';

class Receiptcards extends CRMEntity {
	public $table_name = 'vtiger_receiptcards';
	public $table_index= 'receiptcardid';
	public $column_fields = array();

	/** Indicator if this is a custom module or standard module */
	public $IsCustomModule = true;
	public $HasDirectImageField = false;
	public $moduleIcon = array('library' => 'utility', 'containerClass' => 'slds-icon_container slds-icon-standard-account', 'class' => 'slds-icon', 'icon'=>'send');
	public $tab_name = array('vtiger_crmentity','vtiger_receiptcards','vtiger_receiptcardscf');
	public $tab_name_index = array('vtiger_crmentity'=>'crmid','vtiger_receiptcards'=>'receiptcardid','vtiger_receiptcardscf'=>'receiptcardid');
	/**
	 * Mandatory table for supporting custom fields.
	 */
	public $customFieldTable = array('vtiger_receiptcardscf', 'receiptcardid');

	public $update_product_array = array();

	// This is the list of vtiger_fields that are in the lists.
	public $list_fields = array(
		/* Format: Field Label => array(tablename => columnname) */
		// tablename should not have prefix 'vtiger_'
		'Receiptcards No'=> array('receiptcard' => 'receiptcards_no'),
		'Vendor Name' => array('receiptcard' => 'vendor_id'),
		'Adoption Date' => array('receiptcard' => 'adoption_date'),
		'Delivery No' => array('receiptcard' => 'delivery_no'),
		'Assigned To' => array('crmentity' => 'smownerid')
	);
	public $list_fields_name = array(
		/* Format: Field Label => fieldname */
		'Receiptcards No'=> 'receiptcards_no',
		'Vendor Name' => 'vendor_id',
		'Adoption Date' => 'adoption_date',
		'Delivery No' => 'delivery_no',
		'Assigned To' => 'assigned_user_id'
	);

	// Make the field link to detail view from list view (Fieldname)
	public $list_link_field = 'receiptcards_no';

	// For Popup listview and UI type support
	public $search_fields = array(
		/* Format: Field Label => array(tablename => columnname) */
		// tablename should not have prefix 'vtiger_'
		'Receiptcards No'=> array('receiptcards' => 'receiptcards_no'),
		'Vendor Name' => array('receiptcard' => 'vendor_id'),
		'Adoption Date' => array('receiptcard' => 'adoption_date'),
		'Delivery No' => array('receiptcard' => 'delivery_no'),
	);
	public $search_fields_name = array(
		/* Format: Field Label => fieldname */
		'Receiptcards No'=> 'receiptcards_no',
		'Vendor Name' => 'vendor_id',
		'Adoption Date' => 'adoption_date',
		'Delivery No' => 'delivery_no',
	);

	// For Popup window record selection
	public $popup_fields = array('receiptcards_no', 'startdate');

	// Placeholder for sort fields - All the fields will be initialized for Sorting through initSortFields
	public $sortby_fields = array();

	// For Alphabetical search
	public $def_basicsearch_col = 'receiptcards_no';

	// Column value to use on detail view record text display
	public $def_detailview_recname = 'receiptcards_no';

	// Required Information for enabling Import feature
	public $required_fields = array('receiptcards_no'=>1);

	// Callback function list during Importing
	public $special_functions = array('set_import_assigned_user');

	public $default_order_by = 'createdtime';
	public $default_sort_order='ASC';
	// Used when enabling/disabling the mandatory fields for the module.
	// Refers to vtiger_field.fieldname values.
	public $mandatory_fields = array('createdtime', 'modifiedtime', 'receiptcards_no');

	public function save_module($module) {
		global $updateInventoryProductRel_deduct_stock, $adb;
		if ($this->HasDirectImageField) {
			$this->insertIntoAttachment($this->id, $module);
		}
		$updateInventoryProductRel_deduct_stock = true;

		//in ajax save we should not call this function, because this will delete all the existing product values
		if (inventoryCanSaveProductLines($_REQUEST, 'Receiptcards')) {
			//Based on the total Number of rows we will save the product relationship with this entity
			saveInventoryProductDetails($this, 'Receiptcards');
			if (vtlib_isModuleActive('InventoryDetails')) {
				InventoryDetails::createInventoryDetails($this, 'Receiptcards');
			}
		} elseif ($_REQUEST['action'] == 'ReceiptcardsAjax' || $_REQUEST['action'] == 'MassEditSave') {
			$updateInventoryProductRel_deduct_stock = false;
		}

		// Update the currency id and the conversion rate for the invoice
		$update_query = "update vtiger_receiptcards set currency_id=?, conversion_rate=? where receiptcardid=?";
		$update_params = array($this->column_fields['currency_id'], $this->column_fields['conversion_rate'], $this->id);
		$adb->pquery($update_query, $update_params);
	}

	public function restore($module, $id) {
		global $adb;
		$adb->println("TRANS restore starts $module");
		$adb->startTransaction();

		$adb->pquery('UPDATE vtiger_crmentity SET deleted=0 WHERE crmid = ?', array($id));
		//Restore related entities/records
		$this->restoreRelatedRecords($module, $id);

		$product_info = $adb->pquery('SELECT productid, quantity, sequence_no, incrementondel from vtiger_inventoryproductrel WHERE id=?', array($id));
		$numrows = $adb->num_rows($product_info);
		for ($index = 0; $index < $numrows; $index++) {
			$productid = $adb->query_result($product_info, $index, 'productid');
			$qty = $adb->query_result($product_info, $index, 'quantity');
			addToProductStock($productid, $qty);
		}
		$adb->completeTransaction();
		$adb->println("TRANS restore ends");
	}

	/**
	 * Invoked when special actions are performed on the module.
	 * @param String Module name
	 * @param String Event Type (module.postinstall, module.disabled, module.enabled, module.preuninstall)
	 */
	public function vtlib_handler($modulename, $event_type) {
		global $adb;
		require_once 'include/events/include.inc';
		include_once 'vtlib/Vtiger/Module.php';
		if ($event_type == 'module.postinstall') {
			// Handle post installation actions
			$modAccounts=Vtiger_Module::getInstance('Accounts');
			$modContacts=Vtiger_Module::getInstance('Contacts');
			$modInvD=Vtiger_Module::getInstance('InventoryDetails');
			$modVnd=Vtiger_Module::getInstance('Vendors');
			$modRC=Vtiger_Module::getInstance('Receiptcards');
			$modRC->addLink('HEADERSCRIPT', 'InventoryJS', 'include/js/Inventory.js', '', 1, null, true);
			if ($modAccounts) {
				$modAccounts->setRelatedList($modRC, 'Receiptcards', array('ADD'), 'get_dependents_list');
			}
			if ($modContacts) {
				$modContacts->setRelatedList($modRC, 'Receiptcards', array('ADD'), 'get_dependents_list');
			}
			if ($modInvD) {
				$field = Vtiger_Field::getInstance('related_to', $modInvD);
				$field->setRelatedModules(array('Receiptcards'));
				$modRC->setRelatedList($modInvD, 'InventoryDetails', array(''), 'get_dependents_list');
			}
			if ($modVnd) {
				$modVnd->setRelatedList($modRC, 'Receiptcards', array(''), 'get_dependents_list');
			}

			$emm = new VTEntityMethodManager($adb);
			// Adding EntityMethod for Updating Products data after updating PurchaseOrder
			$emm->addEntityMethod('Receiptcards', 'UpdateInventory', 'include/InventoryHandler.php', 'handleInventoryProductRel');
			// Creating Workflow for Updating Inventory Stock on Receiptcards
			$vtWorkFlow = new VTWorkflowManager($adb);
			$invWorkFlow = $vtWorkFlow->newWorkFlow('Receiptcards');
			$invWorkFlow->test = '[{"fieldname":"receiptcards_no","operation":"does not contain","value":"`!`"}]';
			$invWorkFlow->description = "UpdateInventoryProducts On Every Save";
			$invWorkFlow->defaultworkflow = 1;
			$vtWorkFlow->save($invWorkFlow);

			$tm = new VTTaskManager($adb);
			$task = $tm->createTask('VTEntityMethodTask', $invWorkFlow->id);
			$task->active=true;
			$task->methodName = 'UpdateInventory';
			$task->summary='Update product stock';
			$tm->saveTask($task);
			// set correct capitalization of field names
			$flds = array(
				'adjustment' => 'txtAdjustment',
				'subtotal' => 'hdnSubTotal',
				'total' => 'hdnGrandTotal',
				'taxtype' => 'hdnTaxType',
				'discount_percent' => 'hdnDiscountPercent',
				'discount_amount' => 'hdnDiscountAmount',
				's_h_amount' => 'hdnS_H_Amount',
			);
			foreach ($flds as $column => $field) {
				$adb->pquery('update vtiger_field set fieldname=? where columnname=? and tablename=?', [$field, $column, 'vtiger_receiptcards']);
			}
			$this->setModuleSeqNumber('configure', $modulename, 'RECM-', '0000001');
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
			$modInvD=Vtiger_Module::getInstance('InventoryDetails');
			$modRC=Vtiger_Module::getInstance('Receiptcards');
			if ($modInvD) {
				$field = Vtiger_Field::getInstance('related_to', $modInvD);
				$field->setRelatedModules(array('Receiptcards'));
				$modRC->setRelatedList($modInvD, 'InventoryDetails', array(''), 'get_dependents_list');
			}
			$emm = new VTEntityMethodManager($adb);
			// Adding EntityMethod for Updating Products data after updating Receiptcards
			$emm->addEntityMethod('Receiptcards', 'UpdateInventory', 'include/InventoryHandler.php', 'handleInventoryProductRel');
			// Creating Workflow for Updating Inventory Stock on Receiptcards
			$vtWorkFlow = new VTWorkflowManager($adb);
			$invWorkFlow = $vtWorkFlow->newWorkFlow('Receiptcards');
			$invWorkFlow->test = '[{"fieldname":"receiptcards_no","operation":"does not contain","value":"`!`"}]';
			$invWorkFlow->description = 'UpdateInventoryProducts On Every Save';
			$invWorkFlow->defaultworkflow = 1;
			$vtWorkFlow->save($invWorkFlow);

			$tm = new VTTaskManager($adb);
			$task = $tm->createTask('VTEntityMethodTask', $invWorkFlow->id);
			$task->active=true;
			$task->methodName = 'UpdateInventory';
			$task->summary='Update product stock';
			$tm->saveTask($task);
		}
	}

	/*
	 * Function to get the secondary query part of a report
	 * @param - $module primary module name
	 * @param - $secmodule secondary module name
	 * returns the query string formed on fetching the related data for report for secondary module
	 */
	public function generateReportsSecQuery($module, $secmodule, $queryPlanner, $type = '', $where_condition = '') {
		$query = $this->getRelationQuery($module, $secmodule, 'vtiger_receiptcards', 'receiptcardid');
		$query .= " left join vtiger_currency_info as vtiger_currency_info$secmodule on vtiger_currency_info$secmodule.id = vtiger_receiptcards.currency_id ";
		if (($type !== 'COLUMNSTOTOTAL') || ($type == 'COLUMNSTOTOTAL' && $where_condition == 'add')) {
			$query.='left join vtiger_inventoryproductrel as vtiger_inventoryproductrelReceiptcards on vtiger_receiptcards.receiptcardid=vtiger_inventoryproductrelReceiptcards.id
				left join vtiger_products as vtiger_productsReceiptcards on vtiger_productsReceiptcards.productid = vtiger_inventoryproductrelReceiptcards.productid
				left join vtiger_service as vtiger_serviceReceiptcards on vtiger_serviceReceiptcards.serviceid = vtiger_inventoryproductrelReceiptcards.productid ';
		}
		return $query;
	}
}
?>
