<?php
/*************************************************************************************************
 * Copyright 2012 JPL TSolucio, S.L. -- This file is a part of TSOLUCIO coreBOS Customizations.
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

class UserPrivilegesWriter {

	const WRITE_TO = 'file'; // file | db

	public static function setUserPrivileges($userId) {
		if (self::WRITE_TO == 'file') {
			self::createUserPrivilegesFile($userId);
		} else if(self::WRITE_TO == 'db'){
			self::createUserPrivileges($userId);
		}
	}

	private static function setSharingPrivileges($userId) {
		if (self::WRITE_TO == 'file') {
			self::createSharingPrivilegesFile($userId);
		} else if(self::WRITE_TO == 'db'){
			self::createSharingPrivileges($userId);
		}
	}

	/**
	 * Method to write user privileges in a file
	 *
	 * @param  int $userid
	 */
	private function createUserPrivilegesFile($userId) {
		require_once 'modules/Users/CreateUserPrivilegeFile.php';
		createUserPrivilegesfile($userId);
	}

	/**
	 * Method to write sharing privileges in a file
	 *
	 * @param  int $userid
	 */
	private function createSharingPrivilegesFile($userId) {
		require_once 'modules/Users/CreateUserPrivilegeFile.php';
 		createUserSharingPrivilegesfile($userId)
	}

	/**
	 * Method to write user privileges in database
	 *
	 * @param  int $userId [description]
	 */
	private function createUserPrivileges($userId) {

	}

	/**
	 * Method to write sharing privileges in database
	 *
	 * @param  int $userId [description]
	 */
	private function createUserPrivileges($userId) {

	}
}
