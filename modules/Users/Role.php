<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************ */

class Vtiger_Role {

	private $id;
	private $name;
	private $parentRoleSequence;
	private $depth;

	private function __construct($id, $name, $parentRole, $depth) {
		$this->id = $id;
		$this->name = $name;
		$this->parentRoleSequence = $parentRole;
		$this->depth = $depth;
	}

	public function getId() {
		return $this->id;
	}

	public function getName() {
		return $this->name;
	}

	public function getParentRole() {
		return $this->parentRoleSequence;
	}

	public function getDepth() {
		return $this->depth;
	}

	public function setDepty($depth) {
		$this->depth = $depth;
	}

	public function setParentRole($parentRole) {
		$this->parentRoleSequence = $parentRole;
	}

	/**
	 *
	 * @param QueryResult $result
	 * @param Number $row
	 * @return self
	 */
	public static function getInstanceByResult($result, $row) {
		$db = PearDatabase::getInstance();
		$rowData = $db->query_result_rowdata($result, $row);
		$depth = $rowData['depth'];
		$id = $rowData['roleid'];
		$name = $rowData['rolename'];
		$parentRole = $rowData['parentrole'];
		return new self($id, $name, $parentRole, $depth);
	}

	/**
	 *
	 * @param Role id $id
	 * @return Vtiger_Role
	 */
	public static function getInstanceById($id) {
		$db = PearDatabase::getInstance();
		$result = $db->pquery('select * from vtiger_role where roleid=?', array($id));
		return self::getInstanceByResult($result, 0);
	}

	/**
	 *
	 * @param Vtiger_Role $role
	 */
	public function moveTo($role) {
		global $adb;
		//parent role has current role id in parent role sequence, remove current role id.
		$parentRoleSequence = $role->getParentRole().'::'.$this->getId();
		$subDepth=$role->getDepth() + 1;
		$adb->pquery('update vtiger_role set parentrole=?,depth=? where roleid=?', array($parentRoleSequence, $subDepth, $this->getId()));
		$this->setDepty($subDepth);
		$this->setParentRole($parentRoleSequence);
	}

	public function delete($role) {
		$db = PearDatabase::getInstance();
		$db->dieOnError = true;
		$db->pquery('update vtiger_user2role set roleid=? where roleid=?', array($role->getId(), $this->getId()));

		//Deleteing from vtiger_role2profile vtiger_table
		$db->pquery('delete from vtiger_role2profile where roleid=?', array($this->getId()));

		//delete handling for vtiger_groups
		$db->pquery('delete from vtiger_group2role where roleid=?', array($this->getId()));

		$db->pquery('delete from vtiger_group2rs where roleandsubid=?', array($this->getId()));

		//delete handling for sharing rules
		deleteRoleRelatedSharingRules($this->getId());

		//delete from vtiger_role vtiger_table;
		$db->pquery('delete from vtiger_role where roleid=?', array($this->getId()));

		$targetParentRoleSequence = $role->getParentRole();
		$parentRoleSequence = $this->getParentRole();
		$roleInfoList = getRoleAndSubordinatesInformation($role->getId());
		$query='update vtiger_role set parentrole=?,depth=? where roleid=?';
		foreach ($roleInfoList as $roleId => $roleInfo) {
			// Invalidate any cached information
			VTCacheUtils::clearRoleSubordinates($roleId);
			if ($roleId == $this->getId()) {
				continue;
			}
			$currentParentRoleSequence = $roleInfo[1];
			$currentParentRoleSequence = str_replace($parentRoleSequence, $targetParentRoleSequence, $currentParentRoleSequence);
			$subDepth = count(explode('::', $currentParentRoleSequence))-1;
			$db->pquery($query, array($currentParentRoleSequence, $subDepth, $roleId));
		}
	}
}
?>