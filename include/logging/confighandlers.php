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

$loggerConfigHandlers = array(
	'ErrorLogHandler' => [
		'Enabled' => false,
		'Params' => [
			0, \Monolog\Logger::DEBUG, true, false
		]
	]
);