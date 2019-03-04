<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/

class MailManager_Response {
	private $error = null;
	private $result = null;
	private $json = false;

	public function __construct($isjson = false) {
		$this->json = $isjson;
	}

	public function isJson($flag) {
		$this->json = $flag;
	}

	public function setError($code, $message) {
		$error = array('code' => $code, 'message' => $message);
		$this->error = $error;
	}

	public function getError() {
		return $this->error;
	}

	public function hasError() {
		return !is_null($this->error);
	}

	public function setResult($result) {
		$this->result = $result;
	}

	public function getResult() {
		return $this->result;
	}

	public function addToResult($key, $value) {
		$this->result[$key] = $value;
	}

	public function prepareResponse() {
		$response = array();
		if ($this->result === null) {
			$response['success'] = false;
			$response['error'] = $this->error;
		} else {
			$response['success'] = true;
			$response['result'] = $this->result;
		}
		return $response;
	}

	public function emit() {
		if ($this->json) {
			echo $this->emitJSON();
		} else {
			echo $this->emitHTML();
		}
	}

	public function emitJSON() {
		return json_encode($this->prepareResponse());
	}

	public function emitHTML() {
		if ($this->result === null) {
			return (is_string($this->error))? $this->error : var_export($this->error, true);
		}
		return $this->result;
	}
}