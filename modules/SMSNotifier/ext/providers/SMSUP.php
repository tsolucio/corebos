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

class SMSUP implements ISMSProvider {

	private $username;
	private $password;
	private $parameters = array();
	public $helpURL = 'https://www.smsup.es';
	public $helpLink = 'SMSUp';

	const SERVICE_URI = 'https://api.gateway360.com/api/3.0';
	private static $REQUIRED_PARAMETERS = array('from', 'api_key');

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
			switch (strtoupper($type)) {
				case self::SERVICE_SEND:
					default:
					return  self::SERVICE_URI . '/sms/send';
			}
		}
		return false;
	}

	protected function prepareParameters() {
		$params = array('user' => $this->username, 'pwd' => $this->password);
		foreach (self::$REQUIRED_PARAMETERS as $key) {
			$params[$key] = $this->getParameter($key);
		}
		return $params;
	}

	public function send($message, $tonumbers) {
		$tonumbers = (array)$tonumbers;
		$tonumbers = ['+34647203750'];

		$params = $this->prepareParameters();
		$params['text'] = $message;
		$params['to'] = implode(',', $tonumbers);

        $sending = [
            'api_key' => $params['api_key'],
            'messages' => [
                [
                    'from' => $params['from'],
                    'to' => $params['to'],
                    'text' => $params['text']
                ]
                ],
            ];
		$serviceURL = $this->getServiceURL(self::SERVICE_SEND);
		$httpClient = new Vtiger_Net_Client($serviceURL);
		$httpClient->setHeaders(array(
			'Content-Type' => 'application/json',
			'Accept' => 'application/json',
			'X-Version' => 1,
        ));
		$httpClient->setBody(json_encode($sending));
		$response = $httpClient->doPost(false);
		$rsp = json_decode($response, true);
		$results = array();
		if ($rsp['status'] == 'ok') {
			$responseLines = $rsp['result'];
			foreach ($responseLines as $responseLine) {
				if (!is_array($responseLine) || empty($responseLine)) {
					continue;
				}

				$result = array( 'error' => false, 'statusmessage' => '' );
				if ($responseLine['status'] == 'ok') {
					$result['id'] = $responseLine['sms_id'];
					$result['to'] = $params['to'];
					$result['status'] = self::MSG_STATUS_DISPATCHED;
				} else {
					$result['id'] = '';
					$result['status'] = $responseLine['error_id'];
					$result['error'] = true;
					$result['to'] = $params['to'];
					$result['statusmessage'] = $responseLine['error_msg'];
				}
				$results[] = $result;
			}
		}
		return $results;
	}

	public function query($messageid) {
		// $params = $this->prepareParameters();
		// $params['apimsgid'] = $messageid;

		// $serviceURL = $this->getServiceURL(self::SERVICE_QUERY);
		// $httpClient = new Vtiger_Net_Client($serviceURL);
		// $response = $httpClient->doPost($params);
		// $response = trim($response);

		// $result = array( 'error' => false, 'needlookup' => 1 );

		// if (preg_match('/ERR: (.*)/', $response, $matches)) {
		// 	$result['error'] = true;
		// 	$result['needlookup'] = 0;
		// 	$result['statusmessage'] = $matches[0];
		// } elseif (preg_match("/ID: ([^ ]+) Status: ([^ ]+)/", $response, $matches)) {
		// 	$result['id'] = trim($matches[1]);
		// 	$status = trim($matches[2]);

		// 	// Capture the status code as message by default.
		// 	$result['statusmessage'] = "CODE: $status";

		// 	if ($status === '1') {
		// 		$result['status'] = self::MSG_STATUS_PROCESSING;
		// 	} elseif ($status === '2') {
		// 		$result['status'] = self::MSG_STATUS_DISPATCHED;
		// 		$result['needlookup'] = 0;
		// 	}
		// }
		// return $result;
	}
}
?>
