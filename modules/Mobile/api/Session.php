<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/

class crmtogo_API_Session {

	public static function destroy($sessionid = false) {
		coreBOS_Session::destroy($sessionid);
	}

	public static function init($sessionid = false) {
		if (empty($sessionid)) {
			coreBOS_Session::init(false, true);
			$sessionid = coreBOS_Session::getSessionName();
		} else {
			coreBOS_Session::init(false, true, $sessionid);
		}
		if (coreBOS_Session::isIdle() || coreBOS_Session::isExpired()) {
			return false;
		}
		return $sessionid;
	}

	public static function get($key, $defvalue = '') {
		return coreBOS_Session::get($key, $defvalue);
	}

	public static function set($key, $value) {
		coreBOS_Session::set($key, $value);
	}
}
