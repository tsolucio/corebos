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

class smsFactor implements ISMSProvider
{
	private $_username;
	private $_password;
	private $_parameters = array();
	public $helpURL = 'https://www.smsfactor.com/';
	public $helpLink = 'smsFactor';

	const SERVICE_URI = 'http://gateway.oms-smsfactor.com/httpservice.aspx';
	private static $REQUIRED_PARAMETERS = array('Originator');

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
				case self::SERVICE_AUTH: return  self::SERVICE_URI . '';
				case self::SERVICE_SEND: return  self::SERVICE_URI . '?method=SendSMS&';
				case self::SERVICE_QUERY: return self::SERVICE_URI . '?method=GetSMSStatus&';
			}
		}
		return false;
	}

	public function send($message, $tonumbers) {
		if(!is_array($tonumbers)) {
			$tonumbers = array($tonumbers);
		}
		
		$tonumbers = $this->cleanNumbers($tonumbers);
		$clientMessageReference = $this->generateClientMessageReference();
		$response = $this->sendMessage($clientMessageReference, $message, $tonumbers);
		return $this->processSendMessageResult($response, $clientMessageReference, $tonumbers);
	}

	public function query($messageid) {
		$messageidSplit = split('--', $messageid);
		$clientMessageReference = trim($messageidSplit[0]);
		$number = trim($messageidSplit[1]);
		$response = $this->queryMessage($clientMessageReference);
		return $this->processQueryMessageResult($response, $number);
	}

	private function cleanNumbers($numbers) {
		$pattern = '/[^\+\d]/';
		$replacement = '';
		return preg_replace($pattern, $replacement, $numbers);
	}

	private function generateClientMessageReference() {
		return uniqid();
	}

	private function validEmail($email) {
		$isValid = true;
		$atIndex = strrpos($email, "@");
		if (is_bool($atIndex) && !$atIndex) {
			$isValid = false;
		} else {
			$domain = substr($email, $atIndex+1);
			$local = substr($email, 0, $atIndex);
			$localLen = strlen($local);
			$domainLen = strlen($domain);
			if ($localLen < 1 || $localLen > 64) {
				// local part length exceeded
				$isValid = false;
			} else if ($domainLen < 1 || $domainLen > 255) {
				// domain part length exceeded
				$isValid = false;
			} else if ($local[0] == '.' || $local[$localLen-1] == '.') {
				// local part starts or ends with '.'
				$isValid = false;
			} else if (preg_match('/\\.\\./', $local)) {
				// local part has two consecutive dots
				$isValid = false;
			} else if (!preg_match('/^[A-Za-z0-9\\-\\.]+$/', $domain)) {
				// character not valid in domain part
				$isValid = false;
			} else if (preg_match('/\\.\\./', $domain)) {
				// domain part has two consecutive dots
				$isValid = false;
			} else if (!preg_match('/^(\\\\.|[A-Za-z0-9!#%&`_=\\/$\'*+?^{}|~.-])+$/', str_replace("\\\\","",$local))) {
				// character not valid in local part unless 
				// local part is quoted
				if (!preg_match('/^"(\\\\"|[^"])+"$/', str_replace("\\\\","",$local))) {
					$isValid = false;
				}
			}
		}
		return $isValid;
	}

	private function getReplyMethodID($originator)
	{
		if (substr($originator, 0, 1) === '+' && is_numeric(substr($originator, 1)))
		{
			return 4;
		}
		else if ($this->validEmail($originator))
		{
			return 2;
		}
		else
		{
			return 1;
		}
	}

	private function sendMessage($clientMessageReference, $message, $tonumbers)
	{
		$originator = $this->getParameter('Originator', '');
		$replyMethodID = $this->getReplyMethodID($originator);
		
		$replyData = '';
		if ($replyMethodID == 2)
		{
			$replyData = $originator;
			$originator = '';
		}

		$characterSetID = $this->getParameter('CharacterSet', '2');

		$current_user = new Users();
		$current_user->retrieveCurrentUserInfoFromFile($_SESSION['authenticated_user_id']);

		$serviceURL = $this->getServiceURL(self::SERVICE_SEND);
		$serviceURL = $serviceURL . 'returnCSVString='			. 'true' . '&';
		$serviceURL = $serviceURL . 'externalLogin=' 			. urlencode($this->_username) . '&';
		$serviceURL = $serviceURL . 'password='					. urlencode($this->_password) . '&';
		$serviceURL = $serviceURL . 'clientBillingReference='	. urlencode('vTiger' . '-'. $current_user->user_name) . '&';
		$serviceURL = $serviceURL . 'clientMessageReference='	. urlencode($clientMessageReference) . '&';
		$serviceURL = $serviceURL . 'originator='				. urlencode($originator) . '&';
		$serviceURL = $serviceURL . 'body='						. urlencode(html_entity_decode($message)) . '&';
		$serviceURL = $serviceURL . 'destinations='				. urlencode(implode(',',  $tonumbers)) . '&';
		$serviceURL = $serviceURL . 'validity='					. urlencode('72') . '&';
		$serviceURL = $serviceURL . 'characterSetID='			. urlencode($characterSetID) . '&';
		$serviceURL = $serviceURL . 'replyMethodID='			. urlencode($replyMethodID) . '&'; 
		$serviceURL = $serviceURL . 'replyData='				. urlencode($replyData) . '&';
		$serviceURL = $serviceURL . 'statusNotificationUrl=';

		$httpClient = new Vtiger_Net_Client($serviceURL);
		return $httpClient->doPost(array());
	}

	private function processSendMessageResult($response, $clientMessageReference, $tonumbers)
	{
		$results = array();

		$responseLines = split("\n", $response);

		if(trim($responseLines[0]) === '#1#')
		{
			//Successful transaction
			$numberResults = split(",", $responseLines[1]);
			foreach($numberResults as $numberResult)
			{
				$numberResultSplit = split(":", $numberResult);
				$number = trim($numberResultSplit[0]);
				$code = trim($numberResultSplit[1]);

				$result = array();

				if ($code != '1')
				{
					$result['error'] = true; 
					$result['statusmessage'] = $code;
					$result['to'] = $number;
				}
				else
				{
					$result['error'] = false;
					$result['id'] = $clientMessageReference . '--' . $number;
					$result['status'] = self::MSG_STATUS_PROCESSING;
					$result['statusmessage'] = $code;
					$result['to'] = $number;
				}
				$results[] = $result;
			}
		}
		else
		{
			//Transaction failed
			foreach($tonumbers as $number)
			{
				$result = array('error' => true, 'statusmessage' => $responseLines[0], 'to' => $number);
				$results[] = $result;
			}
		}
		
		return $results;
	}

	private function queryMessage($clientMessageReference)
	{
		$serviceURL = $this->getServiceURL(self::SERVICE_QUERY);
		$serviceURL = $serviceURL . 'returnCSVString='			. 'true' . '&';
		$serviceURL = $serviceURL . 'externalLogin=' 			. urlencode($this->_username) . '&';
		$serviceURL = $serviceURL . 'password='					. urlencode($this->_password) . '&';
		$serviceURL = $serviceURL . 'clientMessageReference='	. urlencode($clientMessageReference);

		$httpClient = new Vtiger_Net_Client($serviceURL);
		return $httpClient->doPost(array());
	}

	private function processQueryMessageResult($response, $number)
	{
		$result = array();

		$responseLines = split("\n", $response);

		if(trim($responseLines[0]) === '#1#')
		{
			//Successful transaction
			$numberResults = split(",", $responseLines[1]);
			foreach($numberResults as $numberResult)
			{
				$numberResultSplit = split(":", $numberResult);
				$thisNumber = trim($numberResultSplit[0]);
				$code = (int)trim($numberResultSplit[1]);

				if ($thisNumber != $number)
					continue;
				
				if($code >= 400 && $code <= 499) {
					$result['error'] = false;
					$result['status'] = self::MSG_STATUS_DELIVERED;
					$result['needlookup'] = 0;
					$result['statusmessage'] = $code;
				}
				else if ($code >= 500 && $code <= 599) {
					$result['error'] = false;
					$result['status'] = self::MSG_STATUS_FAILED;
					$result['needlookup'] = 0;
					$result['statusmessage'] = $code;
				}
				else if ($code >= 600 && $code <= 699) {
					$result['error'] = false;
					$result['status'] = self::MSG_STATUS_DISPATCHED;
					$result['needlookup'] = 1;
					$result['statusmessage'] = $code;
				}
				break;
			}
		}
		else
		{
			//Transaction failed
			$result['error'] = true;
			$result['needlookup'] = 1;
			$result['statusmessage'] = $responseLines[0];
		}
		return $result;
	}
}
?>
