<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/
include_once('vtlib/Vtiger/Utils.php');
include_once('vtlib/Vtiger/FieldBasic.php');

/**
 * Provides APIs to control vtiger CRM Field
 * @package vtlib
 */
class Vtiger_Field extends Vtiger_FieldBasic {

	/**
	 * Get unique picklist id to use
	 * @access private
	 */
	function __getPicklistUniqueId() {
		global $adb;
		return $adb->getUniqueID('vtiger_picklist');
	}

	/**
	 * Set values for picklist field (for all the roles)
	 * @param Array List of values to add.
	 *
	 * @internal Creates picklist base if it does not exists
	 */
	function setPicklistValues($values) {
		global $adb,$default_charset;

		// Non-Role based picklist values
		if($this->uitype == '16') {
			$this->setNoRolePicklistValues($values);
			return;
		}

		$picklist_table = 'vtiger_'.$this->name;
		$picklist_idcol = $this->name.'id';
		if(!Vtiger_Utils::CheckTable($picklist_table)) {
			Vtiger_Utils::CreateTable(
				$picklist_table,
				"($picklist_idcol INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
				$this->name VARCHAR(200) NOT NULL,
				presence INT (1) NOT NULL DEFAULT 1,
				picklist_valueid INT NOT NULL DEFAULT 0)",
				true);
			$new_picklistid = $this->__getPicklistUniqueId();
			$adb->pquery("INSERT INTO vtiger_picklist (picklistid,name) VALUES(?,?)",Array($new_picklistid, $this->name));
			self::log("Creating table $picklist_table ... DONE");
		} else {
			$new_picklistid = $adb->query_result(
				$adb->pquery("SELECT picklistid FROM vtiger_picklist WHERE name=?", Array($this->name)), 0, 'picklistid');
		}

		$specialNameSpacedPicklists  = array(
			'opportunity_type'=>'opptypeid',
			'duration_minutes'=>'minutesid',
			'recurringtype'=>'recurringeventid'
		);

		// Fix Table ID column names
		$fieldName = (string)$this->name;
		if(in_array($fieldName.'_id', $adb->getColumnNames($picklist_table))) {
			$picklist_idcol = $fieldName.'_id';
		} elseif(array_key_exists($fieldName, $specialNameSpacedPicklists)) {
			$picklist_idcol = $specialNameSpacedPicklists[$fieldName];
		}
		// END

		// Add value to picklist now
		$sortid = 0; // TODO To be set per role
		foreach($values as $value) {
			$value = htmlentities($value,ENT_QUOTES,$default_charset);
			$new_picklistvalueid = getUniquePicklistID();
			$presence = 1; // 0 - readonly, Refer function in include/ComboUtil.php
			$new_id = $adb->getUniqueID($picklist_table);
			$adb->pquery("INSERT INTO $picklist_table($picklist_idcol, $this->name, presence, picklist_valueid) VALUES(?,?,?,?)",
				Array($new_id, $value, $presence, $new_picklistvalueid));
			++$sortid;

			// Associate picklist values to all the role
			$adb->pquery("INSERT INTO vtiger_role2picklist(roleid, picklistvalueid, picklistid, sortid) SELECT roleid,
				$new_picklistvalueid, $new_picklistid, $sortid FROM vtiger_role", array());
		}
	}

	/**
	 * Set values for picklist field (non-role based)
	 * @param Array List of values to add
	 *
	 * @internal Creates picklist base if it does not exists
	 * @access private
	 */
	function setNoRolePicklistValues($values) {
		global $adb;

		$picklist_table = 'vtiger_'.$this->name;
		$picklist_idcol = $this->name.'id';

		if(!Vtiger_Utils::CheckTable($picklist_table)) {
			Vtiger_Utils::CreateTable(
				$picklist_table,
				"($picklist_idcol INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
				$this->name VARCHAR(200) NOT NULL,
				sortorderid INT(11),
				presence INT (11) NOT NULL DEFAULT 1)",
				true);
			self::log("Creating table $picklist_table ... DONE");
		}

		// Add value to picklist now
		$sortid = 1;
		foreach($values as $value) {
			$presence = 1; // 0 - readonly, Refer function in include/ComboUtil.php
			$new_id = $adb->getUniqueId($picklist_table);
			$adb->pquery("INSERT INTO $picklist_table($picklist_idcol, $this->name, sortorderid, presence) VALUES(?,?,?,?)",
				Array($new_id, $value, $sortid, $presence));

			$sortid = $sortid+1;
		}
	}

	/**
	 * Set relation between field and modules (UIType 10)
	 * @param Array List of module names
	 *
	 * @internal Creates table vtiger_fieldmodulerel if it does not exists
	 */
	function setRelatedModules($moduleNames) {

		// We need to create core table to capture the relation between the field and modules.
		if(!Vtiger_Utils::CheckTable('vtiger_fieldmodulerel')) {
			Vtiger_Utils::CreateTable(
				'vtiger_fieldmodulerel',
				'(fieldid INT NOT NULL, module VARCHAR(100) NOT NULL, relmodule VARCHAR(100) NOT NULL, status VARCHAR(10), sequence INT)',
				true
			);
		}
		// END

		global $adb;
		foreach($moduleNames as $relmodule) {
			$checkres = $adb->pquery('SELECT * FROM vtiger_fieldmodulerel WHERE fieldid=? AND module=? AND relmodule=?',
				Array($this->id, $this->getModuleName(), $relmodule));

			// If relation already exist continue
			if($adb->num_rows($checkres)) continue;

			$adb->pquery('INSERT INTO vtiger_fieldmodulerel(fieldid, module, relmodule) VALUES(?,?,?)',
				Array($this->id, $this->getModuleName(), $relmodule));

			self::log("Setting $this->name relation with $relmodule ... DONE");
		}
		return true;
	}

	/**
	 * Remove relation between the field and modules (UIType 10)
	 * @param Array List of module names
	 */
	function unsetRelatedModules($moduleNames) {
		global $adb;
		foreach($moduleNames as $relmodule) {
			$adb->pquery('DELETE FROM vtiger_fieldmodulerel WHERE fieldid=? AND module=? AND relmodule = ?',
				Array($this->id, $this->getModuleName(), $relmodule));

			Vtiger_Utils::Log("Unsetting $this->name relation with $relmodule ... DONE");
		}
		return true;
	}

	/**
	 * Get Vtiger_Field instance by fieldid or fieldname
	 * @param mixed fieldid or fieldname
	 * @param Vtiger_Module Instance of the module if fieldname is used
	 */
	static function getInstance($value, $moduleInstance=false) {
		global $adb;
		$instance = false;

		$query = false;
		$queryParams = false;
		if(Vtiger_Utils::isNumber($value)) {
			$query = "SELECT * FROM vtiger_field WHERE fieldid=?";
			$queryParams = Array($value);
		} else {
			$query = "SELECT * FROM vtiger_field WHERE fieldname=? AND tabid=?";
			$queryParams = Array($value, $moduleInstance->id);
		}
		$result = $adb->pquery($query, $queryParams);
		if($adb->num_rows($result)) {
			$instance = new self();
			$instance->initialize($adb->fetch_array($result), $moduleInstance);
		}
		return $instance;
	}

	/**
	 * Get Vtiger_Field instances related to block
	 * @param Vtiger_Block Instnace of block to use
	 * @param Vtiger_Module Instance of module to which block is associated
	 */
	 static function getAllForBlock($blockInstance, $moduleInstance=false) {
		global $adb;
		$instances = false;

		$query = false;
		$queryParams = false;
		if($moduleInstance) {
			$query = "SELECT * FROM vtiger_field WHERE block=? AND tabid=?";
			$queryParams = Array($blockInstance->id, $moduleInstance->id);
		} else {
			$query = "SELECT * FROM vtiger_field WHERE block=?";
			$queryParams = Array($blockInstance->id);
		}
		$result = $adb->pquery($query, $queryParams);
		for($index = 0; $index < $adb->num_rows($result); ++$index) {
			$instance = new self();
			$instance->initialize($adb->fetch_array($result), $moduleInstance, $blockInstance);
			$instances[] = $instance;
		}
		return $instances;
	}

	/**
	 * Get Vtiger_Field instances related to module
	 * @param Vtiger_Module Instance of module to use
	 */
	static function getAllForModule($moduleInstance) {
		global $adb;
		$instances = false;

		$query = "SELECT * FROM vtiger_field WHERE tabid=?";
		$queryParams = Array($moduleInstance->id);

		$result = $adb->pquery($query, $queryParams);
		for($index = 0; $index < $adb->num_rows($result); ++$index) {
			$instance = new self();
			$instance->initialize($adb->fetch_array($result), $moduleInstance);
			$instances[] = $instance;
		}
		return $instances;
	}

	/**
	 * Delete fields associated with the module
	 * @param Vtiger_Module Instance of module
	 * @access private
	 */
	static function deleteForModule($moduleInstance) {
		global $adb;
		$adb->pquery("DELETE FROM vtiger_field WHERE tabid=?", Array($moduleInstance->id));
		self::log("Deleting fields of the module ... DONE");
	}
}
?>
