<?php

class CoreBOS_Request {

	public function __construct($request) {
		foreach ($request as $key => $value) {
			$this->$key = $this->validate($value);
		}
	}

	/**
	 * To be implemanted later
	 *
	 * @param  any $content
	 * @return any
	 */
	private function validate($content) {
		return $content;
	}

	/**
	 * Method to get the request given the key
	 *
	 * @param  String $key
	 * @return String
	 */
	public function get($key) {
		if (isset($this->$key)) {
			return $this->$key;
		}
		return false;
	}

	/**
	 * Method to set a request variable given the
	 * key and the value
	 *
	 * @param String $key
	 */
	public function set($key, $value) {
		$this->$key = $this->validate($value);
	}
}