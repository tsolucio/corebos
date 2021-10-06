<?php

class MailUpException extends Exception {


	private $statusCode;

	public function __construct($statusCode, $message) {
		parent::__construct($message);
		$this->statusCode = $statusCode;
	}

	public function getStatusCode() {
		return $this->statusCode;
	}
}