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

class cbSurveyAnswer extends CRMEntity {
	public $db;
	public $log;

	public $table_name = 'vtiger_cbsurveyanswer';
	public $table_index= 'cbsurveyanswerid';
	public $column_fields = array();

	/** Indicator if this is a custom module or standard module */
	public $IsCustomModule = true;
	public $HasDirectImageField = false;
	/**
	 * Mandatory table for supporting custom fields.
	 */
	public $customFieldTable = array('vtiger_cbsurveyanswercf', 'cbsurveyanswerid');

	/**
	 * Mandatory for Saving, Include tables related to this module.
	 */
	public $tab_name = array('vtiger_crmentity', 'vtiger_cbsurveyanswer', 'vtiger_cbsurveyanswercf');

	/**
	 * Mandatory for Saving, Include tablename and tablekey columnname here.
	 */
	public $tab_name_index = array(
		'vtiger_crmentity' => 'crmid',
		'vtiger_cbsurveyanswer'   => 'cbsurveyanswerid',
		'vtiger_cbsurveyanswercf' => 'cbsurveyanswerid',
	);

	/**
	 * Mandatory for Listing (Related listview)
	 */
	public $list_fields = array(
		/* Format: Field Label => array(tablename => columnname) */
		// tablename should not have prefix 'vtiger_'
		'cbsurveyanswer_no' => array('cbsurveyanswer' => 'cbsurveyanswer_no'),
		'relatedwith' => array('cbsurveyanswer' => 'relatedwith'),
		'cbsurvey' => array('cbsurveyanswer' => 'cbsurvey'),
		'cbsurveydone' => array('cbsurveyanswer' => 'cbsurveydone'),
		'cbsurveyq' => array('cbsurveyanswer' => 'cbsurveyq'),
		'positive' => array('cbsurveyanswer' => 'positive'),
		'Assigned To' => array('crmentity' =>'smownerid')
	);
	public $list_fields_name = array(
		/* Format: Field Label => fieldname */
		'cbsurveyanswer_no' => 'cbsurveyanswer_no',
		'relatedwith' => 'relatedwith',
		'cbsurvey' => 'cbsurvey',
		'cbsurveydone' => 'cbsurveydone',
		'cbsurveyq' => 'cbsurveyq',
		'positive' => 'positive',
		'Assigned To' => 'assigned_user_id'
	);

	// Make the field link to detail view from list view (Fieldname)
	public $list_link_field = 'cbsurveyanswer_no';

	// For Popup listview and UI type support
	public $search_fields = array(
		/* Format: Field Label => array(tablename => columnname) */
		// tablename should not have prefix 'vtiger_'
		'cbsurveyanswer_no' => array('cbsurveyanswer' => 'cbsurveyanswer_no'),
		'relatedwith' => array('cbsurveyanswer' => 'relatedwith'),
		'cbsurvey' => array('cbsurveyanswer' => 'cbsurvey'),
		'cbsurveydone' => array('cbsurveyanswer' => 'cbsurveydone'),
		'cbsurveyq' => array('cbsurveyanswer' => 'cbsurveyq'),
	);
	public $search_fields_name = array(
		/* Format: Field Label => fieldname */
		'cbsurveyanswer_no' => 'cbsurveyanswer_no',
		'relatedwith' => 'relatedwith',
		'cbsurvey' => 'cbsurvey',
		'cbsurveydone' => 'cbsurveydone',
		'cbsurveyq' => 'cbsurveyq',
	);

	// For Popup window record selection
	public $popup_fields = array('cbsurveyanswer_no');

	// Placeholder for sort fields - All the fields will be initialized for Sorting through initSortFields
	public $sortby_fields = array();

	// For Alphabetical search
	public $def_basicsearch_col = 'cbsurveyanswer_no';

	// Column value to use on detail view record text display
	public $def_detailview_recname = 'cbsurveyanswer_no';

	// Required Information for enabling Import feature
	public $required_fields = array('cbsurveyanswer_no'=>1);

	// Callback function list during Importing
	public $special_functions = array('set_import_assigned_user');

	public $default_order_by = 'cbsurveyanswer_no';
	public $default_sort_order='ASC';
	// Used when enabling/disabling the mandatory fields for the module.
	// Refers to vtiger_field.fieldname values.
	public $mandatory_fields = array('createdtime', 'modifiedtime', 'cbsurveyanswer_no');

	public function save_module($module) {
		global $adb;
		if ($this->HasDirectImageField) {
			$this->insertIntoAttachment($this->id, $module);
		}
		// update survey readonly field
		if (!empty($this->column_fields['cbsurveyq'])) {
			$rssrv = $adb->pquery('select cbsurvey from vtiger_cbsurveyquestion where cbsurveyquestionid=?', array($this->column_fields['cbsurveyq']));
			$srvid = $adb->query_result($rssrv, 0, 0);
		} elseif (!empty($this->column_fields['cbsurveydone'])) {
			$rssrv = $adb->pquery('select cbsurvey from vtiger_cbsurveydone where cbsurveydoneid=?', array($this->column_fields['cbsurveydone']));
			$srvid = $adb->query_result($rssrv, 0, 0);
		} else {
			$srvid = 0;
		}
		$adb->pquery('update vtiger_cbsurveyanswer set cbsurvey=? where cbsurveyanswerid=?', array($srvid, $this->id));
	}

	/**
	 * Invoked when special actions are performed on the module.
	 * @param String Module name
	 * @param String Event Type (module.postinstall, module.disabled, module.enabled, module.preuninstall)
	 */
	public function vtlib_handler($modulename, $event_type) {
		if ($event_type == 'module.postinstall') {
			// TODO Handle post installation actions
			$this->setModuleSeqNumber('configure', $modulename, 'srvya-', '0000001');
			$relwith = array('Leads', 'Accounts', 'Contacts', 'cbSurvey', 'cbSurveyDone', 'cbSurveyQuestion');
			$module = Vtiger_Module::getInstance($modulename);
			foreach ($relwith as $m) {
				$mod = Vtiger_Module::getInstance($m);
				$mod->setRelatedList($module, 'cbSurveyAnswer', array('ADD'), 'get_dependents_list');
			}
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
