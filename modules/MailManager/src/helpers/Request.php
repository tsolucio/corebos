<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/
class MailManager_Request {
	protected $valuemap;

	public function __construct($values) {
		$this->valuemap = $values;
	}

	public function has($key) {
		return isset($this->valuemap[$key]);
	}

	public function get($key, $defvalue = '') {
		$value = $defvalue;
		if (isset($this->valuemap[$key])) {
			$value = $this->valuemap[$key];
		}
		if (!empty($value)) {
			$value = vtlib_purify($value);
		}
		return urldecode($value);
	}

	public function set($key, $value) {
		$this->valuemap[$key] = $value;
	}

	public function values() {
		return $this->valuemap;
	}

	public function keys() {
		return array_keys($this->valuemap);
	}

	public function getOperation($defvalue = '') {
		return $this->get('_operation', $defvalue);
	}

	public function getOperationArg($defvalue = '') {
		return $this->get('_operationarg', $defvalue);
	}
}
?>