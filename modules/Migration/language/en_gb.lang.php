<?php
/*+********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ********************************************************************************/

$mod_strings = array(
	'LBL_MIGRATE_INFO' => 'Enter values to migrate data from <b><i>Source</i></b> to <b><i>Current (latest) vtiger CRM</i></b>',
	'LBL_CURRENT_VT_MYSQL_EXIST' => 'Current vtiger\'s MySQL exist in',
	'LBL_THIS_MACHINE' => 'this machine',
	'LBL_DIFFERENT_MACHINE' => 'Different Machine',
	'LBL_CURRENT_VT_MYSQL_PATH' => 'Current vtiger\'s MySQL path',
	'LBL_SOURCE_VT_MYSQL_DUMPFILE' => 'vtiger <b>Source</b> dump file name',
	'LBL_NOTE_TITLE' => 'Note:',
	'LBL_NOTES_LIST1' => 'If current MySQL exists in the same machine then enter the MySQL path (or) you can enter the dump file if you have it.',
	'LBL_NOTES_LIST2' => 'If current MySQL exist on a different machine then enter the (source) dump filename with the full path.',
	'LBL_NOTES_DUMP_PROCESS' => 'To take a database dump please execute the following command from inside the <b>mysql/bin</b> directory:<br /><b>mysqldump --user="mysql_username"  --password="mysql-password" -h "hostname"  --port="mysql_port" "database_name" > dump_filename</b><br />add <b>SET FOREIGN_KEY_CHECKS = 0;</b> at the start of the dump file<br />add <b>SET FOREIGN_KEY_CHECKS = 1;</b> at the end of the dump file',
	'LBL_NOTES_LIST3' => 'Give the MySQL path like <b>/home/crm/vtigerCRM4_5/mysql</b>',
	'LBL_NOTES_LIST4' => 'Give the dump filename with full path like <b>/home/fullpath/4_2_dump.txt</b>',
	'LBL_CURRENT_MYSQL_PATH_FOUND' => 'Current installation\'s MySQL path has been found.',
	'LBL_SOURCE_HOST_NAME' => 'Source host name: ',
	'LBL_SOURCE_MYSQL_PORT_NO' => 'Source MySQL port no.: ',
	'LBL_SOURCE_MYSQL_USER_NAME' => 'Source MySQL user name: ',
	'LBL_SOURCE_MYSQL_PASSWORD' => 'Source MySQL password: ',
	'LBL_SOURCE_DB_NAME' => 'Source database name: ',
	'LBL_MIGRATE' => 'Migrate to current version',
	'LBL_UPGRADE_VTIGER' => 'Upgrade vtiger CRM database',
	'LBL_UPGRADE_FROM_VTIGER_423' => 'Upgrade database from vtiger CRM 4.2.3 to 5.0.0',
	'LBL_SETTINGS' => 'Settings',
	'LBL_STEP' => 'Step',
	'LBL_SELECT_SOURCE' => 'Select source',
	'LBL_STEP1_DESC' => 'To start the database migration, you must specify the format in which the old data is available',
	'LBL_RADIO_BUTTON1_TEXT' => 'I have access to vtiger CRM live database system',
	'LBL_RADIO_BUTTON1_DESC' => 'This option requires you to have the host machine\'s (where the DB is stored) address and DB access details. Both local and remote systems are supported in this method. Refer to documentation for further help.',
	'LBL_RADIO_BUTTON2_TEXT' => 'I have access to vtiger CRM archived database dump',
	'LBL_RADIO_BUTTON2_DESC' => 'This option requires database dump available locally in the same machine in which you are upgrading. You cannot access data dump from a different machine (remote database server). Refer to documentation for further help.',
	'LBL_RADIO_BUTTON3_TEXT' => 'I have a new database with 4.2.3 data',
	'LBL_RADIO_BUTTON3_DESC' => 'This option requires vtiger CRM 4.2.3 database system details, including database server ID, user name, and password. You cannot access data dump from a different machine (remote database server).',
	'LBL_HOST_DB_ACCESS_DETAILS' => 'Host database access details',
	'LBL_MYSQL_HOST_NAME_IP' => 'MySQL host name or IP adddress:',
	'LBL_MYSQL_PORT' => 'MySQL port number:',
	'LBL_MYSQL_USER_NAME' => 'MySQL user name:',
	'LBL_MYSQL_PASSWORD' => 'MySQL password:',
	'LBL_DB_NAME' => 'Database Name : ',
	'LBL_LOCATE_DB_DUMP_FILE' => 'Locate database dump file',
	'LBL_DUMP_FILE_LOCATION' => 'Dump File Location : ',
	'LBL_RADIO_BUTTON3_PROCESS' => '<font color="red">Please do not specify the 4.2.3 database details. This option will alter the given database directly.</font><br />It is strongly recommended that to do the following.<br />1. Take a dump of your 4.2.3 database<br />2. Create new database (better is to create a database in the server where your vtiger 5.0 Database is running)<br />3. Apply this 4.2.3 dump to this new database<br />Now give this new database access details. This migration will modify this Database to fit wit',
	'LBL_ENTER_MYSQL_SERVER_PATH' => 'Enter MySQL server path',
	'LBL_SERVER_PATH_DESC' => 'MySQL path in the server like <b>/home/5beta/vtigerCRM5_beta/mysql/bin</b> or <b>C:Program Filesmysqlin</b>',
	'LBL_MYSQL_SERVER_PATH' => 'MySQL server path:',
	'LBL_MIGRATE_BUTTON' => 'Migrate',
	'LBL_CANCEL_BUTTON' => 'Cancel',
	'LBL_UPGRADE_FROM_VTIGER_5X' => 'Upgrade database from vtiger CRM 5.x to next version',
	'LBL_PATCH_OR_MIGRATION' => 'you must specify the source database version (Patch update or Migration)',
	'ENTER_SOURCE_HOST' => 'Please enter the source host name',
	'ENTER_SOURCE_MYSQL_PORT' => 'Please enter the source MySQL port number',
	'ENTER_SOURCE_MYSQL_USER' => 'Please enter the source MySQL user name',
	'ENTER_SOURCE_DATABASE' => 'Please enter the source database name',
	'ENTER_SOURCE_MYSQL_DUMP' => 'Please enter the valid MySQL dump File',
	'ENTER_HOST' => 'Please enter the host name',
	'ENTER_MYSQL_PORT' => 'Please enter the MySQL port number',
	'ENTER_MYSQL_USER' => 'Please enter the MySQL user name',
	'ENTER_DATABASE' => 'Please enter the database name',
	'SELECT_ANYONE_OPTION' => 'Please select any one option',
	'ENTER_CORRECT_MYSQL_PATH' => 'Please enter the correct MySQL path'
);
?>