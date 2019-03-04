<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/

/**
 * Provides basic API to work with vtiger CRM Fields
 * @package vtlib
 */
class Vtiger_FieldBasic {
	/** ID of this field instance */
	public $id;
	public $name;
	public $label = false;
	public $table = false;
	public $column = false;
	public $columntype = false;
	public $helpinfo = '';
	public $masseditable = 1; // Default: Enable massedit for field

	public $uitype = 1;
	public $typeofdata = 'V~O';
	public $displaytype   = 1;
	public $generatedtype = 1;
	public $readonly      = 1;
	public $presence      = 2;
	public $defaultvalue  = '';
	public $maximumlength = 100;
	public $sequence      = false;
	public $quickcreate   = 1;
	public $quicksequence = false;
	public $info_type     = 'BAS';
	public $block;

	/**
	 * Constructor
	 */
	public function __construct() {
	}

	/**
	 * Initialize this instance
	 * @param Array
	 * @param Vtiger_Module Instance of module to which this field belongs
	 * @param Vtiger_Block Instance of block to which this field belongs
	 * @access private
	 */
	public function initialize($valuemap, $moduleInstance = false, $blockInstance = false) {
		$this->id = $valuemap['fieldid'];
		$this->name = $valuemap['fieldname'];
		$this->label = $valuemap['fieldlabel'];
		$this->column = $valuemap['columnname'];
		$this->table = $valuemap['tablename'];
		$this->uitype = $valuemap['uitype'];
		$this->typeofdata = $valuemap['typeofdata'];
		$this->helpinfo = $valuemap['helpinfo'];
		$this->masseditable = $valuemap['masseditable'];
		$this->block = $blockInstance? $blockInstance : Vtiger_Block::getInstance($valuemap['block'], $moduleInstance);
	}

	/** Cache (Record) the schema changes to improve performance */
	public static $__cacheSchemaChanges = array();

	/**
	 * Initialize vtiger schema changes.
	 * @access private
	 */
	private function __handleVtigerCoreSchemaChanges() {
		// Add helpinfo column to the vtiger_field table
		if (empty(self::$__cacheSchemaChanges['vtiger_field.helpinfo'])) {
			Vtiger_Utils::AddColumn('vtiger_field', 'helpinfo', ' TEXT');
			self::$__cacheSchemaChanges['vtiger_field.helpinfo'] = true;
		}
	}

	/**
	 * Get unique id for this instance
	 * @access private
	 */
	private function __getUniqueId() {
		global $adb;
		return $adb->getUniqueID('vtiger_field');
	}

	/**
	 * Get next sequence id to use within a block for this instance
	 * @access private
	 */
	private function __getNextSequence() {
		global $adb;
		$result = $adb->pquery('SELECT MAX(sequence) AS max_seq FROM vtiger_field WHERE tabid=? AND block=?', array($this->getModuleId(), $this->getBlockId()));
		$maxseq = 0;
		if ($result && $adb->num_rows($result)) {
			$maxseq = (int)$adb->query_result($result, 0, 'max_seq');
			$maxseq += 1;
		}
		return $maxseq;
	}

	/**
	 * Get next quick create sequence id for this instance
	 * @access private
	 */
	private function __getNextQuickCreateSequence() {
		global $adb;
		$result = $adb->pquery('SELECT MAX(quickcreatesequence) AS max_quickcreateseq FROM vtiger_field WHERE tabid=?', array($this->getModuleId()));
		$max_quickcreateseq = 0;
		if ($result && $adb->num_rows($result)) {
			$max_quickcreateseq = (int)$adb->query_result($result, 0, 'max_quickcreateseq');
			$max_quickcreateseq += 1;
		}
		return $max_quickcreateseq;
	}

	/**
	 * Create this field instance
	 * @param Vtiger_Block Instance of the block to use
	 * @access private
	 */
	private function __create($blockInstance) {
		$this->__handleVtigerCoreSchemaChanges();

		global $adb;

		$this->block = $blockInstance;

		$moduleInstance = $this->getModuleInstance();

		$this->id = $this->__getUniqueId();

		if (!$this->sequence) {
			$this->sequence = $this->__getNextSequence();
		}

		// If enabled for display
		if ($this->quickcreate != 1 && !$this->quicksequence) {
			$this->quicksequence = $this->__getNextQuickCreateSequence();
		} else {
			$this->quicksequence = null;
		}

		// Initialize other variables which are not done
		if (!$this->table) {
			$this->table = $moduleInstance->basetable;
		}
		if (!$this->column) {
			$this->column = strtolower($this->name);
			if (!$this->columntype) {
				$this->columntype = 'VARCHAR(100)';
			}
		}

		if (!$this->label) {
			$this->label = $this->name;
		}

		$chkrs = $adb->pquery(
			'select 1 from vtiger_field where tabid=? and (columnname=? or fieldlabel=?) limit 1',
			array($this->getModuleId(), $this->column, $this->label)
		);
		if ($adb->num_rows($chkrs)==0) {
			$params = array(
				$this->getModuleId(),
				$this->id,
				$this->column,
				$this->table,
				(int)$this->generatedtype,
				$this->uitype,
				$this->name,
				$this->label,
				$this->readonly,
				$this->presence,
				$this->defaultvalue,
				$this->maximumlength,
				$this->sequence,
				$this->getBlockId(),
				$this->displaytype,
				$this->typeofdata,
				$this->quickcreate,
				$this->quicksequence,
				$this->info_type,
				$this->helpinfo,
			);
			$result = $adb->pquery(
				'INSERT INTO vtiger_field (
					tabid,
					fieldid,
					columnname,
					tablename,
					generatedtype,
					uitype,
					fieldname,
					fieldlabel,
					readonly,
					presence,
					defaultvalue,
					maximumlength,
					sequence,
					block,
					displaytype,
					typeofdata,
					quickcreate,
					quickcreatesequence,
					info_type,
					helpinfo
				) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)',
				$params
			);
			// Set the field status for mass-edit (if set)
			$adb->pquery('UPDATE vtiger_field SET masseditable=? WHERE fieldid=?', array($this->masseditable, $this->id));
			Vtiger_Profile::initForField($this);
		}
		$colrs = $adb->getColumnNames($this->table);
		if (!in_array($this->column, $colrs)) {
			if (!empty($this->columntype)) {
				Vtiger_Utils::AddColumn($this->table, $this->column, $this->columntype);
			}
		}
		if (!empty($result)) {
			self::log("Creating Field $this->name ... DONE");
			self::log("Module language mapping for $this->label ... CHECK");
		} else {
			self::log("Creating Field $this->name ($this->label) on $moduleInstance->name ... <span style='color:red'>**ERROR**</span>");
			if (!empty($params)) {
				self::log(print_r($params, true));
			} else {
				self::log("A field with that name or label already exists.");
			}
		}
	}

	/**
	 * Update this field instance
	 * @access private
	 * @internal TODO
	 */
	private function __update() {
		$db = PearDatabase::getInstance();
		$query = 'UPDATE vtiger_field SET typeofdata=?,presence=?,quickcreate=?,masseditable=?,defaultvalue=?';
		$params = array($this->typeofdata, $this->presence, $this->quickcreate, $this->masseditable, $this->defaultvalue);

		if (!empty($this->uitype)) {
			$query .= ', uitype=?';
			$params[] = $this->uitype;
		}
		if (!empty($this->label)) {
			$query .= ', fieldlabel=?';
			$params[] = decode_html($this->label);
		}
		$query .= ' WHERE fieldid=?';
		$params[] = $this->id;

		$db->pquery($query, $params);
		self::log("Updating Field $this->name ... DONE");
	}

	/**
	 * Delete this field instance
	 * @access private
	 */
	private function __delete() {
		global $adb;
		Vtiger_Profile::deleteForField($this);
		$adb->pquery('DELETE FROM vtiger_field WHERE fieldid=?', array($this->id));
		self::log("Deleteing Field $this->name ... DONE");
	}

	/**
	 * Get block id to which this field instance is associated
	 */
	public function getBlockId() {
		return $this->block->id;
	}

	/**
	 * Get module id to which this field instance is associated
	 */
	public function getModuleId() {
		return $this->block->module->id;
	}

	/**
	 * Get module name to which this field instance is associated
	 */
	public function getModuleName() {
		return $this->block->module->name;
	}

	/**
	 * Get module instance to which this field instance is associated
	 */
	public function getModuleInstance() {
		return $this->block->module;
	}

	/**
	 * Save this field instance
	 * @param Vtiger_Block Instance of block to which this field should be added.
	 */
	public function save($blockInstance = false) {
		if ($this->id) {
			$this->__update();
		} else {
			$this->__create($blockInstance);
		}
		return $this->id;
	}

	/**
	 * Delete this field instance
	 */
	public function delete() {
		$this->__delete();
	}

	/**
	 * Set Help Information for this instance.
	 * @param String Help text (content)
	 */
	public function setHelpInfo($helptext) {
		// Make sure to initialize the core tables first
		$this->__handleVtigerCoreSchemaChanges();

		global $adb;
		$adb->pquery('UPDATE vtiger_field SET helpinfo=? WHERE fieldid=?', array($helptext, $this->id));
		self::log("Updated help information of $this->name ... DONE");
	}

	/**
	 * Set Masseditable information for this instance.
	 * @param Integer Masseditable value
	 */
	public function setMassEditable($value) {
		global $adb;
		$adb->pquery('UPDATE vtiger_field SET masseditable=? WHERE fieldid=?', array($value, $this->id));
		self::log("Updated masseditable information of $this->name ... DONE");
	}

	/**
	 * Helper function to log messages
	 * @param String Message to log
	 * @param Boolean true appends linebreak, false to avoid it
	 * @access public
	 */
	public static function log($message, $delim = true) {
		Vtiger_Utils::Log($message, $delim);
	}
}
?>
