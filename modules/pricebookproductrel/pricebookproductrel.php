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

class pricebookproductrel extends CRMEntity {
	public $table_name = 'vtiger_pricebookproductrel';
	public $table_index= 'pricebookproductrelid';
	public $column_fields = array();

	/** Indicator if this is a custom module or standard module */
	public $IsCustomModule = true;
	public $HasDirectImageField = false;
	public $moduleIcon = array('library' => 'standard', 'containerClass' => 'slds-icon_container slds-icon-standard-account', 'class' => 'slds-icon', 'icon'=>'price_book_entries');

	/**
	 * Mandatory table for supporting custom fields.
	 */
	public $customFieldTable = array('vtiger_pricebookproductrelcf', 'pricebookproductrelid');
	// related_tables variable should define the association (relation) between dependent tables
	// FORMAT: related_tablename => array(related_tablename_column[, base_tablename, base_tablename_column[, related_module]] )
	// Here base_tablename_column should establish relation with related_tablename_column
	// NOTE: If base_tablename and base_tablename_column are not specified, it will default to modules (table_name, related_tablename_column)
	// Uncomment the line below to support custom field columns on related lists
	// public $related_tables = array('vtiger_pricebookproductrelcf' => array('pricebookproductrelid', 'vtiger_pricebookproductrel', 'pricebookproductrelid', 'pricebookproductrel'));

	/**
	 * Mandatory for Saving, Include tables related to this module.
	 */
	public $tab_name = array('vtiger_crmentity', 'vtiger_pricebookproductrel', 'vtiger_pricebookproductrelcf');

	/**
	 * Mandatory for Saving, Include tablename and tablekey columnname here.
	 */
	public $tab_name_index = array(
		'vtiger_crmentity' => 'crmid',
		'vtiger_pricebookproductrel'   => 'pricebookproductrelid',
		'vtiger_pricebookproductrelcf' => 'pricebookproductrelid',
	);

	/**
	 * Mandatory for Listing (Related listview)
	 */
	public $list_fields = array(
		/* Format: Field Label => array(tablename => columnname) */
		// tablename should not have prefix 'vtiger_'
		'pricebookproductrel_no'=> array('pricebookproductrel' => 'pricebookproductrel_no'),
		'pricebook'=> array('pricebookproductrel' => 'pricebookid'),
		'product'=> array('pricebookproductrel' => 'productid'),
		'listprice'=> array('pricebookproductrel' => 'listprice'),
		'usedcurrency'=> array('pricebookproductrel' => 'usedcurrency'),
		'Assigned To' => array('crmentity' => 'smownerid')
	);
	public $list_fields_name = array(
		/* Format: Field Label => fieldname */
		'pricebookproductrel_no'=> 'pricebookproductrel_no',
		'pricebook'=> 'pricebookid',
		'product'=> 'productid',
		'listprice'=> 'listprice',
		'usedcurrency'=> 'usedcurrency',
		'Assigned To' => 'assigned_user_id'
	);

	// Make the field link to detail view from list view (Fieldname)
	public $list_link_field = 'pricebookproductrel_no';

	// For Popup listview and UI type support
	public $search_fields = array(
		/* Format: Field Label => array(tablename => columnname) */
		// tablename should not have prefix 'vtiger_'
		'pricebookproductrel_no'=> array('pricebookproductrel' => 'pricebookproductrel_no'),
		'pricebook'=> array('pricebookproductrel' => 'pricebookid'),
		'product'=> array('pricebookproductrel' => 'productid'),
		'listprice'=> array('pricebookproductrel' => 'listprice'),
		'usedcurrency'=> array('pricebookproductrel' => 'usedcurrency'),
	);
	public $search_fields_name = array(
		/* Format: Field Label => fieldname */
		'pricebookproductrel_no'=> 'pricebookproductrel_no',
		'pricebook'=> 'pricebookid',
		'product'=> 'productid',
		'listprice'=> 'listprice',
		'usedcurrency'=> 'usedcurrency',
	);

	// For Popup window record selection
	public $popup_fields = array('pricebookproductrel_no');

	// Placeholder for sort fields - All the fields will be initialized for Sorting through initSortFields
	public $sortby_fields = array();

	// For Alphabetical search
	public $def_basicsearch_col = 'pricebookproductrel_no';

	// Column value to use on detail view record text display
	public $def_detailview_recname = 'pricebookproductrel_no';

	// Required Information for enabling Import feature
	public $required_fields = array('pricebookproductrel_no'=>1);

	// Callback function list during Importing
	public $special_functions = array('set_import_assigned_user');

	public $default_order_by = 'pricebookproductrel_no';
	public $default_sort_order='ASC';
	// Used when enabling/disabling the mandatory fields for the module.
	// Refers to vtiger_field.fieldname values.
	public $mandatory_fields = array('createdtime', 'modifiedtime', 'pricebookproductrel_no');

	public function save_module($module) {
		if ($this->HasDirectImageField) {
			$this->insertIntoAttachment($this->id, $module);
		}
		if (!empty($this->column_fields['pricebookid'])) {
			global $adb;
			$pbcrs = $adb->pquery('select currency_id from vtiger_pricebook where pricebookid=?', [$this->column_fields['pricebookid']]);
			if ($pbcrs && $adb->num_rows($pbcrs)) {
				$this->column_fields['usedcurrency'] = $adb->query_result($pbcrs, 0, 'currency_id');
				$adb->pquery(
					'update vtiger_pricebookproductrel set usedcurrency=? where pricebookproductrelid=?',
					[$this->column_fields['usedcurrency'], $this->id]
				);
			}
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
			global $adb;
			$pbpdorel = Vtiger_Module::getInstance($modulename);
			$pb = Vtiger_Module::getInstance('PriceBooks');
			$pdo = Vtiger_Module::getInstance('Products');
			$srv = Vtiger_Module::getInstance('Services');
			$sql = 'select relation_id from vtiger_relatedlists where tabid=? and related_tabid=? and name=?';
			$upd = "update vtiger_relatedlists set related_tabid=?, name='get_dependents_list', label='pricebookproductrel', actions='ADD', relationtype='1:N' where relation_id=?";
			$relrs = $adb->pquery($sql, [$pdo->id, $pb->id, 'get_product_pricebooks']);
			if ($relrs) {
				$adb->pquery($upd, [$pbpdorel->id, $adb->query_result($relrs, 0, 0)]);
			}
			$relrs = $adb->pquery($sql, [$pb->id, $pdo->id, 'get_pricebook_products']);
			if ($relrs) {
				$adb->pquery($upd, [$pbpdorel->id, $adb->query_result($relrs, 0, 0)]);
			}
			$relrs = $adb->pquery($sql, [$srv->id, $pb->id, 'get_service_pricebooks']);
			if ($relrs) {
				$adb->pquery($upd, [$pbpdorel->id, $adb->query_result($relrs, 0, 0)]);
			}
			$relrs = $adb->pquery($sql, [$pb->id, $srv->id, 'get_pricebook_services']);
			if ($relrs) {
				$adb->pquery('delete from vtiger_relatedlists where relation_id=?', [$adb->query_result($relrs, 0, 0)]);
			}
			// create CRM records
			$pbpdors = $adb->query('select * from vtiger_pricebookproductrel');
			while ($pbpdo = $adb->fetch_array($pbpdors)) {
				unset($crme);
				$crme = CRMEntity::getInstance($modulename);
				$crme->tab_name = ['vtiger_crmentity'];
				$crme->mode='';
				$crme->column_fields['pricebookproductrel_no'] = '';
				$crme->column_fields['pricebookid'] = $pbpdo['pricebookid'];
				$crme->column_fields['productid'] = $pbpdo['productid'];
				$crme->column_fields['listprice'] = $pbpdo['listprice'];
				$crme->column_fields['usedcurrency'] = $pbpdo['usedcurrency'];
				$crme->saveentity($modulename);
				$adb->pquery(
					'update vtiger_pricebookproductrel set pricebookproductrelid=? where pricebookid=? and productid=?',
					[$crme->id, $pbpdo['pricebookid'], $pbpdo['productid']]
				);
				$adb->pquery('insert into vtiger_pricebookproductrelcf values (?)', [$crme->id]);
			}
			$adb->pquery('ALTER TABLE `vtiger_pricebookproductrel` ADD PRIMARY KEY(`pricebookproductrelid`)', []);
			// autonumber
			$this->setModuleSeqNumber('configure', $modulename, 'PriceList-', '000000001');
			$crme = CRMEntity::getInstance($modulename);
			$crme->updateMissingSeqNumber($modulename);
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
