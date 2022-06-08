<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/
ini_set('include_path', ini_get('include_path'). PATH_SEPARATOR . 'vtlib/thirdparty/network/');
include 'vtlib/thirdparty/network/Request2.php';

/**
 * Provides API to work with HTTP Connection.
 * @package vtlib
 */
class Vtiger_Net_Client {
	public $client;
	public $url;
	public $response;
	public $request_response;
	public $error = false;
	public $errormsg = '';

	/**
	 * Constructor
	 * @param string URL of the site
	 * Example:
	 * $client = new Vtiger_Net_Client('http://www.vtiger.com');
	 */
	public function __construct($urlpath) {
		$this->setURL($urlpath);
	}

	/**
	 * Set another url for this instance
	 * @param string URL to use go forward
	 */
	public function setURL($urlpath) {
		$this->url = $urlpath;
		$this->client = new HTTP_Request2();
		$this->response = false;
	}

	/**
	 * Set the body of the request
	 * @param string body to be sent
	 */
	public function setBody($body) {
		$this->client->setBody($body);
	}

	/**
	 * Set custom HTTP Headers
	 * @param Map HTTP Header and Value Pairs
	 */
	public function setHeaders($values) {
		foreach ($values as $key => $value) {
			$this->client->setHeader($key, $value);
		}
	}

	/**
	 * Set File Upload
	 * @param string fieldName name of file-upload field
	 * @param string|resource|array  filename full name of local file, pointer to open file or an array of files
	 * @param string sendFilename filename to send in the request
	 * @param string contentType content-type of file being upload
	 */
	public function setFileUpload($fieldName, $filename, $sendFilename = null, $contentType = null) {
		$this->client->addUpload($fieldName, $filename, $sendFilename, $contentType);
	}

	/**
	 * Perform a GET request
	 * @param Map key-value pair or false
	 * @param integer timeout value
	 */
	public function doGet($params = false, $timeout = null) {
		if ($timeout) {
			$this->client->setConfig('connect_timeout', $timeout);
		}
		$this->client->setConfig('adapter', 'HTTP_Request2_Adapter_Curl');
		$this->client->setURL($this->url);
		$this->client->setMethod(HTTP_Request2::METHOD_GET);

		if ($params) {
			$urlpath = $this->client->getUrl();
			foreach ($params as $key => $value) {
				$urlpath->setQueryVariable($key, $value);
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
	 * @param integer timeout value
	 */
	public function doPost($params = false, $timeout = null) {
		if ($timeout) {
			$this->client->setConfig('connect_timeout', $timeout);
		}
		$this->client->setConfig('adapter', 'HTTP_Request2_Adapter_Curl');
		$this->client->setURL($this->url);
		$this->client->setMethod(HTTP_Request2::METHOD_POST);

		if ($params) {
			if (is_string($params)) {
				$this->client->setBody($params);
			} else {
				foreach ($params as $key => $value) {
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
	public function wasError() {
		return $this->error;
	}

	/**
	 * get last request error message
	 */
	public function getErrorMessage() {
		return $this->errormsg;
	}

	/**
	 * Disconnect this instance
	 */
	public function disconnect() {
		//$this->client->disconnect();
	}
}
?>
