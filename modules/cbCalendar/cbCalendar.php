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

class cbCalendar extends CRMEntity {
	public $db;
	public $log;

	public $table_name = 'vtiger_activity';
	public $table_index= 'activityid';
	public $reminder_table = 'vtiger_activity_reminder';
	public $column_fields = array();

	/** Indicator if this is a custom module or standard module */
	public $IsCustomModule = true;
	public $HasDirectImageField = false;
	/**
	 * Mandatory table for supporting custom fields.
	 */
	public $customFieldTable = array('vtiger_activitycf', 'activityid');
	// related_tables variable should define the association (relation) between dependent tables
	// FORMAT: related_tablename => array(related_tablename_column[, base_tablename, base_tablename_column[, related_module]] )
	// Here base_tablename_column should establish relation with related_tablename_column
	// NOTE: If base_tablename and base_tablename_column are not specified, it will default to modules (table_name, related_tablename_column)
	// Uncomment the line below to support custom field columns on related lists
	// var $related_tables = array('vtiger_activitycf'=>array('activityid','vtiger_activity', 'activityid'));

	/**
	 * Mandatory for Saving, Include tables related to this module.
	 */
	public $tab_name = array('vtiger_crmentity', 'vtiger_activity', 'vtiger_activitycf');

	/**
	 * Mandatory for Saving, Include tablename and tablekey columnname here.
	 */
	public $tab_name_index = array(
		'vtiger_crmentity' => 'crmid',
		'vtiger_activity'   => 'activityid',
		'vtiger_activity_reminder'=>'activity_id',
		'vtiger_recurringevents'=>'activityid',
		'vtiger_activitycf' => 'activityid',
		'vtiger_seactivityrel'=>'activityid'
	);

	/**
	 * Mandatory for Listing (Related listview)
	 */
	public $list_fields = array (
		/* Format: Field Label => array(tablename => columnname) */
		// tablename should not have prefix 'vtiger_'
		'Subject'=>array('activity'=>'subject'),
		'Type'=>array('activity'=>'activitytype'),
		'Status'=>array('activity'=>'eventstatus'),
		'Start Date Time'=>array('activity','dtstart'),
		'End Date Time'=>array('activity'=>'dtend'),
		'Related to'=>array('seactivityrel'=>'rel_id'),
		'Contact Name'=>array('activity'=>'cto_id'),
		'Assigned To'=>array('crmentity'=>'smownerid'),
	);
	public $list_fields_name = array(
		/* Format: Field Label => fieldname */
		'Subject'=>'subject',
		'Type'=>'activitytype',
		'Status'=>'eventstatus',
		'Start Date Time'=>'dtstart',
		'End Date Time'=>'dtend',
		'Related to'=>'rel_id',
		'Contact Name'=>'cto_id',
		'Assigned To'=>'assigned_user_id',
	);

	// Make the field link to detail view from list view (Fieldname)
	public $list_link_field = 'subject';

	// For Popup listview and UI type support
	public $search_fields = array(
		/* Format: Field Label => array(tablename => columnname) */
		// tablename should not have prefix 'vtiger_'
		'Subject'=>array('activity'=>'subject'),
		'Type'=>array('activity'=>'activitytype'),
		'Status'=>array('activity'=>'eventstatus'),
		'Start Date Time'=>array('activity'=>'dtstart'),
		'End Date Time'=>array('activity'=>'dtend'),
		'Related to'=>array('seactivityrel'=>'parent_id'),
		'Contact Name'=>array('contactdetails'=>'lastname'),
		'Assigned To'=>array('crmentity'=>'smownerid'),
	);
	public $search_fields_name = array(
		/* Format: Field Label => fieldname */
		'Subject'=>'subject',
		'Type'=>'activitytype',
		'Status'=>'eventstatus',
		'Start Date Time'=>'dtstart',
		'End Date Time'=>'dtend',
		'Related to'=>'parent_id',
		'Contact Name'=>'lastname',
		'Assigned To'=>'assigned_user_id',
	);

	// For Popup window record selection
	public $popup_fields = array('subject');

	// Placeholder for sort fields - All the fields will be initialized for Sorting through initSortFields
	public $sortby_fields = array();

	// For Alphabetical search
	public $def_basicsearch_col = 'subject';

	// Column value to use on detail view record text display
	public $def_detailview_recname = 'subject';

	// Required Information for enabling Import feature
	public $required_fields = array('subject'=>1);

	// Callback function list during Importing
	public $special_functions = array('set_import_assigned_user');

	public $default_order_by = 'subject';
	public $default_sort_order='ASC';
	// Used when enabling/disabling the mandatory fields for the module.
	// Refers to vtiger_field.fieldname values.
	public $mandatory_fields = array('createdtime', 'modifiedtime', 'subject');

	public function save_module($module) {
		global $adb;
		if ($this->HasDirectImageField) {
			$this->insertIntoAttachment($this->id, $module);
		}
		list($ds,$ts) = explode(' ', $this->column_fields['dtstart']);
		list($de,$te) = explode(' ', $this->column_fields['dtend']);
		$this->column_fields['date_start'] = $_REQUEST['date_start'] = $ds;
		$this->column_fields['time_start'] = $_REQUEST['time_start'] = $ts;
		$this->column_fields['due_date'] = $_REQUEST['due_date'] = $de;
		$this->column_fields['time_end'] = $_REQUEST['time_end'] = $te;
		$duration = strtotime($this->column_fields['dtend'])-strtotime($this->column_fields['dtstart']);
		$this->column_fields['duration_hours'] = round($duration/3600, 0);
		$this->column_fields['duration_minutes'] = round($duration % 3600 / 60, 0);
		$adb->pquery(
			'update vtiger_activity set date_start=?, time_start=?, due_date=?, time_end=?, duration_hours=?, duration_minutes=? where activityid=?',
			array($ds,$ts,$de,$te,$this->column_fields['duration_hours'],$this->column_fields['duration_minutes'],$this->id)
		);
		// code added to send mail to the invitees
		if (!empty($_REQUEST['inviteesid'])) {
			$mail_contents = $this->getRequestData($this->id);
			$this->sendInvitation(vtlib_purify($_REQUEST['inviteesid']), vtlib_purify($_REQUEST['subject']), $mail_contents);
		}
		//Insert into vtiger_activity_remainder table
		$this->insertIntoReminderTable(0);

		//Handling for invitees
		$selected_users_string = isset($_REQUEST['inviteesid']) ? $_REQUEST['inviteesid'] : '';
		$invitees_array = explode(';', $selected_users_string);
		$this->insertIntoInviteeTable('Calendar', $invitees_array);

		//Inserting into sales man activity rel
		$this->insertIntoSmActivityRel();
		$this->insertIntoActivityReminderPopup('Calendar');
		if (isset($_REQUEST['recurringcheck']) && $_REQUEST['recurringcheck']) {
			unset($_REQUEST['recurringcheck']);
			$this->column_fields['parent_id'] = $this->column_fields['rel_id'];
			$this->column_fields['contact_id'] = $this->column_fields['cto_id'];
			include_once 'modules/Calendar/RepeatEvents.php';
			Calendar_RepeatEvents::repeatFromRequest($this);
			//Insert into vtiger_recurring event table
			if (isset($this->column_fields['recurringtype']) && $this->column_fields['recurringtype']!='' && $this->column_fields['recurringtype']!='--None--') {
				$recur_data = getrecurringObjValue();
				if (is_object($recur_data)) {
					$this->insertIntoRecurringTable($recur_data);
				}
			}
		}
		//Insert into seactivity rel
		if (isset($this->column_fields['rel_id']) && $this->column_fields['rel_id'] != '' && $this->column_fields['rel_id'] != '0') {
			$res_rel = $adb->pquery('SELECT activityid FROM vtiger_seactivityrel WHERE activityid = ? limit 1', array($this->id));
			if ($adb->num_rows($res_rel) > 0) {
				$adb->pquery('UPDATE vtiger_seactivityrel SET crmid = ? WHERE activityid = ?', array($this->column_fields['rel_id'],$this->id));
			} else {
				$adb->pquery('insert into vtiger_seactivityrel(crmid,activityid) values(?,?)', array($this->column_fields['rel_id'],$this->id));
			}
		} elseif (($this->column_fields['rel_id']=='' || $this->column_fields['rel_id']=='0') && $this->mode=="edit") {
			$this->deleteRelation("vtiger_seactivityrel");
		}
		//Insert into cntactivity rel
		if (empty($this->column_fields['contact_id']) && !empty($this->column_fields['cto_id'])) {
			$this->column_fields['contact_id'] = $this->column_fields['cto_id'];
		}
		if (!empty($this->column_fields['contact_id'])) {
			$listofctos = explode(';', $this->column_fields['contact_id']);
			foreach ($listofctos as $cto) {
				if (!empty($cto)) {
					if (strpos($cto, 'x')) {
						list($wsid,$cto) = explode('x', $cto);
					}
					$chkrs = $adb->pquery('select count(*) from vtiger_cntactivityrel where contactid = ? and activityid = ?', array($cto,$this->id));
					if ($chkrs && $adb->query_result($chkrs, 0, 0) == 0) {
						$adb->pquery('insert into vtiger_cntactivityrel(contactid,activityid) values(?,?)', array($cto,$this->id));
					}
				}
				if (empty($this->column_fields['cto_id'])) {
					$this->column_fields['cto_id'] = $cto;
					$adb->pquery('update vtiger_activity set cto_id = ? where activityid = ?', array($cto,$this->id));
				}
			}
		}
		unset($_REQUEST['timefmt_dtstart'], $_REQUEST['timefmt_dtend'], $_REQUEST['timefmt_followupdt']);
	}

	public function trash($module, $id) {
		parent::trash($module, $id);
		$this->activity_reminder($id, '0', 0, 0, 'delete');
	}

	/** Function to insert values in vtiger_recurringevents table for the specified tablename,module
	  * @param $recurObj -- Recurring Object:: Type varchar
	 */
	public function insertIntoRecurringTable(&$recurObj) {
		global $log,$adb;
		$log->info("in insertIntoRecurringTable  ");
		$st_date = $recurObj->startdate->get_DB_formatted_date();
		$log->debug("st_date ".$st_date);
		$end_date = $recurObj->enddate->get_DB_formatted_date();
		$log->debug("end_date is set ".$end_date);
		$type = $recurObj->getRecurringType();
		$log->debug("type is ".$type);
		$flag="true";

		if ($_REQUEST['mode'] == 'edit') {
			$activity_id=$this->id;

			$sql='select min(recurringdate) AS min_date,max(recurringdate) AS max_date, recurringtype, activityid
				from vtiger_recurringevents
				where activityid=? group by activityid, recurringtype';
			$result = $adb->pquery($sql, array($activity_id));
			$noofrows = $adb->num_rows($result);
			for ($i=0; $i<$noofrows; $i++) {
				$recur_type_b4_edit = $adb->query_result($result, $i, "recurringtype");
				$date_start_b4edit = $adb->query_result($result, $i, "min_date");
				$end_date_b4edit = $adb->query_result($result, $i, "max_date");
			}
			if (($st_date == $date_start_b4edit) && ($end_date==$end_date_b4edit) && ($type == $recur_type_b4_edit)) {
				if ($_REQUEST['set_reminder'] == 'Yes') {
					$sql = 'delete from vtiger_activity_reminder where activity_id=?';
					$adb->pquery($sql, array($activity_id));
					$sql = 'delete from vtiger_recurringevents where activityid=?';
					$adb->pquery($sql, array($activity_id));
					$flag="true";
				} elseif ($_REQUEST['set_reminder'] == 'No') {
					$sql = 'delete from vtiger_activity_reminder where activity_id=?';
					$adb->pquery($sql, array($activity_id));
					$flag="false";
				} else {
					$flag="false";
				}
			} else {
				$sql = 'delete from vtiger_activity_reminder where activity_id=?';
				$adb->pquery($sql, array($activity_id));
				$sql = 'delete from vtiger_recurringevents where activityid=?';
				$adb->pquery($sql, array($activity_id));
			}
		}

		$recur_freq = $recurObj->getRecurringFrequency();
		$recurringinfo = $recurObj->getDBRecurringInfoString();

		if ($flag=="true") {
			$max_recurid_qry = 'select max(recurringid) AS recurid from vtiger_recurringevents;';
			$result = $adb->pquery($max_recurid_qry, array());
			$noofrows = $adb->num_rows($result);
			$recur_id = 0;
			if ($noofrows > 0) {
				$recur_id = $adb->query_result($result, 0, "recurid");
			}
			$current_id =$recur_id+1;
			$recurring_insert = "insert into vtiger_recurringevents values (?,?,?,?,?,?)";
			$rec_params = array($current_id, $this->id, $st_date, $type, $recur_freq, $recurringinfo);
			$adb->pquery($recurring_insert, $rec_params);
			coreBOS_Session::delete('next_reminder_time');
			if ($_REQUEST['set_reminder'] == 'Yes') {
				$this->insertIntoReminderTable($current_id);
			}
		}
	}

	/** Function to insert values in activity_reminder_popup table for the specified module
	  * @param $cbmodule -- module:: Type varchar
	 */
	public function insertIntoActivityReminderPopup($cbmodule) {
		global $adb;

		$cbrecord = $this->id;
		coreBOS_Session::delete('next_reminder_time');
		if (isset($cbmodule) && isset($cbrecord)) {
			list($cbdate,$cbtime) = explode(' ', $this->column_fields['dtstart']);

			$reminder_query = "SELECT reminderid FROM vtiger_activity_reminder_popup WHERE recordid = ?";
			$reminder_params = array($cbrecord);
			$reminderidres = $adb->pquery($reminder_query, $reminder_params);

			$reminderid = null;
			if ($adb->num_rows($reminderidres) > 0) {
				$reminderid = $adb->query_result($reminderidres, 0, "reminderid");
			}

			if (isset($reminderid)) {
				$callback_query = "UPDATE vtiger_activity_reminder_popup set status = 0, date_start = ?, time_start = ? WHERE reminderid = ?";
				$callback_params = array($cbdate, $cbtime, $reminderid);
			} else {
				$callback_query = "INSERT INTO vtiger_activity_reminder_popup (semodule, recordid, date_start, time_start, status) VALUES (?,?,?,?,0)";
				$callback_params = array($cbmodule, $cbrecord, $cbdate, $cbtime);
			}

			$adb->pquery($callback_query, $callback_params);
		}
	}

	/** Function to insert values in vtiger_activity_remainder table for the specified module,
	  * @param $table_name -- table name:: Type varchar
	  * @param $module -- module:: Type varchar
	 */
	public function insertIntoReminderTable($recurid) {
		global $log;
		if (isset($_REQUEST['set_reminder']) && $_REQUEST['set_reminder'] == 'Yes') {
			coreBOS_Session::delete('next_reminder_time');
			$log->debug("set reminder is set");
			$rem_days = $_REQUEST['remdays'];
			$log->debug("rem_days is ".$rem_days);
			$rem_hrs = $_REQUEST['remhrs'];
			$log->debug("rem_hrs is ".$rem_hrs);
			$rem_min = $_REQUEST['remmin'];
			$log->debug("rem_minutes is ".$rem_min);
			$reminder_time = $rem_days * 24 * 60 + $rem_hrs * 60 + $rem_min;
			$log->debug("reminder_time is ".$reminder_time);
			if ($recurid == 0) {
				if ($_REQUEST['mode'] == 'edit') {
					$this->activity_reminder($this->id, $reminder_time, 0, $recurid, 'edit');
				} else {
					$this->activity_reminder($this->id, $reminder_time, 0, $recurid, '');
				}
			} else {
				$this->activity_reminder($this->id, $reminder_time, 0, $recurid, '');
			}
		} elseif (isset($_REQUEST['set_reminder']) && $_REQUEST['set_reminder'] == 'No') {
			$this->activity_reminder($this->id, '0', 0, $recurid, 'delete');
		}
	}

	/**
	 * Function to get reminder for activity
	 * @param  integer   $activity_id     - activity id
	 * @param  string    $reminder_time   - reminder time
	 * @param  integer   $reminder_sent   - 0 or 1
	 * @param  integer   $recurid         - recuring eventid
	 * @param  string    $remindermode    - string like 'edit'
	 */
	public function activity_reminder($activity_id, $reminder_time, $reminder_sent = 0, $recurid = 0, $remindermode = '') {
		global $log;
		$log->debug("Entering activity_reminder(".$activity_id.",".$reminder_time.",".$reminder_sent.",".$recurid.",".$remindermode.") method ...");
		// Check for activityid already present in the reminder_table
		$query_exist = "SELECT activity_id FROM ".$this->reminder_table." WHERE activity_id = ?";
		$result_exist = $this->db->pquery($query_exist, array($activity_id));

		if ($remindermode == 'edit') {
			if ($this->db->num_rows($result_exist) > 0) {
				$query = 'UPDATE '.$this->reminder_table.' SET reminder_sent = ?, reminder_time = ? WHERE activity_id =?';
				$params = array($reminder_sent, $reminder_time, $activity_id);
			} else {
				$query = "INSERT INTO ".$this->reminder_table." VALUES (?,?,?,?)";
				$params = array($activity_id, $reminder_time, 0, $recurid);
			}
			$this->db->pquery($query, $params, true, "Error in processing table $this->reminder_table");
		} elseif (($remindermode == 'delete') && ($this->db->num_rows($result_exist) > 0)) {
			$query = "DELETE FROM ".$this->reminder_table." WHERE activity_id = ?";
			$params = array($activity_id);
			$this->db->pquery($query, $params, true, "Error in processing table $this->reminder_table");
		} elseif ($this->db->num_rows($result_exist) == 0) {
			$query = "INSERT INTO ".$this->reminder_table." VALUES (?,?,?,?)";
			$params = array($activity_id, $reminder_time, 0, $recurid);
			$this->db->pquery($query, $params, true, "Error in processing table $this->reminder_table");
		}
		$log->debug("Exiting vtiger_activity_reminder method ...");
	}

	/** Function to insert values in vtiger_invitees table for the specified module,tablename ,invitees_array
	  * @param $table_name -- table name:: Type varchar
	  * @param $module -- module:: Type varchar
	  * @param $invitees_array Array
	 */
	public function insertIntoInviteeTable($module, $invitees_array) {
		global $log,$adb;
		$log->debug("Entering insertIntoInviteeTable($module,".print_r($invitees_array, true).") method ...");
		if ($this->mode == 'edit') {
			$adb->pquery('delete from vtiger_invitees where activityid=?', array($this->id));
		}
		foreach ($invitees_array as $inviteeid) {
			if ($inviteeid != '') {
				$adb->pquery('insert into vtiger_invitees values(?,?)', array($this->id, $inviteeid));
			}
		}
		$log->debug('Exiting insertIntoInviteeTable method ...');
	}

	/** Function to insert values in vtiger_salesmanactivityrel table for the specified module
	  * @param $module -- module:: Type varchar
	*/
	public function insertIntoSmActivityRel() {
		global $adb;
		if ($this->mode == 'edit') {
			$adb->pquery('delete from vtiger_salesmanactivityrel where activityid=?', array($this->id));
		}

		$user_sql = $adb->pquery("select count(*) as count from vtiger_users where id=?", array($this->column_fields['assigned_user_id']));
		if ($adb->query_result($user_sql, 0, 'count') != 0) {
			$sql_qry = "insert into vtiger_salesmanactivityrel (smid,activityid) values(?,?)";
			$adb->pquery($sql_qry, array($this->column_fields['assigned_user_id'], $this->id));

			if (isset($_REQUEST['inviteesid']) && $_REQUEST['inviteesid']!='') {
				$selected_users_string = $_REQUEST['inviteesid'];
				$invitees_array = explode(';', $selected_users_string);
				foreach ($invitees_array as $inviteeid) {
					if ($inviteeid != '') {
						$resultcheck = $adb->pquery('select 1 from vtiger_salesmanactivityrel where activityid=? and smid=?', array($this->id, $inviteeid));
						if ($adb->num_rows($resultcheck) != 1) {
							$adb->pquery('insert into vtiger_salesmanactivityrel values(?,?)', array($inviteeid, $this->id));
						}
					}
				}
			}
		}
	}

	private function getRequestData($return_id) {
		global $adb;
		$cont_qry = 'select contactid from vtiger_cntactivityrel where activityid=?
			UNION
			select cto_id from vtiger_activity where activityid=?';
		$cont_res = $adb->pquery($cont_qry, array($return_id, $return_id));
		$noofrows = $adb->num_rows($cont_res);
		$cont_id = array();
		if ($noofrows > 0) {
			for ($i=0; $i<$noofrows; $i++) {
				$cont_id[] = $adb->query_result($cont_res, $i, "contactid");
			}
		}
		$cont_name = '';
		foreach ($cont_id as $id) {
			if ($id != '') {
				$displayValueArray = getEntityName('Contacts', $id);
				if (!empty($displayValueArray)) {
					foreach ($displayValueArray as $field_value) {
						$contact_name = $field_value;
					}
				}
				$cont_name .= $contact_name .', ';
			}
		}
		$cont_name  = trim($cont_name, ', ');
		$mail_data = array();
		$mail_data['user_id'] = $this->column_fields['assigned_user_id'];
		$mail_data['subject'] = $this->column_fields['subject'];
		$mail_data['status'] = $this->column_fields['eventstatus'];
		$mail_data['activity_mode'] = 'Events';
		$mail_data['taskpriority'] = $this->column_fields['taskpriority'];
		if (empty($this->column_fields['parent_name'])) {
			if (empty($this->column_fields['rel_id'])) {
				$this->column_fields['parent_name'] = '';
			} else {
				$relinfo = getEntityName(getSalesEntityType($this->column_fields['rel_id']), $this->column_fields['rel_id']);
				$this->column_fields['parent_name'] = $relinfo[$this->column_fields['rel_id']];
			}
		}
		$mail_data['relatedto'] = $this->column_fields['parent_name'];
		$mail_data['contact_name'] = $cont_name;
		$mail_data['description'] = $this->column_fields['description'];
		$mail_data['assign_type'] = vtlib_purify($_REQUEST['assigntype']);
		$mail_data['group_name'] = (!empty($this->column_fields['assigned_group_id']) ? getGroupName($this->column_fields['assigned_group_id']) : '');
		$mail_data['mode'] = $this->mode;
		$startDate = new DateTimeField($this->column_fields['dtstart']);
		$endDate = new DateTimeField($this->column_fields['dtend']);
		$mail_data['st_date_time'] = $startDate->getDBInsertDateTimeValue();
		$mail_data['end_date_time'] = $endDate->getDBInsertDateTimeValue();
		$mail_data['location']=vtlib_purify($this->column_fields['location']);
		return $mail_data;
	}

	public function sendInvitation($inviteesid, $subject, $desc) {
		global $current_user;
		require_once 'modules/Emails/mail.php';
		$invites = getTranslatedString('INVITATION', 'cbCalendar');
		$invitees_array = explode(';', $inviteesid);
		$subject = $invites.' : '.$subject;
		foreach ($invitees_array as $inviteeid) {
			if (!empty($inviteeid)) {
				$description = getActivityDetails($desc, $inviteeid, 'invite');
				$to_email = getUserEmailId('id', $inviteeid);
				send_mail('Calendar', $to_email, $current_user->user_name, '', $subject, $description);
			}
		}
	}

	/**
	 * Apply security restriction (sharing privilege) query part for List view.
	 */
	public function getListViewSecurityParameter($module) {
		global $current_user;
		require 'user_privileges/user_privileges_'.$current_user->id.'.php';
		require 'user_privileges/sharing_privileges_'.$current_user->id.'.php';

		$sec_query = '';
		$tabid = getTabid($module);

		if ($is_admin==false && $profileGlobalPermission[1] == 1 && $profileGlobalPermission[2] == 1 && $defaultOrgSharingPermission[$tabid] == 3) {
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
			if (count($current_user_groups) > 0) {
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
	 * Function to get Activity related Contacts
	 * @param  integer   $id      - activityid
	 * returns related Contacts record in array format
	 */
	public function get_contacts($id, $cur_tab_id, $rel_tab_id, $actions = false) {
		global $log, $singlepane_view, $currentModule, $adb;
		$log->debug("Entering get_contacts(".$id.") method ...");
		$this_module = $currentModule;

		$related_module = vtlib_getModuleNameById($rel_tab_id);
		require_once "modules/$related_module/$related_module.php";
		$other = new $related_module();

		// To make the edit or del link actions to return back to same view.
		if ($singlepane_view == 'true') {
			$returnset = "&return_module=$currentModule&return_action=DetailView&return_id=$id";
		} else {
			$returnset = "&return_module=$currentModule&return_action=CallRelatedList&return_id=$id";
		}

		$button = '';

		if ($actions) {
			if (is_string($actions)) {
				$actions = explode(',', strtoupper($actions));
			}
			$wfs = '';
			if (in_array('SELECT', $actions) && isPermitted($related_module, 4, '') == 'yes') {
				$wfs = new VTWorkflowManager($adb);
				$racbr = $wfs->getRACRuleForRecord($currentModule, $id);
				if (!$racbr || $racbr->hasRelatedListPermissionTo('select', $related_module)) {
					$singular_modname = getTranslatedString('SINGLE_' . $related_module, $related_module);
					$button .= "<input title='" . getTranslatedString('LBL_SELECT') . " " . $singular_modname . "' class='crmbutton small edit' ".
						"type='button' onclick=\"return window.open('index.php?module=$related_module&return_module=$currentModule&action=Popup&popuptype=detailview".
						"&select=enable&form=EditView&form_submit=false&recordid=$id','test','width=640,height=602,resizable=0,scrollbars=0');\" ".
						"value='" . getTranslatedString('LBL_SELECT') . " " . $singular_modname . "'>&nbsp;";
				}
			}
			if (in_array('ADD', $actions) && isPermitted($related_module, 1, '') == 'yes') {
				if ($wfs == '') {
					$wfs = new VTWorkflowManager($adb);
					$racbr = $wfs->getRACRuleForRecord($currentModule, $id);
				}
				if (!$racbr || $racbr->hasRelatedListPermissionTo('create', $related_module)) {
					$button .= "<input type='hidden' name='createmode' id='createmode' value='link' />";
				}
			}
		}

		$query = 'select vtiger_contactdetails.*, vtiger_crmentity.crmid, vtiger_crmentity.smownerid, vtiger_crmentity.modifiedtime';

		$userNameSql = getSqlForNameInDisplayFormat(array('first_name'=>'vtiger_users.first_name','last_name' => 'vtiger_users.last_name'), 'Users');
		$query .= ", CASE WHEN (vtiger_users.user_name NOT LIKE '') THEN $userNameSql ELSE vtiger_groups.groupname END AS user_name";

		$more_relation = '';
		if (!empty($other->related_tables)) {
			foreach ($other->related_tables as $tname => $relmap) {
				$query .= ", $tname.*";
				// Setup the default JOIN conditions if not specified
				if (empty($relmap[1])) {
					$relmap[1] = $other->table_name;
				}
				if (empty($relmap[2])) {
					$relmap[2] = $relmap[0];
				}
						$more_relation .= " LEFT JOIN $tname ON $tname.$relmap[0] = $relmap[1].$relmap[2]";
			}
		}

		$query .= ' FROM vtiger_contactdetails
			inner join vtiger_cntactivityrel on vtiger_cntactivityrel.contactid=vtiger_contactdetails.contactid
			inner join vtiger_crmentity on vtiger_crmentity.crmid = vtiger_contactdetails.contactid
			left join vtiger_users on vtiger_users.id = vtiger_crmentity.smownerid
			left join vtiger_groups on vtiger_groups.groupid = vtiger_crmentity.smownerid';
		$query .= $more_relation;
		$query .= ' where vtiger_cntactivityrel.activityid='.$id.' and vtiger_crmentity.deleted=0';
		$return_value = GetRelatedList($this_module, $related_module, $other, $query, $button, $returnset);
		if ($return_value == null) {
			$return_value = array();
		}
		$return_value['CUSTOM_BUTTON'] = $button;
		$log->debug("Exiting get_contacts method ...");
		return $return_value;
	}

	/**
	 * Invoked when special actions are performed on the module.
	 * @param String Module name
	 * @param String Event Type (module.postinstall, module.disabled, module.enabled, module.preuninstall)
	 */
	public function vtlib_handler($modulename, $event_type) {
		if ($event_type == 'module.postinstall') {
			// TODO Handle post installation actions
			//$this->setModuleSeqNumber('configure', $modulename, 'cbcal-', '0000001');
			global $adb;
			set_time_limit(0);
			$rs = $adb->query('select vtiger_seactivityrel.crmid, activityid
					from vtiger_seactivityrel
					inner join vtiger_crmentity on vtiger_crmentity.crmid = vtiger_seactivityrel.crmid
					where deleted=0 and activityid>0');
			$upd = 'update vtiger_activity set rel_id=? where activityid=?';
			while ($act = $adb->fetch_array($rs)) {
				$adb->pquery($upd, array($act['crmid'],$act['activityid']));
			}
			$rs = $adb->query('select activityid, contactid
					from vtiger_cntactivityrel
					inner join vtiger_crmentity on vtiger_crmentity.crmid = vtiger_cntactivityrel.contactid
					where deleted=0');
			$upd = 'update vtiger_activity set cto_id=? where activityid=?';
			$actid = 0;
			while ($act = $adb->fetch_array($rs)) {
				if ($actid == $act['activityid']) {
					continue;
				}
				$adb->pquery($upd, array($act['contactid'],$act['activityid']));
				$actid = $act['activityid'];
			}
			$adb->query("ALTER TABLE `vtiger_activity` CHANGE `date_start` `date_start` DATE NULL");
			$adb->query("update vtiger_activity set eventstatus=status where activitytype='Task'");
			$adb->query("UPDATE `vtiger_activity` SET 
				`dtstart`= str_to_date(concat(date_format(`date_start`,'%Y/%m/%d'),' ',`time_start`),'%Y/%m/%d %H:%i:%s'),
				`dtend` = str_to_date(concat(date_format(`due_date`,'%Y/%m/%d'),' ',`time_end`),'%Y/%m/%d %H:%i:%s')");
			$bck = $adb->getUniqueID('vtiger_blocks');
			$tabid = getTabid('cbCalendar');
			$rlrs = $adb->pquery('SELECT relation_id FROM vtiger_relatedlists WHERE tabid=? and related_tabid=4', array($tabid));
			$rl = $adb->query_result($rlrs, 0, 0);
			$adb->pquery(
				'INSERT INTO vtiger_blocks
					(blockid,tabid,blocklabel,sequence,show_title,visible,create_view,edit_view,detail_view,display_status,iscustom,isrelatedlist)
					VALUES(?,?,?,?,?,?,?,?,?,?,?,?)',
				array($bck, $tabid, 'Contacts', 2, 0, 0, 0, 0, 0, 1, 1, $rl)
			);
			// Fill Follow up type picklist
			$rs = $adb->query('select activitytype from vtiger_activitytype');
			$module = Vtiger_Module::getInstance($modulename);
			$field = Vtiger_Field::getInstance('followuptype', $module);
			while ($act = $adb->fetch_array($rs)) {
				$field->setPicklistValues(array($act['activitytype']));
			}
			// workflows
			$workflowManager = new VTWorkflowManager($adb);
			$taskManager = new VTTaskManager($adb);
			// Calendar workflow when Send Notification is checked
			$calendarWorkflow = $workflowManager->newWorkFlow("cbCalendar");
			$calendarWorkflow->test = '[{"fieldname":"sendnotification","operation":"is","value":"true:boolean"}]';
			$calendarWorkflow->description = "Workflow for Calendar when Send Notification is True";
			$calendarWorkflow->executionCondition = VTWorkflowManager::$ON_EVERY_SAVE;
			$calendarWorkflow->defaultworkflow = 1;
			$workflowManager->save($calendarWorkflow);
			$task = $taskManager->createTask('VTEmailTask', $calendarWorkflow->id);
			$task->active = true;
			$task->summary = 'Send Task Notification Email to Record Owner';
			$task->recepient = "\$(assigned_user_id : (Users) email1)";
			$task->subject = "Calendar :  \$subject";
			$task->content = '$(assigned_user_id : (Users) last_name) $(assigned_user_id : (Users) first_name) ,<br/>'
					. '<b>Task Notification Details:</b><br/>'
					. 'Subject : $subject<br/>'
					. 'Start date and time : $dtstart ( $(general : (__VtigerMeta__) dbtimezone) ) <br/>'
					. 'End date and time   : $dtend ( $(general : (__VtigerMeta__) dbtimezone) ) <br/>'
					. 'Event type          : $activitytype <br/>'
					. 'Status              : $eventstatus <br/>'
					. 'Priority            : $taskpriority <br/>'
					. 'Related To          : $(parent_id : (Leads) lastname) $(parent_id : (Leads) firstname) $(parent_id : (Accounts) accountname) '
					. '$(parent_id : (Potentials) potentialname) $(parent_id : (HelpDesk) ticket_title) <br/>'
					. 'Contact             : $(contact_id : (Contacts) lastname) $(contact_id : (Contacts) firstname) <br/>'
					. 'Location            : $location <br/>'
					. 'Description         : $description';
			$taskManager->saveTask($task);
			// Calendar workflow create follow up
			$calendarWorkflow = $workflowManager->newWorkFlow("cbCalendar");
			$calendarWorkflow->test='[{"fieldname":"followupcreate","operation":"has changed to","value":"true:boolean","valuetype":"rawtext","joincondition":"and","groupid":"0"}]';
			$calendarWorkflow->description = "Create Calendar Follow Up on change";
			$calendarWorkflow->executionCondition = VTWorkflowManager::$ON_MODIFY;
			$calendarWorkflow->defaultworkflow = 1;
			$workflowManager->save($calendarWorkflow);
			$task = $taskManager->createTask('VTCreateEntityTask', $calendarWorkflow->id);
			$task->active = true;
			$task->summary = 'Create Calendar Follow Up';
			$task->entity_type = "cbCalendar";
			$task->reference_field = "relatedwith";
			$task->field_value_mapping = '[{"fieldname":"subject","modulename":"cbCalendar","valuetype":"expression","value":"concat('."'Follow up: '".',subject )"},'
				.'{"fieldname":"assigned_user_id","modulename":"cbCalendar","valuetype":"fieldname","value":"assigned_user_id "},'
				.'{"fieldname":"dtstart","modulename":"cbCalendar","valuetype":"fieldname","value":"followupdt  "},'
				.'{"fieldname":"dtend","modulename":"cbCalendar","valuetype":"fieldname","value":"followupdt "},'
				.'{"fieldname":"eventstatus","modulename":"cbCalendar","valuetype":"rawtext","value":"Planned"},'
				.'{"fieldname":"taskpriority","modulename":"cbCalendar","valuetype":"rawtext","value":"Medium"},'
				.'{"fieldname":"sendnotification","modulename":"cbCalendar","valuetype":"rawtext","value":"true:boolean"},'
				.'{"fieldname":"activitytype","modulename":"cbCalendar","valuetype":"fieldname","value":"followuptype "},'
				.'{"fieldname":"visibility","modulename":"cbCalendar","valuetype":"rawtext","value":"Private"},'
				.'{"fieldname":"location","modulename":"cbCalendar","valuetype":"fieldname","value":"location "},'
				.'{"fieldname":"reminder_time","modulename":"cbCalendar","valuetype":"rawtext","value":"0"},'
				.'{"fieldname":"recurringtype","modulename":"cbCalendar","valuetype":"rawtext","value":"--None--"},'
				.'{"fieldname":"description","modulename":"cbCalendar","valuetype":"fieldname","value":"description "},'
				.'{"fieldname":"followupcreate","modulename":"cbCalendar","valuetype":"rawtext","value":"false:boolean"},'
				.'{"fieldname":"date_start","modulename":"cbCalendar","valuetype":"fieldname","value":"dtstart "},'
				.'{"fieldname":"time_start","modulename":"cbCalendar","valuetype":"rawtext","value":"00:00"},'
				.'{"fieldname":"due_date","modulename":"cbCalendar","valuetype":"fieldname","value":"dtend "},'
				.'{"fieldname":"time_end","modulename":"cbCalendar","valuetype":"rawtext","value":"00:00"}]';
			$task->test = '';
			$task->reevaluate = 0;
			$taskManager->saveTask($task);
			$calendarWorkflow = $workflowManager->newWorkFlow("cbCalendar");
			$calendarWorkflow->test='[{"fieldname":"followupcreate","operation":"is","value":"true:boolean","valuetype":"rawtext","joincondition":"and","groupid":"0"}]';
			$calendarWorkflow->description = "Create Calendar Follow Up on create";
			$calendarWorkflow->executionCondition = VTWorkflowManager::$ON_FIRST_SAVE;
			$calendarWorkflow->defaultworkflow = 1;
			$workflowManager->save($calendarWorkflow);
			$task = $taskManager->createTask('VTCreateEntityTask', $calendarWorkflow->id);
			$task->active = true;
			$task->summary = 'Create Calendar Follow Up';
			$task->entity_type = "cbCalendar";
			$task->reference_field = "relatedwith";
			$task->field_value_mapping = '[{"fieldname":"subject","modulename":"cbCalendar","valuetype":"expression","value":"concat('."'Follow up: '".',subject )"},'
				.'{"fieldname":"assigned_user_id","modulename":"cbCalendar","valuetype":"fieldname","value":"assigned_user_id "},'
				.'{"fieldname":"dtstart","modulename":"cbCalendar","valuetype":"fieldname","value":"followupdt  "},'
				.'{"fieldname":"dtend","modulename":"cbCalendar","valuetype":"fieldname","value":"followupdt "},'
				.'{"fieldname":"eventstatus","modulename":"cbCalendar","valuetype":"rawtext","value":"Planned"},'
				.'{"fieldname":"taskpriority","modulename":"cbCalendar","valuetype":"rawtext","value":"Medium"},'
				.'{"fieldname":"sendnotification","modulename":"cbCalendar","valuetype":"rawtext","value":"true:boolean"},'
				.'{"fieldname":"activitytype","modulename":"cbCalendar","valuetype":"fieldname","value":"followuptype "},'
				.'{"fieldname":"visibility","modulename":"cbCalendar","valuetype":"rawtext","value":"Private"},'
				.'{"fieldname":"location","modulename":"cbCalendar","valuetype":"fieldname","value":"location "},'
				.'{"fieldname":"reminder_time","modulename":"cbCalendar","valuetype":"rawtext","value":"0"},'
				.'{"fieldname":"recurringtype","modulename":"cbCalendar","valuetype":"rawtext","value":"--None--"},'
				.'{"fieldname":"description","modulename":"cbCalendar","valuetype":"fieldname","value":"description "},'
				.'{"fieldname":"followupcreate","modulename":"cbCalendar","valuetype":"rawtext","value":"false:boolean"},'
				.'{"fieldname":"date_start","modulename":"cbCalendar","valuetype":"fieldname","value":"dtstart "},'
				.'{"fieldname":"time_start","modulename":"cbCalendar","valuetype":"rawtext","value":"00:00"},'
				.'{"fieldname":"due_date","modulename":"cbCalendar","valuetype":"fieldname","value":"dtend "},'
				.'{"fieldname":"time_end","modulename":"cbCalendar","valuetype":"rawtext","value":"00:00"}]';
			$task->test = '';
			$task->reevaluate = 0;
			$taskManager->saveTask($task);

			// custom fields
			$frs = $adb->query("select * from vtiger_field where tablename='vtiger_activitycf'");
			$block = Vtiger_Block::getInstance('LBL_CUSTOM_INFORMATION', $module);
			while ($fldrow = $adb->fetch_array($frs)) {
				$field1 = new Vtiger_Field();
				$field1->name = $fldrow['fieldname'];
				$field1->label= $fldrow['fieldlabel'];
				$field1->column = $fldrow['columnname'];
				$field1->table = $fldrow['tablename'];
				//$field1->columntype = 'VARCHAR(28)';
				$field1->uitype = $fldrow['uitype'];
				$field1->typeofdata = $fldrow['typeofdata'];
				$field1->displaytype = $fldrow['displaytype'];
				$field1->generatedtype = $fldrow['generatedtype'];
				$field1->defaultvalue = $fldrow['defaultvalue'];
				$field1->sequence = $fldrow['sequence'];
				$field1->presence = $fldrow['presence'];
				$field1->readonly = $fldrow['readonly'];
				$field1->quickcreate = $fldrow['quickcreate'];
				$field1->quickcreatesequence = $fldrow['quickcreatesequence'];
				$field1->masseditable = $fldrow['masseditable'];
				$field1->helpinfo = $fldrow['helpinfo'];
				$block->addField($field1);
			}
			require_once 'include/events/include.inc';
			$em = new VTEventsManager($adb);
			$em->registerHandler('corebos.permissions.accessquery', 'modules/cbCalendar/PublicInvitePermission.php', 'PublicInvitePermissionHandler');
			echo "<h4>Permission Event accessquery registered.</h4>";

			//Date Start & Time and Date End & Time are needed on workflows conditions and templates.p
			$evtabid = getTabid('Events');
			$fd_migrate = array('date_start','time_start','due_date','time_end');
			$module = Vtiger_Module::getInstance($modulename);
			foreach ($fd_migrate as $fieldname) {
				$frs = $adb->pquery("select * from vtiger_field where fieldname=? and tabid=?", array($fieldname,$evtabid));
				$block = Vtiger_Block::getInstance('LBL_TASK_INFORMATION', $module);
				while ($fldrow = $adb->fetch_array($frs)) {
					$field1 = new Vtiger_Field();
					$field1->name = $fldrow['fieldname'];
					$field1->label = decode_html($fldrow['fieldlabel']);
					$field1->column = $fldrow['columnname'];
					$field1->table = $fldrow['tablename'];
					$field1->uitype = $fldrow['uitype'];
					$field1->typeofdata = $fldrow['typeofdata'];
					$field1->displaytype = 3;
					$field1->generatedtype = $fldrow['generatedtype'];
					$field1->defaultvalue = $fldrow['defaultvalue'];
					$field1->sequence = $fldrow['sequence'];
					$field1->presence = $fldrow['presence'];
					$field1->readonly = $fldrow['readonly'];
					$field1->quickcreate = $fldrow['quickcreate'];
					$field1->quickcreatesequence = $fldrow['quickcreatesequence'];
					$field1->masseditable = $fldrow['masseditable'];
					$field1->helpinfo = $fldrow['helpinfo'];
					$block->addField($field1);
				}
			}

			// Migrate all the Events/Calendar workflows to cbCalendar
			$rescalwf = $adb->pquery("SELECT * FROM com_vtiger_workflows WHERE module_name IN ('Events','Calendar')", array());
			$workflowManager = new VTWorkflowManager($adb);
			$taskManager = new VTTaskManager($adb);

			while ($calwf = $adb->getNextRow($rescalwf, false)) {
				if ($calwf['summary']=='Workflow for Calendar Todos when Send Notification is True') {
					continue;
				}
				$calwf['test'] = str_replace('parent_id', 'rel_id', $calwf['test']);
				$calwf['test'] = str_replace('contact_id', 'cto_id', $calwf['test']);
				$calwf['test'] = str_replace('taskstatus', 'eventstatus', $calwf['test']);
				$calendarWorkflow = $workflowManager->newWorkFlow("cbCalendar");
				if ($calwf['summary']=='Notify when a task is delayed beyond 24 hrs') {
					$calendarWorkflow->test='[{"fieldname":"date_start","operation":"days ago","value":"1","valuetype":"expression","joincondition":"and","groupid":"0"},'
						.'{"fieldname":"activitytype","operation":"is","value":"Task","valuetype":"rawtext","joincondition":"and","groupid":"0"},'
						.'{"fieldname":"eventstatus","operation":"is not","value":"Held","valuetype":"rawtext","joincondition":"and","groupid":"0"},'
						.'{"fieldname":"eventstatus","operation":"is not","value":"Completed","valuetype":"rawtext","joincondition":"and","groupid":"0"},'
						.'{"fieldname":"eventstatus","operation":"is not","value":"In Progress","valuetype":"rawtext","joincondition":"and","groupid":"0"}]';
				} else {
					$calendarWorkflow->test = $calwf['test'];
				}
				$calendarWorkflow->description = $calwf['summary'];
				$calendarWorkflow->executionCondition = $calwf['execution_condition'];
				$calendarWorkflow->defaultworkflow = $calwf['defaultworkflow'];
				$calendarWorkflow->type = $calwf['type'];
				$calendarWorkflow->schtypeid = $calwf['schtypeid'];
				$calendarWorkflow->schtime = $calwf['schtime'];
				$calendarWorkflow->schdayofmonth = $calwf['schdayofmonth'];
				$calendarWorkflow->schdayofweek = $calwf['schdayofweek'];
				$calendarWorkflow->schannualdates = $calwf['schannualdates'];
				$calendarWorkflow->schminuteinterval = $calwf['schminuteinterval'];
				$workflowManager->save($calendarWorkflow);
				$adb->pquery(
					'UPDATE com_vtiger_workflows SET nexttrigger_time=? WHERE workflow_id=?',
					array((isset($calwf['nexttriger_time']) ? $calwf['nexttriger_time'] : null), $calendarWorkflow->id)
				);
				// get workflow tasks.
				$rescaltk = $adb->pquery("SELECT summary, task_id FROM com_vtiger_workflowtasks WHERE workflow_id = ?", array($calwf['workflow_id']));
				while ($caltk = $adb->getNextRow($rescaltk, false)) {
					$task = $taskManager->createTask('VTEmailTask', $calendarWorkflow->id);
					$task->active = true;
					$task->summary = $caltk['summary'];
					$task->entity_type = "cbCalendar";
					$task->recepient = "\$(assigned_user_id : (Users) email1)";
					$task->subject = "empty taskManager";
					$task->content = 'empty task';
					$task->test = '';
					$task->reevaluate = 1;
					$taskId = $taskManager->saveTask($task);
					//Retreive original to replace the old fieldnames and update the new task with it
					$tasktoedit = $taskManager->retrieveTask($caltk['task_id']);
					$tasktoedit->workflowId = $calendarWorkflow->id;
					$tasktoedit->id = $taskId;
					foreach ($tasktoedit as $key => $value) {
						$tasktoedit->$key = str_replace('parent_id', 'rel_id', $tasktoedit->$key);
						$tasktoedit->$key = str_replace('contact_id', 'cto_id', $tasktoedit->$key);
						$tasktoedit->$key = str_replace('taskstatus', 'eventstatus', $tasktoedit->$key);
					}
					$taskManager->saveTask($tasktoedit);
				}
			}

			//Migrate Calendar/Events filters to cbCalendar.
			$adb->pquery("UPDATE vtiger_customview SET entitytype = 'cbCalendar' WHERE entitytype IN ('Calendar','Events') AND viewname <> 'All'", array());
			$adb->pquery("UPDATE vtiger_cvcolumnlist SET columnname = REPLACE(columnname,'Calendar_','cbCalendar_') WHERE columnname LIKE '%:Calendar_%'", array());
			$adb->pquery("UPDATE vtiger_cvadvfilter SET columnname = REPLACE(columnname,'Calendar_','cbCalendar_') WHERE columnname LIKE '%:Calendar_%'", array());
			$adb->pquery("UPDATE vtiger_cvstdfilter SET columnname = REPLACE(columnname,'Calendar_','cbCalendar_') WHERE columnname LIKE '%:Calendar_%'", array());
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
	public function save_related_module($module, $crmid, $with_module, $with_crmid) {
		global $adb;
		if ($with_module == 'Contacts') {
			$with_crmid = (array)$with_crmid;
			foreach ($with_crmid as $relcrmid) {
				$checkpresence = $adb->pquery('SELECT contactid FROM vtiger_cntactivityrel WHERE activityid = ? AND contactid = ?', array($crmid, $relcrmid));
				// Relation already exists? No need to add again
				if ($checkpresence && $adb->num_rows($checkpresence)) {
					continue;
				}
				$adb->pquery('INSERT INTO vtiger_cntactivityrel(activityid,contactid) VALUES(?,?)', array($crmid, $relcrmid));
			}
		} else {
			parent::save_related_module($module, $crmid, $with_module, $with_crmid);
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
		foreach ($with_crmid as $relcrmid) {
			$data['destinationRecordId'] = $relcrmid;
			if ($with_module == 'Contacts') {
				cbEventHandler::do_action('corebos.entity.link.delete', $data);
				$adb->pquery('DELETE FROM vtiger_cntactivityrel WHERE activityid=? AND contactid=?', array($crmid, $relcrmid));
				cbEventHandler::do_action('corebos.entity.link.delete.final', $data);
			} else {
				parent::delete_related_module($module, $crmid, $with_module, $relcrmid);
			}
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
	//function get_dependents_list($id, $cur_tab_id, $rel_tab_id, $actions=false) { }

	public static function getCalendarActivityType($record) {
		if (empty($record) || !is_numeric($record)) {
			return 'Call';
		}
		global $adb;
		$cbcrs = $adb->pquery('select activitytype from vtiger_activity where activityid=?', array($record));
		if ($cbcrs && $adb->num_rows($cbcrs)==1) {
			$atype = $adb->query_result($cbcrs, 0, 0);
		} else {
			$atype = 'Call';
		}
		return $atype;
	}

	/**
	 * this function sets the status flag of activity to true or false depending on the status passed to it
	 * @param string $status - the status of the activity flag to set
	 * @return:: true if successful; false otherwise
	 */
	public function setActivityReminder($status) {
		global $adb;
		if ($status == 'on') {
			$flag = 0;
		} elseif ($status == 'off') {
			$flag = 1;
		} else {
			return false;
		}
		$sql = 'update vtiger_activity_reminder_popup set status=1 where recordid=?';
		$adb->pquery($sql, array($this->id));
		return true;
	}

	public function clearSingletonSaveFields() {
		unset($_REQUEST['timefmt_dtstart'], $_REQUEST['timefmt_dtend']);
	}

	/** Function to change the status of an event
	 * @param $status string : new status value
	 * @param $activityid integer : activity id
	 */
	public static function changeStatus($status, $activityid) {
		global $log, $current_user;
		$log->debug("Entering changeStatus($status, $activityid) method");
		include_once 'include/Webservices/Revise.php';
		$element = array(
			'id' => vtws_getEntityId('cbCalendar') . 'x' . $activityid,
			'eventstatus' => $status
		);
		vtws_revise($element, $current_user);
		$log->debug('Exiting changeStatus method');
	}

	/*
	 * Function to get the primary query part of a report
	 * @param - $module primary module name
	 * returns the query string formed on fetching the related data for report for secondary module
	 */
	public function generateReportsQuery($module, $queryPlanner) {
		$query = parent::generateReportsQuery($module, $queryPlanner);
		if ($queryPlanner->requireTable('vtiger_activity_reminder')) {
			$query .= ' left join vtiger_activity_reminder on vtiger_activity_reminder.activity_id = vtiger_activity.activityid';
		}
		if ($queryPlanner->requireTable('vtiger_recurringevents')) {
			$query .= ' left join vtiger_recurringevents on vtiger_recurringevents.activityid = vtiger_activity.activityid';
		}
		return $query;
	}
}
?>
