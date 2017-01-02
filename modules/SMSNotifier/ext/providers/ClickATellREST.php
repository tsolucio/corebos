<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/
include_once dirname(__FILE__) . '/../ISMSProvider.php';
include_once 'vtlib/Vtiger/Net/Client.php';

class ClickATellREST implements ISMSProvider {

	private $_username;
	private $_password;
	private $_parameters = array();
	public $helpURL = 'https://archive.clickatell.com/developers/2015/10/08/rest-api/';
	public $helpLink = 'ClickATell REST';

	const SERVICE_URI = 'https://platform.clickatell.com';
	private static $REQUIRED_PARAMETERS = array('api_id', 'from');

	function __construct() {
	}

	/**
	 * Function to get provider name
	 * @return <String> provider name
	 */
	public function getName() {
		return $this->helpLink;
	}

	public function setAuthParameters($username, $password) {
		$this->_username = $username;
		$this->_password = $password;
	}

	public function setParameter($key, $value) {
		$this->_parameters[$key] = $value;
	}

	public function getParameter($key, $defvalue = false) {
		if(isset($this->_parameters[$key])) {
			return $this->_parameters[$key];
		}
		return $defvalue;
	}

	public function getRequiredParams() {
		return self::$REQUIRED_PARAMETERS;
	}

	public function getServiceURL($type = false) {
		if($type) {
			switch(strtoupper($type)) {
				case self::SERVICE_AUTH: return  self::SERVICE_URI . '/';
				case self::SERVICE_SEND: return  self::SERVICE_URI . '/messages';
				case self::SERVICE_QUERY: return self::SERVICE_URI . '/message';
			}
		}
		return false;
	}

	protected function prepareParameters() {
		$params = array('user' => $this->_username, 'password' => $this->_password);
		foreach (self::$REQUIRED_PARAMETERS as $key) {
			$params[$key] = $this->getParameter($key);
		}
		return $params;
	}

	public function send($message, $tonumbers) {
		if(!is_array($tonumbers)) {
			$tonumbers = array($tonumbers);
		}

		$params = array();
		$params['content'] = $message;
		$params['to'] = $tonumbers;

		$serviceURL = $this->getServiceURL(self::SERVICE_SEND);
		$httpClient = new Vtiger_Net_Client($serviceURL);
		$httpClient->setHeaders(array(
			'Content-Type' => 'application/json',
			'Accept' => 'application/json',
			'X-Version' => 1,
			'Authorization' => $this->_parameters['api_id'],
		));
		$httpClient->setBody(json_encode($params));
		$response = $httpClient->doPost(false);
		$rsp = json_decode($response,true);
		global $log;$log->fatal($rsp);
		$results = array();
		if (empty($rsp['error'])) {
			$responseLines = $rsp['messages'];
			$i=0;
			foreach($responseLines as $responseLine) {
				if(!is_array($responseLine) || count($responseLine)==0) continue;

				$result = array( 'error' => false, 'statusmessage' => '' );
				if(empty($responseLine['error'])) {
					$result['id'] = $responseLine['apiMessageId'];
					$result['to'] = $responseLine['to'];
					$result['status'] = ($responseLine['accepted']==1 ? self::MSG_STATUS_DISPATCHED : $responseLine['accepted']);
				} else {
					$result['id'] = '';
					$result['status'] = $responseLine['accepted'];
					$result['error'] = true;
					$result['to'] = $responseLine['to'];
					$result['statusmessage'] = $responseLine['error']; // Complete error message
				}
				$results[] = $result;
			}
		}
		return $results;
	}

	public function query($messageid) {
		// This is done by push now so status cannot be queried and we return a standard compliant response
		return array(
			'error' => false,
			'needlookup' => 0,
			'status' => self::MSG_STATUS_DISPATCHED,
		);
	}
}
?>
