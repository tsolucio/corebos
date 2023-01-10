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

class cbAddress extends CRMEntity {
	public $db;
	public $log;

	public $table_name = 'vtiger_cbaddress';
	public $table_index= 'cbaddressid';
	public $column_fields = array();
	public $popup_function = 'cbAddressCapture';
	

	/** Indicator if this is a custom module or standard module */
	public $IsCustomModule = true;
	public $HasDirectImageField = false;
	public $moduleIcon = array('library' => 'standard', 'containerClass' => 'slds-icon_container slds-icon-standard-address', 'class' => 'slds-icon', 'icon'=>'address');

	/**
	 * Mandatory table for supporting custom fields.
	 */
	public $customFieldTable = array('vtiger_cbaddresscf', 'cbaddressid');

	/**
	 * Mandatory for Saving, Include tables related to this module.
	 */
	public $tab_name = array('vtiger_crmentity', 'vtiger_cbaddress', 'vtiger_cbaddresscf');

	/**
	 * Mandatory for Saving, Include tablename and tablekey columnname here.
	 */
	public $tab_name_index = array(
		'vtiger_crmentity' => 'crmid',
		'vtiger_cbaddress'   => 'cbaddressid',
		'vtiger_cbaddresscf' => 'cbaddressid');

	/**
	 * Mandatory for Listing (Related listview)
	 */
	public $list_fields = array(
		/* Format: Field Label => array(tablename => columnname) */
		// tablename should not have prefix 'vtiger_'
		'Address Number'=> array('cbaddress'=> 'cbaddressno'),
		'Reference'     => array('cbaddress'=> 'reference'),
		'Street'        => array('cbaddress'=> 'street'),
		'PO Box'        => array('cbaddress'=> 'pobox'),
		'City'          => array('cbaddress'=> 'city'),
		'State'         => array('cbaddress'=> 'state'),
		'Postal Code'   => array('cbaddress'=> 'postalcode'),
		'Country'       => array('cbaddress'=> 'country')
	);
	public $list_fields_name = array(
		/* Format: Field Label => fieldname */
		'Address Number'=> 'cbaddressno',
		'Reference'     => 'reference',
		'Street'        => 'street',
		'PO Box'        => 'pobox',
		'City'          => 'city',
		'State'         => 'state',
		'Postal Code'   => 'postalcode',
		'Country'       => 'country'
	);

	// Make the field link to detail view from list view (Fieldname)
	public $list_link_field = 'cbaddressno';

	// For Popup listview and UI type support
	public $search_fields = array(
		/* Format: Field Label => array(tablename => columnname) */
		// tablename should not have prefix 'vtiger_'
		'Address Number'=> array('cbaddress'=> 'cbaddressno'),
		'Reference'     => array('cbaddress'=> 'reference'),
		'Street'        => array('cbaddress'=> 'street'),
		'PO Box'        => array('cbaddress'=> 'pobox'),
		'City'          => array('cbaddress'=> 'city'),
		'State'         => array('cbaddress'=> 'state'),
		'Postal Code'   => array('cbaddress'=> 'postalcode'),
		'Country'       => array('cbaddress'=> 'country')
	);
	public $search_fields_name = array(
		/* Format: Field Label => fieldname */
		'Address Number'=> 'cbaddressno',
		'Reference'     => 'reference',
		'Street'        => 'street',
		'PO Box'        => 'pobox',
		'City'          => 'city',
		'State'         => 'state',
		'Postal Code'   => 'postalcode',
		'Country'       => 'country'
	);

	// For Popup window record selection
	public $popup_fields = array('cbaddressno');

	// Placeholder for sort fields - All the fields will be initialized for Sorting through initSortFields
	public $sortby_fields = array();

	// For Alphabetical search
	public $def_basicsearch_col = 'cbaddressno';

	// Column value to use on detail view record text display
	public $def_detailview_recname = 'cbaddressno';

	// Required Information for enabling Import feature
	public $required_fields = array('cbaddressno'=>1);

	// Callback function list during Importing
	public $special_functions = array('set_import_assigned_user');

	public $default_order_by = 'cbaddressno';
	public $default_sort_order='ASC';
	// Used when enabling/disabling the mandatory fields for the module.
	// Refers to vtiger_field.fieldname values.
	public $mandatory_fields = array('createdtime', 'modifiedtime', 'cbaddressno');

	function getvtlib_open_popup_window_function($fieldname,$basemodule) {
		if (($fieldname=='linktoaddressbilling' || $fieldname=='linktoaddressshipping')
		 and ($basemodule=='Invoice' or $basemodule=='Contacts' or $basemodule=='Accounts' or $basemodule=='Quotes' or $basemodule=='PurchaseOrder' or $basemodule=='SalesOrder')) {
			return 'cbAddressOpenCapture';
		} else {
			return 'vtlib_open_popup_window';
		}
	}

	public function save_module($module) {
		if ($this->HasDirectImageField) {
			$this->insertIntoAttachment($this->id, $module);
		}
	}

	/**
	 * Return query to use based on given modulename, fieldname
	 * Useful to handle specific case handling for Popup
	 */
	function getQueryByModuleField($module, $fieldname, $srcrecord, $query='') {
		// $srcrecord could be empty
		global $adb,$log;
		$query_relation = ' INNER JOIN vtiger_crmentityrel ON (vtiger_crmentityrel.relcrmid = vtiger_crmentity.crmid OR vtiger_crmentityrel.crmid = vtiger_crmentity.crmid) ';
		$wherepos = stripos($query, 'where'); // there is always a where
		$query_body = substr($query, 0, $wherepos-1);
		$query_cond = substr($query, $wherepos+5);
		if($module == 'Invoice' || $module == 'Contacts' || $module == 'Quotes' || $module == 'SalesOrder') {
			$accountID = vtlib_purify($_REQUEST['acc_id']);
			$contactID = vtlib_purify($_REQUEST['cont_id']);
			/*$query = "SELECT vtiger_crmentity.*, vtiger_cbaddress.*, vtiger_cbaddresscf.* 
			FROM vtiger_cbaddress 
			INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_cbaddress.cbaddressid  
			INNER JOIN vtiger_cbaddresscf ON vtiger_cbaddresscf.cbaddressid = vtiger_cbaddress.cbaddressid 
			LEFT JOIN vtiger_users ON vtiger_users.id = vtiger_crmentity.smownerid 
			LEFT JOIN vtiger_groups ON vtiger_groups.groupid = vtiger_crmentity.smownerid 
			WHERE vtiger_cbaddress.cbaddressid > 0 AND vtiger_crmentity.deleted = 0" ORDER BY cbaddressno ASC ;*/
			if(isset($_REQUEST['cont_id']) && $_REQUEST['cont_id'] !='' && $_REQUEST['acc_id'] =='') {
				$query1 = $query_body . $query_relation . " WHERE (vtiger_crmentityrel.crmid = $contactID OR vtiger_crmentityrel.relcrmid = $contactID) and " . $query_cond;
				return $query1;
			}
			elseif((isset($_REQUEST['acc_id']) && $_REQUEST['acc_id'] !='' && $_REQUEST['cont_id'] =='' )) {
				$query1 = $query_body . $query_relation . " WHERE (vtiger_crmentityrel.crmid = $accountID OR vtiger_crmentityrel.relcrmid = $accountID) and " . $query_cond;
				return $query1;
			}
			elseif(isset($_REQUEST['acc_id']) && $_REQUEST['acc_id'] !='' && isset($_REQUEST['cont_id']) && $_REQUEST['cont_id'] !=''){
				$query1 = $query_body . $query_relation . " WHERE (vtiger_crmentityrel.crmid = $accountID OR vtiger_crmentityrel.relcrmid = $accountID or vtiger_crmentityrel.crmid = $contactID OR vtiger_crmentityrel.relcrmid = $contactID) and " . $query_cond;
				$res = $adb->query($query1);
				$number= $adb->num_rows($res);
				if($number > 0){
					 return $query1;
				}
				else return $query;
			}
			else return $query;
		}
		elseif($module == 'Accounts'){
			$accountID = vtlib_purify($_REQUEST['acc_id']);
			if((isset($_REQUEST['acc_id']) && $_REQUEST['acc_id'] !='')){
				$query1 = $query_body . $query_relation . " WHERE (vtiger_crmentityrel.crmid = $accountID OR vtiger_crmentityrel.relcrmid = $accountID) and " . $query_cond;;
				return $query1;
			}
			else return $query;
		}
		elseif($module == 'PurchaseOrder'){
			$contactID = vtlib_purify($_REQUEST['cont_id']);
			if((isset($_REQUEST['cont_id']) && $_REQUEST['cont_id'] !='')){
				$query1 = $query_body . $query_relation . " WHERE (vtiger_crmentityrel.crmid = $contactID OR vtiger_crmentityrel.relcrmid = $contactID) and " . $query_cond;;
				return $query1;
			}
			else return $query;
		}
		return $query;
	}


	/**
	 * Invoked when special actions are performed on the module.
	 * @param String Module name
	 * @param String Event Type (module.postinstall, module.disabled, module.enabled, module.preuninstall)
	 */
	public function vtlib_handler($modulename, $event_type) {
		if ($event_type == 'module.postinstall') {
			global $adb;
			require_once('include/events/include.inc');
			$admodule=Vtiger_Module::getInstance($modulename);
			$admodule->addLink('HEADERSCRIPT', 'AddressCaptureFunctions', 'modules/cbAddress/cbAddress.js');
			$em = new VTEventsManager($adb);
			$em->registerHandler('corebos.filter.editview.setObjectValues', 'modules/cbAddress/setcbAddressField.php', 'cbAddressSetLinkFields');
			$mods = array('Contacts','Accounts','Invoice','Quotes','SalesOrder','PurchaseOrder');
			foreach ($mods as $module) {
				$modrel=Vtiger_Module::getInstance($module);
				if ($modrel) {
					if ($module=='Accounts' or $module=='Contacts') {
						$modrel->setRelatedList($admodule, $modulename, array('SELECT'),'get_related_list');
					}
					$block = VTiger_Block::getInstance('LBL_ADDRESS_INFORMATION',$modrel);
					$field1 = new Vtiger_Field();
					$field1->name = 'linktoaddressbilling';
					$field1->column = 'linktoaddressbilling';
					$field1->label = 'Select Bill Address';
					$field1->columntype = 'INT(11)';
					$field1->uitype = 10;
					$field1->typeofdata = 'I~O';
					$field1->presence = 0;
					$field1->displaytype = 1;
					$block->addField($field1);
					$field1->setRelatedModules(Array($modulename));

					$field2 = new Vtiger_Field();
					$field2->name = 'linktoaddressshipping';
					$field2->column = 'linktoaddressshipping';
					$field2->label = 'Select Ship Address';
					$field2->columntype = 'INT(11)';
					$field2->uitype = 10;
					$field2->typeofdata = 'I~O';
					$field2->presence = 0;
					$field2->displaytype = 1;
					$block->addField($field2);
					$field2->setRelatedModules(Array($modulename));
				}
			}
			// TODO Handle post installation actions
			$this->setModuleSeqNumber('configure', $modulename, 'cbad-', '0000001');
		} elseif ($event_type == 'module.disabled') {
			// TODO Handle actions when this module is disabled.
		} elseif ($event_type == 'module.enabled') {
			// TODO Handle actions when this module is enabled.
		} elseif ($event_type == 'module.preuninstall') {
			// TODO Handle actions when this module is about to be deleted.
		} elseif ($event_type == 'module.preupdate') {
			// TODO Handle actions before this module is updated.
		} elseif ($event_type == 'module.postupdate') {
			// TODO Handle actions after this module is updated.
		}
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
