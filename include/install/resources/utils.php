<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/

define('cbPackageDirectory', 'packages/');
/**
 * Provides few utility functions for installation/migration process
 * @package install
 */

class Installation_Utils {

	public static function getInstallableOptionalModules() {
		$optionalModules = Common_Install_Wizard_Utils::getInstallableModulesFromPackages();
		return $optionalModules;
	}

	// Function to install Vtlib Compliant - Optional Modules
	public static function installOptionalModules($selected_modules) {
		Common_Install_Wizard_Utils::installSelectedOptionalModules($selected_modules);
	}

	public static function getDbOptions() {
		$dbOptions = array();
		$dbOptions['mysqli'] = 'MySQL';
		return $dbOptions;
	}

	public static function checkDbConnection($db_type, $db_hostname, $db_username, $db_password, $db_name, $create_db = false, $create_utf8_db = true, $root_user = '', $root_password = '') {
		global $installationStrings;

		$dbCheckResult = array();
		require_once 'include/DatabaseUtil.php';

		$db_type_status = false; // is there a db type?
		$db_server_status = false; // does the db server connection exist?
		$db_creation_failed = false; // did we try to create a database and fail?
		$db_exist_status = false; // does the database exist?
		$db_utf8_support = false; // does the database support utf8?

		//Checking for database connection parameters
		if ($db_type) {
			$conn = NewADOConnection($db_type);
			$db_type_status = true;
			if (@$conn->Connect($db_hostname, $db_username, $db_password)) {
				$db_server_status = true;
				$serverInfo = $conn->ServerInfo();
				if (Common_Install_Wizard_Utils::isMySQL($db_type)) {
					$mysql_server_version = Common_Install_Wizard_Utils::getMySQLVersion($serverInfo);
				}
				if ($create_db) {
					// drop the current database if it exists
					$dropdb_conn = NewADOConnection($db_type);
					if (@$dropdb_conn->Connect($db_hostname, $root_user, $root_password, $db_name)) {
						$query = "drop database ".$db_name;
						$dropdb_conn->Execute($query);
						$dropdb_conn->Close();
					}

					// create the new database
					$db_creation_failed = true;
					$createdb_conn = NewADOConnection($db_type);
					if (@$createdb_conn->Connect($db_hostname, $root_user, $root_password)) {
						$query = "create database ".$db_name;
						if ($create_utf8_db == 'true') {
							if (Common_Install_Wizard_Utils::isMySQL($db_type)) {
								$query .= " default character set utf8 default collate utf8_general_ci";
							}
							$db_utf8_support = true;
						}
						if ($createdb_conn->Execute($query)) {
							$db_creation_failed = false;
						}
						$createdb_conn->Close();
					}
				}

				// test the connection to the database
				if (@$conn->Connect($db_hostname, $db_username, $db_password, $db_name)) {
					$db_exist_status = true;
					if (!$db_utf8_support) {
						// Check if the database that we are going to use supports UTF-8
						$db_utf8_support = check_db_utf8_support($conn);
					}
				}
				$conn->Close();
			}
		}
		$dbCheckResult['db_utf8_support'] = $db_utf8_support;

		$error_msg = '';
		$error_msg_info = '';

		if (!$db_type_status || !$db_server_status) {
			$error_msg = $installationStrings['ERR_DATABASE_CONNECTION_FAILED'].'. '.$installationStrings['ERR_INVALID_MYSQL_PARAMETERS'];
			$error_msg_info = $installationStrings['MSG_LIST_REASONS'].':<br>
					-  '.$installationStrings['MSG_DB_PARAMETERS_INVALID'].'.<BR>
					-  '.$installationStrings['MSG_DB_USER_NOT_AUTHORIZED'];
		} elseif (Common_Install_Wizard_Utils::isMySQL($db_type) && version_compare($mysql_server_version, '4.1', '<')) {
			$error_msg = $mysql_server_version.' -> '.$installationStrings['ERR_INVALID_MYSQL_VERSION'];
		} elseif ($db_creation_failed) {
			$error_msg = $installationStrings['ERR_UNABLE_CREATE_DATABASE'].' '.$db_name;
			$error_msg_info = $installationStrings['MSG_DB_ROOT_USER_NOT_AUTHORIZED'];
		} elseif (!$db_exist_status) {
			$error_msg = $db_name.' -> '.$installationStrings['ERR_DB_NOT_FOUND'];
		} else {
			$dbCheckResult['flag'] = true;
			return $dbCheckResult;
		}
		$dbCheckResult['flag'] = false;
		$dbCheckResult['error_msg'] = $error_msg;
		$dbCheckResult['error_msg_info'] = $error_msg_info;
		return $dbCheckResult;
	}
}

class Migration_Utils {

	public static function verifyMigrationInfo($migrationInfo) {
		global $installationStrings;

		$dbVerifyResult = array();
		$dbVerifyResult['flag'] = false;
		$configInfo = array();

		if (isset($migrationInfo['source_directory'])) {
			$source_directory = $migrationInfo['source_directory'];
		}
		if (isset($migrationInfo['root_directory'])) {
			$configInfo['root_directory'] = $migrationInfo['root_directory'];
		}
		if (is_dir($source_directory)) {
			if (!is_file($source_directory."config.inc.php")) {
				$dbVerifyResult['error_msg'] = $installationStrings['ERR_NO_CONFIG_FILE'];
				return $dbVerifyResult;
			}
			if (!is_dir($source_directory."user_privileges")) {
				$dbVerifyResult['error_msg'] = $installationStrings['ERR_NO_USER_PRIV_DIR'];
				return $dbVerifyResult;
			}
			if (!is_dir($source_directory."storage")) {
				$dbVerifyResult['error_msg'] = $installationStrings['ERR_NO_STORAGE_DIR'];
				return $dbVerifyResult;
			}
		} else {
			$dbVerifyResult['error_msg'] = $installationStrings['ERR_NO_SOURCE_DIR'];
			return $dbVerifyResult;
		}
		global $dbconfig;
		require_once $source_directory."config.inc.php";
		$old_db_name = $dbconfig['db_name'];
		$db_hostname = $dbconfig['db_server'].$dbconfig['db_port'];
		$db_username = $dbconfig['db_username'];
		$db_password = $dbconfig['db_password'];
		$db_type = $dbconfig['db_type'];

		if (isset($migrationInfo['user_name'])) {
			$user_name = $migrationInfo['user_name'];
		}
		if (isset($migrationInfo['user_pwd'])) {
			$user_pwd = $migrationInfo['user_pwd'];
		}
		if (isset($migrationInfo['old_version'])) {
			$source_version = $migrationInfo['old_version'];
		}
		if (isset($migrationInfo['new_dbname'])) {
			$new_db_name = $migrationInfo['new_dbname'];
		}

		$configInfo['db_name'] = $new_db_name;
		$configInfo['db_type'] = $db_type;
		$configInfo['db_hostname'] = $db_hostname;
		$configInfo['db_username'] = $db_username;
		$configInfo['db_password'] = $db_password;
		$configInfo['admin_email'] = $HELPDESK_SUPPORT_EMAIL_ID;
		$configInfo['currency_name'] = $currency_name;

		$dbVerifyResult['old_dbname'] = $old_db_name;

		$db_type_status = false; // is there a db type?
		$db_server_status = false; // does the db server connection exist?
		$old_db_exist_status = false; // does the old database exist?
		$db_utf8_support = false; // does the database support utf8?
		$new_db_exist_status = false; // does the new database exist?
		$new_db_has_tables = false; // does the new database has tables in it?

		require_once 'include/DatabaseUtil.php';
		//Checking for database connection parameters and copying old database into new database
		if ($db_type) {
			$conn = NewADOConnection($db_type);
			$db_type_status = true;
			if (@$conn->Connect($db_hostname, $db_username, $db_password)) {
				$db_server_status = true;
				$serverInfo = $conn->ServerInfo();
				if (Common_Install_Wizard_Utils::isMySQL($db_type)) {
					$mysql_server_version = Common_Install_Wizard_Utils::getMySQLVersion($serverInfo);
				}

				// test the connection to the old database
				$olddb_conn = NewADOConnection($db_type);
				if (@$olddb_conn->Connect($db_hostname, $db_username, $db_password, $old_db_name)) {
					$old_db_exist_status = true;
					if (version_compare(PHP_VERSION, '5.3.0') >= 0) {
						$sql = 'alter table vtiger_users change user_password user_password varchar(128)';
						$alterResult = $olddb_conn->_Execute($sql);
						if (!is_object($alterResult)) {
							$dbVerifyResult['error_msg'] =
								$installationStrings['LBL_PASSWORD_FIELD_CHANGE_FAILURE'];
						}
						if (!is_array($_SESSION['migration_info']['user_messages'])) {
							unset($_SESSION['migration_info']['user_messages']);
							$_SESSION['migration_info']['user_messages'] = array();
							$_SESSION['migration_info']['user_messages'][] = array(
							'status' => "<span style='color: red;font-weight: bold'>".
									$installationStrings['LBL_IMPORTANT_NOTE']."</span>",
								'msg' => "<span style='color: #3488cc;font-weight: bold'>".
									$installationStrings['LBL_USER_PASSWORD_CHANGE_NOTE']."</span>"
							);
						}

						if (self::resetUserPasswords($olddb_conn)) {
							$_SESSION['migration_info']['user_pwd'] = $user_name;
							$migrationInfo['user_pwd'] = $user_name;
							$user_pwd = $user_name;
						}
					}

					if (Migration_Utils::authenticateUser($olddb_conn, $user_name, $user_pwd)==true) {
						$is_admin = true;
					} else {
						$dbVerifyResult['error_msg'] = $installationStrings['ERR_NOT_VALID_USER'];
						return $dbVerifyResult;
					}
					$olddb_conn->Close();
				}

				// test the connection to the new database
				$newdb_conn = NewADOConnection($db_type);
				if (@$newdb_conn->Connect($db_hostname, $db_username, $db_password, $new_db_name)) {
					$new_db_exist_status = true;
					$noOfTablesInNewDb = Migration_Utils::getNumberOfTables($newdb_conn);
					if ($noOfTablesInNewDb > 0) {
						$new_db_has_tables = true;
					}
					$db_utf8_support = check_db_utf8_support($newdb_conn);
					$configInfo['vt_charset'] = ($db_utf8_support)? "UTF-8" : "ISO-8859-1";
					$newdb_conn->Close();
				}
			}
			$conn->Close();
		}

		if (!$db_type_status || !$db_server_status) {
			$error_msg = $installationStrings['ERR_DATABASE_CONNECTION_FAILED'].'. '.$installationStrings['ERR_INVALID_MYSQL_PARAMETERS'];
			$error_msg_info = $installationStrings['MSG_LIST_REASONS'].':<br>
					-  '.$installationStrings['MSG_DB_PARAMETERS_INVALID'].'.<BR>
					-  '.$installationStrings['MSG_DB_USER_NOT_AUTHORIZED'];
		} elseif (Common_Install_Wizard_Utils::isMySQL($db_type) && $mysql_server_version < '4.1') {
			$error_msg = $mysql_server_version.' -> '.$installationStrings['ERR_INVALID_MYSQL_VERSION'];
		} elseif (!$old_db_exist_status) {
			$error_msg = $old_db_name.' -> '.$installationStrings['ERR_DATABASE_NOT_FOUND'];
		} elseif (!$new_db_exist_status) {
			$error_msg = $new_db_name.' -> '.$installationStrings['ERR_DATABASE_NOT_FOUND'];
		} elseif (!$new_db_has_tables) {
			$error_msg = $new_db_name.' -> '.$installationStrings['ERR_MIGRATION_DATABASE_IS_EMPTY'];
		} else {
			$web_root = ($_SERVER["HTTP_HOST"]) ? $_SERVER["HTTP_HOST"]:$_SERVER['SERVER_NAME'].':'.$_SERVER['SERVER_PORT'];
			$web_root .= $_SERVER["REQUEST_URI"];
			$web_root = preg_replace("/\/install.php(.)*/i", "", $web_root);
			$site_URL = "http://".$web_root;
			$configInfo['site_URL'] = $site_URL;
			$dbVerifyResult['config_info'] = $configInfo;
			$dbVerifyResult['flag'] = true;
			return $dbVerifyResult;
		}
		$dbVerifyResult['config_info'] = $configInfo;
		$dbVerifyResult['error_msg'] = $error_msg;
		$dbVerifyResult['error_msg_info'] = $error_msg_info;
		return $dbVerifyResult;
	}

	private static function authenticateUser($dbConnection, $userName, $userPassword) {
		$userResult = $dbConnection->_Execute("SELECT * FROM vtiger_users WHERE user_name = '$userName'");
		$noOfRows = $userResult->NumRows($userResult);
		if ($noOfRows > 0) {
			$userInfo = $userResult->GetRowAssoc(0);
			$cryptType = $userInfo['crypt_type'];
			$userEncryptedPassword = $userInfo['user_password'];
			$userStatus = $userInfo['status'];
			$isAdmin = $userInfo['is_admin'];

			$computedEncryptedPassword = self::getEncryptedPassword($userName, $cryptType, $userPassword);

			if ($userEncryptedPassword == $computedEncryptedPassword && $userStatus == 'Active' && $isAdmin == 'on') {
				return true;
			}
		}
		return false;
	}

	private static function getNumberOfTables($dbConnection) {
		$metaTablesSql = $dbConnection->metaTablesSQL;
		if (substr($metaTablesSql, -1)=='=') {
			$metaTablesSql.="'".$dbConnection->database."'";
		}
		$noOfTables = 0;
		if (!empty($metaTablesSql)) {
			$tablesResult = $dbConnection->_Execute($metaTablesSql);
			$noOfTables = $tablesResult->NumRows($tablesResult);
		}
		return $noOfTables;
	}

	public static function copyRequiredFiles($sourceDirectory, $destinationDirectory) {
		if (realpath($sourceDirectory) == realpath($destinationDirectory)) {
			return;
		}
		@Migration_Utils::getFilesFromFolder($sourceDirectory."user_privileges/", $destinationDirectory."user_privileges/");
		@Migration_Utils::getFilesFromFolder($sourceDirectory."storage/", $destinationDirectory."storage/");
		@Migration_Utils::getFilesFromFolder($sourceDirectory."test/contact/", $destinationDirectory."test/contact/");
		@Migration_Utils::getFilesFromFolder($sourceDirectory."test/logo/", $destinationDirectory."test/logo/");
		@Migration_Utils::getFilesFromFolder($sourceDirectory."test/product/", $destinationDirectory."test/product/");
		@Migration_Utils::getFilesFromFolder($sourceDirectory."test/user/", $destinationDirectory."test/user/");
	}

	private static function getFilesFromFolder($source, $dest, $forcecopy = false) {
		if (!$forcecopy) {
			$forcecopy = array();
		}

		if ($handle = opendir($source)) {
			while (false != ($file = readdir($handle))) {
				if (is_file($source.$file)) {
					if (!file_exists($dest.$file) || in_array($source.$file, $forcecopy)) {
						$file_handle = fopen($dest.$file, 'w');
						fclose($file_handle);
						copy($source.$file, $dest.$file);
					}
				} elseif ($file != '.' && $file != '..' && is_dir($source.$file)) {
					if (!file_exists("$dest.$file")) {
						mkdir($dest.$file.'/', 0777);
					}
					Migration_Utils::getFilesFromFolder($source.$file.'/', $dest.$file.'/');
				}
			}
		}
		@closedir($handle);
	}

	public static function getInstallableOptionalModules() {
		$optionalModules = Common_Install_Wizard_Utils::getInstallableModulesFromPackages();

		$skipModules = array();
		if (!empty($optionalModules['install'])) {
			$skipModules = array_merge($skipModules, array_keys($optionalModules['install']));
		}
		if (!empty($optionalModules['update'])) {
			$skipModules = array_merge($skipModules, array_keys($optionalModules['update']));
		}

		$mandatoryModules = Common_Install_Wizard_Utils::getMandatoryModuleList();
		$oldVersion = str_replace(
			array('.', ' '),
			array('', ''),
			$_SESSION['migration_info']['old_version']
		);
		$customModules = array();
		if (version_compare($oldVersion, '502') > 0) {
			$customModules = Migration_Utils::getCustomModulesFromDB(array_merge(
				$skipModules,
				$mandatoryModules
			));
		}
		$optionalModules = array_merge($optionalModules, $customModules);
		return $optionalModules;
	}

	public static function getCustomModulesFromDB($skipModules) {
		global $optionalModuleStrings, $adb;

		require_once 'vtlib/Vtiger/Package.php';
		require_once 'vtlib/Vtiger/Module.php';
		require_once 'vtlib/Vtiger/Version.php';

		$customModulesResult = $adb->pquery(
			'SELECT tabid, name FROM vtiger_tab WHERE customized=1 AND name NOT IN ('.generateQuestionMarks($skipModules).')',
			$skipModules
		);
		$noOfCustomModules = $adb->num_rows($customModulesResult);
		$customModules = array();
		for ($i=0; $i<$noOfCustomModules; ++$i) {
			$tabId = $adb->query_result($customModulesResult, $i, 'tabid');
			$moduleName = $adb->query_result($customModulesResult, $i, 'name');
			$moduleDetails = array();
			$moduleDetails['description'] = $optionalModuleStrings[$moduleName.'_description'];
			$moduleDetails['selected'] = false;
			$moduleDetails['enabled'] = false;

			if (Vtiger_Utils::checkTable('vtiger_tab_info')) {
				$tabInfo = getTabInfo($tabId);
				if (Vtiger_Version::check($tabInfo['vtiger_min_version'], '>=') && Vtiger_Version::check($tabInfo['vtiger_max_version'], '<')) {
					$moduleDetails['selected'] = true;
					$moduleDetails['enabled'] = false;
				}
			}
			$customModules['copy'][$moduleName] = $moduleDetails;
		}
		return $customModules;
	}

	// Function to install Vtlib Compliant - Optional Modules
	public static function installOptionalModules($selectedModules, $sourceDirectory, $destinationDirectory) {
		Migration_Utils::copyCustomModules($selectedModules, $sourceDirectory, $destinationDirectory);
		Common_Install_Wizard_Utils::installSelectedOptionalModules($selectedModules, $sourceDirectory, $destinationDirectory);
	}

	private static function copyCustomModules($selectedModules, $sourceDirectory, $destinationDirectory) {
		global $adb;
		$selectedModules = explode(":", $selectedModules);

		$customModulesResult = $adb->pquery('SELECT tabid, name FROM vtiger_tab WHERE customized = 1', array());
		$noOfCustomModules = $adb->num_rows($customModulesResult);
		$mandatoryModules = Common_Install_Wizard_Utils::getMandatoryModuleList();
		$optionalModules = Common_Install_Wizard_Utils::getInstallableModulesFromPackages();
		$skipModules = array_merge($mandatoryModules, $optionalModules);
		for ($i=0; $i<$noOfCustomModules; ++$i) {
			$moduleName = $adb->query_result($customModulesResult, $i, 'name');
			if (!in_array($moduleName, $skipModules)) {
				Migration_Utils::copyModuleFiles($moduleName, $sourceDirectory, $destinationDirectory);
				if (!in_array($moduleName, $selectedModules)) {
					vtlib_toggleModuleAccess((string)$moduleName, false);
				}
			}
		}
	}

	public static function copyModuleFiles($moduleName, $sourceDirectory, $destinationDirectory) {
		$sourceDirectory = realpath($sourceDirectory);
		$destinationDirectory = realpath($destinationDirectory);
		if (!empty($moduleName) && !empty($sourceDirectory) && !empty($destinationDirectory) && $sourceDirectory != $destinationDirectory) {
			if (file_exists("$sourceDirectory/modules/$moduleName")) {
				if (!file_exists("$destinationDirectory/modules/$moduleName")) {
					mkdir("$destinationDirectory/modules/$moduleName".'/', 0777);
				}
				Migration_Utils::getFilesFromFolder("{$sourceDirectory}/modules/$moduleName/", "{$destinationDirectory}/modules/$moduleName/");
			}
			if (file_exists("$sourceDirectory/Smarty/templates/modules/$moduleName")) {
				if (!file_exists("$destinationDirectory/Smarty/templates/modules/$moduleName")) {
					mkdir("$destinationDirectory/Smarty/templates/modules/$moduleName".'/', 0777);
				}
				Migration_Utils::getFilesFromFolder("{$sourceDirectory}/Smarty/templates/modules/$moduleName/", "{$destinationDirectory}/Smarty/templates/modules/$moduleName/");
			}
			if (file_exists("$sourceDirectory/cron/modules/$moduleName")) {
				if (!file_exists("$destinationDirectory/cron/modules/$moduleName")) {
					mkdir("$destinationDirectory/cron/modules/$moduleName".'/', 0777);
				}
				Migration_Utils::getFilesFromFolder("{$sourceDirectory}/cron/modules/$moduleName/", "{$destinationDirectory}/cron/modules/$moduleName/");
			}
		}
	}

	public function migrate($migrationInfo) {
		global $installationStrings;
		$completed = false;

		set_time_limit(0);//ADDED TO AVOID UNEXPECTED TIME OUT WHILE MIGRATING

		global $dbconfig;
		require $migrationInfo['root_directory'] . '/config.inc.php';
		$dbtype		= $dbconfig['db_type'];
		$host		= $dbconfig['db_server'].$dbconfig['db_port'];
		$dbname		= $dbconfig['db_name'];
		$username	= $dbconfig['db_username'];
		$passwd		= $dbconfig['db_password'];

		global $adb,$migrationlog;
		$adb = new PearDatabase($dbtype, $host, $dbname, $username, $passwd);

		$query = " ALTER DATABASE ".$adb->escapeDbName($dbname)." DEFAULT CHARACTER SET utf8";
		$adb->query($query);

		$source_directory = $migrationInfo['source_directory'];
		if (file_exists($source_directory.'user_privileges/CustomInvoiceNo.php')) {
			require_once $source_directory.'user_privileges/CustomInvoiceNo.php';
		}

		$migrationlog = LoggerManager::getLogger('MIGRATION');
		if (isset($migrationInfo['old_version'])) {
			$source_version = $migrationInfo['old_version'];
		}
		if (!isset($source_version) || empty($source_version)) {
			//If source version is not set then we cannot proceed
			echo "<br> ".$installationStrings['LBL_SOURCE_VERSION_NOT_SET'];
			exit;
		}

		$reach = 0;
		include $migrationInfo['root_directory']."/modules/Migration/versions.php";
		foreach ($versions as $version => $label) {
			if ($version == $source_version || $reach == 1) {
				$reach = 1;
				$temp[] = $version;
			}
		}
		$temp[] = $current_version;

		global $adb, $dbname;
		$_SESSION['adodb_current_object'] = $adb;

		@ini_set('zlib.output_compression', 0);
		@ini_set('output_buffering', 'off');
		ob_implicit_flush(true);
		echo '<table width="98%" border="1px" cellpadding="3" cellspacing="0" height="100%">';
		if (is_array($_SESSION['migration_info']['user_messages'])) {
			foreach ($_SESSION['migration_info']['user_messages'] as $infoMap) {
				echo "<tr><td>".$infoMap['status']."</td><td>".$infoMap['msg']."</td></tr>";
			}
		}
		echo "<tr><td colspan='2'><b>{$installationStrings['LBL_GOING_TO_APPLY_DB_CHANGES']}...</b></td></tr>";

		for ($patch_count=0; $patch_count<count($temp); $patch_count++) {
			//Here we have to include all the files (all db differences for each release will be included)
			$filename = "modules/Migration/DBChanges/".$temp[$patch_count]."_to_".$temp[$patch_count+1].".php";
			$empty_tag = "<tr><td colspan='2'>&nbsp;</td></tr>";
			$start_tag = "<tr><td colspan='2'><b><font color='red'>&nbsp;";
			$end_tag = "</font></b></td></tr>";

			if (is_file($filename)) {
				echo $empty_tag.$start_tag.$temp[$patch_count]." ==> ".$temp[$patch_count+1]. " " .$installationStrings['LBL_DATABASE_CHANGES'] ." -- ". $installationStrings['LBL_STARTS'] .".".$end_tag;

				include $filename;//include the file which contains the corresponding db changes

				echo $start_tag.$temp[$patch_count]." ==> ".$temp[$patch_count+1]. " " .$installationStrings['LBL_DATABASE_CHANGES'] ." -- ". $installationStrings['LBL_ENDS'] .".".$end_tag;
			}
		}

		/* Install Vtlib Compliant Modules */
		Common_Install_Wizard_Utils::installMandatoryModules();
		Migration_Utils::installOptionalModules($migrationInfo['selected_optional_modules'], $migrationInfo['source_directory'], $migrationInfo['root_directory']);

		//Here we have to update the version in table. so that when we do migration next time we will get the version
		$res = $adb->query('SELECT * FROM vtiger_version');
		global $vtiger_current_version;
		require $migrationInfo['root_directory'].'/vtigerversion.php';
		if ($adb->num_rows($res)) {
			$res = ExecuteQuery("UPDATE vtiger_version SET old_version='$versions[$source_version]',current_version='$vtiger_current_version'");
			$completed = true;
		} else {
			ExecuteQuery("INSERT INTO vtiger_version (id, old_version, current_version) values (".$adb->getUniqueID('vtiger_version').", '$versions[$source_version]', '$vtiger_current_version');");
			$completed = true;
		}
		echo '</table><br><br>';

		create_tab_data_file();
		create_parenttab_data_file();
		return $completed;
	}

	public static function resetUserPasswords($con) {
		$sql = 'select * from vtiger_users';
		$result = $con->_Execute($sql, false);
		$rowList = $result->GetRows();
		foreach ($rowList as $row) {
			if (!isset($row['crypt_type'])) {
				return false;
			}
			$cryptType = $row['crypt_type'];
			if (strtolower($cryptType) == 'md5' && version_compare(PHP_VERSION, '5.3.0') >= 0) {
				$cryptType = 'PHP5.3MD5';
			}
			$encryptedPassword = self::getEncryptedPassword(
				$row['user_name'],
				$cryptType,
				$row['user_name']
			);
			$userId = $row['id'];
			$sql = "update vtiger_users set user_password=?,crypt_type=? where id=?";
			$updateResult = $con->Execute($sql, array($encryptedPassword, $cryptType, $userId));
			if (!is_object($updateResult)) {
				$_SESSION['migration_info']['user_messages'][] = array(
					'status' => "<span style='color: red;font-weight: bold'>Failed: </span>",
					'msg' => "$sql<br /> $userId<br />"
				);
			}
		}
		return true;
	}

	public static function getEncryptedPassword($userName, $cryptType, $userPassword) {
		$salt = substr($userName, 0, 2);
		// For more details on salt format look at: http://in.php.net/crypt
		if ($cryptType == 'MD5') {
			$salt = '$1$' . $salt . '$';
		} elseif ($cryptType == 'BLOWFISH') {
			$salt = '$2$' . $salt . '$';
		} elseif ($cryptType == 'PHP5.3MD5') {
			//only change salt for php 5.3 or higher version for backward
			//compactibility.
			//crypt API is lot stricter in taking the value for salt.
			$salt = '$1$' . str_pad($salt, 9, '0');
		}
		$computedEncryptedPassword = crypt($userPassword, $salt);
		return $computedEncryptedPassword;
	}

	public static function copyLanguageFiles($sourceDirectory, $destinationDirectory) {
		global $adb;
		$result = $adb->pquery('select * from vtiger_language', array());
		$it = new SqlResultIterator($adb, $result);
		$installedLanguages = array();
		$defaultLanguage = 'en_us';
		foreach ($it as $row) {
			if ($row->prefix !== $defaultLanguage) {
				$installedLanguages[] = $row->prefix;
			}
		}
		self::copyLanguageFileFromFolder(
			$sourceDirectory,
			$destinationDirectory,
			$installedLanguages
		);
	}

	public static function copyLanguageFileFromFolder(
		$sourceDirectory,
		$destinationDirectory,
		$installedLanguages
	) {
		$ignoreDirectoryList = array('.', '..', 'storage','themes','fckeditor', 'HTMLPurifier');
		if ($handle = opendir($sourceDirectory)) {
			while (false !== ($file = readdir($handle))) {
				if (is_dir($sourceDirectory.DIRECTORY_SEPARATOR.$file) && !in_array(
					$file,
					$ignoreDirectoryList
				)) {
					self::copyLanguageFileFromFolder(
						$sourceDirectory.DIRECTORY_SEPARATOR.$file,
						$destinationDirectory.DIRECTORY_SEPARATOR.$file,
						$installedLanguages
					);
					continue;
				} elseif (in_array($file, $ignoreDirectoryList)) {
					continue;
				}
				$found = false;
				foreach ($installedLanguages as $prefix) {
					if (strpos($file, $prefix) === 0) {
						$found = true;
						break;
					}
				}
				if (!empty($file) && $found == true) {
					copy($sourceDirectory.DIRECTORY_SEPARATOR.$file, $destinationDirectory.
							DIRECTORY_SEPARATOR.$file);
				}
			}
			closedir($handle);
		}
	}
}

class ConfigFile_Utils {

	private $rootDirectory;
	private $dbHostname;
	private $dbPort;
	private $dbUsername;
	private $dbPassword;
	private $dbName;
	private $dbType;
	private $siteUrl;
	private $cacheDir;
	private $vtCharset;
	private $currencyName;
	private $adminEmail;

	public function __construct($configFileParameters) {
		if (isset($configFileParameters['root_directory'])) {
			$this->rootDirectory = $configFileParameters['root_directory'];
		}

		if (isset($configFileParameters['db_hostname'])) {
			if (strpos($configFileParameters['db_hostname'], ":")) {
				list($this->dbHostname,$this->dbPort) = explode(":", $configFileParameters['db_hostname']);
			} else {
				$this->dbHostname = $configFileParameters['db_hostname'];
			}
		}

		if (isset($configFileParameters['db_username'])) {
			$this->dbUsername = $configFileParameters['db_username'];
		}
		if (isset($configFileParameters['db_password'])) {
			$this->dbPassword = $configFileParameters['db_password'];
		}
		if (isset($configFileParameters['db_name'])) {
			$this->dbName = $configFileParameters['db_name'];
		}
		if (isset($configFileParameters['db_type'])) {
			$this->dbType = $configFileParameters['db_type'];
		}
		if (isset($configFileParameters['site_URL'])) {
			$this->siteUrl = $configFileParameters['site_URL'];
		}
		if (isset($configFileParameters['admin_email'])) {
			$this->adminEmail = $configFileParameters['admin_email'];
		}
		if (isset($configFileParameters['currency_name'])) {
			$this->currencyName = $configFileParameters['currency_name'];
		}
		if (isset($configFileParameters['vt_charset'])) {
			$this->vtCharset = $configFileParameters['vt_charset'];
		}

		// update default port
		if ($this->dbPort == '') {
			$this->dbPort = ConfigFile_Utils::getDbDefaultPort($this->dbType);
		}
		$this->cacheDir = 'cache/';
	}

	public static function getDbDefaultPort($dbType) {
		if (Common_Install_Wizard_Utils::isMySQL($dbType)) {
			return "3306";
		}
		if (Common_Install_Wizard_Utils::isPostgres($dbType)) {
			return "5432";
		}
		if (Common_Install_Wizard_Utils::isOracle($dbType)) {
			return '1521';
		}
	}

	public function createConfigFile() {
		if (is_file('config.inc.php')) {
			$is_writable = is_writable('config.inc.php');
		} else {
			$is_writable = is_writable('.');
		}

		/* open template configuration file read only */
		$templateFilename = 'config.template.php';
		$templateHandle = fopen($templateFilename, "r");
		if ($templateHandle) {
			/* open include configuration file write only */
			$includeFilename = 'config.inc.php';
			$includeHandle = fopen($includeFilename, "w");
			if ($includeHandle) {
				while (!feof($templateHandle)) {
					$buffer = fgets($templateHandle);

					/* replace _DBC_ variable */
					$buffer = str_replace("_DBC_SERVER_", $this->dbHostname, $buffer);
					$buffer = str_replace("_DBC_PORT_", $this->dbPort, $buffer);
					$buffer = str_replace("_DBC_USER_", $this->dbUsername, $buffer);
					$buffer = str_replace("_DBC_PASS_", $this->dbPassword, $buffer);
					$buffer = str_replace("_DBC_NAME_", $this->dbName, $buffer);
					$buffer = str_replace("_DBC_TYPE_", $this->dbType, $buffer);

					$buffer = str_replace("_SITE_URL_", $this->siteUrl, $buffer);

					/* replace dir variable */
					$buffer = str_replace("_VT_ROOTDIR_", $this->rootDirectory, $buffer);
					$buffer = str_replace("_VT_CACHEDIR_", $this->cacheDir, $buffer);
					$buffer = str_replace("_VT_TMPDIR_", $this->cacheDir."images/", $buffer);
					$buffer = str_replace("_VT_UPLOADDIR_", $this->cacheDir."upload/", $buffer);
					$buffer = str_replace("_DB_STAT_", "true", $buffer);

					/* replace charset variable */
					$buffer = str_replace("_VT_CHARSET_", $this->vtCharset, $buffer);

					/* replace master currency variable */
					$buffer = str_replace("_MASTER_CURRENCY_", $this->currencyName, $buffer);

					/* replace the application unique key variable */
					$buffer = str_replace("_VT_APP_UNIQKEY_", md5((time() + rand(1, 9999999)) . $this->rootDirectory), $buffer);

					fwrite($includeHandle, $buffer);
				}
				fclose($includeHandle);
			}
			fclose($templateHandle);
		}

		if ($templateHandle && $includeHandle) {
			return true;
		}
		return false;
	}
}

class Common_Install_Wizard_Utils {

	public static $recommendedDirectives = array (
		'safe_mode' => 'Off',
		'display_errors' => 'Off',
		'file_uploads' => 'On',
		'register_globals' => 'Off',
		'output_buffering' => 'On',
		'max_execution_time' => '600',
		'memory_limit' => '32',
		'error_reporting' => 'E_ERROR',
		'allow_call_time_pass_reference' => 'Off',
		'short_open_tag' => 'Off',
		'max_input_vars' => '9000',
	);

	public static $writableFilesAndFolders = array (
		'Configuration File' => './config.inc.php',
		'Tabdata File' => './tabdata.php',
		'Installation File' => './install.php',
		'Cache Directory' => './cache/',
		'Image Cache Directory' => './cache/images/',
		'Import Cache Directory' => './cache/import/',
		'Storage Directory' => './storage/',
		'Install Directory' => './install/',
		'User Privileges Directory' => './user_privileges/',
		'Smarty Cache Directory' => './Smarty/cache/',
		'Smarty Compile Directory' => './Smarty/templates_c/',
		'Modules Directory' => './modules/',
		'Cron Modules Directory' => './cron/modules/',
		'Backup Directory' => './backup/',
		'Smarty Modules Directory' => './Smarty/templates/modules/',
		'Logo Directory' => './test/logo/',
		'Logs Directory' => './logs/',
	);

	public static function getRecommendedDirectives() {
		return self::$recommendedDirectives;
	}

	/** Function to check the file access is made within web root directory. */
	public static function checkFileAccess($filepath) {
		global $root_directory, $installationStrings;
		// Set the base directory to compare with
		$use_root_directory = $root_directory;
		if (empty($use_root_directory)) {
			$use_root_directory = realpath(dirname(__FILE__).'/../../..');
		}

		$realfilepath = realpath($filepath);

		/** Replace all \\ with \ first */
		$realfilepath = str_replace('\\\\', '\\', $realfilepath);
		$rootdirpath  = str_replace('\\\\', '\\', $use_root_directory);

		/** Replace all \ with / now */
		$realfilepath = str_replace('\\', '/', $realfilepath);
		$rootdirpath  = str_replace('\\', '/', $rootdirpath);

		if (stripos($realfilepath, $rootdirpath) !== 0) {
			die($installationStrings['ERR_RESTRICTED_FILE_ACCESS']);
		}
	}

	/** Function to check the file access is made within web root directory. */
	public static function checkFileAccessForInclusion($filepath) {
		global $root_directory, $installationStrings;
		// Set the base directory to compare with
		$use_root_directory = $root_directory;
		if (empty($use_root_directory)) {
			$use_root_directory = realpath(dirname(__FILE__).'/../../..');
		}

		$unsafeDirectories = array('storage', 'cache', 'test');

		$realfilepath = realpath($filepath);

		/** Replace all \\ with \ first */
		$realfilepath = str_replace('\\\\', '\\', $realfilepath);
		$rootdirpath  = str_replace('\\\\', '\\', $use_root_directory);

		/** Replace all \ with / now */
		$realfilepath = str_replace('\\', '/', $realfilepath);
		$rootdirpath  = str_replace('\\', '/', $rootdirpath);

		$relativeFilePath = str_replace($rootdirpath, '', $realfilepath);
		$filePathParts = explode('/', $relativeFilePath);

		if (stripos($realfilepath, $rootdirpath) !== 0 || in_array($filePathParts[0], $unsafeDirectories)) {
			die($installationStrings['ERR_RESTRICTED_FILE_ACCESS']);
		}
	}

	public static function getFailedPermissionsFiles() {
		$writableFilesAndFolders = Common_Install_Wizard_Utils::$writableFilesAndFolders;
		$failedPermissions = array();
		require_once 'include/utils/VtlibUtils.php';
		foreach ($writableFilesAndFolders as $index => $value) {
			if (!vtlib_isWriteable($value)) {
				$failedPermissions[$index] = $value;
			}
		}
		return $failedPermissions;
	}

	public static function getCurrentDirectiveValue() {
		$directiveValues = array();
		if (ini_get('safe_mode') == '1' || stripos(ini_get('safe_mode'), 'On') > -1) {
			$directiveValues['safe_mode'] = 'On';
		}
		if (ini_get('display_errors') == '1' || stripos(ini_get('display_errors'), 'On') > -1) {
			$directiveValues['display_errors'] = 'On';
		}
		if (ini_get('file_uploads') != '1' || stripos(ini_get('file_uploads'), 'Off') > -1) {
			$directiveValues['file_uploads'] = 'Off';
		}
		if (ini_get('register_globals') == '1' || stripos(ini_get('register_globals'), 'On') > -1) {
			$directiveValues['register_globals'] = 'On';
		}
		if (ini_get(('output_buffering') < '4096' && ini_get('output_buffering') != '0') || stripos(ini_get('output_buffering'), 'Off') > -1) {
			$directiveValues['output_buffering'] = 'Off';
		}
		if (ini_get('max_execution_time') < 600) {
			$directiveValues['max_execution_time'] = ini_get('max_execution_time');
		}
		if (ini_get('memory_limit') < 32) {
			$directiveValues['memory_limit'] = ini_get('memory_limit');
		}
		if (ini_get('max_input_vars') < 9000) {
			$directiveValues['max_input_vars'] = ini_get('max_input_vars');
		}
		eval('$errorReportingValue = '.self::$recommendedDirectives['error_reporting'].';');
		if (ini_get('error_reporting') != $errorReportingValue) {
			$directiveValues['error_reporting'] = 'NOT RECOMMENDED';
		}
		if (ini_get('allow_call_time_pass_reference') == '1' || stripos(ini_get('allow_call_time_pass_reference'), 'On') > -1) {
			$directiveValues['allow_call_time_pass_reference'] = 'On';
		}
		if (ini_get('short_open_tag') == '1' || stripos(ini_get('short_open_tag'), 'On') > -1) {
			$directiveValues['short_open_tag'] = 'On';
		}

		return $directiveValues;
	}

	public static function check_mysql_extension() {
		if (function_exists('mysql_connect') || function_exists('mysqli_connect')) {
			$mysql_extension = true;
		} else {
			$mysql_extension = false;
		}
		return $mysql_extension;
	}

	public static function isMySQL($dbType) {
		return (stripos($dbType, 'mysql') === 0);
	}

	public static function isOracle($dbType) {
		return $dbType == 'oci8';
	}

	public static function isPostgres($dbType) {
		return $dbType == 'pgsql';
	}

	public static function getInstallableModulesFromPackages() {
		global $optionalModuleStrings;

		require_once 'vtlib/Vtiger/Package.php';
		require_once 'vtlib/Vtiger/Module.php';
		require_once 'vtlib/Vtiger/Version.php';

		$packageDir = cbPackageDirectory.'optional/';
		$handle = opendir($packageDir);
		$optionalModules = array();

		while (false !== ($file = readdir($handle))) {
			$packageNameParts = explode(".", $file);
			if ($packageNameParts[count($packageNameParts)-1] != 'zip') {
				continue;
			}
			array_pop($packageNameParts);
			$packageName = implode("", $packageNameParts);
			if (!empty($packageName)) {
				$packagepath = "$packageDir/$file";
				$package = new Vtiger_Package();
				$moduleName = $package->getModuleNameFromZip($packagepath);
				$desc = $package->getShortDescriptionFromZip($packagepath);
				if ($package->isModuleBundle()) {
					$bundleOptionalModule = array();
					$unzip = new Vtiger_Unzip($packagepath);
					$unzip->unzipAllEx($package->getTemporaryFilePath());
					$moduleInfoList = $package->getAvailableModuleInfoFromModuleBundle();
					foreach ($moduleInfoList as $moduleInfo) {
						$moduleInfo = (Array)$moduleInfo;
						$packagepath = $package->getTemporaryFilePath($moduleInfo['filepath']);
						$subModule = new Vtiger_Package();
						$subModule->getModuleNameFromZip($packagepath);
						$bundleOptionalModule = self::getOptionalModuleDetails(
							$subModule,
							$bundleOptionalModule
						);
					}
					$moduleDetails = array();
					$moduleDetails['description'] = (empty($desc) ? $optionalModuleStrings[$moduleName.'_description'] : $desc);
					$moduleDetails['selected'] = true;
					$moduleDetails['enabled'] = true;
					$migrationAction = 'install';
					if (isset($bundleOptionalModule['update']) && count($bundleOptionalModule['update']) > 0) {
						$moduleDetails['enabled'] = false;
						$migrationAction = 'update';
					}
					$optionalModules[$migrationAction]['module'][$moduleName] = $moduleDetails;
				} else {
					if ($package->isLanguageType()) {
						$package = new Vtiger_Language();
						$package->getModuleNameFromZip($packagepath);
					}
					$optionalModules = self::getOptionalModuleDetails($package, $optionalModules);
				}
			}
		}
		if (is_array($optionalModules['install']['language']) &&
				is_array($optionalModules['install']['module'])) {
			$optionalModules['install'] = array_merge(
				$optionalModules['install']['module'],
				$optionalModules['install']['language']
			);
		} elseif (is_array($optionalModules['install']['language']) &&
				!is_array($optionalModules['install']['module'])) {
			$optionalModules['install'] = $optionalModules['install']['language'];
		} else {
			$optionalModules['install'] = $optionalModules['install']['module'];
		}
		if (isset($optionalModules['update']['language']) || isset($optionalModules['update']['module'])) {
			if (is_array($optionalModules['update']['language']) &&
				is_array($optionalModules['update']['module'])) {
				$optionalModules['update'] = array_merge(
					$optionalModules['update']['module'],
					$optionalModules['update']['language']
				);
			} elseif (is_array($optionalModules['update']['language']) &&
				!is_array($optionalModules['update']['module'])) {
				$optionalModules['update'] = $optionalModules['update']['language'];
			} else {
				$optionalModules['update'] = $optionalModules['update']['module'];
			}
		}
		return $optionalModules;
	}

	/**
	 *
	 * @param String $packagepath - path to the package file.
	 * @return Array
	 */
	public static function getOptionalModuleDetails($package, $optionalModulesInfo) {
		global $optionalModuleStrings;

		$moduleUpdateVersion = $package->getVersion();
		$moduleForVtigerVersion = $package->getDependentVtigerVersion();
		$moduleMaxVtigerVersion = $package->getDependentMaxVtigerVersion();
		$desc = $package->getShortDescription();
		if ($package->isLanguageType()) {
			$type = 'language';
		} else {
			$type = 'module';
		}
		$moduleDetails = null;
		$moduleName = $package->getModuleName();
		if ($moduleName != null) {
			$moduleDetails = array();
			$moduleDetails['description'] = (empty($desc) ? (isset($optionalModuleStrings[$moduleName.'_description']) ? $optionalModuleStrings[$moduleName.'_description'] : '') : $desc);

			if (Vtiger_Version::check($moduleForVtigerVersion, '>=') && Vtiger_Version::check($moduleMaxVtigerVersion, '<')) {
				$moduleDetails['selected'] = true;
				$moduleDetails['enabled'] = true;
			} else {
				$moduleDetails['selected'] = false;
				$moduleDetails['enabled'] = false;
			}

			$migrationAction = 'install';
			if (!$package->isLanguageType()) {
				$moduleInstance = null;
				if (Vtiger_Utils::checkTable('vtiger_tab')) {
					$moduleInstance = Vtiger_Module::getInstance($moduleName);
				}
				if ($moduleInstance) {
					$migrationAction = 'update';
					if (version_compare($moduleUpdateVersion, $moduleInstance->version, '>=')) {
						$moduleDetails['enabled'] = false;
					}
				}
			} else {
				if (Vtiger_Utils::CheckTable(Vtiger_Language::TABLENAME)) {
					$languageList = array_keys(Vtiger_Language::getAll());
					$prefix = $package->getPrefix();
					if (in_array($prefix, $languageList)) {
						$migrationAction = 'update';
					}
				}
			}
			$optionalModulesInfo[$migrationAction][$type][$moduleName] = $moduleDetails;
		}
		return $optionalModulesInfo;
	}

	// Function to install/update mandatory modules
	public static function installMandatoryModules() {
		require_once 'vtlib/Vtiger/Package.php';
		require_once 'vtlib/Vtiger/Module.php';
		require_once 'include/utils/utils.php';

		if ($handle = opendir(cbPackageDirectory.'mandatory')) {
			while (false !== ($file = readdir($handle))) {
				$packageNameParts = explode(".", $file);
				if ($packageNameParts[count($packageNameParts)-1] != 'zip') {
					continue;
				}
				array_pop($packageNameParts);
				$packageName = implode("", $packageNameParts);
				if (!empty($packageName)) {
					$packagepath = cbPackageDirectory."mandatory/$file";
					$package = new Vtiger_Package();
					$module = $package->getModuleNameFromZip($packagepath);
					if ($module != null) {
						$moduleInstance = Vtiger_Module::getInstance($module);
						if ($moduleInstance) {
							self::updateVtlibModule($module, $packagepath);
						} else {
							self::installVtlibModule($module, $packagepath);
						}
					}
				}
			}
			closedir($handle);
		}
	}

	public static function getMandatoryModuleList() {
		require_once 'vtlib/Vtiger/Package.php';
		require_once 'vtlib/Vtiger/Module.php';
		require_once 'include/utils/utils.php';

		$moduleList = array();
		if ($handle = opendir(cbPackageDirectory.'mandatory')) {
			while (false !== ($file = readdir($handle))) {
				$packageNameParts = explode(".", $file);
				if ($packageNameParts[count($packageNameParts)-1] != 'zip') {
					continue;
				}
				array_pop($packageNameParts);
				$packageName = implode("", $packageNameParts);
				if (!empty($packageName)) {
					$packagepath = cbPackageDirectory."mandatory/$file";
					$package = new Vtiger_Package();
					$moduleList[] = $package->getModuleNameFromZip($packagepath);
				}
			}
			closedir($handle);
		}
		return $moduleList;
	}

	public static function installSelectedOptionalModules($selected_modules, $source_directory = '', $destination_directory = '') {
		require_once 'vtlib/Vtiger/Package.php';
		require_once 'vtlib/Vtiger/Module.php';
		require_once 'include/utils/utils.php';

		$selected_modules = explode(":", $selected_modules);

		$languagePacks = array();
		if ($handle = opendir(cbPackageDirectory.'optional')) {
			while (false !== ($file = readdir($handle))) {
				$filename_arr = explode(".", $file);
				if ($filename_arr[count($filename_arr)-1] != 'zip') {
					continue;
				}
				$packagename = $filename_arr[0];
				$packagepath = cbPackageDirectory."optional/$file";
				$package = new Vtiger_Package();
				$module = $package->getModuleNameFromZip($packagepath);

				if (!empty($packagename) && in_array($module, $selected_modules)) {
					if ($package->isLanguageType($packagepath)) {
						$languagePacks[$module] = $packagepath;
						continue;
					}

					if ($module != null) {
						if ($package->isModuleBundle()) {
							$unzip = new Vtiger_Unzip($packagepath);
							$unzip->unzipAllEx($package->getTemporaryFilePath());
							$moduleInfoList = $package->getAvailableModuleInfoFromModuleBundle();
							foreach ($moduleInfoList as $moduleInfo) {
								$moduleInfo = (Array)$moduleInfo;
								$packagepath = $package->getTemporaryFilePath($moduleInfo['filepath']);
								$subModule = new Vtiger_Package();
								$subModuleName = $subModule->getModuleNameFromZip($packagepath);
								$moduleInstance = Vtiger_Module::getInstance($subModuleName);
								if ($moduleInstance) {
									self::updateVtlibModule($subModuleName, $packagepath);
								} else {
									self::installVtlibModule($subModuleName, $packagepath);
								}
							}
						} else {
							$moduleInstance = Vtiger_Module::getInstance($module);
							if ($moduleInstance) {
								self::updateVtlibModule($module, $packagepath);
							} else {
								self::installVtlibModule($module, $packagepath);
							}
						}
					}
				}
			}
			closedir($handle);
		}

		foreach ($languagePacks as $module => $packagepath) {
			self::installVtlibModule($module, $packagepath);
			continue;
		}
	}

	/* Function to install Vtlib Compliant modules
	 * @param - $packagename - Name of the module
	 * @param - $packagepath - Complete path to the zip file of the Module
	 */
	public static function installVtlibModule($packagename, $packagepath) {
		global $log;
		require_once 'vtlib/Vtiger/Package.php';
		require_once 'vtlib/Vtiger/Module.php';
		$Vtiger_Utils_Log = true;
		$package = new Vtiger_Package();

		if ($package->isLanguageType($packagepath)) {
			$package = new Vtiger_Language();
			$package->import($packagepath, true);
			return;
		}
		$module = $package->getModuleNameFromZip($packagepath);

		if ($package->isLanguageType()) {
			require_once 'vtlib/Vtiger/Language.php';
			$languagePack = new Vtiger_Language();
			@$languagePack->import($packagepath, true);
			return;
		}

		$module_exists = false;
		if ($module == null) {
			$log->fatal("$packagename Module zipfile is not valid!");
		} elseif (Vtiger_Module::getInstance($module)) {
			$log->fatal("$module already exists!");
			$module_exists = true;
		}
		if ($module_exists == false) {
			$log->debug("$module - Installation starts here");
			$package->import($packagepath, true);
			$moduleInstance = Vtiger_Module::getInstance($module);
			if (empty($moduleInstance)) {
				$log->fatal("$module module installation failed!");
			}
		}
	}

	/* Function to update Vtlib Compliant modules
	 * @param - $module - Name of the module
	 * @param - $packagepath - Complete path to the zip file of the Module
	 */
	public static function updateVtlibModule($module, $packagepath) {
		global $log;
		require_once 'vtlib/Vtiger/Package.php';
		require_once 'vtlib/Vtiger/Module.php';
		$Vtiger_Utils_Log = true;
		$package = new Vtiger_Package();

		if ($package->isLanguageType($packagepath)) {
			require_once 'vtlib/Vtiger/Language.php';
			$languagePack = new Vtiger_Language();
			$languagePack->update(null, $packagepath, true);
			return;
		}

		if ($module == null) {
			$log->fatal('Module name is invalid');
		} else {
			$moduleInstance = Vtiger_Module::getInstance($module);
			if ($moduleInstance || $package->isModuleBundle($packagepath)) {
				$log->debug("$module - Module instance found - Update starts here");
				$package->update($moduleInstance, $packagepath);
			} else {
				$log->fatal("$module doesn't exists!");
			}
		}
	}

	//Function to to rename the installation file and folder so that no one destroys the setup
	public static function renameInstallationFiles() {
		$renamefile = uniqid(rand(), true);

		$ins_file_renamed = true;
		if (!@rename("install.php", $renamefile."install.php.txt")) {
			if (@copy("install.php", $renamefile."install.php.txt")) {
				if (!@unlink("install.php")) {
					$ins_file_renamed = false;
				}
			} else {
				$ins_file_renamed = false;
			}
		}

		$ins_dir_renamed = true;
		if (!@rename("install/", $renamefile."install/")) {
			if (@copy("install/", $renamefile."install/")) {
				if (!@unlink("install/")) {
					$ins_dir_renamed = false;
				}
			} else {
				$ins_dir_renamed = false;
			}
		}

		$ins_dir_renamed = true;
		if (!@rename("modules/Migration/", $renamefile."Migration/")) {
			if (@copy("modules/Migration/", $renamefile."Migration/")) {
				if (!@unlink("modules/Migration/")) {
					$ins_dir_renamed = false;
				}
			} else {
				$ins_dir_renamed = false;
			}
		}

		$result = array();
		$result['renamefile'] = $renamefile;
		$result['install_file_renamed'] = $ins_file_renamed;
		$result['install_directory_renamed'] = $ins_dir_renamed;

		return $result;
	}

	public static function getMySQLVersion($serverInfo) {
		if (!is_array($serverInfo)) {
			$version = explode('-', $serverInfo);
			$mysql_server_version=$version[0];
		} else {
			$mysql_server_version = $serverInfo['version'];
		}
		return $mysql_server_version;
	}
}

//Function used to execute the query and display the success/failure of the query
function ExecuteQuery($query) {
	global $adb, $installationStrings, $conn;
	global $migrationlog;

	//For third option migration we have to use the $conn object because the queries should be executed in 4.2.3 db
	$status = $adb->query($query);
	if (is_object($status)) {
		echo '
			<tr width="100%">
				<td width="10%"><font color="green"> '.$installationStrings['LBL_SUCCESS'].' </font></td>
				<td width="80%">'.$query.'</td>
			</tr>';
		$migrationlog->debug("Query Success ==> $query");
	} else {
		echo '
			<tr width="100%">
					<td width="5%"><font color="red"> '.$installationStrings['LBL_FAILURE'].' </font></td>
				<td width="70%">'.$query.'</td>
			</tr>';
		$migrationlog->debug("Query Failed ==> $query \n Error is ==> [".$adb->database->ErrorNo()."]".$adb->database->ErrorMsg());
	}
}

//Function used to execute the query and display the success/failure of the query
function ExecutePQuery($query, $params) {
	global $adb, $installationStrings, $conn;
	global $migrationlog;

	//For third option migration we have to use the $conn object because the queries should be executed in 4.2.3 db
	$status = $adb->pquery($query, $params);
	$query = $adb->convert2sql($query, $params);
	if (is_object($status)) {
		echo '
			<tr width="100%">
				<td width="10%"><font color="green"> '.$installationStrings['LBL_SUCCESS'].' </font></td>
				<td width="80%">'.$query.'</td>
			</tr>';
		$migrationlog->debug("Query Success ==> $query");
	} else {
		echo '
			<tr width="100%">
					<td width="5%"><font color="red"> '.$installationStrings['LBL_FAILURE'].' </font></td>
				<td width="70%">'.$query.'</td>
			</tr>';
		$migrationlog->debug("Query Failed ==> $query \n Error is ==> [".$adb->database->ErrorNo()."]".$adb->database->ErrorMsg());
	}
}
?>
