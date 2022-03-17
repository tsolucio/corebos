<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/
include_once 'vtlib/Vtiger/Event.php';
include_once 'include/Webservices/GetUpdates.php';

class ModTracker {

	/**
	 * Constant variables which indicates the status of the changed record.
	 */
	public static $UPDATED = '0';
	public static $DELETED = '1';
	public static $CREATED = '2';
	public static $RESTORED = '3';

	public $default_order_by = 'changedon';
	public $default_sort_order = 'DESC';

	public $list_fields_name = array(
		'whodid'=>'whodid',
		'prevalue'=>'prevalue',
		'postvalue'=>'postvalue',
		'fieldname'=>'fieldname',
		'changedon'=>'changedon',
	);

	// cache variable
	private static $__cache_modtracker = array();

	/* Entry point will invoke this function no need to act on */
	public function track_view($user_id, $current_module, $id = '') {
	}

	/**
	* Invoked when special actions are performed on the module.
	* @param string Module name
	* @param string Event Type
	*/
	public function vtlib_handler($moduleName, $eventType) {
		global $adb;

		$modtrackerModule = Vtiger_Module::getInstance($moduleName);
		$this->getModTrackerEnabledModules();

		if ($eventType == 'module.postinstall') {
			$adb->pquery('UPDATE vtiger_tab SET customized=0 WHERE name=?', array($moduleName));

			$fieldid = $adb->getUniqueID('vtiger_settings_field');
			$blockid = getSettingsBlockId('LBL_OTHER_SETTINGS');
			$seq_res = $adb->pquery('SELECT max(sequence) AS max_seq FROM vtiger_settings_field WHERE blockid = ?', array($blockid));
			if ($adb->num_rows($seq_res) > 0) {
				$cur_seq = $adb->query_result($seq_res, 0, 'max_seq');
				if ($cur_seq != null) {
					$seq = $cur_seq + 1;
				}
			}
			$mturl = 'index.php?module=ModTracker&action=BasicSettings&formodule=ModTracker';
			$adb->pquery(
				'INSERT INTO vtiger_settings_field(fieldid, blockid, name, iconpath, description, linkto, sequence) VALUES (?,?,?,?,?,?,?)',
				array($fieldid, $blockid, 'ModTracker', 'set-IcoLoginHistory.gif', 'LBL_MODTRACKER_DESCRIPTION', $mturl, $seq)
			);
		} elseif ($eventType == 'module.disabled') {
			$em = new VTEventsManager($adb);
			$em->setHandlerInActive('ModTrackerHandler');
			// De-register Common Javascript
			$modtrackerModule->deleteLink('HEADERSCRIPT', 'ModTrackerCommon_JS');
		} elseif ($eventType == 'module.enabled') {
			$em = new VTEventsManager($adb);
			$em->setHandlerActive('ModTrackerHandler');
			// Register Common Javascript
			$modtrackerModule->addLink('HEADERSCRIPT', 'ModTrackerCommon_JS', 'modules/ModTracker/ModTrackerCommon.js');
		} elseif ($eventType == 'module.preuninstall') {
			// Handle actions when this module is about to be deleted.
		} elseif ($eventType == 'module.preupdate') {
			// Handle actions before this module is updated.
		} elseif ($eventType == 'module.postupdate') {
			// Handle actions after this module is updated.
		}
	}

	/**
	 * function gives an array of module names for which modtracking is enabled
	*/
	public function getModTrackerEnabledModules() {
		global $adb;
		$modules = array();
		$moduleResult = $adb->pquery('SELECT * FROM vtiger_modtracker_tabs', array());
		for ($i=0; $i<$adb->num_rows($moduleResult); $i++) {
			$tabId = $adb->query_result($moduleResult, $i, 'tabid');
			$visible = $adb->query_result($moduleResult, $i, 'visible');
			self::updateCache($tabId, $visible);
			if ($visible == 1) {
				$modules[] = getTabModuleName($tabId);
			}
		}
		return $modules;
	}

	/**
	 *Invoked to disable tracking for the module.
	 * @param Integer $tabid
	 */
	public static function disableTrackingForModule($tabid) {
		global $adb;
		if (!self::isModulePresent($tabid)) {
			$adb->pquery('INSERT INTO vtiger_modtracker_tabs VALUES(?,?)', array($tabid, 0));
		} else {
			$adb->pquery('UPDATE vtiger_modtracker_tabs SET visible = 0 WHERE tabid = ?', array($tabid));
		}
		self::updateCache($tabid, 0);
		if (self::isModtrackerLinkPresent($tabid)) {
			$moduleInstance=Vtiger_Module::getInstance($tabid);
			$moduleInstance->deleteLink('DETAILVIEWBASIC', 'View History');
		}
	}

	/**
	 *Invoked to enable tracking for the module.
	 * @param Integer $tabid
	 */
	public static function enableTrackingForModule($tabid) {
		global $adb;
		if (!self::isModulePresent($tabid)) {
			$adb->pquery('INSERT INTO vtiger_modtracker_tabs VALUES(?,?)', array($tabid,1));
		} else {
			$adb->pquery('UPDATE vtiger_modtracker_tabs SET visible = 1 WHERE tabid = ?', array($tabid));
		}
		self::updateCache($tabid, 1);
		if (!self::isModTrackerLinkPresent($tabid)) {
			$moduleInstance=Vtiger_Module::getInstance($tabid);
			$moduleInstance->addLink(
				'DETAILVIEWBASIC',
				'View History',
				"javascript:ModTrackerCommon.showhistory('\$RECORD\$')",
				'',
				0,
				array('path'=>'modules/ModTracker/ModTracker.php','class'=>'ModTracker','method'=>'isViewPermitted')
			);
		}
	}

	/**
	 *Invoked to check if tracking is enabled or disabled for the module.
	 * @param string $modulename
	 */
	public static function isTrackingEnabledForModule($modulename) {
		global $adb;
		$tabid = getTabid($modulename);
		if (!self::getVisibilityForModule($tabid)) {
			$query = $adb->pquery('SELECT * FROM vtiger_modtracker_tabs WHERE vtiger_modtracker_tabs.visible=1 AND vtiger_modtracker_tabs.tabid=?', array($tabid));
			$rows = $adb->num_rows($query);
			$tabid=$adb->query_result($query, 0, 'tabid');
			$visible=$adb->query_result($query, 0, 'visible');
			if ($rows<1) {
				self::updateCache($tabid, $visible);
				return false;
			} else {
				self::updateCache($tabid, $visible);
				return true;
			}
		} else {
			return true;
		}
	}

	/**
	 *Invoked to check if the module is present in the table or not.
	 * @param Integer $tabid
	 */
	public static function isModulePresent($tabid) {
		global $adb;
		if (!self::checkModuleInModTrackerCache($tabid)) {
			$query=$adb->pquery('SELECT * FROM vtiger_modtracker_tabs WHERE tabid = ?', array($tabid));
			$rows = $adb->num_rows($query);
			if ($rows) {
				$tabid=$adb->query_result($query, 0, 'tabid');
				$visible=$adb->query_result($query, 0, 'visible');
				self::updateCache($tabid, $visible);
				return true;
			} else {
				return false;
			}
		} else {
			return true;
		}
	}

	/**
	 *Invoked to check if ModTracker links are enabled for the module.
	 * @param Integer $tabid
	 */
	public static function isModtrackerLinkPresent($tabid) {
		global $adb;
		$module_name = getTabModuleName($tabid);
		$crmEntityTable = CRMEntity::getcrmEntityTableAlias('BusinessActions');
		$rs=$adb->pquery(
			"SELECT businessactionsid
			FROM vtiger_businessactions INNER JOIN '.$crmEntityTable.' ON vtiger_businessactions.businessactionsid = vtiger_crmentity.crmid
			WHERE deleted = 0 AND elementtype_action='DETAILVIEWBASIC' AND linklabel = 'View History'
				AND (module_list = ? OR module_list LIKE ? OR module_list LIKE ? OR module_list LIKE ?)",
			array($module_name, $module_name.' %', '% '.$module_name.' %', '% '.$module_name,)
		);

		return ($adb->num_rows($rs)>=1);
	}

	/**
	 *Invoked to update cache.
	 * @param Integer $tabid
	 * @param Boolean $visible
	 */
	public static function updateCache($tabid, $visible) {
		self::$__cache_modtracker[$tabid] = array(
			'tabid'   => $tabid,
			'visible' => $visible
		);
	}

	/**
	 *Invoked to check the ModTracker cache.
	 * @param Integer $tabid
	 */
	public static function checkModuleInModTrackerCache($tabid) {
		return isset(self::$__cache_modtracker[$tabid]);
	}

	/**
	 *Invoked to fetch the visibility for the module from the cache.
	 * @param Integer $tabid
	 */
	public static function getVisibilityForModule($tabid) {
		if (isset(self::$__cache_modtracker[$tabid])) {
			return self::$__cache_modtracker[$tabid]['visible'];
		}
		return false;
	}

	/**
	 * Get the list of changed records after an internal pointer and a given datetime, optionally limiting the results
	 * @param int $uniqueId
	 * @param int $mtime
	 * @param int $limit
	 * @return array list of created,updated and deleted records and some additional control information
	 */
	public function getChangedRecords($uniqueId, $mtime, $limit = 100) {
		global $adb;
		$datetime = date('Y-m-d H:i:s', $mtime);
		$accessibleModules = $this->getModTrackerEnabledModules();

		if (empty($accessibleModules)) {
			throw new BadMethodCallException('Modtracker not enabled for any modules');
		}

		$query = 'SELECT id, module, modifiedtime, vtiger_crmobject.crmid, smownerid, vtiger_modtracker_basic.status
			FROM vtiger_modtracker_basic
			INNER JOIN vtiger_crmobject ON vtiger_modtracker_basic.crmid = vtiger_crmobject.crmid
				AND vtiger_modtracker_basic.changedon = vtiger_crmobject.modifiedtime
			WHERE id > ? AND changedon >= ? AND module IN ('.generateQuestionMarks($accessibleModules).') ORDER BY id';

		$params = array($uniqueId, $datetime);
		foreach ($accessibleModules as $entityModule) {
			$params[] = $entityModule;
		}

		if ($limit) {
			$query .=" LIMIT $limit";
		}

		$result = $adb->pquery($query, $params);

		$modTime = array();
		$createdRecords = array();
		$updatedRecords = array();
		$deletedRecords = array();
		$rows = $adb->num_rows($result);
		for ($i=0; $i<$rows; $i++) {
			$status = $adb->query_result($result, $i, 'status');

			$record['uniqueid']     = $adb->query_result($result, $i, 'id');
			$record['modifiedtime'] = $adb->query_result($result, $i, 'modifiedtime');
			$record['module']       = $adb->query_result($result, $i, 'module');
			$record['crmid']        = $adb->query_result($result, $i, 'crmid');
			$record['assigneduserid'] = $adb->query_result($result, $i, 'smownerid');

			if ($status == ModTracker::$DELETED) {
				$deletedRecords[] = $record;
			} elseif ($status == ModTracker::$CREATED) {
				$createdRecords[] = $record;
			} elseif ($status == ModTracker::$UPDATED) {
				$updatedRecords[] = $record;
			}

			$modTime[]   = $record['modifiedtime'];
			$uniqueIds[] = $record['uniqueid'];
		}

		if (!empty($uniqueIds)) {
			$maxUniqueId = max($uniqueIds);
		}

		if (empty($maxUniqueId)) {
			$maxUniqueId = $uniqueId;
		}

		if (!empty($modTime)) {
			$maxModifiedTime = max($modTime);
		}
		if (empty($maxModifiedTime)) {
			$maxModifiedTime = $datetime;
		}

		$output['created'] = $createdRecords;
		$output['updated'] = $updatedRecords;
		$output['deleted'] = $deletedRecords;

		$moreQuery = 'SELECT count(*) FROM vtiger_modtracker_basic WHERE id>? AND changedon>=? AND module IN ('.generateQuestionMarks($accessibleModules).')';
		$param = array($maxUniqueId, $maxModifiedTime);
		foreach ($accessibleModules as $entityModule) {
			$param[] = $entityModule;
		}
		$result = $adb->pquery($moreQuery, $param);
		$output['more'] = ($adb->query_result($result, 0, 0)>0);

		$output['uniqueid'] = $maxUniqueId;

		if (!$maxModifiedTime) {
			$modifiedtime = $mtime;
		} else {
			$modifiedtime = vtws_getSeconds($maxModifiedTime);
		}
		if (is_string($modifiedtime)) {
			$modifiedtime = (int)$modifiedtime;
		}
		$output['lastModifiedTime'] = $modifiedtime;

		return $output;
	}

	/** get all the changes that have happened on a record from a given date
	 * @param int crmid of record that we want the changes for
	 * @param string ISO formatted date and time from which we want the changes
	 * @return array of all field changes of the record indexed per date of the change
	*/
	public static function getRecordFieldChanges($crmid, $time) {
		global $adb;
		$fieldResult = $adb->pquery(
			'SELECT *
			FROM vtiger_modtracker_detail
			INNER JOIN vtiger_modtracker_basic ON vtiger_modtracker_basic.id=vtiger_modtracker_detail.id
			WHERE crmid=? AND changedon>=?',
			array($crmid, $time)
		);
		$fields = array();
		while ($row=$adb->fetch_array($fieldResult)) {
			$fieldName = $row['fieldname'];
			if ($fieldName == 'record_id' || $fieldName == 'record_module' || $fieldName == 'createdtime' || $fieldName == 'cbuuid') {
				continue;
			}
			$field['postvalue'] = $row['postvalue'];
			$field['prevalue'] = $row['prevalue'];
			$fields[$row['changedon']][$fieldName] = $field;
		}
		return $fields;
	}

	/** get all the changes that have happened on a field in a record
	 * @param int crmid of record that we want the changes for
	 * @param string field name to retrieve history of
	 * @return array of all field changes of the record indexed per date of the change
	*/
	public static function getRecordFieldHistory($crmid, $field) {
		global $adb;
		$changes = ModTracker::getRecordFieldChanges($crmid, '1970-01-01 00:00');
		$ret = array();
		foreach ($changes as $change) {
			if (isset($change[$field])) {
				$ret[] = array(
					'from' => $change[$field]['prevalue'],
					'to' => $change[$field]['postvalue'],
				);
			}
		}
		return $ret;
	}

	public static function isViewPermitted($linkData) {
		$moduleName = $linkData->getModule();
		$recordId = $linkData->getInputParameter('record');
		return isPermitted($moduleName, 'DetailView', $recordId) == 'yes';
	}

	public function getModTrackerJSON($crmid, $page, $order_by = 'changedon', $sorder = 'DESC') {
		global $log, $adb, $currentModule;
		$log->debug('> getModTrackerJSON');

		$where = '';
		$params = array();
		if (!empty($crmid)) {
			$where .= 'where crmid=?';
			array_push($params, $crmid);
		}

		$list_query = "select * from vtiger_modtracker_basic join vtiger_modtracker_detail on vtiger_modtracker_basic.id=vtiger_modtracker_detail.id $where order by ";
		if ($sorder != '' && $order_by != '') {
			$list_query .= $order_by.' '.$sorder;
		} else {
			$list_query .= $this->default_order_by.' '.$this->default_sort_order;
		}
		if (!empty($_REQUEST['perPage']) && is_numeric($_REQUEST['perPage'])) {
			$rowsperpage = (int) vtlib_purify($_REQUEST['perPage']);
		} else {
			$rowsperpage = GlobalVariable::getVariable('Report_ListView_PageSize', 20);
		}
		$from = ($page-1)*$rowsperpage;
		$limit = " limit $from,$rowsperpage";
		$result = $adb->pquery($list_query.$limit, $params);
		$count_result = $adb->query(mkCountQuery($adb->convert2sql($list_query, $params)));
		$noofrows = $adb->query_result($count_result, 0, 0);
		if ($result) {
			if ($noofrows>0) {
				$entries_list = array(
					'data' => array(
						'contents' => array(),
						'pagination' => array(
							'page' => $page,
							'totalCount' => $noofrows,
						),
					),
					'result' => true,
				);
				$unames = array();
				while ($lgn = $adb->fetch_array($result)) {
					$entry = array();
					if (!isset($unames[$lgn['whodid']])) {
						$unames[$lgn['whodid']] = getUserFullName($lgn['whodid']);
					}
					$entry['whodid'] = $unames[$lgn['whodid']];
					$entry['changedon'] = $lgn['changedon'];
					$entry['fieldname'] = $lgn['fieldname'];
					$entry['prevalue'] = $lgn['prevalue'];
					$entry['postvalue'] = $lgn['postvalue'];
					$entries_list['data']['contents'][] = $entry;
				}
			} else {
				$entries_list = array(
					'data' => array(
						'contents' => array(),
						'pagination' => array(
							'page' => 1,
							'totalCount' => 0,
						),
					),
					'result' => false,
					'message' => getTranslatedString('NoData', 'ModTracker'),
				);
			}
		} else {
			$entries_list = array(
				'data' => array(
					'contents' => array(),
					'pagination' => array(
						'page' => 1,
						'totalCount' => 0,
					),
				),
				'result' => false,
				'message' => getTranslatedString('ERR_SQL', 'ModTracker'),
				'debug_query' => $list_query.$limit,
				'debug_params' => json_encode($params),
			);
		}
		$log->debug('< getModTrackerJSON');
		return json_encode($entries_list);
	}
}
?>