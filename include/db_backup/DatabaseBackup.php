<?php
/*+*******************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ********************************************************************************/
include_once 'include/adodb/adodb.inc.php';

class DatabaseConfig {
	private $hostName = null;
	private $username = null;
	private $password = null;
	private $dbName = null;
	private $rootUsername = null;
	private $rootPassword = null;
	private $dbType = null;

	public function __construct($dbserver, $username, $password, $dbName, $dbType = 'mysql', $rootusername = '', $rootpassword = '') {
		$this->hostName = $dbserver;
		$this->username = $username;
		$this->password = $password;
		$this->dbName = $dbName;
		$this->rootUsername = $rootusername;
		$this->rootPassword = $rootpassword;
		$this->dbType = $dbType;
	}

	/**
	 *
	 * @return DatabaseConfig
	 */
	public static function getInstanceFromConfigFile() {
		require 'config.inc.php';
		$config = new DatabaseConfig(
			$dbconfig['db_hostname'],
			$dbconfig['db_username'],
			$dbconfig['db_password'],
			$dbconfig['db_name'],
			$dbconfig['db_type'],
			$dbconfig['db_username'],
			$dbconfig['db_password']
		);
		return $config;
	}

	/**
	 *
	 * @param DatabaseConfig $config
	 * @return DatabaseConfig
	 */
	public static function getInstanceFromOtherConfig($config) {
		$newConfig = new DatabaseConfig(
			$config->getHostName(),
			$config->getUsername(),
			$config->getPassword(),
			$config->getDatabaseName(),
			$config->getDBType(),
			$config->getRootUsername(),
			$config->getRootUsername()
		);
		return $newConfig;
	}

	public function getHostName() {
		return $this->hostName;
	}

	public function getUsername() {
		return $this->username;
	}

	public function getPassword() {
		return $this->password;
	}

	public function getRootUsername() {
		return $this->rootUsername;
	}

	public function getRootPassword() {
		return $this->rootPassword;
	}

	public function getDatabaseName() {
		return $this->dbName;
	}

	public function getDBType() {
		return $this->dbType;
	}

	public function setDatabaseName($dbName) {
		$this->dbName = $dbName;
	}

	public function setRootUsername($rootUsername) {
		$this->rootUsername = $rootUsername;
	}

	public function setRootPassword($rootPassword) {
		$this->rootPassword = $rootPassword;
	}
}

class DatabaseBackup {

	private $source = null;
	private $target = null;
	private $skipStages = null;
	public static $langString = array(
		'SourceConnectFailed'=>'Source database connect failed',
		'DestConnectFailed'=>'Destination database connect failed',
		'TableListFetchError'=>'Failed to get Table List for database',
		'SqlExecutionError'=>'Execution of following query failed',
	);

	public function __construct($source, $target, $skipStages = array()) {
		$this->skipStages = $skipStages;
		$this->source = $source;
		$this->target = $target;
	}

	public function setSource($source) {
		$this->source = $source;
	}

	public function setTarget($target) {
		$this->target = $target;
	}

	public function backup() {
		while ($this->source->valid()) {
			$info = $this->source->next();
			if (!in_array($info['stage'], $this->skipStages)) {
				$this->target->addStageData($info['stage'], $info['data']);
			}
		}
	}
}
?>