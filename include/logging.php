<?php
/*************************************************************************************************
 * Copyright 2014 JPL TSolucio, S.L. -- This file is a part of TSOLUCIO coreBOS.
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
*************************************************************************************************/
require_once 'config.inc.php';

// Performance Optimization: Configure the log folder
if (!empty($LOG4PHP_DEBUG)) {
	define('LOG4PHP_DIR', 'include/log4php.debug');
	require_once LOG4PHP_DIR.'/Logger.php';
	Logger::configure('log4php.properties');
	class LoggerManager {
		public static function getlogger($name = 'ROOT') {
			return Logger::getLogger($name);
		}
	}
} else {
	define('LOG4PHP_DIR', 'include/log4php');
	require_once LOG4PHP_DIR.'/LoggerManager.php';
	require_once LOG4PHP_DIR.'/LoggerPropertyConfigurator.php';
	$config = new LoggerPropertyConfigurator();
	$config->configure('log4php.properties');
}
global $logbg;
if (empty($logbg)) {
	$logbg= LoggerManager::getLogger('BACKGROUND');
}
?>