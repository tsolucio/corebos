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
require_once 'modules/cbMap/processmap/processMap.php';
include_once 'modules/cbMap/cbRule.php';

class cbMap extends CRMEntity {
	public $db;
	public $log;

	public $table_name = 'vtiger_cbmap';
	public $table_index= 'cbmapid';
	public $column_fields = array();

	/** Indicator if this is a custom module or standard module */
	public $IsCustomModule = true;
	public $HasDirectImageField = false;
	/**
	 * Mandatory table for supporting custom fields.
	 */
	public $customFieldTable = array('vtiger_cbmapcf', 'cbmapid');
	// Uncomment the line below to support custom field columns on related lists
	// var $related_tables = array('vtiger_cbmapcf' => array('cbmapid', 'vtiger_cbmap', 'cbmapid', 'cbmap'));

	/**
	 * Mandatory for Saving, Include tables related to this module.
	 */
	public $tab_name = array('vtiger_crmentity', 'vtiger_cbmap', 'vtiger_cbmapcf');

	/**
	 * Mandatory for Saving, Include tablename and tablekey columnname here.
	 */
	public $tab_name_index = array(
		'vtiger_crmentity' => 'crmid',
		'vtiger_cbmap'   => 'cbmapid',
		'vtiger_cbmapcf' => 'cbmapid',
	);

	/**
	 * Mandatory for Listing (Related listview)
	 */
	public $list_fields = array (
		/* Format: Field Label => array(tablename => columnname) */
		// tablename should not have prefix 'vtiger_'
		'Map Number'=> array('cbmap'=> 'mapnumber'),
		'Map Name'=> array('cbmap'=> 'mapname'),
		'Map Type'=> array('cbmap'=> 'maptype'),
		'Target Module'=> array('cbmap'=> 'targetname'),
		'Description' => array('crmentity'=>'description')
	);
	public $list_fields_name = array(
		/* Format: Field Label => fieldname */
		'Map Number'=> 'mapnumber',
		'Map Name'=> 'mapname',
		'Map Type'=> 'maptype',
		'Target Module'=> 'targetname',
		'Description' => 'description'
	);

	// Make the field link to detail view from list view (Fieldname)
	public $list_link_field = 'mapname';

	// For Popup listview and UI type support
	public $search_fields = array(
		/* Format: Field Label => array(tablename => columnname) */
		// tablename should not have prefix 'vtiger_'
		'Map Number'=> array('cbmap'=> 'mapnumber'),
		'Map Name'=> array('cbmap'=> 'mapname'),
		'Map Type'=> array('cbmap'=> 'maptype'),
		'Target Module'=> array('cbmap'=> 'targetname'),
		'Description' => array('crmentity'=>'description')
	);
	public $search_fields_name = array(
		/* Format: Field Label => fieldname */
		'Map Number'=> 'mapnumber',
		'Map Name'=> 'mapname',
		'Map Type'=> 'maptype',
		'Target Module'=> 'targetname',
		'Description' => 'description'
	);

	// For Popup window record selection
	public $popup_fields = array('mapname');

	// Placeholder for sort fields - All the fields will be initialized for Sorting through initSortFields
	public $sortby_fields = array();

	// For Alphabetical search
	public $def_basicsearch_col = 'mapname';

	// Column value to use on detail view record text display
	public $def_detailview_recname = 'mapname';

	// Required Information for enabling Import feature
	public $required_fields = array('mapname'=>1);

	// Callback function list during Importing
	public $special_functions = array('set_import_assigned_user');

	public $default_order_by = 'mapname';
	public $default_sort_order='ASC';
	// Used when enabling/disabling the mandatory fields for the module.
	// Refers to vtiger_field.fieldname values.
	public $mandatory_fields = array('createdtime', 'modifiedtime', 'mapname');

	public function save_module($module) {
		if ($this->HasDirectImageField) {
			$this->insertIntoAttachment($this->id, $module);
		}
		if (!empty($this->column_fields['content'])) {
			$xml = simplexml_load_string($this->column_fields['content']);
			$json = json_encode($xml);
			global $adb;
			$adb->pquery('update vtiger_cbmap set contentjson=? where cbmapid=?', array($json, $this->id));
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
			$modGV=Vtiger_Module::getInstance('GlobalVariable');
			$modMap=Vtiger_Module::getInstance('cbMap');
			if ($modGV) {
				$blockInstance = VTiger_Block::getInstance('LBL_GLOBAL_VARIABLE_INFORMATION', $modGV);
				$field = new Vtiger_Field();
				$field->name = 'bmapid';
				$field->label= 'cbMap';
				$field->table = $modGV->basetable;
				$field->column = 'bmapid';
				$field->columntype = 'INT(11)';
				$field->uitype = 10;
				$field->displaytype = 1;
				$field->typeofdata = 'V~O';
				$field->presence = 0;
				$blockInstance->addField($field);
				$field->setRelatedModules(array('cbMap'));
				$modMap->setRelatedList($modGV, 'GlobalVariable', array('ADD'), 'get_dependents_list');
			}
			$this->setModuleSeqNumber('configure', $modulename, 'BMAP-', '0000001');
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

	public function retrieve_entity_info($cbmapid, $mname, $deleted = false) {
		global $current_user;
		$holduser = $current_user;
		$current_user = Users::getActiveAdminUser();
		parent::retrieve_entity_info($cbmapid, $mname, $deleted);
		$current_user = $holduser;
	}

	public function __call($name, $arguments) {
		require_once 'modules/cbMap/processmap/'.$name.'.php';
		$processmap = new $name($this);
		return $processmap->processMap($arguments);
	}

	public static function getMapByID($cbmapid) {
		global $adb;
		$query = 'SELECT crmid,setype FROM vtiger_crmentity where crmid=? AND deleted=0';
		$result = $adb->pquery($query, array($cbmapid));
		if ($result && $adb->num_rows($result)>0 && $adb->query_result($result, 0, 'setype') == 'cbMap') {
			$cbmap = new cbMap();
			$cbmap->retrieve_entity_info($cbmapid, 'cbMap');
			return $cbmap;
		} else {
			return null;
		}
	}

	public static function getMapByName($name, $type = '') {
		global $adb;
		$sql = 'select cbmapid
			from vtiger_cbmap
			inner join vtiger_crmentity on crmid=cbmapid
			where deleted=0 and mapname=?';
		$prm = array($name);
		if ($type!='') {
			$sql .= ' and maptype=?';
			$prm[] = $type;
		}
		$mrs = $adb->pquery($sql, $prm);
		if ($mrs && $adb->num_rows($mrs)>0) {
			$cbmapid = $adb->query_result($mrs, 0, 0);
			$cbmap = new cbMap();
			$cbmap->retrieve_entity_info($cbmapid, 'cbMap');
			return $cbmap;
		} else {
			return null;
		}
	}

	public static function getMapIdByName($name) {
		global $adb;
		$mrs = $adb->pquery(
			'select cbmapid
			from vtiger_cbmap
			inner join vtiger_crmentity on crmid=cbmapid
			where deleted=0 and mapname=?',
			array($name)
		);
		if ($mrs && $adb->num_rows($mrs)>0) {
			return $adb->query_result($mrs, 0, 0);
		} else {
			return 0;
		}
	}

	public function getMapArray() {
		$ret = array();
		$name = basename($this->column_fields['maptype']);
		@require_once 'modules/cbMap/processmap/'.$name.'.php';
		if (class_exists($name)) {
			$processmap = new $name($this);
			if (method_exists($processmap, 'convertMap2Array')) {
				$ret = $processmap->convertMap2Array();
			}
		}
		return $ret;
	}

	public function getvtlib_open_popup_window_function($fieldname, $basemodule) {
		if ($fieldname=='brmap' && $basemodule=='BusinessActions') {
			return 'openBRMapInBA';
		} else {
			return 'vtlib_open_popup_window';
		}
	}
}
?>