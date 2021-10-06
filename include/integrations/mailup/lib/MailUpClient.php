<?php

require_once 'include/integrations/mailup/lib/MailUpException.php';
require_once 'include/integrations/mailup/lib/DataFilter.php';

class MailUpClient {

	private $clientId;
	private $secretKey;
	private $api = array();
	private $accessToken;
	private $refreshToken;
	private $tokenTime;
	private $errorUrl;

	const ACCESSTOKEN = 'mailup_access_token';
	const REFRESHTOKEN = 'mailup_refresh_token';
	const TOKENTIME = 'mailup_token_time';

	public function __construct($auth, $api = array()) {
		$this->clientId = $auth['client_id'];
		$this->secretKey = $auth['secret_key'];
		$this->api = $this->api();

		$this->loadToken();
	}

	public function getAccessToken() {
		return $this->accessToken;
	}

	public function getConsoleUrl() {
		return $this->api['console'];
	}

	public function getMailStatsUrl() {
		return $this->api['mail_stats'];
	}

	public function getErrorUrl() {
		return $this->errorUrl;
	}

	public function getTokenTime() {
		$time = $this->tokenTime;

		if (null !== $this->tokenTime) {
			$time = $this->tokenTime - time();
		}

		return $time;
	}

	public function logonByKey($callback) {
		$url = $this->api['logon'] . "?client_id=" . $this->clientId . "&client_secret=" . $this->secretKey . "&response_type=code&redirect_uri=" . $callback;
		header("Location: " . $url);
	}

	public function retrieveTokenByCode($code) {
		$url = $this->api['token'] . "?code=" . $code . "&grant_type=authorization_code";

		$curl = curl_init();
		curl_setopt($curl, CURLOPT_URL, $url);
		// Return result as string to script
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		// Not verify the host certificate
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
		// Not check name in the certificate
		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);

		$result = curl_exec($curl);
		$response_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
		curl_close($curl);

		if ($response_code !== 200 & $response_code !== 302) {
			$this->clearToken();
			throw new MailUpException($code, "Authorization error");
		}

		$result = json_decode($result);

		$this->saveToken($result->access_token, $result->refresh_token, $result->expires_in);
	}

	public function retrieveTokenByPassword($username, $password) {
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_URL, $this->api['token']);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($curl, CURLOPT_POST, true);

		$username = DataFilter::convertToString($username);
		$password = DataFilter::convertToString($password);

		$body = 'grant_type=password&username=' . rawurlencode($username) . '&password=' . rawurlencode($password) . '&client_id=' .
				rawurlencode($this->clientId) .  rawurlencode($this->secretKey);
		$headers = array(
			"Content-type: application/x-www-form-urlencoded",
			"Content-Length: " . strlen($body),
			"Accept: application/json",
			"Authorization: Basic " . base64_encode($this->clientId . ':' . $this->secretKey)
		);

		curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($curl, CURLOPT_POSTFIELDS, $body);

		$result = curl_exec($curl);
		$code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
		curl_close($curl);

		if ($code !== 200 & $code !== 302) {
			$this->clearToken();
			throw new MailUpException($code, "Authorization error");
		}

		$result = json_decode($result);

		$this->saveToken($result->access_token, $result->refresh_token, $result->expires_in);
	}

	public function refreshToken() {
		$body = "client_id=" . rawurlencode($this->clientId) . "&client_secret=" . rawurlencode($this->secretKey) . "&refresh_token=" . rawurlencode($this->refreshToken) . "&grant_type=refresh_token";
		$headers = array(
			"Content-type: application/x-www-form-urlencoded",
			"Content-length: " . strlen($body),
			"Accept: application/json"
		);

		$curl = curl_init();
		curl_setopt($curl, CURLOPT_URL, $this->api['token']);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($curl, CURLOPT_POST, true);
		curl_setopt($curl, CURLOPT_POSTFIELDS, $body);
		curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);

		$result = curl_exec($curl);
		$code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
		curl_close($curl);

		if ($code != 200 && $code != 302) {
			$this->clearToken();
			throw new MailUpException($code, "Authorization error");
		}

		$result = json_decode($result);

		$this->saveToken($result->access_token, $result->refresh_token, $result->expires_in);
	}

	public function makeRequest($method, $url, $content_type = "JSON", $body = "", $refresh = true) {
		$temp_file = null;
		$content_type = ($content_type === "XML" ? "application/xml" : "application/json");
		$headers = array(
			"Content-type: " . $content_type,
			"Content-length: " . strlen($body),
			"Accept: " . $content_type,
			"Authorization: Bearer " . $this->accessToken
		);

		$curl = curl_init();
		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);

		switch ($method) {
			case "POST":
				curl_setopt($curl, CURLOPT_POST, true);
				curl_setopt($curl, CURLOPT_POSTFIELDS, $body);
				curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
				break;
			case "PUT":
				$temp_file = tmpfile();
				fwrite($temp_file, $body);
				fseek($temp_file, 0);
				curl_setopt($curl, CURLOPT_PUT, true);
				curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
				curl_setopt($curl, CURLOPT_INFILE, $temp_file);
				curl_setopt($curl, CURLOPT_INFILESIZE, strlen($body));
				break;
			case "DELETE":
				$headers[1] = "Content-length: 0";
				curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "DELETE");
				curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
				break;
			default:
				$headers[1] = "Content-length: 0";
				curl_setopt($curl, CURLOPT_HTTPGET, true);
				curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
		}

		$result = curl_exec($curl);
		$code = curl_getinfo($curl, CURLINFO_HTTP_CODE);

		if ($temp_file !== null) {
			fclose($temp_file);
		}
		curl_close($curl);

		if ($code === 401 & $refresh === true) {
			$this->refreshToken();
			return $this->makeRequest($method, $url, $content_type, $body, false);
		}

		if ($code !== 200 & $code !== 302) {
			$result = json_decode($result);
			$error_desc = "";

			if (isset($result->ErrorDescription)) {
				$error_desc = $result->ErrorDescription;
			} else {
				$error_desc = "Unknown error";
			}

			throw new MailUpException($code, $error_desc);
		}

		return $result;
	}

	private function loadToken() {
		$this->accessToken = coreBOS_Settings::getSetting(self::ACCESSTOKEN, '');
		$this->refreshToken = coreBOS_Settings::getSetting(self::REFRESHTOKEN, '');
		$this->tokenTime = coreBOS_Settings::getSetting(self::TOKENTIME, '');
	}

	private function clearToken() {
		$this->accessToken = null;
		$this->refreshToken = null;
		$this->tokenTime = null;

		coreBOS_Settings::setSetting(self::ACCESSTOKEN, $this->accessToken);
		coreBOS_Settings::setSetting(self::REFRESHTOKEN, $this->refreshToken);
		coreBOS_Settings::setSetting(self::TOKENTIME, $this->tokenTime);
	}

	private function saveToken($token, $refresh, $time) {
		$this->accessToken = $token;
		$this->refreshToken = $refresh;
		$this->tokenTime = time() + $time;
		coreBOS_Settings::setSetting(self::ACCESSTOKEN, $this->accessToken);
		coreBOS_Settings::setSetting(self::REFRESHTOKEN, $this->refreshToken);
		coreBOS_Settings::setSetting(self::TOKENTIME, $this->tokenTime);
	}

	public function getResult($method, $type, $body, $env, $ep, $text) {
		$result = array();
		$url = "Console" === $env ? $this->api['console'] : $this->api['mail_stats'];
		$url = $url . $ep;
		$this->errorUrl = $url;
		$result['res_body']  = $this->makeRequest($method, $url, $type, $body);
		$result['url'] = $env;
		$result['content_type'] = $type;
		$result['method'] = $method;
		$result['endpoint'] = $ep;
		$result['text'] = $text;
		$result['req_body'] = $body;
		return $result;
	}

	private function api() {
		return array(
			'logon' => 'https://services.mailup.com/Authorization/OAuth/LogOn',
			'authorization' => 'https://services.mailup.com/Authorization/OAuth/Authorization',
			'token' => 'https://services.mailup.com/Authorization/OAuth/Token',
			'console' => 'https://services.mailup.com/API/v1.1/Rest/ConsoleService.svc',
			'mail_stats' => 'https://services.mailup.com/API/v1.1/Rest/MailStatisticsService.svc'
		);
	}
}