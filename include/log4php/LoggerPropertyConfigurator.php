<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

/** Classes to avoid logging */
class LoggerPropertyConfigurator {

	public static $singleton = false;

	public function __construct() {
		LoggerPropertyConfigurator::$singleton = $this;
	}

	public function configure($configfile) {
		$configinfo = parse_ini_file($configfile);

		$types = array();
		$appenders = array();

		foreach ($configinfo as $k => $v) {
			if (preg_match('/log4php.rootLogger/i', $k, $m)) {
				$name = 'ROOT';
				list($level, $appender) = explode(',', $v);
				$types[$name]['level'] = $level;
				$types[$name]['appender'] = $appender;
			}
			if (preg_match('/log4php.logger.(.*)/i', $k, $m)) {
				$name = $m[1];
				list($level, $appender) = explode(',', $v);
				$types[$name]['level'] = $level;
				$types[$name]['appender'] = $appender;
			}
			if (preg_match('/log4php.appender.([^.]+).?(.*)/i', $k, $m)) {
				$appenders[$m[1]][$m[2]] = $v;
			}
		}

		$this->types = $types;
		$this->appenders = $appenders;
	}

	public function getConfigInfo($type) {
		if (isset($this->types[$type])) {
			$typeinfo = $this->types[$type];
			return array (
				'level'   => $typeinfo['level'],
				'appender'=> $this->appenders[$typeinfo['appender']]
			);
		}
		return false;
	}

	public static function getInstance() {
		return self::$singleton;
	}
}
?>
