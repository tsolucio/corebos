<?php
/*************************************************************************************************
 * Copyright 2016 JPL TSolucio, S.L. -- This file is a part of TSOLUCIO coreBOS Customizations.
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
/**
 * Session class
 */
class coreBOS_Session {

	/**
	 * Constructor
	 * Avoid creation of instances.
	 */
	private function __construct() {
	}

	/**
	 * Destroy session
	 */
	static function destroy() {
		session_regenerate_id(true);
		session_unset();
		session_destroy();
	}

	/**
	 * Initialize session
	 */
	static function init($setKCFinder=false) {
		session_name(self::getSessionName());
		session_start();
		if ($setKCFinder) self::setKCFinderVariables();
	}

	/**
	 * create session name from given URL or $site_URL
	 */
	static function getSessionName($URL='') {
		global $site_URL;
		if (empty($site_URL)) {
			if (file_exists('config.inc.php')) {
				include 'config.inc.php';
			}
			if (file_exists('../config.inc.php')) {
				include '../config.inc.php';
				@include '../config-dev.inc.php';
			}
		}
		if (empty($URL)) $URL = $site_URL;
		$purl = parse_url($URL);
		$sn = preg_replace('/[^A-Za-z0-9]/', '', $purl['host'].$purl['path'].(isset($purl['port'])?$purl['port']:''));
		if (is_numeric($sn)) $sn = 'cb'.$sn;
		return $sn;
	}

	/**
	 * set KCFinder session variables
	 */
	static function setKCFinderVariables() {
		global $upload_badext, $site_URL, $root_directory;
		$_SESSION['KCFINDER'] = array();
		$_SESSION['KCFINDER']['disabled'] = false;
		$_SESSION['KCFINDER']['uploadURL'] = $site_URL.'/storage/kcimages';
		$_SESSION['KCFINDER']['uploadDir'] = $root_directory.'storage/kcimages';
		$deniedExts = implode(' ', $upload_badext);
		$_SESSION['KCFINDER']['deniedExts'] = $deniedExts;

		list($http,$urldomain) = explode('://', $site_URL);
		if(strpos($urldomain,':') !== false){
			list($domain,$port) = explode(':', $urldomain);
			$_SESSION['KCFINDER']['cookieDomain'] = $domain;
		}
	}

	/**
	 * Is key defined in session?
	 */
	static function has($key) {
		return isset($_SESSION[$key]);
	}

	/**
	 * Get value for the key.
	 */
	static function get($key, $defvalue = '') {
		return (isset($_SESSION[$key]) ? $_SESSION[$key] : $defvalue);
	}

	/**
	 * Set value for the key.
	 */
	static function set($key, $value) {
		$_SESSION[$key] = $value;
	}

	/**
	 * Delete value for the key.
	 */
	static function delete($key) {
		unset($_SESSION[$key]);
	}

}