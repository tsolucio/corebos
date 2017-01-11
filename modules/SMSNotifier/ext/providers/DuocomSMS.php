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

class DuocomSMS implements ISMSProvider {

	private $_username;
	private $_password;
	private $_parameters = array();
	public $helpURL = 'https://www.telefacil.com/wiki/index.php/Integraci%C3%B3n_con_Mensajer%C3%ADa_SMS_(SMSNotifier)';
	public $helpLink = 'TeleFacil';

	const SERVICE_URI = 'https://scgi.duocom.es/cgi-bin/telefacil2/apisms';
	private static $REQUIRED_PARAMETERS = array('mascara');

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
				//case self::SERVICE_SEND: return  self::SERVICE_URI . '/http/sendmsg';
				case self::SERVICE_SEND: return  self::SERVICE_URI;
				case self::SERVICE_QUERY: return self::SERVICE_URI . '/http/querymsg';
			}
		}
		return false;
	}

	protected function prepareParameters() {
		$params = array('user' => $this->_username, 'pwd' => $this->_password);
		foreach (self::$REQUIRED_PARAMETERS as $key) {
			$params[$key] = $this->getParameter($key);
		}
		return $params;
	}

	public function send($message, $tonumbers) {
		if(!is_array($tonumbers)) {
			$tonumbers = array($tonumbers);
		}

		$params = $this->prepareParameters();
		$params['mensaje'] = $message;
		$params['accion'] = 'enviar';
		$params['principal'] = $this->_username;
		$params['pin'] = $this->_password;

		if (count($tonumbers) <= 5) { //envio normal
			for ($i = 1; $i <= count($tonumbers); $i++)
				$params['movil'.$i] = $tonumbers[$i-1];
		}
		else  //envio masivo
			$params['moviles'] = implode('_', $tonumbers);

		$serviceURL = $this->getServiceURL(self::SERVICE_SEND);
		$httpClient = new Vtiger_Net_Client($serviceURL);
		$response = $httpClient->doPost($params);

		$responseLines = split("\n", $response);

		$results = array();
		foreach($responseLines as $responseLine) {
			$responseLine = trim($responseLine);
			if(empty($responseLine)) continue;

			$result = array( 'error' => false, 'statusmessage' => '' );
			if(preg_match("/ERROR:(.*)/", trim($responseLine), $matches)) {
				$result['error'] = true;
				if (count($tonumbers) > 1)
					$result['to'] = $tonumbers[0]."...";
				else
					$result['to'] = $tonumbers[0];
				$result['statusmessage'] = $matches[0]; // Complete error message
				$result['status'] = self::MSG_STATUS_FAILED;
			} else if (strcmp($responseLine, "1") == 0) {
				$result['error'] = false;
				$result['status'] = self::MSG_STATUS_DELIVERED;
				if (count($tonumbers) > 1)
					$result['to'] = $tonumbers[0]."...";
				else
					$result['to'] = $tonumbers[0];
				$result['statusmessage'] = 'OK';
				$result['id'] = '1';
			} else {
				$result['error'] = true;
				$result['to'] = implode(',', $tonumbers);
				$result['statusmessage'] = 'No enviado';//$matches[0]; // Complete error message
			}
			$results[] = $result;
		}
		return $results;
	}

	public function query($messageid) {
		// No query support so we return a standard compliant response
		return array(
			'error' => false,
			'needlookup' => 0,
			'status' => self::MSG_STATUS_DISPATCHED,
		);
	}
}
?>
