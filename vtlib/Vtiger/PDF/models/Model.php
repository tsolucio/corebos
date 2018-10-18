<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/
class Vtiger_PDF_Model {
	protected $values = array();

	public function set($key, $value) {
		$this->values[$key] = $value;
	}

	public function get($key, $defvalue = '') {
		return (isset($this->values[$key]))? $this->values[$key] : $defvalue;
	}

	public function count() {
		return count($this->values);
	}

	public function keys() {
		return array_keys($this->values);
	}
}