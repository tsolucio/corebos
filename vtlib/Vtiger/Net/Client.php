<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/
ini_set('include_path',ini_get('include_path'). PATH_SEPARATOR . 'vtlib/thirdparty/network/');
include 'vtlib/thirdparty/network/Request2.php';

/**
 * Provides API to work with HTTP Connection.
 * @package vtlib
 */
class Vtiger_Net_Client {
	var $client;
	var $url;
	var $response;
	var $request_response;
	var $error = false;
	var $errormsg = '';

	/**
	 * Constructor
	 * @param String URL of the site
	 * Example: 
	 * $client = new Vtiger_Net_Client('http://www.vtiger.com');
	 */
	function __construct($url) {
		$this->setURL($url);
	}

	/**
	 * Set another url for this instance
	 * @param String URL to use go forward
	 */
	function setURL($url) {
		$this->url = $url;
		$this->client = new HTTP_Request2();
		$this->response = false;
	}

	/**
	 * Set the body of the request
	 * $param String body to be sent
	 */
	function setBody($body) {
		$this->client->setBody($body);
	}

	/**
	 * Set custom HTTP Headers
	 * @param Map HTTP Header and Value Pairs
	 */
	function setHeaders($values) {
		foreach($values as $key=>$value) {
			$this->client->setHeader($key, $value);
		}
	}

	/**
	 * Perform a GET request
	 * @param Map key-value pair or false
	 * @param Integer timeout value
	 */
	function doGet($params=false, $timeout=null) {
		if($timeout) $this->client->setConfig('connect_timeout', $timeout);
		$this->client->setConfig('adapter', 'HTTP_Request2_Adapter_Curl');
		$this->client->setURL($this->url);
		$this->client->setMethod(HTTP_Request2::METHOD_GET);

		if($params) {
			$url = $this->client->getUrl();
			foreach($params as $key=>$value) {
				$url->setQueryVariable($key, $value);
			}
		}
		try {
			$this->request_response = $this->client->send();
			$content = $this->request_response->getBody();
			$this->error = false;
			$this->errormsg = '';
		} catch (Exception $e) {
			$content = false;
			$this->error = true;
			$this->errormsg = $e->getMessage();
		}
		$this->response = !$this->error;
		$this->disconnect();
		return $content;
	}

	/**
	 * Perform a POST request
	 * @param Map key-value pair or false
	 * @param Integer timeout value
	 */
	function doPost($params=false, $timeout=null) {
		if($timeout) $this->client->setConfig('connect_timeout', $timeout);
		$this->client->setConfig('adapter', 'HTTP_Request2_Adapter_Curl');
		$this->client->setURL($this->url);
		$this->client->setMethod(HTTP_Request2::METHOD_POST);

		if($params) {
			if(is_string($params)) {
				$this->client->setBody($params);
			} else {
				foreach($params as $key=>$value) {
					$this->client->addPostParameter($key, $value);
				}
			}
		}
		try {
			$this->request_response = $this->client->send();
			$content = $this->request_response->getBody();
			$this->error = false;
			$this->errormsg = '';
		} catch (Exception $e) {
			$content = false;
			$this->error = true;
			$this->errormsg = $e->getMessage();
		}
		$this->response = !$this->error;
		$this->disconnect();

		return $content;
	}

	/**
	 * Did last request resulted in error?
	 */
	function wasError() {
		return $this->error;
	}

	/**
	 * get last request error message
	 */
	function getErrorMessage() {
		return $this->errormsg;
	}

	/**
	 * Disconnect this instance
	 */
	function disconnect() {
		//$this->client->disconnect();
	}
}
?>
