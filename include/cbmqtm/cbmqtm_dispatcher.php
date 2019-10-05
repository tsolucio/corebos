<?php
/*************************************************************************************************
 * Copyright 2017 JPL TSolucio, S.L. -- This file is a part of TSOLUCIO coreBOS Customizations.
 * Licensed under the vtiger CRM Public License Version 1.1 (the "License"); you may not use this
 * file except in compliance with the License. You can redistribute it and/or modify it
 * under the terms of the License. JPL TSolucio, S.L. reserves all rights not expressly
 * granted by the License. coreBOS distributed by JPL TSolucio S.L. is distributed in
 * the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied
 * warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. Unless required by
 * applicable law or agreed to in writing, software distributed under the License is
 * distributed on an "AS IS" BASIS, WITHOUT ANY WARRANTIES OR CONDITIONS OF ANY KIND,
 * either express or implied. See the License for the specific language governing
 * permissions and limitations under the License. You may obtain a copy of the License
 * at <http://corebos.org/documentation/doku.php?id=en:devel:vpl11>
 *************************************************************************************************
 *  Module       : coreBOS Message Queue and Task Manager Manager
 *  Author       : JPL TSolucio, S. L.
 *************************************************************************************************/
include_once 'vtlib/Vtiger/Module.php';

class coreBOS_MQTMDispatcher extends \Core_Daemon {
	protected $loop_interval = 5;
	protected $cb_db = null;
	protected $cb_mq = null;

	/**
	 * The only plugin we're using is a simple file-based lock to prevent 2 instances from running
	 */
	protected function setup_plugins() {
		$this->plugin('Lock_File');
	}

	/**
	 * This is where you implement any once-per-execution setup code.
	 * @return void
	 * @throws \Exception
	 */
	protected function setup() {
		$this->cb_db = PearDatabase::getInstance();
		$this->cb_mq = coreBOS_MQTM::getInstance();
		$this->loop_interval = GlobalVariable::getVariable('MQTM_Loop_Interval', 5, '', Users::getActiveAdminId());
	}

	/**
	 * This is where you implement the tasks you want your daemon to perform.
	 * This method is called at the frequency defined by loop_interval.
	 *
	 * @return void
	 */
	protected function execute() {
		if (empty($this->cb_db) || $this->cb_db->database->_connectionID->errno>0) {
			global $adb;
			$adb->connect();
			$this->setup();
		}
		$callbacks = $this->cb_mq->getSubscriptionWakeUps();
		foreach ($callbacks as $callback) {
			if (!empty($callback['file'])) {
				include_once $callback['file'];
			}
			if (!empty($callback['class']) && !empty($callback['method'])) {
				$this->task(array(new $callback['class'],$callback['method']));
			} elseif (!empty($callback['method'])) {
				$this->task($callback['method']);
			}
		}
		$this->cb_mq->expireMessages();
	}

	/**
	 * No logging
	 */
	public function log($message, $label = '', $indent = 1) {
		return;
	}

	protected function log_file() {
		return '';
	}
}
