<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  coreBOS CRM Open Source
 * The Initial Developer of the Original Code is coreBOS.
 * Portions created by coreBOS are Copyright (C) coreBOS.
 * All Rights Reserved.
 *************************************************************************************/

/**
 * Core logging class.
 */
class cbLogger {
	private $logger = null;

	// default recommended values, the real values are in include/logging/config.php
	private $enableLogLevel = array(
		'ERROR' => true,
		'FATAL' => true,
		'INFO' => true,
		'WARNING' => true,
		'DEBUG' => true,
		'ALERT' => true,
	);

	public function __construct($name, $logger) {
		require 'include/logging/config.php';
		$this->enableLogLevel = $loggerConfig['enableLogLevels'];
		$this->enableLogLevel['WARN'] = $loggerConfig['enableLogLevels']['WARNING'];
		$this->enableLogLevel['CRITICAL'] = $loggerConfig['enableLogLevels']['FATAL'];
		$this->logger = $logger;

		/** For migration log-level we need debug turned-on */
		if (strtoupper($name) == 'MIGRATION') {
			$this->enableLogLevel['DEBUG'] = true;
		}
	}

	public function withName($name) {
		$this->logger = $this->logger->withName($name);
	}

	public function emit($level, $message, $context) {
		switch ($level) {
			case 'INFO':
				$this->logger->info($message, $context);
				break;
			case 'DEBUG':
				$this->logger->debug($message, $context);
				break;
			case 'WARN':
			case 'WARNING':
				$this->logger->warning($message, $context);
				break;
			case 'ERROR':
				$this->logger->error($message, $context);
				break;
			case 'CRITICAL':
			case 'FATAL':
				$this->logger->critical($message, $context);
				break;
			case 'ALERT':
				$this->logger->alert($message, $context);
				break;
			default:
				break;
		}
	}

	public function info($message, $context = []) {
		if ($this->isLevelEnabled('INFO')) {
			if (is_array($message) || is_object($message)) {
				$context = (array)$message;
				$message = '';
			}
			$this->emit('INFO', $message, $context);
		}
	}

	public function debug($message, $context = []) {
		if ($this->isDebugEnabled()) {
			if (is_array($message) || is_object($message)) {
				$context = (array)$message;
				$message = '';
			}
			$this->emit('DEBUG', $message, $context);
		}
	}

	public function warning($message, $context = []) {
		$this->warn($message, $context);
	}

	public function warn($message, $context = []) {
		if ($this->isLevelEnabled('WARN')) {
			if (is_array($message) || is_object($message)) {
				$context = (array)$message;
				$message = '';
			}
			$this->emit('WARN', $message, $context);
		}
	}

	public function critical($message, $context = []) {
		$this->fatal($message, $context);
	}

	public function fatal($message, $context = []) {
		if ($this->isLevelEnabled('FATAL')) {
			if (is_array($message) || is_object($message)) {
				$context = (array)$message;
				$message = '';
			}
			$this->emit('FATAL', $message, $context);
		}
	}

	public function error($message, $context = []) {
		if ($this->isLevelEnabled('ERROR')) {
			if (is_array($message) || is_object($message)) {
				$context = (array)$message;
				$message = '';
			}
			$this->emit('ERROR', $message, $context);
		}
	}

	public function alert($message, $context = []) {
		if ($this->isLevelEnabled('ALERT')) {
			if (is_array($message) || is_object($message)) {
				$context = (array)$message;
				$message = '';
			}
			$this->emit('ALERT', $message, $context);
		}
	}

	public function isLevelEnabled($level) {
		return $this->enableLogLevel[$level];
	}

	public function isDebugEnabled() {
		return $this->isLevelEnabled('DEBUG');
	}
}
?>