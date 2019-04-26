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
 *************************************************************************************************/

// For Migration status.
define("MIG_CHARSET_PHP_UTF8_DB_UTF8", 1);
define("MIG_CHARSET_PHP_NONUTF8_DB_NONUTF8", 2);
define("MIG_CHARSET_PHP_NONUTF8_DB_UTF8", 3);
define("MIG_CHARSET_PHP_UTF8_DB_NONUTF8", 4);

function get_config_status() {
	global $default_charset;
	return ($default_charset == 'UTF-8' ? 1 : 0);
}

function getMigrationCharsetFlag() {
	global $adb;

	$db_status=check_db_utf8_support($adb);
	$config_status=get_config_status();

	if ($db_status == $config_status) {
		if ($db_status == 1) { // Both are UTF-8
			$db_migration_status = MIG_CHARSET_PHP_UTF8_DB_UTF8;
		} else { // Both are Non UTF-8
			$db_migration_status = MIG_CHARSET_PHP_NONUTF8_DB_NONUTF8;
		}
	} else {
		if ($db_status == 1) { // Database charset is UTF-8 and CRM charset is Non UTF-8
			$db_migration_status = MIG_CHARSET_PHP_NONUTF8_DB_UTF8;
		} else { // Database charset is Non UTF-8 and CRM charset is UTF-8
			$db_migration_status = MIG_CHARSET_PHP_UTF8_DB_NONUTF8;
		}
	}
	return $db_migration_status;
}

/** Get Smarty compiled file for the specified template filename.
 * * @param $template_file Template filename for which the compiled file has to be returned.
 * * @return Compiled file for the specified template file.
 * */
function get_smarty_compiled_file($template_file, $path = null) {

	global $root_directory;
	if ($path == null) {
		$path = $root_directory . 'Smarty/templates_c/';
	}
	$mydir = @opendir($path);
	$compiled_file = null;
	while (false !== ($file = readdir($mydir)) && $compiled_file == null) {
		if ($file != "." && $file != ".." && $file != ".svn") {
			//chmod($path.$file, 0777);
			if (is_dir($path . $file)) {
				chdir('.');
				$compiled_file = get_smarty_compiled_file($template_file, $path . $file . '/');
				//rmdir($path.$file); // No need to delete the directories.
			} else {
				// Check if the file name matches the required template fiel name
				if (strripos($file, $template_file . '.php') == (strlen($file) - strlen($template_file . '.php'))) {
					$compiled_file = $path . $file;
				}
			}
		}
	}
	@closedir($mydir);
	return $compiled_file;
}

/** Clear the Smarty cache files(in Smarty/smarty_c)
 * * This function will called after migration.
 * */
function clear_smarty_cache($path = null) {

	global $root_directory;
	if ($path == null) {
		$path = $root_directory . 'Smarty/templates_c/';
	}
	$mydir = @opendir($path);
	while (false !== ($file = readdir($mydir))) {
		if ($file != "." && $file != ".." && $file != ".svn") {
			//chmod($path.$file, 0777);
			if (is_dir($path . $file)) {
				chdir('.');
				clear_smarty_cache($path . $file . '/');
				//rmdir($path.$file); // No need to delete the directories.
			} else {
				// Delete only files ending with .tpl.php
				if (strripos($file, '.tpl.php') == (strlen($file) - strlen('.tpl.php'))) {
					unlink($path . $file);
				}
			}
		}
	}
	@closedir($mydir);
}

/** Function to carry out all the necessary actions after migration */
function perform_post_migration_activities() {
	//After applying all the DB Changes,Here we clear the Smarty cache files
	clear_smarty_cache();
	//Writing tab data in flat file
	create_tab_data_file();
	create_parenttab_data_file();
}
