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
class Versatile implements ISMSProvider {

	private $userName;
	private $password;
	private $parameters = array();
	public $helpURL = 'https://versatilesmshub.com/';
	public $helpLink = 'Versatile';

	const SERVICE_URI = 'https://sms.versatilesmshub.com/api/mt';
	private static $REQUIRED_PARAMETERS = array('senderid', 'channel', 'DCS', 'flashsms', 'route');

	/**
	 * Function to get provider name
	 * @return string provider name
	 */
	public function getName() {
		return $this->helpLink;
	}

	/**
	 * Function to set authentication parameters
	 * @param string $userName
	 * @param string $password
	 */
	public function setAuthParameters($userName, $password) {
		$this->userName = $userName;
		$this->password = $password;
	}

	/**
	 * Function to set non-auth parameter.
	 * @param string $key
	 * @param string $value
	 */
	public function setParameter($key, $value) {
		$this->parameters[$key] = $value;
	}

	/**
	 * Function to get parameter value
	 * @param string $key
	 * @param string $defaultValue
	 * @return string value/$default value
	 */
	public function getParameter($key, $defaultValue = false) {
		if (isset($this->parameters[$key])) {
			return $this->parameters[$key];
		}
		return $defaultValue;
	}

	/**
	 * Function to get required parameters other than (userName, password)
	 * @return array required parameters list
	 */
	public function getRequiredParams() {
		return self::$REQUIRED_PARAMETERS;
	}

	/**
	 * Function to get service URL to use for a given type
	 * @param string $type like SEND, PING, QUERY
	 */
	public function getServiceURL($type = false) {
		if ($type) {
			switch (strtoupper($type)) {
				case self::SERVICE_AUTH:
					return  self::SERVICE_URI . '/http/auth';
				case self::SERVICE_SEND:
					return  self::SERVICE_URI . '/SendSMS';
				case self::SERVICE_QUERY:
				default:
					return self::SERVICE_URI . '/http/querymsg';
			}
		}
		return false;
	}

	/**
	 * Function to prepare parameters
	 * @return array parameters
	 */
	protected function prepareParameters() {
		$params = array('user' => $this->userName, 'password' => $this->password);
		foreach (self::$REQUIRED_PARAMETERS as $key) {
			$params[$key] = $this->getParameter($key);
		}
		return $params;
	}

	/**
	 * Function to handle SMS Send operation
	 * @param string $message
	 * @param mixed $toNumbers One or Array of numbers
	 */
	public function send($message, $toNumbers) {
		$toNumbers = (array)$toNumbers;

		$params = $this->prepareParameters();
		$params['text'] = $message;
		$params['number'] = implode(',', $toNumbers);

		$serviceURL = $this->getServiceURL(self::SERVICE_SEND);
		$httpClient = new Vtiger_Net_Client($serviceURL);
		$response = $httpClient->doPost($params);
		$responseLines = explode("\n", $response);

		$results = array();
		foreach ($responseLines as $responseLine) {
			$responseLine = trim($responseLine);
			if (empty($responseLine)) {
				continue;
			}

			$result = array( 'error' => false, 'statusmessage' => '' );
			if (preg_match("/ERR:(.*)/", trim($responseLine), $matches)) {
				$result['error'] = true;
				if (count($toNumbers) > 1) {
					$result['to'] = $toNumbers[0].'...';
				} else {
					$result['to'] = $toNumbers[0];
				}
				$result['statusmessage'] = $matches[0]; // Complete error message
			} elseif (preg_match("/ID: ([^ ]+)TO:(.*)/", $responseLine, $matches)) {
				$result['id'] = trim($matches[1]);
				$result['to'] = trim($matches[2]);
				$result['status'] = self::MSG_STATUS_PROCESSING;
			} elseif (preg_match("/ID: (.*)/", $responseLine, $matches)) {
				$result['id'] = trim($matches[1]);
				$result['to'] = $toNumbers[0];
				$result['status'] = self::MSG_STATUS_PROCESSING;
			}
			$results[] = $result;
		}
		return $results;
	}

	/**
	 * Function to get query for status using messgae id
	 * @param integer $messageId
	 */
	public function query($messageId) {
		$params = $this->prepareParameters();
		$params['apimsgid'] = $messageId;

		$serviceURL = $this->getServiceURL(self::SERVICE_QUERY);
		$httpClient = new Vtiger_Net_Client($serviceURL);
		$response = $httpClient->doPost($params);
		$response = trim($response);

		$result = array( 'error' => false, 'needlookup' => 1 );

		if (preg_match("/ERR: (.*)/", $response, $matches)) {
			$result['error'] = true;
			$result['needlookup'] = 0;
			$result['statusmessage'] = $matches[0];
		} elseif (preg_match("/ID: ([^ ]+) Status: ([^ ]+)/", $response, $matches)) {
			$result['id'] = trim($matches[1]);
			$status = trim($matches[2]);

			// Capture the status code as message by default.
			$result['statusmessage'] = "CODE: $status";
			if ($status === '1') {
				$result['status'] = self::MSG_STATUS_PROCESSING;
			} elseif ($status === '2') {
				$result['status'] = self::MSG_STATUS_DISPATCHED;
				$result['needlookup'] = 0;
			}
		}
		return $result;
	}
}
?>
