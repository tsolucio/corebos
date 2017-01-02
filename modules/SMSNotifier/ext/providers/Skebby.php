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

//include_once 'vtlib/Vtiger/Net/Client.php'; // not used

define('NET_ERROR', 'Errore+di+rete+impossibile+spedire+il+messaggio');
define('SENDER_ERROR', 'Puoi+specificare+solo+un+tipo+di+mittente,+numerico+o+alfanumerico');

define ('SMS_TYPE_CLASSIC', 'classic');
define ('SMS_TYPE_CLASSIC_PLUS', 'classic_plus');
define ('SMS_TYPE_BASIC', 'basic');

/**
 *	Skebby Implementation on corebos plugin
 *
 * Type: classic OR classic_plus OR basic
 * Prefix: 39 for italy, is valid only for recipient, not sender
 * From: alphanumeric string (max 11 chars) or numeric sender
 *
 */
class Skebby implements ISMSProvider {

	private $_username;
	private $_password;
	private $_parameters = array();
	public $helpURL = 'http://www.skebby.com/';
	public $helpLink = 'Skebby';

	private $_enableLogging = false;
	
	// Skebby gateway
	const SERVICE_URI = 'http://gateway.skebby.it/'; 
	private static $REQUIRED_PARAMETERS = array('Type','From','Prefix'); // parameters specific of Skebby

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
				case self::SERVICE_AUTH: return  self::SERVICE_URI . '/unsupported/vtiger/auth';
				case self::SERVICE_SEND: return  self::SERVICE_URI . '/api/send/smseasy/advanced/http.php';
				case self::SERVICE_QUERY: return self::SERVICE_URI . '/unsupported/vtiger/querymsg';
			}
		}
		return false;
	}

	protected function prepareParameters() { 
		$params = array('username' => $this->_username, 'password' => $this->_password);
		foreach (self::$REQUIRED_PARAMETERS as $key) {
			$params[$key] = $this->getParameter($key);
		}
		return $params;
	}

	public function send($message, $recipients) {
		if(!is_array($recipients)) {
			$recipients = array($recipients);
		}

		$params = $this->prepareParameters();

		if($params['Prefix'] && is_numeric($params['Prefix'])){
			foreach($recipients as $key => $value){
				$finalRecipient = $params['Prefix'].$value;
				// strip leading 0 and +
				$finalRecipient = ltrim($finalRecipient, '0+');
				// strip all non numeric 
				$finalRecipient = preg_replace('/[^0-9]+/', '', $finalRecipient);
				$recipients[$key] = $finalRecipient;
			}
		}

		$smsType = $params['Type'] ? strtolower($params['Type']) : 'classic_plus';

		switch($smsType){
			case 'basic':
				$response = $this->skebbyGatewaySendSMS($params['username'],$params['password'],$recipients, $message, SMS_TYPE_BASIC);
			break;
			case 'classic':
			case 'classic_plus':
			default:
				$typeToSend = 'classic' == $smsType ? SMS_TYPE_CLASSIC : SMS_TYPE_CLASSIC_PLUS;
				$sender = $params['From'] ? $params['From'] : 'SMS';

				if (is_numeric($sender)){
					// Invio SMS con mittente personalizzato di tipo numerico
					$response = $this->skebbyGatewaySendSMS($params['username'],$params['password'],$recipients,$message, $typeToSend,$sender);
				}
				else{
					// Invio SMS con mittente personalizzato di tipo alfanumerico
					$response = $this->skebbyGatewaySendSMS($params['username'],$params['password'],$recipients,$message, $typeToSend,'',$sender);
				}
			break;
		}

		$this->log('response: ' . print_r($response, true));

		$results = array();
		foreach($recipients as $to) {
			$result = array(  'to' => $to );
			if('success' == $response['status']){
				$result['id'] = $response['id'] ? $response['id'] : $to;
				$result['status'] = self::MSG_STATUS_DISPATCHED;
				$result['error'] = false;
				$result['statusmessage'] = 'Sent';
			}
			else{
				$result['status'] = self::MSG_STATUS_FAILED;
				$result['error'] = true;
				$result['statusmessage'] = $result['message'];
			}
			$results[] = $result;
		}

		return $results;
	}

	public function query($messageid) {
		$result = array( 'error' => false, 'needlookup' => 1 );
		$result['status'] = self::MSG_STATUS_DISPATCHED;
		$result['needlookup'] = 0;
		return $result;
	}

	function do_post_request($url, $data, $optional_headers = null){
		if(!function_exists('curl_init')) {
			$params = array(
					'http' => array(
							'method' => 'POST',
							'content' => $data
					)
			);
			if ($optional_headers !== null) {
				$params['http']['header'] = $optional_headers;
			}
			$ctx = stream_context_create($params);
			$fp = @fopen($url, 'rb', false, $ctx);
			if (!$fp) {
				return 'status=failed&message='.NET_ERROR;
			}
			$response = @stream_get_contents($fp);
			if ($response === false) {
				return 'status=failed&message='.NET_ERROR;
			}
			return $response;
		} else {
			$ch = curl_init();
			curl_setopt($ch,CURLOPT_CONNECTTIMEOUT,10);
			curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
			curl_setopt($ch,CURLOPT_TIMEOUT,60);
			curl_setopt($ch,CURLOPT_USERAGENT,'Generic Client');
			curl_setopt($ch,CURLOPT_POSTFIELDS,$data);
			curl_setopt($ch,CURLOPT_URL,$url);
	
			if ($optional_headers !== null) {
				curl_setopt($ch,CURLOPT_HTTPHEADER,$optional_headers);
			}

			$response = curl_exec($ch);
			curl_close($ch);
			if(!$response){
				return 'status=failed&message='.NET_ERROR;
			}
			return $response;
		}
	}

	function skebbyGatewaySendSMS($username,$password,$recipients,$text,$sms_type=SMS_TYPE_CLASSIC,$sender_number='',$sender_string='',$user_reference='',$charset='',$optional_headers=null) {
		$url = $this->getServiceUrl(self::SERVICE_SEND);

		switch($sms_type) {
			case SMS_TYPE_CLASSIC:
			default:
				$method='send_sms_classic';
				break;
			case SMS_TYPE_CLASSIC_PLUS:
				$method='send_sms_classic_report';
				break;
			case SMS_TYPE_BASIC:
				$method='send_sms_basic';
				break;
		}

		$parameters = 'method='
			.urlencode($method).'&'
			.'username='.urlencode($username).'&'
			.'password='.urlencode($password).'&'
			.'text='.urlencode($text).'&'
			.'recipients[]='.implode('&recipients[]=',$recipients);
			
		if($sender_number != '' && $sender_string != '') {
			parse_str('status=failed&message='.SENDER_ERROR,$result);
			return $result;
		}
		$parameters .= $sender_number != '' ? '&sender_number='.urlencode($sender_number) : '';
		$parameters .= $sender_string != '' ? '&sender_string='.urlencode($sender_string) : '';

		$parameters .= $user_reference != '' ? '&user_reference='.urlencode($user_reference) : '';

		switch($charset) {
			case 'UTF-8':
				$parameters .= '&charset='.urlencode('UTF-8');
				break;
			case '':
			case 'ISO-8859-1':
			default:
				break;
		}
	
		$this->log('request'. $parameters);

		parse_str($this->do_post_request($url,$parameters,$optional_headers),$result);

		return $result;
	}

	function skebbyGatewayGetCredit($username,$password,$charset=''){
		$url = $this->getServiceUrl(self::SERVICE_SEND);
		$method = 'get_credit';
	
		$parameters = 'method='.urlencode($method).'&'
			.'username='.urlencode($username).'&'
			.'password='.urlencode($password);
	
		switch($charset) {
			case 'UTF-8':
				$parameters .= '&charset='.urlencode('UTF-8');
				break;
			default:
		}
	
		parse_str($this->do_post_request($url,$parameters),$result);
		return $result;
	}

	function log($text){
		if($this->enableLogging){
			$fileName = '/tmp/skebby_logging.txt';
			$fp = fopen($fileName, 'a+');
			if($fp){
				flock($fp, LOCK_EX);
				fwrite($fp, $text . "\n");
				flock($fp, LOCK_UN);
				fclose($fp);
			}
		}
	}

}
