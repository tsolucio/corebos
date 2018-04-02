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
require 'user_privileges/default_module_view.php';
require_once 'modules/InventoryDetails/InventoryDetails.php';

class PurchaseOrder extends CRMEntity {
	public $db;
	public $log;

	public $table_name = 'vtiger_purchaseorder';
	public $table_index= 'purchaseorderid';
	public $column_fields = array();

	/** Indicator if this is a custom module or standard module */
	public $IsCustomModule = false;
	public $HasDirectImageField = false;
	public $tab_name = array('vtiger_crmentity','vtiger_purchaseorder','vtiger_pobillads','vtiger_poshipads','vtiger_purchaseordercf');
	public $tab_name_index = array(
		'vtiger_crmentity' => 'crmid',
		'vtiger_purchaseorder' => 'purchaseorderid',
		'vtiger_pobillads' => 'pobilladdressid',
		'vtiger_poshipads' => 'poshipaddressid',
		'vtiger_purchaseordercf'=>'purchaseorderid'
	);

	/**
	 * Mandatory table for supporting custom fields.
	 */
	public $customFieldTable = array('vtiger_purchaseordercf', 'purchaseorderid');

	public $sortby_fields = array('subject','tracking_no','smownerid','lastname');

	/**
	 * Mandatory for Listing (Related listview)
	 */
	public $list_fields = array(
		'Order No'=>array('purchaseorder'=>'purchaseorder_no'),
		'Subject'=>array('purchaseorder'=>'subject'),
		'Vendor Name'=>array('purchaseorder'=>'vendorid'),
		'Tracking Number'=>array('purchaseorder'=> 'tracking_no'),
		'Total'=>array('purchaseorder'=>'total'),
		'Assigned To'=>array('crmentity'=>'smownerid')
	);
	public $list_fields_name = array(
		'Order No'=>'purchaseorder_no',
		'Subject'=>'subject',
		'Vendor Name'=>'vendor_id',
		'Tracking Number'=>'tracking_no',
		'Total'=>'hdnGrandTotal',
		'Assigned To'=>'assigned_user_id'
	);

	// Make the field link to detail view from list view (Fieldname)
	public $list_link_field = 'subject';

	// For Popup listview and UI type support
	public $search_fields = array(
		'Order No'=>array('purchaseorder'=>'purchaseorder_no'),
		'Subject'=>array('purchaseorder'=>'subject'),
	);
	public $search_fields_name = array(
		'Order No'=>'purchaseorder_no',
		'Subject'=>'subject',
	);

	// For Popup window record selection
	public $popup_fields = array('subject');

	// Used when enabling/disabling the mandatory fields for the module.
	// Refers to vtiger_field.fieldname values.
	public $mandatory_fields = array('subject', 'vendor_id','createdtime' ,'modifiedtime');

	// This is the list of vtiger_fields that are required.
	public $required_fields = array("accountname"=>1);

	//Added these variables which are used as default order by and sortorder in ListView
	public $default_order_by = 'subject';
	public $default_sort_order = 'ASC';

	// For Alphabetical search
	public $def_basicsearch_col = 'subject';

	// Column value to use on detail view record text display
	public $def_detailview_recname = 'subject';
	public $record_status = '';

	public function save($module, $fileid = '') {
		if ($this->mode=='edit') {
			$this->record_status = getSingleFieldValue($this->table_name, 'postatus', $this->table_index, $this->id);
		}
		parent::save($module, $fileid);
	}

	public function save_module($module) {
		if ($this->HasDirectImageField) {
			$this->insertIntoAttachment($this->id, $module);
		}
		if ($this->mode=='edit' && !empty($this->record_status) && $this->record_status != $this->column_fields['postatus'] && $this->column_fields['postatus'] != '') {
			$this->registerInventoryHistory();
		}
		//in ajax save we should not call this function, because this will delete all the existing product values
		if (inventoryCanSaveProductLines($_REQUEST, 'PurchaseOrder')) {
			//Based on the total Number of rows we will save the product relationship with this entity
			saveInventoryProductDetails($this, 'PurchaseOrder');
			if (vtlib_isModuleActive("InventoryDetails")) {
				InventoryDetails::createInventoryDetails($this, 'PurchaseOrder');
			}
		}
	}

	public function registerInventoryHistory() {
		global $app_strings;
		if (isset($_REQUEST['ajxaction']) && $_REQUEST['ajxaction'] == 'DETAILVIEW') { //if we use ajax edit
			$relatedname = getVendorName($this->column_fields['vendor_id']);
			$total = $this->column_fields['hdnGrandTotal'];
		} else { //using edit button and save
			$relatedname = $_REQUEST["vendor_name"];
			$total = $_REQUEST['total'];
		}
		if ($this->column_fields['postatus'] == $app_strings['LBL_NOT_ACCESSIBLE']) {
			//If the value in the request is Not Accessible for a picklist, the existing value will be replaced instead of Not Accessible value.
			$stat_value = getSingleFieldValue($this->table_name, 'postatus', $this->table_index, $this->id);
		} else {
			$stat_value = $this->column_fields['postatus'];
		}
		addInventoryHistory(get_class($this), $this->id, $relatedname, $total, $stat_value);
	}

	/**
	 * Customizing the restore procedure.
	 */
	public function restore($module, $id) {
		global $adb;
		parent::restore($module, $id);
		$result = $adb->pquery('SELECT postatus FROM vtiger_purchaseorder where purchaseorderid=?', array($id));
		$poStatus = $adb->query_result($result, 0, 'postatus');
		if ($poStatus == 'Received Shipment') {
			addProductsToStock($id);
		}
	}

	/**
	 * Customizing the Delete procedure.
	 */
	public function trash($module, $recordId) {
		global $adb;
		$result = $adb->pquery("SELECT postatus FROM vtiger_purchaseorder where purchaseorderid=?", array($recordId));
		$poStatus = $adb->query_result($result, 0, 'postatus');
		if ($poStatus == 'Received Shipment') {
			deductProductsFromStock($recordId);
		}
		parent::trash($module, $recordId);
	}

	/**	Function used to get the Status history of the Purchase Order
	 *	@param $id - purchaseorder id
	 *	@return $return_data - array with header and the entries in format Array('header'=>$header,'entries'=>$entries_list)
	 *	 where as $header and $entries_list are arrays which contains header values and all column values of all entries
	 */
	public function get_postatushistory($id) {
		global $log, $adb, $app_strings, $current_user;
		$log->debug("Entering get_postatushistory(".$id.") method ...");

		$query = 'select vtiger_postatushistory.*, vtiger_purchaseorder.purchaseorder_no
			from vtiger_postatushistory
			inner join vtiger_purchaseorder on vtiger_purchaseorder.purchaseorderid = vtiger_postatushistory.purchaseorderid
			inner join vtiger_crmentity on vtiger_crmentity.crmid = vtiger_purchaseorder.purchaseorderid
			where vtiger_crmentity.deleted = 0 and vtiger_purchaseorder.purchaseorderid = ?';
		$result=$adb->pquery($query, array($id));
		$header = array();
		$header[] = $app_strings['Order No'];
		$header[] = $app_strings['Vendor Name'];
		$header[] = $app_strings['LBL_AMOUNT'];
		$header[] = $app_strings['LBL_PO_STATUS'];
		$header[] = $app_strings['LBL_LAST_MODIFIED'];

		//Getting the field permission for the current user. 1 - Not Accessible, 0 - Accessible
		//Vendor, Total are mandatory fields. So no need to do security check to these fields.

		//If field is accessible then getFieldVisibilityPermission function will return 0 else return 1
		$postatus_access = (getFieldVisibilityPermission('PurchaseOrder', $current_user->id, 'postatus') != '0')? 1 : 0;
		$picklistarray = getAccessPickListValues('PurchaseOrder');

		$postatus_array = ($postatus_access != 1)? $picklistarray['postatus']: array();
		//- ==> picklist field is not permitted in profile
		//Not Accessible - picklist is permitted in profile but picklist value is not permitted
		$error_msg = ($postatus_access != 1)? getTranslatedString('LBL_NOT_ACCESSIBLE'): '-';
		$entries_list = array();
		while ($row = $adb->fetch_array($result)) {
			$entries = array();

			$entries[] = $row['purchaseorder_no'];
			$entries[] = $row['vendorname'];
			$total = new CurrencyField($row['total']);
			$entries[] = $total->getDisplayValueWithSymbol($current_user);
			$entries[] = (in_array($row['postatus'], $postatus_array))? getTranslatedString($row['postatus'], 'PurchaseOrder'): $error_msg;
			$date = new DateTimeField($row['lastmodified']);
			$entries[] = $date->getDisplayDateTimeValue();

			$entries_list[] = $entries;
		}

		$return_data = array('header'=>$header, 'entries'=>$entries_list, 'navigation'=>array('',''));
		$log->debug('Exiting get_postatushistory method ...');
		return $return_data;
	}

	/*
	 * Function to get the secondary query part of a report
	 * @param - $module primary module name
	 * @param - $secmodule secondary module name
	 * returns the query string formed on fetching the related data for report for secondary module
	 */
	public function generateReportsSecQuery($module, $secmodule, $queryPlanner, $type = '', $where_condition = '') {
		$matrix = $queryPlanner->newDependencyMatrix();
		$matrix->setDependency('vtiger_crmentityPurchaseOrder', array('vtiger_usersPurchaseOrder', 'vtiger_groupsPurchaseOrder', 'vtiger_lastModifiedByPurchaseOrder'));
		$matrix->setDependency('vtiger_inventoryproductrelPurchaseOrder', array('vtiger_productsPurchaseOrder', 'vtiger_servicePurchaseOrder'));

		if (!$queryPlanner->requireTable('vtiger_purchaseorder', $matrix) && !$queryPlanner->requireTable('vtiger_purchaseordercf', $matrix)) {
			return '';
		}
		$matrix->setDependency('vtiger_purchaseorder', array('vtiger_crmentityPurchaseOrder', "vtiger_currency_info$secmodule",
				'vtiger_purchaseordercf', 'vtiger_vendorRelPurchaseOrder', 'vtiger_pobillads',
				'vtiger_poshipads', 'vtiger_inventoryproductrelPurchaseOrder', 'vtiger_contactdetailsPurchaseOrder'));
		$query = parent::generateReportsSecQuery($module, $secmodule, $queryPlanner, $type, $where_condition);
		if ($queryPlanner->requireTable("vtiger_pobillads")) {
			$query .= " left join vtiger_pobillads on vtiger_purchaseorder.purchaseorderid=vtiger_pobillads.pobilladdressid";
		}
		if ($queryPlanner->requireTable("vtiger_poshipads")) {
			$query .= " left join vtiger_poshipads on vtiger_purchaseorder.purchaseorderid=vtiger_poshipads.poshipaddressid";
		}
		if ($queryPlanner->requireTable("vtiger_currency_info$secmodule")) {
			$query .= " left join vtiger_currency_info as vtiger_currency_info$secmodule on vtiger_currency_info$secmodule.id = vtiger_purchaseorder.currency_id";
		}
		if (($type !== 'COLUMNSTOTOTAL') || ($type == 'COLUMNSTOTOTAL' && $where_condition == 'add')) {
			if ($queryPlanner->requireTable('vtiger_inventoryproductrelPurchaseOrder', $matrix)) {
				if ($module == 'Products') {
					$query .= ' left join vtiger_inventoryproductrel as vtiger_inventoryproductrelPurchaseOrder on'
						.' vtiger_purchaseorder.purchaseorderid = vtiger_inventoryproductrelPurchaseOrder.id'
						.' and vtiger_inventoryproductrelPurchaseOrder.productid=vtiger_products.productid ';
				} elseif ($module == 'Services') {
					$query .= ' left join vtiger_inventoryproductrel as vtiger_inventoryproductrelPurchaseOrder on'
						.' vtiger_purchaseorder.purchaseorderid = vtiger_inventoryproductrelPurchaseOrder.id'
						.' and vtiger_inventoryproductrelPurchaseOrder.productid=vtiger_service.serviceid ';
				} else {
					$query .= ' left join vtiger_inventoryproductrel as vtiger_inventoryproductrelPurchaseOrder on'
						.' vtiger_purchaseorder.purchaseorderid = vtiger_inventoryproductrelPurchaseOrder.id ';
				}
			}
			if ($queryPlanner->requireTable('vtiger_productsPurchaseOrder')) {
				$query .= ' left join vtiger_products as vtiger_productsPurchaseOrder on'
					.' vtiger_productsPurchaseOrder.productid = vtiger_inventoryproductrelPurchaseOrder.productid';
			}
			if ($queryPlanner->requireTable('vtiger_servicePurchaseOrder')) {
				$query .= ' left join vtiger_service as vtiger_servicePurchaseOrder on'
					.' vtiger_servicePurchaseOrder.serviceid = vtiger_inventoryproductrelPurchaseOrder.productid';
			}
		}
		if ($queryPlanner->requireTable('vtiger_vendorRelPurchaseOrder')) {
			$query .= ' left join vtiger_vendor as vtiger_vendorRelPurchaseOrder on vtiger_vendorRelPurchaseOrder.vendorid = vtiger_purchaseorder.vendorid';
		}
		if ($queryPlanner->requireTable('vtiger_contactdetailsPurchaseOrder')) {
			$query .= ' left join vtiger_contactdetails as vtiger_contactdetailsPurchaseOrder on'
				.' vtiger_contactdetailsPurchaseOrder.contactid = vtiger_purchaseorder.contactid';
		}
		return $query;
	}

	/*
	 * Function to get the relation tables for related modules
	 * @param - $secmodule secondary module name
	 * returns the array with table names and fieldnames storing relations between module and this module
	 */
	public function setRelationTables($secmodule) {
		$rel_tables = array (
			'Calendar' =>array('vtiger_seactivityrel'=>array('crmid','activityid'),'vtiger_purchaseorder'=>'purchaseorderid'),
			'Documents' => array('vtiger_senotesrel'=>array('crmid','notesid'),'vtiger_purchaseorder'=>'purchaseorderid'),
			'Contacts' => array('vtiger_purchaseorder'=>array('purchaseorderid','contactid')),
		);
		return isset($rel_tables[$secmodule]) ? $rel_tables[$secmodule] : '';
	}

	// Function to unlink an entity with given Id from another entity
	public function unlinkRelationship($id, $return_module, $return_id) {
		if (empty($return_module) || empty($return_id)) {
			return;
		}

		if ($return_module == 'Vendors') {
			$sql_req ='UPDATE vtiger_crmentity SET deleted = 1 WHERE crmid= ?';
			$this->db->pquery($sql_req, array($id));
		} elseif ($return_module == 'Contacts') {
			$sql_req ='UPDATE vtiger_purchaseorder SET contactid=? WHERE purchaseorderid = ?';
			$this->db->pquery($sql_req, array(null, $id));
		} elseif ($return_module == 'Documents') {
			$sql = 'DELETE FROM vtiger_senotesrel WHERE crmid=? AND notesid=?';
			$this->db->pquery($sql, array($id, $return_id));
		} else {
			parent::unlinkRelationship($id, $return_module, $return_id);
		}
	}

	/*Function to create records in current module.
	**This function called while importing records to this module*/
	public function createRecords($obj) {
		return createRecords($obj);
	}

	/*Function returns the record information which means whether the record is imported or not
	**This function called while importing records to this module*/
	public function importRecord($obj, $inventoryFieldData, $lineItemDetails) {
		return importRecord($obj, $inventoryFieldData, $lineItemDetails);
	}

	/*Function to return the status count of imported records in current module.
	**This function called while importing records to this module*/
	public function getImportStatusCount($obj) {
		return getImportStatusCount($obj);
	}

	public function undoLastImport($obj, $user) {
		undoLastImport($obj, $user);
	}

	/** Function to export the lead records in CSV Format
	* @param reference variable - where condition is passed when the query is executed
	* Returns Export PurchaseOrder Query.
	*/
	public function create_export_query($where) {
		global $log, $current_user;
		$log->debug("Entering create_export_query($where) method ...");

		include 'include/utils/ExportUtils.php';

		//To get the Permitted fields query and the permitted fields list
		$sql = getPermittedFieldsQuery('PurchaseOrder', 'detail_view');
		$fields_list = getFieldsListFromQuery($sql);
		$fields_list .= getInventoryFieldsForExport($this->table_name);
		$userNameSql = getSqlForNameInDisplayFormat(array('first_name'=>'vtiger_users.first_name', 'last_name' => 'vtiger_users.last_name'), 'Users');

		$query = "SELECT $fields_list FROM vtiger_crmentity
			INNER JOIN vtiger_purchaseorder ON vtiger_purchaseorder.purchaseorderid = vtiger_crmentity.crmid
			LEFT JOIN vtiger_purchaseordercf ON vtiger_purchaseordercf.purchaseorderid = vtiger_purchaseorder.purchaseorderid
			LEFT JOIN vtiger_pobillads ON vtiger_pobillads.pobilladdressid = vtiger_purchaseorder.purchaseorderid
			LEFT JOIN vtiger_poshipads ON vtiger_poshipads.poshipaddressid = vtiger_purchaseorder.purchaseorderid
			LEFT JOIN vtiger_inventoryproductrel ON vtiger_inventoryproductrel.id = vtiger_purchaseorder.purchaseorderid
			LEFT JOIN vtiger_products ON vtiger_products.productid = vtiger_inventoryproductrel.productid
			LEFT JOIN vtiger_service ON vtiger_service.serviceid = vtiger_inventoryproductrel.productid
			LEFT JOIN vtiger_contactdetails ON vtiger_contactdetails.contactid = vtiger_purchaseorder.contactid
			LEFT JOIN vtiger_vendor ON vtiger_vendor.vendorid = vtiger_purchaseorder.vendorid
			LEFT JOIN vtiger_currency_info ON vtiger_currency_info.id = vtiger_purchaseorder.currency_id
			LEFT JOIN vtiger_groups ON vtiger_groups.groupid = vtiger_crmentity.smownerid
			LEFT JOIN vtiger_users as vtigerCreatedBy ON vtiger_crmentity.smcreatorid = vtigerCreatedBy.id and vtigerCreatedBy.status='Active'
			LEFT JOIN vtiger_users ON vtiger_users.id = vtiger_crmentity.smownerid";

		$query .= $this->getNonAdminAccessControlQuery('PurchaseOrder', $current_user);
		$where_auto = ' vtiger_crmentity.deleted=0';

		if ($where != '') {
			$query .= " where ($where) AND ".$where_auto;
		} else {
			$query .= ' where '.$where_auto;
		}

		$log->debug('Exiting create_export_query method ...');
		return $query;
	}
}
?>
