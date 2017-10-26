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
require('user_privileges/default_module_view.php');
require_once('modules/InventoryDetails/InventoryDetails.php');

class PurchaseOrder extends CRMEntity {
	var $db, $log; // Used in class functions of CRMEntity

	var $table_name = 'vtiger_purchaseorder';
	var $table_index= 'purchaseorderid';
	var $column_fields = Array();

	/** Indicator if this is a custom module or standard module */
	var $IsCustomModule = false;
	var $HasDirectImageField = false;
	var $tab_name = Array('vtiger_crmentity','vtiger_purchaseorder','vtiger_pobillads','vtiger_poshipads','vtiger_purchaseordercf');
	var $tab_name_index = Array('vtiger_crmentity'=>'crmid','vtiger_purchaseorder'=>'purchaseorderid','vtiger_pobillads'=>'pobilladdressid','vtiger_poshipads'=>'poshipaddressid','vtiger_purchaseordercf'=>'purchaseorderid');

	/**
	 * Mandatory table for supporting custom fields.
	 */
	var $customFieldTable = Array('vtiger_purchaseordercf', 'purchaseorderid');

	var $sortby_fields = Array('subject','tracking_no','smownerid','lastname');

	// This is used to retrieve related vtiger_fields from form posts.
	var $additional_column_fields = Array('assigned_user_name', 'smownerid', 'opportunity_id', 'case_id', 'contact_id', 'task_id', 'note_id', 'meeting_id', 'call_id', 'email_id', 'parent_name', 'member_id' );

	// This is the list of vtiger_fields that are in the lists.
	var $list_fields = Array(
		'Order No'=>Array('purchaseorder'=>'purchaseorder_no'),
		'Subject'=>Array('purchaseorder'=>'subject'),
		'Vendor Name'=>Array('purchaseorder'=>'vendorid'),
		'Tracking Number'=>Array('purchaseorder'=> 'tracking_no'),
		'Total'=>Array('purchaseorder'=>'total'),
		'Assigned To'=>Array('crmentity'=>'smownerid')
	);
	var $list_fields_name = Array(
		'Order No'=>'purchaseorder_no',
		'Subject'=>'subject',
		'Vendor Name'=>'vendor_id',
		'Tracking Number'=>'tracking_no',
		'Total'=>'hdnGrandTotal',
		'Assigned To'=>'assigned_user_id'
	);

	// Make the field link to detail view from list view (Fieldname)
	var $list_link_field = 'subject';

	// For Popup listview and UI type support
	var $search_fields = Array(
		'Order No'=>Array('purchaseorder'=>'purchaseorder_no'),
		'Subject'=>Array('purchaseorder'=>'subject'),
	);
	var $search_fields_name = Array(
		'Order No'=>'purchaseorder_no',
		'Subject'=>'subject',
	);

	// For Popup window record selection
	var $popup_fields = Array('subject');

	// Used when enabling/disabling the mandatory fields for the module.
	// Refers to vtiger_field.fieldname values.
	var $mandatory_fields = Array('subject', 'vendor_id','createdtime' ,'modifiedtime');

	// This is the list of vtiger_fields that are required.
	var $required_fields = array("accountname"=>1);

	//Added these variables which are used as default order by and sortorder in ListView
	var $default_order_by = 'subject';
	var $default_sort_order = 'ASC';

	// For Alphabetical search
	var $def_basicsearch_col = 'subject';

	// Column value to use on detail view record text display
	var $def_detailview_recname = 'subject';
	var $record_status = '';

	function __construct() {
		global $log;
		$this_module = get_class($this);
		$this->column_fields = getColumnFields($this_module);
		$this->db = PearDatabase::getInstance();
		$this->log = $log;
		$sql = 'SELECT 1 FROM vtiger_field WHERE uitype=69 and tabid = ? limit 1';
		$tabid = getTabid($this_module);
		$result = $this->db->pquery($sql, array($tabid));
		if ($result and $this->db->num_rows($result)==1) {
			$this->HasDirectImageField = true;
		}
	}

	function save($module, $fileid = '') {
		if ($this->mode=='edit') {
			$this->record_status = getSingleFieldValue($this->table_name, 'postatus', $this->table_index, $this->id);
		}
		parent::save($module, $fileid);
	}

	function save_module($module) {
		global $adb;
		if ($this->HasDirectImageField) {
			$this->insertIntoAttachment($this->id,$module);
		}
		if ($this->mode=='edit' and !empty($this->record_status) and $this->record_status != $this->column_fields['postatus'] && $this->column_fields['postatus'] != '') {
			$this->registerInventoryHistory();
		}
		//in ajax save we should not call this function, because this will delete all the existing product values
		if(inventoryCanSaveProductLines($_REQUEST, 'PurchaseOrder')) {
			//Based on the total Number of rows we will save the product relationship with this entity
			saveInventoryProductDetails($this, 'PurchaseOrder');
			if(vtlib_isModuleActive("InventoryDetails"))
				InventoryDetails::createInventoryDetails($this,'PurchaseOrder');
		}
	}

	function registerInventoryHistory() {
		global $app_strings;
		if (isset($_REQUEST['ajxaction']) and $_REQUEST['ajxaction'] == 'DETAILVIEW') { //if we use ajax edit
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
	function restore($module, $id) {
		global $adb, $updateInventoryProductRel_deduct_stock;
		parent::restore($module, $id);
		$result = $adb->pquery("SELECT postatus FROM vtiger_purchaseorder where purchaseorderid=?", array($id));
		$poStatus = $adb->query_result($result,0,'postatus');
		if($poStatus == 'Received Shipment') {
			addProductsToStock($id);
		}
	}

	/**
	 * Customizing the Delete procedure.
	 */
	function trash($module, $recordId) {
		global $adb;
		$result = $adb->pquery("SELECT postatus FROM vtiger_purchaseorder where purchaseorderid=?", array($recordId));
		$poStatus = $adb->query_result($result,0,'postatus');
		if($poStatus == 'Received Shipment') {
			deductProductsFromStock($recordId);
		}
		parent::trash($module, $recordId);
	}

	/**	Function used to get the Status history of the Purchase Order
	 *	@param $id - purchaseorder id
	 *	@return $return_data - array with header and the entries in format Array('header'=>$header,'entries'=>$entries_list) where as $header and $entries_list are arrays which contains header values and all column values of all entries
	 */
	function get_postatushistory($id) {
		global $log, $adb, $mod_strings, $app_strings, $current_user;
		$log->debug("Entering get_postatushistory(".$id.") method ...");

		$query = 'select vtiger_postatushistory.*, vtiger_purchaseorder.purchaseorder_no from vtiger_postatushistory inner join vtiger_purchaseorder on vtiger_purchaseorder.purchaseorderid = vtiger_postatushistory.purchaseorderid inner join vtiger_crmentity on vtiger_crmentity.crmid = vtiger_purchaseorder.purchaseorderid where vtiger_crmentity.deleted = 0 and vtiger_purchaseorder.purchaseorderid = ?';
		$result=$adb->pquery($query, array($id));
		$noofrows = $adb->num_rows($result);
		$header = Array();
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
		$entries_list = Array();
		while ($row = $adb->fetch_array($result)) {
			$entries = Array();

			$entries[] = $row['purchaseorder_no'];
			$entries[] = $row['vendorname'];
			$total = new CurrencyField($row['total']);
			$entries[] = $total->getDisplayValueWithSymbol($current_user);
			$entries[] = (in_array($row['postatus'], $postatus_array))? getTranslatedString($row['postatus'],'PurchaseOrder'): $error_msg;
			$date = new DateTimeField($row['lastmodified']);
			$entries[] = $date->getDisplayDateTimeValue();

			$entries_list[] = $entries;
		}

		$return_data = Array('header'=>$header,'entries'=>$entries_list,'navigation'=>array('',''));
		$log->debug("Exiting get_postatushistory method ...");
		return $return_data;
	}

	/*
	 * Function to get the secondary query part of a report
	 * @param - $module primary module name
	 * @param - $secmodule secondary module name
	 * returns the query string formed on fetching the related data for report for secondary module
	 */
	function generateReportsSecQuery($module,$secmodule,$queryPlanner,$type = '',$where_condition = '') {
		$matrix = $queryPlanner->newDependencyMatrix();
		$matrix->setDependency('vtiger_crmentityPurchaseOrder', array('vtiger_usersPurchaseOrder', 'vtiger_groupsPurchaseOrder', 'vtiger_lastModifiedByPurchaseOrder'));
		$matrix->setDependency('vtiger_inventoryproductrelPurchaseOrder', array('vtiger_productsPurchaseOrder', 'vtiger_servicePurchaseOrder'));

		if (!$queryPlanner->requireTable('vtiger_purchaseorder', $matrix) && !$queryPlanner->requireTable('vtiger_purchaseordercf',$matrix)) {
			return '';
		}
		$matrix->setDependency('vtiger_purchaseorder',array('vtiger_crmentityPurchaseOrder', "vtiger_currency_info$secmodule",
				'vtiger_purchaseordercf', 'vtiger_vendorRelPurchaseOrder', 'vtiger_pobillads',
				'vtiger_poshipads', 'vtiger_inventoryproductrelPurchaseOrder', 'vtiger_contactdetailsPurchaseOrder'));
		$query = $this->getRelationQuery($module,$secmodule,"vtiger_purchaseorder","purchaseorderid",$queryPlanner);
		if ($queryPlanner->requireTable("vtiger_crmentityPurchaseOrder", $matrix)) {
			$query .= " left join vtiger_crmentity as vtiger_crmentityPurchaseOrder on vtiger_crmentityPurchaseOrder.crmid=vtiger_purchaseorder.purchaseorderid and vtiger_crmentityPurchaseOrder.deleted=0";
		}
		if ($queryPlanner->requireTable("vtiger_purchaseordercf")) {
			$query .= " left join vtiger_purchaseordercf on vtiger_purchaseorder.purchaseorderid = vtiger_purchaseordercf.purchaseorderid";
		}
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
				if ($queryPlanner->requireTable("vtiger_inventoryproductrelPurchaseOrder", $matrix)) {
				if ($module == 'Products') {
					$query .= " left join vtiger_inventoryproductrel as vtiger_inventoryproductrelPurchaseOrder on vtiger_purchaseorder.purchaseorderid = vtiger_inventoryproductrelPurchaseOrder.id and vtiger_inventoryproductrelPurchaseOrder.productid=vtiger_products.productid ";
				} elseif ($module == 'Services') {
					$query .= " left join vtiger_inventoryproductrel as vtiger_inventoryproductrelPurchaseOrder on vtiger_purchaseorder.purchaseorderid = vtiger_inventoryproductrelPurchaseOrder.id and vtiger_inventoryproductrelPurchaseOrder.productid=vtiger_service.serviceid ";
				} else {
					$query .= " left join vtiger_inventoryproductrel as vtiger_inventoryproductrelPurchaseOrder on vtiger_purchaseorder.purchaseorderid = vtiger_inventoryproductrelPurchaseOrder.id ";
				}
			}
			if ($queryPlanner->requireTable("vtiger_productsPurchaseOrder")) {
				$query .= " left join vtiger_products as vtiger_productsPurchaseOrder on vtiger_productsPurchaseOrder.productid = vtiger_inventoryproductrelPurchaseOrder.productid";
			}
			if ($queryPlanner->requireTable("vtiger_servicePurchaseOrder")) {
				$query .= " left join vtiger_service as vtiger_servicePurchaseOrder on vtiger_servicePurchaseOrder.serviceid = vtiger_inventoryproductrelPurchaseOrder.productid";
			}
		}
		if ($queryPlanner->requireTable("vtiger_usersPurchaseOrder")) {
			$query .= " left join vtiger_users as vtiger_usersPurchaseOrder on vtiger_usersPurchaseOrder.id = vtiger_crmentityPurchaseOrder.smownerid";
		}
		if ($queryPlanner->requireTable("vtiger_groupsPurchaseOrder")) {
			$query .= " left join vtiger_groups as vtiger_groupsPurchaseOrder on vtiger_groupsPurchaseOrder.groupid = vtiger_crmentityPurchaseOrder.smownerid";
		}
		if ($queryPlanner->requireTable("vtiger_vendorRelPurchaseOrder")) {
			$query .= " left join vtiger_vendor as vtiger_vendorRelPurchaseOrder on vtiger_vendorRelPurchaseOrder.vendorid = vtiger_purchaseorder.vendorid";
		}
		if ($queryPlanner->requireTable("vtiger_contactdetailsPurchaseOrder")) {
			$query .= " left join vtiger_contactdetails as vtiger_contactdetailsPurchaseOrder on vtiger_contactdetailsPurchaseOrder.contactid = vtiger_purchaseorder.contactid";
		}
		if ($queryPlanner->requireTable("vtiger_lastModifiedByPurchaseOrder")) {
			$query .= " left join vtiger_users as vtiger_lastModifiedByPurchaseOrder on vtiger_lastModifiedByPurchaseOrder.id = vtiger_crmentityPurchaseOrder.modifiedby ";
		}
		if ($queryPlanner->requireTable("vtiger_CreatedByPurchaseOrder")) {
			$query .= " left join vtiger_users as vtiger_CreatedByPurchaseOrder on vtiger_CreatedByPurchaseOrder.id = vtiger_crmentityPurchaseOrder.smcreatorid ";
		}
		return $query;
	}

	/*
	 * Function to get the relation tables for related modules
	 * @param - $secmodule secondary module name
	 * returns the array with table names and fieldnames storing relations between module and this module
	 */
	function setRelationTables($secmodule){
		$rel_tables = array (
			"Calendar" =>array("vtiger_seactivityrel"=>array("crmid","activityid"),"vtiger_purchaseorder"=>"purchaseorderid"),
			"Documents" => array("vtiger_senotesrel"=>array("crmid","notesid"),"vtiger_purchaseorder"=>"purchaseorderid"),
			"Contacts" => array("vtiger_purchaseorder"=>array("purchaseorderid","contactid")),
		);
		return isset($rel_tables[$secmodule]) ? $rel_tables[$secmodule] : '';
	}

	// Function to unlink an entity with given Id from another entity
	function unlinkRelationship($id, $return_module, $return_id) {
		global $log;
		if(empty($return_module) || empty($return_id)) return;

		if($return_module == 'Vendors') {
			$sql_req ='UPDATE vtiger_crmentity SET deleted = 1 WHERE crmid= ?';
			$this->db->pquery($sql_req, array($id));
		} elseif($return_module == 'Contacts') {
			$sql_req ='UPDATE vtiger_purchaseorder SET contactid=? WHERE purchaseorderid = ?';
			$this->db->pquery($sql_req, array(null, $id));
		} elseif($return_module == 'Documents') {
			$sql = 'DELETE FROM vtiger_senotesrel WHERE crmid=? AND notesid=?';
			$this->db->pquery($sql, array($id, $return_id));
		} else {
			parent::unlinkRelationship($id, $return_module, $return_id);
		}
	}

	/*Function to create records in current module.
	**This function called while importing records to this module*/
	function createRecords($obj) {
		return createRecords($obj);
	}

	/*Function returns the record information which means whether the record is imported or not
	**This function called while importing records to this module*/
	function importRecord($obj, $inventoryFieldData, $lineItemDetails) {
		return importRecord($obj, $inventoryFieldData, $lineItemDetails);
	}

	/*Function to return the status count of imported records in current module.
	**This function called while importing records to this module*/
	function getImportStatusCount($obj) {
		return getImportStatusCount($obj);
	}

	function undoLastImport($obj, $user) {
		$undoLastImport = undoLastImport($obj, $user);
	}

	/** Function to export the lead records in CSV Format
	* @param reference variable - where condition is passed when the query is executed
	* Returns Export PurchaseOrder Query.
	*/
	function create_export_query($where) {
		global $log, $current_user;
		$log->debug("Entering create_export_query(".$where.") method ...");

		include("include/utils/ExportUtils.php");

		//To get the Permitted fields query and the permitted fields list
		$sql = getPermittedFieldsQuery("PurchaseOrder", "detail_view");
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

		$query .= $this->getNonAdminAccessControlQuery('PurchaseOrder',$current_user);
		$where_auto = " vtiger_crmentity.deleted=0";

		if($where != "") {
			$query .= " where ($where) AND ".$where_auto;
		} else {
			$query .= " where ".$where_auto;
		}

		$log->debug("Exiting create_export_query method ...");
		return $query;
	}

}

?>
