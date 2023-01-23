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
require_once 'include/integrations/redis/redis.php';
use coreBOS_Settings;

$loggerConfigHandlers = array(
	'ErrorLogHandler' => [
		'Enabled' => false,
		'Params' => [
			0, \Monolog\Logger::DEBUG, true, false
		]
	],
);

$cbRedis = new corebos_redis();
// $redisSettings = $cbRedis->getSettings();
// $redis = $cbRedis->getRedisClient();
// if ($redis) {
// 	$loggerConfigHandlers['RedisHandler'] = [
// 		'Enabled' => true,
// 		'Params' => [
// 			$redis, 'corebosLogs', \Monolog\Logger::DEBUG, true, 0
// 		]
// 	];
// }