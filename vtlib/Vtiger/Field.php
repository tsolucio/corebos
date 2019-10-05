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
include_once 'vtlib/Vtiger/FieldBasic.php';

/**
 * Provides APIs to control vtiger CRM Field
 * @package vtlib
 */
class Vtiger_Field extends Vtiger_FieldBasic {

	public $webserviceField = false;

	/**
	 * Get unique picklist id to use
	 */
	public function __getPicklistUniqueId() {
		global $adb;
		return $adb->getUniqueID('vtiger_picklist');
	}

	/**
	 * Set values for picklist field (for all the roles)
	 * @param Array List of values to add.
	 *
	 * @internal Creates picklist base if it does not exists
	 */
	public function setPicklistValues($values) {
		global $adb;

		// Non-Role based picklist values
		if ($this->uitype == '16') {
			$this->setNoRolePicklistValues($values);
			return;
		}

		$picklist_table = 'vtiger_'.$this->name;
		$picklist_idcol = $this->name.'id';
		if (!Vtiger_Utils::CheckTable($picklist_table)) {
			Vtiger_Utils::CreateTable(
				$picklist_table,
				"($picklist_idcol INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
				$this->name VARCHAR(200) NOT NULL,
				presence INT (1) NOT NULL DEFAULT 1,
				picklist_valueid INT NOT NULL DEFAULT 0)",
				true
			);
			$new_picklistid = $this->__getPicklistUniqueId();
			$adb->pquery('INSERT INTO vtiger_picklist (picklistid,name) VALUES(?,?)', array($new_picklistid, $this->name));
			self::log("Creating table $picklist_table ... DONE");
		} else {
			$rs = $adb->pquery('SELECT picklistid FROM vtiger_picklist WHERE name=?', array($this->name));
			$new_picklistid = $adb->query_result($rs, 0, 'picklistid');
		}

		$specialNameSpacedPicklists = array(
			'opportunity_type'=>'opptypeid',
			'duration_minutes'=>'minutesid',
			'recurringtype'=>'recurringeventid'
		);

		// Fix Table ID column names
		$fieldName = (string)$this->name;
		if (in_array($fieldName.'_id', $adb->getColumnNames($picklist_table))) {
			$picklist_idcol = $fieldName.'_id';
		} elseif (array_key_exists($fieldName, $specialNameSpacedPicklists)) {
			$picklist_idcol = $specialNameSpacedPicklists[$fieldName];
		}

		// Add value to picklist now
		$sortid = 0; // TODO To be set per role
		foreach ($values as $value) {
			$existsrs = $adb->pquery("select count(*) as cnt from $picklist_table where $this->name = ? COLLATE utf8_bin", array($value));
			if ($adb->query_result($existsrs, 0, 0)!=0) {
				continue;  // already exists so we ignore it
			}
			$new_picklistvalueid = getUniquePicklistID();
			$presence = 1; // 0 - readonly, Refer function in include/ComboUtil.php
			$new_id = $adb->getUniqueID($picklist_table);
			$adb->pquery(
				"INSERT INTO $picklist_table($picklist_idcol, $this->name, presence, picklist_valueid) VALUES(?,?,?,?)",
				array($new_id, $value, $presence, $new_picklistvalueid)
			);
			++$sortid;

			// Associate picklist values to all the role
			$adb->pquery("INSERT INTO vtiger_role2picklist(roleid, picklistvalueid, picklistid, sortid) SELECT roleid,
				$new_picklistvalueid, $new_picklistid, $sortid FROM vtiger_role", array());
		}
	}

	/**
	 * Delete values for picklist field (for all the roles)
	 * @param array List of values to delete. 'value_to_delete' => 'substitute_value'
	 *
	 * @internal Creates picklist base if it does not exists
	 */
	public function delPicklistValues($values) {
		global $adb;

		foreach ($values as $gvar => $newval) {
			$sql = 'select * from vtiger_'.$this->name.' where BINARY '.$this->name.'=?';
			$result = $adb->pquery($sql, array($gvar));
			if ($adb->num_rows($result)>0) {
				$origPicklistID = $adb->query_result($result, 0, 'picklist_valueid');
				$sql = 'delete from vtiger_'.$this->name.' where BINARY '.$this->name.'=?';
				$adb->pquery($sql, array($gvar));
				$sql = 'delete from vtiger_role2picklist where picklistvalueid=?';
				$adb->pquery($sql, array($origPicklistID));
				$sql = 'DELETE FROM vtiger_picklist_dependency WHERE sourcevalue=? AND sourcefield=? AND tabid=?';
				$adb->pquery($sql, array($gvar, $this->name, $this->getModuleId()));
				if (!empty($newval)) {
					$updsql = "UPDATE {$this->table} SET {$this->column}=? WHERE {$this->column}=?";
					$adb->pquery($updsql, array($newval,$gvar));
				}
			}
		}
	}

	/**
	 * Set values for picklist field (non-role based)
	 * @param array List of values to add
	 *
	 * @internal Creates picklist base if it does not exists
	 * @access private
	 */
	public function setNoRolePicklistValues($values) {
		global $adb;

		$special_pl = array('recurring_frequency');
		$picklist_table = 'vtiger_'.$this->name;
		if (in_array($this->name, $special_pl)) {
			$picklist_idcol = $this->name.'_id';
		} else {
			$picklist_idcol = $this->name.'id';
		}

		if (!Vtiger_Utils::CheckTable($picklist_table)) {
			Vtiger_Utils::CreateTable(
				$picklist_table,
				"($picklist_idcol INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
				$this->name VARCHAR(200) NOT NULL,
				sortorderid INT(11),
				presence INT (11) NOT NULL DEFAULT 1)",
				true
			);
			self::log("Creating table $picklist_table ... DONE");
		}

		// Add value to picklist now
		$sortid = 1;
		foreach ($values as $value) {
			$existsrs = $adb->pquery("select count(*) as cnt from $picklist_table where $this->name = ?", array($value));
			if ($adb->query_result($existsrs, 0, 0)!=0) {
				continue;  // already exists so we ignore it
			}
			$presence = 1; // 0 - readonly, Refer function in include/ComboUtil.php
			$new_id = $adb->getUniqueId($picklist_table);
			$adb->pquery(
				"INSERT INTO $picklist_table($picklist_idcol, $this->name, sortorderid, presence) VALUES(?,?,?,?)",
				array($new_id, $value, $sortid, $presence)
			);

			$sortid = $sortid+1;
		}
	}

	/**
	 * Set relation between field and modules (UIType 10)
	 * @param array List of module names
	 *
	 * @internal Creates table vtiger_fieldmodulerel if it does not exists
	 */
	public function setRelatedModules($moduleNames) {
		if (!is_array($moduleNames) && is_string($moduleNames)) {
			$moduleNames = array($moduleNames);
		}
		// We need to create core table to capture the relation between the field and modules.
		if (!Vtiger_Utils::CheckTable('vtiger_fieldmodulerel')) {
			Vtiger_Utils::CreateTable(
				'vtiger_fieldmodulerel',
				'(fieldid INT NOT NULL, module VARCHAR(100) NOT NULL, relmodule VARCHAR(100) NOT NULL, status VARCHAR(10), sequence INT)',
				true
			);
		}

		global $adb;
		$rs = $adb->pquery('SELECT max(sequence) FROM vtiger_fieldmodulerel WHERE fieldid=? AND module=?', array($this->id, $this->getModuleName()));
		$nextseq = $adb->query_result($rs, 0, 0);
		if (empty($nextseq)) {
			$nextseq=0;
		}
		foreach ($moduleNames as $relmodule) {
			$checkres = $adb->pquery(
				'SELECT * FROM vtiger_fieldmodulerel WHERE fieldid=? AND module=? AND relmodule=?',
				array($this->id, $this->getModuleName(), $relmodule)
			);

			// If relation already exist continue
			if ($adb->num_rows($checkres)) {
				continue;
			}

			$adb->pquery(
				'INSERT INTO vtiger_fieldmodulerel(fieldid, module, relmodule, sequence) VALUES(?,?,?,?)',
				array($this->id, $this->getModuleName(), $relmodule, ++$nextseq)
			);

			self::log("Setting $this->name relation with $relmodule ... DONE");
		}
		return true;
	}

	/**
	 * Remove relation between the field and modules (UIType 10)
	 * @param array List of module names
	 */
	public function unsetRelatedModules($moduleNames) {
		global $adb;
		if (!is_array($moduleNames) && is_string($moduleNames)) {
			$moduleNames = array($moduleNames);
		}
		foreach ($moduleNames as $relmodule) {
			$adb->pquery(
				'DELETE FROM vtiger_fieldmodulerel WHERE fieldid=? AND module=? AND relmodule = ?',
				array($this->id, $this->getModuleName(), $relmodule)
			);

			Vtiger_Utils::Log("Unsetting $this->name relation with $relmodule ... DONE");
		}
		return true;
	}

	/**
	 * Get Vtiger_Field instance by fieldid or fieldname
	 * @param mixed fieldid or fieldname
	 * @param Vtiger_Module Instance of the module if fieldname is used
	 */
	public static function getInstance($value, $moduleInstance = false) {
		global $adb;
		$instance = false;

		$query = false;
		$queryParams = false;
		if (Vtiger_Utils::isNumber($value)) {
			$query = 'SELECT * FROM vtiger_field WHERE fieldid=?';
			$queryParams = array($value);
		} else {
			if (empty($moduleInstance)) {
				return false;
			}
			$query = 'SELECT * FROM vtiger_field WHERE fieldname=? AND tabid=?';
			$queryParams = array($value, $moduleInstance->id);
		}
		$result = $adb->pquery($query, $queryParams);
		if ($adb->num_rows($result)) {
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
	public static function getAllForBlock($blockInstance, $moduleInstance = false) {
		global $adb;
		$instances = false;

		$query = false;
		$queryParams = false;
		if ($moduleInstance) {
			$query = 'SELECT * FROM vtiger_field WHERE block=? AND tabid=? ORDER BY sequence';
			$queryParams = array($blockInstance->id, $moduleInstance->id);
		} else {
			$query = 'SELECT * FROM vtiger_field WHERE block=? ORDER BY sequence';
			$queryParams = array($blockInstance->id);
		}
		$result = $adb->pquery($query, $queryParams);
		for ($index = 0; $index < $adb->num_rows($result); ++$index) {
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
	public static function getAllForModule($moduleInstance) {
		global $adb;
		$instances = false;

		$result = $adb->pquery('SELECT * FROM vtiger_field WHERE tabid=? ORDER BY block,sequence', array($moduleInstance->id));
		for ($index = 0; $index < $adb->num_rows($result); ++$index) {
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
	public static function deleteForModule($moduleInstance) {
		global $adb;
		$adb->pquery("DELETE FROM vtiger_field WHERE tabid=?", array($moduleInstance->id));
		self::log("Deleting fields of the module ... DONE");
	}

	/**
	 * Function to get list of modules the field refernced to
	 * @return <Array> -  list of modules for which field is refered to
	 */
	public function getReferenceList($hideDisabledModules = true, $presenceZero = true) {
		$webserviceField = $this->getWebserviceFieldObject();
		$referenceList = $webserviceField->getReferenceList($hideDisabledModules);
		if ($presenceZero && is_array($referenceList) && count($referenceList) > 0) {
			foreach ($referenceList as $key => $referenceModule) {
				$moduleModel = Vtiger_Module::getInstance($referenceModule);
				if ($moduleModel && $moduleModel->presence != 0) {
					unset($referenceList[$key]);
				}
			}
		}
		return $referenceList;
	}

	/**
	 * Function to get the Webservice Field Object for the current Field Object
	 * @return WebserviceField instance
	 */
	public function getWebserviceFieldObject() {
		if ($this->webserviceField == false) {
			$db = PearDatabase::getInstance();

			$row = array();
			$row['uitype'] = $this->uitype;
			$row['block'] = $this->block->id;
			$row['tablename'] = $this->table;
			$row['columnname'] = $this->column;
			$row['fieldname'] = $this->name;
			$row['fieldlabel'] = $this->label;
			$row['displaytype'] = $this->displaytype;
			$row['masseditable'] = $this->masseditable;
			$row['typeofdata'] = $this->typeofdata;
			$row['presence'] = $this->presence;
			$row['tabid'] = $this->getModuleId();
			$row['fieldid'] = $this->id;
			$row['readonly'] = !$this->readonly;
			$row['defaultvalue'] = $this->defaultvalue;

			$this->webserviceField = WebserviceField::fromArray($db, $row);
		}
		return $this->webserviceField;
	}
}
?>
