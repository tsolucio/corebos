<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/
require_once 'include/logging.php';
require_once 'include/database/PearDatabase.php';
require_once 'data/CRMEntity.php';
require_once 'modules/Contacts/Contacts.php';
require_once 'modules/Accounts/Accounts.php';
require_once 'modules/Potentials/Potentials.php';
require_once 'modules/Users/Users.php';

class Emails extends CRMEntity {
	public $db;
	public $log;

	public $table_name = 'vtiger_activity';
	public $table_index = 'activityid';
	public $column_fields = array();

	/** Indicator if this is a custom module or standard module */
	public $IsCustomModule = false;

	// added to check email save from plugin or not
	public $plugin_save = false;
	public $rel_users_table = 'vtiger_salesmanactivityrel';
	public $rel_contacts_table = 'vtiger_cntactivityrel';
	public $rel_serel_table = 'vtiger_seactivityrel';
	public $tab_name = array('vtiger_crmentity', 'vtiger_activity', 'vtiger_emaildetails');
	public $tab_name_index = array('vtiger_crmentity' => 'crmid', 'vtiger_activity' => 'activityid',
		'vtiger_seactivityrel' => 'activityid', 'vtiger_cntactivityrel' => 'activityid', 'vtiger_email_track' => 'mailid', 'vtiger_emaildetails' => 'emailid');
	public $list_fields = array(
		'Subject' => array('activity' => 'subject'),
		'Related to' => array('seactivityrel' => 'parent_id'),
		'Date Sent' => array('activity' => 'date_start'),
		'Time Sent' => array('activity' => 'time_start'),
		'Assigned To' => array('crmentity' => 'smownerid'),
		'Access Count' => array('email_track' => 'access_count')
	);
	public $list_fields_name = array(
		'Subject' => 'subject',
		'Related to' => 'parent_id',
		'Date Sent' => 'date_start',
		'Time Sent' => 'time_start',
		'Assigned To' => 'assigned_user_id',
		'Access Count' => 'access_count'
	);
	public $list_link_field = 'subject';
	public $sortby_fields = array('subject', 'date_start', 'saved_toid');

	// For Alphabetical search
	public $def_basicsearch_col = 'subject';

	// Column value to use on detail view record text display
	public $def_detailview_recname = 'subject';

	public $default_order_by = 'date_start';
	public $default_sort_order = 'DESC';
	// Used when enabling/disabling the mandatory fields for the module.
	// Refers to vtiger_field.fieldname values.
	public $mandatory_fields = array('subject', 'assigned_user_id');

	public function __construct() {
		global $log;
		$this_module = get_class($this);
		$this->column_fields = getColumnFields($this_module);
		$this->db = PearDatabase::getInstance();
		$this->log = $log;
	}

	public function save_module($module) {
		global $adb;
		//Inserting into seactivityrel
		if (!empty($_REQUEST['module']) && $_REQUEST['module'] == 'Emails' && !$this->plugin_save) {
			if ($_REQUEST['currentid'] != '') {
				$actid = $_REQUEST['currentid'];
			} else {
				$actid = $_REQUEST['record'];
			}
			$parentid = $_REQUEST['parent_id'];
			if ($_REQUEST['module'] != 'Emails') {
				if (!$parentid) {
					$parentid = $adb->getUniqueID('vtiger_seactivityrel');
				}
				$mysql = 'insert into vtiger_seactivityrel values(?,?)';
				$adb->pquery($mysql, array($parentid, $actid));
			} else {
				$myids = explode("|", $parentid);  //2@71|
				for ($i = 0; $i < (count($myids) - 1); $i++) {
					$realid = explode("@", $myids[$i]);
					$mycrmid = $realid[0];
					//added to handle the relationship of emails with vtiger_users
					if (getModuleForField($realid[1]) == 'Users') {
						$del_q = 'delete from vtiger_salesmanactivityrel where smid=? and activityid=?';
						$adb->pquery($del_q, array($mycrmid, $actid));
						$mysql = 'insert into vtiger_salesmanactivityrel values(?,?)';
					} else {
						$del_q = 'delete from vtiger_seactivityrel where crmid=? and activityid=?';
						$adb->pquery($del_q, array($mycrmid, $actid));
						$mysql = 'insert into vtiger_seactivityrel values(?,?)';
					}
					$params = array($mycrmid, $actid);
					$adb->pquery($mysql, $params);
				}
			}
		} else {
			if (isset($this->column_fields['parent_id']) && $this->column_fields['parent_id'] != '') {
				$realid = explode('@', $this->column_fields['parent_id']);
				$mycrmid = $realid[0];
				if ($realid[1]=='-1') { // user
					$adb->pquery('DELETE FROM vtiger_salesmanactivityrel WHERE smid = ? AND activityid = ?', array($mycrmid, $this->id));
					//$this->insertIntoEntityTable('vtiger_seactivityrel', $module);
					$sql = 'insert into vtiger_salesmanactivityrel values (?,?)';
					$params = array($mycrmid, $this->id);
				} else {
					$adb->pquery('DELETE FROM vtiger_seactivityrel WHERE crmid = ? AND activityid = ?', array($this->column_fields['parent_id'], $this->id));
					//$this->insertIntoEntityTable('vtiger_seactivityrel', $module);
					$sql = 'insert into vtiger_seactivityrel values(?,?)';
					$params = array($mycrmid, $this->id);
				}
				$adb->pquery($sql, $params);
			} elseif (empty($this->column_fields['parent_id']) && $this->mode == 'edit') {
				$this->deleteRelation('vtiger_seactivityrel');
			}
		}

		//Insert into cntactivity rel
		if (isset($this->column_fields['contact_id']) && $this->column_fields['contact_id'] != '') {
			$this->insertIntoEntityTable('vtiger_cntactivityrel', $module);
		} elseif (empty($this->column_fields['contact_id']) && $this->mode == 'edit') {
			$this->deleteRelation('vtiger_cntactivityrel');
		}

		//Inserting into attachment
		$this->insertIntoAttachment($this->id, $module);
	}

	/**
	 * Function to get the array of record ids from a string pattern like "2@71|17@-1|120@15"
	 * This will filter user record ids
	 * @param type $recordIdsStr
	 * @return type
	 */
	public function getCRMIdsFromStringPattern($recordIdsStr) {
		$recordIds = array();
		if (strpos($recordIdsStr, '@') !== false && strpos($recordIdsStr, '|') !== false) {
			$recordIdsParts = explode('|', $recordIdsStr);
			for ($i = 0; $i < (count($recordIdsParts) - 1); $i++) {
				$recordIdParts = explode('@', $recordIdsParts[$i]);
				//filter user records
				if ($recordIdParts[1] !== -1) {
					$recordIds[] = $recordIdParts[0];
				}
			}
		}
		return $recordIds;
	}

	public function insertIntoAttachment($id, $module, $direct_import = false) {
		global $log, $adb;
		$log->debug("Entering into insertIntoAttachment($id,$module) method.");

		$file_saved = false;

		//Added to send generated Invoice PDF with mail
		$pdfAttached = isset($_REQUEST['pdf_attachment']) ? $_REQUEST['pdf_attachment'] : '';
		//created Invoice pdf is attached with the mail
		if (isset($_REQUEST['pdf_attachment']) && $_REQUEST['pdf_attachment'] != '') {
			$file_saved = pdfAttach($this, $module, $pdfAttached, $id);
		}

		//This is to added to store the existing attachment id of the contact where we should delete this when we give new image
		foreach ($_FILES as $fileindex => $files) {
			if ($files['name'] != '' && $files['size'] > 0) {
				$files['original_name'] = (empty($_REQUEST[$fileindex.'_hidden']) ? vtlib_purify($files['name']) : vtlib_purify($_REQUEST[$fileindex.'_hidden']));
				$file_saved = $this->uploadAndSaveFile($id, $module, $files);
			}
		}
		if ($module == 'Emails' && isset($_REQUEST['att_id_list']) && $_REQUEST['att_id_list'] != '') {
			$att_lists = explode(";", $_REQUEST['att_id_list'], -1);
			$sql_rel = 'insert into vtiger_seattachmentsrel values(?,?)';
			foreach ($att_lists as $att) {
				$adb->pquery($sql_rel, array($id, $att));
			}
		}
		if ($module == 'Emails' && isset($_REQUEST['doc_attachments']) && count($_REQUEST['doc_attachments']) > 0) {
			$documentIds = $_REQUEST['doc_attachments'];
			for ($i = 0; $i < count($documentIds); $i++) {
				$query = 'select attachmentsid from vtiger_seattachmentsrel where crmid=?';
				$res = $adb->pquery($query, array($documentIds[$i]));
				$attachmentId = $adb->query_result($res, 0, 0);
				$query = 'insert into vtiger_seattachmentsrel values(?, ?)';
				$adb->pquery($query, array($id,$attachmentId));
			}
		}
		$log->debug("Exiting from insertIntoAttachment($id,$module) method.");
	}

	public static function EmailHasBeenSent($emailid) {
		global $adb;
		if (strpos($emailid, 'x')>0) {
			list($wsid, $emailid) = explode('x', $emailid);
		}
		$sql = 'select email_flag from vtiger_emaildetails where emailid=?';
		$result = $adb->pquery($sql, array($emailid));
		$email_flag = $adb->query_result($result, 0, 'email_flag');
		return  ($email_flag == 'SENT');
	}

	/*
	* Function to get the secondary query part of a report
	* @param - $module primary module name
	* @param - $secmodule secondary module name
	* returns the query string formed on fetching the related data for report for secondary module
	*/
	public function generateReportsSecQuery($module, $secmodule, $queryPlanner, $type = '', $where_condition = '') {
		$matrix = $queryPlanner->newDependencyMatrix();

		$matrix->setDependency("vtiger_crmentityEmails", array("vtiger_groupsEmails","vtiger_usersEmails","vtiger_lastModifiedByEmails"));

		if (!$queryPlanner->requireTable('vtiger_activity', $matrix)) {
			return '';
		}

		$matrix->setDependency("vtiger_activity", array("vtiger_crmentityEmails","vtiger_email_track"));

		$query = $this->getRelationQuery($module, $secmodule, "vtiger_activity", "activityid", $queryPlanner);
		$query = " LEFT JOIN vtiger_seactivityrel ON vtiger_crmentity.crmid=vtiger_seactivityrel.crmid";
		$query .= " LEFT JOIN vtiger_activity ON vtiger_seactivityrel.activityid=vtiger_activity.activityid and vtiger_activity.activitytype = 'Emails'";
		$query .= " LEFT JOIN vtiger_crmentity as vtiger_crmentityEmails ON vtiger_crmentityEmails.crmid=vtiger_activity.activityid and vtiger_crmentityEmails.deleted=0";
		$query .= " LEFT JOIN vtiger_emaildetails ON vtiger_emaildetails.emailid=vtiger_crmentityEmails.crmid";
		if ($queryPlanner->requireTable("vtiger_groupsEmails")) {
			$query .= " LEFT JOIN vtiger_groups AS vtiger_groupsEmails ON vtiger_groupsEmails.groupid = vtiger_crmentityEmails.smownerid";
		}
		if ($queryPlanner->requireTable("vtiger_usersEmails")) {
			$query .= " LEFT JOIN vtiger_users AS vtiger_usersEmails ON vtiger_usersEmails.id = vtiger_crmentityEmails.smownerid";
		}
		if ($queryPlanner->requireTable("vtiger_lastModifiedByEmails")) {
			$query .= " LEFT JOIN vtiger_users AS vtiger_lastModifiedByEmails ON vtiger_lastModifiedByEmails.id = vtiger_crmentityEmails.modifiedby and vtiger_seactivityreltmpEmails.activityid = vtiger_activityEmails.activityid";
		}
		if ($queryPlanner->requireTable("vtiger_CreatedByEmails")) {
			$query .= " left join vtiger_users as vtiger_CreatedByEmails on vtiger_CreatedByEmails.id = vtiger_crmentityEmails.smcreatorid and vtiger_seactivityreltmpEmails.activityid = vtiger_activityEmails.activityid";
		}
		if ($queryPlanner->requireTable("vtiger_email_track")) {
			$query .= " LEFT JOIN vtiger_email_track ON vtiger_email_track.mailid = vtiger_emaildetails.emailid and vtiger_email_track.crmid = vtiger_crmentity.crmid";
		}
		return $query;
	}

	/*
	* Function to get the relation tables for related modules
	* @param - $secmodule secondary module name
	* returns the array with table names and fieldnames storing relations between module and this module
	*/
	public function setRelationTables($secmodule) {
		$rel_tables = array (
			"Leads" => array("vtiger_seactivityrel" => array("activityid", "crmid"), "vtiger_activity" => "activityid"),
			"Vendors" => array("vtiger_seactivityrel" => array("activityid", "crmid"), "vtiger_activity" => "activityid"),
			"Contacts" => array("vtiger_seactivityrel" => array("activityid", "crmid"), "vtiger_activity" => "activityid"),
			"Accounts" => array("vtiger_seactivityrel" => array("activityid", "crmid"), "vtiger_activity" => "activityid"),
		);
		return isset($rel_tables[$secmodule]) ? $rel_tables[$secmodule] : '';
	}

	/** Returns a list of the associated contacts */
	public function get_contacts($id, $cur_tab_id, $rel_tab_id, $actions = false) {
		global $log, $currentModule, $adb;
		$log->debug("Entering get_contacts(" . $id . ") method ...");
		$this_module = $currentModule;

		$related_module = vtlib_getModuleNameById($rel_tab_id);
		require_once "modules/$related_module/$related_module.php";
		$other = new $related_module();

		$parenttab = getParentTab();

		$returnset = '&return_module=' . $this_module . '&return_action=DetailView&return_id=' . $id;

		$button = '';

		if ($actions) {
			if (is_string($actions)) {
				$actions = explode(',', strtoupper($actions));
			}
			if (in_array('SELECT', $actions) && isPermitted($related_module, 4, '') == 'yes') {
				$button .= "<input title='" . getTranslatedString('LBL_SELECT') . " " . getTranslatedString($related_module) .
				"' class='crmbutton small edit' type='button' onclick=\"return window.open('index.php?module=$related_module&return_module=$currentModule&action=Popup".
				"&popuptype=detailview&select=enable&form=EditView&form_submit=false&recordid=$id&parenttab=$parenttab','test',".
				"'width=640,height=602,resizable=0,scrollbars=0');\" value='" . getTranslatedString('LBL_SELECT') .' '. getTranslatedString($related_module) . "'>&nbsp;";
			}
			if (in_array('BULKMAIL', $actions) && isPermitted($related_module, 1, '') == 'yes') {
				$button .= "<input title='" . getTranslatedString('LBL_BULK_MAILS') . "' class='crmbutton small create'" .
						" onclick='this.form.action.value=\"sendmail\";this.form.module.value=\"$this_module\"' type='submit' name='button'" .
						" value='" . getTranslatedString('LBL_BULK_MAILS') . "'>";
			}
		}

		$query = 'select vtiger_contactdetails.accountid, vtiger_contactdetails.contactid, vtiger_contactdetails.firstname,vtiger_contactdetails.lastname,
			vtiger_contactdetails.department, vtiger_contactdetails.title, vtiger_contactdetails.email, vtiger_contactdetails.phone, vtiger_contactdetails.emailoptout,
			vtiger_crmentity.crmid, vtiger_crmentity.smownerid, vtiger_crmentity.modifiedtime
			from vtiger_contactdetails
			inner join vtiger_cntactivityrel on vtiger_cntactivityrel.contactid=vtiger_contactdetails.contactid
			inner join vtiger_crmentity on vtiger_crmentity.crmid = vtiger_contactdetails.contactid
			left join vtiger_groups on vtiger_groups.groupid=vtiger_crmentity.smownerid
			where vtiger_crmentity.deleted=0 and vtiger_cntactivityrel.activityid=' . $adb->quote($id);

		$return_value = GetRelatedList($this_module, $related_module, $other, $query, $button, $returnset);

		if ($return_value == null) {
			$return_value = array();
		}
		$return_value['CUSTOM_BUTTON'] = $button;

		$log->debug("Exiting get_contacts method ...");
		return $return_value;
	}

	/** Returns the column name that needs to be sorted */
	public function getSortOrder() {
		global $log;
		$log->debug("Entering getSortOrder() method ...");
		if (isset($_REQUEST['sorder'])) {
			$sorder = $this->db->sql_escape_string($_REQUEST['sorder']);
		} else {
			$sorder = (!empty($_SESSION['EMAILS_SORT_ORDER']) ? ($_SESSION['EMAILS_SORT_ORDER']) : ($this->default_sort_order));
		}

		$log->debug("Exiting getSortOrder method ...");
		return $sorder;
	}

	/** Returns the order in which the records need to be sorted */
	public function getOrderBy() {
		global $log;
		$log->debug("Entering getOrderBy() method ...");

		$use_default_order_by = '';
		if (GlobalVariable::getVariable('Application_ListView_Default_Sorting', 0)) {
			$use_default_order_by = $this->default_order_by;
		}

		if (isset($_REQUEST['order_by'])) {
			$order_by = $this->db->sql_escape_string($_REQUEST['order_by']);
		} else {
			$order_by = (!empty($_SESSION['EMAILS_ORDER_BY']) ? ($_SESSION['EMAILS_ORDER_BY']) : ($use_default_order_by));
		}

		$log->debug("Exiting getOrderBy method ...");
		return $order_by;
	}

	/** Returns a list of the associated users */
	public function get_users($id) {
		global $log, $adb, $app_strings, $current_user;
		$log->debug("Entering get_users( $id ) method ...");

		$id = $_REQUEST['record'];

		$button = '<input title="' . getTranslatedString('LBL_BULK_MAILS') . '" accessykey="F" class="crmbutton small create"
			onclick="this.form.action.value=\"sendmail\";this.form.return_action.value=\"DetailView\";this.form.module.value=\"Emails\";this.form.return_module.value=\"Emails\";"
			name="button" value="' . getTranslatedString('LBL_BULK_MAILS') . '" type="submit">&nbsp;
			<input title="' . getTranslatedString('LBL_BULK_MAILS') . '" accesskey="" tabindex="2" class="crmbutton small edit"
			value="' . getTranslatedString('LBL_SELECT_USER_BUTTON_LABEL') . '" name="Button"
			onclick=\"return window.open("index.php?module=Users&return_module=Emails&action=Popup&popuptype=detailview&select=enable&form=EditView&form_submit=true&return_id='.
			$id . '&recordid=' . $id . '","test","width=640,height=520,resizable=0,scrollbars=0");\"type="button">';

		$query = 'SELECT vtiger_users.id, vtiger_users.first_name, vtiger_users.last_name, vtiger_users.user_name, vtiger_users.email1, vtiger_users.email2,
			vtiger_users.secondaryemail, vtiger_users.phone_home, vtiger_users.phone_work, vtiger_users.phone_mobile, vtiger_users.phone_other, vtiger_users.phone_fax
			from vtiger_users
			inner join vtiger_salesmanactivityrel on vtiger_salesmanactivityrel.smid=vtiger_users.id and vtiger_salesmanactivityrel.activityid=?';
		$result = $adb->pquery($query, array($id));

		$header [] = $app_strings['LBL_LIST_NAME'];
		$header [] = $app_strings['LBL_LIST_USER_NAME'];
		$header [] = $app_strings['LBL_EMAIL'];
		$header [] = $app_strings['LBL_PHONE'];
		while ($row = $adb->fetch_array($result)) {
			$entries = array();

			$entries[] = getFullNameFromArray('Users', $row);

			$entries[] = $row['user_name'];
			$entries[] = $row['email1'];
			if ($email == '') {
				$email = $row['email2'];
			}
			if ($email == '') {
				$email = $row['secondaryemail'];
			}
			$entries[] = $row['phone_home'];
			if ($phone == '') {
				$phone = $row['phone_work'];
			}
			if ($phone == '') {
				$phone = $row['phone_mobile'];
			}
			if ($phone == '') {
				$phone = $row['phone_other'];
			}
			if ($phone == '') {
				$phone = $row['phone_fax'];
			}
			$entries_list[] = $entries;
		}

		if ($entries_list != '') {
			$return_data = array("header" => $header, "entries" => $entries);
		}

		if ($return_data == null) {
			$return_data = array();
		}
		$return_data['CUSTOM_BUTTON'] = $button;

		$log->debug("Exiting get_users method ...");
		return $return_data;
	}

	/**
	 * Returns a list of the Emails to be exported
	 */
	public function create_export_query($where) {
		global $log, $current_user;
		$log->debug("Entering create_export_query( $where ) method ...");

		include "include/utils/ExportUtils.php";

		//To get the Permitted fields query and the permitted fields list
		$sql = getPermittedFieldsQuery("Emails", "detail_view");
		$fields_list = getFieldsListFromQuery($sql);

		$query = "SELECT $fields_list FROM vtiger_activity
			INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid=vtiger_activity.activityid
			LEFT JOIN vtiger_users ON vtiger_users.id = vtiger_crmentity.smownerid
			LEFT JOIN vtiger_users as vtigerCreatedBy ON vtiger_crmentity.smcreatorid = vtigerCreatedBy.id and vtigerCreatedBy.status='Active'
			LEFT JOIN vtiger_seactivityrel ON vtiger_seactivityrel.activityid = vtiger_activity.activityid
			LEFT JOIN vtiger_contactdetails ON vtiger_contactdetails.contactid = vtiger_seactivityrel.crmid
			LEFT JOIN vtiger_cntactivityrel ON vtiger_cntactivityrel.activityid = vtiger_activity.activityid
				AND vtiger_cntactivityrel.contactid = vtiger_cntactivityrel.contactid
			LEFT JOIN vtiger_groups ON vtiger_groups.groupid = vtiger_crmentity.smownerid
			LEFT JOIN vtiger_salesmanactivityrel ON vtiger_salesmanactivityrel.activityid = vtiger_activity.activityid
			LEFT JOIN vtiger_emaildetails ON vtiger_emaildetails.emailid = vtiger_activity.activityid
			LEFT JOIN vtiger_seattachmentsrel ON vtiger_activity.activityid=vtiger_seattachmentsrel.crmid
			LEFT JOIN vtiger_attachments ON vtiger_seattachmentsrel.attachmentsid = vtiger_attachments.attachmentsid";
		$query .= getNonAdminAccessControlQuery('Emails', $current_user);
		$query .= "WHERE vtiger_activity.activitytype='Emails' AND vtiger_crmentity.deleted=0 ";

		$log->debug("Exiting create_export_query method ...");
		return $query;
	}

	/**
	 * Used to releate email and contacts -- Outlook Plugin
	 */
	public function set_emails_contact_invitee_relationship($email_id, $contact_id) {
		global $log;
		$log->debug("Entering set_emails_contact_invitee_relationship(" . $email_id . "," . $contact_id . ") method ...");
		$query = "insert into $this->rel_contacts_table (contactid,activityid) values(?,?)";
		$this->db->pquery($query, array($contact_id, $email_id), true, "Error setting email to contact relationship: <BR>$query");
		$log->debug("Exiting set_emails_contact_invitee_relationship method ...");
	}

	/**
	 * Used to releate email and salesentity -- Outlook Plugin
	 */
	public function set_emails_se_invitee_relationship($email_id, $contact_id) {
		global $log;
		$log->debug("Entering set_emails_se_invitee_relationship(" . $email_id . "," . $contact_id . ") method ...");
		$query = "insert into $this->rel_serel_table (crmid,activityid) values(?,?)";
		$this->db->pquery($query, array($contact_id, $email_id), true, "Error setting email to contact relationship: <BR>$query");
		$log->debug("Exiting set_emails_se_invitee_relationship method ...");
	}

	/**
	 * Used to releate email and Users -- Outlook Plugin
	 */
	public function set_emails_user_invitee_relationship($email_id, $user_id) {
		global $log;
		$log->debug("Entering set_emails_user_invitee_relationship(" . $email_id . "," . $user_id . ") method ...");
		$query = "insert into $this->rel_users_table (smid,activityid) values (?,?)";
		$this->db->pquery($query, array($user_id, $email_id), true, "Error setting email to user relationship: <BR>$query");
		$log->debug("Exiting set_emails_user_invitee_relationship method ...");
	}

	// Function to unlink an entity with given Id from another entity
	public function unlinkRelationship($id, $return_module, $return_id) {
		$sql = 'DELETE FROM vtiger_seactivityrel WHERE activityid=? AND crmid = ?';
		$this->db->pquery($sql, array($id, $return_id));
		$sql = 'DELETE FROM vtiger_crmentityrel WHERE (crmid=? AND relmodule=? AND relcrmid=?) OR (relcrmid=? AND module=? AND crmid=?)';
		$params = array($id, $return_module, $return_id, $id, $return_module, $return_id);
		$this->db->pquery($sql, $params);
		$this->db->pquery('UPDATE vtiger_crmentity SET modifiedtime = ? WHERE crmid = ?', array(date('y-m-d H:i:d'), $id));
	}

	public function getListButtons($app_strings) {
		global $currentModule;
		$list_buttons = array();

		if (isPermitted($currentModule, 'Delete', '') == 'yes') {
			$list_buttons['del'] = $app_strings['LBL_MASS_DELETE'];
		}
		unset($list_buttons['mass_edit']);
		return $list_buttons;
	}
}

/** Function to get the emailids for the given ids form the request parameters
 *  It returns an array which contains the mailids and the parentidlists
 */
function get_to_emailids($module) {
	global $adb, $current_user;
	require_once 'include/Webservices/Query.php';
	//$idlists1 = "";
	if (empty($_REQUEST['field_lists'])) {
		switch ($module) {
			case 'Accounts':
				$_REQUEST['field_lists']=getFieldid(getTabid('Accounts'), 'email1');
				break;
			case 'Contacts':
				$_REQUEST['field_lists']=getFieldid(getTabid('Contacts'), 'email');
				break;
			case 'Vendors':
				$_REQUEST['field_lists']=getFieldid(getTabid('Vendors'), 'email');
				break;
		}
	}
	$fieldids = explode(':', vtlib_purify($_REQUEST['field_lists']));
	if ($_REQUEST['idlist'] == 'all' || $_REQUEST['idlist'] == 'relatedListSelectAll') {
		$idlist = getSelectedRecords($_REQUEST, vtlib_purify($_REQUEST['pmodule']), vtlib_purify($_REQUEST['idlist']), vtlib_purify($_REQUEST['excludedRecords']));
	} else {
		$idlist = explode(':', str_replace('undefined', '', vtlib_purify($_REQUEST['idlist'])));
	}

	$entityids = array();
	foreach ($idlist as $key => $id) {
		$entityids[] = vtws_getWebserviceEntityId($module, $id);
	}
	$vtwsObject = VtigerWebserviceObject::fromName($adb, $module);
	$vtwsCRMObjectMeta = new VtigerCRMObjectMeta($vtwsObject, $current_user);
	$emailFields = $vtwsCRMObjectMeta->getEmailFields();

	foreach ($emailFields as $key => $fieldname) {
		$fieldid = $vtwsCRMObjectMeta->getFieldIdFromFieldName($fieldname);
		if (!in_array($fieldid, $fieldids)) {
			unset($emailFields[$key]);
		}
	}
	if (empty($emailFields)) {
		return false;
	}
	$params = $idlist;
	if ($module == 'Leads') {
		$query = 'SELECT firstname,lastname,'.implode(",", $emailFields).',vtiger_leaddetails.leadid as id
				  FROM vtiger_leaddetails
				  INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid=vtiger_leaddetails.leadid
				  LEFT JOIN vtiger_leadscf ON vtiger_leaddetails.leadid = vtiger_leadscf.leadid
				  WHERE vtiger_crmentity.deleted=0 AND vtiger_leaddetails.leadid IN ('.generateQuestionMarks($idlist).')';
		$leadcols = $adb->getColumnNames('vtiger_leaddetails');
		if (in_array('emailoptout', $leadcols)) {
			$query = $query.' AND vtiger_leaddetails.emailoptout=0';
		}
	} elseif ($module == 'Contacts') {
		$query = 'SELECT firstname,lastname,'.implode(",", $emailFields).',vtiger_contactdetails.contactid as id
				  FROM vtiger_contactdetails
				  INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid=vtiger_contactdetails.contactid
				  LEFT JOIN vtiger_contactscf ON vtiger_contactdetails.contactid = vtiger_contactscf.contactid
				  WHERE vtiger_crmentity.deleted=0 AND vtiger_contactdetails.contactid IN ('.generateQuestionMarks($idlist).') AND vtiger_contactdetails.emailoptout=0';
	} elseif ($module == 'Accounts') {
		$query = 'SELECT vtiger_account.accountname, '.implode(",", $emailFields).',vtiger_account.accountid as id FROM vtiger_account
				   INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid=vtiger_account.accountid
				   LEFT JOIN vtiger_accountscf ON vtiger_accountscf.accountid= vtiger_account.accountid
				   WHERE vtiger_crmentity.deleted=0 AND vtiger_account.accountid IN ('.generateQuestionMarks($idlist).') AND vtiger_account.emailoptout=0';
	} elseif ($module == 'Project') {
		$query = 'SELECT projectname,'.implode(",", $emailFields).',vtiger_project.projectid as id
				  FROM vtiger_project
				  INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid=vtiger_project.projectid
				  LEFT JOIN vtiger_projectcf ON vtiger_projectcf.projectid = vtiger_project.projectid
				  WHERE vtiger_crmentity.deleted=0 AND vtiger_project.projectid IN ('.generateQuestionMarks($idlist).')';
	} elseif ($module == 'ProjectTask') {
		$query = 'SELECT projecttaskname,'.implode(",", $emailFields).',vtiger_projecttask.projecttaskid as id
				  FROM vtiger_projecttask
				  INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid=vtiger_projecttask.projecttaskid
				  LEFT JOIN vtiger_projecttaskcf ON vtiger_projecttaskcf.projecttaskid = vtiger_projecttask.projecttaskid
				  WHERE vtiger_crmentity.deleted=0 AND vtiger_projecttask.projecttaskid IN ('.generateQuestionMarks($idlist).')';
	} elseif ($module == 'Potentials') {
		$query = 'SELECT potentialname,'.implode(",", $emailFields).',vtiger_potential.potentialid as id
				  FROM vtiger_potential
				  INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid=vtiger_potential.potentialid
				  LEFT JOIN vtiger_potentialscf ON vtiger_potentialscf.potentialid = vtiger_potential.potentialid
				  WHERE vtiger_crmentity.deleted=0 AND vtiger_potential.potentialid IN ('.generateQuestionMarks($idlist).')';
	} elseif ($module == 'HelpDesk') {
		$query = 'SELECT title,'.implode(",", $emailFields).',vtiger_troubletickets.ticketid as id
				  FROM vtiger_troubletickets
				  INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid=vtiger_troubletickets.ticketid
				  LEFT JOIN vtiger_ticketcf ON vtiger_ticketcf.ticketid = vtiger_troubletickets.ticketid
				  WHERE vtiger_crmentity.deleted=0 AND vtiger_troubletickets.ticketid IN ('.generateQuestionMarks($idlist).')';
	} elseif ($module == 'Vendors') {
		$query = 'SELECT vtiger_vendor.vendorname, '.implode(",", $emailFields).',vtiger_vendor.vendorid as id FROM vtiger_vendor
				   INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid=vtiger_vendor.vendorid
				   LEFT JOIN vtiger_vendorcf ON vtiger_vendorcf.vendorid= vtiger_vendor.vendorid
				   WHERE vtiger_crmentity.deleted=0 AND vtiger_vendor.vendorid IN ('.generateQuestionMarks($idlist).')';
	} else {
		$minfo = getEntityFieldNames($module);
		$qg = new QueryGenerator($module, $current_user);
		$fields = $emailFields;
		$fields[] = $minfo['fieldname'];
		$fields[] = 'id';
		$qg->setFields($fields);
		$qg->addCondition('id', $idlist, 'i');
		$query = $qg->getQuery();
		$query = preg_replace('/'.$minfo['entityidfield'].'/', $minfo['entityidfield'].' as id', $query, 1);
		$fldrs = $adb->pquery('select columnname from vtiger_field where tabid=? and fieldname=?', array(getTabid($module),$minfo['fieldname']));
		$minfo['columnname'] = $adb->query_result($fldrs, 0, 0);
		$params = array();
	}
	$result = $adb->pquery($query, $params);

	$idlists = $mailids = '';
	if ($adb->num_rows($result)>0) {
		while ($entityvalue = $adb->fetchByAssoc($result)) {
			$vtwsid = $entityvalue['id'];
			foreach ($emailFields as $emailFieldName) {
				if ($entityvalue[$emailFieldName] != null || $entityvalue[$emailFieldName] != '') {
					$idlists .= $vtwsid . '@' . $vtwsCRMObjectMeta->getFieldIdFromFieldName($emailFieldName) . '|';
					if ($module == 'Leads' || $module == 'Contacts') {
						$mailids .= $entityvalue['lastname'] . " " . $entityvalue['firstname'] . "<" . $entityvalue[$emailFieldName] . ">,";
					} elseif ($module == "Project") {
						$mailids .= $entityvalue['projectname'] . "<" . $entityvalue[$emailFieldName] . ">,";
					} elseif ($module == "ProjectTask") {
						$mailids .= $entityvalue['projecttaskname'] . "<" . $entityvalue[$emailFieldName] . ">,";
					} elseif ($module == "Potentials") {
						$mailids .= $entityvalue['potentialname'] . "<" . $entityvalue[$emailFieldName] . ">,";
					} elseif ($module == "HelpDesk") {
						$mailids .= $entityvalue['title'] . "<" . $entityvalue[$emailFieldName] . ">,";
					} elseif ($module == "Vendors") {
						$mailids .= $entityvalue['vendorname'] . "<" . $entityvalue[$emailFieldName] . ">,";
					} elseif ($module == "Accounts") {
						$mailids .= $entityvalue['accountname'] . "<" . $entityvalue[$emailFieldName] . ">,";
					} else {
						$mailids .= $entityvalue[$minfo['columnname']] . "<" . $entityvalue[$emailFieldName] . ">,";
					}
				}
			}
		}
	}
	return array('idlists' => $idlists, 'mailds' => $mailids);
}

// attach the generated pdf with the email
function pdfAttach($obj, $module, $file_name, $id) {
	global $log, $adb, $current_user;
	$log->debug("Entering into pdfAttach() method.");

	$file_name = basename($file_name);
	$date_var = date('Y-m-d H:i:s');

	$ownerid = $obj->column_fields['assigned_user_id'];
	if (!isset($ownerid) || $ownerid == '') {
		$ownerid = $current_user->id;
	}

	$current_id = $adb->getUniqueID("vtiger_crmentity");

	$upload_file_path = decideFilePath();

	//Copy the file from temporary directory into storage directory for upload
	$source_file_path = "storage/" . $file_name;
	$status = copy($source_file_path, $upload_file_path . $current_id . "_" . $file_name);
	//Check wheather the copy process is completed successfully or not. if failed no need to put entry in attachment table
	if ($status) {
		$query1 = 'insert into vtiger_crmentity (crmid,smcreatorid,smownerid,setype,description,createdtime,modifiedtime) values(?,?,?,?,?,?,?)';
		$dv = $adb->formatDate($date_var, true);
		$params1 = array($current_id, $current_user->id, $ownerid, "$module Attachment", $obj->column_fields['description'], $dv, $dv);
		$adb->pquery($query1, $params1);

		$query2 = 'insert into vtiger_attachments(attachmentsid, name, description, type, path) values(?,?,?,?,?)';
		$params2 = array($current_id, $file_name, $obj->column_fields['description'], 'pdf', $upload_file_path);
		$adb->pquery($query2, $params2);

		$query3 = 'insert into vtiger_seattachmentsrel values(?,?)';
		$adb->pquery($query3, array($id, $current_id));

		// Delete the file that was copied
		checkFileAccessForDeletion($source_file_path);
		unlink($source_file_path);

		return true;
	} else {
		$log->debug('pdf not attached');
		return false;
	}
}

//this function check email fields profile permission as well as field access permission
function emails_checkFieldVisiblityPermission($fieldname, $mode = 'readonly') {
	global $current_user;
	$ret = getFieldVisibilityPermission('Emails', $current_user->id, $fieldname, $mode);
	return $ret;
}
?>
