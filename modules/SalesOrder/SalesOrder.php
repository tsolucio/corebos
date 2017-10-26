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

class SalesOrder extends CRMEntity {
	var $db, $log; // Used in class functions of CRMEntity

	var $table_name = 'vtiger_salesorder';
	var $table_index= 'salesorderid';
	var $column_fields = Array();

	/** Indicator if this is a custom module or standard module */
	var $IsCustomModule = false;
	var $HasDirectImageField = false;
	var $tab_name = Array('vtiger_crmentity','vtiger_salesorder','vtiger_sobillads','vtiger_soshipads','vtiger_salesordercf','vtiger_invoice_recurring_info');
	var $tab_name_index = Array('vtiger_crmentity'=>'crmid','vtiger_salesorder'=>'salesorderid','vtiger_sobillads'=>'sobilladdressid','vtiger_soshipads'=>'soshipaddressid','vtiger_salesordercf'=>'salesorderid','vtiger_invoice_recurring_info'=>'salesorderid');
	/**
	 * Mandatory table for supporting custom fields.
	 */
	var $customFieldTable = Array('vtiger_salesordercf', 'salesorderid');
	// Uncomment the line below to support custom field columns on related lists
	var $related_tables = Array('vtiger_account'=>array('accountid'));

	var $update_product_array = Array();

	var $sortby_fields = Array('subject','smownerid','accountname','lastname');

	// This is used to retrieve related vtiger_fields from form posts.
	var $additional_column_fields = Array('assigned_user_name', 'smownerid', 'opportunity_id', 'case_id', 'contact_id', 'task_id', 'note_id', 'meeting_id', 'call_id', 'email_id', 'parent_name', 'member_id' );

	// This is the list of vtiger_fields that are in the lists.
	var $list_fields = Array(
		'Order No'=>Array('salesorder' => 'salesorder_no'),
		'Subject'=>Array('salesorder'=>'subject'),
		'Account Name'=>Array('account'=>'accountid'),
		'Quote Name'=>Array('quotes'=>'quoteid'),
		'Total'=>Array('salesorder'=>'total'),
		'Assigned To'=>Array('crmentity'=>'smownerid')
	);
	var $list_fields_name = Array(
		'Order No'=>'salesorder_no',
		'Subject'=>'subject',
		'Account Name'=>'account_id',
		'Quote Name'=>'quote_id',
		'Total'=>'hdnGrandTotal',
		'Assigned To'=>'assigned_user_id'
	);
	var $list_link_field = 'subject';

	var $search_fields = Array(
		'Order No'=>Array('salesorder'=>'salesorder_no'),
		'Subject'=>Array('salesorder'=>'subject'),
		'Account Name'=>Array('account'=>'accountid'),
		'Quote Name'=>Array('salesorder'=>'quoteid')
	);
	var $search_fields_name = Array(
		'Order No'=>'salesorder_no',
		'Subject'=>'subject',
		'Account Name'=>'account_id',
		'Quote Name'=>'quote_id'
	);

	// For Popup window record selection
	var $popup_fields = Array('subject');

	// For Alphabetical search
	var $def_basicsearch_col = 'subject';

	// Column value to use on detail view record text display
	var $def_detailview_recname = 'subject';

	// This is the list of vtiger_fields that are required.
	var $required_fields = array("accountname"=>1);

	//Added these variables which are used as default order by and sortorder in ListView
	var $default_order_by = 'subject';
	var $default_sort_order = 'ASC';

	var $mandatory_fields = Array('subject','createdtime' ,'modifiedtime');
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
			$this->record_status = getSingleFieldValue($this->table_name, 'sostatus', $this->table_index, $this->id);
		}
		parent::save($module, $fileid);
	}

	function save_module($module) {
		global $updateInventoryProductRel_deduct_stock;
		if ($this->HasDirectImageField) {
			$this->insertIntoAttachment($this->id,$module);
		}
		if ($this->mode=='edit' and !empty($this->record_status) and $this->record_status != $this->column_fields['sostatus'] && $this->column_fields['sostatus'] != '') {
			$this->registerInventoryHistory();
		}
		$updateInventoryProductRel_deduct_stock = true;
		//Checking if quote_id is present and updating the quote status
		if($this->column_fields['quote_id'] != '') {
			$newStatus = GlobalVariable::getVariable('Quote_StatusOnSalesOrderSave', 'Accepted');
			if ($newStatus!='DoNotChange') {
				$qt_id = $this->column_fields['quote_id'];
				$query1 = 'update vtiger_quotes set quotestage=? where quoteid=?';
				$this->db->pquery($query1, array($newStatus, $qt_id));
			}
		}

		//in ajax save we should not call this function, because this will delete all the existing product values
		if(inventoryCanSaveProductLines($_REQUEST, 'SalesOrder')) {
			//Based on the total Number of rows we will save the product relationship with this entity
			saveInventoryProductDetails($this, 'SalesOrder');
			if(vtlib_isModuleActive("InventoryDetails"))
				InventoryDetails::createInventoryDetails($this,'SalesOrder');
		} else if($_REQUEST['action'] == 'SalesOrderAjax' || $_REQUEST['action'] == 'MassEditSave') {
			$updateInventoryProductRel_deduct_stock = false;
		}
	}

	function registerInventoryHistory() {
		global $app_strings;
		if (isset($_REQUEST['ajxaction']) and $_REQUEST['ajxaction'] == 'DETAILVIEW') { //if we use ajax edit
			if (GlobalVariable::getVariable('Application_B2B', '1')) {
				$relatedname = getAccountName($this->column_fields['account_id']);
			} else {
				$relatedname = getContactName($this->column_fields['contact_id']);
			}
			$total = $this->column_fields['hdnGrandTotal'];
		} else { //using edit button and save
			if (GlobalVariable::getVariable('Application_B2B', '1')) {
				$relatedname = $_REQUEST["account_name"];
			} else {
				$relatedname = $_REQUEST["contact_name"];
			}
			$total = $_REQUEST['total'];
		}
		if ($this->column_fields['sostatus'] == $app_strings['LBL_NOT_ACCESSIBLE']) {
			//If the value in the request is Not Accessible for a picklist, the existing value will be replaced instead of Not Accessible value.
			$stat_value = getSingleFieldValue($this->table_name, 'sostatus', $this->table_index, $this->id);
		} else {
			$stat_value = $this->column_fields['sostatus'];
		}
		addInventoryHistory(get_class($this), $this->id, $relatedname, $total, $stat_value);
	}

	/**
	 * Customizing the restore procedure.
	 */
	function restore($module, $id) {
		global $adb, $updateInventoryProductRel_deduct_stock;
		$result = $adb->pquery("SELECT sostatus FROM vtiger_salesorder where salesorderid=?", array($id));
		$soStatus = $adb->query_result($result,0,'sostatus');
		if($soStatus != 'Cancelled') {
			$updateInventoryProductRel_deduct_stock = true;
		}
		parent::restore($module, $id);
	}

	/**
	 * Customizing the Delete procedure.
	 */
	function trash($module, $recordId) {
		global $adb;
		$result = $adb->pquery("SELECT sostatus FROM vtiger_salesorder where salesorderid=?", array($recordId));
		$soStatus = $adb->query_result($result,0,'sostatus');
		if($soStatus != 'Cancelled') {
			addProductsToStock($recordId);
		}
		parent::trash($module, $recordId);
	}

	/** Function to get the invoices associated with the Sales Order
	 *  This function accepts the id as arguments and execute the MySQL query using the id
	 *  and sends the query and the id as arguments to renderRelatedInvoices() method.
	 */
	function get_invoices($id)
	{
		global $log,$singlepane_view;
		$log->debug("Entering get_invoices(".$id.") method ...");
		require_once('modules/Invoice/Invoice.php');

		$focus = new Invoice();

		$button = '';
		if($singlepane_view == 'true')
			$returnset = '&return_module=SalesOrder&return_action=DetailView&return_id='.$id;
		else
			$returnset = '&return_module=SalesOrder&return_action=CallRelatedList&return_id='.$id;

		$userNameSql = getSqlForNameInDisplayFormat(array('first_name'=>
							'vtiger_users.first_name', 'last_name' => 'vtiger_users.last_name'), 'Users');
		$query = "select vtiger_crmentity.*, vtiger_invoice.*, vtiger_account.accountname,
			vtiger_salesorder.subject as salessubject, case when
			(vtiger_users.user_name not like '') then $userNameSql else vtiger_groups.groupname
			end as user_name from vtiger_invoice
			inner join vtiger_crmentity on vtiger_crmentity.crmid=vtiger_invoice.invoiceid
			left outer join vtiger_account on vtiger_account.accountid=vtiger_invoice.accountid
			inner join vtiger_salesorder on vtiger_salesorder.salesorderid=vtiger_invoice.salesorderid
			LEFT JOIN vtiger_invoicecf ON vtiger_invoicecf.invoiceid = vtiger_invoice.invoiceid
			LEFT JOIN vtiger_invoicebillads ON vtiger_invoicebillads.invoicebilladdressid = vtiger_invoice.invoiceid
			LEFT JOIN vtiger_invoiceshipads ON vtiger_invoiceshipads.invoiceshipaddressid = vtiger_invoice.invoiceid
			left join vtiger_users on vtiger_users.id=vtiger_crmentity.smownerid
			left join vtiger_groups on vtiger_groups.groupid=vtiger_crmentity.smownerid
			where vtiger_crmentity.deleted=0 and vtiger_salesorder.salesorderid=".$id;

		$log->debug("Exiting get_invoices method ...");
		return GetRelatedList('SalesOrder','Invoice',$focus,$query,$button,$returnset);
	}

	/**	Function used to get the Status history of the Sales Order
	 *	@param $id - salesorder id
	 *	@return $return_data - array with header and the entries in format Array('header'=>$header,'entries'=>$entries_list) where as $header and $entries_list are arrays which contains header values and all column values of all entries
	 */
	function get_sostatushistory($id) {
		global $log, $adb, $mod_strings, $app_strings, $current_user;
		$log->debug("Entering get_sostatushistory(".$id.") method ...");

		$query = 'select vtiger_sostatushistory.*, vtiger_salesorder.salesorder_no from vtiger_sostatushistory inner join vtiger_salesorder on vtiger_salesorder.salesorderid = vtiger_sostatushistory.salesorderid inner join vtiger_crmentity on vtiger_crmentity.crmid = vtiger_salesorder.salesorderid where vtiger_crmentity.deleted = 0 and vtiger_salesorder.salesorderid = ?';
		$result=$adb->pquery($query, array($id));
		$noofrows = $adb->num_rows($result);
		$header = array();
		$header[] = $app_strings['Order No'];
		$header[] = $app_strings['LBL_ACCOUNT_NAME'];
		$header[] = $app_strings['LBL_AMOUNT'];
		$header[] = $app_strings['LBL_SO_STATUS'];
		$header[] = $app_strings['LBL_LAST_MODIFIED'];

		//Getting the field permission for the current user. 1 - Not Accessible, 0 - Accessible
		//Account Name , Total are mandatory fields. So no need to do security check to these fields.

		//If field is accessible then getFieldVisibilityPermission function will return 0 else return 1
		$sostatus_access = (getFieldVisibilityPermission('SalesOrder', $current_user->id, 'sostatus') != '0')? 1 : 0;
		$picklistarray = getAccessPickListValues('SalesOrder');

		$sostatus_array = ($sostatus_access != 1)? $picklistarray['sostatus']: array();
		//- ==> picklist field is not permitted in profile
		//Not Accessible - picklist is permitted in profile but picklist value is not permitted
		$error_msg = ($sostatus_access != 1)? getTranslatedString('LBL_NOT_ACCESSIBLE'): '-';
		$entries_list = Array();
		while ($row = $adb->fetch_array($result)) {
			$entries = Array();

			$entries[] = $row['salesorder_no'];
			$entries[] = $row['accountname'];
			$total = new CurrencyField($row['total']);
			$entries[] = $total->getDisplayValueWithSymbol($current_user);
			$entries[] = (in_array($row['sostatus'], $sostatus_array))? getTranslatedString($row['sostatus'],'SalesOrder'): $error_msg;
			$date = new DateTimeField($row['lastmodified']);
			$entries[] = $date->getDisplayDateTimeValue();

			$entries_list[] = $entries;
		}

		$return_data = Array('header'=>$header,'entries'=>$entries_list,'navigation'=>array('',''));
		$log->debug("Exiting get_sostatushistory method ...");
		return $return_data;
	}

	/*
	 * Function to get the secondary query part of a report
	 * @param - $module primary module name
	 * @param - $secmodule secondary module name
	 * returns the query string formed on fetching the related data for report for secondary module
	 */
	function generateReportsSecQuery($module,$secmodule,$queryPlanner,$type = '',$where_condition = ''){
		$matrix = $queryPlanner->newDependencyMatrix();
		$matrix->setDependency('vtiger_crmentitySalesOrder', array('vtiger_usersSalesOrder', 'vtiger_groupsSalesOrder', 'vtiger_lastModifiedBySalesOrder'));
		$matrix->setDependency('vtiger_inventoryproductrelSalesOrder', array('vtiger_productsSalesOrder', 'vtiger_serviceSalesOrder'));
		if (!$queryPlanner->requireTable('vtiger_salesorder', $matrix) && !$queryPlanner->requireTable('vtiger_salesordercf',$matrix)) {
			return '';
		}
		$matrix->setDependency('vtiger_salesorder',array('vtiger_crmentitySalesOrder', "vtiger_currency_info$secmodule",
				'vtiger_salesordercf', 'vtiger_potentialRelSalesOrder', 'vtiger_sobillads','vtiger_soshipads',
				'vtiger_inventoryproductrelSalesOrder', 'vtiger_contactdetailsSalesOrder', 'vtiger_accountSalesOrder',
				'vtiger_invoice_recurring_info','vtiger_quotesSalesOrder'));
		$query = $this->getRelationQuery($module,$secmodule,"vtiger_salesorder","salesorderid", $queryPlanner);
		if ($queryPlanner->requireTable("vtiger_crmentitySalesOrder",$matrix)){
			$query .= " left join vtiger_crmentity as vtiger_crmentitySalesOrder on vtiger_crmentitySalesOrder.crmid=vtiger_salesorder.salesorderid and vtiger_crmentitySalesOrder.deleted=0";
		}
		if ($queryPlanner->requireTable("vtiger_salesordercf")){
			$query .= " left join vtiger_salesordercf on vtiger_salesorder.salesorderid = vtiger_salesordercf.salesorderid";
		}
		if ($queryPlanner->requireTable("vtiger_sobillads")){
			$query .= " left join vtiger_sobillads on vtiger_salesorder.salesorderid=vtiger_sobillads.sobilladdressid";
		}
		if ($queryPlanner->requireTable("vtiger_soshipads")){
			$query .= " left join vtiger_soshipads on vtiger_salesorder.salesorderid=vtiger_soshipads.soshipaddressid";
		}
		if ($queryPlanner->requireTable("vtiger_currency_info$secmodule")){
			$query .= " left join vtiger_currency_info as vtiger_currency_info$secmodule on vtiger_currency_info$secmodule.id = vtiger_salesorder.currency_id";
		}
		if (($type !== 'COLUMNSTOTOTAL') || ($type == 'COLUMNSTOTOTAL' && $where_condition == 'add')) {
			if ($queryPlanner->requireTable("vtiger_inventoryproductrelSalesOrder", $matrix)){
				if ($module == 'Products') {
					$query .= " left join vtiger_inventoryproductrel as vtiger_inventoryproductrelSalesOrder on vtiger_salesorder.salesorderid = vtiger_inventoryproductrelSalesOrder.id and vtiger_inventoryproductrelSalesOrder.productid=vtiger_products.productid ";
				} elseif ($module == 'Services') {
					$query .= " left join vtiger_inventoryproductrel as vtiger_inventoryproductrelSalesOrder on vtiger_salesorder.salesorderid = vtiger_inventoryproductrelSalesOrder.id and vtiger_inventoryproductrelSalesOrder.productid=vtiger_service.serviceid ";
				} else {
					$query .= " left join vtiger_inventoryproductrel as vtiger_inventoryproductrelSalesOrder on vtiger_salesorder.salesorderid = vtiger_inventoryproductrelSalesOrder.id ";
				}
			}
			if ($queryPlanner->requireTable("vtiger_productsSalesOrder")){
				$query .= " left join vtiger_products as vtiger_productsSalesOrder on vtiger_productsSalesOrder.productid = vtiger_inventoryproductrelSalesOrder.productid";
			}
			if ($queryPlanner->requireTable("vtiger_serviceSalesOrder")){
				$query .= " left join vtiger_service as vtiger_serviceSalesOrder on vtiger_serviceSalesOrder.serviceid = vtiger_inventoryproductrelSalesOrder.productid";
			}
		}
		if ($queryPlanner->requireTable("vtiger_groupsSalesOrder")){
			$query .= " left join vtiger_groups as vtiger_groupsSalesOrder on vtiger_groupsSalesOrder.groupid = vtiger_crmentitySalesOrder.smownerid";
		}
		if ($queryPlanner->requireTable("vtiger_usersSalesOrder")){
			$query .= " left join vtiger_users as vtiger_usersSalesOrder on vtiger_usersSalesOrder.id = vtiger_crmentitySalesOrder.smownerid";
		}
		if ($queryPlanner->requireTable("vtiger_potentialRelSalesOrder")){
			$query .= " left join vtiger_potential as vtiger_potentialRelSalesOrder on vtiger_potentialRelSalesOrder.potentialid = vtiger_salesorder.potentialid";
		}
		if ($queryPlanner->requireTable("vtiger_contactdetailsSalesOrder")){
			$query .= " left join vtiger_contactdetails as vtiger_contactdetailsSalesOrder on vtiger_salesorder.contactid = vtiger_contactdetailsSalesOrder.contactid";
		}
		if ($queryPlanner->requireTable("vtiger_invoice_recurring_info")){
			$query .= " left join vtiger_invoice_recurring_info on vtiger_salesorder.salesorderid = vtiger_invoice_recurring_info.salesorderid";
		}
		if ($queryPlanner->requireTable("vtiger_quotesSalesOrder")){
			$query .= " left join vtiger_quotes as vtiger_quotesSalesOrder on vtiger_salesorder.quoteid = vtiger_quotesSalesOrder.quoteid";
		}
		if ($queryPlanner->requireTable("vtiger_accountSalesOrder")){
			$query .= " left join vtiger_account as vtiger_accountSalesOrder on vtiger_accountSalesOrder.accountid = vtiger_salesorder.accountid";
		}
		if ($queryPlanner->requireTable("vtiger_lastModifiedBySalesOrder")){
			$query .= " left join vtiger_users as vtiger_lastModifiedBySalesOrder on vtiger_lastModifiedBySalesOrder.id = vtiger_crmentitySalesOrder.modifiedby ";
		}
		if ($queryPlanner->requireTable("vtiger_CreatedBySalesOrder")){
			$query .= " left join vtiger_users as vtiger_CreatedBySalesOrder on vtiger_CreatedBySalesOrder.id = vtiger_crmentitySalesOrder.smcreatorid ";
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
			"Calendar" =>array("vtiger_seactivityrel"=>array("crmid","activityid"),"vtiger_salesorder"=>"salesorderid"),
			"Invoice" =>array("vtiger_invoice"=>array("salesorderid","invoiceid"),"vtiger_salesorder"=>"salesorderid"),
			"Quotes" =>array("vtiger_quotes"=>array("salesorderid","quoteid")),
			"Potentials" =>array("vtiger_salesorder"=>array("salesorderid","potentialid")),
			"Documents" => array("vtiger_senotesrel"=>array("crmid","notesid"),"vtiger_salesorder"=>"salesorderid"),
			"Accounts" => array("vtiger_salesorder"=>array("salesorderid","accountid")),
			"Contacts" => array("vtiger_salesorder"=>array("salesorderid","contactid")),
		);
		return isset($rel_tables[$secmodule]) ? $rel_tables[$secmodule] : '';
	}

	// Function to unlink an entity with given Id from another entity
	function unlinkRelationship($id, $return_module, $return_id) {
		global $log;
		if(empty($return_module) || empty($return_id)) return;

		if($return_module == 'Accounts') {
			$this->trash('SalesOrder',$id);
		}
		elseif($return_module == 'Quotes') {
			$relation_query = 'UPDATE vtiger_salesorder SET quoteid=? WHERE salesorderid=?';
			$this->db->pquery($relation_query, array(null, $id));
		}
		elseif($return_module == 'Potentials') {
			$relation_query = 'UPDATE vtiger_salesorder SET potentialid=? WHERE salesorderid=?';
			$this->db->pquery($relation_query, array(null, $id));
		}
		elseif($return_module == 'Contacts') {
			$relation_query = 'UPDATE vtiger_salesorder SET contactid=? WHERE salesorderid=?';
			$this->db->pquery($relation_query, array(null, $id));
		} elseif($return_module == 'Documents') {
			$sql = 'DELETE FROM vtiger_senotesrel WHERE crmid=? AND notesid=?';
			$this->db->pquery($sql, array($id, $return_id));
		} else {
			parent::unlinkRelationship($id, $return_module, $return_id);
		}
	}

	public function getJoinClause($tableName) {
		if ($tableName == 'vtiger_invoice_recurring_info') {
			return 'LEFT JOIN';
		}
		return parent::getJoinClause($tableName);
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
	* Returns Export SalesOrder Query.
	*/
	function create_export_query($where) {
		global $log, $current_user;
		$log->debug("Entering create_export_query(".$where.") method ...");

		include("include/utils/ExportUtils.php");

		//To get the Permitted fields query and the permitted fields list
		$sql = getPermittedFieldsQuery("SalesOrder", "detail_view");
		$fields_list = getFieldsListFromQuery($sql);
		$fields_list .= getInventoryFieldsForExport($this->table_name);
		$userNameSql = getSqlForNameInDisplayFormat(array('first_name'=>'vtiger_users.first_name', 'last_name' => 'vtiger_users.last_name'), 'Users');

		$query = "SELECT $fields_list FROM vtiger_crmentity
			INNER JOIN vtiger_salesorder ON vtiger_salesorder.salesorderid = vtiger_crmentity.crmid
			LEFT JOIN vtiger_salesordercf ON vtiger_salesordercf.salesorderid = vtiger_salesorder.salesorderid
			LEFT JOIN vtiger_sobillads ON vtiger_sobillads.sobilladdressid = vtiger_salesorder.salesorderid
			LEFT JOIN vtiger_soshipads ON vtiger_soshipads.soshipaddressid = vtiger_salesorder.salesorderid
			LEFT JOIN vtiger_inventoryproductrel ON vtiger_inventoryproductrel.id = vtiger_salesorder.salesorderid
			LEFT JOIN vtiger_products ON vtiger_products.productid = vtiger_inventoryproductrel.productid
			LEFT JOIN vtiger_service ON vtiger_service.serviceid = vtiger_inventoryproductrel.productid
			LEFT JOIN vtiger_contactdetails ON vtiger_contactdetails.contactid = vtiger_salesorder.contactid
			LEFT JOIN vtiger_invoice_recurring_info ON vtiger_invoice_recurring_info.salesorderid = vtiger_salesorder.salesorderid
			LEFT JOIN vtiger_potential ON vtiger_potential.potentialid = vtiger_salesorder.potentialid
			LEFT JOIN vtiger_account ON vtiger_account.accountid = vtiger_salesorder.accountid
			LEFT JOIN vtiger_currency_info ON vtiger_currency_info.id = vtiger_salesorder.currency_id
			LEFT JOIN vtiger_quotes ON vtiger_quotes.quoteid = vtiger_salesorder.quoteid
			LEFT JOIN vtiger_groups ON vtiger_groups.groupid = vtiger_crmentity.smownerid
			LEFT JOIN vtiger_users as vtigerCreatedBy ON vtiger_crmentity.smcreatorid = vtigerCreatedBy.id and vtigerCreatedBy.status='Active'
			LEFT JOIN vtiger_users ON vtiger_users.id = vtiger_crmentity.smownerid";

		$query .= $this->getNonAdminAccessControlQuery('SalesOrder',$current_user);
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
