<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/
include_once __DIR__ . '/../ISMSProvider.php';
include_once 'vtlib/Vtiger/Net/Client.php';

class TestFunctionality implements ISMSProvider {

	private $username;
	private $password;
	private $parameters = array();
	public $helpURL = '';
	public $helpLink = 'Test SMS Functionality';

	const SERVICE_URI = 'http://localhost/';
	private static $REQUIRED_PARAMETERS = array('app_id');

	/**
	 * Function to get provider name
	 * @return string provider name
	 */
	public function getName() {
		return $this->helpLink;
	}

	public function setAuthParameters($username, $password) {
		$this->username = $username;
		$this->password = $password;
	}

	public function setParameter($key, $value) {
		$this->parameters[$key] = $value;
	}

	public function getParameter($key, $defvalue = false) {
		if (isset($this->parameters[$key])) {
			return $this->parameters[$key];
		}
		return $defvalue;
	}

	public function getRequiredParams() {
		return self::$REQUIRED_PARAMETERS;
	}

	public function getServiceURL($type = false) {
		if ($type) {
			return  self::SERVICE_URI . $type;
		}
		return 'false';
	}

	protected function prepareParameters() {
		$params = array('user' => $this->username, 'pwd' => $this->password);
		foreach (self::$REQUIRED_PARAMETERS as $key) {
			$params[$key] = $this->getParameter($key);
		}
		return $params;
	}

	public function send($message, $tonumbers) {
		global $log;
		$tonumbers = (array)$tonumbers;
		$params = $this->prepareParameters();
		$params['text'] = $message;
		$params['to'] = implode(',', $tonumbers);
		$serviceURL = $this->getServiceURL(self::SERVICE_SEND);
		$result = array(array(
			'id' => uniqid($serviceURL),
			'action' => 'send',
			'where' => $serviceURL,
			'to' => $params['to'],
			'statusmessage' => $params['text'],
			'status' => self::MSG_STATUS_DELIVERED,
			'params' => $params,
		));
		$log->fatal($result);
		return $result;
	}

	public function query($messageid) {
		global $log, $adb;
		$serviceURL = $this->getServiceURL(self::SERVICE_QUERY);
		$smsrs = $adb->pquery('select status from vtiger_smsnotifier_status where smsmessageid=?', array($messageid));
		if ($smsrs && $adb->num_rows($smsrs)>0) {
			$stat = $smsrs->fields['status'];
			if ($stat==self::MSG_STATUS_DELIVERED) {
				$stat = self::MSG_STATUS_ERROR;
				$error = true;
			} else {
				$stat = self::MSG_STATUS_DELIVERED;
				$error = false;
			}
		} else {
			$stat = self::MSG_STATUS_ERROR;
			$error = true;
		}
		$result = array(
			'id' => $messageid,
			'action' => 'query',
			'where' => $serviceURL,
			'statusmessage' => 'CODE: '.($error ? 'all ok' : 'error'),
			'status' => $stat,
			'error' => $error,
			'needlookup' => 1,
		);
		$log->fatal($result);
		return $result;
	}
}
?>
