<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/
require_once('data/CRMEntity.php');
require_once('data/Tracker.php');

class ServiceContracts extends CRMEntity {
	var $db, $log; // Used in class functions of CRMEntity

	var $table_name = 'vtiger_servicecontracts';
	var $table_index= 'servicecontractsid';
	var $column_fields = Array();

	/** Indicator if this is a custom module or standard module */
	var $IsCustomModule = true;
	var $HasDirectImageField = false;
	/**
	 * Mandatory table for supporting custom fields.
	 */
	var $customFieldTable = Array('vtiger_servicecontractscf', 'servicecontractsid');
	// Uncomment the line below to support custom field columns on related lists
	var $related_tables = Array('vtiger_servicecontractscf'=>array('servicecontractsid','vtiger_servicecontracts', 'servicecontractsid'));

	/**
	 * Mandatory for Saving, Include tables related to this module.
	 */
	var $tab_name = Array('vtiger_crmentity', 'vtiger_servicecontracts', 'vtiger_servicecontractscf');

	/**
	 * Mandatory for Saving, Include tablename and tablekey columnname here.
	 */
	var $tab_name_index = Array(
		'vtiger_crmentity' => 'crmid',
		'vtiger_servicecontracts' => 'servicecontractsid',
		'vtiger_servicecontractscf'=>'servicecontractsid');

	/**
	 * Mandatory for Listing (Related listview)
	 */
	var $list_fields = Array (
		/* Format: Field Label => Array(tablename => columnname) */
		// tablename should not have prefix 'vtiger_'
		'Subject' => Array('servicecontracts' => 'subject'),
		'Assigned To' => Array('crmentity' => 'smownerid'),
		'Contract No' => Array('servicecontracts' => 'contract_no'),
		'Used Units' => Array('servicecontracts' => 'used_units'),
		'Total Units' => Array('servicecontracts' => 'total_units')
	);
	var $list_fields_name = Array(
		/* Format: Field Label => fieldname */
		'Subject' => 'subject',
		'Assigned To' => 'assigned_user_id',
		'Contract No' =>  'contract_no',
		'Used Units' => 'used_units',
		'Total Units' => 'total_units'
	);

	// Make the field link to detail view from list view (Fieldname)
	var $list_link_field = 'subject';

	// For Popup listview and UI type support
	var $search_fields = Array(
		/* Format: Field Label => Array(tablename => columnname) */
		// tablename should not have prefix 'vtiger_'
		'Subject' => Array('servicecontracts' => 'subject'),
		'Contract No' => Array('servicecontracts' => 'contract_no'),
		'Assigned To' => Array('vtiger_crmentity' => 'assigned_user_id'),
		'Used Units' => Array('servicecontracts' => 'used_units'),
		'Total Units' => Array('servicecontracts' => 'total_units')
	);
	var $search_fields_name = Array(
		/* Format: Field Label => fieldname */
		'Subject' => 'subject',
		'Contract No' => 'contract_no',
		'Assigned To' => 'assigned_user_id',
		'Used Units' => 'used_units',
		'Total Units' => 'total_units'
	);

	// For Popup window record selection
	var $popup_fields = Array('subject');

	// Placeholder for sort fields - All the fields will be initialized for Sorting through initSortFields
	var $sortby_fields = Array();

	// For Alphabetical search
	var $def_basicsearch_col = 'subject';

	// Column value to use on detail view record text display
	var $def_detailview_recname = 'subject';

	// Required Information for enabling Import feature
	var $required_fields = Array('assigned_user_id'=>1);

	// Callback function list during Importing
	var $special_functions = Array('set_import_assigned_user');

	var $default_order_by = 'subject';
	var $default_sort_order='ASC';
	// Used when enabling/disabling the mandatory fields for the module.
	// Refers to vtiger_field.fieldname values.
	var $mandatory_fields = Array('subject','assigned_user_id');

	function save_module($module) {
		if ($this->HasDirectImageField) {
			$this->insertIntoAttachment($this->id,$module);
		}
		$return_action = $_REQUEST['return_action'];
		$for_module = $_REQUEST['return_module'];
		$for_crmid  = $_REQUEST['return_id'];
		if ($return_action && $for_module && $for_crmid) {
			if ($for_module == 'HelpDesk') {
				$on_focus = CRMEntity::getInstance($for_module);
				$on_focus->save_related_module($for_module, $for_crmid, $module, $this->id);
			}
		}
	}

	/**
	 * Invoked when special actions are performed on the module.
	 * @param String Module name
	 * @param String Event Type (module.postinstall, module.disabled, module.enabled, module.preuninstall)
	 */
	function vtlib_handler($moduleName, $eventType) {
		require_once('include/utils/utils.php');
		global $adb;

 		if($eventType == 'module.postinstall') {
			require_once('vtlib/Vtiger/Module.php');
			$this->setModuleSeqNumber('configure', $modulename, 'srvcto-', '0000001');
			$moduleInstance = Vtiger_Module::getInstance($moduleName);

			$accModuleInstance = Vtiger_Module::getInstance('Accounts');
			$accModuleInstance->setRelatedList($moduleInstance,'Service Contracts',array('add'),'get_dependents_list');

			$conModuleInstance = Vtiger_Module::getInstance('Contacts');
			$conModuleInstance->setRelatedList($moduleInstance,'Service Contracts',array('add'),'get_dependents_list');

			$helpDeskInstance = Vtiger_Module::getInstance("HelpDesk");
			$helpDeskInstance->setRelatedList($moduleInstance,"Service Contracts",Array('ADD','SELECT'));

			// Initialize module sequence for the module
			$adb->pquery("INSERT into vtiger_modentity_num values(?,?,?,?,?,?)",array($adb->getUniqueId("vtiger_modentity_num"),$moduleName,'SERCON',1,1,1));

			// Make the picklist value 'Complete' for status as non-editable
			$adb->query("UPDATE vtiger_contract_status SET presence=0 WHERE contract_status='Complete'");

			// Mark the module as Standard module
			$adb->pquery('UPDATE vtiger_tab SET customized=0 WHERE name=?', array($moduleName));

		} else if($eventType == 'module.disabled') {
			$em = new VTEventsManager($adb);
			$em->setHandlerInActive('ServiceContractsHandler');

		} else if($eventType == 'module.enabled') {
			$em = new VTEventsManager($adb);
			$em->setHandlerActive('ServiceContractsHandler');

		} else if($eventType == 'module.preuninstall') {
			// TODO Handle actions when this module is about to be deleted.
		} else if($eventType == 'module.preupdate') {
			// TODO Handle actions before this module is updated.
		} else if($eventType == 'module.postupdate') {
			// TODO Handle actions after this module is updated.
		}
	}

	/**
	 * Handle saving related module information.
	 * NOTE: This function has been added to CRMEntity (base class).
	 * You can override the behavior by re-defining it here.
	 */
	function save_related_module($module, $crmid, $with_module, $with_crmids) {

		$with_crmids = (array)$with_crmids;
		foreach($with_crmids as $with_crmid) {
			parent::save_related_module($module, $crmid, $with_module, $with_crmid);
			if ($with_module == 'HelpDesk') {
				$this->updateHelpDeskRelatedTo($crmid,$with_crmid);
				$this->updateServiceContractState($crmid);
			}
		}
	}

	// Function to Update the parent_id of HelpDesk with sc_related_to of ServiceContracts if the parent_id is not set.
	function updateHelpDeskRelatedTo($focusId, $entityIds) {
		global $log;
		$log->debug('Entering into function updateHelpDeskRelatedTo');

		$entityIds = (array)$entityIds;
		$selectTicketsQuery = "SELECT ticketid FROM vtiger_troubletickets WHERE (parent_id IS NULL OR parent_id = 0) AND ticketid IN (" . generateQuestionMarks($entityIds) .")";
		$selectTicketsResult = $this->db->pquery($selectTicketsQuery, array($entityIds));
		$noOfTickets = $this->db->num_rows($selectTicketsResult);
		for ($i=0; $i < $noOfTickets; ++$i) {
			$ticketId = $this->db->query_result($selectTicketsResult,$i,'ticketid');
			$updateQuery = "UPDATE vtiger_troubletickets, vtiger_servicecontracts SET parent_id=vtiger_servicecontracts.sc_related_to" .
				" WHERE vtiger_servicecontracts.sc_related_to IS NOT NULL AND vtiger_servicecontracts.sc_related_to != 0" .
				" AND vtiger_servicecontracts.servicecontractsid = ? AND vtiger_troubletickets.ticketid = ?";
			$updateResult = $this->db->pquery($updateQuery, array($focusId, $ticketId));
		}

		$log->debug('Exit from function updateHelpDeskRelatedTo');
	}

	// Function to Compute and Update the Used Units and Progress of the Service Contract based on all the related Trouble tickets.
	function updateServiceContractState($focusId) {
		$this->id = $focusId;
		$this->retrieve_entity_info($focusId,'ServiceContracts');

		$contractTicketsResult = $this->db->pquery("SELECT relcrmid FROM vtiger_crmentityrel
														WHERE module = 'ServiceContracts'
														AND relmodule = 'HelpDesk' AND crmid = ?
													UNION
														SELECT crmid FROM vtiger_crmentityrel
														WHERE relmodule = 'ServiceContracts'
														AND module = 'HelpDesk' AND relcrmid = ?",
													array($focusId,$focusId));

		$noOfTickets = $this->db->num_rows($contractTicketsResult);
		$ticketFocus = CRMEntity::getInstance('HelpDesk');
		$totalUsedUnits = 0;
		for($i=0; $i < $noOfTickets; ++$i) {
			$ticketId = $this->db->query_result($contractTicketsResult, $i, 'relcrmid');
			$ticketFocus->id = $ticketId;
			if(isRecordExists($ticketId)) {
				$ticketFocus->retrieve_entity_info($ticketId, 'HelpDesk');
				if (strtolower($ticketFocus->column_fields['ticketstatus']) == 'closed') {
					$totalUsedUnits += $this->computeUsedUnits($ticketFocus->column_fields);
				}
			}
		}
		$this->updateUsedUnits($totalUsedUnits);

		$this->calculateProgress();
	}

	// Function to Upate the Used Units of the Service Contract based on the given Ticket id.
	function computeUsedUnits($ticketData, $operator='+') {
		$trackingUnit = strtolower($this->column_fields['tracking_unit']);
		$workingHoursPerDay = 24;

		$usedUnits = 0;
		if ($trackingUnit == 'incidents') {
			$usedUnits = 1;
		} elseif ($trackingUnit == 'days') {
			if(!empty($ticketData['days'])) {
				$usedUnits = $ticketData['days'];
			} elseif(!empty($ticketData['hours'])) {
				$usedUnits = $ticketData['hours'] / $workingHoursPerDay;
			}
		} elseif ($trackingUnit == 'hours') {
			if(!empty($ticketData['hours'])) {
				$usedUnits = $ticketData['hours'];
			} elseif(!empty($ticketData['days'])) {
				$usedUnits = $ticketData['days'] * $workingHoursPerDay;
			}
		}
		return $usedUnits;
	}

	// Function to Upate the Used Units of the Service Contract.
	function updateUsedUnits($usedUnits) {
		$this->column_fields['used_units'] = $usedUnits;
		$updateQuery = "UPDATE vtiger_servicecontracts SET used_units = $usedUnits WHERE servicecontractsid = ?";
		$this->db->pquery($updateQuery, array($this->id));
	}

	// Function to Calculate the End Date, Planned Duration, Actual Duration and Progress of a Service Contract
	function calculateProgress() {
		$updateCols = array();
		$updateParams = array();

		$startDate = $this->column_fields['start_date'];
		$dueDate = $this->column_fields['due_date'];
		$endDate = $this->column_fields['end_date'];

		$usedUnits = $this->column_fields['used_units'];
		$totalUnits = $this->column_fields['total_units'];

		$contractStatus = $this->column_fields['contract_status'];

		// Update the End date if the status is Complete or if the Used Units reaches/exceeds Total Units
		// We need to do this first to make sure Actual duration is computed properly
		if($contractStatus == 'Complete' || (!empty($usedUnits) && !empty($totalUnits) && $usedUnits >= $totalUnits)) {
			if(empty($endDate)) {
				$endDate = date('Y-m-d');
				$this->db->pquery('UPDATE vtiger_servicecontracts SET end_date=? WHERE servicecontractsid = ?', array(date('Y-m-d'), $this->id));
			}
		} else {
			$endDate = null;
			$this->db->pquery('UPDATE vtiger_servicecontracts SET end_date=? WHERE servicecontractsid = ?', array(null, $this->id));
		}

		// Calculate the Planned Duration based on Due date and Start date. (in days)
		if(!empty($dueDate) && !empty($startDate)) {
			$plannedDurationUpdate = " planned_duration = (TO_DAYS(due_date)-TO_DAYS(start_date)+1)";
		} else {
			$plannedDurationUpdate = " planned_duration = ''";
		}
		$updateCols[] = $plannedDurationUpdate;

		// Calculate the Actual Duration based on End date and Start date. (in days)
		if(!empty($endDate) && !empty($startDate)) {
			$actualDurationUpdate = "actual_duration = (TO_DAYS(end_date)-TO_DAYS(start_date)+1)";
		} else {
			$actualDurationUpdate = "actual_duration = ''";
		}
		$updateCols[] = $actualDurationUpdate;

		// Update the Progress based on Used Units and Total Units (in percentage)
		if(!empty($usedUnits) && !empty($totalUnits) && $totalUnits > 0) {
			$progressUpdate = 'progress = ?';
			$progressUpdateParams = (float)(($usedUnits * 100) / $totalUnits);
		} else {
			$progressUpdate = 'progress = ?';
			$progressUpdateParams = null;
		}
		$updateCols[] = $progressUpdate;
		$updateParams[] = $progressUpdateParams;

		if(count($updateCols) > 0) {
			$updateQuery = 'UPDATE vtiger_servicecontracts SET '. implode(",", $updateCols) .' WHERE servicecontractsid = ?';
			$updateParams[] = $this->id;
			$this->db->pquery($updateQuery, $updateParams);
		}
	}

	/**
	 * Handle deleting related module information.
	 * NOTE: This function has been added to CRMEntity (base class).
	 * You can override the behavior by re-defining it here.
	 */
	function delete_related_module($module, $crmid, $with_module, $with_crmid) {
	 	parent::delete_related_module($module, $crmid, $with_module, $with_crmid);
	 	if ($with_module == 'HelpDesk') {
	 		$this->updateServiceContractState($crmid);
	 	}
	}

	/**
	 * Handle getting related list information.
	 * NOTE: This function has been added to CRMEntity (base class).
	 * You can override the behavior by re-defining it here.
	 */
	//function get_related_list($id, $cur_tab_id, $rel_tab_id, $actions=false) { }

	/**
	 * Handle getting dependents list information.
	 * NOTE: This function has been added to CRMEntity (base class).
	 * You can override the behavior by re-defining it here.
	 */
	//function get_dependents_list($id, $cur_tab_id, $rel_tab_id, $actions=false) { }
}
?>
