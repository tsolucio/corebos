<?php
/* +***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 * *********************************************************************************** */

class Install_InitSchema {

	protected $sql_directory = 'schema/';
	protected $db = false;

	public function __construct($db = '') {
		$this->db = $db;
	}

	/**
	 * Function starts applying schema changes
	 */
	public function initialize() {
		include 'modules/Settings/configod.php';
		$this->initializeDatabase($this->sql_directory, array($corebosInstallDatabase));
		$this->setDefaultUsersAccess();
		$currencyName = $_SESSION['installation_info']['currency_name'];
		$currencyCode = $_SESSION['installation_info']['currency_code'];
		$currencySymbol = $_SESSION['installation_info']['currency_symbol'];
		$this->db->pquery(
			'UPDATE vtiger_currency_info SET currency_name = ?, currency_code = ?, currency_symbol = ?',
			array($currencyName, $currencyCode, $currencySymbol)
		);
		// recalculate all sharing rules for users
		require_once 'include/utils/UserInfoUtil.php';
		RecalculateSharingRules();
	}

	public function initializeDatabase($location, $filesName = array()) {
		$this->db->query('SET FOREIGN_KEY_CHECKS = 0;');
		if (!$filesName) {
			echo 'No files';
			return false;
		}
		$splitQueries = '';
		foreach ($filesName as $name) {
			$sql_file = $location . $name . '.sql';
			$return = true;
			if (!($fileBuffer = file_get_contents($sql_file))) {
				echo 'Invalid file: ' . $sql_file;
				return false;
			}
			$splitQueries .= $fileBuffer;
		}
		$create_query = substr_count($splitQueries, 'CREATE TABLE');
		$insert_query = substr_count($splitQueries, 'INSERT INTO');
		$alter_query = substr_count($splitQueries, 'ALTER TABLE');
		$executed_query = 0;
		$queries = $this->splitQueries($splitQueries);
		foreach ($queries as $query) {
			// Trim any whitespace.
			$query = trim($query);
			if (!empty($query) && ($query{0} != '#') && ($query{0} != '-')) {
				try {
					$this->db->query($query);
					$executed_query++;
				} catch (RuntimeException $e) {
					echo $e->getMessage();
					$return = false;
				}
			}
		}
		$this->db->query('SET FOREIGN_KEY_CHECKS = 1;');
		return array('status' => $return, 'create' => $create_query, 'insert' => $insert_query, 'alter' => $alter_query, 'executed' => $executed_query);
	}

	/**
	 * Function creates default user's Role, Profiles
	 */
	public function setDefaultUsersAccess() {
		$adminPassword = $_SESSION['installation_info']['admin_password'];
		$this->db->pquery('update vtiger_users set email1=? where id=1', array($_SESSION['installation_info']['admin_email']));
		$newUser = new Users();
		$newUser->retrieve_entity_info(1, 'Users');
		$newUser->change_password('admin', $adminPassword, false);
		$this->db->pquery('UPDATE `vtiger_users` SET `change_password` = ? where id=1', array(($adminPassword=='admin' ? 1 : 0)));
		require_once('modules/Users/CreateUserPrivilegeFile.php');
		createUserPrivilegesfile(1);
	}

	private function splitQueries($query) {
		$buffer = array();
		$queries = array();
		$in_string = false;

		// Trim any whitespace.
		$query = trim($query);
		// Remove comment lines.
		$query = preg_replace("/\n\#[^\n]*/", '', "\n" . $query);
		// Remove PostgreSQL comment lines.
		$query = preg_replace("/\n\--[^\n]*/", '', "\n" . $query);
		// Find function
		$funct = explode('CREATE OR REPLACE FUNCTION', $query);
		// Save sql before function and parse it
		$query = $funct[0];

		// Parse the schema file to break up queries.
		for ($i = 0; $i < strlen($query) - 1; $i++) {
			if ($query[$i] == ";" && !$in_string) {
				$queries[] = substr($query, 0, $i);
				$query = substr($query, $i + 1);
				$i = 0;
			}
			if ($in_string && ($query[$i] == $in_string) && $buffer[1] != "\\") {
				$in_string = false;
			} elseif (!$in_string && ($query[$i] == '"' || $query[$i] == "'") && (!isset($buffer[0]) || $buffer[0] != "\\")) {
				$in_string = $query[$i];
			}
			if (isset($buffer[1])) {
				$buffer[0] = $buffer[1];
			}
			$buffer[1] = $query[$i];
		}
		// If the is anything left over, add it to the queries.
		if (!empty($query)) {
			$queries[] = $query;
		}
		// Add function part as is
		for ($f = 1; $f < count($funct); $f++) {
			$queries[] = 'CREATE OR REPLACE FUNCTION ' . $funct[$f];
		}
		return $queries;
	}
}