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

class DiscountLine extends CRMEntity {
	public $db;
	public $log;

	public $table_name = 'vtiger_discountline';
	public $table_index= 'discountlineid';
	public $column_fields = array();

	/** Indicator if this is a custom module or standard module */
	public $IsCustomModule = true;
	public $HasDirectImageField = false;
	/**
	 * Mandatory table for supporting custom fields.
	 */
	public $customFieldTable = array('vtiger_discountlinecf', 'discountlineid');

	/**
	 * Mandatory for Saving, Include tables related to this module.
	 */
	public $tab_name = array('vtiger_crmentity', 'vtiger_discountline', 'vtiger_discountlinecf');

	/**
	 * Mandatory for Saving, Include tablename and tablekey columnname here.
	 */
	public $tab_name_index = array(
		'vtiger_crmentity' => 'crmid',
		'vtiger_discountline'   => 'discountlineid',
		'vtiger_discountlinecf' => 'discountlineid',
	);

	/**
	 * Mandatory for Listing (Related listview)
	 */
	public $list_fields = array(
		/* Format: Field Label => array(tablename => columnname) */
		// tablename should not have prefix 'vtiger_'
		'discountline_no'=> array('discountline' => 'discountline_no'),
		'Line'=> array('discountline' => 'productcategory'),
		'Discount'=> array('discountline' => 'discount'),
		'Assigned To' => array('crmentity' => 'smownerid')
	);
	public $list_fields_name = array(
		/* Format: Field Label => fieldname */
		'discountline_no'=> 'discountline_no',
		'Line'=> 'productcategory',
		'Discount'=>'discount',
		'Assigned To' => 'assigned_user_id'
	);

	// Make the field link to detail view from list view (Fieldname)
	public $list_link_field = 'discountline_no';

	// For Popup listview and UI type support
	public $search_fields = array(
		/* Format: Field Label => array(tablename => columnname) */
		// tablename should not have prefix 'vtiger_'
		'discountline_no'=> array('discountline' => 'discountline_no')
	);
	public $search_fields_name = array(
		/* Format: Field Label => fieldname */
		'discountline_no'=> 'discountline_no'
	);

	// For Popup window record selection
	public $popup_fields = array('discountline_no');

	// Placeholder for sort fields - All the fields will be initialized for Sorting through initSortFields
	public $sortby_fields = array();

	// For Alphabetical search
	public $def_basicsearch_col = 'discountline_no';

	// Column value to use on detail view record text display
	public $def_detailview_recname = 'discountline_no';

	// Required Information for enabling Import feature
	public $required_fields = array('discountline_no'=>1);

	// Callback function list during Importing
	public $special_functions = array('set_import_assigned_user');

	public $default_order_by = 'discountline_no';
	public $default_sort_order='ASC';
	// Used when enabling/disabling the mandatory fields for the module.
	// Refers to vtiger_field.fieldname values.
	public $mandatory_fields = array('accountid','dlcategory','discount');

	public function save_module($module) {
		if ($this->HasDirectImageField) {
			$this->insertIntoAttachment($this->id, $module);
		}
	}

	/* Validate values trying to be saved.
	 * @param array $_REQUEST input values. Note: column_fields array is already loaded
	 * @return array
	 *   saveerror: true if error false if not
	 *   errormessage: message to return to user if error, empty otherwise
	 *   error_action: action to redirect to inside the same module in case of error. if redirected to EditView (default action)
	 *                 all values introduced by the user will be preloaded
	 */
	public function preSaveCheck($request) {
		global $adb;
		$saveerror = false;
		$errmsg = '';
		$sql='SELECT 1 FROM vtiger_discountline
			INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_discountline.discountlineid
			WHERE vtiger_crmentity.deleted=0 AND accountid=? AND dlcategory=? limit 1';
		$result = $adb->pquery($sql, array($request['accountid'], $request['productcategory']));
		if ($adb->num_rows($result)==1) {
			$saveerror = true;
			$errmsg = getTranslatedString('LBL_PERMISSION', 'DiscountLine');
		}
		return array($saveerror,$errmsg, 'EditView', '');
	}

	/**
	 * Invoked when special actions are performed on the module.
	 * @param String Module name
	 * @param String Event Type (module.postinstall, module.disabled, module.enabled, module.preuninstall)
	 */
	public function vtlib_handler($modulename, $event_type) {
		global $adb;
		include_once 'vtlib/Vtiger/Module.php';
		if ($event_type == 'module.postinstall') {
			// TODO Handle post installation actions
			$this->setModuleSeqNumber('configure', $modulename, 'DTOLINE-', '000001');
			$adb->query("UPDATE vtiger_field SET fieldname = 'productcategory' WHERE tablename='vtiger_service' and columnname='servicecategory'");
			$modAcc=Vtiger_Module::getInstance('Accounts');
			$modDto=Vtiger_Module::getInstance('DiscountLine');
			$modAcc->setRelatedList($modDto, 'DiscountLine', array('ADD'), 'get_dependents_list');
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

	public static function getDiscount($pdoid, $accid) {
		global $adb;
		// search productline alone and productline+client
		$dtopdors = $adb->pquery(
			'select discount
				from vtiger_discountline
				inner join vtiger_crmentity on crmid=discountlineid
					left join vtiger_products on productcategory=dlcategory and productid=?
					left join vtiger_service on productcategory=dlcategory and serviceid=?
					where deleted=0 and (accountid=? or accountid=0)
					order by accountid desc',
			array($pdoid, $pdoid, $accid)
		);
		if ($dtopdors && $adb->num_rows($dtopdors)>0) {
			return $adb->query_result($dtopdors, 0, 'discount');
		}
		if (!empty($accid)) {
			// search direct client discount for all products
			$dtopdors = $adb->pquery(
				'select discount
					from vtiger_discountline
					inner join vtiger_crmentity on crmid=discountlineid
					where deleted=0 and accountid=? and dlcategory=?
					limit 1',
				array($accid, '--None--')
			);
			if ($dtopdors && $adb->num_rows($dtopdors)>0) {
				return $adb->query_result($dtopdors, 0, 'discount');
			}
		}
		return 0;
	}
}
?>
