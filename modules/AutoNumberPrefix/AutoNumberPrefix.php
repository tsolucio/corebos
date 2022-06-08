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

class AutoNumberPrefix extends CRMEntity {
	public $table_name = 'vtiger_autonumberprefix';
	public $table_index= 'autonumberprefixid';
	public $column_fields = array();

	/** Indicator if this is a custom module or standard module */
	public $IsCustomModule = true;
	public $HasDirectImageField = false;
	public $moduleIcon = array('library' => 'standard', 'containerClass' => 'slds-icon_container slds-icon-standard-account', 'class' => 'slds-icon', 'icon'=>'number_input');

	/**
	 * Mandatory table for supporting custom fields.
	 */
	public $customFieldTable = array('vtiger_autonumberprefix', 'autonumberprefixid');
	// Uncomment the line below to support custom field columns on related lists
	// public $related_tables = array('vtiger_autonumberprefix'=>array('autonumberprefixid','vtiger_autonumberprefix', 'autonumberprefixid'));

	/**
	 * Mandatory for Saving, Include tables related to this module.
	 */
	public $tab_name = array('vtiger_crmentity', 'vtiger_autonumberprefix', 'vtiger_autonumberprefixcf');

	/**
	 * Mandatory for Saving, Include tablename and tablekey columnname here.
	 */
	public $tab_name_index = array(
		'vtiger_crmentity'          => 'crmid',
		'vtiger_autonumberprefix'   => 'autonumberprefixid',
		'vtiger_autonumberprefixcf' => 'autonumberprefixid',
	);

	/**
	 * Mandatory for Listing (Related listview)
	 */
	public $list_fields = array(
		/* Format: Field Label => array(tablename => columnname) */
		// tablename should not have prefix 'vtiger_'
		'AutoPrefixNumber No'=> array('autonumberprefix' => 'autonumberprefixno'),
		'Prefix'             => array('autonumberprefix' => 'prefix'),
		'Module'             => array('autonumberprefix' => 'semodule'),
		'Format'             => array('autonumberprefix' => 'format'),
		'Active'             => array('autonumberprefix' => 'active'),
		'Current Value'      => array('autonumberprefix' => 'current'),
		'Default'            => array('autonumberprefix' => 'default1'),
	);
	public $list_fields_name = array(
		/* Format: Field Label => fieldname */
		'AutoPrefixNumber No'=> 'autonumberprefixno',
		'Prefix'             => 'prefix',
		'Module'             => 'semodule',
		'Format'             => 'format',
		'Active'             => 'active',
		'Current Value'      => 'current',
		'Default'            => 'default1',
	);

	// Make the field link to detail view from list view (Fieldname)
	public $list_link_field = 'autonumberprefixno';

	// For Popup listview and UI type support
	public $search_fields = array(
		/* Format: Field Label => array(tablename => columnname) */
		// tablename should not have prefix 'vtiger_'
		'AutoPrefixNumber No'=> array('autonumberprefix' => 'autonumberprefixno'),
		'Prefix'             => array('autonumberprefix' => 'prefix'),
		'Module'             => array('autonumberprefix' => 'semodule'),
		'Format'             => array('autonumberprefix' => 'format'),
		'Active'             => array('autonumberprefix' => 'active'),
		'Current Value'      => array('autonumberprefix' => 'current'),
		'Default'            => array('autonumberprefix' => 'default1'),
	);
	public $search_fields_name = array(
		/* Format: Field Label => fieldname */
		'AutoPrefixNumber No'=> 'autonumberprefixno',
		'Prefix'             => 'prefix',
		'Module'             => 'semodule',
		'Format'             => 'format',
		'Active'             => 'active',
		'Current Value'      => 'current',
		'Default'            => 'default1',
	);

	// For Popup window record selection
	public $popup_fields = array('autonumberprefixno');

	// Placeholder for sort fields - All the fields will be initialized for Sorting through initSortFields
	public $sortby_fields = array();

	// For Alphabetical search
	public $def_basicsearch_col = 'autonumberprefixno';

	// Column value to use on detail view record text display
	public $def_detailview_recname = 'autonumberprefixno';

	// Required Information for enabling Import feature
	public $required_fields = array('autonumberprefixno'=>1);

	// Callback function list during Importing
	public $special_functions = array('set_import_assigned_user');

	public $default_order_by = 'autonumberprefixno';
	public $default_sort_order='ASC';
	// Used when enabling/disabling the mandatory fields for the module.
	// Refers to vtiger_field.fieldname values.
	public $mandatory_fields = array('createdtime', 'modifiedtime', 'autonumberprefixno');

	public function save_module($module) {
		if ($this->HasDirectImageField) {
			$this->insertIntoAttachment($this->id, $module);
		}
	}

	/**
	 * Invoked when special actions are performed on the module.
	 * @param string Module name
	 * @param string Event Type (module.postinstall, module.disabled, module.enabled, module.preuninstall)
	 */
	public function vtlib_handler($modulename, $event_type) {
		require_once 'include/utils/utils.php';
		global $adb;
		if ($event_type == 'module.postinstall') {
			// Handle post installation actions
			$em = new VTEventsManager($adb);
			$em->registerHandler('corebos.filter.ModuleSeqNumber.set', 'modules/AutoNumberPrefix/PrefixEvent.php', 'PrefixEvent');
			$em->registerHandler('corebos.filter.ModuleSeqNumber.increment', 'modules/AutoNumberPrefix/PrefixEvent.php', 'PrefixEvent');
			$em->registerHandler('corebos.filter.ModuleSeqNumber.get', 'modules/AutoNumberPrefix/PrefixEvent.php', 'PrefixEvent');
			$em->registerHandler('corebos.filter.ModuleSeqNumber.fillempty', 'modules/AutoNumberPrefix/PrefixEvent.php', 'PrefixEvent');
			$adb->pquery('UPDATE vtiger_settings_field SET active=? WHERE vtiger_settings_field.name in (?)', array('1', 'LBL_CUSTOMIZE_MODENT_NUMBER'));
			$cnmsg = $adb->getColumnNames('com_vtiger_workflows_expfunctions');
			if (!in_array('funcdesc', $cnmsg)) {
				$adb->query('ALTER TABLE `com_vtiger_workflows_expfunctions` ADD `funcdesc` varchar(2000) NULL;');
				$adb->pquery('UPDATE com_vtiger_workflows_expfunctions SET funcdesc=expinfo', array());
			}
			if (!in_array('funccategory', $cnmsg)) {
				$adb->query('ALTER TABLE `com_vtiger_workflows_expfunctions` ADD `funccategory` varchar(25) NULL;');
				$adb->pquery('UPDATE com_vtiger_workflows_expfunctions SET funccategory=?', array('["Application"]'));
			}
			if (!in_array('funcparam', $cnmsg)) {
				$adb->query('ALTER TABLE `com_vtiger_workflows_expfunctions` ADD `funcparam` varchar(2000) NULL;');
				$adb->pquery('UPDATE com_vtiger_workflows_expfunctions SET funcparam=?', array('[]'));
			}
			if (!in_array('funcexamples', $cnmsg)) {
				$adb->query('ALTER TABLE `com_vtiger_workflows_expfunctions` ADD `funcexamples` varchar(2000) NULL;');
				$adb->pquery('UPDATE com_vtiger_workflows_expfunctions SET funcexamples=?', array('[]'));
			}
			$adb->pquery(
				'INSERT INTO `com_vtiger_workflows_expfunctions`
					(`expname`, `expinfo`, `funcname`, `funcfile`, rawparams, needscontext, funcdesc, funccategory, funcparam, funcexamples)
					VALUES (?,?,?,?,?,?,?,?,?,?)',
				array(
					'AutoNumberInc',
					'AutoNumberInc(ANPid)',
					'__cb_autonumber_inc',
					'modules/AutoNumberPrefix/wf_autonum.php',
					0,
					1,
					'Assigns the next counter value to any field based on the rules of the record',
					json_encode(array('Application')),
					json_encode(array(
						array(
							'name' => 'ANPid',
							'type' => 'Number/String',
							'optional' => false,
							'desc' => 'CRMID or AutoNumber field value of the record with details to get next increment value',
						),
					)),
					json_encode(array(
						'AutoNumberInc(999)',
						"AutoNumberInc('ANPx-00001')",
					)),
				)
			);
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

	public function AutoNumberIncrement($entity = null) {
		global $default_charset,$adb;
		$anpid = $this->id;
		$prefix = $this->column_fields['prefix'];
		$prefix = html_entity_decode($prefix, ENT_QUOTES, $default_charset);
		$curid = $this->column_fields['current'];
		$isworkflowexpression = $this->column_fields['isworkflowexpression'];
		$format = $this->column_fields['format'];
		$format = html_entity_decode($format, ENT_QUOTES, $default_charset);
		if ($isworkflowexpression) {
			$format = sprintf($format, $curid);
			$parser = new VTExpressionParser(new VTExpressionSpaceFilter(new VTExpressionTokenizer($format)));
			$expression = $parser->expression();
			$exprEvaluater = new VTFieldExpressionEvaluater($expression);
			$prev_inv_no = $prefix . $exprEvaluater->evaluate($entity);
		} else {
			if (is_numeric($format)) {
				$fmtlen = strlen($format);
				$temp = str_repeat('0', $fmtlen);
				$numchars = max(strlen($curid), $fmtlen);
				$prev_inv_no = $prefix . substr($temp.$curid, -$numchars);
			} else {
				$prev_inv_no = $prefix . sprintf(date($format, time()), $curid);
			}
		}
		$adb->pquery('UPDATE vtiger_autonumberprefix SET current=current+1 where autonumberprefixid=?', array($anpid));
		return decode_html($prev_inv_no);
	}
}
?>
