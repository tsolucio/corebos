<?php
/*+*******************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ******************************************************************************/
require_once 'include/events/SqlResultIterator.inc';

class VTEntityMethodManager {

	private $adb;

	public function __construct($adb) {
		$this->adb = $adb;
	}

	public function addEntityMethod($moduleName, $methodName, $functionPath, $functionName) {
		$adb = $this->adb;
		$rs = $adb->pquery(
			'select count(*)
			from com_vtiger_workflowtasks_entitymethod
			where module_name=? and function_path=? and function_name=? and method_name=?',
			array($moduleName, $functionPath, $functionName, $methodName)
		);
		$cnt = $adb->query_result($rs, 0, 0);
		if ($cnt==0) {
			$id = $adb->getUniqueId('com_vtiger_workflowtasks_entitymethod');
			$adb->pquery(
				'insert into com_vtiger_workflowtasks_entitymethod (workflowtasks_entitymethod_id, module_name, function_path, function_name, method_name) values (?,?,?,?,?)',
				array($id, $moduleName, $functionPath, $functionName, $methodName)
			);
		}
	}

	public function executeMethod($entityData, $methodName) {
		$adb = $this->adb;
		$moduleName = $entityData->getModuleName();
		$result = $adb->pquery(
			'select function_path, function_name from com_vtiger_workflowtasks_entitymethod where module_name=? and method_name=?',
			array($moduleName, $methodName)
		);
		if ($adb->num_rows($result)!=0) {
			$data = $adb->raw_query_result_rowdata($result, 0);
			$functionPath = $data['function_path'];
			$functionName = $data['function_name'];
			require_once $functionPath;
			$functionName($entityData);
		}
	}

	public function methodsForModule($moduleName) {
		$result = $this->adb->pquery('select method_name from com_vtiger_workflowtasks_entitymethod where module_name=?', array($moduleName));
		$it = new SqlResultIterator($this->adb, $result);
		$methodNames = array();
		foreach ($it as $row) {
			$methodNames[] = $row->method_name;
		}
		return $methodNames;
	}

	/**
	 * Function to remove workflowtasks entity method
	 * @param <String> Module Name
	 * @param <String> Entity Method Name.
	 */
	public function removeEntityMethod($moduleName, $methodName) {
		$this->adb->pquery('DELETE FROM com_vtiger_workflowtasks_entitymethod WHERE module_name = ? and method_name= ?', array($moduleName, $methodName));
	}
}
?>
