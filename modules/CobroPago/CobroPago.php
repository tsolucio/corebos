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
	var $HasDirectImageField = false;
	/**
	 * Mandatory table for supporting custom fields.
	 */
	var $customFieldTable = Array('vtiger_cobropagocf', 'cobropagoid');
	// Uncomment the line below to support custom field columns on related lists
	var $related_tables = Array('vtiger_cobropagocf'=>array('cobropagoid','vtiger_cobropago', 'cobropagoid'));

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
		'CyP No'=>Array('cobropago'=>'cyp_no'),
		'Reference'=>Array('cobropago'=>'reference'),
		'PaymentMode'=>Array('cobropago'=>'paymentmode'),
		'Amount'=>Array('cobropago'=>'amount'),
		'DueDate'=>Array('cobropago'=>'duedate'),
		'Assigned To' => Array('crmentity' => 'smownerid')
	);
	var $list_fields_name = Array(
		'CyP No'=>'cyp_no',
		'Reference'=>'reference',
		'PaymentMode'=>'paymentmode',
		'Amount'=>'amount',
		'DueDate'=>'duedate',
		'Assigned To' => 'assigned_user_id'
	);

	// Make the field link to detail view from list view (Fieldname)
	var $list_link_field = 'cyp_no';

	// For Popup listview and UI type support
	var $search_fields = Array(
		'CyP No'=>Array('cobropago'=>'cyp_no'),
		'Reference'=>Array('cobropago'=>'reference'),
		'PaymentMode'=>Array('cobropago'=>'paymentmode'),
		'Amount'=>Array('cobropago'=>'amount'),
		'DueDate'=>Array('cobropago'=>'duedate')
	);
	var $search_fields_name = Array(
		'CyP No'=>'cyp_no',
		'Reference'=>'reference',
		'PaymentMode'=>'paymentmode',
		'Amount'=>'amount',
		'DueDate'=>'duedate'
	);

	// For Popup window record selection
	var $popup_fields = Array('cyp_no');

	// Placeholder for sort fields - All the fields will be initialized for Sorting through initSortFields
	var $sortby_fields = Array();

	// For Alphabetical search
	var $def_basicsearch_col = 'reference';

	// Column value to use on detail view record text display
	var $def_detailview_recname = 'cyp_no';

	// Required Information for enabling Import feature
	var $required_fields = Array('reference'=>1);

	// Callback function list during Importing
	var $special_functions = Array('set_import_assigned_user');

	var $default_order_by = 'cyp_no';
	var $default_sort_order='ASC';
	// Used when enabling/disabling the mandatory fields for the module.
	// Refers to vtiger_field.fieldname values.
	var $mandatory_fields = Array('createdtime', 'modifiedtime','cyp_no');

	function save($module, $fileid = '') {
		global $adb, $current_user;
		$update_after = false;
		if ($this->column_fields['paid'] == 'on' or $this->column_fields['paid'] == '1'){
			if($this->mode != 'edit'){
				$update_after = true;
				$update_log = getTranslatedString('Payment Paid','CobroPago').$current_user->user_name.getTranslatedString('PaidOn','CobroPago').date("l dS F Y h:i:s A").'--//--';
			}else{
				$SQL = 'SELECT paid,update_log FROM vtiger_cobropago WHERE cobropagoid=?';
				$result = $adb->pquery($SQL,array($this->id));
				$old_paid = $adb->query_result($result,0,'paid');
				if ($old_paid == '0'){
					$update_after = true;
					$update_log = $adb->query_result($result,0,'update_log');
					$update_log .= getTranslatedString('Payment Paid','CobroPago').$current_user->user_name.getTranslatedString('PaidOn','CobroPago').date("l dS F Y h:i:s A").'--//--';
				}
			}
		}
		parent::save($module, $fileid);
		if ($update_after){
			$SQL_UPD = 'UPDATE vtiger_cobropago SET update_log=? WHERE cobropagoid=?';
			$adb->pquery($SQL_UPD,array($update_log,$this->id));
		}
	}

	function save_module($module) {
		global $current_user,$log,$adb;
		if ($this->HasDirectImageField) {
			$this->insertIntoAttachment($this->id,$module);
		}
		$cypid = $this->id;
		$data = $this->column_fields;
		// Entity has been saved, take next action
		if (empty($data['register']) and $this->mode=='') {
			$refDateValue = new DateTimeField();  // right now
			$this->column_fields['register'] = $refDateValue->getDisplayDate();
			$adb->pquery('update vtiger_cobropago set register=? where cobropagoid=?',array($refDateValue->getDBInsertDateValue(),$cypid));
		}
		$currencyid=fetchCurrency($current_user->id);
		$rate_symbol = getCurrencySymbolandCRate($currencyid);
		$rate = $rate_symbol['rate'];
		$value=0;
		if(isset($data['amount']) and isset($data['cost'])) {
			$value = CurrencyField::convertToDollar($data['amount']-$data['cost'],$rate);
		}
		$adb->pquery('update vtiger_cobropago set benefit=? where cobropagoid=?',array($value,$cypid));

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
		$rs = $adb->pquery('select sum(amount) as suma from vtiger_cobropago inner join vtiger_crmentity on crmid=cobropagoid where deleted=0 and credit=1 and parent_id=?',array($pid));
		$sumamountcredit =$adb->query_result($rs,0,0);
		$rs = $adb->pquery('select sum(amount) as suma from vtiger_cobropago inner join vtiger_crmentity on crmid=cobropagoid where deleted=0 and credit=0 and parent_id=?',array($pid));
		$sumamountdebit =$adb->query_result($rs,0,0);
		$rs = $adb->pquery("select sum(amount) as suma from vtiger_cobropago inner join vtiger_crmentity on crmid=cobropagoid where deleted=0 and credit=1 and paid='0' and parent_id=?",array($pid));
		$sumpendingcredit=$adb->query_result($rs,0,0);
		$rs = $adb->pquery("select sum(amount) as suma from vtiger_cobropago inner join vtiger_crmentity on crmid=cobropagoid where deleted=0 and credit=0 and paid='0' and parent_id=?",array($pid));
		$sumpendingdebit=$adb->query_result($rs,0,0);
		$sumamount=$sumamountcredit-$sumamountdebit;
		$sumpending=$sumpendingcredit-$sumpendingdebit;
		$balance=$sumamount-$sumpending;
		$adb->pquery("update vtiger_account set balance=?,totalamount=?,totalpending=? where accountid=?",array($balance,$sumamount,$sumpending,$pid));
	  }
	  if ($parent_module=='Contacts' and self::contact_control_installed()) {
		$rs = $adb->pquery('select sum(amount) as suma from vtiger_cobropago inner join vtiger_crmentity on crmid=cobropagoid where deleted=0 and credit=1 and parent_id=?',array($pid));
		$sumamountcredit =$adb->query_result($rs,0,0);
		$rs = $adb->pquery('select sum(amount) as suma from vtiger_cobropago inner join vtiger_crmentity on crmid=cobropagoid where deleted=0 and credit=0 and parent_id=?',array($pid));
		$sumamountdebit =$adb->query_result($rs,0,0);
		$rs = $adb->pquery("select sum(amount) as suma from vtiger_cobropago inner join vtiger_crmentity on crmid=cobropagoid where deleted=0 and credit=1 and paid='0' and parent_id=?",array($pid));
		$sumpendingcredit=$adb->query_result($rs,0,0);
		$rs = $adb->pquery("select sum(amount) as suma from vtiger_cobropago inner join vtiger_crmentity on crmid=cobropagoid where deleted=0 and credit=0 and paid='0' and parent_id=?",array($pid));
		$sumpendingdebit=$adb->query_result($rs,0,0);
		$sumamount=$sumamountcredit-$sumamountdebit;
		$sumpending=$sumpendingcredit-$sumpendingdebit;
		$balance=$sumamount-$sumpending;
		$adb->pquery("update vtiger_contactdetails set balance=?,totalamount=?,totalpending=? where contactid=?",array($balance,$sumamount,$sumpending,$pid));
	  }
	  if ($parent_module=='Vendors' and self::vendor_control_installed()) {
		$rs = $adb->pquery('select sum(amount) as suma from vtiger_cobropago inner join vtiger_crmentity on crmid=cobropagoid where deleted=0 and credit=1 and parent_id=?',array($pid));
		$sumamountcredit =$adb->query_result($rs,0,0);
		$rs = $adb->pquery('select sum(amount) as suma from vtiger_cobropago inner join vtiger_crmentity on crmid=cobropagoid where deleted=0 and credit=0 and parent_id=?',array($pid));
		$sumamountdebit =$adb->query_result($rs,0,0);
		$rs = $adb->pquery("select sum(amount) as suma from vtiger_cobropago inner join vtiger_crmentity on crmid=cobropagoid where deleted=0 and credit=1 and paid='0' and parent_id=?",array($pid));
		$sumpendingcredit=$adb->query_result($rs,0,0);
		$rs = $adb->pquery("select sum(amount) as suma from vtiger_cobropago inner join vtiger_crmentity on crmid=cobropagoid where deleted=0 and credit=0 and paid='0' and parent_id=?",array($pid));
		$sumpendingdebit=$adb->query_result($rs,0,0);
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
			$this->setModuleSeqNumber('configure', $modulename, 'PAY-', '0000001');
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

	/**	Function used to get the Payments Stage history of the CobroPago
	 *	@param $id - cobropagoid
	 *	return $return_data - array with header and the entries in format Array('header'=>$header,'entries'=>$entries_list) where as $header and $entries_list are array which contains all the column values of an row
	 */
	function get_payment_history($id)
	{
		global $log, $adb, $app_strings;
		$log->debug("Entering get_stage_history(".$id.") method ...");

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

		$return_data = Array('header'=>$header,'entries'=>$entries_list,'navigation'=>array('',''));

		$log->debug("Exiting get_stage_history method ...");
		return $return_data;
	}

	function get_history_cobropago($cobropagoid){
		global $log, $adb;
		$log->debug("Entering into get_history_cobropago($cobropagoid) method ...");

		$query="select reference,update_log from vtiger_cobropago where cobropagoid=?";
		$result=$adb->pquery($query, array($cobropagoid));
		$update_log = $adb->query_result($result,0,"update_log");

		$splitval = explode('--//--',trim($update_log,'--//--'));

		$header[] = $adb->query_result($result,0,"reference");

		$return_value = Array('header'=>$header,'entries'=>$splitval,'navigation'=>array('',''));

		$log->debug("Exiting from get_history_cobropago($cobropagoid) method ...");

		return $return_value;
	}

	function preEditCheck($request,$smarty) {
		global $log, $app_strings;
		$isduplicate = isset($_REQUEST['isDuplicate']) ? $_REQUEST['isDuplicate'] : null;
		if (!$this->permissiontoedit() and $isduplicate != 'true') {
			$log->debug("You don't have permission to edit cobropago");
			$smarty->assign('APP', $app_strings);
			$smarty->display('modules/Vtiger/OperationNotPermitted.tpl');
			exit;
		}
		list($request,$smarty,$void) = cbEventHandler::do_filter('corebos.filter.preEditCheck', array($request,$smarty,$this));
		return '';
	}

	/**
	 *	This function check is this payment is paid or not, to haver permission to edit
	**/
	function permissiontoedit()
	{
		global $log,$current_user,$adb;
		$log->debug("Entering permissiontoedit() method ...");

		$res = $adb->pquery("select block_paid from vtiger_cobropagoconfig",array());
		$Block_paid = $adb->query_result($res,0,'block_paid');

		if (is_admin($current_user) or $Block_paid!='on') return true;

		if($this->column_fields['paid'] == 1)
			$permiso = false;
		else
			$permiso = true;

		$log->debug("Exiting permissiontoedit method ...");
		return $permiso;
	}
}
?>
