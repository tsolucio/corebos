<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is: coreBOS Open Source
 * The Initial Developer of the Original Code is coreBOS.
 * Portions created by coreBOS are Copyright (C) coreBOS.
 * All Rights Reserved.
 *************************************************************************************/

/**
 * Core logging class.
 */
class cbLoggerStub {

	public function withName($name) {
	}

	public function emit($level, $message, $context = []) {
	}

	public function info($message, $context = []) {
	}

	public function debug($message, $context = []) {
	}

	public function warning($message, $context = []) {
	}

	public function warn($message, $context = []) {
	}

	public function critical($message, $context = []) {
	}

	public function fatal($message, $context = []) {
	}

	public function error($message, $context = []) {
	}

	public function isLevelEnabled($level) {
		return false;
	}

	public function isDebugEnabled() {
		return false;
	}
}
?>