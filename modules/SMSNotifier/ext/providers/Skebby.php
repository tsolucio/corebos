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
include_once __DIR__ . '/../ISMSProvider.php';

define('NET_ERROR', 'Errore+di+rete+impossibile+spedire+il+messaggio');
define('SENDER_ERROR', 'Puoi+specificare+solo+un+tipo+di+mittente,+numerico+o+alfanumerico');

define('SMS_TYPE_CLASSIC', 'TI');
define('SMS_TYPE_CLASSIC_PLUS', 'GP');
define('SMS_TYPE_BASIC', 'SI');

/**
 *	Skebby Implementation on corebos plugin
 *
 * Type: classic OR classic_plus OR basic
 * Prefix: 39 for italy, is valid only for recipient, not sender
 * From: alphanumeric string (max 11 chars) or numeric sender
 *
 */
class Skebby implements ISMSProvider {

	private $userName;
	private $password;
	private $parameters = array();
	private $enableLogging = false;
	public $helpURL = 'https://www.skebby.com/';
	public $helpLink = 'Skebby';

	// Skebby gateway
	const SERVICE_URI = 'https://api.skebby.it/API/v1.0/REST/';
	private static $REQUIRED_PARAMETERS = array('Type','From','Prefix'); // parameters specific of Skebby

	/**
	 * Function to get provider name
	 * @return string provider name
	 */
	public function getName() {
		return $this->helpLink;
	}

	public function setAuthParameters($username, $password) {
		$this->userName = $username;
		$this->password = $password;
	}

	public function setParameter($key, $value) {
		$this->parameters[$key] = $value;
	}

	public function getParameter($key, $defaultValue = false) {
		if (isset($this->parameters[$key])) {
			return $this->parameters[$key];
		}
		return $defaultValue;
	}

	public function getRequiredParams() {
		return self::$REQUIRED_PARAMETERS;
	}

	public function getServiceURL($type = false) {
		if ($type) {
			switch (strtoupper($type)) {
				case self::SERVICE_AUTH:
					return self::SERVICE_URI . '/http/auth';
				case self::SERVICE_SEND:
					return self::SERVICE_URI . '/api/send/smseasy/advanced/http.php';
				case self::SERVICE_QUERY:
				default:
					return self::SERVICE_URI . '/http/querymsg';
			}
		}
		return false;
	}

	protected function prepareParameters() {
		$params = array('username' => $this->userName, 'password' => $this->password);
		foreach (self::$REQUIRED_PARAMETERS as $key) {
			$params[$key] = $this->getParameter($key);
		}
		return $params;
	}

	private function loginSkebby($username, $password) {
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, self::SERVICE_URI . 'login');
		curl_setopt($ch, CURLOPT_USERPWD, $username.':'.$password);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$response = curl_exec($ch);
		$info = curl_getinfo($ch);
		curl_close($ch);
		if ($info['http_code'] != 200) {
			return array(
				'errormessage' => $info['url'],
				'errorcode' => $info['http_code'],
			);
		}
		return explode(';', $response);
	}

	/**
	 * Sends an SMS message
	 */
	private function sendSMSSkebby($auth, $sendSMS) {
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, self::SERVICE_URI . 'sms');
		curl_setopt($ch, CURLOPT_HTTPHEADER, array(
			'Content-type: application/json',
			'user_key: ' . $auth[0],
			'Session_key: ' . $auth[1]
		));
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($sendSMS));
		$response = curl_exec($ch);
		curl_close($ch);
		return json_decode($response, true);
	}

	public function send($message, $recipients) {
		$recipients = (array)$recipients;
		$this->log('recipients: ' . print_r($recipients, true));
		$params = $this->prepareParameters();

		if ($params['Prefix'] && is_numeric($params['Prefix'])) {
			foreach ($recipients as $key => $value) {
				$finalRecipient = $params['Prefix'].$value;
				// strip leading 0 and +
				$finalRecipient = ltrim($finalRecipient, '0+');
				// strip all non numeric
				$finalRecipient = preg_replace('/[^0-9]+/', '', $finalRecipient);
				$recipients[$key] = $finalRecipient;
			}
		}

		$sender = $params['From'] ? $params['From'] : 'SMS';
		$smsType = $params['Type'] ? strtoupper($params['Type']) : 'GP';
		switch ($smsType) {
			case 'SI':
				$response = $this->skebbyGatewaySendSMS($params['username'], $params['password'], $recipients, $message, SMS_TYPE_BASIC, $sender);
				break;
			case 'TI':
			case 'GP':
			default:
				$typeToSend = 'TI' == $smsType ? SMS_TYPE_CLASSIC : SMS_TYPE_CLASSIC_PLUS;
				if (is_numeric($sender)) {
					// Invio SMS con mittente personalizzato di tipo numerico
					$response = $this->skebbyGatewaySendSMS($params['username'], $params['password'], $recipients, $message, $typeToSend, $sender);
				} else {
					// Invio SMS con mittente personalizzato di tipo alfanumerico
					$response = $this->skebbyGatewaySendSMS($params['username'], $params['password'], $recipients, $message, $typeToSend, '', $sender);
				}
				break;
		}

		$this->log('response: ' . print_r($response, true));
		$results = array();
		if (isset($response['errorcode'])) {
			$results[] = array(
				'status' => self::MSG_STATUS_FAILED,
				'error' => true,
				'statusmessage' => $response['errorcode'].' - '.$response['errormessage'],
			);
		} else {
			foreach ($recipients as $to) {
				$result = array('to' => $to);
				if ('OK' == $response['result']) {
					$result['id'] = $response['order_id'] ? $response['order_id'] : $to;
					$result['status'] = self::MSG_STATUS_DISPATCHED;
					$result['error'] = false;
					$result['statusmessage'] = 'Sent';
				} else {
					$result['status'] = self::MSG_STATUS_FAILED;
					$result['error'] = true;
					$result['statusmessage'] = $result['message'];
				}
				$results[] = $result;
			}
		}
		$this->log('results: ' . print_r($results, true));
		return $results;
	}

	public function query($messageid) {
		return array(
			'error' => false,
			'needlookup' => 1,
			'status' => self::MSG_STATUS_DISPATCHED,
			'needlookup' => 0,
		);
	}

	public function do_post_request($url, $data, $optional_headers = null) {
		if (!function_exists('curl_init')) {
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
			curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_TIMEOUT, 60);
			curl_setopt($ch, CURLOPT_USERAGENT, 'Generic Client');
			curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
			curl_setopt($ch, CURLOPT_URL, $url);

			if ($optional_headers !== null) {
				curl_setopt($ch, CURLOPT_HTTPHEADER, $optional_headers);
			}

			$response = curl_exec($ch);
			curl_close($ch);
			if (!$response) {
				return 'status=failed&message='.NET_ERROR;
			}
			return $response;
		}
	}

	public function skebbyGatewaySendSMS($username, $password, $recipients, $text, $sms_type = SMS_TYPE_CLASSIC, $sender_number = '', $sender_string = '', $user_reference = '', $charset = '', $optional_headers = null) {
		$auth = $this->loginSkebby($username, $password);
		$this->log('AUTH: '. print_r($auth, true));
		if (isset($auth['errorcode'])) {
			return $auth;
		}
		if ($sender_number != '' && $sender_string != '') {
			return array(
				'errormessage' => SENDER_ERROR,
				'errorcode' => 'failed',
			);
		}
		if (!in_array($sms_type, array(SMS_TYPE_CLASSIC_PLUS, SMS_TYPE_BASIC, SMS_TYPE_CLASSIC))) {
			$sms_type = SMS_TYPE_CLASSIC;
		}
		$msg = array(
			'message' => $text,
			'message_type' => $sms_type,
			'returnCredits' => true,
			'recipient' => $recipients,
			'sender' => ($sender_string != '' ? urlencode($sender_string) : ''),
			'encoding' => 'UCS2',
		);
		$this->log('MESSAGE: '. print_r($msg, true));
		return $this->sendSMSSkebby($auth, $msg);
	}

	public function skebbyGatewayGetCredit($username, $password, $charset = '') {
		$url = $this->getServiceUrl(self::SERVICE_SEND);
		$method = 'get_credit';
		$params = 'method='.urlencode($method).'&username='.urlencode($username).'&password='.urlencode($password);
		if ($charset == 'UTF-8') {
			$params .= '&charset='.urlencode('UTF-8');
		}
		parse_str($this->do_post_request($url, $params), $result);
		return $result;
	}

	public function log($text) {
		if ($this->enableLogging) {
			$fileName = 'logs/skebby.log';
			$fp = fopen($fileName, 'a+');
			if ($fp) {
				flock($fp, LOCK_EX);
				fwrite($fp, $text . "\n");
				flock($fp, LOCK_UN);
				fclose($fp);
			}
		}
	}
}
