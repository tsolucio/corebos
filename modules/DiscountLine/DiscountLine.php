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
	public $mandatory_fields = array('dlcategory','discount');

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
		// global $adb;
		// $saveerror = false;
		// $errmsg = '';
		// $sql='SELECT 1 FROM vtiger_discountline
		// 	INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_discountline.discountlineid
		// 	WHERE vtiger_crmentity.deleted=0 AND accountid=? AND dlcategory=? limit 1';
		// $result = $adb->pquery($sql, array($request['accountid'], $request['productcategory']));
		// if ($adb->num_rows($result)==1) {
		// 	$saveerror = true;
		// 	$errmsg = getTranslatedString('LBL_PERMISSION', 'DiscountLine');
		// }
		// return array($saveerror,$errmsg, 'EditView', '');
		global $log, $app_strings;
		if ($this->mode == 'edit' && !$this->permissiontoedit()) {
			$log->debug("You don't have permission to save Price Modification");
			return array(true, $app_strings['LBL_PERMISSION'], 'index', array());
		}
		list($request,$void,$saveerror,$errormessage,$error_action,$returnvalues) =
			cbEventHandler::do_filter('corebos.filter.preSaveCheck', array($request, $this, false, '', '', ''));
		return array($saveerror, $errormessage, $error_action, $returnvalues);
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
			$modAccounts = Vtiger_Module::getInstance('Accounts');
			$modAccounts->setRelatedList(Vtiger_Module::getInstance('Discountline'), 'Price Modification', array('ADD','SELECT'));
			$modServices = Vtiger_Module::getInstance('Services');
			$modServices->setRelatedList(Vtiger_Module::getInstance('Discountline'), 'Price Modification', array('ADD','SELECT'));
			$modProducts = Vtiger_Module::getInstance('Products');
			$modProducts->setRelatedList(Vtiger_Module::getInstance('Discountline'), 'Price Modification', array('ADD','SELECT'));
			$modContacts = Vtiger_Module::getInstance('Contacts');
			$modContacts->setRelatedList(Vtiger_Module::getInstance('Discountline'), 'Price Modification', array('ADD','SELECT'));
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
	public function save_related_module($module, $crmid, $with_module, $with_crmids) {
		global $adb;
		if ($with_module == 'Products' || $with_module == 'Services' || $with_module == 'Accounts' || $with_module == 'Contacts') {
			$with_crmids = (array)$with_crmids;
			foreach ($with_crmids as $with_crmid) {
				$checkResult = $adb->pquery(
					'SELECT * FROM vtiger_crmentityrel INNER JOIN ( SELECT vtiger_crmentityrel.relcrmid FROM vtiger_crmentityrel WHERE crmid = ? OR crmid = ? ) temp ON vtiger_crmentityrel.relcrmid = temp.relcrmid AND( relmodule = ? OR relmodule = ? ) WHERE ( vtiger_crmentityrel.relmodule = ? OR vtiger_crmentityrel.relmodule = ? ) AND crmid IN( SELECT crmid FROM vtiger_crmentityrel WHERE (crmid = ? OR crmid = ?) AND( module = ? OR module = ? ) AND( relcrmid = ? OR relcrmid = ? ) AND( relmodule = ? OR relmodule = ? ) )',
					array(
						$crmid, $with_crmid, $with_module, $module, $with_module, $module, $crmid, $with_crmid,
						$module, $with_module, $with_crmid, $crmid,
						$with_module, $module
					)
				);
				if ($adb->num_rows($checkResult) > 1) {
					$sql = "DELETE FROM vtiger_crmentityrel WHERE crmid = ? AND module = ? AND relcrmid = ? AND relmodule = ? LIMIT 1";
					$adb->pquery($sql, array($crmid, $module, $with_crmid, $with_module));
				} elseif ($adb->num_rows($checkResult) == 0) {
					$sql = 'INSERT INTO vtiger_crmentityrel VALUES(?,?,?,?)';
					$adb->pquery($sql, array($crmid, $module, $with_crmid, $with_module));
				}
			}
		} else {
			parent::save_related_module($module, $crmid, $with_module, $with_crmid);
		}
	}

	public function delete_related_module($module, $crmid, $with_module, $with_crmid) {
		global $adb;
		if ($with_module == 'ProductDetail') {
			$with_crmid = (array)$with_crmid;
			$data = array();
			$data['sourceModule'] = $module;
			$data['sourceRecordId'] = $crmid;
			$data['destinationModule'] = $with_module;
			foreach ($with_crmid as $relcrmid) {
				$data['destinationRecordId'] = $relcrmid;
				cbEventHandler::do_action('corebos.entity.link.delete', $data);
				$adb->pquery(
					'DELETE FROM vtiger_crmentityrel WHERE (crmid=? AND module=? AND relcrmid=? AND relmodule=?) OR (relcrmid=? AND relmodule=? AND crmid=? AND module=?)',
					array($crmid, $module, $relcrmid, $with_module,$crmid, $module, $relcrmid, $with_module)
				);
			}
		} else {
			parent::delete_related_module($module, $crmid, $with_module, $with_crmid);
		}
	}
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

	public static function getDiscount($accountid, $contactid, $productid, $category) {
		global $adb;
		$search_in = (1 == GlobalVariable::getVariable('Application_B2C', '0')) ? $contactid : $accountid;

		if ($inventory_line['category'] == "None") {
			$query_string = 'SELECT *FROM vtiger_discountline INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = discountlineid INNER JOIN vtiger_crmentityrel ON (
				vtiger_crmentityrel.crmid=discountlineid OR vtiger_crmentityrel.relcrmid=discountlineid) WHERE deleted=0 AND (vtiger_crmentityrel.crmid=? OR 
				vtiger_crmentityrel.relcrmid=? OR vtiger_crmentityrel.crmid=? OR vtiger_crmentityrel.relcrmid=?)';
			$params = array($search_in, $search_in, $productid, $productid);
		} else {
			$query_string = 'SELECT *FROM vtiger_discountline INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = discountlineid INNER JOIN vtiger_crmentityrel ON (
				vtiger_crmentityrel.crmid=discountlineid OR vtiger_crmentityrel.relcrmid=discountlineid) WHERE deleted=0 AND vtiger_discountline.dlcategory =? AND (
					vtiger_crmentityrel.crmid=? OR vtiger_crmentityrel.relcrmid=? OR vtiger_crmentityrel.crmid=? OR vtiger_crmentityrel.relcrmid=?)';
			$params = array($category, $search_in, $search_in, $productid, $productid);
		}

		$query_result = $adb->pquery($query_string, $params);
		if ($adb->num_rows($query_result) > 0) {
			if (!empty($adb->query_result($query_result, 0, "cbmapid"))) {
				$mapid = $adb->query_result($query_result, 0, "cbmapid");
				$context = array('recordid' => $productid);
				$value = coreBOS_Rule::evaluate($mapid, $context);

				if ($adb->query_result($query_result, 0, "returnvalue") == 'Cost+Margin') {
					$result_cost_price = $adb->pquery("SELECT unit_price FROM `vtiger_products` WHERE productid=?", array($productid));
					$unitprice = $adb->query_result($result_cost_price, 0, "unit_price");
					$unitprice = $unitprice + (1 + $value);
				} elseif ($adb->query_result($query_result, 0, "returnvalue") == 'Unit+Discount') {
					$result_unit_price = $adb->pquery("SELECT unit_price FROM `vtiger_products` WHERE productid=?", array($productid));
					$unitprice = $adb->query_result($result_unit_price, 0, "unit_price");
					return array('unit price' => $unitprice , 'discount' => $value);
				}
			} else {
				$value = $adb->query_result($query_result, 0, "discount");
				if ($adb->query_result($query_result, 0, "returnvalue") == 'Cost+Margin') {
					$result_cost_price = $adb->pquery("SELECT unit_price FROM `vtiger_products` WHERE productid=?", array($productid));
					$unitprice = $adb->query_result($result_cost_price, 0, "unit_price");
					$unitprice = $unitprice + (1 + $value);
					return array('unit price' => $unitprice, 'discount' => 0);
				} elseif ($adb->query_result($query_result, 0, "returnvalue") == 'Unit+Discount') {
					$result_unit_price = $adb->pquery("SELECT unit_price FROM `vtiger_products` WHERE productid=?", array($productid));
					$unitprice = $adb->query_result($result_unit_price, 0, "unit_price");
					return array('unit price' => $unitprice , 'discount' => $value);
				}
			}
		} else {
			return false;
		}
	}
}
?>
