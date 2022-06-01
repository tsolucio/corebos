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

class cbProcessStep extends CRMEntity {
	public $table_name = 'vtiger_cbprocessstep';
	public $table_index= 'cbprocessstepid';
	public $column_fields = array();

	/** Indicator if this is a custom module or standard module */
	public $IsCustomModule = true;
	public $HasDirectImageField = false;
	public $moduleIcon = array('library' => 'standard', 'containerClass' => 'slds-icon_container slds-icon-standard-account', 'class' => 'slds-icon', 'icon'=>'steps');

	/**
	 * Mandatory table for supporting custom fields.
	 */
	public $customFieldTable = array('vtiger_cbprocessstepcf', 'cbprocessstepid');
	// related_tables variable should define the association (relation) between dependent tables
	// FORMAT: related_tablename => array(related_tablename_column[, base_tablename, base_tablename_column[, related_module]] )
	// Here base_tablename_column should establish relation with related_tablename_column
	// NOTE: If base_tablename and base_tablename_column are not specified, it will default to modules (table_name, related_tablename_column)
	// Uncomment the line below to support custom field columns on related lists
	// public $related_tables = array('vtiger_cbprocessstepcf' => array('cbprocessstepid', 'vtiger_cbprocessstep', 'cbprocessstepid', 'cbprocessstep'));

	/**
	 * Mandatory for Saving, Include tables related to this module.
	 */
	public $tab_name = array('vtiger_crmentity', 'vtiger_cbprocessstep', 'vtiger_cbprocessstepcf');

	/**
	 * Mandatory for Saving, Include tablename and tablekey columnname here.
	 */
	public $tab_name_index = array(
		'vtiger_crmentity' => 'crmid',
		'vtiger_cbprocessstep'   => 'cbprocessstepid',
		'vtiger_cbprocessstepcf' => 'cbprocessstepid',
	);

	/**
	 * Mandatory for Listing (Related listview)
	 */
	public $list_fields = array(
		/* Format: Field Label => array(tablename => columnname) */
		// tablename should not have prefix 'vtiger_'
		'cbprocessstep_no'=> array('cbprocessstep' => 'cbprocessstep_no'),
		'processflow' => array('cbprocessstep' => 'processflow'),
		'fromstep' => array('cbprocessstep' => 'fromstep'),
		'tostep' => array('cbprocessstep' => 'tostep'),
		'active' => array('cbprocessstep' => 'active'),
		'Assigned To' => array('crmentity' => 'smownerid')
	);
	public $list_fields_name = array(
		/* Format: Field Label => fieldname */
		'cbprocessstep_no'=> 'cbprocessstep_no',
		'processflow' => 'processflow',
		'fromstep' => 'fromstep',
		'tostep' => 'tostep',
		'active' => 'active',
		'Assigned To' => 'assigned_user_id'
	);

	// Make the field link to detail view from list view (Fieldname)
	public $list_link_field = 'cbprocessstep_no';

	// For Popup listview and UI type support
	public $search_fields = array(
		/* Format: Field Label => array(tablename => columnname) */
		// tablename should not have prefix 'vtiger_'
		'cbprocessstep_no'=> array('cbprocessstep' => 'cbprocessstep_no'),
		'processflow' => array('cbprocessstep' => 'processflow'),
		'fromstep' => array('cbprocessstep' => 'fromstep'),
		'tostep' => array('cbprocessstep' => 'tostep'),
		'active' => array('cbprocessstep' => 'active'),
	);
	public $search_fields_name = array(
		/* Format: Field Label => fieldname */
		'cbprocessstep_no'=> 'cbprocessstep_no',
		'processflow' => 'processflow',
		'fromstep' => 'fromstep',
		'tostep' => 'tostep',
		'active' => 'active',
	);

	// For Popup window record selection
	public $popup_fields = array('cbprocessstep_no');

	// Placeholder for sort fields - All the fields will be initialized for Sorting through initSortFields
	public $sortby_fields = array();

	// For Alphabetical search
	public $def_basicsearch_col = 'cbprocessstep_no';

	// Column value to use on detail view record text display
	public $def_detailview_recname = 'cbprocessstep_no';

	// Required Information for enabling Import feature
	public $required_fields = array('cbprocessstep_no'=>1);

	// Callback function list during Importing
	public $special_functions = array('set_import_assigned_user');

	public $default_order_by = 'cbprocessstep_no';
	public $default_sort_order='ASC';
	// Used when enabling/disabling the mandatory fields for the module.
	// Refers to vtiger_field.fieldname values.
	public $mandatory_fields = array('createdtime', 'modifiedtime', 'cbprocessstep_no');

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
			global $adb;
			// TODO Handle post installation actions
			$this->setModuleSeqNumber('configure', $modulename, 'bpmstp-', '000000001');
			// Relation with Workflows
			$module = Vtiger_Module::getInstance($modulename);
			$newrelid = $adb->getUniqueID('vtiger_relatedlists');
			$adb->query("INSERT INTO vtiger_relatedlists
				(relation_id, tabid, related_tabid, name, sequence, label, presence, actions,relationtype) VALUES
				($newrelid, ".$module->id.", 0, 'getPositivetasks', '1', 'PostiveValidationTasks',0,'ADD,SELECT','N:N');");
			$newrelid = $adb->getUniqueID('vtiger_relatedlists');
			$adb->query("INSERT INTO vtiger_relatedlists
				(relation_id, tabid, related_tabid, name, sequence, label, presence, actions,relationtype) VALUES
				($newrelid, ".$module->id.", 0, 'getNegativetasks', '2', 'NegtiveValidationTasks',0,'ADD,SELECT','N:N');");
			$modplog = Vtiger_Module::getInstance('ProcessLog');
			$module->setRelatedList($modplog, 'ProcessLog', array('ADD'), 'get_dependents_list');
			require_once 'include/events/include.inc';
			$em = new VTEventsManager($adb);
			$em->registerHandler('corebos.relatedlist.dellink', 'modules/cbProcessStep/workflowRelatedListLinks.php', 'workflowRelatedListLinks');
			echo "<h4>dellink filter registered.</h4>";
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

	public function getWorkflowRLWithSign($id, $cur_tab_id, $rel_tab_id, $actions, $positive) {
		require_once 'modules/com_vtiger_workflow/VTWorkflow.php';
		global $currentModule, $singlepane_view, $adb;
		$related_module = 'com_vtiger_workflow';
		$other = new Workflow();
		unset($other->list_fields['Tools'], $other->list_fields_name['Tools']);
		$button = '';
		if ($actions) {
			if (is_string($actions)) {
				$actions = explode(',', strtoupper($actions));
			}
			if (in_array('SELECT', $actions) && isPermitted($related_module, 4, '') == 'yes') {
				$button .= "<input title='" . getTranslatedString('LBL_SELECT') . ' ' . getTranslatedString($related_module, $related_module).
					"' class='crmbutton small edit' type='button' onclick=\"return window.open('index.php?module=$related_module&return_module=$currentModule".
					"&cbcustompopupinfo=bpmsteprl&bpmsteprl=".($positive ? 'positive' : 'negative').
					"&action=Popup&popuptype=detailview&select=enable&form=EditView&form_submit=false&recordid=$id','test',".
					"'width=640,height=602,resizable=0,scrollbars=0');\" value='" . getTranslatedString('LBL_SELECT') . ' '.
					getTranslatedString($related_module, $related_module) . "'>&nbsp;";
			}
			if (in_array('ADD', $actions) && isPermitted($related_module, 1, '') == 'yes') {
				$singular_modname = getTranslatedString('SINGLE_' . $related_module, $related_module);
				$button .= "<input type='hidden' name='createmode' value='link' />" .
					"<input title='" . getTranslatedString('LBL_ADD_NEW') . " " . $singular_modname . "' class='crmbutton small create'" .
					" onclick='this.form.action.value=\"workflowlist\";this.form.module.value=\"$related_module\"' type='submit' name='button'" .
					" value='" . getTranslatedString('LBL_ADD_NEW') . " " . $singular_modname . "'>&nbsp;";
			}
		}

		// To make the edit or del link actions to return back to same view.
		if ($singlepane_view == 'true') {
			$returnset = "&return_module=$currentModule&return_action=DetailView&return_id=$id";
		} else {
			$returnset = "&return_module=$currentModule&return_action=CallRelatedList&return_id=$id";
		}

		// we have to cleanup the relations because workflow doesn't do it, so when a workflow is deleted, that ID is not deleted from the relation
		$adb->query('delete from vtiger_cbprocesssteprel where wfid not in (select workflow_id from com_vtiger_workflows)');

		$query = 'SELECT *,workflow_id as crmid ';
		$query .= ' FROM com_vtiger_workflows';
		$query .= ' INNER JOIN vtiger_cbprocesssteprel ON wfid = workflow_id';
		$query .= " WHERE stepid = $id and ".($positive ? '' : '!').'positive';

		$return_value = GetRelatedList($currentModule, $related_module, $other, $query, $button, $returnset);

		if ($return_value == null) {
			$return_value = array('header'=>array(),'entries'=>array(),'navigation'=>array('',''));
		}
		$return_value['CUSTOM_BUTTON'] = $button;

		return $return_value;
	}

	public function getNegativetasks($id, $cur_tab_id, $rel_tab_id, $actions = false) {
		return $this->getWorkflowRLWithSign($id, $cur_tab_id, $rel_tab_id, $actions, 0);
	}

	public function getPositivetasks($id, $cur_tab_id, $rel_tab_id, $actions = false) {
		return $this->getWorkflowRLWithSign($id, $cur_tab_id, $rel_tab_id, $actions, 1);
	}

	/**
	 * Handle saving related module information.
	 * NOTE: This function has been added to CRMEntity (base class).
	 * You can override the behavior by re-defining it here.
	 */
	public function save_related_module($module, $crmid, $with_module, $with_crmid) {
		global $adb;
		$positive = (isset($_REQUEST['bpmsteprl']) && $_REQUEST['bpmsteprl']=='positive') ? '1' : '0';
		$with_crmid = (array)$with_crmid;
		foreach ($with_crmid as $relcrmid) {
			if ($with_module == 'com_vtiger_workflow') {
				$checkpresence = $adb->pquery('SELECT 1 FROM vtiger_cbprocesssteprel WHERE stepid=? AND wfid=? AND positive=?', array($crmid, $relcrmid, $positive));
				if ($checkpresence && $adb->num_rows($checkpresence)) {
					continue;
				}
				$adb->pquery('INSERT INTO vtiger_cbprocesssteprel(stepid, wfid, positive) VALUES(?,?,?)', array($crmid, $relcrmid, $positive));
			} else {
				parent::save_related_module($module, $crmid, $with_module, $with_crmid);
			}
		}
	}

	/**
	 * Handle deleting related module information.
	 * NOTE: This function has been added to CRMEntity (base class).
	 * You can override the behavior by re-defining it here.
	 */
	public function delete_related_module($module, $crmid, $with_module, $with_crmid) {
		global $adb;
		$with_crmid = (array)$with_crmid;
		$data = array();
		$data['sourceModule'] = $module;
		$data['sourceRecordId'] = $crmid;
		$data['destinationModule'] = $with_module;
		if ($with_module == 'com_vtiger_workflow') {
			$positive = (isset($_REQUEST['bpmsteprl']) && $_REQUEST['bpmsteprl']=='positive') ? '1' : '0';
			foreach ($with_crmid as $relcrmid) {
				$data['destinationRecordId'] = $relcrmid;
				cbEventHandler::do_action('corebos.entity.link.delete', $data);
				$adb->pquery(
					'DELETE FROM vtiger_cbprocesssteprel WHERE stepid=? AND wfid=? AND positive=?',
					array($crmid, $relcrmid, $positive)
				);
				cbEventHandler::do_action('corebos.entity.link.delete.final', $data);
			}
		} else {
			parent::delete_related_module($module, $crmid, $with_module, $with_crmid);
		}
	}

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
