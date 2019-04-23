<?php
require_once 'include/wsClient/Net/curl_http_client.php';

class cbHTTP_Client extends Curl_HTTP_Client {
	public $_serviceurl = '';

	public function __construct($url) {
		if (!function_exists('curl_exec')) {
			die('cbHTTP_Client: Curl extension not enabled!');
		}
		parent::__construct();
		$this->_serviceurl = $url;
		$useragent = 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1)';
		$this->set_user_agent($useragent);

		// Escape SSL certificate hostname verification
		curl_setopt($this->ch, CURLOPT_SSL_VERIFYHOST, 0);
	}

	public function doPost($postdata = false, $decodeResponseJSON = false, $timeout = 20) {
		if ($postdata === false) {
			$postdata = array();
		}
		$resdata = $this->send_post_data($this->_serviceurl, $postdata, null, $timeout);
		if ($resdata && $decodeResponseJSON) {
			$resdata = json_decode($resdata, true);
		}
		return $resdata;
	}

	public function doGet($getdata = false, $decodeResponseJSON = false, $timeout = 20) {
		if ($getdata === false) {
			$getdata = array();
		}
		$queryString = '';
		foreach ($getdata as $key => $value) {
			$queryString .= '&' . urlencode($key)."=".urlencode($value);
		}
		$resdata = $this->fetch_url("$this->_serviceurl?$queryString", null, $timeout);
		if ($resdata && $decodeResponseJSON) {
			$resdata = json_decode($resdata, true);
		}
		return $resdata;
	}
}
?>
