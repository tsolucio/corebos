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
 * Core Workflow logging class.
 */
class cbWFLogger {

	public function withName($name) {
		return 'cbwflog';
	}

	public function emit($context) {
		global $adb;
		if (GlobalVariable::getVariable('Debug_Workflow_Execution', (empty($_REQUEST['_logwf']) ? 0 : 1))) {
			$adb->pquery(
				'insert into cbwflog (exectime, pid, wftkid, recid, parentid, name, wftype, recvalues, conditions, evaluation, inqueue, haserror, logsmsgs)
					values (?,?,?,?,?,?,?,?,?,?,?,?,?)',
				[
					date('Y-m-d H:i:s'),
					getmypid(),
					$context['wftkid'],
					$context['recid'],
					$context['parentid'],
					$context['name'],
					$context['wftype'],
					json_encode($context['recvalues']),
					empty($context['conditions']) ? json_encode('') : $context['conditions'],
					$context['evaluation'],
					$context['inqueue'],
					$context['haserror'],
					json_encode($context['logsmsgs']),
				]
			);
			return $adb->getLastInsertID();
		}
	}

	public function info($context = []) {
		return $this->emit($context);
	}

	public function debug($context = []) {
		return $this->emit($context);
	}

	public function warning($context = []) {
		return $this->emit($context);
	}

	public function warn($context = []) {
		return $this->emit($context);
	}

	public function critical($context = []) {
		return $this->emit($context);
	}

	public function fatal($context = []) {
		return $this->emit($context);
	}

	public function error($context = []) {
		return $this->emit($context);
	}

	public function isLevelEnabled($level) {
		return true;
	}

	public function isDebugEnabled() {
		return true;
	}
}
?>