<?php

/**
 * This configuration will be read and overlaid on top of the
 * default configuration. Command line arguments will be applied
 * after this file is read.
 */
return [
    // Supported values: '7.0', '7.1', '7.2', null.
    // If this is set to null,
    // then Phan assumes the PHP version which is closest to the minor version
    // of the php executable used to execute phan.
    // TODO: Set this.
    'target_php_version' => null,

    // A list of directories that should be parsed for class and
    // method information. After excluding the directories
    // defined in exclude_analysis_directory_list, the remaining
    // files will be statically analyzed for errors.
    //
    // Thus, both first-party and third-party code being used by
    // your application should be included in this list.
    'directory_list' => [
        'include',
        'modules',
        'data',
    ],
	'file_list' => [
		'config.inc.php',
		'index.php',
		'webservice.php',
		'vtigerversion.php',
		'vtigercron.php',
		'tabdata.php',
		'sync.php',
		'Smarty_setup.php',
		'Popup.php',
	],
	'ignore_undeclared_variables_in_global_scope' => true,
// 	'globals_type_map' => [
// 		'vtigerCRM_Smarty'=>'vtigerCRM_Smarty',
// 		'VTWorkflowManager'=>'VTWorkflowManager',
// 		'Vtiger_Cron'=>'Vtiger_Cron',
// 	],
	'suppress_issue_types' => [
		'PhanUndeclaredMethod',
		'PhanUndeclaredClassMethod',
		'PhanUndeclaredExtendedClass',
		//'PhanUndeclaredProperty',
	],
    // A directory list that defines files that will be excluded
    // from static analysis, but whose class and method
    // information should be included.
    //
    // Generally, you'll want to include the directories for
    // third-party code (such as "vendor/") in this list.
    //
    // n.b.: If you'd like to parse but not analyze 3rd
    //       party code, directories containing that code
    //       should be added to both the `directory_list`
    //       and `exclude_analysis_directory_list` arrays.
    "exclude_analysis_directory_list" => [
        'modules/Calendar/iCal/',
        'modules/Calendar4You/fullcalendar/',
        'include/adodb',
        'include/antlr',
        'include/asynquence',
        'include/bunnyjs',
        'include/calculator',
        'include/ckeditor',
        'include/clock',
        'include/csrfmagic',
        'include/dropzone',
        'include/fpdi',
        'include/freetag',
        'include/htmlpurifier',
        'include/HTTP_Session2',
        'include/jquery',
        'include/LD',
        'include/log4php',
        'include/log4php.debug',
        'include/nusoap',
        'include/PhpSpreadsheet',
        'include/simplepie',
        'include/sw-precache',
        'include/tcpdf',
    ],
];