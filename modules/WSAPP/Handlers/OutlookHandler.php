<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/
require_once 'include/fields/DateTimeField.php';
require_once 'modules/WSAPP/SyncServer.php';
require_once 'modules/WSAPP/Handlers/SyncHandler.php';
require_once 'modules/WSAPP/OutlookSyncServer.php';

class OutlookHandler extends SyncHandler {

	public function __construct($appkey) {
		$this->syncServer = new OutlookSyncServer();
		$this->key = $appkey;
	}

	public function get($module, $token, $user) {
		$this->syncModule = $module;
		$this->user = $user;
		$result = $this->syncServer->get($this->key, $module, $token, $user);
		$nativeForamtElementList = $result;
		$nativeForamtElementList['created'] = $this->syncToNativeFormat($result['created']);
		$nativeForamtElementList['updated'] = $this->syncToNativeFormat($result['updated']);
		return $nativeForamtElementList;
	}

	public function put($element, $user) {
		$this->user = $user;
		$element = $this->nativeToSyncFormat($element);
		if ($element=='cbCalendar') {
			//To convert minutes to seconds. Since the webservices require the reminder to be in seconds
			$this->convertReminderTimeToSecond($element);
		}
		return $this->syncServer->put($this->key, $element, $user);
	}

	public function map($olMapElement, $user) {
		$this->user = $user;
		$element = $this->convertMapRecordsToSyncFormat($olMapElement);
		return $this->syncServer->map($this->key, $element, $user);
	}

	public function nativeToSyncFormat($element) {
		$syncFormatElementList = array();
		foreach ($element as $recordDetails) {
			if (!empty($recordDetails['values'])) {
				$recordDetails['values'] = $this->convertRecordToSyncFormat($recordDetails['module'], $recordDetails['values']);
			}
			$syncFormatElementList[] = $recordDetails;
		}
		return $syncFormatElementList;
	}

	public function syncToNativeFormat($recordList) {
		$nativeFormatRecordList = array();
		foreach ($recordList as $record) {
			$nativeFormatRecordList[] = $this->convertRecordToNativeFormat($this->syncModule, $record);
		}
		return $nativeFormatRecordList;
	}

	private function convertRecordToSyncFormat($module, $record) {
		if ($module == 'cbCalendar') {
			$startTime = strtotime($record['dtstart']);
			$endTime = strtotime($record['dtend']);
			$dateFormat = 'Y-m-d';
			$timeFormat = 'H:i:s';
			$record['date_start'] = date($dateFormat, $startTime);
			$record['time_start'] = date($timeFormat, $startTime);
			$record['due_date'] = date($dateFormat, $endTime);
			$record['time_end'] = date($timeFormat, $endTime);
			$record['duration_hours'] = date('H', ($endTime-$startTime));
			$record['duration_minutes'] = date('i', ($endTime-$startTime));
		}
		$record['modifiedtime'] = $record['utclastmodifiedtime'];
		return $record;
	}

	private function convertRecordToNativeFormat($module, $record) {
		if ($module == 'cbCalendar') {
			$dformat = 'Y-m-d H:i:s';

			$record['start_time'] = date($dformat, strtotime($record['dtstart']));
			$record['end_time'] = date($dformat, strtotime($record['dtend']));

			// convert the start time and end time to user time zone as outlook does not take the datetime in utc
			$oldDateFormat = $this->user->date_format;
			$this->user->date_format = 'yyyy-mm-dd';
			$dateTimeField = new DateTimeField($record['start_time']);
			$record['start_time'] = $dateTimeField->getDisplayDateTimeValue($this->user);

			$dateTimeField = new DateTimeField($record['end_time']);
			$record['end_time'] = $dateTimeField->getDisplayDateTimeValue($this->user);
			$this->user->date_format = $oldDateFormat;
		}
		return $record;
	}

	private function convertMapRecordsToSyncFormat($elements) {
		$syncMapFormatElements = array();
		$syncMapFormatElements['create'] = array();
		$syncMapFormatElements['delete'] = array();
		$syncMapFormatElements['update'] = array();

		foreach ($elements as $olElement) {
			if ($olElement['mode']=='create') {
				$syncMapFormatElements['create'][$olElement['clientid']] = $olElement['values'];
			} elseif ($olElement['mode']=='update') {
				$syncMapFormatElements['update'][$olElement['clientid']] = $olElement['values'];
			} elseif ($olElement['mode']=='delete') {
				$syncMapFormatElements['delete'][] = $olElement['clientid'];
			}
		}
		return $syncMapFormatElements;
	}

	private function convertReminderTimeToSecond($records) {
		foreach ($records as $record) {
			if (!empty($record['values'])) {
				//converting mins to seconds
				$record['reminder_time']  = $record['reminder_time']*60;
			}
		}
	}
}
?>
