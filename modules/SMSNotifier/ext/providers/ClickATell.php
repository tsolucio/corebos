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

class ClickATell implements ISMSProvider {

	private $_username;
	private $_password;
	private $_parameters = array();
	public $helpURL = 'https://archive.clickatell.com/developers/2015/10/08/http/s/';
	public $helpLink = 'ClickATell HTTP';

	const SERVICE_URI = 'http://api.clickatell.com';
	private static $REQUIRED_PARAMETERS = array('api_id', 'from', 'mo');

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
				case self::SERVICE_AUTH: return  self::SERVICE_URI . '/http/auth';
				case self::SERVICE_SEND: return  self::SERVICE_URI . '/http/sendmsg';
				case self::SERVICE_QUERY: return self::SERVICE_URI . '/http/querymsg';
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

		$params = $this->prepareParameters();
		$params['text'] = $message;
		$params['to'] = implode(',', $tonumbers);

		$serviceURL = $this->getServiceURL(self::SERVICE_SEND);
		$httpClient = new Vtiger_Net_Client($serviceURL);
		$response = $httpClient->doPost($params);
		$responseLines = explode("\n", $response);

		$results = array();
		$i=0;
		foreach($responseLines as $responseLine) {

			$responseLine = trim($responseLine);
			if(empty($responseLine)) continue;

			$result = array( 'error' => false, 'statusmessage' => '' );
			if(preg_match("/ERR:(.*)/", trim($responseLine), $matches)) {
				$result['error'] = true;
				$result['to'] = $tonumbers[$i++];
				$result['statusmessage'] = $matches[0]; // Complete error message
			} else if(preg_match("/ID: ([^ ]+)TO:(.*)/", $responseLine, $matches)) {
				$result['id'] = trim($matches[1]);
				$result['to'] = trim($matches[2]);
				$result['status'] = self::MSG_STATUS_PROCESSING;
			} else if(preg_match("/ID: (.*)/", $responseLine, $matches)) {
				$result['id'] = trim($matches[1]);
				$result['to'] = $tonumbers[0];
				$result['status'] = self::MSG_STATUS_PROCESSING;
			}
			$results[] = $result;
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

		$result = array( 'error' => false, 'needlookup' => 1, 'statusmessage' => '' );

		if(preg_match("/ERR: (.*)/", $response, $matches)) {
			$result['error'] = true;
			$result['needlookup'] = 0;
			$result['statusmessage'] = $matches[0];
		} else if(preg_match("/ID: ([^ ]+) Status: ([^ ]+)/", $response, $matches)) {
			$result['id'] = trim($matches[1]);
			$status = trim($matches[2]);

			// Capture the status code as message by default.
			$result['statusmessage'] = "CODE: $status";

			if($status == '002' || $status == '008' || $status == '011' ) {
				$result['status'] = self::MSG_STATUS_PROCESSING;
			} else if($status == '003' || $status == '004') {
				$result['status'] = self::MSG_STATUS_DISPATCHED;
				$result['needlookup'] = 0;
			} else {
				$statusMessage = "";
				switch($status) {
				case '001': $statusMessage = 'Message unknown';                 $needlookup = 0; break;
				case '005': $statusMessage = 'Error with message';              $needlookup = 0; break;
				case '006': $statusMessage = 'User cancelled message delivery'; $needlookup = 0; break;
				case '007': $statusMessage = 'Error delivering message';        $needlookup = 0; break;
				case '009': $statusMessage = 'Routing error';                   $needlookup = 0; break;
				case '010': $statusMessage = 'Message expired';                 $needlookup = 0; break;
				case '012': $statusMessage = 'Out of credit';                   $needlookup = 0; break;
				}
				if(!empty($statusMessage)) {
					$result['error'] = true;
					$result['needlookup'] = $needlookup;
					$result['statusmessage'] = $statusmessage;
				}
			}
		}
		return $result;
	}

	/**
	* Function to handle UTF-8 Check and conversion
	* @author Nuri Unver
	*/
	public function smstxtcode($data) {
		$mb_hex = '';
		$utf = 0;
		for($i = 0; $i < strlen ( $data ); $i ++) {
			$c = mb_substr ( $data, $i, 1, 'UTF-8' );
			$o = unpack ( 'N', mb_convert_encoding ( $c, 'UCS-4BE', 'UTF-8' ) );
			$hx = sprintf ( '%04X', $o [1] );
			$utf += intval ( substr ( $hx, 0, 2 ) );
			$mb_hex .= $hx;
		}
		if ($utf > 0) {
			$return = $mb_hex;
			$utf = 1;
		} else {
			$return = utf8_decode ( $data );
			$utf = 0;
		}
		return array (
			$utf,
			$return
		);
	}

}

/*
 * On vtiger CRM forum: Nuri Unver February 2014

For those who need to send UTF-8 messages. The ClickATell extension, as it is, just sends whatever text you type. So, you get garbage when you use extended character sets.
I changed the ClickATell.php file so that

1) It checks whether the message contains any extended characters
2) If it does, in converts the message into unicode hex format (credits for conversion code goes to Dagon on phpbuilder forum)
3) If the message contains extended characters, it sets unicode setting when sending message to ClickATell, that knows it is receiving unicode hex instead of plain text.
4) If the message does not contain extended characters, it does a utf8 decoding just in case you have characters that are not extended but not latin either.
5) Finally, I also added concat parameter so that if the message is long, it can send it as two or three messages. This is important especially for UTF-8 messages as the maximum you can get is 70 chars per message.

CODE

Add method smstxtcode() // already included

protected function prepareParameters() {
-$params = array('user' => $this->userName, 'password' => $this->password);
+$params = array('user' => $this->userName, 'password' => $this->password, 'unicode' => '1', 'concat' => '3');
foreach (self::$REQUIRED_PARAMETERS as $key) {


In function send($message, $toNumbers)

$params = $this->prepareParameters();
-$params['text'] = $message;
+$smsarray = $this->smstxtcode($message);
+$params['text'] = $smsarray[1];
+$params['unicode'] = $smsarray[0];
$params['to'] = implode(',', $toNumbers);

*/
?>
