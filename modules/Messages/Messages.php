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

class Messages extends CRMEntity {
	public $table_name = 'vtiger_messages';
	public $table_index= 'messagesid';
	public $column_fields = array();

	/** Indicator if this is a custom module or standard module */
	public $IsCustomModule = true;
	public $HasDirectImageField = false;
	public $moduleIcon = array('library' => 'standard', 'containerClass' => 'slds-icon_container slds-icon-standard-account', 'class' => 'slds-icon', 'icon'=>'messaging_conversation');

	/**
	 * Mandatory table for supporting custom fields.
	 */
	public $customFieldTable = array('vtiger_messagescf', 'messagesid');
	// Uncomment the line below to support custom field columns on related lists
	// public $related_tables = array('vtiger_MODULE_NAME_LOWERCASEcf' => array('MODULE_NAME_LOWERCASEid', 'vtiger_MODULE_NAME_LOWERCASE', 'MODULE_NAME_LOWERCASEid', 'MODULE_NAME_LOWERCASE'));

	/**
	 * Mandatory for Saving, Include tables related to this module.
	 */
	public $tab_name = array('vtiger_crmentity', 'vtiger_messages', 'vtiger_messagescf');

	/**
	 * Mandatory for Saving, Include tablename and tablekey columnname here.
	 */
	public $tab_name_index = array(
		'vtiger_crmentity' => 'crmid',
		'vtiger_messages'   => 'messagesid',
		'vtiger_messagescf' => 'messagesid',
	);

	/**
	 * Mandatory for Listing (Related listview)
	 */
	public $list_fields = array(
		/* Format: Field Label => array(tablename => columnname) */
		// tablename should not have prefix 'vtiger_'
		'Messages No'=> array('messages' => 'messageno'),
		'Message Name'=> array('messages' => 'messagename'),
		'Campaign'=> array('messages' => 'campaign_message'),
		'Contact'=> array('messages' => 'contact_message'),
		'Status'=> array('messages' => 'status_message'),
		'Delivered' => array('messages' =>'delivered'),
		'Open' => array('messages' => 'open'),
		'Clicked' => array('messages' => 'clicked'),
		'Bounce' => array('messages' => 'bounce'),
		'Unsubscribe' => array('messages' => 'unsubscribe'),
		'no_mail' => array('messages' => 'no_mail')
	);
	public $list_fields_name = array(
		'Messages No'=> 'messageno',
		'Message Name'=> 'messagename',
		'Campaign'=> 'campaign_message',
		'Contact'=>'contact_message',
		'Status'=> 'status_message',
		'Delivered' => 'delivered',
		'Open' => 'open',
		'Clicked' => 'clicked',
		'Bounce' => 'bounce',
		'Unsubscribe' => 'unsubscribe',
		'no_mail' => 'no_mail',
	);

	// Make the field link to detail view from list view (Fieldname)
	public $list_link_field = 'messageno';

	// For Popup listview and UI type support
	public $search_fields = array(
		'Messages No'=> array('messages' => 'messageno'),
		'Message Name'=> array('messages' => 'messagename'),
		'Campaign'=> array('messages' => 'campaign_message'),
		'Contact'=> array('messages' => 'contact_message'),
		'Status'=> array('messages' => 'status_message'),
		'Delivered' => array('messages' => 'delivered'),
		'Open' => array('messages' => 'open'),
		'Clicked' => array('messages' => 'clicked'),
		'Bounce' => array('messages' => 'bounce'),
		'Unsubscribe' => array('messages' => 'unsubscribe'),
		'no_mail' => array('messages' => 'no_mail'),
	);
	public $search_fields_name = array(
		'Messages No'=> 'messageno',
		'Message Name'=> 'messagename',
		'Campaign'=> 'campaign_message',
		'Contact'=>'contact_message',
		'Status'=> 'status_message',
		'Delivered' => 'delivered',
		'Open' => 'open',
		'Clicked' => 'clicked',
		'Bounce' => 'bounce',
		'Unsubscribe' => 'unsubscribe',
		'no_mail' => 'no_mail'
	);

	// For Popup window record selection
	public $popup_fields = array('messageno');

	// Placeholder for sort fields - All the fields will be initialized for Sorting through initSortFields
	public $sortby_fields = array();

	// For Alphabetical search
	public $def_basicsearch_col = 'messageno';

	// Column value to use on detail view record text display
	public $def_detailview_recname = 'messageno';

	// Required Information for enabling Import feature
	public $required_fields = array('messageno'=>1);

	// Callback function list during Importing
	public $special_functions = array('set_import_assigned_user');

	public $default_order_by = 'messageno';
	public $default_sort_order='ASC';
	// Used when enabling/disabling the mandatory fields for the module.
	// Refers to vtiger_field.fieldname values.
	public $mandatory_fields = array('createdtime', 'modifiedtime', 'messageno');

	public function save_module($module) {
		if ($this->HasDirectImageField) {
			$this->insertIntoAttachment($this->id, $module);
		}
	}

	/* create a message record
	 * @param $fields array of field values. valid elements are:
	 *   name: message name field
	 *   datenametext: if name is empty this text will be concatenated to the date to construct the name
	 *   type: message type
	 *   uniqueid
	 *   clicked, dropped, bounce, open, delivered, unsubscribe, spamreport
	 *   status
	 *   template
	 *   account, contact, lead, campaign, relatedto
	 *   description
	 *   userid
	 */
	public static function createMessage($fields) {
		$info = array();
		if (empty($fields['name'])) {
			$info['messagename'] = $info['messagesname'] = date('Y-m-d H:i').(empty($fields['datenametext']) ? '' : ' '.$fields['datenametext']);
		} else {
			$info['messagename'] = $info['messagesname'] = $fields['name'];
		}
		if (isset($fields['type'])) {
			$info['messagetype'] = $info['messagestype'] = $fields['type'];
		} else {
			$info['messagetype'] = $info['messagestype'] = '';
		}
		if (isset($fields['uniqueid'])) {
			$info['messagesuniqueid'] = $fields['uniqueid'];
		} else {
			$info['messagesuniqueid'] = '';
		}
		$nf = array('clicked', 'dropped', 'bounce', 'open', 'delivered', 'unsubscribe', 'spamreport');
		foreach ($nf as $fld) {
			if (isset($fields[$fld])) {
				$info[$fld] = $fields[$fld];
			} else {
				$info[$fld] = '0';
			}
		}
		if (isset($fields['status'])) {
			$info['status_message'] = $fields['status'];
		} else {
			$info['status_message'] = '--None--';
		}
		if (isset($fields['relatedto'])) {
			$info['messagesrelatedto'] = $fields['relatedto'];
		} else {
			$info['messagesrelatedto'] = '0';
		}
		if (isset($fields['campaign'])) {
			$info['campaign_message'] = $fields['campaign'];
		} else {
			$info['campaign_message'] = '0';
		}
		if (isset($fields['account'])) {
			$info['account_message'] = $fields['account'];
		} else {
			$info['account_message'] = '0';
		}
		if (isset($fields['contact'])) {
			$info['contact_message'] = $fields['contact'];
		} else {
			$info['contact_message'] = '0';
		}
		if (isset($fields['lead'])) {
			$info['lead_message'] = $fields['lead'];
		} else {
			$info['lead_message'] = '0';
		}
		if (isset($fields['template'])) {
			$info['email_tplid'] = $fields['template'];
		} else {
			$info['email_tplid'] = '0';
		}
		if (isset($fields['description'])) {
			$info['description'] = $fields['description'];
		} else {
			$info['description'] = '';
		}
		if (isset($fields['userid'])) {
			$info['assigned_user_id'] = $fields['userid'];
		} else {
			global $current_user;
			$info['assigned_user_id'] = $current_user->id;
		}
		if (isset($fields['attachment'])) {
			$info['attachment'] = $fields['attachment'];
		} else {
			$info['attachment'] = '';
		}
		$info['no_mail'] = '';
		$info['lasteventtime'] = '';
		$info['lasturlclicked'] = '';
		$msg = new Messages();
		$msg->column_fields = $info;
		$msg->save('Messages');
		return $msg->id;
	}

	/**
	 * Invoked when special actions are performed on the module.
	 * @param string Module name
	 * @param string Event Type (module.postinstall, module.disabled, module.enabled, module.preuninstall)
	 */
	public function vtlib_handler($modulename, $event_type) {
		if ($event_type == 'module.postinstall') {
			// Handle post installation actions
			$this->setModuleSeqNumber('configure', $modulename, 'MSG-', '0000001');
			$module = Vtiger_Module::getInstance($modulename);
			$mod = Vtiger_Module::getInstance('Leads');
			$mod->setRelatedList($module, 'Messages', array('ADD'), 'get_dependents_list');
			$mod = Vtiger_Module::getInstance('Contacts');
			$mod->setRelatedList($module, 'Messages', array('ADD'), 'get_dependents_list');
			$mod = Vtiger_Module::getInstance('Accounts');
			$mod->setRelatedList($module, 'Messages', array('ADD'), 'get_dependents_list');
			$mod = Vtiger_Module::getInstance('Campaigns');
			$mod->setRelatedList($module, 'Messages', array('ADD'), 'get_dependents_list');
			// Add followup fields on CRM emails
			$mod = Vtiger_Module::getInstance('Emails');
			$blockInstance = Vtiger_Block::getInstance('LBL_EMAIL_INFORMATION', $mod);
			$field = new Vtiger_Field();
			$field->name = 'bounce';
			$field->label= 'Bounce';
			$field->table = 'vtiger_emaildetails';
			$field->column = 'bounce';
			$field->columntype = 'INT(11)';
			$field->uitype = 1;
			$field->displaytype = 1;
			$field->typeofdata = 'I~O';
			$field->presence = 0;
			$blockInstance->addField($field);
			$field = new Vtiger_Field();
			$field->name = 'clicked';
			$field->label= 'Clicked';
			$field->table = 'vtiger_emaildetails';
			$field->column = 'clicked';
			$field->columntype = 'INT(11)';
			$field->uitype = 1;
			$field->displaytype = 1;
			$field->typeofdata = 'I~O';
			$field->presence = 0;
			$blockInstance->addField($field);
			$field = new Vtiger_Field();
			$field->name = 'spamreport';
			$field->label= 'Spam';
			$field->table = 'vtiger_emaildetails';
			$field->column = 'spamreport';
			$field->columntype = 'INT(11)';
			$field->uitype = 1;
			$field->displaytype = 1;
			$field->typeofdata = 'I~O';
			$field->presence = 0;
			$blockInstance->addField($field);
			$field = new Vtiger_Field();
			$field->name = 'delivered';
			$field->label= 'Delivered';
			$field->table = 'vtiger_emaildetails';
			$field->column = 'delivered';
			$field->columntype = 'INT(11)';
			$field->uitype = 56;
			$field->displaytype = 1;
			$field->typeofdata = 'C~O';
			$field->presence = 0;
			$blockInstance->addField($field);
			$field = new Vtiger_Field();
			$field->name = 'dropped';
			$field->label= 'Dropped';
			$field->table = 'vtiger_emaildetails';
			$field->column = 'dropped';
			$field->columntype = 'INT(11)';
			$field->uitype = 1;
			$field->displaytype = 1;
			$field->typeofdata = 'I~O';
			$field->presence = 0;
			$blockInstance->addField($field);
			$field = new Vtiger_Field();
			$field->name = 'open';
			$field->label= 'Open';
			$field->table = 'vtiger_emaildetails';
			$field->column = 'open';
			$field->columntype = 'INT(11)';
			$field->uitype = 1;
			$field->displaytype = 1;
			$field->typeofdata = 'I~O';
			$field->presence = 0;
			$blockInstance->addField($field);
			$field = new Vtiger_Field();
			$field->name = 'unsubscribe';
			$field->label= 'Unsubscribe';
			$field->table = 'vtiger_emaildetails';
			$field->column = 'unsubscribe';
			$field->columntype = 'INT(11)';
			$field->uitype = 56;
			$field->displaytype = 1;
			$field->typeofdata = 'C~O';
			$field->presence = 0;
			$blockInstance->addField($field);
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
		}
	}
}
?>
