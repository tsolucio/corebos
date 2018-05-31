<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/
include_once 'config.inc.php';
require_once 'include/logging.php';
require_once 'include/database/PearDatabase.php';

/** This class is used to track the recently viewed items on a per user basis.
 * It is intended to be called by each module when rendering the detail form.
 */
class Tracker {
	public $log;
	public $db;
	public $table_name = 'vtiger_tracker';
	public $history_max_viewed = 10;

	// Tracker table
	public $column_fields = array(
		"id",
		"user_id",
		"module_name",
		"item_id",
		"item_summary"
	);

	public function __construct() {
		$this->log = LoggerManager::getLogger('Tracker');
		global $adb;
		$this->db = $adb;
		$this->history_max_viewed = GlobalVariable::getVariable('Application_TrackerMaxHistory', 10);
	}

	/**
	 * Add this new item to the tracker table. If there are to many items (global config for now)
	 * then remove the oldest item. If there is more than one extra item, log an error.
	 * If the new item is the same as the most recent item then do not change the list
	 */
	public function track_view($user_id, $current_module, $item_id, $item_summary) {
		global $adb, $log, $default_charset;
		$log->info("in track view method ".$current_module);
		$this->delete_history($user_id, $item_id);
		// change the query so that it puts the tracker entry whenever you touch on the DetailView of the required entity
		// get the first name and last name from the respective modules
		if ($current_module != '') {
			$query = "select fieldname,tablename,entityidfield from vtiger_entityname where modulename = ?";
			$result = $adb->pquery($query, array($current_module));
			$fieldsname = $adb->query_result($result, 0, 'fieldname');
			$tablename = $adb->query_result($result, 0, 'tablename');
			$entityidfield = $adb->query_result($result, 0, 'entityidfield');
			if (!(strpos($fieldsname, ',') === false)) {
				// concatenate multiple fields with an whitespace between them
				$fieldlists = explode(',', $fieldsname);
				$fl = array();
				foreach ($fieldlists as $c) {
					if (count($fl)) {
						$fl[] = "' '";
					}
					$fl[] = $c;
				}
				$fieldsname = $adb->sql_concat($fl);
			}
			$query1 = "select $fieldsname as entityname from $tablename where $entityidfield = ?";
			$result = $adb->pquery($query1, array($item_id));
			$item_summary = html_entity_decode($adb->query_result($result, 0, 'entityname'), ENT_QUOTES, $default_charset);
			$item_summary = textlength_check($item_summary);
		}
		#if condition added to skip faq in last viewed history
		$query = "INSERT into $this->table_name (user_id, module_name, item_id, item_summary) values (?,?,?,?)";
		$qparams = array($user_id, $current_module, $item_id, $item_summary);
		$this->log->info('Track Item View: '.$query);
		$this->db->pquery($query, $qparams, true);
		$this->prune_history($user_id);
	}

	/**
	 * param $user_id - The id of the user to retrive the history for
	 * param $module_name - Filter the history to only return records from the specified module. If not specified all records are returned
	 * return - return the array of result set rows from the query. All of the table fields are included
	 */
	public function get_recently_viewed($user_id, $module_name = '') {
		if (empty($user_id)) {
			return;
		}
		global $current_user;

		//$query = "SELECT * from $this->table_name WHERE user_id='$user_id' ORDER BY id DESC";
		$query = "SELECT *
			from {$this->table_name}
			inner join vtiger_crmentity on vtiger_crmentity.crmid=vtiger_tracker.item_id WHERE user_id=? and vtiger_crmentity.deleted=0 ORDER BY id DESC";
		$this->log->debug("About to retrieve list: $query");
		$result = $this->db->pquery($query, array($user_id), true);
		$list = array();
		while ($row = $this->db->fetchByAssoc($result, -1, false)) {
			// If the module was not specified or the module matches the module of the row, add the row to the list
			if ($module_name == '' || $row['module_name'] == $module_name) {
				//Adding Security check
				require_once 'include/utils/utils.php';
				require_once 'include/utils/UserInfoUtil.php';
				$entity_id = $row['item_id'];
				$module = $row['module_name'];
				$per = 'no';
				if ($module == 'Users' && is_admin($current_user)) {
					$per = 'yes';
				} else {
					$per = isPermitted($module, 'DetailView', $entity_id);
				}
				if ($per == 'yes') {
					$list[] = $row;
				}
			}
		}
		return $list;
	}

	/**
	 * This method cleans out any entry for a record for a user.
	 * It is used to remove old occurances of previously viewed items.
	 */
	private function delete_history($user_id, $item_id) {
		$query = "DELETE from $this->table_name WHERE user_id=? and item_id=?";
		$this->db->pquery($query, array($user_id, $item_id), true);
	}

	/**
	 * This method cleans out any entry for a record.
	 */
	private function delete_item_history($item_id) {
		$query = "DELETE from $this->table_name WHERE item_id=?";
		$this->db->pquery($query, array($item_id), true);
	}

	/**
	 * This function will clean out old history records for this user if necessary.
	 */
	private function prune_history($user_id) {
		$this->log->debug("Enter prune_history($user_id)");
		// Check to see if the number of items in the list is now greater than the config max.
		$rs = $this->db->pquery("SELECT count(*) from {$this->table_name} WHERE user_id=?", array($user_id));
		$count = $this->db->query_result($rs, 0, 0);
		while ($count > $this->history_max_viewed) {
			// delete the last one. This assumes that entries are added one at a time > we should never add a bunch of entries
			$query = "SELECT * from $this->table_name WHERE user_id='$user_id' ORDER BY id ASC";
			$result = $this->db->limitQuery($query, 0, 1);
			$oldest_item = $this->db->fetchByAssoc($result, -1, false);
			$query = "DELETE from $this->table_name WHERE id=?";
			$result = $this->db->pquery($query, array($oldest_item['id']), true);
			$count--;
		}
	}
}
?>
