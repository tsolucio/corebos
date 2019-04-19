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

abstract class cbmqtm_manager {
	static protected $instance = null;

	public static function getInstance() {

		if (null === static::$instance) {
			static::$instance = new static;
		}

		return static::$instance;
	}

	protected function __construct() {
	}

	protected function __clone() {
	}

	abstract public function sendMessage($channel, $producer, $consumer, $type, $share, $sequence, $expires, $deliverafter, $userid, $information);
	abstract public function getMessage($channel, $consumer, $producer = '*', $userid = '*');
	abstract public function isMessageWaiting($channel, $consumer, $producer = '*', $userid = '*');
	abstract public function rejectMessage($message, $invalidreason);
	abstract public function subscribeToChannel($channel, $producer, $consumer, $callback);
	abstract public function unsubscribeToChannel($channel, $producer, $consumer, $callback);
}