<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  coreBOS CRM Open Source
 * The Initial Developer of the Original Code is coreBOS.
 * Portions created by coreBOS are Copyright (C) coreBOS.
 * All Rights Reserved.
 *************************************************************************************/
$loggerConfig = array(
	// Enable/Disable messages of certain levels tweeking the next values
	'enableLogLevels' => [
		'ERROR' => true,
		'FATAL' => true,
		'INFO' => true,
		'WARNING' => true,
		'DEBUG' => true,
		'ALERT' => true,
	],
	// use the next sections to activate different log services and their level
	'SECURITY'=>[
		'Enabled' => false,
		'Level' => 'FATAL',
		'MaxBackup' => 10,
		'File' => 'security',
	],
	'INSTALL' => [
		'Enabled' => false,
		'Level' => 'DEBUG',
		'MaxBackup' => 10,
		'File' => 'installation',
	],
	'APPLICATION' => [
		'Enabled' => false,
		'Level' => 'FATAL',
		'MaxBackup' => 10,
		'File' => 'corebosapp',
	],
	'MIGRATION' => [
		'Enabled' => true,
		'Level' => 'DEBUG',
		'MaxBackup' => 10,
		'File' => 'migration',
	],
	'SOAP' => [
		'Enabled' => false,
		'Level' => 'FATAL',
		'MaxBackup' => 10,
		'File' => 'soap',
	],
	'SQLTIME' => [
		'Enabled' => false,
		'Level' => 'FATAL',
		'MaxBackup' => 10,
		'File' => 'sqltime',
	],
	'BACKGROUND' => [
		'Enabled' => false,
		'Level' => 'FATAL',
		'MaxBackup' => 10,
		'File' => 'background',
	],
	'JAVASCRIPT' => [
		'Enabled' => false,
		'Level' => 'FATAL',
		'MaxBackup' => 10,
		'File' => 'javascript',
	],
	'IMPORT' => [
		'Enabled' => false,
		'Level' => 'DEBUG',
		'MaxBackup' => 10,
		'File' => 'import',
	],
);
