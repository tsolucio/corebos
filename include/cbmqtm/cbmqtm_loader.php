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
 *  Module       : coreBOS Message Queue Loader
 *  Author       : JPL TSolucio, S. L.
 *************************************************************************************************/
/*
 * CREATE TABLE `cbmqtm_config` ( `cbmqtm_key` VARCHAR(200) NOT NULL , `cbmqtm_value` VARCHAR(500) NOT NULL , PRIMARY KEY (`cbmqtm_key`)) ENGINE = InnoDB;
 */
class coreBOS_MQTM {
	static protected $instance = null;

	static public function getInstance() {

		if (null === static::$instance) {
			$adb = PearDatabase::getInstance();
			$cbmqtmrs = $adb->pquery('select cbmqtm_value from cbmqtm_config where cbmqtm_key=?',array('cbmqtm_classfile'));
			if ($cbmqtmrs and $adb->num_rows($cbmqtmrs)==1) {
				$filename = $adb->query_result($cbmqtmrs, 0, 0);
				if (file_exists($filename)) {
					include_once $filename;
					$cbmqtmrs = $adb->pquery('select cbmqtm_value from cbmqtm_config where cbmqtm_key=?',array('cbmqtm_classname'));
					if ($cbmqtmrs and $adb->num_rows($cbmqtmrs)==1) {
						$cbmqtm_classname = $adb->query_result($cbmqtmrs, 0, 0);
						if (class_exists($cbmqtm_classname)) {
							static::$instance = $cbmqtm_classname::getInstance();
						}
					}
				}
			}
		}

		return static::$instance;
	}

	protected function __construct() {}

	protected function __clone() {}

}