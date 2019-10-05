<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/
include_once 'vtlib/Vtiger/ModuleBasic.php';

/**
 * Provides API to work with vtiger CRM Modules
 * @package vtlib
 */
class Vtiger_Module extends Vtiger_ModuleBasic {

	/**
	 * Function to get the Module/Tab id
	 * @return <Number>
	 */
	public function getId() {
		return $this->id;
	}

	/**
	 * Get unique id for related list
	 * @access private
	 */
	private function __getRelatedListUniqueId() {
		global $adb;
		return $adb->getUniqueID('vtiger_relatedlists');
	}

	/**
	 * Get related list sequence to use
	 * @access private
	 */
	private function __getNextRelatedListSequence() {
		global $adb;
		$max_sequence = 0;
		$result = $adb->pquery('SELECT max(sequence) as maxsequence FROM vtiger_relatedlists WHERE tabid=?', array($this->id));
		if ($adb->num_rows($result)) {
			$max_sequence = $adb->query_result($result, 0, 'maxsequence');
		}
		return ++$max_sequence;
	}

	/**
	 * Set related list information between other module
	 * @param Vtiger_Module Instance of target module with which relation should be setup
	 * @param String Label to display in related list (default is target module name)
	 * @param Array List of action button to show ('ADD', 'SELECT')
	 * @param String Callback function name of this module to use as handler
	 *
	 * @internal Creates table vtiger_crmentityrel if it does not exists
	 */
	public function setRelatedList($moduleInstance, $label = '', $actions = false, $function_name = 'get_related_list', $fieldId = null, $relationtype = null) {
		global $adb;

		if (empty($moduleInstance)) {
			return;
		}

		if (!Vtiger_Utils::CheckTable('vtiger_crmentityrel')) {
			Vtiger_Utils::CreateTable(
				'vtiger_crmentityrel',
				'(crmid INT NOT NULL, module VARCHAR(100) NOT NULL, relcrmid INT NOT NULL, relmodule VARCHAR(100) NOT NULL)',
				true
			);
		}

		$relation_id = $this->__getRelatedListUniqueId();
		$sequence = $this->__getNextRelatedListSequence();
		$presence = 0; // 0 - Enabled, 1 - Disabled

		if (empty($label)) {
			$label = $moduleInstance->name;
		}

		// Allow ADD action of other module records (default)
		if ($actions === false) {
			$actions = array('ADD');
		}

		$useactions_text = $actions;
		if (is_array($actions)) {
			$useactions_text = implode(',', $actions);
		}
		$useactions_text = strtoupper($useactions_text);

		Vtiger_Utils::AddColumn('vtiger_relatedlists', 'actions', 'VARCHAR(50)');
		Vtiger_Utils::AddColumn('vtiger_relatedlists', 'relationfieldid', 'INT(11) DEFAULT NULL');
		Vtiger_Utils::AddColumn('vtiger_relatedlists', 'relationtype', 'VARCHAR(3) DEFAULT NULL');
		$rs = $adb->pquery(
			'SELECT count(*) from vtiger_relatedlists where tabid=? and related_tabid=? and name=? and label=?',
			array($this->id,$moduleInstance->id,$function_name,$label)
		);
		$relchk = $adb->query_result($rs, 0, 0);
		if ($relchk == 0) {
			if (is_null($fieldId)) {
				$relatedModuleReferenceFields = $moduleInstance->getFieldsByType('reference');
				foreach ($relatedModuleReferenceFields as $fieldModel) {
					$referenceList = $fieldModel->getReferenceList(false);
					if (in_array($this->name, $referenceList)) {
						$fieldId = $fieldModel->id;
						break;
					}
				}
			}
			if (is_null($relationtype)) {
				if ($function_name == 'get_dependents_list' || $function_name == 'get_activities') {
					$relationtype = '1:N';
				} elseif ($function_name == 'get_related_list' || $function_name == 'get_attachments') {
					$relationtype = 'N:N';
				}
			}
			$adb->pquery(
				'INSERT INTO vtiger_relatedlists
					(relation_id,tabid,related_tabid,name,sequence,label,presence,actions,relationfieldid,relationtype) VALUES (?,?,?,?,?,?,?,?,?,?)',
				array($relation_id,$this->id,$moduleInstance->id,$function_name,$sequence,$label,$presence,$useactions_text,$fieldId,$relationtype)
			);
		}
		self::log("Setting relation with $moduleInstance->name [$useactions_text] ... DONE");
	}

	/**
	 * Unset related list information that exists with other module
	 * @param Vtiger_Module Instance of target module with which relation should be setup
	 * @param String Label to display in related list (default is target module name)
	 * @param String Callback function name of this module to use as handler
	 */
	public function unsetRelatedList($moduleInstance, $label = '', $function_name = 'get_related_list') {
		global $adb;

		if (empty($moduleInstance)) {
			return;
		}

		if (empty($label)) {
			$label = $moduleInstance->name;
		}

		$adb->pquery(
			'DELETE FROM vtiger_relatedlists WHERE tabid=? AND related_tabid=? AND name=? AND label=?',
			array($this->id, $moduleInstance->id, $function_name, $label)
		);

		self::log("Unsetting relation with $moduleInstance->name ... DONE");
	}

	public function unsetRelatedListForField($fieldId) {
		$db = PearDatabase::getInstance();
		$db->pquery('DELETE FROM vtiger_relatedlists WHERE relationfieldid=?', array($fieldId));
	}

	/**
	 * Add custom link for a module page
	 * @param String Type can be like 'DETAILVIEW', 'LISTVIEW' etc..
	  * @param String Label to use for display
	 * @param String HREF value to use for generated link
	 * @param String Path to the image file (relative or absolute)
	 * @param Integer Sequence of appearance
	 *
	 * NOTE: $url can have variables like $MODULE (module for which link is associated),
	 * $RECORD (record on which link is dispalyed)
	 */
	public function addLink($type, $label, $url, $iconpath = '', $sequence = 0, $handlerInfo = null, $onlyonmymodule = false) {
		Vtiger_Link::addLink($this->id, $type, $label, $url, $iconpath, $sequence, $handlerInfo, $onlyonmymodule);
	}

	/**
	 * Delete custom link of a module
	 * @param String Type can be like 'DETAILVIEW', 'LISTVIEW' etc..
	  * @param String Display label to lookup
	 * @param String URL value to lookup
	 */
	public function deleteLink($type, $label, $url = false) {
		Vtiger_Link::deleteLink($this->id, $type, $label, $url);
	}

	/**
	 * Get all the custom links related to this module.
	 */
	public function getLinks() {
		return Vtiger_Link::getAll($this->id);
	}

	/**
	 * Initialize webservice setup for this module instance.
	 */
	public function initWebservice() {
		Vtiger_Webservice::initialize($this);
	}

	/**
	 * De-Initialize webservice setup for this module instance.
	 */
	public function deinitWebservice() {
		Vtiger_Webservice::uninitialize($this);
	}

	/**
	 * Get instance by id or name
	 * @param mixed id or name of the module
	 */
	public static function getInstance($value) {
		global $adb;
		$instance = false;

		$query = false;
		if (Vtiger_Utils::isNumber($value)) {
			$query = 'SELECT * FROM vtiger_tab WHERE tabid=?';
		} else {
			$query = 'SELECT * FROM vtiger_tab WHERE name=?';
		}
		$result = $adb->pquery($query, array($value));
		if ($adb->num_rows($result)) {
			$instance = new self();
			$instance->initialize($adb->fetch_array($result));
		}
		return $instance;
	}

	/**
	 * Get instance of the module class.
	 * @param String Module name
	 */
	public static function getClassInstance($modulename) {
		if ($modulename == 'Calendar') {
			$modulename = 'Activity';
		}

		$instance = false;
		$filepath = "modules/$modulename/$modulename.php";
		if (Vtiger_Utils::checkFileAccessForInclusion($filepath, false)) {
			include_once $filepath;
			if (class_exists($modulename)) {
				$instance = new $modulename();
			}
		}
		return $instance;
	}

	/**
	 * Fire the event for the module (if vtlib_handler is defined)
	 */
	public static function fireEvent($modulename, $event_type) {
		$instance = self::getClassInstance((string)$modulename);
		if ($instance) {
			if (method_exists($instance, 'vtlib_handler')) {
				self::log("Invoking vtlib_handler for $event_type ...START");
				$instance->vtlib_handler((string)$modulename, (string)$event_type);
				self::log("Invoking vtlib_handler for $event_type ...DONE");
			}
		}
	}
}
?>