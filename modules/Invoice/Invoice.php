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
require_once 'modules/InventoryDetails/InventoryDetails.php';
include_once 'include/Webservices/Revise.php';

class Invoice extends CRMEntity {
	public $table_name = 'vtiger_invoice';
	public $table_index= 'invoiceid';
	public $column_fields = array();

	/** Indicator if this is a custom module or standard module */
	public $IsCustomModule = false;
	public $HasDirectImageField = false;
	public $moduleIcon = array('library' => 'standard', 'containerClass' => 'slds-icon_container slds-icon-standard-contract', 'class' => 'slds-icon', 'icon'=>'contract');

	/**
	 * Mandatory table for supporting custom fields.
	 */
	public $customFieldTable = array('vtiger_invoicecf', 'invoiceid');

	public $tab_name = array('vtiger_crmentity','vtiger_invoice','vtiger_invoicebillads','vtiger_invoiceshipads','vtiger_invoicecf');

	public $tab_name_index = array(
		'vtiger_crmentity' => 'crmid',
		'vtiger_invoice'=>'invoiceid',
		'vtiger_invoicebillads'=>'invoicebilladdressid',
		'vtiger_invoiceshipads'=>'invoiceshipaddressid',
		'vtiger_invoicecf'=>'invoiceid'
	);

	public $list_fields = array(
		//'Invoice No'=>array('crmentity'=>'crmid'),
		'Invoice No'=>array('invoice'=>'invoice_no'),
		'Subject'=>array('invoice'=>'subject'),
		'Sales Order'=>array('invoice'=>'salesorderid'),
		'Status'=>array('invoice'=>'invoicestatus'),
		'Total'=>array('invoice'=>'total'),
		'Account Name'=>array('invoice'=>'account_id'),
		'Assigned To'=>array('crmentity'=>'smownerid')
	);
	public $list_fields_name = array(
		'Invoice No'=>'invoice_no',
		'Subject'=>'subject',
		'Sales Order'=>'salesorder_id',
		'Status'=>'invoicestatus',
		'Total'=>'hdnGrandTotal',
		'Account Name'=>'account_id',
		'Assigned To'=>'assigned_user_id'
	);
	public $list_link_field= 'subject';

	public $search_fields = array(
		//'Invoice No'=>array('crmentity'=>'crmid'),
		'Invoice No'=>array('invoice'=>'invoice_no'),
		'Subject'=>array('purchaseorder'=>'subject'),
		'Account Name'=>array('invoice'=>'account_id'),
	);
	public $search_fields_name = array(
		'Invoice No'=>'invoice_no',
		'Subject'=>'subject',
		'Account Name'=>'account_id',
	);

	// For Popup window record selection
	public $popup_fields = array('subject');

	// Placeholder for sort fields - All the fields will be initialized for Sorting through initSortFields
	public $sortby_fields = array('subject','invoice_no','invoicestatus','smownerid','accountname','lastname');

	// For Alphabetical search
	public $def_basicsearch_col = 'subject';

	// Column value to use on detail view record text display
	public $def_detailview_recname = 'subject';

	public $required_fields = array('accountname'=>1);

	//Added these variables which are used as default order by and sortorder in ListView
	public $default_order_by = 'crmid';
	public $default_sort_order = 'ASC';
	public $mandatory_fields = array('subject','createdtime' ,'modifiedtime');
	public $_salesorderid;
	public $_recurring_mode;
	public $record_status = '';
	public $update_product_array = array();

	public function save($module, $fileid = '') {
		if ($this->mode=='edit') {
			$this->record_status = getSingleFieldValue($this->table_name, 'invoicestatus', $this->table_index, $this->id);
		}
		parent::save($module, $fileid);
	}

	public function save_module($module) {
		global $updateInventoryProductRel_deduct_stock, $current_user;
		if ($this->HasDirectImageField) {
			$this->insertIntoAttachment($this->id, $module);
		}
		if ($this->mode=='edit' && !empty($this->record_status) && $this->record_status!=$this->column_fields['invoicestatus'] && $this->column_fields['invoicestatus']!='') {
			$this->registerInventoryHistory();
		}
		//Checking if salesorderid is present and updating the SO status
		if (!empty($this->column_fields['salesorder_id'])) {
			$newStatus = GlobalVariable::getVariable('SalesOrder_StatusOnInvoiceSave', 'Approved');
			if ($newStatus!='DoNotChange') {
				$h = isset($_REQUEST['ajxaction']) ? $_REQUEST['ajxaction'] : 'NOTSET';
				$_REQUEST['ajxaction'] = 'Workflow';
				try {
					vtws_revise(array('id'=>vtws_getEntityId('SalesOrder').'x'.$this->column_fields['salesorder_id'], 'sostatus'=>$newStatus), $current_user);
				} catch (\Throwable $th) {
				}
				if ($h=='NOTSET') {
					unset($_REQUEST['ajxaction']);
				} else {
					$_REQUEST['ajxaction'] = $h;
				}
			}
		}

		//in ajax save we should not call this function, because this will delete all the existing product values
		$updateInventoryProductRel_deduct_stock = true;
		if (isset($this->_recurring_mode) && $this->_recurring_mode == 'recurringinvoice_from_so' && isset($this->_salesorderid) && $this->_salesorderid!='') {
			// We are getting called from the RecurringInvoice cron service!
			$this->createRecurringInvoiceFromSO();
			if (vtlib_isModuleActive('InventoryDetails')) {
				InventoryDetails::createInventoryDetails($this, 'Invoice');
			}
		} elseif (isset($_REQUEST)) {
			if (inventoryCanSaveProductLines($_REQUEST, 'Invoice')) {
				//Based on the total Number of rows we will save the product relationship with this entity
				saveInventoryProductDetails($this, 'Invoice');
				if (vtlib_isModuleActive('InventoryDetails')) {
					InventoryDetails::createInventoryDetails($this, 'Invoice');
				}
			} elseif ($_REQUEST['action'] == 'InvoiceAjax' || $_REQUEST['action'] == 'MassEditSave') {
				$updateInventoryProductRel_deduct_stock = false;
			}
		}

		// Update due amount
		self::updateAmountDue($this->id, $this->column_fields, $module);
	}

	public function registerInventoryHistory() {
		global $app_strings;
		if (GlobalVariable::getVariable('Application_B2B', '1')) {
			if (!empty($this->column_fields['account_id'])) {
				$relatedname = getAccountName($this->column_fields['account_id']);
			} else {
				$relatedname = getAccountName(getSingleFieldValue($this->table_name, 'accountid', $this->table_index, $this->id));
			}
		} else {
			if (!empty($this->column_fields['contact_id'])) {
				$relatedname = getContactName($this->column_fields['contact_id']);
			} else {
				$relatedname = getContactName(getSingleFieldValue($this->table_name, 'contactid', $this->table_index, $this->id));
			}
		}
		$total = getSingleFieldValue($this->table_name, 'total', $this->table_index, $this->id);
		if ($this->column_fields['invoicestatus'] == $app_strings['LBL_NOT_ACCESSIBLE']) {
			//If the value in the request is Not Accessible for a picklist, the existing value will be replaced instead of Not Accessible value.
			$stat_value = getSingleFieldValue($this->table_name, 'invoicestatus', $this->table_index, $this->id);
		} else {
			$stat_value = $this->column_fields['invoicestatus'];
		}
		addInventoryHistory(get_class($this), $this->id, $relatedname, $total, $stat_value);
	}

	/**
	 * Customizing the restore procedure.
	 */
	public function restore($module, $id) {
		global $adb, $updateInventoryProductRel_deduct_stock;
		$result = $adb->pquery('SELECT invoicestatus FROM vtiger_invoice where invoiceid=?', array($id));
		$invoiceStatus = $adb->query_result($result, 0, 'invoicestatus');
		if ($invoiceStatus != 'Cancel') {
			$updateInventoryProductRel_deduct_stock = true;
		}
		parent::restore($module, $id);
	}

	/**
	 * Customizing the Delete procedure.
	 */
	public function trash($module, $recordId) {
		global $adb;
		$result = $adb->pquery('SELECT invoicestatus FROM vtiger_invoice where invoiceid=?', array($recordId));
		$invoiceStatus = $adb->query_result($result, 0, 'invoicestatus');
		if ($invoiceStatus != 'Cancel') {
			addProductsToStock($recordId);
		}
		parent::trash($module, $recordId);
	}

	/**	Function used to get the Status history of the Invoice
	 *	@param $id - invoice id
	 *	@return $return_data - array with header and the entries in format array('header'=>$header,'entries'=>$entries_list)
	 *		where as $header and $entries_list are arrays which contains header values and all column values of all entries
	 */
	public function get_invoicestatushistory($id) {
		global $log, $adb, $app_strings, $current_user;
		$log->debug('> get_invoicestatushistory '.$id);
		$crmEntityTable = $this->denormalized ? $this->crmentityTable.' as vtiger_crmentity' : 'vtiger_crmentity';
		$query = 'select vtiger_invoicestatushistory.*, vtiger_invoice.invoice_no
			from vtiger_invoicestatushistory
			inner join vtiger_invoice on vtiger_invoice.invoiceid = vtiger_invoicestatushistory.invoiceid
			inner join '.$crmEntityTable.' on vtiger_crmentity.crmid = vtiger_invoice.invoiceid
			where vtiger_crmentity.deleted = 0 and vtiger_invoice.invoiceid = ?';
		$result=$adb->pquery($query, array($id));
		$header = array();
		$header[] = $app_strings['Invoice No'];
		$header[] = $app_strings['LBL_ACCOUNT_NAME'];
		$header[] = $app_strings['LBL_AMOUNT'];
		$header[] = $app_strings['LBL_INVOICE_STATUS'];
		$header[] = $app_strings['LBL_LAST_MODIFIED'];

		//Getting the field permission for the current user. 1 - Not Accessible, 0 - Accessible
		//Account Name , Amount are mandatory fields. So no need to do security check to these fields.

		//If field is accessible then getFieldVisibilityPermission function will return 0 else return 1
		$invoicestatus_access = (getFieldVisibilityPermission('Invoice', $current_user->id, 'invoicestatus') != '0')? 1 : 0;
		$picklistarray = getAccessPickListValues('Invoice');

		$invoicestatus_array = ($invoicestatus_access != 1)? $picklistarray['invoicestatus']: array();
		//- ==> picklist field is not permitted in profile
		//Not Accessible - picklist is permitted in profile but picklist value is not permitted
		$error_msg = ($invoicestatus_access != 1)? getTranslatedString('LBL_NOT_ACCESSIBLE'): '-';
		$entries_list = array();
		while ($row = $adb->fetch_array($result)) {
			$entries = array();

			$entries[] = $row['invoice_no'];
			$entries[] = $row['accountname'];
			$total = new CurrencyField($row['total']);
			$entries[] = $total->getDisplayValueWithSymbol($current_user);
			$entries[] = (in_array($row['invoicestatus'], $invoicestatus_array))? getTranslatedString($row['invoicestatus'], 'Invoice'): $error_msg;
			$entries[] = DateTimeField::convertToUserFormat($row['lastmodified']);

			$entries_list[] = $entries;
		}

		$return_data = array('header'=>$header,'entries'=>$entries_list,'navigation'=>array('',''));
		$log->debug('< get_invoicestatushistory');
		return $return_data;
	}

	// Function to get column name - Overriding function of base class
	public function get_column_value($columname, $fldvalue, $fieldname, $uitype, $datatype = '') {
		if ($columname == 'salesorderid' && $fldvalue == '') {
			return null;
		}
		return parent::get_column_value($columname, $fldvalue, $fieldname, $uitype, $datatype);
	}

	/*
	 * Function to get the secondary query part of a report
	 * @param - $module primary module name
	 * @param - $secmodule secondary module name
	 * returns the query string formed on fetching the related data for report for secondary module
	 */
	public function generateReportsSecQuery($module, $secmodule, $queryPlanner, $type = '', $where_condition = '') {
		// Define the dependency matrix ahead
		$matrix = $queryPlanner->newDependencyMatrix();
		$matrix->setDependency('vtiger_crmentityInvoice', array('vtiger_usersInvoice', 'vtiger_groupsInvoice', 'vtiger_lastModifiedByInvoice'));
		$matrix->setDependency('vtiger_inventoryproductrelInvoice', array('vtiger_productsInvoice', 'vtiger_serviceInvoice'));

		if (!$queryPlanner->requireTable('vtiger_invoice', $matrix) && !$queryPlanner->requireTable('vtiger_invoicecf', $matrix)) {
			return '';
		}
		$matrix->setDependency('vtiger_invoice', array('vtiger_crmentityInvoice', "vtiger_currency_info$secmodule",
				'vtiger_invoicecf', 'vtiger_salesorderInvoice', 'vtiger_invoicebillads',
				'vtiger_invoiceshipads', 'vtiger_inventoryproductrelInvoice', 'vtiger_contactdetailsInvoice', 'vtiger_accountInvoice'));
		$query = parent::generateReportsSecQuery($module, $secmodule, $queryPlanner, $type, $where_condition);
		if ($queryPlanner->requireTable("vtiger_currency_info$secmodule")) {
			$query .= " left join vtiger_currency_info as vtiger_currency_info$secmodule on vtiger_currency_info$secmodule.id = vtiger_invoice.currency_id";
		}
		if ($queryPlanner->requireTable('vtiger_salesorderInvoice')) {
			$query .= ' left join vtiger_salesorder as vtiger_salesorderInvoice on vtiger_salesorderInvoice.salesorderid=vtiger_invoice.salesorderid';
		}
		if ($queryPlanner->requireTable('vtiger_invoicebillads')) {
			$query .= ' left join vtiger_invoicebillads on vtiger_invoice.invoiceid=vtiger_invoicebillads.invoicebilladdressid';
		}
		if ($queryPlanner->requireTable('vtiger_invoiceshipads')) {
			$query .= ' left join vtiger_invoiceshipads on vtiger_invoice.invoiceid=vtiger_invoiceshipads.invoiceshipaddressid';
		}
		if (($type !== 'COLUMNSTOTOTAL') || ($type == 'COLUMNSTOTOTAL' && $where_condition == 'add')) {
			if ($queryPlanner->requireTable('vtiger_inventoryproductrelInvoice', $matrix)) {
				if ($module == 'Products') {
					$query .= ' left join vtiger_inventoryproductrel as vtiger_inventoryproductrelInvoice on'.
						' vtiger_invoice.invoiceid = vtiger_inventoryproductrelInvoice.id and vtiger_inventoryproductrelInvoice.productid=vtiger_products.productid ';
				} elseif ($module == 'Services') {
					$query .= ' left join vtiger_inventoryproductrel as vtiger_inventoryproductrelInvoice on'.
						' vtiger_invoice.invoiceid = vtiger_inventoryproductrelInvoice.id and vtiger_inventoryproductrelInvoice.productid=vtiger_service.serviceid ';
				} else {
					$query .= ' left join vtiger_inventoryproductrel as vtiger_inventoryproductrelInvoice on'.
						' vtiger_invoice.invoiceid = vtiger_inventoryproductrelInvoice.id ';
				}
			}
			if ($queryPlanner->requireTable('vtiger_productsInvoice')) {
				$query .= ' left join vtiger_products as vtiger_productsInvoice on vtiger_productsInvoice.productid = vtiger_inventoryproductrelInvoice.productid';
			}
			if ($queryPlanner->requireTable('vtiger_serviceInvoice')) {
				$query .= ' left join vtiger_service as vtiger_serviceInvoice on vtiger_serviceInvoice.serviceid = vtiger_inventoryproductrelInvoice.productid';
			}
		}
		if ($queryPlanner->requireTable('vtiger_contactdetailsInvoice')) {
			$query .= ' left join vtiger_contactdetails as vtiger_contactdetailsInvoice on vtiger_invoice.contactid = vtiger_contactdetailsInvoice.contactid';
		}
		if ($queryPlanner->requireTable('vtiger_accountInvoice')) {
			$query .= ' left join vtiger_account as vtiger_accountInvoice on vtiger_accountInvoice.accountid = vtiger_invoice.accountid';
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
			'Documents' => array('vtiger_senotesrel'=>array('crmid','notesid'),'vtiger_invoice'=>'invoiceid'),
			'Accounts' => array('vtiger_invoice'=>array('invoiceid','accountid')),
			'Contacts' => array('vtiger_invoice'=>array('invoiceid','contactid')),
		);
		return isset($rel_tables[$secmodule]) ? $rel_tables[$secmodule] : '';
	}

	// Function to unlink an entity with given Id from another entity
	public function unlinkRelationship($id, $return_module, $return_id) {
		global $adb;
		if (empty($return_module) || empty($return_id)) {
			return;
		}
		$customRelModules = ['Accounts', 'Contacts', 'SalesOrder', 'Documents'];
		if (in_array($return_module, $customRelModules)) {
			$data = array();
			$data['sourceModule'] = getSalesEntityType($id);
			$data['sourceRecordId'] = $id;
			$data['destinationModule'] = $return_module;
			$data['destinationRecordId'] = $return_id;
			cbEventHandler::do_action('corebos.entity.link.delete', $data);
		}
		if ($return_module == 'Accounts' || $return_module == 'Contacts') {
			$this->trash('Invoice', $id);
		} elseif ($return_module=='SalesOrder') {
			$relation_query = 'UPDATE vtiger_invoice set salesorderid=? where invoiceid=?';
			$adb->pquery($relation_query, array(null,$id));
		} elseif ($return_module == 'Documents') {
			$sql = 'DELETE FROM vtiger_senotesrel WHERE crmid=? AND notesid=?';
			$adb->pquery($sql, array($id, $return_id));
		} else {
			parent::unlinkRelationship($id, $return_module, $return_id);
		}
		if (in_array($return_module, $customRelModules)) {
			cbEventHandler::do_action('corebos.entity.link.delete.final', $data);
		}
	}

	/**
	 * Function to get the relations of salesorder to invoice for recurring invoice procedure
	 * @param integer $_salesorderid Salesorder ID through object property
	 */
	public function createRecurringInvoiceFromSO() {
		global $adb;
		$salesorder_id = $this->_salesorderid;
		$res = $adb->pquery('SELECT * FROM vtiger_inventoryproductrel WHERE id=?', array($salesorder_id));
		$no_of_products = $adb->num_rows($res);
		//To permit Saving InventoryLines && save FinancialInfo
		$_REQUEST['totalProductCount'] = $no_of_products;
		$fieldsList = $adb->getFieldsArray($res);
		$update_stock = array();
		for ($j=0; $j<$no_of_products; $j++) {
			$row = $adb->query_result_rowdata($res, $j);
			$col_value = array();
			for ($k=0; $k<count($fieldsList); $k++) {
				//Here we check if field is different form lineitem_id (original) and
				//field is different from discount_amount or is equal and not empty (null) and
				//field is different from discount_percent or is equal and not empty (null)
				//to prevent fails when discount percent is null (resulting invoice have discount_percent as 0 and
				//don't do any discount)
				if ($fieldsList[$k]!='lineitem_id' &&
					($fieldsList[$k]!='discount_amount' || ($fieldsList[$k]=='discount_amount' && !empty($row[$fieldsList[$k]]))) &&
					($fieldsList[$k]!='discount_percent' || ($fieldsList[$k]=='discount_percent' && !empty($row[$fieldsList[$k]]))) &&
					(substr($fieldsList[$k], 0, 3) != 'tax' || (substr($fieldsList[$k], 0, 3) == 'tax' && !empty($row[$fieldsList[$k]])))
				) {
					$col_value[$fieldsList[$k]] = $row[$fieldsList[$k]];
				}
			}
			if (!empty($col_value)) {
				$col_value['id'] = $this->id;
				$col_value['comment']= decode_html($col_value['comment']);
				$columns = array_keys($col_value);
				$values = array_values($col_value);
				$query2 = 'INSERT INTO vtiger_inventoryproductrel('. implode(',', $columns) .') VALUES ('. generateQuestionMarks($values) .')';
				$adb->pquery($query2, array($values));
				$prod_id = $col_value['productid'];
				$qty = $col_value['quantity'];
				$update_stock[$col_value['sequence_no']] = $qty;
			}
		}

		$res = $adb->pquery('SELECT * FROM vtiger_inventorysubproductrel WHERE id=?', array($salesorder_id));
		$no_of_products = $adb->num_rows($res);
		$fieldsList = $adb->getFieldsArray($res);
		for ($j=0; $j<$no_of_products; $j++) {
			$row = $adb->query_result_rowdata($res, $j);
			$col_value = array();
			for ($k=0; $k<count($fieldsList); $k++) {
				$col_value[$fieldsList[$k]] = $row[$fieldsList[$k]];
			}
			if (!empty($col_value)) {
				$col_value['id'] = $this->id;
				$columns = array_keys($col_value);
				$values = array_values($col_value);
				$query2 = 'INSERT INTO vtiger_inventorysubproductrel('. implode(',', $columns) .') VALUES ('. generateQuestionMarks($values) .')';
				$adb->pquery($query2, array($values));
				$prod_id = $col_value['productid'];
				$qty = $update_stock[$col_value['sequence_no']];
			}
		}

		// Add the Shipping taxes for the Invoice
		$query3 = 'SELECT * FROM vtiger_inventoryshippingrel WHERE id=?';
		$res = $adb->pquery($query3, array($salesorder_id));
		$no_of_shippingtax = $adb->num_rows($res);
		$fieldsList = $adb->getFieldsArray($res);
		for ($j=0; $j<$no_of_shippingtax; $j++) {
			$row = $adb->query_result_rowdata($res, $j);
			$col_value = array();
			for ($k=0; $k<count($fieldsList); $k++) {
				if (!empty($row[$fieldsList[$k]])) {
					$col_value[$fieldsList[$k]] = $row[$fieldsList[$k]];
				}
			}
			if (!empty($col_value)) {
				$col_value['id'] = $this->id;
				$columns = array_keys($col_value);
				$values = array_values($col_value);
				$query4 = 'INSERT INTO vtiger_inventoryshippingrel('. implode(',', $columns) .') VALUES ('. generateQuestionMarks($values) .')';
				$adb->pquery($query4, array($values));
			}
		}

		//Update the netprice (subtotal), taxtype, discount, S&H charge, adjustment and total for the Invoice
		$updatequery = ' UPDATE vtiger_invoice SET ';
		$updateparams = array();
		// Remaining column values to be updated -> column name to field name mapping
		$invoice_column_field = array(
			'adjustment' => 'txtAdjustment',
			'subtotal' => 'hdnSubTotal',
			'total' => 'hdnGrandTotal',
			'taxtype' => 'hdnTaxType',
			'discount_percent' => 'hdnDiscountPercent',
			'discount_amount' => 'hdnDiscountAmount',
			's_h_amount' => 'hdnS_H_Amount',
		);
		$updatecols = array();
		foreach ($invoice_column_field as $col => $field) {
			$updatecols[] = "$col=?";
			$updateparams[] = $this->column_fields[$field];
		}
		if (!empty($updatecols)) {
			$updatequery .= implode(',', $updatecols);

			$updatequery .= ' WHERE invoiceid=?';
			$updateparams[] = $this->id;

			$adb->pquery($updatequery, $updateparams);
		}
	}

	public static function updateAmountDue($invoiceId, &$column_fields, $context) {
		global $adb;
		if (!vtlib_isModuleActive('CobroPago')) {
			return true;
		}
		include_once 'vtlib/Vtiger/Module.php';
		if ($context=='CobroPago') {
			$em = new VTEventsManager($adb);
			// Initialize Event trigger cache
			$em->initTriggerCache();
			$entityData = VTEntityData::fromEntityId($adb, $invoiceId);
			//Event triggering code
			$em->triggerEvent('vtiger.entity.beforesave', $entityData);
		}
		$type = getSalesEntityType($invoiceId);
		$crmEntityTable = CRMEntity::getcrmEntityTableAlias('CobroPago', true);
		if ($type=='Invoice' && self::invoice_control_installed()) {
			//Sum Credit = 1
			$query = "select sum(amount)
			from vtiger_cobropago cp
			join ".$crmEntityTable." crm_cp on crm_cp.crmid=cp.cobropagoid and crm_cp.deleted=0
			where cp.related_id={$invoiceId} and paid=1 and credit=1";
			$res = $adb->query($query);
			$totalCredit = $adb->query_result($res, 0, 0);
			if ($totalCredit=='') {
				$totalCredit = 0;
			}
			//Sum Credit = 0
			$query = "select sum(amount)
			from vtiger_cobropago cp
			join ".$crmEntityTable." crm_cp on crm_cp.crmid=cp.cobropagoid and crm_cp.deleted=0
			where cp.related_id={$invoiceId} and paid=1 and credit=0";
			$res = $adb->query($query);
			$totalNoCredit = $adb->query_result($res, 0, 0);
			if ($totalNoCredit=='') {
				$totalNoCredit = 0;
			}
			$totalPaid = $totalCredit - $totalNoCredit;

			$query = "select i.total, i.salesorderid, so.quoteid
			from vtiger_invoice i
			left join vtiger_salesorder so on so.salesorderid=i.salesorderid
			where i.invoiceid={$invoiceId}";
			$res = $adb->query($query);
			$totalDue = $adb->query_result($res, 0, 'total');
			$salesorderId = $adb->query_result($res, 0, 'salesorderid');
			$quoteId = $adb->query_result($res, 0, 'quoteid');
			$amountDue = $totalDue-$totalPaid;
			if ($amountDue<=0) {
				$newStatus = GlobalVariable::getVariable('CobroPago_Invoice_Status_OnPaid', 'DoNotChange');
				if ($newStatus!='DoNotChange') {
					$query = "update vtiger_invoice set invoicestatus='{$newStatus}' where invoiceid={$invoiceId}";
					$adb->query($query);
					if ($context=='Invoice') {
						$column_fields['invoicestatus'] = $newStatus;
					}
				}
			}
			$query = "update vtiger_invoice set amount_due={$amountDue}, amount_paid={$totalPaid}, total_amount=total where invoiceid={$invoiceId}";
			$adb->query($query);
			if ($context=='Invoice') {
				$column_fields['amount_due'] = $amountDue;
				$column_fields['amount_paid'] = $totalPaid;
				$column_fields['total_amount'] = $totalDue;
			}
		}
		if ($type =='PurchaseOrder' && self::po_control_installed()) {
			//Sum Credit = 0
			$query = "select sum(amount) as am
			from vtiger_cobropago cp
			join ".$crmEntityTable." crm_cp on crm_cp.crmid=cp.cobropagoid and crm_cp.deleted=0
			where cp.related_id={$invoiceId} and paid=1 and credit=0";
			$res = $adb->query($query);
			$totalNoCredit = $adb->query_result($res, 0, 0);
			if ($totalNoCredit=='') {
				$totalNoCredit = 0;
			}
			//Sum Credit = 1
			$query = "select sum(amount)
			from vtiger_cobropago cp
			join ".$crmEntityTable." crm_cp on crm_cp.crmid=cp.cobropagoid and crm_cp.deleted=0
			where cp.related_id={$invoiceId} and paid=1 and credit=1";
			$res = $adb->query($query);
			$totalCredit = $adb->query_result($res, 0, 0);
			if ($totalCredit=='') {
				$totalCredit = 0;
			}

			$totalPaid = $totalNoCredit - $totalCredit;

			$query = "select po.total
			from vtiger_purchaseorder po
			where po.purchaseorderid={$invoiceId}";
			$res = $adb->query($query);
			$totalDue = $adb->query_result($res, 0, 'total');
			$amountDue = $totalDue-$totalPaid;
			if ($amountDue<=0) {
				$newStatus = GlobalVariable::getVariable('CobroPago_PurchaseOrder_Status_OnPaid', 'DoNotChange');
				if ($newStatus!='DoNotChange') {
					$query = "update vtiger_purchaseorder set postatus='{$newStatus}' where purchaseorderid={$invoiceId}";
					$adb->query($query);
					if ($context=='PurchaseOrder') {
						$column_fields['postatus'] = $newStatus;
					}
				}
			}
			$query = "update vtiger_purchaseorder set amount_due={$amountDue}, amount_paid={$totalPaid}, total_amount=total where  purchaseorderid={$invoiceId}";
			$adb->query($query);
			if ($context=='PurchaseOrder') {
				$column_fields['amount_due'] = $amountDue;
				$column_fields['amount_paid'] = $totalPaid;
				$column_fields['total_amount'] = $totalDue;
			}
		}
		if ($type=='SalesOrder' && self::so_control_installed()) {
			//Sum Credit = 1
			$query = "select sum(amount)
			from vtiger_cobropago cp
			join ".$crmEntityTable." crm_cp on crm_cp.crmid=cp.cobropagoid and crm_cp.deleted=0
			where cp.related_id={$invoiceId} and paid=1 and credit=1";
			$res = $adb->query($query);
			$totalCredit = $adb->query_result($res, 0, 0);
			if ($totalCredit=='') {
				$totalCredit = 0;
			}
			//Sum Credit = 0
			$query = "select sum(amount)
			from vtiger_cobropago cp
			join ".$crmEntityTable." crm_cp on crm_cp.crmid=cp.cobropagoid and crm_cp.deleted=0
			where cp.related_id={$invoiceId} and paid=1 and credit=0";
			$res = $adb->query($query);
			$totalNoCredit = $adb->query_result($res, 0, 0);
			if ($totalNoCredit=='') {
				$totalNoCredit = 0;
			}
			$totalPaid = $totalCredit - $totalNoCredit;
			$query = "select i.total, i.quoteid
			from vtiger_salesorder i
			where i.salesorderid={$invoiceId}";
			$res = $adb->query($query);
			$totalDue = $adb->query_result($res, 0, 'total');
			$quoteId = $adb->query_result($res, 0, 'quoteid');
			$amountDue = $totalDue-$totalPaid;
			if ($amountDue<=0) {
				$newStatus = GlobalVariable::getVariable('CobroPago_SalesOrder_Status_OnPaid', 'DoNotChange');
				if ($newStatus!='DoNotChange') {
					$query = "update vtiger_salesorder set sostatus='{$newStatus}' where salesorderid={$invoiceId}";
					$adb->query($query);
					if ($context=='SalesOrder') {
						$column_fields['sostatus'] = $newStatus;
					}
				}
			}
			$query = "update vtiger_salesorder set amount_due={$amountDue}, amount_paid={$totalPaid}, total_amount=total where salesorderid={$invoiceId}";
			$adb->query($query);
			if ($context=='SalesOrder') {
				$column_fields['amount_due'] = $amountDue;
				$column_fields['amount_paid'] = $totalPaid;
				$column_fields['total_amount'] = $totalDue;
			}
		}
		if ($context=='CobroPago') {
			//Event triggering code
			$em->triggerEvent('vtiger.entity.aftersave', $entityData);
			$em->triggerEvent('vtiger.entity.aftersave.final', $entityData);
			//Event triggering code ends
		}
	}
	public static function invoice_control_installed() {
		global $adb;
		$cninv=$adb->getColumnNames('vtiger_invoice');
		if (in_array('amount_due', $cninv) && in_array('amount_paid', $cninv) && in_array('total_amount', $cninv)) {
			return true;
		}
		return false;
	}
	public static function po_control_installed() {
		global $adb;
		$cninv=$adb->getColumnNames('vtiger_purchaseorder');
		if (in_array('amount_due', $cninv) && in_array('amount_paid', $cninv) && in_array('total_amount', $cninv)) {
			return true;
		}
		return false;
	}
	public static function so_control_installed() {
		global $adb;
		$cninv=$adb->getColumnNames('vtiger_salesorder');
		if (in_array('amount_due', $cninv) && in_array('amount_paid', $cninv) && in_array('total_amount', $cninv)) {
			return true;
		}
		return false;
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
	* Returns Export Invoice Query.
	*/
	public function create_export_query($where) {
		global $log, $current_user;
		$log->debug('> create_export_query '.$where);

		include_once 'include/utils/ExportUtils.php';

		//To get the Permitted fields query and the permitted fields list
		$sql = getPermittedFieldsQuery('Invoice', 'detail_view');
		$fields_list = getFieldsListFromQuery($sql);
		$fields_list .= getInventoryFieldsForExport($this->table_name);
		$crmEntityTable = $this->denormalized ? $this->crmentityTable.' as vtiger_crmentity' : 'vtiger_crmentity';
		$query = "SELECT $fields_list FROM ".$crmEntityTable." 
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
			LEFT JOIN vtiger_users as vtigerCreatedBy ON vtiger_crmentity.smcreatorid = vtigerCreatedBy.id and vtigerCreatedBy.status='Active'
			LEFT JOIN vtiger_users ON vtiger_users.id = vtiger_crmentity.smownerid";

		$query .= $this->getNonAdminAccessControlQuery('Invoice', $current_user);
		$where_auto = ' vtiger_crmentity.deleted=0';

		if ($where != '') {
			$query .= " where ($where) AND ".$where_auto;
		} else {
			$query .= ' where '.$where_auto;
		}

		$log->debug('< create_export_query');
		return $query;
	}

	/**
	 * Handle saving related module information.
	 * NOTE: This function has been added to CRMEntity (base class).
	 * You can override the behavior by re-defining it here.
	 */
	public function save_related_module($module, $crmid, $with_module, $with_crmid) {
		global $adb;
		if ($module == 'Invoice' && $with_module == 'Assets') {
			$adb->pquery('UPDATE vtiger_assets SET invoiceid = ? WHERE assetsid = ?', array($crmid, $with_crmid));
		}
		parent::save_related_module($module, $crmid, $with_module, $with_crmid);
	}
}
?>
