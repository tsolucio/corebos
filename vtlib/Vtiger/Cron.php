<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *********************************************************************************** */

include_once 'vtlib/Vtiger/Utils.php';
require_once 'include/database/PearDatabase.php';

/**
 * Provides API to work with Cron tasks
 * @package vtlib
 */
class Vtiger_Cron {

	protected static $schemaInitialized = false;
	protected static $instanceCache = array();
	public static $STATUS_DISABLED = 0;
	public static $STATUS_ENABLED = 1;
	public static $STATUS_RUNNING = 2;
	protected $data;
	protected $bulkMode = false;
	public $fixedTasks = array();

	/**
	 * Constructor
	 */
	protected function __construct($values) {
		$this->data = $values;
		self::$instanceCache[$this->getName()] = $this;
	}

	/**
	 * Get id reference of this instance.
	 */
	public function getId() {
		return $this->data['id'];
	}

	/**
	 * Get name of this task instance.
	 */
	public function getName() {
		return decode_html($this->data['name']);
	}

	/**
	 * Get the frequency set.
	 */
	public function getFrequency() {
		return (int)$this->data['frequency'];
	}

	/**
	 * Get the daily set.
	 */
	public function getdaily() {
		return (int)$this->data['daily'];
	}

	/**
	 * Get the status
	 */
	public function getStatus() {
		return (int)$this->data['status'];
	}
	/**
	 * Get the timestamp lastrun started.
	 */
	public function getLastStart() {
		return (int)$this->data['laststart'];
	}

	/**
	 * Get the timestamp lastrun ended.
	 */
	public function getLastEnd() {
		return (int)$this->data['lastend'];
	}

	/**
	 * Get the user datetimefeild
	 */
	public function getLastEndDateTime() {
		if ($this->data['lastend'] != null) {
			$lastEndDateTime = new DateTimeField(date('Y-m-d H:i:s', $this->data['lastend']));
			return $lastEndDateTime->getDisplayDateTimeValue();
		} else {
			return '';
		}
	}

	/**
	 *
	 * get the last start datetime field
	 */
	public function getLastStartDateTime() {
		if ($this->data['laststart'] != null) {
			$lastStartDateTime = new DateTimeField(date('Y-m-d H:i:s', $this->data['laststart']));
			return $lastStartDateTime->getDisplayDateTimeValue();
		} else {
			return '';
		}
	}

	/**
	 * Get Time taken to complete task
	 */
	public function getTimeDiff() {
		$lastStart = $this->getLastStart();
		$lastEnd   = $this->getLastEnd();
		return $lastEnd - $lastStart;
	}

	/**
	 * Get the configured handler file.
	 */
	public function getHandlerFile() {
		return $this->data['handler_file'];
	}

	/**
	 *Get the Module name
	 */
	public function getModule() {

		return $this->data['module'];
	}

	/**
	 * get the Sequence
	 */
	public function getSequence() {
		return $this->data['sequence'];
	}

	/**
	 * get the description of cron
	 */
	public function getDescription() {
		return $this->data['description'];
	}

	/**
	 * Check if task is right state for running.
	 */
	public function isRunnable() {
		$runnable = false;

		if (!$this->isDisabled() && !$this->isRunning()) {
			// Take care of last time (end - on success, start - if timedout)
			$lastTime = ($this->getLastEnd() > 0) ? $this->getLastEnd() : $this->getLastStart();
			$elapsedTime = time() - $lastTime;
			$runnable = ($elapsedTime >= $this->getFrequency());
		}
		return $runnable;
	}

	/**
	 * Helper function to check the status value.
	 */
	public function statusEqual($value) {
		$status = (int)$this->data['status'];
		return $status == $value;
	}

	/**
	 * Is task in running status?
	 */
	public function isRunning() {
		return $this->statusEqual(self::$STATUS_RUNNING);
	}

	/**
	 * Is task enabled?
	 */
	public function isEnabled() {
		return $this->statusEqual(self::$STATUS_ENABLED);
	}

	/**
	 * Is task disabled?
	 */
	public function isDisabled() {
		return $this->statusEqual(self::$STATUS_DISABLED);
	}

	/**
	 * Update status
	 */
	public function updateStatus($status) {
		switch ((int)$status) {
			case self::$STATUS_DISABLED:
			case self::$STATUS_ENABLED:
			case self::$STATUS_RUNNING:
				break;
			default:
				throw new Exception('Invalid status');
		}
		self::querySilent('UPDATE vtiger_cron_task SET status=? WHERE id=?', array($status, $this->getId()));
	}

	/**
	 * update frequency
	 */
	public function updateFrequency($frequency) {
		self::querySilent('UPDATE vtiger_cron_task SET frequency=? WHERE id=?', array($frequency, $this->getId()));
	}

	/**
	 * update daily
	 */
	public function updatedaily($d) {
		self::querySilent('UPDATE vtiger_cron_task SET daily=? WHERE id=?', array($d, $this->getId()));
	}

	/**
	 * update last start/end
	 */
	public function updatelaststartend($startend) {
		self::querySilent('UPDATE vtiger_cron_task SET lastend=?, laststart=? WHERE id=?', array($startend,$startend, $this->getId()));
	}

	/**
	 * Mark this instance as running.
	 */
	public function markRunning() {
		self::querySilent('UPDATE vtiger_cron_task SET status=?, laststart=?, lastend=? WHERE id=?', array(self::$STATUS_RUNNING, time(), 0, $this->getId()));
		return $this;
	}

	/**
	 * Mark this instance as finished.
	 */
	public function markFinished($daily, $timestart) {
		if ($daily==1) {
			$time=strtotime(' +1 days', $timestart);
		} else {
			$time=time();
		}
		self::querySilent('UPDATE vtiger_cron_task SET status=?, lastend=? WHERE id=?', array(self::$STATUS_ENABLED, $time, $this->getId()));
		return $this;
	}

	/**
	 * Set the bulkMode flag
	 */
	public function setBulkMode($mode = null) {
		$this->bulkMode = $mode;
	}

	/**
	 * Is task in bulk mode execution?
	 */
	public function inBulkMode() {
		return $this->bulkMode;
	}

	/**
	 * Detect if the task was started by never finished.
	 */
	public function hadTimedout() {
		return ($this->data['lastend'] === '0' && $this->data['laststart'] != 0);
	}

	/**
	 * Execute SQL query silently (even when table doesn't exist)
	 */
	protected static function querySilent($sql, $params = false) {
		global $adb;
		$old_dieOnError = $adb->dieOnError;
		$adb->dieOnError = false;
		$result = $adb->pquery($sql, $params);
		$adb->dieOnError = $old_dieOnError;
		return $result;
	}

	/**
	 * Initialize the schema.
	 */
	protected static function initializeSchema() {
		if (!self::$schemaInitialized) {
			if (!Vtiger_Utils::CheckTable('vtiger_cron_task')) {
				Vtiger_Utils::CreateTable(
					'vtiger_cron_task',
					'(id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
					name VARCHAR(100) UNIQUE KEY, handler_file VARCHAR(100) UNIQUE KEY,
					frequency int, laststart long, lastend long, status int,module VARCHAR(100),
					sequence int,description TEXT)',
					true
				);
			}
			self::$schemaInitialized = true;
		}
	}

	public static function nextSequence() {
		global $adb;
		$result = self::querySilent('SELECT MAX(sequence) FROM vtiger_cron_task ORDER BY SEQUENCE');
		if ($result && $adb->num_rows($result)) {
			$row = $adb->fetch_array($result);
		}
		if ($row == null) {
			$row['max(sequence)'] = 1;
		}
		return $row['max(sequence)']+1;
	}

	/**
	 * Register cron task.
	 */
	public static function register($name, $handler_file, $frequency, $module = 'Home', $status = 1, $sequence = 0, $description = '') {
		self::initializeSchema();
		self::getInstance($name);
		if ($sequence == 0) {
			$sequence = self::nextSequence();
		}
		self::querySilent(
			'INSERT INTO vtiger_cron_task (name, handler_file, frequency, status, sequence,module, description) VALUES(?,?,?,?,?,?,?)',
			array($name, $handler_file, $frequency, $status, $sequence, $module, $description)
		);
	}

	/**
	 * De-register cron task.
	 */
	public static function deregister($name) {
		self::querySilent('DELETE FROM vtiger_cron_task WHERE name=?', array($name));
		if (isset(self::$instanceCache["$name"])) {
			unset(self::$instanceCache["$name"]);
		}
	}

	/**
	 * Get instances that are active (not disabled)
	 */
	public static function listAllActiveInstances($byStatus = 0) {
		global $adb;
		$instances = array();
		if ($byStatus == 0) {
			$result = self::querySilent('SELECT * FROM vtiger_cron_task WHERE status <> ? ORDER BY SEQUENCE', array(self::$STATUS_DISABLED));
		} else {
			$result = self::querySilent('SELECT * FROM vtiger_cron_task ORDER BY SEQUENCE');
		}
		if ($result && $adb->num_rows($result)) {
			while ($row = $adb->fetch_array($result)) {
				$instances[] = new Vtiger_Cron($row);
			}
		}
		return $instances;
	}

	/**
	 * Get instance of cron task.
	 */
	public static function getInstance($name) {
		global $adb;

		$instance = false;
		if (isset(self::$instanceCache["$name"])) {
			$instance = self::$instanceCache["$name"];
		}

		if ($instance === false) {
			$result = self::querySilent('SELECT * FROM vtiger_cron_task WHERE name=?', array($name));
			if ($result && $adb->num_rows($result)) {
				$instance = new Vtiger_Cron($adb->fetch_array($result));
			}
		}
		return $instance;
	}

	/**
	 * Get instance of cron job by id
	 */
	public static function getInstanceById($id) {
		global $adb;
		$instance = false;
		if (isset(self::$instanceCache[$id])) {
			$instance = self::$instanceCache[$id];
		}

		if ($instance === false) {
			$result = self::querySilent('SELECT * FROM vtiger_cron_task WHERE id=?', array($id));
			if ($result && $adb->num_rows($result)) {
				$instance = new Vtiger_Cron($adb->fetch_array($result));
			}
		}
		return $instance;
	}

	public static function listAllInstancesByModule($module) {
		global $adb;

		$instances = array();
		$result = self::querySilent('SELECT * FROM vtiger_cron_task WHERE module=?', array($module));
		if ($result && $adb->num_rows($result)) {
			while ($row = $adb->fetch_array($result)) {
				$instances[] = new Vtiger_Cron($row);
			}
		}
		return $instances;
	}
}
?>