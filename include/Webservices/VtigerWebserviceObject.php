<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class VtigerWebserviceObject {

	private $id;
	private $name;
	private $handlerPath;
	private $handlerClass;

	private function __construct($entityId, $entityName, $handler_path, $handler_class) {
		$this->id = $entityId;
		$this->name = $entityName;
		$this->handlerPath = $handler_path;
		$this->handlerClass = $handler_class;
	}

	// Cache variables to enable result re-use
	private static $_fromNameCache = array();

	public static function fromName($adb, $entityName) {
		$rowData = false;

		// If the information not available in cache?
		if (!isset(self::$_fromNameCache[$entityName])) {
			$result = $adb->pquery('select * from vtiger_ws_entity where name=?', array($entityName));
			if ($result) {
				$rowCount = $adb->num_rows($result);
				if ($rowCount === 1) {
					$rowData = $adb->query_result_rowdata($result, 0);
					self::$_fromNameCache[$entityName] = $rowData;
				}
			}
		}

		$rowData = isset(self::$_fromNameCache[$entityName]) ? self::$_fromNameCache[$entityName] : false;

		if ($rowData) {
			return new VtigerWebserviceObject($rowData['id'], $rowData['name'], $rowData['handler_path'], $rowData['handler_class']);
		}
		throw new WebServiceException(WebServiceErrorCode::$ACCESSDENIED, 'Permission to perform the operation is denied for name');
	}

	// Cache variables to enable result re-use
	private static $_fromIdCache = array();

	public static function fromId($adb, $entityId) {
		$rowData = false;
		if (strpos($entityId, 'x')>0) {
			list($entityId,$void) = explode('x', $entityId);
		}
		// If the information not available in cache?
		if (!isset(self::$_fromIdCache[$entityId])) {
			$result = $adb->pquery('select * from vtiger_ws_entity where id=?', array($entityId));
			if ($result) {
				$rowCount = $adb->num_rows($result);
				if ($rowCount === 1) {
					$rowData = $adb->query_result_rowdata($result, 0);
					self::$_fromIdCache[$entityId] = $rowData;
				}
			}
		}

		if (!empty(self::$_fromIdCache[$entityId])) {
			$rowData = self::$_fromIdCache[$entityId];
			return new VtigerWebserviceObject($rowData['id'], $rowData['name'], $rowData['handler_path'], $rowData['handler_class']);
		}
		throw new WebServiceException(WebServiceErrorCode::$ACCESSDENIED, 'Permission to perform the operation is denied for id');
	}

	public static function fromQuery($adb, $query) {
		$moduleRegex = '/[fF][rR][Oo][Mm]\s+([^\s;]+)/';
		$matches = array();
		$found = preg_match($moduleRegex, $query, $matches);
		if ($found === 1) {
			return VtigerWebserviceObject::fromName($adb, trim($matches[1]));
		}
		throw new WebServiceException(WebServiceErrorCode::$ACCESSDENIED, 'Permission to perform the operation is denied for query');
	}

	public function getEntityName() {
		return $this->name;
	}

	public function getEntityId() {
		return $this->id;
	}

	public function getHandlerPath() {
		return $this->handlerPath;
	}

	public function getHandlerClass() {
		return $this->handlerClass;
	}
}
?>