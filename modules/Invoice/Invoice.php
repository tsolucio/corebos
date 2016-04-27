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
require_once('modules/InventoryDetails/InventoryDetails.php');

class Invoice extends CRMEntity {
	var $db, $log; // Used in class functions of CRMEntity

	var $table_name = 'vtiger_invoice';
	var $table_index= 'invoiceid';
	var $column_fields = Array();

	/** Indicator if this is a custom module or standard module */
	var $IsCustomModule = false;
	var $HasDirectImageField = false;
	var $tab_name = Array('vtiger_crmentity','vtiger_invoice','vtiger_invoicebillads','vtiger_invoiceshipads','vtiger_invoicecf');
	var $tab_name_index = Array('vtiger_crmentity'=>'crmid','vtiger_invoice'=>'invoiceid','vtiger_invoicebillads'=>'invoicebilladdressid','vtiger_invoiceshipads'=>'invoiceshipaddressid','vtiger_invoicecf'=>'invoiceid');
	var $entity_table = 'vtiger_crmentity';
	/**
	 * Mandatory table for supporting custom fields.
	 */
	var $customFieldTable = Array('vtiger_invoicecf', 'invoiceid');

	var $update_product_array = Array();

	// This is used to retrieve related vtiger_fields from form posts.
	var $additional_column_fields = Array('assigned_user_name', 'smownerid', 'opportunity_id', 'case_id', 'contact_id', 'task_id', 'note_id', 'meeting_id', 'call_id', 'email_id', 'parent_name', 'member_id' );

	// This is the list of vtiger_fields that are in the lists.
	var $list_fields = Array(
		//'Invoice No'=>Array('crmentity'=>'crmid'),
		'Invoice No'=>Array('invoice'=>'invoice_no'),
		'Subject'=>Array('invoice'=>'subject'),
		'Sales Order'=>Array('invoice'=>'salesorderid'),
		'Status'=>Array('invoice'=>'invoicestatus'),
		'Total'=>Array('invoice'=>'total'),
		'Account Name'=>Array('invoice'=>'account_id'),
		'Assigned To'=>Array('crmentity'=>'smownerid')
	);
	var $list_fields_name = Array(
		'Invoice No'=>'invoice_no',
		'Subject'=>'subject',
		'Sales Order'=>'salesorder_id',
		'Status'=>'invoicestatus',
		'Total'=>'hdnGrandTotal',
		'Account Name'=>'account_id',
		'Assigned To'=>'assigned_user_id'
	);
	var $list_link_field= 'subject';

	var $search_fields = Array(
		//'Invoice No'=>Array('crmentity'=>'crmid'),
		'Invoice No'=>Array('invoice'=>'invoice_no'),
		'Subject'=>Array('purchaseorder'=>'subject'),
		'Account Name'=>Array('invoice'=>'account_id'),
	);
	var $search_fields_name = Array(
		'Invoice No'=>'invoice_no',
		'Subject'=>'subject',
		'Account Name'=>'account_id',
	);

	// Placeholder for sort fields - All the fields will be initialized for Sorting through initSortFields
	var $sortby_fields = Array('subject','invoice_no','invoicestatus','smownerid','accountname','lastname');

	// For Alphabetical search
	var $def_basicsearch_col = 'subject';

	// Column value to use on detail view record text display
	var $def_detailview_recname = 'subject';

	// This is the list of vtiger_fields that are required.
	var $required_fields = array("accountname"=>1);

	//Added these variables which are used as default order by and sortorder in ListView
	var $default_order_by = 'crmid';
	var $default_sort_order = 'ASC';
	var $mandatory_fields = Array('subject','createdtime' ,'modifiedtime');
	var $_salesorderid;
	var $_recurring_mode;

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

	function save_module($module) {
		global $updateInventoryProductRel_deduct_stock;
		$updateInventoryProductRel_deduct_stock = true;
		//Checking if salesorderid is present and updating the SO status
		if(!empty($this->column_fields['salesorder_id'])) {
			$newStatus = GlobalVariable::getVariable('SalesOrderStatusOnInvoiceSave', 'Approved');
			if ($newStatus!='DoNotChange') {
				$so_id = $this->column_fields['salesorder_id'];
				$query1 = 'update vtiger_salesorder set sostatus=? where salesorderid=?';
				$this->db->pquery($query1, array($newStatus, $so_id));
			}
		}

		//in ajax save we should not call this function, because this will delete all the existing product values
		if(isset($this->_recurring_mode) && $this->_recurring_mode == 'recurringinvoice_from_so' && isset($this->_salesorderid) && $this->_salesorderid!='') {
			// We are getting called from the RecurringInvoice cron service!
			$this->createRecurringInvoiceFromSO();
			if(vtlib_isModuleActive("InventoryDetails"))
				InventoryDetails::createInventoryDetails($this,'Invoice');

		} else if(isset($_REQUEST)) {
			if(inventoryCanSaveProductLines($_REQUEST)) {
				//Based on the total Number of rows we will save the product relationship with this entity
				saveInventoryProductDetails($this, 'Invoice');
				if(vtlib_isModuleActive("InventoryDetails"))
					InventoryDetails::createInventoryDetails($this,'Invoice');
			} else if($_REQUEST['action'] == 'InvoiceAjax' || $_REQUEST['action'] == 'MassEditSave') {
				$updateInventoryProductRel_deduct_stock = false;
			}
		}

		// Update the currency id and the conversion rate for the invoice
		$update_query = "update vtiger_invoice set currency_id=?, conversion_rate=? where invoiceid=?";

		$update_params = array($this->column_fields['currency_id'], $this->column_fields['conversion_rate'], $this->id);
		$this->db->pquery($update_query, $update_params);
	}

	/**
	 * Customizing the restore procedure.
	 */
	function restore($module, $id) {
		global $adb, $updateInventoryProductRel_deduct_stock;
		$result = $adb->pquery("SELECT invoicestatus FROM vtiger_invoice where invoiceid=?", array($id));
		$invoiceStatus = $adb->query_result($result,0,'invoicestatus');
		if($invoiceStatus != 'Cancel') {
			$updateInventoryProductRel_deduct_stock = true;
		}
		parent::restore($module, $id);
	}

	/**
	 * Customizing the Delete procedure.
	 */
	function trash($module, $recordId) {
		global $adb;
		$result = $adb->pquery("SELECT invoicestatus FROM vtiger_invoice where invoiceid=?", array($recordId));
		$invoiceStatus = $adb->query_result($result,0,'invoicestatus');
		if($invoiceStatus != 'Cancel') {
			addProductsToStock($recordId);
		}
		parent::trash($module, $recordId);
	}

	/**	function used to get the list of activities which are related to the invoice
	 *	@param int $id - invoice id
	 *	@return array - return an array which will be returned from the function GetRelatedList
	 */
	function get_activities($id, $cur_tab_id, $rel_tab_id, $actions=false) {
		global $log, $singlepane_view,$currentModule,$current_user;
		$log->debug("Entering get_activities(".$id.") method ...");
		$this_module = $currentModule;

		$related_module = vtlib_getModuleNameById($rel_tab_id);
		require_once("modules/$related_module/Activity.php");
		$other = new Activity();
		vtlib_setup_modulevars($related_module, $other);
		$singular_modname = vtlib_toSingular($related_module);

		$parenttab = getParentTab();

		if($singlepane_view == 'true')
			$returnset = '&return_module='.$this_module.'&return_action=DetailView&return_id='.$id;
		else
			$returnset = '&return_module='.$this_module.'&return_action=CallRelatedList&return_id='.$id;

		$button = '';

		$button .= '<input type="hidden" name="activity_mode">';

		if($actions) {
			if(is_string($actions)) $actions = explode(',', strtoupper($actions));
			if(in_array('ADD', $actions) && isPermitted($related_module,1, '') == 'yes') {
				if(getFieldVisibilityPermission('Calendar',$current_user->id,'parent_id', 'readwrite') == '0') {
					$button .= "<input title='".getTranslatedString('LBL_NEW'). " ". getTranslatedString('LBL_TODO', $related_module) ."' class='crmbutton small create'" .
						" onclick='this.form.action.value=\"EventEditView\";this.form.module.value=\"Calendar4You\";this.form.return_module.value=\"$this_module\";this.form.activity_mode.value=\"Task\";' type='submit' name='button'" .
						" value='". getTranslatedString('LBL_ADD_NEW'). " " . getTranslatedString('LBL_TODO', $related_module) ."'>&nbsp;";
				}
			}
		}

		$userNameSql = getSqlForNameInDisplayFormat(array('first_name'=>
							'vtiger_users.first_name', 'last_name' => 'vtiger_users.last_name'), 'Users');
		$query = "SELECT case when (vtiger_users.user_name not like '') then $userNameSql else vtiger_groups.groupname end as user_name,
				vtiger_contactdetails.lastname, vtiger_contactdetails.firstname, vtiger_contactdetails.contactid,
				vtiger_activity.*,vtiger_seactivityrel.*,vtiger_crmentity.crmid, vtiger_crmentity.smownerid,
				vtiger_crmentity.modifiedtime
				from vtiger_activity
				inner join vtiger_seactivityrel on vtiger_seactivityrel.activityid=vtiger_activity.activityid
				inner join vtiger_crmentity on vtiger_crmentity.crmid=vtiger_activity.activityid
				left join vtiger_cntactivityrel on vtiger_cntactivityrel.activityid= vtiger_activity.activityid
				left join vtiger_contactdetails on vtiger_contactdetails.contactid = vtiger_cntactivityrel.contactid
				left join vtiger_users on vtiger_users.id=vtiger_crmentity.smownerid
				left join vtiger_groups on vtiger_groups.groupid=vtiger_crmentity.smownerid
				where vtiger_seactivityrel.crmid=".$id." and activitytype='Task' and vtiger_crmentity.deleted=0
						and (vtiger_activity.status is not NULL and vtiger_activity.status != 'Completed')
						and (vtiger_activity.status is not NULL and vtiger_activity.status != 'Deferred')";

		$return_value = GetRelatedList($this_module, $related_module, $other, $query, $button, $returnset);

		if($return_value == null) $return_value = Array();
		$return_value['CUSTOM_BUTTON'] = $button;

		$log->debug("Exiting get_activities method ...");
		return $return_value;
	}

	/**	function used to get the the activity history related to the quote
	 *	@param int $id - invoice id
	 *	@return array - return an array which will be returned from the function GetHistory
	 */
	function get_history($id)
	{
		global $log;
		$log->debug("Entering get_history(".$id.") method ...");
		$userNameSql = getSqlForNameInDisplayFormat(array('first_name'=>
							'vtiger_users.first_name', 'last_name' => 'vtiger_users.last_name'), 'Users');
		$query = "SELECT vtiger_contactdetails.lastname, vtiger_contactdetails.firstname,
				vtiger_contactdetails.contactid,vtiger_activity.*,vtiger_seactivityrel.*,
				vtiger_crmentity.crmid, vtiger_crmentity.smownerid, vtiger_crmentity.modifiedtime,
				vtiger_crmentity.createdtime, vtiger_crmentity.description,
				case when (vtiger_users.user_name not like '') then $userNameSql else vtiger_groups.groupname end as user_name
				from vtiger_activity
				inner join vtiger_seactivityrel on vtiger_seactivityrel.activityid=vtiger_activity.activityid
				inner join vtiger_crmentity on vtiger_crmentity.crmid=vtiger_activity.activityid
				left join vtiger_cntactivityrel on vtiger_cntactivityrel.activityid= vtiger_activity.activityid
				left join vtiger_contactdetails on vtiger_contactdetails.contactid = vtiger_cntactivityrel.contactid
				left join vtiger_groups on vtiger_groups.groupid=vtiger_crmentity.smownerid
				left join vtiger_users on vtiger_users.id=vtiger_crmentity.smownerid
				where vtiger_activity.activitytype='Task'
					and (vtiger_activity.status = 'Completed' or vtiger_activity.status = 'Deferred')
					and vtiger_seactivityrel.crmid=".$id."
					and vtiger_crmentity.deleted = 0";
		//Don't add order by, because, for security, one more condition will be added with this query in include/RelatedListView.php

		$log->debug("Exiting get_history method ...");
		return getHistory('Invoice',$query,$id);
	}

	/**	Function used to get the Status history of the Invoice
	 *	@param $id - invoice id
	 *	@return $return_data - array with header and the entries in format Array('header'=>$header,'entries'=>$entries_list) where as $header and $entries_list are arrays which contains header values and all column values of all entries
	 */
	function get_invoicestatushistory($id)
	{
		global $log;
		$log->debug("Entering get_invoicestatushistory(".$id.") method ...");

		global $adb;
		global $mod_strings;
		global $app_strings;

		$query = 'select vtiger_invoicestatushistory.*, vtiger_invoice.invoice_no from vtiger_invoicestatushistory inner join vtiger_invoice on vtiger_invoice.invoiceid = vtiger_invoicestatushistory.invoiceid inner join vtiger_crmentity on vtiger_crmentity.crmid = vtiger_invoice.invoiceid where vtiger_crmentity.deleted = 0 and vtiger_invoice.invoiceid = ?';
		$result=$adb->pquery($query, array($id));
		$noofrows = $adb->num_rows($result);

		$header[] = $app_strings['Invoice No'];
		$header[] = $app_strings['LBL_ACCOUNT_NAME'];
		$header[] = $app_strings['LBL_AMOUNT'];
		$header[] = $app_strings['LBL_INVOICE_STATUS'];
		$header[] = $app_strings['LBL_LAST_MODIFIED'];

		//Getting the field permission for the current user. 1 - Not Accessible, 0 - Accessible
		//Account Name , Amount are mandatory fields. So no need to do security check to these fields.
		global $current_user;

		//If field is accessible then getFieldVisibilityPermission function will return 0 else return 1
		$invoicestatus_access = (getFieldVisibilityPermission('Invoice', $current_user->id, 'invoicestatus') != '0')? 1 : 0;
		$picklistarray = getAccessPickListValues('Invoice');

		$invoicestatus_array = ($invoicestatus_access != 1)? $picklistarray['invoicestatus']: array();
		//- ==> picklist field is not permitted in profile
		//Not Accessible - picklist is permitted in profile but picklist value is not permitted
		$error_msg = ($invoicestatus_access != 1)? 'Not Accessible': '-';

		while($row = $adb->fetch_array($result))
		{
			$entries = Array();

			// Module Sequence Numbering
			//$entries[] = $row['invoiceid'];
			$entries[] = $row['invoice_no'];
			// END
			$entries[] = $row['accountname'];
			$entries[] = $row['total'];
			$entries[] = (in_array($row['invoicestatus'], $invoicestatus_array))? $row['invoicestatus']: $error_msg;
			$entries[] = DateTimeField::convertToUserFormat($row['lastmodified']);

			$entries_list[] = $entries;
		}

		$return_data = Array('header'=>$header,'entries'=>$entries_list);

		$log->debug("Exiting get_invoicestatushistory method ...");

		return $return_data;
	}

	// Function to get column name - Overriding function of base class
	function get_column_value($columname, $fldvalue, $fieldname, $uitype, $datatype = '') {
		if ($columname == 'salesorderid') {
			if ($fldvalue == '') return null;
		}
		return parent::get_column_value($columname, $fldvalue, $fieldname, $uitype, $datatype);
	}

	/*
	 * Function to get the secondary query part of a report
	 * @param - $module primary module name
	 * @param - $secmodule secondary module name
	 * returns the query string formed on fetching the related data for report for secondary module
	 */
	function generateReportsSecQuery($module,$secmodule,$type = '',$where_condition = ''){
		$query = $this->getRelationQuery($module,$secmodule,"vtiger_invoice","invoiceid");
		$query .= " left join vtiger_crmentity as vtiger_crmentityInvoice on vtiger_crmentityInvoice.crmid=vtiger_invoice.invoiceid and vtiger_crmentityInvoice.deleted=0
			left join vtiger_invoicecf on vtiger_invoice.invoiceid = vtiger_invoicecf.invoiceid
			left join vtiger_currency_info as vtiger_currency_info$secmodule on vtiger_currency_info$secmodule.id = vtiger_invoice.currency_id
			left join vtiger_salesorder as vtiger_salesorderInvoice on vtiger_salesorderInvoice.salesorderid=vtiger_invoice.salesorderid
			left join vtiger_invoicebillads on vtiger_invoice.invoiceid=vtiger_invoicebillads.invoicebilladdressid
			left join vtiger_invoiceshipads on vtiger_invoice.invoiceid=vtiger_invoiceshipads.invoiceshipaddressid ";
		if(($type !== 'COLUMNSTOTOTAL') || ($type == 'COLUMNSTOTOTAL' && $where_condition == 'add')) {
			$query .= "left join vtiger_inventoryproductrel as vtiger_inventoryproductrelInvoice on vtiger_invoice.invoiceid = vtiger_inventoryproductrelInvoice.id
				left join vtiger_products as vtiger_productsInvoice on vtiger_productsInvoice.productid = vtiger_inventoryproductrelInvoice.productid
				left join vtiger_service as vtiger_serviceInvoice on vtiger_serviceInvoice.serviceid = vtiger_inventoryproductrelInvoice.productid ";
		}
		$query .= "left join vtiger_groups as vtiger_groupsInvoice on vtiger_groupsInvoice.groupid = vtiger_crmentityInvoice.smownerid
			left join vtiger_users as vtiger_usersInvoice on vtiger_usersInvoice.id = vtiger_crmentityInvoice.smownerid
			left join vtiger_contactdetails as vtiger_contactdetailsInvoice on vtiger_invoice.contactid = vtiger_contactdetailsInvoice.contactid
			left join vtiger_account as vtiger_accountInvoice on vtiger_accountInvoice.accountid = vtiger_invoice.accountid
			left join vtiger_users as vtiger_lastModifiedByInvoice on vtiger_lastModifiedByInvoice.id = vtiger_crmentityInvoice.modifiedby ";
		return $query;
	}

	/*
	 * Function to get the relation tables for related modules
	 * @param - $secmodule secondary module name
	 * returns the array with table names and fieldnames storing relations between module and this module
	 */
	function setRelationTables($secmodule){
		$rel_tables = array (
			"Calendar" =>array("vtiger_seactivityrel"=>array("crmid","activityid"),"vtiger_invoice"=>"invoiceid"),
			"Documents" => array("vtiger_senotesrel"=>array("crmid","notesid"),"vtiger_invoice"=>"invoiceid"),
			"Accounts" => array("vtiger_invoice"=>array("invoiceid","accountid")),
			"Contacts" => array("vtiger_invoice"=>array("invoiceid","contactid")),
		);
		return $rel_tables[$secmodule];
	}

	// Function to unlink an entity with given Id from another entity
	function unlinkRelationship($id, $return_module, $return_id) {
		global $log;
		if(empty($return_module) || empty($return_id)) return;

		if($return_module == 'Accounts' || $return_module == 'Contacts') {
			$this->trash('Invoice',$id);
		} elseif($return_module=='SalesOrder') {
			$relation_query = 'UPDATE vtiger_invoice set salesorderid=? where invoiceid=?';
			$this->db->pquery($relation_query, array(null,$id));
		} else {
			$sql = 'DELETE FROM vtiger_crmentityrel WHERE (crmid=? AND relmodule=? AND relcrmid=?) OR (relcrmid=? AND module=? AND crmid=?)';
			$params = array($id, $return_module, $return_id, $id, $return_module, $return_id);
			$this->db->pquery($sql, $params);
		}
	}

	/*
	 * Function to get the relations of salesorder to invoice for recurring invoice procedure
	 * @param - $salesorder_id Salesorder ID
	 */
	function createRecurringInvoiceFromSO(){
		global $adb;
		$salesorder_id = $this->_salesorderid;
		$query1 = "SELECT * FROM vtiger_inventoryproductrel WHERE id=?";
		$res = $adb->pquery($query1, array($salesorder_id));
		$no_of_products = $adb->num_rows($res);
		$fieldsList = $adb->getFieldsArray($res);
		$update_stock = array();
		for($j=0; $j<$no_of_products; $j++) {
			$row = $adb->query_result_rowdata($res, $j);
			$col_value = array();
			for($k=0; $k<count($fieldsList); $k++) {
                            //Here we check if field is different form lineitem_id (original) and
                            //field is different from discount_amount or is equal and not empty (null) and
                            //field is different from discount_percent or is equal and not empty (null)
                            //to prevent fails when discount percent is null (resulting invoice have discount_percent as 0 and 
                            //don't do any discount)
				if($fieldsList[$k]!='lineitem_id' &&
                                    ($fieldsList[$k]!='discount_amount' || ($fieldsList[$k]=='discount_amount' && !empty($row[$fieldsList[$k]]))) &&
                                    ($fieldsList[$k]!='discount_percent' || ($fieldsList[$k]=='discount_percent' && !empty($row[$fieldsList[$k]])))){
					$col_value[$fieldsList[$k]] = $row[$fieldsList[$k]];
				}
			}
			if(count($col_value) > 0) {
				$col_value['id'] = $this->id;
				$col_value['comment']= decode_html($col_value['comment']);
				$columns = array_keys($col_value);
				$values = array_values($col_value);
				$query2 = "INSERT INTO vtiger_inventoryproductrel(". implode(",",$columns) .") VALUES (". generateQuestionMarks($values) .")";
				$adb->pquery($query2, array($values));
				$prod_id = $col_value['productid'];
				$qty = $col_value['quantity'];
				$update_stock[$col_value['sequence_no']] = $qty;
				updateStk($prod_id,$qty,'',array(),'Invoice');
			}
		}

		$query1 = "SELECT * FROM vtiger_inventorysubproductrel WHERE id=?";
		$res = $adb->pquery($query1, array($salesorder_id));
		$no_of_products = $adb->num_rows($res);
		$fieldsList = $adb->getFieldsArray($res);
		for($j=0; $j<$no_of_products; $j++) {
			$row = $adb->query_result_rowdata($res, $j);
			$col_value = array();
			for($k=0; $k<count($fieldsList); $k++) {
					$col_value[$fieldsList[$k]] = $row[$fieldsList[$k]];
			}
			if(count($col_value) > 0) {
				$col_value['id'] = $this->id;
				$columns = array_keys($col_value);
				$values = array_values($col_value);
				$query2 = "INSERT INTO vtiger_inventorysubproductrel(". implode(",",$columns) .") VALUES (". generateQuestionMarks($values) .")";
				$adb->pquery($query2, array($values));
				$prod_id = $col_value['productid'];
				$qty = $update_stock[$col_value['sequence_no']];
				updateStk($prod_id,$qty,'',array(),'Invoice');
			}
		}

		// Add the Shipping taxes for the Invoice
		$query3 = "SELECT * FROM vtiger_inventoryshippingrel WHERE id=?";
		$res = $adb->pquery($query3, array($salesorder_id));
		$no_of_shippingtax = $adb->num_rows($res);
		$fieldsList = $adb->getFieldsArray($res);
		for($j=0; $j<$no_of_shippingtax; $j++) {
			$row = $adb->query_result_rowdata($res, $j);
			$col_value = array();
			for($k=0; $k<count($fieldsList); $k++) {
				$col_value[$fieldsList[$k]] = $row[$fieldsList[$k]];
			}
			if(count($col_value) > 0) {
				$col_value['id'] = $this->id;
				$columns = array_keys($col_value);
				$values = array_values($col_value);
				$query4 = "INSERT INTO vtiger_inventoryshippingrel(". implode(",",$columns) .") VALUES (". generateQuestionMarks($values) .")";
				$adb->pquery($query4, array($values));
			}
		}

		//Update the netprice (subtotal), taxtype, discount, S&H charge, adjustment and total for the Invoice
		$updatequery = " UPDATE vtiger_invoice SET ";
		$updateparams = array();
		// Remaining column values to be updated -> column name to field name mapping
		$invoice_column_field = Array (
			'adjustment' => 'txtAdjustment',
			'subtotal' => 'hdnSubTotal',
			'total' => 'hdnGrandTotal',
			'taxtype' => 'hdnTaxType',
			'discount_percent' => 'hdnDiscountPercent',
			'discount_amount' => 'hdnDiscountAmount',
			's_h_amount' => 'hdnS_H_Amount',
		);
		$updatecols = array();
		foreach($invoice_column_field as $col => $field) {
			$updatecols[] = "$col=?";
			$updateparams[] = $this->column_fields[$field];
		}
		if (count($updatecols) > 0) {
			$updatequery .= implode(",", $updatecols);

			$updatequery .= " WHERE invoiceid=?";
			array_push($updateparams, $this->id);

			$adb->pquery($updatequery, $updateparams);
		}
	}

	/*Function to create records in current module.
	**This function called while importing records to this module*/
	function createRecords($obj) {
		$createRecords = createRecords($obj);
		return $createRecords;
	}

	/*Function returns the record information which means whether the record is imported or not
	**This function called while importing records to this module*/
	function importRecord($obj, $inventoryFieldData, $lineItemDetails) {
		$entityInfo = importRecord($obj, $inventoryFieldData, $lineItemDetails);
		return $entityInfo;
	}

	/*Function to return the status count of imported records in current module.
	**This function called while importing records to this module*/
	function getImportStatusCount($obj) {
		$statusCount = getImportStatusCount($obj);
		return $statusCount;
	}

	function undoLastImport($obj, $user) {
		$undoLastImport = undoLastImport($obj, $user);
	}

	/** Function to export the lead records in CSV Format
	* @param reference variable - where condition is passed when the query is executed
	* Returns Export Invoice Query.
	*/
	function create_export_query($where) {
		global $log, $current_user;
		$log->debug("Entering create_export_query(".$where.") method ...");

		include("include/utils/ExportUtils.php");

		//To get the Permitted fields query and the permitted fields list
		$sql = getPermittedFieldsQuery("Invoice", "detail_view");
		$fields_list = getFieldsListFromQuery($sql);
		$fields_list .= getInventoryFieldsForExport($this->table_name);
		$userNameSql = getSqlForNameInDisplayFormat(array('first_name'=>'vtiger_users.first_name', 'last_name' => 'vtiger_users.last_name'), 'Users');

		$query = "SELECT $fields_list FROM ".$this->entity_table."
				INNER JOIN vtiger_invoice ON vtiger_invoice.invoiceid = vtiger_crmentity.crmid
				LEFT JOIN vtiger_invoicecf ON vtiger_invoicecf.invoiceid = vtiger_invoice.invoiceid
				LEFT JOIN vtiger_salesorder ON vtiger_salesorder.salesorderid = vtiger_invoice.salesorderid
				LEFT JOIN vtiger_invoicebillads ON vtiger_invoicebillads.invoicebilladdressid = vtiger_invoice.invoiceid
				LEFT JOIN vtiger_invoiceshipads ON vtiger_invoiceshipads.invoiceshipaddressid = vtiger_invoice.invoiceid
				LEFT JOIN vtiger_inventoryproductrel ON vtiger_inventoryproductrel.id = vtiger_invoice.invoiceid
				LEFT JOIN vtiger_products ON vtiger_products.productid = vtiger_inventoryproductrel.productid
				LEFT JOIN vtiger_service ON vtiger_service.serviceid = vtiger_inventoryproductrel.productid
				LEFT JOIN vtiger_contactdetails ON vtiger_contactdetails.contactid = vtiger_invoice.contactid
				LEFT JOIN vtiger_account ON vtiger_account.accountid = vtiger_invoice.accountid
				LEFT JOIN vtiger_currency_info ON vtiger_currency_info.id = vtiger_invoice.currency_id
				LEFT JOIN vtiger_groups ON vtiger_groups.groupid = vtiger_crmentity.smownerid
				LEFT JOIN vtiger_users ON vtiger_users.id = vtiger_crmentity.smownerid";

		$query .= $this->getNonAdminAccessControlQuery('Invoice',$current_user);
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
