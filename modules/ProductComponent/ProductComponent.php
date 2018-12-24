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

class ProductComponent extends CRMEntity {
	public $db;
	public $log;

	public $table_name = 'vtiger_productcomponent';
	public $table_index= 'productcomponentid';
	public $column_fields = array();

	/** Indicator if this is a custom module or standard module */
	public $IsCustomModule = true;
	public $HasDirectImageField = false;
	/**
	 * Mandatory table for supporting custom fields.
	 */
	public $customFieldTable = array('vtiger_productcomponentcf', 'productcomponentid');

	/**
	 * Mandatory for Saving, Include tables related to this module.
	 */
	public $tab_name = array('vtiger_crmentity', 'vtiger_productcomponent', 'vtiger_productcomponentcf');

	/**
	 * Mandatory for Saving, Include tablename and tablekey columnname here.
	 */
	public $tab_name_index = array(
		'vtiger_crmentity' => 'crmid',
		'vtiger_productcomponent'   => 'productcomponentid',
		'vtiger_productcomponentcf' => 'productcomponentid',
	);

	/**
	 * Mandatory for Listing (Related listview)
	 */
	public $list_fields = array(
		/* Format: Field Label => array(tablename => columnname) */
		// tablename should not have prefix 'vtiger_'
		'Relation Number'=> array('productcomponent' => 'relno'),
		'frompdo'=> array('productcomponent' => 'frompdo'),
		'topdo'=> array('productcomponent' => 'topdo'),
		'quantity'=> array('productcomponent' => 'quantity'),
		'Unit Price'=>array('products'=>'unit_price'),
		'Cost Price'=>array('products'=>'cost_price'),
		'Relation Mode'=> array('productcomponent' => 'relmode'),
		'Relation from'=> array('productcomponent' => 'relfrom'),
		'Relation to'=> array('productcomponent' => 'relto')
	);
	public $list_fields_name = array(
		/* Format: Field Label => fieldname */
		'Relation Number'=> 'relno',
		'frompdo'=> 'frompdo',
		'topdo'=> 'topdo',
		'quantity'=> 'quantity',
		'Unit Price'=> 'unit_price',
		'Cost Price'=> 'cost_price',
		'Relation Mode'=> 'relmode',
		'Relation from'=> 'relfrom',
		'Relation to'=> 'relto'
	);

	// Make the field link to detail view from list view (Fieldname)
	public $list_link_field = 'relno';

	// For Popup listview and UI type support
	public $search_fields = array(
		/* Format: Field Label => array(tablename => columnname) */
		// tablename should not have prefix 'vtiger_'
		'Relation Number'=> array('productcomponent' => 'relno'),
		'frompdo'=> array('productcomponent' => 'frompdo'),
		'topdo'=> array('productcomponent' => 'topdo'),
		'quantity'=> array('productcomponent' => 'quantity'),
		'Relation Mode'=> array('productcomponent' => 'relmode'),
		'Relation from'=> array('productcomponent' => 'relfrom'),
		'Relation to'=> array('productcomponent' => 'relto')
	);
	public $search_fields_name = array(
		/* Format: Field Label => fieldname */
		'Relation Number'=> 'relno',
		'frompdo'=> 'frompdo',
		'topdo'=> 'topdo',
		'quantity'=> 'quantity',
		'Relation Mode'=> 'relmode',
		'Relation from'=> 'relfrom',
		'Relation to'=> 'relto'
	);

	// For Popup window record selection
	public $popup_fields = array('relno');

	// Placeholder for sort fields - All the fields will be initialized for Sorting through initSortFields
	public $sortby_fields = array();

	// For Alphabetical search
	public $def_basicsearch_col = 'relno';

	// Column value to use on detail view record text display
	public $def_detailview_recname = 'relno';

	// Required Information for enabling Import feature
	public $required_fields = array('relno'=>1);

	// Callback function list during Importing
	public $special_functions = array('set_import_assigned_user');

	public $default_order_by = 'relno';
	public $default_sort_order='ASC';
	// Used when enabling/disabling the mandatory fields for the module.
	// Refers to vtiger_field.fieldname values.
	public $mandatory_fields = array('createdtime', 'modifiedtime', 'relno');

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
		if ($event_type == 'module.postinstall') {
			// TODO Handle post installation actions
			$this->setModuleSeqNumber('configure', $modulename, 'pcmpnt', '0000001');
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

	public static function getRelation($fromProduct = '*', $toProduct = '*', $fromDate = '*', $toDate = '*', $relationType = '*') {
		global $adb;
		$sql = 'select * from vtiger_productcomponent
			inner join vtiger_crmentity on crmid=productcomponentid
			where deleted=0';
		if ($fromProduct!='*') {
			$sql.=' and frompdo=? ';
			$params[] = $fromProduct;
		}
		if ($toProduct!='*') {
			$sql.=' and topdo=? ';
			$params[] = $toProduct;
		}
		if ($fromDate!='*') {
			$sql.=' and relfrom<=? ';
			$params[] = $fromDate;
		}
		if ($toDate!='*') {
			$sql.=' and relto>=? ';
			$params[] = $toDate;
		}
		if ($relationType!='*') {
			$sql.=' and relmode=? ';
			$params[] = $relationType;
		}
		$rs = $adb->pquery($sql, $params);
		$ret = array();
		while ($pa = $adb->fetch_array($rs)) {
			$frompdo_name = getEntityName('Products', $pa['frompdo']);
			$topdo_name = getEntityName('Products', $pa['topdo']);
			$ret[$pa['productcomponentid']] = array(
				'relno' => $pa['relno'],
				'frompdo' => $pa['frompdo'],
				'frompdo_name' => $frompdo_name[$pa['frompdo']],
				'topdo' => $pa['topdo'],
				'topdo_name' => $topdo_name[$pa['topdo']],
				'relmode' => $pa['relmode'],
				'relfrom' => $pa['relfrom'],
				'relto' => $pa['relto'],
				'quantity' => $pa['quantity'],
				'instructions' => $pa['instructions'],
			);
		}
		return $ret;
	}
}
?>
