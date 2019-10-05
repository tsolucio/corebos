<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/
ini_set('include_path', ini_get('include_path'). PATH_SEPARATOR . '../../../include/HTTP_Session2');
include_once __DIR__ . '/../../../include/HTTP_Session2/Session2.php';

class crmtogo_API_Session {

	public function __construct() {
	}

	public static function destroy($sessionid = false) {
		HTTP_Session2::destroy($sessionid);
	}

	public static function init($sessionid = false) {
		if (empty($sessionid)) {
			HTTP_Session2::start(null, null);
			$sessionid = HTTP_Session2::id();
		} else {
			HTTP_Session2::start(null, $sessionid);
		}
		if (HTTP_Session2::isIdle() || HTTP_Session2::isExpired()) {
			return false;
		}
		return $sessionid;
	}

	public static function get($key, $defvalue = '') {
		return HTTP_Session2::get($key, $defvalue);
	}

	public static function set($key, $value) {
		HTTP_Session2::set($key, $value);
	}
}
