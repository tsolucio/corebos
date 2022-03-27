<?php
/*************************************************************************************************
 * Copyright 2021 JPL TSolucio, S.L. -- This file is a part of TSOLUCIO coreBOS customizations.
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
 *************************************************************************************************
 *  Module    : ClickHouse Integration
 *  Version   : 1.0
 *  Author    : JPL TSolucio, S. L.
 *************************************************************************************************/
include_once 'vtlib/Vtiger/Module.php';
require_once 'include/Webservices/Revise.php';
require_once 'include/Webservices/Create.php';
require_once 'include/integrations/clickhouse/chchangeset.php';

class corebos_clickhouse {
	// Configuration Properties
	private $clickhouse_host = '';
	private $clickhouse_port = '';
	private $clickhouse_password = '';
	private $clickhouse_username = '';
	private $clickhouse_database;
	private $mailup;

	// Configuration Keys
	const KEY_ISACTIVE = 'clickhouse_isactive';
	const HOST = 'clickhouse_host';
	const DATABASE = 'clickhouse_database';
	const USERNAME = 'clickhouse_username';
	const PASSWORD = 'clickhouse_password';
	const PORT = 'clickhouse_port';

	// Debug
	const DEBUG = true;

	// Errors
	public static $ERROR_NONE = 0;
	public static $ERROR_NOTCONFIGURED = 1;
	public static $ERROR_NOACCESSTOKEN = 2;

	private $messagequeue = null;

	public function __construct() {
		$this->initGlobalScope();
	}

	public function initGlobalScope() {
		$this->clickhouse_host = coreBOS_Settings::getSetting(self::HOST, '');
		$this->clickhouse_port = coreBOS_Settings::getSetting(self::PORT, '');
		$this->clickhouse_database = coreBOS_Settings::getSetting(self::DATABASE, '');
		$this->clickhouse_username = coreBOS_Settings::getSetting(self::USERNAME, 'default');
		$this->clickhouse_password = coreBOS_Settings::getSetting(self::PASSWORD, '');
		$this->messagequeue = coreBOS_MQTM::getInstance();
	}

	public function saveSettings($isactive, $host, $port, $database, $username, $password) {
		global $adb;
		coreBOS_Settings::setSetting(self::KEY_ISACTIVE, $isactive);
		coreBOS_Settings::setSetting(self::HOST, $host);
		coreBOS_Settings::setSetting(self::PORT, $port);
		coreBOS_Settings::setSetting(self::DATABASE, $database);
		coreBOS_Settings::setSetting(self::USERNAME, $username);
		coreBOS_Settings::setSetting(self::PASSWORD, $password);

		$em = new VTEventsManager($adb);
		$cs = new clickhousechangeset(0, false);
		if (self::useClickHouseHook()) {
			$cs->applyChange();
			$em->registerHandler('corebos.filter.massageQueueLogger', 'include/integrations/clickhouse/clickhouse.php', 'corebos_clickhouse');
			self::createClickhouseDB();
		} else {
			$cs->undoChange();
			$em->unregisterHandler('corebos_clickhouse');
		}
	}

	public function getSettings() {
		return array(
			'isActive' => coreBOS_Settings::getSetting(self::KEY_ISACTIVE, ''),
			'clickhouse_host' => coreBOS_Settings::getSetting(self::HOST, ''),
			'clickhouse_port' => coreBOS_Settings::getSetting(self::PORT, ''),
			'clickhouse_database' => coreBOS_Settings::getSetting(self::DATABASE, 'default'),
			'clickhouse_username' => coreBOS_Settings::getSetting(self::USERNAME, 'default'),
			'clickhouse_password' => coreBOS_Settings::getSetting(self::PASSWORD, '')
		);
	}

	public function isActive() {
		return (coreBOS_Settings::getSetting(self::KEY_ISACTIVE, '0')=='1');
	}

	public static function useClickHouseHook() {
		$clickhouse = coreBOS_Settings::getSetting(self::KEY_ISACTIVE, '0');
		$host = coreBOS_Settings::getSetting(self::HOST, '');
		$port = coreBOS_Settings::getSetting(self::PORT, '');
		$database = coreBOS_Settings::getSetting(self::DATABASE, 'default');
		return ($clickhouse != '0' && $host != '' && $port != '' && $database != '' );
	}

	public static function connectToClickhouse() {
		$config = [
			'host' => coreBOS_Settings::getSetting(self::HOST, ''),
			'port' => coreBOS_Settings::getSetting(self::PORT, ''),
			'username' => coreBOS_Settings::getSetting(self::USERNAME, ''),
			'password' => coreBOS_Settings::getSetting(self::PASSWORD, ''),
			'readonly' => false,
		];
		$chInstance = new ClickHouseDB\Client($config);
		$chInstance->setTimeout(20); // seconds
		$chInstance->setConnectTimeOut(10);
		$chInstance->settings()->set('allow_ddl', 1);
		return $chInstance;
	}

	public static function createClickhouseDB() {
		$chInstance = self::connectToClickhouse();
		$chInstance->database(coreBOS_Settings::getSetting(self::DATABASE, 'default'));
	}

	public function createClickHouseTables($query) {
		$chInstance = self::connectToClickhouse();
		$chInstance->write($query);
	}

	public function ClickHouseSync() {
		$this->initGlobalScope();
		if (!$this->isActive()) {
			return;
		}
		$cbmq = $this->messagequeue;
		$db = coreBOS_Settings::getSetting(self::DATABASE, 'default');
		while ($msg = $cbmq->getMessage('ClickhouseChannel', 'ClickHouseSync', 'ClickHouseHandler')) {
			$change = unserialize($msg['information']);
			$table = $change['table'];
			$columns = [];
			$values = [];
			foreach ($change['data'] as $column => $value) {
				$columns[] = $column;
				$values[] = $value;
			}
			$this->sendDataToclickHouse($db, $table, $columns, $values);
		}
	}

	public function sendDataToclickHouse($db, $table, $columns, $values) {
		$chInstance = self::connectToClickhouse();
		$chInstance->insert($table.$db, [ $values ], $columns);
	}
}
?>