<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/

class Vtiger_Request {

	// Datastore
	private $valuemap = array();
	private $rawvaluemap = array();
	private $defaultmap = array();

	/**
	 * Default constructor
	 */
	public function __construct($values = array(), $rawvalues = array(), $stripslashes = true) {
		$this->valuemap = $values;
		$this->rawvaluemap = $rawvalues;
		if ($stripslashes && !empty($this->valuemap)) {
			$this->valuemap = $this->stripslashes_recursive($this->valuemap);
		}
	}

	/**
	 * Strip the slashes recursively on the values.
	 */
	public function stripslashes_recursive($value) {
		$value = is_array($value) ? array_map(array($this, 'stripslashes_recursive'), $value) : stripslashes($value);
		return $value;
	}

	/**
	 * Get key value (otherwise default value)
	 */
	public function get($key, $defvalue = '') {
		$value = $defvalue;
		if (isset($this->valuemap[$key])) {
			$value = $this->valuemap[$key];
		}
		if ($value === '' && isset($this->defaultmap[$key])) {
			$value = $this->defaultmap[$key];
		}

		$isJSON = false;
		if (is_string($value)) {
			// NOTE: json_decode gets confused with big-integers (when passed as string)
			// and converts them to ugly exponential format - to overcome this we are performing a pre-check
			$val = trim($value);
			if ((strpos($val, '[') === 0 && substr($val, -1) == ']') || (strpos($val, '{') === 0 && substr($val, -1) == '}')) {
				$isJSON = true;
				$value = trim($value);
			}
		}
		if($isJSON) {
			$decodeValue = json_decode($value,true);
			if (isset($decodeValue)) {
				$value = $decodeValue;
			}
		}

		//Handled for null because vtlib_purify returns empty string
		if (!empty($value)) {
			$value = vtlib_purify($value);
			//$value = str_replace(array(chr(10),chr(13)), '', $value);
		}
		return $value;
	}

	/**
	 * Get value for key as boolean
	 */
	public function getBoolean($key, $defvalue = '') {
		return strcasecmp('true', $this->get($key, $defvalue).'') === 0;
	}

	/**
	 * Get data map Raw
	 */
	public function getAllRaw()
	{
		return $this->rawvaluemap;
	}

	/**
	 * Get data map
	 */
	public function getAll() {
		$vals = array();
		foreach ($this->valuemap as $k => $v) {
			$vals[$k] = $this->get($k);
		}
		return $vals;
	}

	/**
	 * Check for existence of key
	 */
	public function has($key) {
		return isset($this->valuemap[$key]);
	}

	/**
	 * Is the value (linked to key) empty?
	 */
	public function isEmpty($key) {
		$value = $this->get($key);
		return empty($value);
	}

	/**
	 * Get the raw value (if present) ignoring primary value.
	 */
	public function getRaw($key, $defvalue = '') {
		if (isset($this->rawvaluemap[$key])) {
			return $this->rawvaluemap[$key];
		}
		return $this->get($key, $defvalue);
	}

	/**
	 * Set the value for key
	 */
	public function set($key, $newvalue) {
		$this->valuemap[$key] = $newvalue;
	}

	/**
	 * Delete the value for key
	 */
	public function delete($key) {
		unset($this->valuemap[$key]);
		unset($this->rawvaluemap[$key]);
	}

	/**
	 * Set the value for key, both in the object as well as global $_REQUEST variable
	 */
	public function setGlobal($key, $newvalue) {
		$this->set($key, $newvalue);
		$_REQUEST[$key] = $newvalue;
	}

	/**
	 * Set default value for key
	 */
	public function setDefault($key, $defvalue) {
		$this->defaultmap[$key] = $defvalue;
	}

	public function isAjax() {
		if (!empty($_SERVER['HTTP_X_PJAX']) && $_SERVER['HTTP_X_PJAX'] === true) {
			return true;
		} elseif (!empty($_SERVER['HTTP_X_REQUESTED_WITH'])) {
			return true;
		}
		return false;
	}

	/**
	 * Validating incoming request.
	 */
	public function validateReadAccess() {
		$this->validateReferer();
		return true;
	}

	public function validateWriteAccess($skipRequestTypeCheck = false) {
		if (!$skipRequestTypeCheck) {
			if ($_SERVER['REQUEST_METHOD'] != 'POST')
				throw new Exception('Invalid request - validate Write Access');
		}
		$this->validateReadAccess();
		$this->validateCSRF();
		return true;
	}

	protected function validateReferer() {
		global $current_user;
		// Referer check if present - to over come
		if (isset($_SERVER['HTTP_REFERER']) && $current_user) {//Check for user post authentication.
			global $site_URL;
			if ((stripos($_SERVER['HTTP_REFERER'], $site_URL) !== 0) && ($this->get('module') != 'Install')) {
				throw new Exception('Illegal request');
			}
		}
		return true;
	}

	protected function validateCSRF() {
		if (!csrf_check(false)) {
			throw new Exception('Unsupported request');
		}
	}

	public static function get_ip() {
		//Just get the headers if we can or else use the SERVER global
		if (function_exists('apache_request_headers')) {
			$headers = apache_request_headers();
		} else {
			$headers = $_SERVER;
		}

		//Get the forwarded IP if it exists
		if (array_key_exists( 'X-Forwarded-For', $headers) && filter_var($headers['X-Forwarded-For'], FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
			$the_ip = $headers['X-Forwarded-For'];
		} elseif (array_key_exists( 'HTTP_X_FORWARDED_FOR', $headers) && filter_var($headers['HTTP_X_FORWARDED_FOR'], FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
			$the_ip = $headers['HTTP_X_FORWARDED_FOR'];
		} else {
			$the_ip = filter_var($_SERVER['REMOTE_ADDR'], FILTER_VALIDATE_IP, FILTER_FLAG_IPV4);
		}
		return $the_ip;
	}

	/**
	* Search valuemap for keys starting with
	*   RETURN
	*   return_
	*   return
	* for all those keys it will retrieve the value and if not empty, it will construct a URL using the lower case of the key and the urlencoded value
	* the rules for converting the key to a variable name are:
	*   RETURN: it will use the key exactly as is but in lower case
	*   return_: it will strip the "return_" from the start of the string and use the rest as the key in lower case
	*   return: it will strip the "return" from the start of the string and use the rest as the key in lower case
	* @return String - return url
	*/
	public function getReturnURL() {
		$data = $this->getAll();
		$returnURL = array();
		foreach ($data as $key => $value) {
			if (stripos($key, 'return_') === 0 && !empty($value) && $value != '/') {
				$newKey = str_replace_once('return_','',$key);
				$returnURL[strtolower($newKey)] = $value;
			} elseif (stripos($key, 'return') === 0 && !empty($value) && $value != '/') {
				$newKey = str_replace_once('return','',$key);
				$returnURL[strtolower($newKey)] = $value;
			}
		}
		return http_build_query($returnURL);
	}

}
