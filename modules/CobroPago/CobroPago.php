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
require_once('modules/Invoice/Invoice.php');

class CobroPago extends CRMEntity {
	var $db, $log; // Used in class functions of CRMEntity

	var $table_name = 'vtiger_cobropago';
	var $table_index= 'cobropagoid';
	var $column_fields = Array();

	/** Indicator if this is a custom module or standard module */
	var $IsCustomModule = true;

	/**
	 * Mandatory table for supporting custom fields.
	 */
	var $customFieldTable = Array('vtiger_cobropagocf', 'cobropagoid');

	/**
	 * Mandatory for Saving, Include tables related to this module.
	 */
	var $tab_name = Array('vtiger_crmentity', 'vtiger_cobropago', 'vtiger_cobropagocf');

	/**
	 * Mandatory for Saving, Include tablename and tablekey columnname here.
	 */
	var $tab_name_index = Array(
		'vtiger_crmentity' => 'crmid',
		'vtiger_cobropago'   => 'cobropagoid',
	    'vtiger_cobropagocf' => 'cobropagoid');

	/**
	 * Mandatory for Listing (Related listview)
	 */
	var $list_fields = Array (
			'Reference'=>Array('cobropago'=>'reference'),
			'PaymentMode'=>Array('cobropago'=>'paymentmode'),
			'Amount'=>Array('cobropago'=>'amount'),
			'DueDate'=>Array('cobropago'=>'duedate'),
		'Assigned To' => Array('crmentity','smownerid')
	);
	var $list_fields_name = Array(
			'Reference'=>'reference',
			'PaymentMode'=>'paymentmode',	  			
			'Amount'=>'amount',
			'DueDate'=>'duedate',
		'Assigned To' => 'assigned_user_id'
	);

	// Make the field link to detail view from list view (Fieldname)
	var $list_link_field = 'reference';

	// For Popup listview and UI type support
	var $search_fields = Array(
			'Reference'=>Array('cobropago'=>'reference'),
            'PaymentMode'=>Array('cobropago'=>'paymentmode'),
            'Amount'=>Array('cobropago'=>'amount'),
            'DueDate'=>Array('cobropago'=>'duedate')
	);
	var $search_fields_name = Array(
            'Reference'=>'reference',
            'PaymentMode'=>'paymentmode',               
            'Amount'=>'amount',
            'DueDate'=>'duedate'
	);

	// For Popup window record selection
	var $popup_fields = Array('reference');

	// Placeholder for sort fields - All the fields will be initialized for Sorting through initSortFields
	var $sortby_fields = Array();

	// For Alphabetical search
	var $def_basicsearch_col = 'reference';

	// Column value to use on detail view record text display
	var $def_detailview_recname = 'reference';

	// Required Information for enabling Import feature
	var $required_fields = Array('reference'=>1);

	// Callback function list during Importing
	var $special_functions = Array('set_import_assigned_user');

	var $default_order_by = 'reference';
	var $default_sort_order='ASC';
	// Used when enabling/disabling the mandatory fields for the module.
	// Refers to vtiger_field.fieldname values.
	var $mandatory_fields = Array('createdtime', 'modifiedtime', 'reference');
	
	function __construct() {
		global $log, $currentModule;
		$this->column_fields = getColumnFields($currentModule);
		$this->db = PearDatabase::getInstance();
		$this->log = $log;
	}

	function getSortOrder() {
		global $currentModule;

		$sortorder = $this->default_sort_order;
		if($_REQUEST['sorder']) $sortorder = $this->db->sql_escape_string($_REQUEST['sorder']);
		else if($_SESSION[$currentModule.'_Sort_Order']) 
			$sortorder = $_SESSION[$currentModule.'_Sort_Order'];

		return $sortorder;
	}

	function getOrderBy() {
		global $currentModule;
		
		$use_default_order_by = '';		
		if(PerformancePrefs::getBoolean('LISTVIEW_DEFAULT_SORTING', true)) {
			$use_default_order_by = $this->default_order_by;
		}
		
		$orderby = $use_default_order_by;
		if($_REQUEST['order_by']) $orderby = $this->db->sql_escape_string($_REQUEST['order_by']);
		else if($_SESSION[$currentModule.'_Order_By'])
			$orderby = $_SESSION[$currentModule.'_Order_By'];
		return $orderby;
	}

	function save_module($module) {
		global $current_user,$log,$adb;
		$cypid = $this->id;
		$data = $this->column_fields;
		// Entity has been saved, take next action
		$currencyid=fetchCurrency($current_user->id);
		$rate_symbol = getCurrencySymbolandCRate($currencyid);
		$rate = $rate_symbol['rate'];
		$value=0;
		if(isset($data['amount']) and isset($data['cost'])) {
			$value = convertToDollar($data['amount']-$data['cost'],$rate);
		}
		$adb->query("update vtiger_cobropago set benefit='$value' where cobropagoid=".$cypid);

		$relatedId = $this->column_fields['related_id'];
		if (!empty($relatedId) and self::invoice_control_installed()) {
			Invoice::updateAmountDue($relatedId);
		}
		// Calculate related module balance
		$this->calculateRelatedTotals($this->column_fields['parent_id']);
	}

	public static function calculateRelatedTotals($pid) {
	  global $adb;
	  $parent_module = getSalesEntityType($pid);
	  if ($parent_module=='Accounts' and self::account_control_installed()) {
		$sumamountcredit =$adb->query_result($adb->pquery("select sum(amount) as suma from vtiger_cobropago inner join vtiger_crmentity on crmid=cobropagoid where deleted=0 and credit=1 and parent_id=?",array($pid)),0,0);
		$sumamountdebit =$adb->query_result($adb->pquery("select sum(amount) as suma from vtiger_cobropago inner join vtiger_crmentity on crmid=cobropagoid where deleted=0 and credit=0 and parent_id=?",array($pid)),0,0);
		$sumpendingcredit=$adb->query_result($adb->pquery("select sum(amount) as suma from vtiger_cobropago inner join vtiger_crmentity on crmid=cobropagoid where deleted=0 and credit=1 and paid='0' and parent_id=?",array($pid)),0,0);
		$sumpendingdebit=$adb->query_result($adb->pquery("select sum(amount) as suma from vtiger_cobropago inner join vtiger_crmentity on crmid=cobropagoid where deleted=0 and credit=0 and paid='0' and parent_id=?",array($pid)),0,0);
		$sumamount=$sumamountcredit-$sumamountdebit;
		$sumpending=$sumpendingcredit-$sumpendingdebit;
		$balance=$sumamount-$sumpending;
		$adb->pquery("update vtiger_account set balance=?,totalamount=?,totalpending=? where accountid=?",array($balance,$sumamount,$sumpending,$pid));
	  }
	  if ($parent_module=='Contacts' and self::contact_control_installed()) {
		$sumamountcredit =$adb->query_result($adb->pquery("select sum(amount) as suma from vtiger_cobropago inner join vtiger_crmentity on crmid=cobropagoid where deleted=0 and credit=1 and parent_id=?",array($pid)),0,0);
		$sumamountdebit =$adb->query_result($adb->pquery("select sum(amount) as suma from vtiger_cobropago inner join vtiger_crmentity on crmid=cobropagoid where deleted=0 and credit=0 and parent_id=?",array($pid)),0,0);
		$sumpendingcredit=$adb->query_result($adb->pquery("select sum(amount) as suma from vtiger_cobropago inner join vtiger_crmentity on crmid=cobropagoid where deleted=0 and credit=1 and paid='0' and parent_id=?",array($pid)),0,0);
		$sumpendingdebit=$adb->query_result($adb->pquery("select sum(amount) as suma from vtiger_cobropago inner join vtiger_crmentity on crmid=cobropagoid where deleted=0 and credit=0 and paid='0' and parent_id=?",array($pid)),0,0);
		$sumamount=$sumamountcredit-$sumamountdebit;
		$sumpending=$sumpendingcredit-$sumpendingdebit;
		$balance=$sumamount-$sumpending;
		$adb->pquery("update vtiger_contactdetails set balance=?,totalamount=?,totalpending=? where contactid=?",array($balance,$sumamount,$sumpending,$pid));
	  }
	  if ($parent_module=='Vendors' and self::vendor_control_installed()) {
		$sumamountcredit =$adb->query_result($adb->pquery("select sum(amount) as suma from vtiger_cobropago inner join vtiger_crmentity on crmid=cobropagoid where deleted=0 and credit=1 and parent_id=?",array($pid)),0,0);
		$sumamountdebit =$adb->query_result($adb->pquery("select sum(amount) as suma from vtiger_cobropago inner join vtiger_crmentity on crmid=cobropagoid where deleted=0 and credit=0 and parent_id=?",array($pid)),0,0);
		$sumpendingcredit=$adb->query_result($adb->pquery("select sum(amount) as suma from vtiger_cobropago inner join vtiger_crmentity on crmid=cobropagoid where deleted=0 and credit=1 and paid='0' and parent_id=?",array($pid)),0,0);
		$sumpendingdebit=$adb->query_result($adb->pquery("select sum(amount) as suma from vtiger_cobropago inner join vtiger_crmentity on crmid=cobropagoid where deleted=0 and credit=0 and paid='0' and parent_id=?",array($pid)),0,0);
		$sumamount=$sumamountcredit-$sumamountdebit;
		$sumpending=$sumpendingcredit-$sumpendingdebit;
		$balance=$sumamount-$sumpending;
		$adb->pquery("update vtiger_vendor set balance=?,totalamount=?,totalpending=? where vendorid=?",array($balance,$sumamount,$sumpending,$pid));
	  }
	}

	function trash($module,$record) {
		global $adb;
		parent::trash($module,$record);
		$rs = $adb->pquery("select related_id,parent_id from vtiger_cobropago where cobropagoid=?",array($record));
		if ($rs and $adb->num_rows($rs)==1) {
			$relatedId = $adb->query_result($rs,0,'related_id');
			$pid = $adb->query_result($rs,0,'parent_id');
			if (!empty($relatedId) and self::invoice_control_installed()) {
				Invoice::updateAmountDue($relatedId);
			}
			// Calculate related module balance
			CobroPago::calculateRelatedTotals($pid);
		}
	}

	function unlinkRelationship($id, $return_module, $return_id) {
		global $adb;
		parent::unlinkRelationship($id, $return_module, $return_id);
		$rs = $adb->pquery("select related_id,parent_id from vtiger_cobropago where cobropagoid=?",array($id));
		if ($rs and $adb->num_rows($rs)==1) {
			$relatedId = $adb->query_result($rs,0,'related_id');
			$pid = $adb->query_result($rs,0,'parent_id');
			if (!empty($relatedId) and self::invoice_control_installed()) {
				Invoice::updateAmountDue($relatedId);
			}
			// Calculate related module balance
			CobroPago::calculateRelatedTotals($pid);
		}
	}

	public static function account_control_installed() {
		global $adb;
		$cnacc=$adb->getColumnNames('vtiger_account');
		if (in_array('balance', $cnacc)
		and in_array('totalamount', $cnacc)
		and in_array('totalpending', $cnacc)) return true;
		return false;
	}
	public static function contact_control_installed() {
		global $adb;
		$cnacc=$adb->getColumnNames('vtiger_contactdetails');
		if (in_array('balance', $cnacc)
		and in_array('totalamount', $cnacc)
		and in_array('totalpending', $cnacc)) return true;
		return false;
	}
	public static function vendor_control_installed() {
		global $adb;
		$cnacc=$adb->getColumnNames('vtiger_vendor');
		if (in_array('balance', $cnacc)
				and in_array('totalamount', $cnacc)
				and in_array('totalpending', $cnacc)) return true;
		return false;
	}
	public static function invoice_control_installed() {
		global $adb;
		$cninv=$adb->getColumnNames('vtiger_invoice');
		if (in_array('amount_due', $cninv)
		and in_array('amount_paid', $cninv)
		and in_array('total_amount', $cninv)) return true;
		return false;
	}

	/**
	 * Return query to use based on given modulename, fieldname
	 * Useful to handle specific case handling for Popup
	 */
	function getQueryByModuleField($module, $fieldname, $srcrecord, $query='') {
		// $srcrecord could be empty
	}

	/**
	 * Get list view query (send more WHERE clause condition if required)
	 */
	function getListQuery($module, $usewhere='') {
		$query = "SELECT vtiger_crmentity.*, $this->table_name.*";
		
		// Keep track of tables joined to avoid duplicates
		$joinedTables = array();

		// Select Custom Field Table Columns if present
		if(!empty($this->customFieldTable)) $query .= ", " . $this->customFieldTable[0] . ".* ";

		$query .= " FROM $this->table_name";

		$query .= "	INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = $this->table_name.$this->table_index";

		$joinedTables[] = $this->table_name;
		$joinedTables[] = 'vtiger_crmentity';
		
		// Consider custom table join as well.
		if(!empty($this->customFieldTable)) {
			$query .= " INNER JOIN ".$this->customFieldTable[0]." ON ".$this->customFieldTable[0].'.'.$this->customFieldTable[1] .
				      " = $this->table_name.$this->table_index";
			$joinedTables[] = $this->customFieldTable[0]; 
		}
		$query .= " LEFT JOIN vtiger_users ON vtiger_users.id = vtiger_crmentity.smownerid";
		$query .= " LEFT JOIN vtiger_groups ON vtiger_groups.groupid = vtiger_crmentity.smownerid";

		$joinedTables[] = 'vtiger_users';
		$joinedTables[] = 'vtiger_groups';
		
		$linkedModulesQuery = $this->db->pquery("SELECT distinct fieldname, columnname, relmodule FROM vtiger_field" .
				" INNER JOIN vtiger_fieldmodulerel ON vtiger_fieldmodulerel.fieldid = vtiger_field.fieldid" .
				" WHERE uitype='10' AND vtiger_fieldmodulerel.module=?", array($module));
		$linkedFieldsCount = $this->db->num_rows($linkedModulesQuery);
		
		for($i=0; $i<$linkedFieldsCount; $i++) {
			$related_module = $this->db->query_result($linkedModulesQuery, $i, 'relmodule');
			$fieldname = $this->db->query_result($linkedModulesQuery, $i, 'fieldname');
			$columnname = $this->db->query_result($linkedModulesQuery, $i, 'columnname');
			
			$other =  CRMEntity::getInstance($related_module);
			vtlib_setup_modulevars($related_module, $other);
			
			if(!in_array($other->table_name, $joinedTables)) {
				$query .= " LEFT JOIN $other->table_name ON $other->table_name.$other->table_index = $this->table_name.$columnname";
				$joinedTables[] = $other->table_name;
			}
		}

		global $current_user;
		$query .= $this->getNonAdminAccessControlQuery($module,$current_user);
		$query .= "	WHERE vtiger_crmentity.deleted = 0 ".$usewhere;
		return $query;
	}

	/**
	 * Apply security restriction (sharing privilege) query part for List view.
	 */
	function getListViewSecurityParameter($module) {
		global $current_user;
		require('user_privileges/user_privileges_'.$current_user->id.'.php');
		require('user_privileges/sharing_privileges_'.$current_user->id.'.php');

		$sec_query = '';
		$tabid = getTabid($module);

		if($is_admin==false && $profileGlobalPermission[1] == 1 && $profileGlobalPermission[2] == 1 
			&& $defaultOrgSharingPermission[$tabid] == 3) {

				$sec_query .= " AND (vtiger_crmentity.smownerid in($current_user->id) OR vtiger_crmentity.smownerid IN 
					(
						SELECT vtiger_user2role.userid FROM vtiger_user2role 
						INNER JOIN vtiger_users ON vtiger_users.id=vtiger_user2role.userid 
						INNER JOIN vtiger_role ON vtiger_role.roleid=vtiger_user2role.roleid 
						WHERE vtiger_role.parentrole LIKE '".$current_user_parent_role_seq."::%'
					) 
					OR vtiger_crmentity.smownerid IN 
					(
						SELECT shareduserid FROM vtiger_tmp_read_user_sharing_per 
						WHERE userid=".$current_user->id." AND tabid=".$tabid."
					) 
					OR 
						(";
		
					// Build the query based on the group association of current user.
					if(sizeof($current_user_groups) > 0) {
						$sec_query .= " vtiger_groups.groupid IN (". implode(",", $current_user_groups) .") OR ";
					}
					$sec_query .= " vtiger_groups.groupid IN 
						(
							SELECT vtiger_tmp_read_group_sharing_per.sharedgroupid 
							FROM vtiger_tmp_read_group_sharing_per
							WHERE userid=".$current_user->id." and tabid=".$tabid."
						)";
				$sec_query .= ")
				)";
		}
		return $sec_query;
	}

	/**
	 * Create query to export the records.
	 */
	function create_export_query($where)
	{
		global $current_user;
		$thismodule = $_REQUEST['module'];
		
		include("include/utils/ExportUtils.php");

		//To get the Permitted fields query and the permitted fields list
		$sql = getPermittedFieldsQuery($thismodule, "detail_view");
		
		$fields_list = getFieldsListFromQuery($sql);

		$query = "SELECT $fields_list, vtiger_users.user_name AS user_name 
					FROM vtiger_crmentity INNER JOIN $this->table_name ON vtiger_crmentity.crmid=$this->table_name.$this->table_index";

		if(!empty($this->customFieldTable)) {
			$query .= " INNER JOIN ".$this->customFieldTable[0]." ON ".$this->customFieldTable[0].'.'.$this->customFieldTable[1] .
				      " = $this->table_name.$this->table_index"; 
		}

		$query .= " LEFT JOIN vtiger_groups ON vtiger_groups.groupid = vtiger_crmentity.smownerid";
		$query .= " LEFT JOIN vtiger_users ON vtiger_crmentity.smownerid = vtiger_users.id and vtiger_users.status='Active'";
		
		$linkedModulesQuery = $this->db->pquery("SELECT distinct fieldname, columnname, relmodule FROM vtiger_field" .
				" INNER JOIN vtiger_fieldmodulerel ON vtiger_fieldmodulerel.fieldid = vtiger_field.fieldid" .
				" WHERE uitype='10' AND vtiger_fieldmodulerel.module=?", array($thismodule));
		$linkedFieldsCount = $this->db->num_rows($linkedModulesQuery);

		for($i=0; $i<$linkedFieldsCount; $i++) {
			$related_module = $this->db->query_result($linkedModulesQuery, $i, 'relmodule');
			$fieldname = $this->db->query_result($linkedModulesQuery, $i, 'fieldname');
			$columnname = $this->db->query_result($linkedModulesQuery, $i, 'columnname');
			
			$other = CRMEntity::getInstance($related_module);
			vtlib_setup_modulevars($related_module, $other);
			
			$query .= " LEFT JOIN $other->table_name ON $other->table_name.$other->table_index = $this->table_name.$columnname";
		}

		$query .= $this->getNonAdminAccessControlQuery($thismodule,$current_user);
		$where_auto = " vtiger_crmentity.deleted=0";

		if($where != '') $query .= " WHERE ($where) AND $where_auto";
		else $query .= " WHERE $where_auto";

		return $query;
	}

	/**
	 * Initialize this instance for importing.
	 */
	function initImport($module) {
		$this->db = PearDatabase::getInstance();
		$this->initImportableFields($module);
	}

	/**
	 * Create list query to be shown at the last step of the import.
	 * Called From: modules/Import/UserLastImport.php
	 */
	function create_import_query($module) {
		global $current_user;
		$query = "SELECT vtiger_crmentity.crmid, case when (vtiger_users.user_name not like '') then vtiger_users.user_name else vtiger_groups.groupname end as user_name, $this->table_name.* FROM $this->table_name
			INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = $this->table_name.$this->table_index
			LEFT JOIN vtiger_users_last_import ON vtiger_users_last_import.bean_id=vtiger_crmentity.crmid
			LEFT JOIN vtiger_users ON vtiger_users.id = vtiger_crmentity.smownerid
			LEFT JOIN vtiger_groups ON vtiger_groups.groupid = vtiger_crmentity.smownerid
			WHERE vtiger_users_last_import.assigned_user_id='$current_user->id'
			AND vtiger_users_last_import.bean_type='$module'
			AND vtiger_users_last_import.deleted=0";
		return $query;
	}

	/**
	 * Delete the last imported records.
	 */
	function undo_import($module, $user_id) {
		global $adb;
		$count = 0;
		$query1 = "select bean_id from vtiger_users_last_import where assigned_user_id=? AND bean_type='$module' AND deleted=0";
		$result1 = $adb->pquery($query1, array($user_id)) or die("Error getting last import for undo: ".mysql_error()); 
		while ( $row1 = $adb->fetchByAssoc($result1))
		{
			$query2 = "update vtiger_crmentity set deleted=1 where crmid=?";
			$result2 = $adb->pquery($query2, array($row1['bean_id'])) or die("Error undoing last import: ".mysql_error()); 
			$count++;			
		}
		return $count;
	}
	
	/**
	 * Transform the value while exporting
	 */
	function transform_export_value($key, $value) {
		return parent::transform_export_value($key, $value);
	}

	/**
	 * Function which will set the assigned user id for import record.
	 */
	function set_import_assigned_user()
	{
		global $current_user, $adb;
		$record_user = $this->column_fields["assigned_user_id"];
		
		if($record_user != $current_user->id){
			$sqlresult = $adb->pquery("select id from vtiger_users where id = ? union select groupid as id from vtiger_groups where groupid = ?", array($record_user, $record_user));
			if($this->db->num_rows($sqlresult)!= 1) {
				$this->column_fields["assigned_user_id"] = $current_user->id;
			} else {			
				$row = $adb->fetchByAssoc($sqlresult, -1, false);
				if (isset($row['id']) && $row['id'] != -1) {
					$this->column_fields["assigned_user_id"] = $row['id'];
				} else {
					$this->column_fields["assigned_user_id"] = $current_user->id;
				}
			}
		}
	}
	
	/** 
	 * Function which will give the basic query to find duplicates
	 */
	function getDuplicatesQuery($module,$table_cols,$field_values,$ui_type_arr,$select_cols='') {
		$select_clause = "SELECT ". $this->table_name .".".$this->table_index ." AS recordid, vtiger_users_last_import.deleted,".$table_cols;

		// Select Custom Field Table Columns if present
		if(isset($this->customFieldTable)) $query .= ", " . $this->customFieldTable[0] . ".* ";

		$from_clause = " FROM $this->table_name";

		$from_clause .= "	INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = $this->table_name.$this->table_index";

		// Consider custom table join as well.
		if(isset($this->customFieldTable)) {
			$from_clause .= " INNER JOIN ".$this->customFieldTable[0]." ON ".$this->customFieldTable[0].'.'.$this->customFieldTable[1] .
				      " = $this->table_name.$this->table_index"; 
		}
		$from_clause .= " LEFT JOIN vtiger_users ON vtiger_users.id = vtiger_crmentity.smownerid
						LEFT JOIN vtiger_groups ON vtiger_groups.groupid = vtiger_crmentity.smownerid";
		
		$where_clause = "	WHERE vtiger_crmentity.deleted = 0";
		$where_clause .= $this->getListViewSecurityParameter($module);
					
		if (isset($select_cols) && trim($select_cols) != '') {
			$sub_query = "SELECT $select_cols FROM  $this->table_name AS t " .
				" INNER JOIN vtiger_crmentity AS crm ON crm.crmid = t.".$this->table_index;
			// Consider custom table join as well.
			if(isset($this->customFieldTable)) {
				$sub_query .= " LEFT JOIN ".$this->customFieldTable[0]." tcf ON tcf.".$this->customFieldTable[1]." = t.$this->table_index";
			}
			$sub_query .= " WHERE crm.deleted=0 GROUP BY $select_cols HAVING COUNT(*)>1";	
		} else {
			$sub_query = "SELECT $table_cols $from_clause $where_clause GROUP BY $table_cols HAVING COUNT(*)>1";
		}	
		
		
		$query = $select_clause . $from_clause .
					" LEFT JOIN vtiger_users_last_import ON vtiger_users_last_import.bean_id=" . $this->table_name .".".$this->table_index .
					" INNER JOIN (" . $sub_query . ") AS temp ON ".get_on_clause($field_values,$ui_type_arr,$module) .
					$where_clause .
					" ORDER BY $table_cols,". $this->table_name .".".$this->table_index ." ASC";
					
		return $query;		
	}

	/**
	 * Invoked when special actions are performed on the module.
	 * @param String Module name
	 * @param String Event Type (module.postinstall, module.disabled, module.enabled, module.preuninstall)
	 */
	function vtlib_handler($modulename, $event_type) {
		if($event_type == 'module.postinstall') {
			// TODO Handle post installation actions
			$modAccounts=Vtiger_Module::getInstance('Accounts');
			$modContacts=Vtiger_Module::getInstance('Contacts');
			$modVnd=Vtiger_Module::getInstance('Vendors');
			$modInvoice=Vtiger_Module::getInstance('Invoice');
			$modSO=Vtiger_Module::getInstance('SalesOrder');
			$modPO=Vtiger_Module::getInstance('PurchaseOrder');
			$modQt=Vtiger_Module::getInstance('Quotes');
			$modCpg=Vtiger_Module::getInstance('Campaigns');
			$modPot=Vtiger_Module::getInstance('Potentials');
			$modHD=Vtiger_Module::getInstance('HelpDesk');
			$modPrj=Vtiger_Module::getInstance('Project');
			$modPrjTask=Vtiger_Module::getInstance('ProjectTask');
			$modCyP=Vtiger_Module::getInstance('CobroPago');
			
			if ($modAccounts) $modAccounts->setRelatedList($modCyP, 'CobroPago', Array('ADD'),'get_dependents_list');
			if ($modContacts) $modContacts->setRelatedList($modCyP, 'CobroPago', Array('ADD'),'get_dependents_list');
			if ($modVnd) $modVnd->setRelatedList($modCyP, 'CobroPago', Array('ADD'),'get_dependents_list');
			if ($modInvoice) $modInvoice->setRelatedList($modCyP, 'CobroPago', Array('ADD'),'get_dependents_list');
			if ($modInvoice) $modInvoice->addLink('DETAILVIEWBASIC','Add Payment','index.php?module=CobroPago&action=EditView&related_id=$RECORD$&return_module=Invoice&return_id=$RECORD$&return_action=DetailView');
			if ($modSO) $modSO->setRelatedList($modCyP, 'CobroPago', Array('ADD'),'get_dependents_list');
			if ($modSO) $modSO->addLink('DETAILVIEWBASIC','Add Payment','index.php?module=CobroPago&action=EditView&related_id=$RECORD$&return_module=SalesOrder&return_id=$RECORD$&return_action=DetailView');
			if ($modPO) $modPO->setRelatedList($modCyP, 'CobroPago', Array('ADD'),'get_dependents_list');
			if ($modPO) $modPO->addLink('DETAILVIEWBASIC','Add Payment','index.php?module=CobroPago&action=EditView&related_id=$RECORD$&return_module=PurchaseOrder&return_id=$RECORD$&return_action=DetailView');
			if ($modQt) $modQt->setRelatedList($modCyP, 'CobroPago', Array('ADD'),'get_dependents_list');
			if ($modQt) $modQt->addLink('DETAILVIEWBASIC','Add Payment','index.php?module=CobroPago&action=EditView&related_id=$RECORD$&return_module=Quotes&return_id=$RECORD$&return_action=DetailView');
			if ($modCpg) $modCpg->setRelatedList($modCyP, 'CobroPago', Array('ADD'),'get_dependents_list');
			if ($modPot) $modPot->setRelatedList($modCyP, 'CobroPago', Array('ADD'),'get_dependents_list');
			if ($modHD) $modHD->setRelatedList($modCyP, 'CobroPago', Array('ADD'),'get_dependents_list');
			if ($modPrj) $modPrj->setRelatedList($modCyP, 'CobroPago', Array('ADD'),'get_dependents_list');
			if ($modPrjTask) $modPrjTask->setRelatedList($modCyP, 'CobroPago', Array('ADD'),'get_dependents_list');
		} else if($event_type == 'module.disabled') {
			// TODO Handle actions when this module is disabled.
		} else if($event_type == 'module.enabled') {
			// TODO Handle actions when this module is enabled.
		} else if($event_type == 'module.preuninstall') {
			// TODO Handle actions when this module is about to be deleted.
		} else if($event_type == 'module.preupdate') {
			// TODO Handle actions before this module is updated.
		} else if($event_type == 'module.postupdate') {
			// TODO Handle actions after this module is updated.
		}
	}

	/** 
	 * Handle saving related module information.
	 * NOTE: This function has been added to CRMEntity (base class).
	 * You can override the behavior by re-defining it here.
	 */
	// function save_related_module($module, $crmid, $with_module, $with_crmid) { }
	
	/**
	 * Handle deleting related module information.
	 * NOTE: This function has been added to CRMEntity (base class).
	 * You can override the behavior by re-defining it here.
	 */
	//function delete_related_module($module, $crmid, $with_module, $with_crmid) { }

	/**
	 * Handle getting related list information.
	 * NOTE: This function has been added to CRMEntity (base class).
	 * You can override the behavior by re-defining it here.
	 */
	//function get_related_list($id, $cur_tab_id, $rel_tab_id, $actions=false) { }

	/**
	 * Handle getting dependents list information.
	 * NOTE: This function has been added to CRMEntity (base class).
	 * You can override the behavior by re-defining it here.
	 */
	//function get_dependents_list($id, $cur_tab_id, $rel_tab_id, $actions=false) { }

	function get_activities($id, $cur_tab_id, $rel_tab_id, $actions)
	{
		global $log, $singlepane_view,$currentModule,$current_user;
		$log->debug("Entering get_activities(".$id.") method ...");
		global $mod_strings;
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
				$button .= "<input title='".getTranslatedString('LBL_NEW'). " ". getTranslatedString('LBL_TODO', $related_module) ."' class='crmbutton small create'" .
					" onclick='this.form.action.value=\"EditView\";this.form.module.value=\"$related_module\";this.form.return_module.value=\"$this_module\";this.form.activity_mode.value=\"Task\";' type='submit' name='button'" .
					" value='". getTranslatedString('LBL_ADD_NEW'). " " . getTranslatedString('LBL_TODO', $related_module) ."'>&nbsp;";
				$button .= "<input title='".getTranslatedString('LBL_NEW'). " ". getTranslatedString('LBL_TODO', $related_module) ."' class='crmbutton small create'" .
					" onclick='this.form.action.value=\"EditView\";this.form.module.value=\"$related_module\";this.form.return_module.value=\"$this_module\";this.form.activity_mode.value=\"Events\";' type='submit' name='button'" .
					" value='". getTranslatedString('LBL_ADD_NEW'). " " . getTranslatedString('LBL_EVENT', $related_module) ."'>";
			}
		}

		$query = "SELECT vtiger_activity.*,
		    vtiger_seactivityrel.*, vtiger_contactdetails.lastname,
		    vtiger_contactdetails.firstname, vtiger_cntactivityrel.*,
		    vtiger_crmentity.crmid, vtiger_crmentity.smownerid,
		    vtiger_crmentity.modifiedtime,
		    case when (vtiger_users.user_name not like '') then vtiger_users.user_name else vtiger_groups.groupname end as user_name,
		    vtiger_recurringevents.recurringtype
		    from vtiger_activity
		    inner join vtiger_seactivityrel on vtiger_seactivityrel.activityid=vtiger_activity.activityid
		    inner join vtiger_crmentity on vtiger_crmentity.crmid=vtiger_activity.activityid
		    left join vtiger_cntactivityrel on vtiger_cntactivityrel.activityid = vtiger_activity.activityid
		    left join vtiger_contactdetails on vtiger_contactdetails.contactid = vtiger_cntactivityrel.contactid
		    inner join vtiger_cobropago on vtiger_cobropago.cobropagoid=vtiger_seactivityrel.crmid
		    left join vtiger_users on vtiger_users.id=vtiger_crmentity.smownerid
		    left join vtiger_groups on vtiger_groups.groupid=vtiger_crmentity.smownerid
		    left outer join vtiger_recurringevents on vtiger_recurringevents.activityid=vtiger_activity.activityid
		    where vtiger_seactivityrel.crmid=".$id." and vtiger_crmentity.deleted=0 
		    and ((vtiger_activity.activitytype='Task' and vtiger_activity.status not in ('Completed','Deferred'))
		    or (vtiger_activity.activitytype in ('Meeting','Call') and  vtiger_activity.eventstatus not in ('','Held'))) ";
					
		$return_value = GetRelatedList($this_module, $related_module, $other, $query, $button, $returnset); 
		
		if($return_value == null) $return_value = Array();
		$return_value['CUSTOM_BUTTON'] = $button;
		
		$log->debug("Exiting get_activities method ...");
		return $return_value;
	}

	/**	Function used to get the Payments Stage history of the CobroPago
	 *	@param $id - cobropagoid
	 *	return $return_data - array with header and the entries in format Array('header'=>$header,'entries'=>$entries_list) where as $header and $entries_list are array which contains all the column values of an row
	 */
	function get_payment_history($id)
	{	
		global $log;
		$log->debug("Entering get_stage_history(".$id.") method ...");

		global $adb;
		global $mod_strings;
		global $app_strings;

		$query = 'select vtiger_potstagehistory.*, vtiger_cobropago.reference from vtiger_potstagehistory inner join vtiger_cobropago on vtiger_cobropago.cobropagoid = vtiger_potstagehistory.cobropagoid inner join vtiger_crmentity on vtiger_crmentity.crmid = vtiger_cobropago.cobropagoid where vtiger_crmentity.deleted = 0 and vtiger_cobropago.cobropagoid = ?';
		$result=$adb->pquery($query, array($id));
		$noofrows = $adb->num_rows($result);

		$header[] = $app_strings['LBL_AMOUNT'];
		$header[] = $app_strings['LBL_SALES_STAGE'];
		$header[] = $app_strings['LBL_PROBABILITY'];
		$header[] = $app_strings['LBL_CLOSE_DATE'];
		$header[] = $app_strings['LBL_LAST_MODIFIED'];

		//Getting the field permission for the current user. 1 - Not Accessible, 0 - Accessible
		//Sales Stage, Expected Close Dates are mandatory fields. So no need to do security check to these fields.
		global $current_user;

		//If field is accessible then getFieldVisibilityPermission function will return 0 else return 1
		$amount_access = (getFieldVisibilityPermission('CobroPago', $current_user->id, 'amount') != '0')? 1 : 0;
		$probability_access = (getFieldVisibilityPermission('CobroPago', $current_user->id, 'probability') != '0')? 1 : 0;
		$picklistarray = getAccessPickListValues('CobroPago');

		$potential_stage_array = $picklistarray['sales_stage'];
		//- ==> picklist field is not permitted in profile
		//Not Accessible - picklist is permitted in profile but picklist value is not permitted
		$error_msg = 'Not Accessible';

		while($row = $adb->fetch_array($result))
		{
			$entries = Array();

			$entries[] = ($amount_access != 1)? $row['amount'] : 0;
			$entries[] = (in_array($row['stage'], $potential_stage_array))? $row['stage']: $error_msg;
			$entries[] = ($probability_access != 1) ? $row['probability'] : 0;
			$entries[] = getDisplayDate($row['closedate']);
			$entries[] = getDisplayDate($row['lastmodified']);

			$entries_list[] = $entries;
		}

		$return_data = Array('header'=>$header,'entries'=>$entries_list);

	 	$log->debug("Exiting get_stage_history method ...");

		return $return_data;
	}
	
	/**
	* Function to get CobroPago related Task & Event which have activity type Held, Completed or Deferred.
	* @param  integer   $id 
	* returns related Task or Event record in array format
	*/
	function get_history($id, $cur_tab_id, $rel_tab_id, $actions)
	{
			global $log;
			$log->debug("Entering get_history(".$id.") method ...");
			$query = "SELECT vtiger_activity.activityid, vtiger_activity.subject, vtiger_activity.status,
		vtiger_activity.eventstatus, vtiger_activity.activitytype,vtiger_activity.date_start, 
		vtiger_activity.due_date, vtiger_activity.time_start,vtiger_activity.time_end,
		vtiger_crmentity.modifiedtime, vtiger_crmentity.createdtime, 
		vtiger_crmentity.description,case when (vtiger_users.user_name not like '') then vtiger_users.user_name else vtiger_groups.groupname end as user_name 
				from vtiger_activity
				inner join vtiger_seactivityrel on vtiger_seactivityrel.activityid=vtiger_activity.activityid
				inner join vtiger_crmentity on vtiger_crmentity.crmid=vtiger_activity.activityid
				left join vtiger_groups on vtiger_groups.groupid=vtiger_crmentity.smownerid
				left join vtiger_users on vtiger_users.id=vtiger_crmentity.smownerid
				where (vtiger_activity.activitytype = 'Meeting' or vtiger_activity.activitytype='Call' or vtiger_activity.activitytype='Task')
				and (vtiger_activity.status = 'Completed' or vtiger_activity.status = 'Deferred' or (vtiger_activity.eventstatus = 'Held' and vtiger_activity.eventstatus != ''))
				and vtiger_seactivityrel.crmid=".$id."
                                and vtiger_crmentity.deleted = 0";
		//Don't add order by, because, for security, one more condition will be added with this query in include/RelatedListView.php

		$log->debug("Exiting get_history method ...");
		return getHistory('CobroPago',$query,$id);
	}
	
	function get_history_cobropago($cobropagoid){
		global $log, $adb;
		$log->debug("Entering into get_history_cobropago($cobropagoid) method ...");

		$query="select reference,update_log from vtiger_cobropago where cobropagoid=?";
		$result=$adb->pquery($query, array($cobropagoid));
		$update_log = $adb->query_result($result,0,"update_log");

		$splitval = split('--//--',trim($update_log,'--//--'));

		$header[] = $adb->query_result($result,0,"reference");

		$return_value = Array('header'=>$header,'entries'=>$splitval);

		$log->debug("Exiting from get_history_cobropago($cobropagoid) method ...");

		return $return_value;
	}

	
	/**	
	 *	This function check is this payment is paid or not, to haver permission to edit
	**/
	function permissiontoedit()
	{
		global $log,$current_user,$adb;
		$log->debug("Entering permissiontoedit() method ...");
		
		require('user_privileges/user_privileges_'.$current_user->id.'.php');
		$Block_paid = $adb->query_result($adb->query("select block_paid from vtiger_cobropagoconfig"),0,0);
		
		if ($is_admin or $Block_paid!='on') return true;
	
		if($this->column_fields['paid'] == 1)
			$permiso = false;
		else
			$permiso = true;

		$log->debug("Exiting permissiontoedit method ...");
		return $permiso;
	}

}
?>
