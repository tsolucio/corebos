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
 * Configuration file reader
 */
class ConfigFileReader {
	// Each line is treated as configuration row
	protected $rows;
	// Path to configuration file
	protected $filepath;
	
	// Iteration support for rows
	protected $rowIndex;
	
	// Editables and Viewables
	protected $viewables;
	protected $editables;
	
	/**
	 * Constructor
	 * You can restrict variable display for viewing and editing
	 */
	function __construct($path, $viewables=array(), $editables=array()) {
		$this->filepath = $path;
		$this->viewables = $viewables;
		$this->editables = $editables;
		$this->read();
	}
	
	/**
	 * Read and parse the configuration file contents.
	 */
	protected function read() {
		$fileContent = trim(file_get_contents($this->filepath));
		$pattern = '/\$([^=]+)=([^;]+);/';
		$matches = null;
		$matchesFound = preg_match_all($pattern, $fileContent, $matches);
		$configContents = array();
		if($matchesFound) {
			$configContents = $matches[0];
		}
		$this->rows = array();
		foreach($configContents as $configLine) {
			$this->rows[] = new ConfigFileRow($configLine, $this);
		}
		$this->rowIndex = -1;
		unset($fileContent);
	}
	
	/**
	 * Save the rows back to configuration.
	 */
	function save() {
		$fileContent = trim(file_get_contents($this->filepath));
		if ($this->rows) {
			$fp = fopen($this->filepath, 'w');
			$rowcount = count($this->rows);
			for($index = 0; $index < $rowcount; ++$index) {
				$row = $this->rows[$index];
				if($row->isEditable()) {
					$variableName = $row->variableName();
					$newVariableValue = $row->variableValue();
					$pattern = '/\$'.$variableName.'[\s]+=([^;]+);/';
					$replacement = $row->toString();
					$fileContent = preg_replace($pattern, $replacement, $fileContent);
				}
			}
			fwrite($fp, $fileContent);
			fclose($fp);
		}
	}
	
	function editables($key = false) {
		if ($key === false) return array_keys($this->editables);
		return $this->editables[$key];
	}
	
	function viewables($key = false) {
		if ($key === false) return array_keys($this->viewables);
		return $this->viewables[$key];
	}
	
	/**
	 * Set new value to the desired variable.
	 */
	function setVariableValue($name, $value) {
		if ($this->rows) {
			foreach($this->rows as $row) {
				if ($row->matchesVariableName($name)) {
					if($name == 'upload_maxsize'){
						return $row->setVariableValue($value*1000000);
					}else{
						return $row->setVariableValue($value);
					}
				}
			}
		}
	}
	
	/**
	 * Get all the rows
	 */
	function getAll() {
		return $this->rows;
	}
	
	/**
	 * Has next row to read?
	 */
	function next() {
		if ($this->rowIndex++ < count($this->rows)) {
			return true;
		}
	}
	
	/**
	 * Get the current row during iteration (please call next() before this)
	 */
	function get() {
		return $this->rows[$this->rowIndex];
	}
	
	/**
	 * Rewind the iteration
	 */
	function rewind() {
		$this->rowIndex = 0;
	}
}

/**
 * Configuration file row class
 */
class ConfigFileRow {
	// Actual line content
	protected $lineContent;
	// Parsed variable name and value
	protected $parsedVarName = false;
	protected $parsedVarValue= '';
	
	// Is the variable of string type?
	protected $isValueString = false;
	
	// Some variables which is never editable
	protected static $alltimeNoneditableVars = array(
		"dbconfig['db_server']",
		"application_unique_key"
	);
	
	// Editable and Viewable variable names
	protected $parent;
	
	// Is the variable value editable?
	protected $isValueEditable = false;
	
	// Regex to detect variable name and its safe value
	static $variableRegex = '/^[ \t]*\\$([^=]+)=([^;]+)/';
	//Regex to detect support name,it doesnt allow any single quote,and special characters,it does allow only alpha numeric,utf8,.com,@
	static $variableUnSafeValueRegex = "/[\x{4e00}-\x{9fa5}[:print:]]+.*\-/u";
	/**
	 * Constructor
	 */
	function __construct($content, $parent) {
		$this->lineContent = $content;
		$this->parent = $parent;
		$this->parse();
	}
	
	/**
	 * Parse the content
	 */
	protected function parse() {
		if (preg_match(self::$variableRegex, $this->lineContent, $m)) {
			$this->parsedVarName = trim($m[1]);
			$this->parsedVarValue = trim($m[2]);
			// Is variable string type?
			if (strpos($this->parsedVarValue, "'") === 0 || strpos($this->parsedVarValue, '"') === 0) {
				$this->isValueString = true;
				$this->parsedVarValue = trim($m[2], "'\" ");
			}
			if (!in_array($this->parsedVarName, self::$alltimeNoneditableVars)) {
				$this->isValueEditable = true;
			} else {
				$this->isValueEditable = false;
			}
		}
	}
	
	/**
	 * Does the row represent variable?
	 */
	function isVariable() {
		return ($this->parsedVarName !== false);
	}
	
	/**
	 * Is the variable viewable?
	 */
	function isViewable() {
		if ($this->isVariable()) {
			$editables = $this->parent->editables();
			if (!empty($editables)) {
				return in_array($this->parsedVarName, $this->parent->viewables());
			} else {
				return true;
			}
		}
		return false;
	}
	
	/** 
	 * Is the variable editable?
	 */
	function isEditable() {
		if ($this->isVariable()) {
			$editables = $this->parent->editables();
			if (empty($editables)) {
				return $this->isValueEditable;
			}
			return ((in_array($this->parsedVarName, $editables) !== false) && $this->isValueEditable);
		}
		return false;
	}
	
	/**
	 * Get variable name
	 */
	function variableName() {
		return $this->parsedVarName;
	}
	
	/**
	 * Check if the variable name matches with input
	 */
	function matchesVariableName($input) {
		$input = ltrim($input, '$');
		return ($input == $this->parsedVarName);
	}
	
	/**
	 * Get variable value
	 */
	function variableValue() {
		return $this->parsedVarValue;
	}
	
	/**
	 * Is the variable value string type?
	 */
	function isValueString() {
		return $this->isValueString;
	}
	
	/**
	 * Set the variable value
	 */
	function setVariableValue($value) {
		// TODO Avoid any PHP String concate hacks
		if (preg_match(self::$variableUnSafeValueRegex, $value, $m)) {
			return false;
		}
		// Should the value be restricted to a set?
		$meta = $this->meta();
		if (isset($meta['values']) && is_array($meta['values']) ) {
			$allowedValues = array_keys($meta['values']);
			if (!empty($allowedValues) && !in_array($value, $allowedValues)) {
				return false;
			}
		}
		$this->parsedVarValue = $value;
		return true;
	}
	
	/**
	 * Get the meta information
	 */
	function meta() {
		if ($this->isEditable()) return $this->parent->editables($this->parsedVarName);
		if ($this->isViewable()) return $this->parent->viewables($this->parsedVarName);
		return false;
	}
	
	/**
	 * String representation of the instance
	 */
	function toString() {
		if ($this->isVariable()) {
			$encloseWith = "";
			if ($this->isValueString()) {
				$encloseWith = "'";
			}
			return sprintf("\$%s = %s%s%s;", $this->parsedVarName, $encloseWith, $this->parsedVarValue, $encloseWith);
		}
		return $this->lineContent;
	}
}
?>