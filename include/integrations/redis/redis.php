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
 *  Module    : PSR-16 redis integration
 *  Version   : 1.0
 *  Author    : JPL TSolucio, S. L.
 *************************************************************************************************/
// require 'vendor/autoload.php';

class corebos_redis {

	// Configuration Properties
	private $ip;
	private $port;

	// Configuration Keys
	const KEY_ISACTIVE = 'redis_isactive';
	const KEY_IP = 'redis_ip';
	const KEY_PORT = 'redis_port';

	// Utilities
	private static $redisClient = null;

	public function __construct() {
		$this->initGlobalScope();
	}

	public function initGlobalScope() {
		if ($this->isActive()) {
			$this->ip = coreBOS_Settings::getSetting(self::KEY_IP, '');
			$this->port = coreBOS_Settings::getSetting(self::KEY_PORT, '');
			$this->setRedisClient();
		}
	}

	public function saveSettings($isactive, $ip = null, $port = null) {
		global $log;
		$log->fatal('the save funtion is being executed');
		coreBOS_Settings::setSetting(self::KEY_ISACTIVE, $isactive);
		coreBOS_Settings::setSetting(self::KEY_IP, $ip);
		coreBOS_Settings::setSetting(self::KEY_PORT, $port);
	}

	public function getSettings() {
		if (!$this->getRedisClient()) {
			$this->setRedisClient();
		}
		return array(
			'isActive' => coreBOS_Settings::getSetting(self::KEY_ISACTIVE, ''),
			'ip' => coreBOS_Settings::getSetting(self::KEY_IP, ''),
			'port' => coreBOS_Settings::getSetting(self::KEY_PORT, ''),
		);
	}

	public function isActive() {
		$isactive = coreBOS_Settings::getSetting(self::KEY_ISACTIVE, '0');
		return ($isactive=='1');
	}

	public function getRedisClient() {
		return self::$redisClient;
	}

	private function setRedisClient() {
		if (!self::$redisClient) {
			$redis_ip = coreBOS_Settings::getSetting(self::KEY_IP, '');
			$redis_port = coreBOS_Settings::getSetting(self::KEY_PORT, '');
			try {
				$this::$redisClient = new Redis();
				$this::$redisClient->connect($redis_ip, $redis_port);
			} catch (Exception $exception) {
				self::$redisClient = null;
			}
		}
	}

	private function isConnected() {
		if (!$this->getRedisClient()) {
			return false;
		}
		return true;
	}

	public function isUsable() {
		global $log;
		if ($this->isActive()) {
			$log->fatal('it is active');
		} else {
			$log->fatal('it is NOT active');
		}

		if ($this->isConnected()) {
			$log->fatal('it is connected');
		} else {
			$log->fatal('it is NOT connected');
		}

		return $this->isActive() && $this->isConnected();
	}
}

if (empty($cbRedis)) {
	$cbRedis = new corebos_redis();
}
?>