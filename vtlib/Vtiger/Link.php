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
		BusinessActions::addLink($tabid, $type, $label, $url, $iconpath, $sequence, $handlerInfo, $onlyonmymodule);
	}

	/**
	 * Delete link of the module
	 * @param Integer Module ID
	 * @param String Link Type (like DETAILVIEW). Useful for grouping based on pages.
	 * @param String Display label
	 * @param String URL of link to lookup while deleting
	 */
	public static function deleteLink($tabid, $type, $label, $url = false) {
		BusinessActions::deleteLink($tabid, $type, $label, $url);
	}

	/**
	 * Delete all links related to module
	 * @param Integer Module ID.
	 */
	public static function deleteAll($tabid) {
		BusinessActions::deleteAll($tabid);
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
		return BusinessActions::getAllByType($tabid, $type, $parameters, $userid, $recordid);
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
		BusinessActions::updateLink($tabId, $linkId, $linkInfo);
	}
}
?>
