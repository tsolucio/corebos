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

class Project extends CRMEntity {
	var $db, $log; // Used in class functions of CRMEntity

	var $table_name = 'vtiger_project';
	var $table_index= 'projectid';
	var $column_fields = Array();

	/** Indicator if this is a custom module or standard module */
	var $IsCustomModule = true;
	var $HasDirectImageField = false;
	/**
	 * Mandatory table for supporting custom fields.
	 */
	var $customFieldTable = Array('vtiger_projectcf', 'projectid');
	// Uncomment the line below to support custom field columns on related lists
	// var $related_tables = Array('vtiger_projectcf'=>array('projectid','vtiger_project', 'projectid'));

	/**
	 * Mandatory for Saving, Include tables related to this module.
	 */
	var $tab_name = Array('vtiger_crmentity', 'vtiger_project', 'vtiger_projectcf');

	/**
	 * Mandatory for Saving, Include tablename and tablekey columnname here.
	 */
	var $tab_name_index = Array(
		'vtiger_crmentity' => 'crmid',
		'vtiger_project'   => 'projectid',
		'vtiger_projectcf' => 'projectid');

	/**
	 * Mandatory for Listing (Related listview)
	 */
	var $list_fields = Array (
		/* Format: Field Label => Array(tablename => columnname) */
		// tablename should not have prefix 'vtiger_'
		'Project Name'=> Array('project' => 'projectname'),
		'Related to'=> Array('project' => 'linktoaccountscontacts'),
		'Start Date'=> Array('project' => 'startdate'),
		'Status'=>Array('project' => 'projectstatus'),
		'Type'=>Array('project' => 'projecttype'),
		'Assigned To' => Array('crmentity' => 'smownerid')
	);
	var $list_fields_name = Array(
		/* Format: Field Label => fieldname */
		'Project Name'=> 'projectname',
		'Related to'=> 'linktoaccountscontacts',
		'Start Date'=> 'startdate',
		'Status'=>'projectstatus',
		'Type'=>'projecttype',
		'Assigned To' => 'assigned_user_id'
	);

	// Make the field link to detail view from list view (Fieldname)
	var $list_link_field = 'projectname';

	// For Popup listview and UI type support
	var $search_fields = Array(
		/* Format: Field Label => Array(tablename => columnname) */
		// tablename should not have prefix 'vtiger_'
		'Project Name'=> Array('project' => 'projectname'),
		'Related to'=> Array('project' => 'linktoaccountscontacts'),
		'Start Date'=> Array('project' => 'startdate'),
		'Status'=>Array('project' => 'projectstatus'),
		'Type'=>Array('project' => 'projecttype'),
	);
	var $search_fields_name = Array(
		/* Format: Field Label => fieldname */
		'Project Name'=> 'projectname',
		'Related to'=> 'linktoaccountscontacts',
		'Start Date'=> 'startdate',
		'Status'=>'projectstatus',
		'Type'=>'projecttype',
	);

	// For Popup window record selection
	var $popup_fields = Array('projectname');

	// Placeholder for sort fields - All the fields will be initialized for Sorting through initSortFields
	var $sortby_fields = Array();

	// For Alphabetical search
	var $def_basicsearch_col = 'projectname';

	// Column value to use on detail view record text display
	var $def_detailview_recname = 'projectname';

	// Required Information for enabling Import feature
	var $required_fields = Array('projectname'=>1);

	// Callback function list during Importing
	var $special_functions = Array('set_import_assigned_user');

	var $default_order_by = 'projectname';
	var $default_sort_order='ASC';
	// Used when enabling/disabling the mandatory fields for the module.
	// Refers to vtiger_field.fieldname values.
	var $mandatory_fields = Array('createdtime', 'modifiedtime', 'projectname');

	function save_module($module) {
		if ($this->HasDirectImageField) {
			$this->insertIntoAttachment($this->id,$module);
		}
	}

	/**
	 * Apply security restriction (sharing privilege) query part for List view.
	 */
	function getListViewSecurityParameter($module) {
		global $current_user;
		require('user_privileges/user_privileges_'.$current_user->id.'.php');
		require('user_privileges/sharing_privileges_'.$current_user->id.'.php');

		$sec_query = '';
		$tabid = getTabid($module);

		if($is_admin==false && $profileGlobalPermission[1] == 1 && $profileGlobalPermission[2] == 1
			&& $defaultOrgSharingPermission[$tabid] == 3) {

				$sec_query .= " AND (vtiger_crmentity.smownerid in($current_user->id) OR vtiger_crmentity.smownerid IN
					(
						SELECT vtiger_user2role.userid FROM vtiger_user2role
						INNER JOIN vtiger_users ON vtiger_users.id=vtiger_user2role.userid
						INNER JOIN vtiger_role ON vtiger_role.roleid=vtiger_user2role.roleid
						WHERE vtiger_role.parentrole LIKE '".$current_user_parent_role_seq."::%'
					)
					OR vtiger_crmentity.smownerid IN
					(
						SELECT shareduserid FROM vtiger_tmp_read_user_sharing_per
						WHERE userid=".$current_user->id." AND tabid=".$tabid."
					)
					OR (";

					// Build the query based on the group association of current user.
					if(sizeof($current_user_groups) > 0) {
						$sec_query .= " vtiger_groups.groupid IN (". implode(",", $current_user_groups) .") OR ";
					}
					$sec_query .= " vtiger_groups.groupid IN
						(
							SELECT vtiger_tmp_read_group_sharing_per.sharedgroupid
							FROM vtiger_tmp_read_group_sharing_per
							WHERE userid=".$current_user->id." and tabid=".$tabid."
						)";
				$sec_query .= ")
				)";
		}
		return $sec_query;
	}

	/**
	 * Invoked when special actions are performed on the module.
	 * @param String Module name
	 * @param String Event Type (module.postinstall, module.disabled, module.enabled, module.preuninstall)
	 */
	function vtlib_handler($modulename, $event_type) {
		if($event_type == 'module.postinstall') {
			global $adb;
			$this->setModuleSeqNumber('configure', $modulename, 'prj-', '0000001');
			include_once('vtlib/Vtiger/Module.php');
			$moduleInstance = Vtiger_Module::getInstance($modulename);
			$projectsResult = $adb->pquery('SELECT tabid FROM vtiger_tab WHERE name=?', array('Project'));
			$projectTabid = $adb->query_result($projectsResult, 0, 'tabid');

			// Mark the module as Standard module
			$adb->pquery('UPDATE vtiger_tab SET customized=0 WHERE name=?', array($modulename));

			// Add module to Customer portal
			if(getTabid('CustomerPortal') && $projectTabid) {
				$checkAlreadyExists = $adb->pquery('SELECT 1 FROM vtiger_customerportal_tabs WHERE tabid=?', array($projectTabid));
				if($checkAlreadyExists && $adb->num_rows($checkAlreadyExists) < 1) {
					$maxSequenceQuery = $adb->query("SELECT max(sequence) as maxsequence FROM vtiger_customerportal_tabs");
					$maxSequence = $adb->query_result($maxSequenceQuery, 0, 'maxsequence');
					$nextSequence = $maxSequence+1;
					$adb->query("INSERT INTO vtiger_customerportal_tabs(tabid,visible,sequence) VALUES ($projectTabid,1,$nextSequence)");
					$adb->query("INSERT INTO vtiger_customerportal_prefs(tabid,prefkey,prefvalue) VALUES ($projectTabid,'showrelatedinfo',1)");
				}
			}

			// Add Gnatt chart to the related list of the module
			$relation_id = $adb->getUniqueID('vtiger_relatedlists');
			$max_sequence = 0;
			$result = $adb->query("SELECT max(sequence) as maxsequence FROM vtiger_relatedlists WHERE tabid=$projectTabid");
			if($adb->num_rows($result)) $max_sequence = $adb->query_result($result, 0, 'maxsequence');
			$sequence = $max_sequence+1;
			$adb->pquery("INSERT INTO vtiger_relatedlists(relation_id,tabid,related_tabid,name,sequence,label,presence) VALUES(?,?,?,?,?,?,?)",
						array($relation_id,$projectTabid,0,'get_gantt_chart',$sequence,'Charts',0));

			// Add Project module to the related list of Accounts module
			$accountsModuleInstance = Vtiger_Module::getInstance('Accounts');
			$accountsModuleInstance->setRelatedList($moduleInstance, 'Projects', Array('ADD'), 'get_dependents_list');

			// Add Project module to the related list of Accounts module
			$contactsModuleInstance = Vtiger_Module::getInstance('Contacts');
			$contactsModuleInstance->setRelatedList($moduleInstance, 'Projects', Array('ADD'), 'get_dependents_list');

			// Add Project module to the related list of HelpDesk module
			$helpDeskModuleInstance = Vtiger_Module::getInstance('HelpDesk');
			$helpDeskModuleInstance->setRelatedList($moduleInstance, 'Projects', Array('SELECT'), 'get_related_list');

			$modcommentsModuleInstance = Vtiger_Module::getInstance('ModComments');
			if($modcommentsModuleInstance && file_exists('modules/ModComments/ModComments.php')) {
				include_once 'modules/ModComments/ModComments.php';
				if(class_exists('ModComments')) ModComments::addWidgetTo(array('Project'));
			}

		} else if($event_type == 'module.disabled') {
			// TODO Handle actions when this module is disabled.
		} else if($event_type == 'module.enabled') {
			// TODO Handle actions when this module is enabled.
		} else if($event_type == 'module.preuninstall') {
			// TODO Handle actions when this module is about to be deleted.
		} else if($event_type == 'module.preupdate') {
			// TODO Handle actions before this module is updated.
		} else if($event_type == 'module.postupdate') {
			global $adb;

			$projectsResult = $adb->pquery('SELECT tabid FROM vtiger_tab WHERE name=?', array('Project'));
			$projectTabid = $adb->query_result($projectsResult, 0, 'tabid');

			// Add Gnatt chart to the related list of the module
			$relation_id = $adb->getUniqueID('vtiger_relatedlists');
			$max_sequence = 0;
			$result = $adb->query("SELECT max(sequence) as maxsequence FROM vtiger_relatedlists WHERE tabid=$projectTabid");
			if($adb->num_rows($result)) $max_sequence = $adb->query_result($result, 0, 'maxsequence');
			$sequence = $max_sequence+1;
			$adb->pquery("INSERT INTO vtiger_relatedlists(relation_id,tabid,related_tabid,name,sequence,label,presence) VALUES(?,?,?,?,?,?,?)",
						array($relation_id,$projectTabid,0,'get_gantt_chart',$sequence,'Charts',0));

			// Add Comments widget to Project module
			$modcommentsModuleInstance = Vtiger_Module::getInstance('ModComments');
			if($modcommentsModuleInstance && file_exists('modules/ModComments/ModComments.php')) {
				include_once 'modules/ModComments/ModComments.php';
				if(class_exists('ModComments')) ModComments::addWidgetTo(array('Project'));
			}
		}
	}

	/**
	 * Handle saving related module information.
	 * NOTE: This function has been added to CRMEntity (base class).
	 * You can override the behavior by re-defining it here.
	 */
	// function save_related_module($module, $crmid, $with_module, $with_crmid) { }

	/**
	 * Handle deleting related module information.
	 * NOTE: This function has been added to CRMEntity (base class).
	 * You can override the behavior by re-defining it here.
	 */
	function delete_related_module($module, $crmid, $with_module, $with_crmid) {
		if (!in_array($with_module, array('ProjectMilestone', 'ProjectTask'))) {
			parent::delete_related_module($module, $crmid, $with_module, $with_crmid);
			return;
		}
		$destinationModule = vtlib_purify($_REQUEST['destination_module']);
		if (!is_array($with_crmid)) $with_crmid = Array($with_crmid);
		foreach($with_crmid as $relcrmid) {
			$child = CRMEntity::getInstance($destinationModule);
			$child->retrieve_entity_info($relcrmid, $destinationModule);
			$child->mode='edit';
			$child->column_fields['projectid']='';
			$child->save($destinationModule,$relcrmid);
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

	function get_gantt_chart($id, $cur_tab_id, $rel_tab_id, $actions=false){
		require_once("BURAK_Gantt.class.php");

		$headers = array();
		$headers[0] = getTranslatedString('LBL_PROGRESS_CHART');

		$entries = array();

		global $adb,$tmp_dir,$default_charset;
		$record = $id;
		$g = new BURAK_Gantt();
		// set grid type
		$g->setGrid(1);
		// set Gantt colors
		$g->setColor("group","000000");
		$g->setColor("progress","660000");

		$related_projecttasks = $adb->pquery("SELECT pt.* FROM vtiger_projecttask AS pt
			INNER JOIN vtiger_crmentity AS crment ON pt.projecttaskid=crment.crmid
			WHERE projectid=? AND crment.deleted=0 AND pt.startdate IS NOT NULL AND pt.enddate IS NOT NULL",
			array($record));

		while($rec_related_projecttasks = $adb->fetchByAssoc($related_projecttasks)){

			if($rec_related_projecttasks['projecttaskprogress']=="--none--"){
				$percentage = 0;
			} else {
				$percentage = str_replace("%","",$rec_related_projecttasks['projecttaskprogress']);
			}

			$rec_related_projecttasks['projecttaskname'] = iconv($default_charset, "ISO-8859-2//TRANSLIT",$rec_related_projecttasks['projecttaskname']);
			$g->addTask($rec_related_projecttasks['projecttaskid'],$rec_related_projecttasks['startdate'],$rec_related_projecttasks['enddate'],$percentage,$rec_related_projecttasks['projecttaskname']);
		}

		$related_projectmilestones = $adb->pquery("SELECT pm.* FROM vtiger_projectmilestone AS pm
			INNER JOIN vtiger_crmentity AS crment on pm.projectmilestoneid=crment.crmid
			WHERE projectid=? and crment.deleted=0",
			array($record));

		while($rec_related_projectmilestones = $adb->fetchByAssoc($related_projectmilestones)){
			$rec_related_projectmilestones['projectmilestonename'] = iconv($default_charset, "ISO-8859-2//TRANSLIT",$rec_related_projectmilestones['projectmilestonename']);
			$g->addMilestone($rec_related_projectmilestones['projectmilestoneid'],$rec_related_projectmilestones['projectmilestonedate'],$rec_related_projectmilestones['projectmilestonename']);
		}

		$g->outputGantt($tmp_dir."diagram_".$record.".jpg","100");

		$origin = $tmp_dir."diagram_".$record.".jpg";
		$destination = $tmp_dir."pic_diagram_".$record.".jpg";

		$imagesize = getimagesize($origin);
		$actualWidth = $imagesize[0];
		$actualHeight = $imagesize[1];

		$size = 1000;
		if($actualWidth > $size){
			$width = $size;
			$height = ($actualHeight * $size) / $actualWidth;
			copy($origin,$destination);
			$id_origin = imagecreatefromjpeg($destination);
			$id_destination = imagecreate($width, $height);
			imagecopyresized($id_destination, $id_origin, 0, 0, 0, 0, $width, $height, $actualWidth, $actualHeight);
			imagejpeg($id_destination,$destination);
			imagedestroy($id_origin);
			imagedestroy($id_destination);
			$image = $destination;
		} else {
			$image = $origin;
		}

		$fullGanttChartImageUrl = $tmp_dir."diagram_".$record.".jpg";
		$thumbGanttChartImageUrl = $image;
		$entries[0] = array("<a href='$fullGanttChartImageUrl' border='0' target='_blank'><img src='$thumbGanttChartImageUrl' border='0'></a>");

		return array('header'=> $headers, 'entries'=> $entries);
	}
}
?>
