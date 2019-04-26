<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

/**
 * A wrapper around CRMEntity instances
 */
class VTEntityData {
	private $isNew = false;
	private $isDeleted = false;

	/**
	 * Get an entity data object.
	 *
	 * @param $adb Pear database instance.
	 * @param $entityId The id of the entity to load.
	 * @return The new entity data object.
	 */
	public static function fromEntityId($adb, $entityId) {
		$obj = new VTEntityData();
		$obj->entityId = $entityId;
		$result = $adb->pquery('select setype from vtiger_crmentity where crmid=?', array($entityId));
		$setype = $adb->query_result($result, 0, 'setype');

		if ($setype == 'Calendar') {
			$setype = vtws_getCalendarEntityType($entityId);
		}

		$obj->moduleName = $setype;

		require_once 'data/CRMEntity.php';
		$focus = CRMEntity::getInstance($setype);

		$focus->retrieve_entity_info($entityId, $setype);
		$focus->id = $entityId;
		$obj->isNew = false;
		$obj->focus = $focus;
		return $obj;
	}

	/**
	 * Get an entity data object.
	 * @param $adb Pear database instance.
	 * @param $userId The id of the entity to load.
	 * @return The new entity data object.
	 */
	public static function fromUserId($adb, $userId) {
		$obj = new VTEntityData();
		$obj->entityId = $userId;

		$obj->moduleName = 'Users';

		require_once 'data/CRMEntity.php';
		$focus = CRMEntity::getInstance($obj->moduleName);

		$focus->retrieve_entity_info($userId, $obj->moduleName);
		$focus->id = $userId;
		$obj->isNew = false;
		$obj->focus = $focus;
		return $obj;
	}

	/**
	 * Get an entity data object from a crmentity object
	 *
	 * @param $crmEntity The CRMEntity instance.
	 * @return The new entity data object.
	 */
	public static function fromCRMEntity($crmEntity) {
		$obj = new VTEntityData();
		$obj->focus = $crmEntity;
		$obj->isNew = !(isset($crmEntity->id) && $crmEntity->id != null);
		return $obj;
	}

	/**
	 * Get the data from the entity object as an array.
	 *
	 * @return An array representation of the module data.
	 */
	public function getData() {
		return $this->focus->column_fields;
	}

	/**
	 * Get the entity id.
	 *
	 * @return The entity id.
	 */
	public function getId() {
		return (isset($this->focus->id) ? $this->focus->id : 0);
	}

	/**
	 * Get the name of the module represented by the entity data object.
	 *
	 * @return The module name.
	 */
	public function getModuleName() {
		$className = get_class($this->focus);
		$importModuleMapping = array(
			'ImportLead'=>'Leads',
			'ImportAccount'=>'Accounts',
			'ImportContact'=>'Contacts',
			'ImportOpportunity'=>'Potentials',
			'ImportProduct'=>'Products',
			'ImportTicket'=>'HelpDesk',
			'ImportVendors'=>'Vendors'
		);
		$moduleName = $className;
		if (array_key_exists($className, $importModuleMapping)) {
			$moduleName = $importModuleMapping[$className];
		}

		if ($className == 'Activity') {
			$id = $this->getId();
			if ($id != null || $id != '') {
				$moduleName = vtws_getCalendarEntityType($id);
			}
		}
		return $moduleName;
	}

	public function get($fieldName) {
		return (isset($this->focus->column_fields[$fieldName]) ? $this->focus->column_fields[$fieldName] : null);
	}

	public function set($fieldName, $value) {
		$this->focus->column_fields[$fieldName] = $value;
	}

	/**
	 * Check whether the object is stored on the database.
	 *
	 * @return True if the object is saved false otherwiser.
	 */
	public function isSaved() {
		return isset($this->focus->id);
	}

	/**
	* Check whether the object is new.
	*
	* @return True if the object is new, false otherwise.
	*/
	public function isNew() {
		return $this->isNew;
	}

	/**
	* Check whether the object has been deleted.
	* @return True if the object has been deleted, false otherwise.
	*/
	public function isDeleted() {
		return $this->isDeleted;
	}

	public function SetDeleted($id) {
		$this->isNew=false;
		$this->isDeleted=true;
		$this->focus->id=$id;
	}
}
?>
