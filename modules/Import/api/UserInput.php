<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/

class Import_API_UserInput {
	protected $valuemap;
	
	function __construct($values = array()) {
		$this->valuemap = $values;
	}

	function get($key) {
		if(isset($this->valuemap[$key])) {
			return $this->valuemap[$key];
		}
		return '';
	}
	
	function has($key) {
		return isset($this->valuemap[$key]);
	}
	
	function set($key, $newvalue) {
		$this->valuemap[$key]= $newvalue;
	}
}