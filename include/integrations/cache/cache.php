<?php
/*************************************************************************************************
 * Copyright 2020 JPL TSolucio, S.L. -- This file is a part of TSOLUCIO coreBOS customizations.
 * You can copy, adapt and distribute the work under the "Attribution-NonCommercial-ShareAlike"
 * Vizsage Public License (the "License"). You may not use this file except in compliance with the
 * License. Roughly speaking, non-commercial users may share and modify this code, but must give credit
 * and share improvements. However, for proper details please read the full License, available at
 * http://vizsage.com/license/Vizsage-License-BY-NC-SA.html and the handy reference for understanding
 * the full license at http://vizsage.com/license/Vizsage-Deed-BY-NC-SA.html. Unless required by
 * applicable law or agreed to in writing, any software distributed under the License is distributed
 * on an  "AS IS" BASIS, WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and limitations under the
 * License terms of Creative Commons Attribution-NonCommercial-ShareAlike 3.0 (the License).
 * ************************************************************************************************
 *  Module    : PSR-16 cache integration
 *  Version   : 1.0
 *  Author    : JPL TSolucio, S. L.
 *************************************************************************************************/
require 'vendor/autoload.php';

class corebos_cache {

	// Configuration Properties
	private $adapter;
	private $ip;
	private $port;

	// Configuration Keys
	const KEY_ISACTIVE = 'cache_isactive';
	const KEY_ADAPTER = 'cache_adapter';
	const KEY_IP = 'cache_ip';
	const KEY_PORT = 'cache_port';

	const ADAPTER_MEMORY = 'memory';
	const ADAPTER_REDIS = 'redis';
	const ADAPTER_MEMCACHED = 'memcached';

	// Utilities
	private static $cacheClient = null;

	public function __construct() {
		$this->initGlobalScope();
	}

	public function initGlobalScope() {
		if ($this->isActive()) {
			$this->adapter = coreBOS_Settings::getSetting(self::KEY_ADAPTER, '');
			$this->ip = coreBOS_Settings::getSetting(self::KEY_IP, '');
			$this->port = coreBOS_Settings::getSetting(self::KEY_PORT, '');
			if ($this->adapter == self::ADAPTER_MEMORY) {
				$this->setCacheClient();
			} elseif (!empty($this->ip) && !empty($this->port)) {
				if ($this->adapter == self::ADAPTER_REDIS) {
					$adapterOptions = ['server' => ['host' => $this->ip, 'port' => $this->port, 'timeout' => 100000]];
				} else { //defaults to memcached
					$adapterOptions = ['servers' => [[$this->ip, $this->port]]];
				}
				$plugins = ['serializer'];
				$this->setCacheClient($adapterOptions, $plugins);
			}
		}
	}

	public function saveSettings($isactive, $adapter = 'memory', $ip = null, $port = null) {
		coreBOS_Settings::setSetting(self::KEY_ISACTIVE, $isactive);
		coreBOS_Settings::setSetting(self::KEY_ADAPTER, $adapter);
		coreBOS_Settings::setSetting(self::KEY_IP, $ip);
		coreBOS_Settings::setSetting(self::KEY_PORT, $port);
	}

	public function getSettings() {
		return array(
			'isActive' => coreBOS_Settings::getSetting(self::KEY_ISACTIVE, ''),
			'adapter' => coreBOS_Settings::getSetting(self::KEY_ADAPTER, ''),
			'ip' => coreBOS_Settings::getSetting(self::KEY_IP, ''),
			'port' => coreBOS_Settings::getSetting(self::KEY_PORT, ''),
		);
	}

	public function isActive() {
		$isactive = coreBOS_Settings::getSetting(self::KEY_ISACTIVE, '0');
		return ($isactive=='1');
	}

	public function getCacheClient() {
		return self::$cacheClient;
	}

	private function setCacheClient($adapterOptions = [], $plugins = null) {
		if (!self::$cacheClient) {
			try {
				self::$cacheClient = new cbCache($this->adapter, $adapterOptions, $plugins);
				self::$cacheClient->has('health');
			} catch (Exception $exception) {
				self::$cacheClient = null;
			}
		}
	}

	private function isConnected() {
		if (!$this->getCacheClient()) {
			return false;
		}
		return true;
	}

	public function isUsable() {
		return $this->isActive() && $this->isConnected();
	}
}
?>