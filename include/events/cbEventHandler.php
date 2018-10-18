<?php
/*************************************************************************************************
 * Copyright 2015 JPL TSolucio, S.L. -- This file is a part of TSOLUCIO coreBOS Customizations.
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
 *  Module	   : coreBOS Enhanced Events
 *  Version	  : 1.0
 *  Author	   : JPL TSolucio, S. L.
 *  Based on idea/code from Stefan Warnat <support@stefanwarnat.de>
 *  Licensed under he MIT License (MIT)  Copyright (c) 2013 Stefan Warnat
 *  https://github.com/swarnat/vtigerCRM-EventHandler
 *************************************************************************************************/
class cbEventHandler {
	protected static $_eventManager = false;
	protected static $_objectCache = array();
	protected static $_filterCache = false;

	protected static $Counter = 0;
	protected static $CounterInternal = 0;

	protected static $numCounter = array(0, 0);

	protected static function _loadFilterCache($filtername) {
		global $adb;
		$query = 'SELECT handler_path, handler_class FROM vtiger_eventhandlers WHERE is_active=true AND event_name = ?';
		$result = $adb->pquery($query, array($filtername));

		if (!isset(self::$_filterCache[$filtername])) {
			self::$_filterCache[$filtername] = array();
		}

		while ($filter = $adb->fetchByAssoc($result)) {
			self::$_filterCache[$filtername][] = $filter;
		}
	}

	public static function do_action($eventName, $parameter = false) {
		$startTime = microtime(true);
		self::$numCounter[0]++;

		// if vtiger.footer Action is called, output the timings for admins
		if ($eventName == 'corebos.footer') {
			global $current_user;
			$show_response_time = GlobalVariable::getVariable('Debug_Calculate_Response_Time', 0);
			if ($current_user->is_admin == 'on' && $show_response_time) {
				echo "<div style='text-align:left;font-size:11px;padding:0 30px;color:rgb(153, 153, 153);'>"
					."Event processing <span title='total time the EventHandler was active' alt='total time the EventHandler was active'>".round(self::$Counter*1000, 1)
					."</span> / <span title='time Events used internal' alt='time Events used internal'>".round(self::$CounterInternal*1000, 1).' msec ('
					.self::$numCounter[0].' Actions / '.self::$numCounter[1].' Filter)</div>';
			}
		}

		// Handle Events with the internal EventsManager
		if (self::$_eventManager === false) {
			global $adb;
			self::$_eventManager = new VTEventsManager($adb);
			// Initialize Event trigger cache
			self::$_eventManager->initTriggerCache();
		}

		$startTime2 = microtime(true);
		self::$_eventManager->triggerEvent($eventName, $parameter);

		self::$Counter += (microtime(true) - $startTime);
		self::$CounterInternal += (microtime(true) - $startTime2);
	}

	public static function do_filter($filtername, $parameter) {
		$startTime = microtime(true);

		// load the Cache for this Filter
		if (self::$_filterCache === false || !isset(self::$_filterCache[$filtername])) {
			self::_loadFilterCache($filtername);
		}

		// if no filter is registerd only return $parameter
		if (!isset(self::$_filterCache[$filtername]) || count(self::$_filterCache[$filtername]) == 0) {
			return $parameter;
		}

		foreach (self::$_filterCache[$filtername] as $filter) {
			self::$numCounter[1]++;

			// if not used before this, create the Handler Class
			if (!isset(self::$_objectCache[$filter['handler_path'].'/'.$filter['handler_class']])) {
				require_once $filter['handler_path'];
				$className = $filter['handler_class'];
				self::$_objectCache[$filter['handler_path'].'#'.$filter['handler_class']] = new $className();
			}

			$obj = self::$_objectCache[$filter['handler_path'].'#'.$filter['handler_class']];

			$startTime2 = microtime(true);
			// call the filter and set the return value again to $parameter
			$parameter = $obj->handleFilter($filtername, $parameter);
			self::$CounterInternal += (microtime(true) - $startTime2);
		}

		self::$Counter += (microtime(true) - $startTime);

		return $parameter;
	}
}
