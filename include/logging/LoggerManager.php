<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  coreBOS CRM Open Source
 * The Initial Developer of the Original Code is coreBOS.
 * Portions created by coreBOS are Copyright (C) coreBOS.
 * All Rights Reserved.
 *************************************************************************************/
require_once 'vendor/autoload.php';
use Monolog\Logger;
use Monolog\Formatter\LineFormatter;
use Monolog\Handler\RotatingFileHandler;

require_once 'config.inc.php';

class LoggerManager {
	private static $cacheLoggers = array();

	public static function getlogger($name = 'APPLICATION') {
		require 'include/logging/config.php';
		$LOGLINEFORMAT = '[%datetime%{'.getmypid()."}] %channel%.%level_name%: %message% :::: %context%\n";
		if (isset($loggerConfig[$name])) {
			if (empty(self::$cacheLoggers[$name])) {
				if ($loggerConfig[$name]['Enabled']) {
					$logger = new Logger($name);
					$formatter = new LineFormatter($LOGLINEFORMAT, 'Y-m-d H:i:s:u');
					$logger = self::addHandlers($logger, $formatter);
					$stream = new RotatingFileHandler('logs/'.$loggerConfig[$name]['File'].'.log', $loggerConfig[$name]['MaxBackup'], self::getLogLevel($loggerConfig[$name]['Level']), true, 0664);
					$stream->setFormatter($formatter);
					$logger->pushHandler($stream);
					self::$cacheLoggers[$name] = new cbLogger($name, $logger);
				} else {
					self::$cacheLoggers[$name] = new cbLoggerStub();
				}
			}
		} else {
			if (empty(self::$cacheLoggers['APPLICATION'])) {
				if ($loggerConfig[$name]['Enabled']) {
					$logger = new Logger('APPLICATION');
					$formatter = new LineFormatter($LOGLINEFORMAT, 'Y-m-d H:i:s:u');
					$logger = self::addHandlers($logger, $formatter);
					$stream = new RotatingFileHandler('logs/'.$loggerConfig['APPLICATION']['File'].'.log', $loggerConfig['APPLICATION']['MaxBackup'], self::getLogLevel($loggerConfig[$name]['Level']));
					$stream->setFormatter($formatter);
					$logger->pushHandler($stream);
					self::$cacheLoggers['APPLICATION'] = new cbLogger('APPLICATION', $logger);
				} else {
					self::$cacheLoggers['APPLICATION'] = new cbLoggerStub();
				}
			}
			self::$cacheLoggers['APPLICATION']->withName($name);
			$name = 'APPLICATION';
		}
		return self::$cacheLoggers[$name];
	}

	private static function addHandlers($logger, $formatter) {
		require 'include/logging/confighandlers.php';
		foreach ($loggerConfigHandlers as $handler => $cfg) {
			if ($cfg['Enabled']) {
				$use = "\Monolog\Handler\\$handler";
				$logger->pushHandler(new $use(...$cfg['Params']));
			}
		}
		return $logger;
	}

	private static function getLogLevel($corebos) {
		switch ($corebos) {
			case 'INFO':
				return Logger::INFO;
			case 'DEBUG':
				return Logger::DEBUG;
			case 'WARN':
			case 'WARNING':
				return Logger::WARNING;
			case 'ERROR':
				return Logger::ERROR;
			case 'ALERT':
				return Logger::ALERT;
			case 'CRITICAL':
			case 'FATAL':
			default:
				return Logger::CRITICAL;
		}
	}
}
?>