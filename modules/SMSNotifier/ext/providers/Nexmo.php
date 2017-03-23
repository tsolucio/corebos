<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 * modified by crm-now  www.crm-now.com
 ************************************************************************************/
include_once dirname(__FILE__) . '/../ISMSProvider.php';
include_once 'vtlib/Vtiger/Net/Client.php';

class Nexmo implements ISMSProvider {

	private $_username;
	private $_password;
	private $_parameters = array();
	public $helpURL = 'https://www.nexmo.com/';
	public $helpLink = 'nexmo';

	const SERVICE_URI = 'https://rest.nexmo.com';
	private static $REQUIRED_PARAMETERS = array('api_key', 'api_secret','from');

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
				case self::SERVICE_AUTH: return  self::SERVICE_URI . '/sms/json';
				case self::SERVICE_SEND: return  self::SERVICE_URI . '/sms/json';
				case self::SERVICE_QUERY: return self::SERVICE_URI . '/search/message';
			}
		}
		return false;
	}

	protected function prepareParameters() {
		//$params = array('user' => $this->_username, 'pwd' => $this->_password);
		$params = array();
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
		$params['text'] = $message;
		//Nexmo's SMS API can only accept one message per request.
		foreach ($tonumbers	as $to_key => $to_number) {
			if (trim($to_number) !='') {
				$params['to'] = $to_number;
				$url = 'https://rest.nexmo.com/sms/json?' . http_build_query($params);
				$ch = curl_init($url);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
				$response = curl_exec($ch);
				$response_arr[] = get_object_vars(json_decode( $response ));
			}
		}
		$results = array();

		foreach($response_arr as $message_response) {
			$message_content = get_object_vars($message_response['messages'][0]);
			//if(empty($message_response)) continue;
			$result = array( 'error' => false, 'statusmessage' => '' );
			if(isset ($message_content['error-text'])) {
				$result['error'] = true; 
				$result['to'] =  $message_content['to'];
				$result['statusmessage'] = $message_content['messages'][0]['error-text'];
			} 
			else {
				$result['id'] = $message_content['message-id'];
				$result['to'] = $message_content['to'];
				$result['status'] = self::MSG_STATUS_DISPATCHED;
			}
			$results[] = $result;
		}
		return $results;
	}

	public function query($messageid) {

		$params = $this->prepareParameters();
		$url = 'https://rest.nexmo.com/search/message/'.$params['api_key'].'/'.$params['api_secret'].'/'.$messageid;
		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$response = curl_exec($ch);
		$response_obj = json_decode( $response );
		$message_arr = get_object_vars($response_obj);
		$result = array( 'error' => false, 'needlookup' => 1 );
		//the following is not perfect and needs a rework
		if(isset ($message_arr['error-text'])) {
			$result['error'] = true; 
			$result['needlookup'] = 0;
			$result['statusmessage'] = trim($message_arr['final-status']);
		} 
		else  {
			if (isset ($message_arr['final-status'])) {
				$result['status'] = self::MSG_STATUS_DISPATCHED;
				$result['needlookup'] = 0;
			}
			else {
				if($message_arr['status'] == 'BUFFERED' ) {
					$result['status'] = self::MSG_STATUS_PROCESSING;
				} 
				else if($status == 'DELIVRD') {
					$result['status'] = self::MSG_STATUS_DISPATCHED;
					$result['needlookup'] = 0;
				} 
				else {
					$result['status'] = self::MSG_STATUS_DISPATCHED;
					$result['needlookup'] = 0;
				}
			}
		}

		return $result;
	}
}
?>
