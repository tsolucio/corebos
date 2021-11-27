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
require_once 'modules/Invoice/Invoice.php';

class CobroPago extends CRMEntity {
	public $table_name = 'vtiger_cobropago';
	public $table_index= 'cobropagoid';
	public $column_fields = array();

	/** Indicator if this is a custom module or standard module */
	public $IsCustomModule = true;
	public $HasDirectImageField = false;
	public $moduleIcon = array('library' => 'utility', 'containerClass' => 'slds-icon_container slds-icon-standard-contract', 'class' => 'slds-icon slds-box--xx-small ', 'icon'=>'money');

	/**
	 * Mandatory table for supporting custom fields.
	 */
	public $customFieldTable = array('vtiger_cobropagocf', 'cobropagoid');
	// Uncomment the line below to support custom field columns on related lists
	public $related_tables = array('vtiger_cobropagocf'=>array('cobropagoid','vtiger_cobropago', 'cobropagoid'));

	/**
	 * Mandatory for Saving, Include tables related to this module.
	 */
	public $tab_name = array('vtiger_crmentity', 'vtiger_cobropago', 'vtiger_cobropagocf');

	/**
	 * Mandatory for Saving, Include tablename and tablekey columnname here.
	 */
	public $tab_name_index = array(
		'vtiger_crmentity' => 'crmid',
		'vtiger_cobropago'   => 'cobropagoid',
		'vtiger_cobropagocf' => 'cobropagoid',
	);

	/**
	 * Mandatory for Listing (Related listview)
	 */
	public $list_fields = array(
		'CyP No'=>array('cobropago'=>'cyp_no'),
		'Reference'=>array('cobropago'=>'reference'),
		'PaymentMode'=>array('cobropago'=>'paymentmode'),
		'Amount'=>array('cobropago'=>'amount'),
		'DueDate'=>array('cobropago'=>'duedate'),
		'Assigned To' => array('crmentity' => 'smownerid')
	);
	public $list_fields_name = array(
		'CyP No'=>'cyp_no',
		'Reference'=>'reference',
		'PaymentMode'=>'paymentmode',
		'Amount'=>'amount',
		'DueDate'=>'duedate',
		'Assigned To' => 'assigned_user_id'
	);

	// Make the field link to detail view from list view (Fieldname)
	public $list_link_field = 'cyp_no';

	// For Popup listview and UI type support
	public $search_fields = array(
		'CyP No'=>array('cobropago'=>'cyp_no'),
		'Reference'=>array('cobropago'=>'reference'),
		'PaymentMode'=>array('cobropago'=>'paymentmode'),
		'Amount'=>array('cobropago'=>'amount'),
		'DueDate'=>array('cobropago'=>'duedate')
	);
	public $search_fields_name = array(
		'CyP No'=>'cyp_no',
		'Reference'=>'reference',
		'PaymentMode'=>'paymentmode',
		'Amount'=>'amount',
		'DueDate'=>'duedate'
	);

	// For Popup window record selection
	public $popup_fields = array('cyp_no');

	// Placeholder for sort fields - All the fields will be initialized for Sorting through initSortFields
	public $sortby_fields = array();

	// For Alphabetical search
	public $def_basicsearch_col = 'reference';

	// Column value to use on detail view record text display
	public $def_detailview_recname = 'cyp_no';

	// Required Information for enabling Import feature
	public $required_fields = array('reference'=>1);

	// Callback function list during Importing
	public $special_functions = array('set_import_assigned_user');

	public $default_order_by = 'cyp_no';
	public $default_sort_order='ASC';
	// Used when enabling/disabling the mandatory fields for the module.
	// Refers to vtiger_field.fieldname values.
	public $mandatory_fields = array('createdtime', 'modifiedtime','cyp_no');

	public function save($module, $fileid = '') {
		global $adb, $current_user;
		$update_after = false;
		if ($this->column_fields['paid'] == 'on' || $this->column_fields['paid'] == '1') {
			if ($this->mode != 'edit') {
				$update_after = true;
				$update_log = getTranslatedString('Payment Paid', 'CobroPago').$current_user->user_name.getTranslatedString('PaidOn', 'CobroPago');
				$update_log .= date('Y-m-d H:i:s').'--//--';
			} else {
				$result = $adb->pquery('SELECT paid,update_log FROM vtiger_cobropago WHERE cobropagoid=?', array($this->id));
				$old_paid = $adb->query_result($result, 0, 'paid');
				if ($old_paid == '0') {
					$update_after = true;
					$update_log = $adb->query_result($result, 0, 'update_log');
					$update_log .= getTranslatedString('Payment Paid', 'CobroPago').$current_user->user_name.getTranslatedString('PaidOn', 'CobroPago');
					$update_log .= date('Y-m-d H:i:s').'--//--';
				}
			}
		}
		parent::save($module, $fileid);
		if ($update_after) {
			$adb->pquery('UPDATE vtiger_cobropago SET update_log=? WHERE cobropagoid=?', array($update_log, $this->id));
		}
	}

	public function save_module($module) {
		global $current_user, $adb;
		if ($this->HasDirectImageField) {
			$this->insertIntoAttachment($this->id, $module);
		}
		$cypid = $this->id;
		$data = $this->column_fields;
		// Entity has been saved, take next action
		if (empty($data['register']) && $this->mode=='') {
			$refDateValue = new DateTimeField();  // right now
			$this->column_fields['register'] = $refDateValue->getDisplayDate();
			$adb->pquery('update vtiger_cobropago set register=? where cobropagoid=?', array($refDateValue->getDBInsertDateValue(), $cypid));
		}
		$currencyid=fetchCurrency($current_user->id);
		$rate_symbol = getCurrencySymbolandCRate($currencyid);
		$rate = $rate_symbol['rate'];
		$value=0;
		if (isset($data['amount']) && isset($data['cost'])) {
			$am = CurrencyField::convertToDBFormat($data['amount']);
			$ct = CurrencyField::convertToDBFormat($data['cost']);
			$value = CurrencyField::convertToDollar($am-$ct, $rate);
		}
		$adb->pquery('update vtiger_cobropago set benefit=? where cobropagoid=?', array($value, $cypid));

		$relatedId = $this->column_fields['related_id'];
		if (!empty($relatedId) && self::invoice_control_installed()) {
			$relatedId_seType = getSalesEntityType($relatedId);
			$related_focus = CRMEntity::getInstance($relatedId_seType);
			$related_focus->retrieve_entity_info($relatedId, $relatedId_seType);
			Invoice::updateAmountDue($relatedId, $related_focus->column_fields, 'CobroPago');
		}
		// Calculate related module balance
		$this->calculateRelatedTotals($this->column_fields['parent_id']);
	}

	public static function calculateRelatedTotals($pid) {
		$crmEntityTable = CRMEntity::getcrmEntityTableAlias('CobroPago');
		global $adb;
		$parent_module = getSalesEntityType($pid);
		if ($parent_module=='Accounts' && self::account_control_installed()) {
			$rs = $adb->pquery(
				'select sum(amount) as suma from vtiger_cobropago inner join '.$crmEntityTable.' on vtiger_crmentity.crmid=cobropagoid where vtiger_crmentity.deleted=0 and credit=1 and parent_id=?',
				array($pid)
			);
			$sumamountcredit =$adb->query_result($rs, 0, 0);
			$rs = $adb->pquery(
				'select sum(amount) as suma from vtiger_cobropago inner join '.$crmEntityTable.' on vtiger_crmentity.crmid=cobropagoid where vtiger_crmentity.deleted=0 and credit=0 and parent_id=?',
				array($pid)
			);
			$sumamountdebit =$adb->query_result($rs, 0, 0);
			$rs = $adb->pquery(
				"select sum(amount) as suma from vtiger_cobropago inner join ".$crmEntityTable." on vtiger_crmentity.crmid=cobropagoid where vtiger_crmentity.deleted=0 and credit=1 and paid='0' and parent_id=?",
				array($pid)
			);
			$sumpendingcredit=$adb->query_result($rs, 0, 0);
			$rs = $adb->pquery(
				"select sum(amount) as suma from vtiger_cobropago inner join ".$crmEntityTable." on vtiger_crmentity.crmid=cobropagoid where vtiger_crmentity.deleted=0 and credit=0 and paid='0' and parent_id=?",
				array($pid)
			);
			$sumpendingdebit=$adb->query_result($rs, 0, 0);
			$sumamount=$sumamountcredit-$sumamountdebit;
			$sumpending=$sumpendingcredit-$sumpendingdebit;
			$balance=$sumamount-$sumpending;
			$adb->pquery('update vtiger_account set balance=?,totalamount=?,totalpending=? where accountid=?', array($balance,$sumamount,$sumpending,$pid));
		}
		if ($parent_module=='Contacts' && self::contact_control_installed()) {
			$rs = $adb->pquery(
				'select sum(amount) as suma from vtiger_cobropago inner join '.$crmEntityTable.' on vtiger_crmentity.crmid=cobropagoid where vtiger_crmentity.deleted=0 and credit=1 and parent_id=?',
				array($pid)
			);
			$sumamountcredit =$adb->query_result($rs, 0, 0);
			$rs = $adb->pquery(
				'select sum(amount) as suma from vtiger_cobropago inner join '.$crmEntityTable.' on vtiger_crmentity.crmid=cobropagoid where vtiger_crmentity.deleted=0 and credit=0 and parent_id=?',
				array($pid)
			);
			$sumamountdebit =$adb->query_result($rs, 0, 0);
			$rs = $adb->pquery(
				"select sum(amount) as suma from vtiger_cobropago inner join ".$crmEntityTable." on vtiger_crmentity.crmid=cobropagoid where vtiger_crmentity.deleted=0 and credit=1 and paid='0' and parent_id=?",
				array($pid)
			);
			$sumpendingcredit=$adb->query_result($rs, 0, 0);
			$rs = $adb->pquery(
				"select sum(amount) as suma from vtiger_cobropago inner join ".$crmEntityTable." on vtiger_crmentity.crmid=cobropagoid where vtiger_crmentity.deleted=0 and credit=0 and paid='0' and parent_id=?",
				array($pid)
			);
			$sumpendingdebit=$adb->query_result($rs, 0, 0);
			$sumamount=$sumamountcredit-$sumamountdebit;
			$sumpending=$sumpendingcredit-$sumpendingdebit;
			$balance=$sumamount-$sumpending;
			$adb->pquery('update vtiger_contactdetails set balance=?,totalamount=?,totalpending=? where contactid=?', array($balance,$sumamount,$sumpending,$pid));
		}
		if ($parent_module=='Vendors' && self::vendor_control_installed()) {
			$rs = $adb->pquery(
				'select sum(amount) as suma from vtiger_cobropago inner join '.$crmEntityTable.' on vtiger_crmentity.crmid=cobropagoid where vtiger_crmentity.deleted=0 and credit=1 and parent_id=?',
				array($pid)
			);
			$sumamountcredit =$adb->query_result($rs, 0, 0);
			$rs = $adb->pquery(
				'select sum(amount) as suma from vtiger_cobropago inner join '.$crmEntityTable.' on vtiger_crmentity.crmid=cobropagoid where vtiger_crmentity.deleted=0 and credit=0 and parent_id=?',
				array($pid)
			);
			$sumamountdebit =$adb->query_result($rs, 0, 0);
			$rs = $adb->pquery(
				"select sum(amount) as suma from vtiger_cobropago inner join ".$crmEntityTable." on vtiger_crmentity.crmid=cobropagoid where vtiger_crmentity.deleted=0 and credit=1 and paid='0' and parent_id=?",
				array($pid)
			);
			$sumpendingcredit=$adb->query_result($rs, 0, 0);
			$rs = $adb->pquery(
				"select sum(amount) as suma from vtiger_cobropago inner join ".$crmEntityTable." on vtiger_crmentity.crmid=cobropagoid where vtiger_crmentity.deleted=0 and credit=0 and paid='0' and parent_id=?",
				array($pid)
			);
			$sumpendingdebit=$adb->query_result($rs, 0, 0);
			$sumamount=$sumamountcredit-$sumamountdebit;
			$sumpending=$sumpendingcredit-$sumpendingdebit;
			$balance=$sumamount-$sumpending;
			$adb->pquery('update vtiger_vendor set balance=?,totalamount=?,totalpending=? where vendorid=?', array($balance, $sumamount, $sumpending, $pid));
		}
	}

	public function trash($module, $record) {
		global $adb;
		parent::trash($module, $record);
		$rs = $adb->pquery('select related_id,parent_id from vtiger_cobropago where cobropagoid=?', array($record));
		if ($rs && $adb->num_rows($rs)==1) {
			$relatedId = $adb->query_result($rs, 0, 'related_id');
			$pid = $adb->query_result($rs, 0, 'parent_id');
			if (!empty($relatedId) && isRecordExists($relatedId) && self::invoice_control_installed()) {
				$relatedId_seType = getSalesEntityType($relatedId);
				$related_focus = CRMEntity::getInstance($relatedId_seType);
				$related_focus->retrieve_entity_info($relatedId, $relatedId_seType);
				Invoice::updateAmountDue($relatedId, $related_focus->column_fields, 'CobroPago');
			}
			// Calculate related module balance
			CobroPago::calculateRelatedTotals($pid);
		}
	}

	public function unlinkRelationship($id, $return_module, $return_id) {
		global $adb;
		$rs = $adb->pquery('select related_id,parent_id from vtiger_cobropago where cobropagoid=?', array($id));
		parent::unlinkRelationship($id, $return_module, $return_id);
		if ($rs && $adb->num_rows($rs)==1) {
			$relatedId = $adb->query_result($rs, 0, 'related_id');
			$pid = $adb->query_result($rs, 0, 'parent_id');
			if (!empty($relatedId) && self::invoice_control_installed()) {
				$relatedId_seType = getSalesEntityType($relatedId);
				$related_focus = CRMEntity::getInstance($relatedId_seType);
				$related_focus->retrieve_entity_info($relatedId, $relatedId_seType);
				Invoice::updateAmountDue($relatedId, $related_focus->column_fields, 'CobroPago');
			}
			// Calculate related module balance
			CobroPago::calculateRelatedTotals($pid);
		}
	}

	public static function account_control_installed() {
		global $adb;
		$cnacc=$adb->getColumnNames('vtiger_account');
		return (in_array('balance', $cnacc) && in_array('totalamount', $cnacc) && in_array('totalpending', $cnacc));
	}
	public static function contact_control_installed() {
		global $adb;
		$cnacc=$adb->getColumnNames('vtiger_contactdetails');
		return (in_array('balance', $cnacc) && in_array('totalamount', $cnacc) && in_array('totalpending', $cnacc));
	}
	public static function vendor_control_installed() {
		global $adb;
		$cnacc=$adb->getColumnNames('vtiger_vendor');
		return (in_array('balance', $cnacc) && in_array('totalamount', $cnacc) && in_array('totalpending', $cnacc));
	}
	public static function invoice_control_installed() {
		global $adb;
		$cninv=$adb->getColumnNames('vtiger_invoice');
		return (in_array('amount_due', $cninv) && in_array('amount_paid', $cninv) && in_array('total_amount', $cninv));
	}

	/**
	 * Invoked when special actions are performed on the module.
	 * @param string Module name
	 * @param string Event Type (module.postinstall, module.disabled, module.enabled, module.preuninstall)
	 */
	public function vtlib_handler($modulename, $event_type) {
		if ($event_type == 'module.postinstall') {
			// Handle post installation actions
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

			if ($modAccounts) {
				$modAccounts->setRelatedList($modCyP, 'CobroPago', array('ADD'), 'get_dependents_list');
			}
			if ($modContacts) {
				$modContacts->setRelatedList($modCyP, 'CobroPago', array('ADD'), 'get_dependents_list');
			}
			if ($modVnd) {
				$modVnd->setRelatedList($modCyP, 'CobroPago', array('ADD'), 'get_dependents_list');
			}
			if ($modInvoice) {
				$modInvoice->setRelatedList($modCyP, 'CobroPago', array('ADD'), 'get_dependents_list');
			}
			if ($modInvoice) {
				$modInvoice->addLink(
					'DETAILVIEWBASIC',
					'Add Payment',
					'index.php?module=CobroPago&action=EditView&related_id=$RECORD$&return_module=Invoice&return_id=$RECORD$&return_action=DetailView'
				);
			}
			if ($modSO) {
				$modSO->setRelatedList($modCyP, 'CobroPago', array('ADD'), 'get_dependents_list');
			}
			if ($modSO) {
				$modSO->addLink(
					'DETAILVIEWBASIC',
					'Add Payment',
					'index.php?module=CobroPago&action=EditView&related_id=$RECORD$&return_module=SalesOrder&return_id=$RECORD$&return_action=DetailView'
				);
			}
			if ($modPO) {
				$modPO->setRelatedList($modCyP, 'CobroPago', array('ADD'), 'get_dependents_list');
			}
			if ($modPO) {
				$modPO->addLink(
					'DETAILVIEWBASIC',
					'Add Payment',
					'index.php?module=CobroPago&action=EditView&related_id=$RECORD$&return_module=PurchaseOrder&return_id=$RECORD$&return_action=DetailView'
				);
			}
			if ($modQt) {
				$modQt->setRelatedList($modCyP, 'CobroPago', array('ADD'), 'get_dependents_list');
			}
			if ($modQt) {
				$modQt->addLink(
					'DETAILVIEWBASIC',
					'Add Payment',
					'index.php?module=CobroPago&action=EditView&related_id=$RECORD$&return_module=Quotes&return_id=$RECORD$&return_action=DetailView'
				);
			}
			if ($modCpg) {
				$modCpg->setRelatedList($modCyP, 'CobroPago', array('ADD'), 'get_dependents_list');
			}
			if ($modPot) {
				$modPot->setRelatedList($modCyP, 'CobroPago', array('ADD'), 'get_dependents_list');
			}
			if ($modHD) {
				$modHD->setRelatedList($modCyP, 'CobroPago', array('ADD'), 'get_dependents_list');
			}
			if ($modPrj) {
				$modPrj->setRelatedList($modCyP, 'CobroPago', array('ADD'), 'get_dependents_list');
			}
			if ($modPrjTask) {
				$modPrjTask->setRelatedList($modCyP, 'CobroPago', array('ADD'), 'get_dependents_list');
			}
			$this->setModuleSeqNumber('configure', $modulename, 'PAY-', '0000001');
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
			global $adb;
			$module = Vtiger_Module::getInstance($modulename);
			$field = Vtiger_Field::getInstance('reference', $module);
			$adb->query("update vtiger_field set uitype=4, typeofdata='V~O' where fieldid={$field->id}");
			BusinessActions::addLink(getTabid('CobroPago'), 'DETAILVIEWBASIC', 'Pay', 'notifications.php?type=Pay&cpid=$RECORD$', 'themes/images/Opportunities.gif', 0, null, false, 0);
		}
	}

	public function get_history_cobropago($cobropagoid) {
		global $log, $adb;
		$log->debug('> get_history_cobropago '.$cobropagoid);

		$result=$adb->pquery('select reference,update_log from vtiger_cobropago where cobropagoid=?', array($cobropagoid));
		$update_log = $adb->query_result($result, 0, 'update_log');

		$splitval = explode('--//--', trim($update_log, '--//--'));

		$header[] = $adb->query_result($result, 0, 'reference');

		$return_value = array('header'=>$header,'entries'=>$splitval,'navigation'=>array('',''));

		$log->debug('< get_history_cobropago');
		return $return_value;
	}

	public function preEditCheck($request, $smarty) {
		global $log, $app_strings;
		$isduplicate = isset($_REQUEST['isDuplicate']) ? $_REQUEST['isDuplicate'] : null;
		if ($this->mode == 'edit' && !$this->permissiontoedit() && $isduplicate != 'true') {
			$log->debug("You don't have permission to edit cobropago");
			$smarty->assign('APP', $app_strings);
			$smarty->display('modules/Vtiger/OperationNotPermitted.tpl');
			exit;
		}
		list($request,$smarty,$void) = cbEventHandler::do_filter('corebos.filter.preEditCheck', array($request,$smarty,$this));
		return '';
	}

	public function preSaveCheck($request) {
		global $log, $app_strings;
		if ($this->mode == 'edit' && !$this->permissiontoedit()) {
			$log->debug("You don't have permission to save cobropago");
			return array(true, $app_strings['LBL_PERMISSION'], 'index', array());
		}
		list($request,$void,$saveerror,$errormessage,$error_action,$returnvalues) =
			cbEventHandler::do_filter('corebos.filter.preSaveCheck', array($request, $this, false, '', '', ''));
		return array($saveerror, $errormessage, $error_action, $returnvalues);
	}

	/**
	 *	This function check is this payment is paid or not, to haver permission to edit
	**/
	public function permissiontoedit() {
		global $log,$current_user,$adb;
		$log->debug('> permissiontoedit');

		$res = $adb->pquery('select block_paid from vtiger_cobropagoconfig', array());
		$Block_paid = $adb->query_result($res, 0, 'block_paid');

		if (is_admin($current_user) || $Block_paid!='on') {
			return true;
		}

		if ($this->column_fields['paid'] == 1) {
			$permiso = false;
		} else {
			$permiso = true;
		}
		$log->debug('< permissiontoedit');
		return $permiso;
	}
}
?>
