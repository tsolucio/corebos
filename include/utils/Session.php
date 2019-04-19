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

	private static $session_name = '';

	/**
	 * Constructor
	 * Avoid creation of instances.
	 */
	private function __construct() {
	}

	/**
	 * Destroy session
	 */
	public static function destroy() {
		self::init();
		session_regenerate_id(true);
		session_unset();
		session_destroy();
	}

	public static function isSessionStarted() {
		$sid = session_id();
		return function_exists('session_status') ? (PHP_SESSION_ACTIVE == session_status()) : (!empty($sid));
	}

	/**
	 * Initialize session
	 */
	public static function init($setKCFinder = false, $saveTabValues = false) {
		if (!isset($_SESSION)) {
			session_name(self::getSessionName());
		}
		@session_start();
		if ($setKCFinder) {
			self::setKCFinderVariables();
		}
		if ($saveTabValues) {
			self::copyTabVariables();
		}
	}

	/**
	 * Copy Browser Tab variables to session
	 */
	public static function copyTabVariables() {
		if (!empty($_COOKIE['corebos_browsertabID'])) {
			$corebos_browsertabID = vtlib_purify($_COOKIE['corebos_browsertabID']);
			$newvars = array();
			foreach ($_SESSION as $key => $value) {
				if (strpos($key, $corebos_browsertabID) !== false && strpos($key, $corebos_browsertabID.'__prev') === false) {
					$newvars[$key.'__prev'] = $value;
				}
			}
			foreach ($newvars as $key => $value) {
				$_SESSION[$key] = $value;
			}
		}
	}

	/**
	 * create session name from given URL or $site_URL
	 */
	public static function getSessionName($URL = '', $force = false) {
		global $site_URL;
		if (self::$session_name!='' && !$force) {
			return self::$session_name;
		}
		if (empty($site_URL)) {
			if (file_exists('config.inc.php')) {
				include 'config.inc.php';
			}
			if (file_exists('../config.inc.php')) {
				include '../config.inc.php';
				@include '../config-dev.inc.php';
			}
		}
		if (empty($URL)) {
			$URL = $site_URL;
		}
		if (empty($URL)) {
			$URL = $_SERVER['REQUEST_SCHEME'].'://'.$_SERVER['HTTP_HOST'].$_SERVER['SCRIPT_NAME'];
		}
		$purl = parse_url($URL);
		$sn = preg_replace('/[^A-Za-z0-9]/', '', (isset($purl['host'])?$purl['host']:'').(isset($purl['path'])?$purl['path']:'').(isset($purl['port'])?$purl['port']:''));
		if (is_numeric($sn)) {
			$sn = 'cb'.$sn;
		}
		self::$session_name = $sn;
		return $sn;
	}

	/**
	* set session name
	*/
	public static function setSessionName($session_name) {
		self::$session_name = $session_name;
	}
	/**
	 * set KCFinder session variables
	 */
	public static function setKCFinderVariables() {
		global $upload_badext, $site_URL, $root_directory;
		if (empty($site_URL)) {
			return false;
		}
		self::init();
		$_SESSION['KCFINDER'] = array();
		$_SESSION['KCFINDER']['disabled'] = false;
		$_SESSION['KCFINDER']['uploadURL'] = $site_URL.'/storage/kcimages';
		$_SESSION['KCFINDER']['uploadDir'] = $root_directory.'storage/kcimages';
		$deniedExts = implode(' ', $upload_badext);
		$_SESSION['KCFINDER']['deniedExts'] = $deniedExts;

		list($http,$urldomain) = explode('://', $site_URL);
		if (strpos($urldomain, ':') !== false) {
			list($domain,$port) = explode(':', $urldomain);
			$_SESSION['KCFINDER']['cookieDomain'] = $domain;
		}
		session_write_close();
	}

	/**
	 * set global User session variables
	 */
	public static function setUserGlobalSessionVariables() {
		if (empty($_SESSION['__UnifiedSearch_SelectedModules__'])) {
			$appSearchModules = GlobalVariable::getVariable('Application_Global_Search_SelectedModules', '');
			if (!empty($appSearchModules)) {
				$selected_modules = explode(',', $appSearchModules);
				self::init();
				$_SESSION['__UnifiedSearch_SelectedModules__'] = $selected_modules;
				session_write_close();
			}
		}
	}

	/**
	 * Is key defined in session?
	 * Array elements can be specified by separating them with a caret ^
	 */
	public static function has($key, $sespos = null) {
		$keyparts = explode('^', $key);
		if (count($keyparts)==1) {
			if (is_null($sespos)) {
				return isset($_SESSION[$key]);
			} else {
				return isset($sespos[$keyparts[0]]);
			}
		} else {
			if (is_null($sespos)) {
				if (!isset($_SESSION[$keyparts[0]]) || !is_array($_SESSION[$keyparts[0]])) {
					return false;
				}
				$sespos = $_SESSION[$keyparts[0]];
			} else {
				if (!isset($sespos[$keyparts[0]]) || !is_array($sespos[$keyparts[0]])) {
					return false;
				}
				$sespos = $sespos[$keyparts[0]];
			}
			$key = substr($key, strpos($key, '^')+1);
			return self::has($key, $sespos);
		}
	}

	/**
	 * Get value for the key.
	 * Array elements can be specified by separating them with a caret ^
	 */
	public static function get($key, $defvalue = '') {
		$keyparts = explode('^', $key);
		if (count($keyparts)==1) {
			return (isset($_SESSION[$key]) ? $_SESSION[$key] : $defvalue);
		}
		if (!isset($_SESSION[$keyparts[0]])) {
			return $defvalue;
		}
		$sespos = $_SESSION[$keyparts[0]];
		for ($p=1, $pMax = count($keyparts); $p< $pMax; $p++) {
			if (!isset($sespos[$keyparts[$p]])) {
				return $defvalue;
			}
			$sespos = $sespos[$keyparts[$p]];
		}
		return $sespos;
	}

	/**
	 * Set value for the key.
	 * Array elements can be specified by separating them with a caret ^
	 */
	public static function set($key, $value, &$sespos = null) {
		$keyparts = explode('^', $key);
		self::init();
		if (count($keyparts)==1) {
			if (is_null($sespos)) {
				$_SESSION[$key] = $value;
			} else {
				if (!is_array($sespos)) {
					$sespos = array();
				}
				$sespos[$key] = $value;
			}
		} else {
			$key = substr($key, strpos($key, '^')+1);
			if (is_null($sespos)) {
				if (!isset($_SESSION[$keyparts[0]]) || !is_array($_SESSION[$keyparts[0]])) {
					$_SESSION[$keyparts[0]] = array();
				}
				self::set($key, $value, $_SESSION[$keyparts[0]]);
			} else {
				if (!isset($sespos[$keyparts[0]]) || !is_array($sespos[$keyparts[0]])) {
					$sespos[$keyparts[0]] = array();
				}
				self::set($key, $value, $sespos[$keyparts[0]]);
			}
		}
		session_write_close();
	}

	/**
	 * Merge the values of an array on to the SESSION array
	 * @param Array of key=>value to add to the SESSION
	 * @param boolean, if true array values have precedence, else the existing SESSION values have precedence
	 */
	public static function merge($values, $overwrite_session = false) {
		self::init();
		if ($overwrite_session) {
			$_SESSION = array_merge($_SESSION, $values);
		} else {
			$_SESSION = array_merge($values, $_SESSION);
		}
		session_write_close();
	}

	/**
	 * Delete value for the key.
	 * Array elements can be specified by separating them with a caret ^
	 */
	public static function delete($key, &$sespos = null) {
		$keyparts = explode('^', $key);
		self::init();
		if (count($keyparts)==1) {
			if (is_null($sespos)) {
				if (isset($_SESSION[$key])) {
					unset($_SESSION[$key]);
				}
			} else {
				if (isset($sespos[$key])) {
					unset($sespos[$key]);
				}
			}
		} else {
			$key = substr($key, strpos($key, '^')+1);
			if (is_null($sespos)) {
				if (!isset($_SESSION[$keyparts[0]]) || !is_array($_SESSION[$keyparts[0]])) {
					return false; // this should be an exception
				}
				self::delete($key, $_SESSION[$keyparts[0]]);
			} else {
				self::delete($key, $sespos[$keyparts[0]]);
			}
		}
		session_write_close();
	}

	/**
	 * Delete all top level values whose key starts with the given string
	 */
	public static function deleteStartsWith($startswith) {
		self::init();
		if (version_compare(phpversion(), '5.6.0') >= 0) {
			$_SESSION = array_filter($_SESSION, function ($key) use ($startswith) {
				return strpos($key, $startswith)!==0;
			}, ARRAY_FILTER_USE_KEY);
		} else {
			$matchedKeys = array_filter(array_keys($_SESSION), function ($key) use ($startswith) {
				return strpos($key, $startswith)!==0;
			});
			$_SESSION = array_intersect_key($_SESSION, array_flip($matchedKeys));
		}
		session_write_close();
	}
}
