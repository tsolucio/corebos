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

class SMSNotifierBase extends CRMEntity {
	public $db;
	public $log;

	public $table_name = 'vtiger_smsnotifier';
	public $table_index= 'smsnotifierid';

	/** Indicator if this is a custom module or standard module */
	public $IsCustomModule = true;
	public $HasDirectImageField = false;
	/**
	 * Mandatory table for supporting custom fields.
	 */
	public $customFieldTable = array('vtiger_smsnotifiercf', 'smsnotifierid');

	/**
	 * Mandatory for Saving, Include tables related to this module.
	 */
	public $tab_name = array('vtiger_crmentity', 'vtiger_smsnotifier', 'vtiger_smsnotifiercf');

	/**
	 * Mandatory for Saving, Include tablename and tablekey columnname here.
	 */
	public $tab_name_index = array(
		'vtiger_crmentity' => 'crmid',
		'vtiger_smsnotifier' => 'smsnotifierid',
		'vtiger_smsnotifiercf'=>'smsnotifierid'
	);

	/**
	 * Mandatory for Listing (Related listview)
	 */
	public $list_fields = array(
		/* Format: Field Label => array(tablename => columnname) */
		// tablename should not have prefix 'vtiger_'
		'Message' => array('smsnotifier' => 'message'),
		'Assigned To' => array('crmentity' => 'smownerid')
	);
	public $list_fields_name = array(
		/* Format: Field Label => fieldname */
		'Message' => 'message',
		'Assigned To' => 'assigned_user_id'
	);

	// Make the field link to detail view
	public $list_link_field = 'message';

	// For Popup listview and UI type support
	public $search_fields = array(
		/* Format: Field Label => array(tablename => columnname) */
		// tablename should not have prefix 'vtiger_'
		'Message' => array('smsnotifier' => 'message')
	);
	public $search_fields_name = array(
		/* Format: Field Label => fieldname */
		'Message' => 'message'
	);

	// For Popup window record selection
	public $popup_fields = array('message');

	// Allow sorting on the following (field column names)
	public $sortby_fields = array('message');

	// For Alphabetical search
	public $def_basicsearch_col = 'message';

	// Column value to use on detail view record text display
	public $def_detailview_recname = 'message';

	// Required Information for enabling Import feature
	public $required_fields = array('assigned_user_id'=>1);

	// Callback function list during Importing
	public $special_functions = array('set_import_assigned_user');

	public $default_order_by = 'crmid';
	public $default_sort_order='DESC';
	// Used when enabling/disabling the mandatory fields for the module.
	// Refers to vtiger_field.fieldname values.
	public $mandatory_fields = array('createdtime', 'modifiedtime', 'message');

	public function save_module($module) {
		if ($this->HasDirectImageField) {
			$this->insertIntoAttachment($this->id, $module);
		}
	}

	/**
	 * Invoked when special actions are performed on the module.
	 * @param String Module name
	 * @param String Event Type (module.postinstall, module.disabled, module.enabled, module.preuninstall)
	 */
	public function vtlib_handler($modulename, $event_type) {
		//adds sharing accsess
		$SMSNotifierModule  = Vtiger_Module::getInstance('SMSNotifier');
		Vtiger_Access::setDefaultSharing($SMSNotifierModule);
		$registerLinks = false;
		$unregisterLinks = false;
		if ($event_type == 'module.postinstall') {
			global $adb;
			$unregisterLinks = true;
			$registerLinks = true;

			// Mark the module as Standard module
			$adb->pquery('UPDATE vtiger_tab SET customized=0 WHERE name=?', array($modulename));
		} elseif ($event_type == 'module.disabled') {
			$unregisterLinks = true;
		} elseif ($event_type == 'module.enabled') {
			$registerLinks = true;
		} elseif ($event_type == 'module.preuninstall') {
			// TODO Handle actions when this module is about to be deleted.
		} elseif ($event_type == 'module.preupdate') {
			// TODO Handle actions before this module is updated.
		} elseif ($event_type == 'module.postupdate') {
			// TODO Handle actions after this module is updated.
		}
		if ($unregisterLinks) {
			$smsnotifierModuleInstance = Vtiger_Module::getInstance('SMSNotifier');
			$smsnotifierModuleInstance->deleteLink("HEADERSCRIPT", "SMSNotifierCommonJS", "modules/SMSNotifier/SMSNotifierCommon.js");

			$leadsModuleInstance = Vtiger_Module::getInstance('Leads');
			$leadsModuleInstance->deleteLink('LISTVIEWBASIC', 'Send SMS');
			$leadsModuleInstance->deleteLink('DETAILVIEWBASIC', 'Send SMS');

			$contactsModuleInstance = Vtiger_Module::getInstance('Contacts');
			$contactsModuleInstance->deleteLink('LISTVIEWBASIC', 'Send SMS');
			$contactsModuleInstance->deleteLink('DETAILVIEWBASIC', 'Send SMS');

			$accountsModuleInstance = Vtiger_Module::getInstance('Accounts');
			$accountsModuleInstance->deleteLink('LISTVIEWBASIC', 'Send SMS');
			$accountsModuleInstance->deleteLink('DETAILVIEWBASIC', 'Send SMS');
		}

		if ($registerLinks) {
			$smsnotifierModuleInstance = Vtiger_Module::getInstance('SMSNotifier');
			$smsnotifierModuleInstance->addLink('HEADERSCRIPT', 'SMSNotifierCommonJS', 'modules/SMSNotifier/SMSNotifierCommon.js');

			$leadsModuleInstance = Vtiger_Module::getInstance('Leads');

			$leadsModuleInstance->addLink('LISTVIEWBASIC', 'Send SMS', "SMSNotifierCommon.displaySelectWizard(this, '\$MODULE\$');");
			$leadsModuleInstance->addLink('DETAILVIEWBASIC', 'Send SMS', "javascript:SMSNotifierCommon.displaySelectWizard_DetailView('\$MODULE\$', '\$RECORD\$');");

			$contactsModuleInstance = Vtiger_Module::getInstance('Contacts');
			$contactsModuleInstance->addLink('LISTVIEWBASIC', 'Send SMS', "SMSNotifierCommon.displaySelectWizard(this, '\$MODULE\$');");
			$contactsModuleInstance->addLink('DETAILVIEWBASIC', 'Send SMS', "javascript:SMSNotifierCommon.displaySelectWizard_DetailView('\$MODULE\$', '\$RECORD\$');");

			$accountsModuleInstance = Vtiger_Module::getInstance('Accounts');
			$accountsModuleInstance->addLink('LISTVIEWBASIC', 'Send SMS', "SMSNotifierCommon.displaySelectWizard(this, '\$MODULE\$');");
			$accountsModuleInstance->addLink('DETAILVIEWBASIC', 'Send SMS', "javascript:SMSNotifierCommon.displaySelectWizard_DetailView('\$MODULE\$', '\$RECORD\$');");
		}
	}

	public function getListButtons($app_strings) {
		$list_buttons = array();

		if (isPermitted('SMSNotifier', 'Delete', '') == 'yes') {
			$list_buttons['del'] = $app_strings['LBL_MASS_DELETE'];
		}

		return $list_buttons;
	}

	/**
	 * Handle saving related module information.
	 * NOTE: This function has been added to CRMEntity (base class).
	 * You can override the behavior by re-defining it here.
	 */
	// public function save_related_module($module, $crmid, $with_module, $with_crmid) { }

	/**
	 * Handle deleting related module information.
	 * NOTE: This function has been added to CRMEntity (base class).
	 * You can override the behavior by re-defining it here.
	 */
	//public function delete_related_module($module, $crmid, $with_module, $with_crmid) { }

	/**
	 * Handle getting related list information.
	 * NOTE: This function has been added to CRMEntity (base class).
	 * You can override the behavior by re-defining it here.
	 */
	//public function get_related_list($id, $cur_tab_id, $rel_tab_id, $actions=false) { }

	/**
	 * Handle getting dependents list information.
	 * NOTE: This function has been added to CRMEntity (base class).
	 * You can override the behavior by re-defining it here.
	 */
	//public function get_dependents_list($id, $cur_tab_id, $rel_tab_id, $actions=false) { }
}
?>
