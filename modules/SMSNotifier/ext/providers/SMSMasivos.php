<?php
/*************************************************************************************************
 * Copyright 2016 JPL TSolucio, S.L. -- This file is a part of TSOLUCIO coreBOS Customizations.
 * Licensed under the vtiger CRM Public License Version 1.1 (the "License"); you may not use this
 * file except in compliance with the License. You can redistribute it and/or modify it
 * under the terms of the License. JPL TSolucio, S.L. reserves all rights not expressly
 * granted by the License. coreBOS distributed by JPL TSolucio S.L. is distributed in
 * the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied
 * warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. Unless required by
 * applicable law or agreed to in writing, software distributed under the License is
 * distributed on an "AS IS" BASIS, WITHOUT ANY WARRANTIES OR CONDITIONS OF ANY KIND,
 * either express or implied. See the License for the specific language governing
 * permissions and limitations under the License. You may obtain a copy of the License
 * at <http://corebos.org/documentation/doku.php?id=en:devel:vpl11>
 *************************************************************************************************/
include_once dirname(__FILE__) . '/../ISMSProvider.php';
include_once 'vtlib/Vtiger/Net/Client.php';

class SMSMasivos implements ISMSProvider {

	private $_username;
	private $_password;
	private $_parameters = array('numregion');
	public $helpURL = 'http://www.smasivos.com';
	public $helpLink = 'SMSMasivos';

	const SERVICE_URI = 'http://www.smasivos.com';
	private static $REQUIRED_PARAMETERS = array();

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

	public function getParameter($key, $defvalue = false)  {
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
				case self::SERVICE_AUTH: return  self::SERVICE_URI . '/http/auth';
				case self::SERVICE_SEND: return  self::SERVICE_URI . '/sms/api.envio.php';
				case self::SERVICE_QUERY: return self::SERVICE_URI . '/http/querymsg';
			}
		}
		return false;
	}

	protected function prepareParameters() {
		$params = array('usuario' => $this->_username, 'password' => $this->_password);
		foreach (self::$REQUIRED_PARAMETERS as $key) {
			$params[$key] = $this->getParameter($key);
		}
		return $params;
	}

	public function send($message, $tonumbers) {
		if(!is_array($tonumbers)) {
			$tonumbers = array($tonumbers);
		}

		foreach ($tonumbers as $key => $value) {
			if (substr($value, 0,3) == '044') $tonumbers[$key]=substr($value,3);
		}

		$results = array();
		foreach($tonumbers as $numcelular){
			$params = $this->prepareParameters();
			$params['mensaje'] = substr($message,0,160);
			$params['numcelular'] = $numcelular;
			$params['numregion'] = '52';
			$serviceURL = $this->getServiceURL(self::SERVICE_SEND);
			$httpClient = new Vtiger_Net_Client($serviceURL);
			$response = $httpClient->doPost($params);
			$responseobj = json_decode($response);
			$referencia = $responseobj->referencia;
			$responseLines = split("\n", $response);
			$i = 0;
			foreach($responseLines as $responseLine) {
				$responseLine = trim($responseLine);
				if(empty($responseLine)) continue;
				$result = array( 'error' => false, 'statusmessage' => '' );
				if(preg_match("/Error(.*)/", trim($responseLine), $matches)) {
					$result['error'] = true;
					$result['to'] = $numcelular;
					$i++;
					$result['statusmessage'] = $matches[0]; // Complete error message
				} else if(preg_match("/\"Estatus\":\"([^ ]+)\",\"referencia\":(.*)\}/", $responseLine, $matches)) {
					$result['id'] = trim($matches[2]);
					$result['to'] = trim($numcelular);
					$result['statusmessage'] = $matches[1];
					$result['status'] = self::MSG_STATUS_DISPATCHED;
				} else if(preg_match("/ID: (.*)/", $responseLine, $matches)) {
					$result['id'] = trim($matches[1]);
					$result['to'] = $numcelular;
					$result['status'] = self::MSG_STATUS_PROCESSING;
				}
				$results[] = $result;
			}
		}
		return $results;
	}

	public function query($messageid) {
		$params = $this->prepareParameters();
		$params['apimsgid'] = $messageid;

		$serviceURL = $this->getServiceURL(self::SERVICE_QUERY);
		$httpClient = new Vtiger_Net_Client($serviceURL);
		$response = $httpClient->doPost($params);

		$response = trim($response);

		$result = array( 'error' => false, 'needlookup' => 1 );

		if(preg_match("/ERR: (.*)/", $response, $matches)) {
			$result['error'] = true;
			$result['needlookup'] = 0;
			$result['statusmessage'] = $matches[0];
		} else if(preg_match("/ID: ([^ ]+) Status: ([^ ]+)/", $response, $matches)) {
			$result['id'] = trim($matches[1]);
			$status = trim($matches[2]);

			// Capture the status code as message by default.
			$result['statusmessage'] = "CODE: $status";

			if($status === '1') {
				$result['status'] = self::MSG_STATUS_PROCESSING;
			} else if($status === '2') {
				$result['status'] = self::MSG_STATUS_DISPATCHED;
				$result['needlookup'] = 0;
			}
		}
		return $result;
	}
}
?>
