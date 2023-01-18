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
	private $clickhouse_webhook_secret = '';

	// Configuration Keys
	const KEY_ISACTIVE = 'clickhouse_isactive';
	const HOST = 'clickhouse_host';
	const DATABASE = 'clickhouse_database';
	const USERNAME = 'clickhouse_username';
	const PASSWORD = 'clickhouse_password';
	const PORT = 'clickhouse_port';
	const CLICKHOUSE_WEBHOOKSECRET = 'clickhouse_webhook_secret';

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
		$this->clickhouse_port = (int)coreBOS_Settings::getSetting(self::PORT, 0);
		$this->clickhouse_database = coreBOS_Settings::getSetting(self::DATABASE, '');
		$this->clickhouse_username = coreBOS_Settings::getSetting(self::USERNAME, 'default');
		$this->clickhouse_password = coreBOS_Settings::getSetting(self::PASSWORD, '');
		$this->clickhouse_webhook_secret = coreBOS_Settings::getSetting(self::CLICKHOUSE_WEBHOOKSECRET, '');
		$this->messagequeue = coreBOS_MQTM::getInstance();
	}

	public function saveSettings($isactive, $host, $port, $database, $username, $password, $webhook_secret) {
		global $adb;
		$old_webhook_secret = coreBOS_Settings::getSetting(self::CLICKHOUSE_WEBHOOKSECRET, '');
		coreBOS_Settings::setSetting(self::KEY_ISACTIVE, $isactive);
		coreBOS_Settings::setSetting(self::HOST, $host);
		coreBOS_Settings::setSetting(self::PORT, $port);
		coreBOS_Settings::setSetting(self::DATABASE, $database);
		coreBOS_Settings::setSetting(self::USERNAME, $username);
		coreBOS_Settings::setSetting(self::PASSWORD, $password);
		coreBOS_Settings::setSetting(self::CLICKHOUSE_WEBHOOKSECRET, $webhook_secret);

		$em = new VTEventsManager($adb);
		$cs = new clickhousechangeset(0, false);
		if (self::useClickHouseHook()) {
			$cs->applyChange();
			$em->registerHandler('corebos.filter.massageQueueLogger', 'include/integrations/clickhouse/clickhouse.php', 'corebos_clickhouse');
			self::createClickhouseDB();
			$checkrs = $adb->pquery(
				'select 1 from vtiger_notificationdrivers where path=? and functionname=?',
				array('include/integrations/clickhouse/notification.php', 'chnotification')
			);
			if ($checkrs && $adb->num_rows($checkrs)==0) {
				$adb->query(
					"INSERT INTO vtiger_notificationdrivers (type, path, functionname, signedvalue, signedkey, signedvalidation)
						VALUES ('clickhouse','include/integrations/clickhouse/notification.php','chnotification','$webhook_secret','secret','validateClickHouseSecret')"
				);
			} else {
				if ($old_webhook_secret !== $webhook_secret) {
					$adb->query(
						'UPDATE vtiger_notificationdrivers SET signedvalue=? WHERE path=? and functionname=?',
						array($webhook_secret, 'include/integrations/clickhouse/notification.php', 'chnotification')
					);
				}
			}
		} else {
			$cs->undoChange();
			$em->unregisterHandler('corebos_clickhouse');
			$adb->pquery(
				'DELETE FROM vtiger_notificationdrivers WHERE path=? and functionname=?',
				array('include/integrations/clickhouse/notification.php', 'chnotification')
			);
		}
	}

	public function getSettings() {
		return array(
			'isActive' => coreBOS_Settings::getSetting(self::KEY_ISACTIVE, ''),
			'clickhouse_host' => coreBOS_Settings::getSetting(self::HOST, ''),
			'clickhouse_port' => (int)coreBOS_Settings::getSetting(self::PORT, 0),
			'clickhouse_database' => coreBOS_Settings::getSetting(self::DATABASE, 'default'),
			'clickhouse_username' => coreBOS_Settings::getSetting(self::USERNAME, 'default'),
			'clickhouse_password' => coreBOS_Settings::getSetting(self::PASSWORD, ''),
			'clickhouse_webhook_secret' => coreBOS_Settings::getSetting(self::CLICKHOUSE_WEBHOOKSECRET, '')
		);
	}

	public function isActive() {
		return (coreBOS_Settings::getSetting(self::KEY_ISACTIVE, '0')=='1');
	}

	public static function useClickHouseHook() {
		$clickhouse = coreBOS_Settings::getSetting(self::KEY_ISACTIVE, '0');
		$host = coreBOS_Settings::getSetting(self::HOST, '');
		$port = (int)coreBOS_Settings::getSetting(self::PORT, 0);
		$database = coreBOS_Settings::getSetting(self::DATABASE, 'default');
		return ($clickhouse != '0' && $host != '' && !empty($port) && $database != '' );
	}

	public static function connectToClickhouse() {
		$config = [
			'host' => coreBOS_Settings::getSetting(self::HOST, ''),
			'port' => (int)coreBOS_Settings::getSetting(self::PORT, 0),
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

	public function addUpdateTable($ws_name, $table_name, $access, $create, $read, $write, $old_ws_name = '', $old_table_name = '') {
		global $adb;
		$result = [
			'success' => false,
			'message' => getTranslatedString('clickhouse_upserterror', 'Utilities'),
		];
		$check = $adb->pquery('select 1 from vtiger_ws_entity where `name`=?', [$ws_name]);
		if ($old_table_name === '' || $old_ws_name === '') {
			if ($check && $adb->num_rows($check)>0) {
				$result['message'] = getTranslatedString('clickhouse_tableexists', 'Utilities');
				return $result;
			}
			$query = 'INSERT INTO `vtiger_ws_clickhousetables` (`ws_name`, `table_name`, `access`, `create`, `read`, `write`) VALUES (?,?,?,?,?,?)';
			$res = $adb->pquery($query, array($ws_name, $table_name, $access, $create, $read, $write));
			if ($res) {
				$wsid = $adb->getUniqueID('vtiger_ws_entity');
				$adb->pquery(
					"INSERT INTO `vtiger_ws_entity` (`id`, `name`, `handler_path`, `handler_class`, `ismodule`)
						VALUES (?, ?, 'include/Webservices/VtigerClickHouseOperation.php', 'VtigerClickHouseOperation', '0')",
					array($wsid, $ws_name)
				);
				$adb->pquery('INSERT INTO vtiger_ws_entity_tables (webservice_entity_id, table_name) VALUES (?, ?)', array($wsid, $table_name));
				return ['success' => true];
			}
		} else {
			if ($ws_name == $old_ws_name && (!$check || $adb->num_rows($check)!=1)) {
				return $result;
			}
			if ($ws_name != $old_ws_name) {
				if ($check && $adb->num_rows($check)>0) {
					$result['message'] = getTranslatedString('clickhouse_tableexists', 'Utilities');
					return $result;
				}
				$check = $adb->pquery('select 1 from vtiger_ws_entity where `name`=? and handler_class=?', [$old_ws_name, 'VtigerClickHouseOperation']);
				if (!$check || $adb->num_rows($check)==0) {
					$result['message'] = getTranslatedString('clickhouse_tableexists', 'Utilities');
					return $result;
				}
			}
			$query = 'UPDATE vtiger_ws_clickhousetables SET `ws_name`=?,`table_name`=?,`access`=?,`create`=?,`read`=?,`write`=? WHERE ';
			if ($old_ws_name === '') {
				$query.= 'table_name=?';
				$reference = $old_table_name;
			} else {
				$query.= 'ws_name=?';
				$reference = $old_ws_name;
			}
			$res = $adb->pquery($query, array($ws_name, $table_name, $access, $create, $read, $write, $reference));
			if ($res) {
				if ($ws_name !== $old_ws_name) {
					$adb->pquery('UPDATE vtiger_ws_entity SET name=? WHERE name=? and handler_class=?', array($ws_name, $old_ws_name, 'VtigerClickHouseOperation'));
				}
				if ($table_name !== $old_table_name) {
					$adb->pquery('UPDATE vtiger_ws_entity_tables SET table_name=? WHERE table_name=?', array($table_name, $old_table_name));
				}
				return ['success' => true];
			} else {
				return $result;
			}
		}
		return $result;
	}

	public function getTables() {
		global $adb;
		$tables = array();
		$res = $adb->query('select * from vtiger_ws_clickhousetables');
		while ($row = $adb->fetch_array($res)) {
			$table = array(
				'id' => $row['id'],
				'ws_name' => $row['ws_name'],
				'table_name' => $row['table_name'],
				'access' => $row['access'],
				'create' => $row['create'],
				'read' => $row['read'],
				'write' => $row['write'],
				'delete' => $row['delete'],
			);
			$tables[] = $table;
		}
		return $tables;
	}

	public function getTable($table_name) {
		global $adb;
		$table = array();
		$res = $adb->pquery('select * from vtiger_ws_clickhousetables where table_name = ? limit 1', array($table_name));
		if ($res) {
			$row = $adb->fetch_array($res);
			$table = array(
				'id' => $row['id'],
				'ws_name' => $row['ws_name'],
				'table_name' => $row['table_name'],
				'access' => $row['access'],
				'create' => $row['create'],
				'read' => $row['read'],
				'write' => $row['write'],
				'delete' => $row['delete'],
			);
		}
		return $table;
	}

	public function deleteTable($table_name, $ws_name) {
		global $adb;
		$res = $adb->pquery('delete from vtiger_ws_clickhousetables where table_name = ?', array($table_name));
		if ($res) {
			$adb->pquery('delete from vtiger_ws_entity where name = ?', array($ws_name));
			$adb->pquery('delete from vtiger_ws_entity_tables where table_name = ?', array($table_name));
			return true;
		}
		return false;
	}
}
?>