<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class SessionManager {
	private $maxLife;
	private $idleLife;
	private $sessionVar = '__SessionExists';
	private $error;

	public function __construct() {
		$now = time();
		$this->maxLife = $now + GlobalVariable::getVariable('WebService_Session_Life_Span', 86400);
		$this->idleLife = $now + GlobalVariable::getVariable('WebService_Session_Idle_Time', 1800);

		// if (!coreBOS_Session::isSessionStarted()) {
		// 	coreBOS_Session::init(false, false, $sname);
		// }
		// only first invocation of following method, which is setExpire
		//have an effect and any further invocation will be have no effect.
		coreBOS_Session::setExpire($this->maxLife);
		// this method replaces the new with old time if second params is true
		//otherwise it subtracts the time from previous time
		coreBOS_Session::setIdle($this->idleLife, true);
	}

	public function isValid() {
		$valid = true;
		// expired
		if (coreBOS_Session::isExpired()) {
			$valid = false;
			coreBOS_Session::destroy();
			throw new WebServiceException(WebServiceErrorCode::$SESSLIFEOVER, 'Session life span over, please login again');
		}
		// idled
		if (coreBOS_Session::isIdle()) {
			$valid = false;
			coreBOS_Session::destroy();
			throw new WebServiceException(WebServiceErrorCode::$SESSIONIDLE, 'Session has been invalidated due to lack of activity');
		}
		//invalid sessionId provided.
		if (!$this->get($this->sessionVar) && !coreBOS_Session::isNew()) {
			$valid = false;
			coreBOS_Session::destroy();
			throw new WebServiceException(WebServiceErrorCode::$SESSIONIDINVALID, 'Session Identifier provided is invalid');
		}
		return $valid;
	}

	public function startSession($sid = null, $adoptSession = false, $sname = null) {
		if (!$sid || strlen($sid) ===0) {
			$sid = null;
		}
		global $log;$log->fatal([$sid, $adoptSession, $sname, $_SESSION]);
		coreBOS_Session::init(false, false, $sid);
		$newSID = coreBOS_Session::id();
		$adoptSession=true;
		if (!$sid || $adoptSession) {
			$this->set($this->sessionVar, 'true');
		} else {
			if (!$this->get($this->sessionVar)) {
				coreBOS_Session::destroy();
				throw new WebServiceException(WebServiceErrorCode::$SESSIONIDINVALID, 'Session Identifier provided is invalid');
			}
		}

		if (!$this->isValid()) {
			$newSID = null;
		}
		return $newSID;
	}

	public function getSessionId() {
		return coreBOS_Session::id();
	}

	public function set($var_name, $var_value) {
		coreBOS_Session::set($var_name, $var_value);
	}

	public function get($name) {
		return coreBOS_Session::get($name);
	}

	public function getError() {
		return $this->error;
	}

	public function destroy() {
		coreBOS_Session::destroy();
	}
}
?>
