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
 *************************************************************************************************
 *  Author       : JPL TSolucio, S. L.
 *************************************************************************************************/

class coreBOS_Settings {

	private static $cached_values = array();

	/*
	 * @return $default if not found
	 */
	public static function getSetting($skey, $default) {
		global $adb;
		if (isset(self::$cached_values[$skey])) {
			return self::$cached_values[$skey];
		} else {
			$cbstrs = $adb->pquery('select setting_value from cb_settings where setting_key=?', array($skey));
			if ($cbstrs && $adb->num_rows($cbstrs)==1) {
				$value = $adb->query_result($cbstrs, 0, 0);
				self::$cached_values[$skey] = $value;
			} else {
				$value = $default;
			}
			return $value;
		}
	}

	public static function setSetting($skey, $svalue) {
		global $adb;
		$adb->pquery('INSERT INTO cb_settings (setting_key,setting_value) VALUES (?,?) ON DUPLICATE KEY UPDATE setting_value=?', array($skey, $svalue, $svalue));
		self::$cached_values[$skey] = $svalue;
	}

	public static function delSetting($skey) {
		global $adb;
		$adb->pquery('DELETE FROM cb_settings WHERE setting_key=?', array($skey));
		unset(self::$cached_values[$skey]);
	}

	public static function delSettingStartsWith($startswith) {
		global $adb;
		$adb->pquery('DELETE FROM cb_settings WHERE setting_key LIKE ?', array($startswith.'%'));
		if (version_compare(phpversion(), '5.6.0') >= 0) {
			self::$cached_values = array_filter(self::$cached_values, function ($key) use ($startswith) {
				return strpos($key, $startswith)!==0;
			}, ARRAY_FILTER_USE_KEY);
		} else {
			$matchedKeys = array_filter(array_keys(self::$cached_values), function ($key) use ($startswith) {
				return strpos($key, $startswith)!==0;
			});
			self::$cached_values = array_intersect_key(self::$cached_values, array_flip($matchedKeys));
		}
	}

	public static function settingExists($skey) {
		global $adb;
		$cbstrs = $adb->pquery('select 1 from cb_settings where setting_key=?', array($skey));
		return ($cbstrs && $adb->num_rows($cbstrs)==1);
	}
}

/*
	CREATE TABLE `cb_settings` (
	  `setting_key` varchar(200) NOT NULL,
	  `setting_value` varchar(1000) NOT NULL,
	  PRIMARY KEY (`setting_key`)
	) ENGINE=InnoDB DEFAULT CHARSET=utf8;
 */
