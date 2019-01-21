<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/
include_once 'vtlib/Vtiger/Utils.php';
include_once 'vtlib/Vtiger/Utils/StringTemplate.php';
include_once 'vtlib/Vtiger/LinkData.php';
include_once 'modules/BusinessActions/BusinessActions.php';

/**
 * Provides API to handle custom links
 * @package vtlib
 */
class Vtiger_Link {
	public $tabid;
	public $linkid;
	public $linktype;
	public $linklabel;
	public $linkurl;
	public $linkicon;
	public $sequence;
	public $status = false;
	public $handler_path;
	public $handler_class;
	public $handler;
	public $onlyonmymodule = false;

	// Ignore module while selection
	const IGNORE_MODULE = -1;

	/**
	 * Constructor
	 */
	public function __construct() {
	}

	/**
	 * Initialize this instance.
	 */
	public function initialize($valuemap) {
		$this->tabid          = $valuemap['tabid'];
		$this->linkid         = $valuemap['linkid'];
		$this->linktype       = $valuemap['linktype'];
		$this->linklabel      = $valuemap['linklabel'];
		$this->linkurl        = decode_html($valuemap['linkurl']);
		$this->linkicon       = decode_html($valuemap['linkicon']);
		$this->sequence       = $valuemap['sequence'];
		$this->status         = (isset($valuemap['status']) ? $valuemap['status'] : false);
		$this->handler_path   = $valuemap['handler_path'];
		$this->handler_class  = $valuemap['handler_class'];
		$this->handler        = $valuemap['handler'];
		$this->onlyonmymodule = $valuemap['onlyonmymodule'];
	}

	/**
	 * Get module name.
	 */
	public function module() {
		if (!empty($this->tabid)) {
			return getTabModuleName($this->tabid);
		}
		return false;
	}

	/**
	 * Get unique id for the insertion
	 */
	public static function __getUniqueId() {
		global $adb;
		return $adb->getUniqueID('vtiger_links');
	}

	/** Cache (Record) the schema changes to improve performance */
	private static $__cacheSchemaChanges = array();

	/**
	 * Initialize the schema (tables)
	 */
	private static function __initSchema() {
		if (empty(self::$__cacheSchemaChanges['vtiger_links'])) {
			if (!Vtiger_Utils::CheckTable('vtiger_links')) {
				Vtiger_Utils::CreateTable(
					'vtiger_links',
					'(linkid INT NOT NULL PRIMARY KEY,
					tabid INT, linktype VARCHAR(20), linklabel VARCHAR(30), linkurl VARCHAR(255), linkicon VARCHAR(100), sequence INT, status INT(1) NOT NULL DEFAULT 1)',
					true
				);
				Vtiger_Utils::ExecuteQuery(
					'CREATE INDEX link_tabidtype_idx on vtiger_links(tabid,linktype)'
				);
			}
			self::$__cacheSchemaChanges['vtiger_links'] = true;
		}
		global $adb;
		$lns=$adb->getColumnNames('vtiger_links');
		if (!in_array('onlyonmymodule', $lns)) {
			$adb->query('ALTER TABLE `vtiger_links` ADD `onlyonmymodule` BOOLEAN NOT NULL DEFAULT FALSE');
		}
	}

	/**
	 * Add link given module
	 * @param Integer Module ID
	 * @param String Link Type (like DETAILVIEW). Useful for grouping based on pages.
	 * @param String Label to display
	 * @param String HREF value or URL to use for the link
	 * @param String ICON to use on the display
	 * @param Integer Order or sequence of displaying the link
	 */
	public static function addLink($tabid, $type, $label, $url, $iconpath = '', $sequence = 0, $handlerInfo = null, $onlyonmymodule = false) {

		if (self::isBusinessActionCompatible()) {
			BusinessActions::addLink($tabid, $type, $label, $url, $iconpath, $sequence, $handlerInfo, $onlyonmymodule);
		} else {
			global $adb;
			self::__initSchema();
			$checkres = $adb->pquery(
				'SELECT linkid FROM vtiger_links WHERE tabid=? AND linktype=? AND linkurl=? AND linkicon=? AND linklabel=?',
				array($tabid, $type, $url, $iconpath, $label)
			);
			if (!$adb->num_rows($checkres)) {
				$uniqueid = self::__getUniqueId();
				$sql = 'INSERT INTO vtiger_links (linkid,tabid,linktype,linklabel,linkurl,linkicon,sequence';
				$params = array($uniqueid, $tabid, $type, $label, $url, $iconpath, (int)$sequence);
				if (!empty($handlerInfo)) {
					$sql .= (', handler_path, handler_class, handler');
					$params[] = (isset($handlerInfo['path']) ? $handlerInfo['path'] : '');
					$params[] = (isset($handlerInfo['class']) ? $handlerInfo['class'] : '');
					$params[] = (isset($handlerInfo['method']) ? $handlerInfo['method'] : '');
				}
				$params[] = $onlyonmymodule;
				$sql .= (', onlyonmymodule) VALUES ('.generateQuestionMarks($params).')');
				$adb->pquery($sql, $params);
				self::log("Adding Link ($type - $label) ... DONE");
			}
		}
	}

	/**
	 * Delete link of the module
	 * @param Integer Module ID
	 * @param String Link Type (like DETAILVIEW). Useful for grouping based on pages.
	 * @param String Display label
	 * @param String URL of link to lookup while deleting
	 */
	public static function deleteLink($tabid, $type, $label, $url = false) {

		if (self::isBusinessActionCompatible()) {
			BusinessActions::deleteLink($tabid, $type, $label, $url);
		} else {
			global $adb;
			self::__initSchema();
			if ($url) {
				$adb->pquery(
					'DELETE FROM vtiger_links WHERE tabid=? AND linktype=? AND linklabel=? AND linkurl=?',
					array($tabid, $type, $label, $url)
				);
				self::log("Deleting Link ($type - $label - $url) ... DONE");
			} else {
				$adb->pquery(
					'DELETE FROM vtiger_links WHERE tabid=? AND linktype=? AND linklabel=?',
					array($tabid, $type, $label)
				);
				self::log("Deleting Link ($type - $label) ... DONE");
			}
		}
	}

	/**
	 * Delete all links related to module
	 * @param Integer Module ID.
	 */
	public static function deleteAll($tabid) {

		if (self::isBusinessActionCompatible()) {
			BusinessActions::deleteAll($tabid);
		} else {
			global $adb;
			self::__initSchema();
			$adb->pquery('DELETE FROM vtiger_links WHERE tabid=?', array($tabid));
			self::log('Deleting Links ... DONE');
		}
	}

	/**
	 * Get all the links related to module
	 * @param Integer Module ID.
	 */
	public static function getAll($tabid) {
		return self::getAllByType($tabid);
	}

	/**
	 * Get all the link related to module based on type
	 * @param Integer Module ID
	 * @param mixed String or List of types to select
	 * @param Map Key-Value pair to use for formating the link url
	 */
	public static function getAllByType($tabid, $type = false, $parameters = false, $userid = null, $recordid = null) {

		if (self::isBusinessActionCompatible()) {
			return BusinessActions::getAllByType($tabid, $type, $parameters, $userid, $recordid);
		} else {
			global $adb, $current_user, $currentModule;
			self::__initSchema();

			$multitype = false;
			$orderby = ' order by linktype,sequence'; //MSL
			if ($type) {
				// Multiple link type selection?
				if (is_array($type)) {
					$multitype = true;
					if ($tabid === self::IGNORE_MODULE) {
						$sql = 'SELECT * FROM vtiger_links WHERE linktype IN ('.
							Vtiger_Utils::implodestr('?', count($type), ',') .') ';
						$params = $type;
						$permittedTabIdList = getPermittedModuleIdList();
						if (count($permittedTabIdList) > 0 && $current_user->is_admin !== 'on') {
							$sql .= ' and tabid IN ('.
								Vtiger_Utils::implodestr('?', count($permittedTabIdList), ',').')';
							$params[] = $permittedTabIdList;
						}
						if (!empty($currentModule)) {
							$sql .= ' and ((onlyonmymodule and tabid=?) or !onlyonmymodule) ';
							$params[] = getTabid($currentModule);
						}
						$result = $adb->pquery($sql . $orderby, array($adb->flatten_array($params)));
					} else {
						$result = $adb->pquery(
							'SELECT * FROM vtiger_links WHERE tabid=? AND linktype IN ('.
							Vtiger_Utils::implodestr('?', count($type), ',') .')' . $orderby,
							array($tabid, $adb->flatten_array($type))
						);
					}
				} else {
					// Single link type selection
					if ($tabid === self::IGNORE_MODULE) {
						$result = $adb->pquery('SELECT * FROM vtiger_links WHERE linktype=?' . $orderby, array($type));
					} else {
						$result = $adb->pquery('SELECT * FROM vtiger_links WHERE tabid=? AND linktype=?' . $orderby, array($tabid, $type));
					}
				}
			} else {
				$result = $adb->pquery('SELECT * FROM vtiger_links WHERE tabid=?' . $orderby, array($tabid));
			}

			$strtemplate = new Vtiger_StringTemplate();
			if ($parameters) {
				foreach ($parameters as $key => $value) {
					$strtemplate->assign($key, $value);
				}
			}

			$instances = array();
			if ($multitype) {
				foreach ($type as $t) {
					$instances[$t] = array();
				}
			}

			while ($row = $adb->fetch_array($result)) {
				/** Should the widget be shown */
				$return = cbEventHandler::do_filter('corebos.filter.link.show', array($row, $type, $parameters));
				if ($return == false) {
					continue;
				}
				$instance = new self();
				$instance->initialize($row);
				if (!empty($row['handler_path']) && isInsideApplication($row['handler_path'])) {
					checkFileAccessForInclusion($row['handler_path']);
					require_once $row['handler_path'];
					$linkData = new Vtiger_LinkData($instance, $current_user);
					$ignore = call_user_func(array($row['handler_class'], $row['handler']), $linkData);
					if (!$ignore) {
						self::log("Ignoring Link ... ".var_export($row, true));
						continue;
					}
				}
				if ($parameters) {
					$instance->linkurl = $strtemplate->merge($instance->linkurl);
					$instance->linkicon= $strtemplate->merge($instance->linkicon);
				}
				if ($multitype) {
					$instances[$instance->linktype][] = $instance;
				} else {
					$instances[] = $instance;
				}
			}
			return $instances;
		}
	}

	/**
	 * Helper function to log messages
	 * @param String Message to log
	 * @param Boolean true appends linebreak, false to avoid it
	 * @access private
	 */
	private static function log($message, $delimit = true) {
		Vtiger_Utils::Log($message, $delimit);
	}

	/**
	 * Checks whether the user is admin or not
	 * @param Vtiger_LinkData $linkData
	 * @return Boolean
	 */
	public static function isAdmin($linkData) {
		$user = $linkData->getUser();
		return $user->is_admin == 'on' || $user->column_fields['is_admin'] == 'on';
	}

	public static function updateLink($tabId, $linkId, $linkInfo = array()) {

		if (self::isBusinessActionCompatible()) {
			BusinessActions::updateLink($tabId, $linkId, $linkInfo);
		} else {
			if ($linkInfo && is_array($linkInfo)) {
				$db = PearDatabase::getInstance();
				$result = $db->pquery('SELECT 1 FROM vtiger_links WHERE tabid=? AND linkid=?', array($tabId, $linkId));
				if ($db->num_rows($result)) {
					$columnsList = $db->getColumnNames('vtiger_links');
					$isColumnUpdate = false;

					$sql = 'UPDATE vtiger_links SET ';
					foreach ($linkInfo as $column => $columnValue) {
						if (in_array($column, $columnsList)) {
							$columnValue = ($column == 'sequence') ? intval($columnValue) : $columnValue;
							$sql .= "$column='$columnValue',";
							$isColumnUpdate = true;
						}
					}

					if ($isColumnUpdate) {
						$sql = trim($sql, ',').' WHERE tabid=? AND linkid=?';
						$db->pquery($sql, array($tabId, $linkId));
					}
				}
			}
		}
	}

	public static function isBusinessActionCompatible() {

		$db = PearDatabase::getInstance();
		$compatibility_check = $db->pquery("SELECT cbupdaterid 
												  FROM vtiger_cbupdater
											     WHERE classname = ?
											       AND pathfilename = ?
							   					   AND execstate = ?", array('migrateLinksIntoBusinessActionEntities', 'build/changeSets/2018/migrateLinksIntoBusinessActionEntities.php', 'Executed'));

		return ($db->num_rows($compatibility_check) > 0) ? true : false;
	}
}
?>
