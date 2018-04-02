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
require_once 'include/RelatedListView.php';
require 'user_privileges/default_module_view.php';
require_once 'modules/InventoryDetails/InventoryDetails.php';

class Quotes extends CRMEntity {
	public $db;
	public $log;

	public $table_name = 'vtiger_quotes';
	public $table_index= 'quoteid';
	public $column_fields = array();

	/** Indicator if this is a custom module or standard module */
	public $IsCustomModule = false;
	public $HasDirectImageField = false;
	/**
	 * Mandatory table for supporting custom fields.
	 */
	public $customFieldTable = array('vtiger_quotescf', 'quoteid');
	// Uncomment the line below to support custom field columns on related lists
	public $related_tables = array('vtiger_account'=>array('accountid'));

	public $tab_name = array('vtiger_crmentity','vtiger_quotes','vtiger_quotesbillads','vtiger_quotesshipads','vtiger_quotescf');

	public $tab_name_index = array(
		'vtiger_crmentity' => 'crmid',
		'vtiger_quotes'=>'quoteid',
		'vtiger_quotesbillads'=>'quotebilladdressid',
		'vtiger_quotesshipads'=>'quoteshipaddressid',
		'vtiger_quotescf'=>'quoteid'
	);

	// This is the list of vtiger_fields that are in the lists.
	public $list_fields = array(
		'Quote No'=>array('quotes'=>'quote_no'),
		'Subject'=>array('quotes'=>'subject'),
		'Quote Stage'=>array('quotes'=>'quotestage'),
		'Potential Name'=>array('quotes'=>'potentialid'),
		'Account Name'=>array('account'=> 'accountid'),
		'Total'=>array('quotes'=> 'total'),
		'Assigned To'=>array('crmentity'=>'smownerid')
	);
	public $list_fields_name = array(
		'Quote No'=>'quote_no',
		'Subject'=>'subject',
		'Quote Stage'=>'quotestage',
		'Potential Name'=>'potential_id',
		'Account Name'=>'account_id',
		'Total'=>'hdnGrandTotal',
		'Assigned To'=>'assigned_user_id'
	);

	// Make the field link to detail view from list view (Fieldname)
	public $list_link_field = 'subject';

	// For Popup listview and UI type support
	public $search_fields = array(
		'Quote No'=>array('quotes'=>'quote_no'),
		'Subject'=>array('quotes'=>'subject'),
		'Account Name'=>array('quotes'=>'accountid'),
		'Quote Stage'=>array('quotes'=>'quotestage'),
	);
	public $search_fields_name = array(
		'Quote No'=>'quote_no',
		'Subject'=>'subject',
		'Account Name'=>'account_id',
		'Quote Stage'=>'quotestage',
	);

	// For Popup window record selection
	public $popup_fields = array('subject');

	public $sortby_fields = array();

	// For Alphabetical search
	public $def_basicsearch_col = 'subject';

	// Column value to use on detail view record text display
	public $def_detailview_recname = 'subject';

	// This is the list of vtiger_fields that are required.
	public $required_fields = array("accountname"=>1);

	//Added these variables which are used as default order by and sortorder in ListView
	public $default_order_by = 'crmid';
	public $default_sort_order = 'ASC';

	public $mandatory_fields = array('subject','createdtime' ,'modifiedtime');
	public $record_status = '';

	public function save($module, $fileid = '') {
		if ($this->mode=='edit') {
			$this->record_status = getSingleFieldValue($this->table_name, 'quotestage', $this->table_index, $this->id);
		}
		parent::save($module, $fileid);
	}

	public function save_module($module) {
		if ($this->HasDirectImageField) {
			$this->insertIntoAttachment($this->id, $module);
		}
		if ($this->mode=='edit' && !empty($this->record_status) && $this->record_status!=$this->column_fields['quotestage'] && $this->column_fields['quotestage']!='') {
			$this->registerInventoryHistory();
		}
		//in ajax save we should not call this function, because this will delete all the existing product values
		if (inventoryCanSaveProductLines($_REQUEST, 'Quotes')) {
			//Based on the total Number of rows we will save the product relationship with this entity
			saveInventoryProductDetails($this, 'Quotes');
			if (vtlib_isModuleActive('InventoryDetails')) {
				InventoryDetails::createInventoryDetails($this, 'Quotes');
			}
		}
	}

	public function registerInventoryHistory() {
		global $app_strings;
		if (isset($_REQUEST['ajxaction']) && $_REQUEST['ajxaction'] == 'DETAILVIEW') { //if we use ajax edit
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
		if ($this->column_fields['quotestage'] == $app_strings['LBL_NOT_ACCESSIBLE']) {
			//If the value in the request is Not Accessible for a picklist, the existing value will be replaced instead of Not Accessible value.
			$stat_value = getSingleFieldValue($this->table_name, 'quotestage', $this->table_index, $this->id);
		} else {
			$stat_value = $this->column_fields['quotestage'];
		}
		addInventoryHistory(get_class($this), $this->id, $relatedname, $total, $stat_value);
	}

	/**	function used to get the list of sales orders which are related to the Quotes
	 *	@param int $id - quote id
	 *	@return array - return an array which will be returned from the function GetRelatedList
	 */
	public function get_salesorder($id) {
		global $log,$singlepane_view;
		$log->debug("Entering get_salesorder(".$id.") method ...");
		require_once 'modules/SalesOrder/SalesOrder.php';
		$focus = new SalesOrder();

		$button = '';

		if ($singlepane_view == 'true') {
			$returnset = '&return_module=Quotes&return_action=DetailView&return_id='.$id;
		} else {
			$returnset = '&return_module=Quotes&return_action=CallRelatedList&return_id='.$id;
		}

		$userNameSql = getSqlForNameInDisplayFormat(array('first_name'=> 'vtiger_users.first_name', 'last_name' => 'vtiger_users.last_name'), 'Users');
		$query = "select vtiger_crmentity.*, vtiger_salesorder.*, vtiger_quotes.subject as quotename
			, vtiger_account.accountname,case when (vtiger_users.user_name not like '') then
			$userNameSql else vtiger_groups.groupname end as user_name
		from vtiger_salesorder
		inner join vtiger_crmentity on vtiger_crmentity.crmid=vtiger_salesorder.salesorderid
		left outer join vtiger_quotes on vtiger_quotes.quoteid=vtiger_salesorder.quoteid
		left outer join vtiger_account on vtiger_account.accountid=vtiger_salesorder.accountid
		left join vtiger_groups on vtiger_groups.groupid=vtiger_crmentity.smownerid
		LEFT JOIN vtiger_salesordercf ON vtiger_salesordercf.salesorderid = vtiger_salesorder.salesorderid
		LEFT JOIN vtiger_invoice_recurring_info ON vtiger_invoice_recurring_info.start_period = vtiger_salesorder.salesorderid
		LEFT JOIN vtiger_sobillads ON vtiger_sobillads.sobilladdressid = vtiger_salesorder.salesorderid
		LEFT JOIN vtiger_soshipads ON vtiger_soshipads.soshipaddressid = vtiger_salesorder.salesorderid
		left join vtiger_users on vtiger_users.id=vtiger_crmentity.smownerid
		where vtiger_crmentity.deleted=0 and vtiger_salesorder.quoteid = ".$id;
		$log->debug('Exiting get_salesorder method ...');
		return GetRelatedList('Quotes', 'SalesOrder', $focus, $query, $button, $returnset);
	}

	/**	Function used to get the Quote Stage history of the Quotes
	 *	@param $id - quote id
	 *	@return $return_data - array with header and the entries in format array('header'=>$header,'entries'=>$entries_list)
	 *	 where as $header and $entries_list are arrays which contains header values and all column values of all entries
	 */
	public function get_quotestagehistory($id) {
		global $log, $adb, $app_strings, $current_user;
		$log->debug("Entering get_quotestagehistory($id) method ...");

		$query = 'select vtiger_quotestagehistory.*, vtiger_quotes.quote_no
			from vtiger_quotestagehistory
			inner join vtiger_quotes on vtiger_quotes.quoteid = vtiger_quotestagehistory.quoteid
			inner join vtiger_crmentity on vtiger_crmentity.crmid = vtiger_quotes.quoteid
			where vtiger_crmentity.deleted = 0 and vtiger_quotes.quoteid = ?';
		$result=$adb->pquery($query, array($id));
		$header = array();
		$header[] = $app_strings['Quote No'];
		$header[] = $app_strings['LBL_ACCOUNT_NAME'];
		$header[] = $app_strings['LBL_AMOUNT'];
		$header[] = $app_strings['Quote Stage'];
		$header[] = $app_strings['LBL_LAST_MODIFIED'];

		//Getting the field permission for the current user. 1 - Not Accessible, 0 - Accessible
		//Account Name , Total are mandatory fields. So no need to do security check to these fields.

		//If field is accessible then getFieldVisibilityPermission function will return 0 else return 1
		$quotestage_access = (getFieldVisibilityPermission('Quotes', $current_user->id, 'quotestage') != '0')? 1 : 0;
		$picklistarray = getAccessPickListValues('Quotes');

		$quotestage_array = ($quotestage_access != 1)? $picklistarray['quotestage']: array();
		//- ==> picklist field is not permitted in profile
		//Not Accessible - picklist is permitted in profile but picklist value is not permitted
		$error_msg = ($quotestage_access != 1)? getTranslatedString('LBL_NOT_ACCESSIBLE'): '-';
		$entries_list = array();
		while ($row = $adb->fetch_array($result)) {
			$entries = array();

			$entries[] = $row['quote_no'];
			$entries[] = $row['accountname'];
			$total = new CurrencyField($row['total']);
			$entries[] = $total->getDisplayValueWithSymbol($current_user);
			$entries[] = (in_array($row['quotestage'], $quotestage_array))? getTranslatedString($row['quotestage'], 'Quotes'): $error_msg;
			$date = new DateTimeField($row['lastmodified']);
			$entries[] = $date->getDisplayDateTimeValue();

			$entries_list[] = $entries;
		}

		$return_data = array('header'=>$header,'entries'=>$entries_list,'navigation'=>array('',''));
		$log->debug("Exiting get_quotestagehistory method ...");
		return $return_data;
	}

	// Function to get column name - Overriding function of base class
	public function get_column_value($columname, $fldvalue, $fieldname, $uitype, $datatype = '') {
		if ($columname == 'potentialid' || $columname == 'contactid') {
			if ($fldvalue == '') {
				return null;
			}
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
		$matrix = $queryPlanner->newDependencyMatrix();
		$matrix->setDependency('vtiger_crmentityQuotes', array('vtiger_usersQuotes', 'vtiger_groupsQuotes', 'vtiger_lastModifiedByQuotes'));
		$matrix->setDependency('vtiger_inventoryproductrelQuotes', array('vtiger_productsQuotes', 'vtiger_serviceQuotes'));

		if (!$queryPlanner->requireTable('vtiger_quotes', $matrix) && !$queryPlanner->requireTable('vtiger_quotescf', $matrix)) {
			return '';
		}
		$matrix->setDependency('vtiger_quotes', array('vtiger_crmentityQuotes', "vtiger_currency_info$secmodule",
			'vtiger_quotescf', 'vtiger_potentialRelQuotes', 'vtiger_quotesbillads','vtiger_quotesshipads',
			'vtiger_inventoryproductrelQuotes', 'vtiger_contactdetailsQuotes', 'vtiger_accountQuotes',
			'vtiger_invoice_recurring_info','vtiger_quotesQuotes','vtiger_usersRel1'));
		$query = parent::generateReportsSecQuery($module, $secmodule, $queryPlanner, $type, $where_condition);
		if ($queryPlanner->requireTable("vtiger_quotesbillads")) {
			$query .= " left join vtiger_quotesbillads on vtiger_quotes.quoteid=vtiger_quotesbillads.quotebilladdressid";
		}
		if ($queryPlanner->requireTable('vtiger_quotesshipads')) {
			$query .= ' left join vtiger_quotesshipads on vtiger_quotes.quoteid=vtiger_quotesshipads.quoteshipaddressid';
		}
		if ($queryPlanner->requireTable("vtiger_currency_info$secmodule")) {
			$query .= " left join vtiger_currency_info as vtiger_currency_info$secmodule on vtiger_currency_info$secmodule.id = vtiger_quotes.currency_id";
		}
		if (($type !== 'COLUMNSTOTOTAL') || ($type == 'COLUMNSTOTOTAL' && $where_condition == 'add')) {
			if ($queryPlanner->requireTable("vtiger_inventoryproductrelQuotes", $matrix)) {
				if ($module == 'Products') {
					$query .= ' left join vtiger_inventoryproductrel as vtiger_inventoryproductrelQuotes on'
						.' vtiger_quotes.quoteid = vtiger_inventoryproductrelQuotes.id and vtiger_inventoryproductrelQuotes.productid=vtiger_products.productid ';
				} elseif ($module == 'Services') {
					$query .= ' left join vtiger_inventoryproductrel as vtiger_inventoryproductrelQuotes on'
						.' vtiger_quotes.quoteid = vtiger_inventoryproductrelQuotes.id and vtiger_inventoryproductrelQuotes.productid=vtiger_service.serviceid ';
				} else {
					$query .= ' left join vtiger_inventoryproductrel as vtiger_inventoryproductrelQuotes on'
						.' vtiger_quotes.quoteid = vtiger_inventoryproductrelQuotes.id ';
				}
			}
			if ($queryPlanner->requireTable("vtiger_productsQuotes")) {
				$query .= " left join vtiger_products as vtiger_productsQuotes on vtiger_productsQuotes.productid = vtiger_inventoryproductrelQuotes.productid";
			}
			if ($queryPlanner->requireTable("vtiger_serviceQuotes")) {
				$query .= " left join vtiger_service as vtiger_serviceQuotes on vtiger_serviceQuotes.serviceid = vtiger_inventoryproductrelQuotes.productid";
			}
		}
		if ($queryPlanner->requireTable("vtiger_usersRel1")) {
			$query .= " left join vtiger_users as vtiger_usersRel1 on vtiger_usersRel1.id = vtiger_quotes.inventorymanager";
		}
		if ($queryPlanner->requireTable("vtiger_potentialRelQuotes")) {
			$query .= " left join vtiger_potential as vtiger_potentialRelQuotes on vtiger_potentialRelQuotes.potentialid = vtiger_quotes.potentialid";
		}
		if ($queryPlanner->requireTable("vtiger_contactdetailsQuotes")) {
			$query .= " left join vtiger_contactdetails as vtiger_contactdetailsQuotes on vtiger_contactdetailsQuotes.contactid = vtiger_quotes.contactid";
		}
		if ($queryPlanner->requireTable("vtiger_accountQuotes")) {
			$query .= " left join vtiger_account as vtiger_accountQuotes on vtiger_accountQuotes.accountid = vtiger_quotes.accountid";
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
			'SalesOrder' =>array('vtiger_salesorder'=>array('quoteid','salesorderid'),'vtiger_quotes'=>'quoteid'),
			'Calendar' =>array('vtiger_seactivityrel'=>array('crmid','activityid'),'vtiger_quotes'=>'quoteid'),
			'Documents' => array('vtiger_senotesrel'=>array('crmid','notesid'),'vtiger_quotes'=>'quoteid'),
			'Accounts' => array('vtiger_quotes'=>array('quoteid','accountid')),
			'Contacts' => array('vtiger_quotes'=>array('quoteid','contactid')),
			'Potentials' => array('vtiger_quotes'=>array('quoteid','potentialid')),
		);
		return isset($rel_tables[$secmodule]) ? $rel_tables[$secmodule] : '';
	}

	// Function to unlink an entity with given Id from another entity
	public function unlinkRelationship($id, $return_module, $return_id) {
		if (empty($return_module) || empty($return_id)) {
			return;
		}

		if ($return_module == 'Accounts') {
			$this->trash('Quotes', $id);
		} elseif ($return_module == 'Potentials') {
			$relation_query = 'UPDATE vtiger_quotes SET potentialid=? WHERE quoteid=?';
			$this->db->pquery($relation_query, array(null, $id));
		} elseif ($return_module == 'Contacts') {
			$relation_query = 'UPDATE vtiger_quotes SET contactid=? WHERE quoteid=?';
			$this->db->pquery($relation_query, array(null, $id));
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
	* Returns Export Quotes Query.
	*/
	public function create_export_query($where) {
		global $log, $current_user;
		$log->debug("Entering create_export_query($where) method ...");

		include 'include/utils/ExportUtils.php';

		//To get the Permitted fields query and the permitted fields list
		$sql = getPermittedFieldsQuery('Quotes', 'detail_view');
		$fields_list = getFieldsListFromQuery($sql);
		$fields_list .= getInventoryFieldsForExport($this->table_name);
		$userNameSql = getSqlForNameInDisplayFormat(array('first_name'=>'vtiger_users.first_name', 'last_name' => 'vtiger_users.last_name'), 'Users');

		$query = "SELECT $fields_list FROM vtiger_crmentity
			INNER JOIN vtiger_quotes ON vtiger_quotes.quoteid = vtiger_crmentity.crmid
			LEFT JOIN vtiger_quotescf ON vtiger_quotescf.quoteid = vtiger_quotes.quoteid
			LEFT JOIN vtiger_quotesbillads ON vtiger_quotesbillads.quotebilladdressid = vtiger_quotes.quoteid
			LEFT JOIN vtiger_quotesshipads ON vtiger_quotesshipads.quoteshipaddressid = vtiger_quotes.quoteid
			LEFT JOIN vtiger_inventoryproductrel ON vtiger_inventoryproductrel.id = vtiger_quotes.quoteid
			LEFT JOIN vtiger_products ON vtiger_products.productid = vtiger_inventoryproductrel.productid
			LEFT JOIN vtiger_service ON vtiger_service.serviceid = vtiger_inventoryproductrel.productid
			LEFT JOIN vtiger_contactdetails ON vtiger_contactdetails.contactid = vtiger_quotes.contactid
			LEFT JOIN vtiger_potential ON vtiger_potential.potentialid = vtiger_quotes.potentialid
			LEFT JOIN vtiger_account ON vtiger_account.accountid = vtiger_quotes.accountid
			LEFT JOIN vtiger_currency_info ON vtiger_currency_info.id = vtiger_quotes.currency_id
			LEFT JOIN vtiger_users AS vtiger_inventoryManager ON vtiger_inventoryManager.id = vtiger_quotes.inventorymanager
			LEFT JOIN vtiger_groups ON vtiger_groups.groupid = vtiger_crmentity.smownerid
			LEFT JOIN vtiger_users as vtigerCreatedBy ON vtiger_crmentity.smcreatorid = vtigerCreatedBy.id and vtigerCreatedBy.status='Active'
			LEFT JOIN vtiger_users ON vtiger_users.id = vtiger_crmentity.smownerid";

		$query .= $this->getNonAdminAccessControlQuery('Quotes', $current_user);
		$where_auto = ' vtiger_crmentity.deleted=0';

		if ($where != "") {
			$query .= " where ($where) AND ".$where_auto;
		} else {
			$query .= ' where '.$where_auto;
		}

		$log->debug('Exiting create_export_query method ...');
		return $query;
	}
}
?>
