<?php
/*************************************************************************************************
 * Copyright 2020 JPL TSolucio, S.L. -- This file is a part of TSOLUCIO coreBOS Customizations.
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
 * ************************************************************************************************
 *  Author       : JPL TSolucio, S. L.
 *************************************************************************************************/

require_once 'include/database/PearDatabase.php';
require 'vendor/autoload.php';
use Laminas\Cache\Psr\SimpleCache\SimpleCacheDecorator;
use Laminas\Cache\StorageFactory;

class cbCache extends SimpleCacheDecorator {

	private $modifiedTimePostfix = '_modified_time';

	public function __construct($adapterName, $adapterOptions = [], $plugins = null) {
		$configurations = [
			'adapter' => [
				'name'    => $adapterName,
				'options' => $adapterOptions,
			],
		];
		if ($plugins) {
			$configurations['plugins'] = $plugins;
		}

		$storage = StorageFactory::factory($configurations);
		parent::__construct($storage);
	}

	public function getModifiedTimePostfix() {
		return $this->modifiedTimePostfix;
	}

	public function hasWithModuleCheck($key, $module, $id = 0) {
		global $adb;
		$modifiedTimeKey = $key . $this->modifiedTimePostfix;
		if (!$this->has($key) && !$this->has($modifiedTimeKey)) {
			return false;
		}
		if (!empty($id)) {
			$entities = $adb->pquery("select date_format(modifiedtime,'%Y%m%d%H%i%s') from vtiger_crmentity where crmid=? and deleted=0", array($id));
			$itemsExpired = $this->removeExpiredCacheItems($key, $entities);
			if ($itemsExpired) {
				return false;
			}
		} else {
			$entities = $adb->pquery(
				"select date_format(modifiedtime,'%Y%m%d%H%i%s') from vtiger_crmentity where setype=? and deleted=0 order by modifiedtime desc limit 1",
				array($module)
			);
			$itemsExpired = $this->removeExpiredCacheItems($key, $entities);
			if ($itemsExpired) {
				return false;
			}
		}
		return true;
	}

	public function hasWithQueryCheck($key, $query, $queryParams = []) {
		global $adb;
		$modifiedTimeKey = $key . $this->modifiedTimePostfix;
		if (!$this->has($key) && !$this->has($modifiedTimeKey)) {
			return false;
		}
		$result = $adb->pquery($query, $queryParams);
		$itemsExpired = $this->removeExpiredCacheItems($key, $result);
		if ($itemsExpired) {
			return false;
		}
		return true;
	}

	private function removeExpiredCacheItems($key, $dbEntities) {
		global $adb;

		$modifiedTimeKey = $key . $this->modifiedTimePostfix;
		$cacheItemModifiedTime = $this->get($modifiedTimeKey);

		if ($adb->num_rows($dbEntities) == 0) {
			$this->deleteMultiple([$key, $modifiedTimeKey]);
			return true;
		}
		$lastModifiedTime = $adb->query_result($dbEntities, 0, 0);
		$lastModifiedTimestamp = strtotime($lastModifiedTime);
		$cacheModifiedTimestamp = strtotime($cacheItemModifiedTime);
		if ($lastModifiedTimestamp > $cacheModifiedTimestamp) {
			$this->deleteMultiple([$key, $modifiedTimeKey]);
			return true;
		}

		return false;
	}
}