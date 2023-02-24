<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/
class com_vtiger_workflow extends CRMEntity {

	public $table_name = 'com_vtiger_workflows';
	public $table_index= 'workflow_id';
	public $tab_name = array('com_vtiger_workflows');
	public $tab_name_index = array('com_vtiger_workflows' => 'workflow_id');
	public $list_link_field = 'summary';
	public $default_order_by = 'summary';
	public $default_sort_order='ASC';
	public $moduleIcon = array('library' => 'standard', 'containerClass' => 'slds-icon_container slds-icon-standard-document', 'class' => 'slds-icon', 'icon'=>'process');

	/**
	 * Track the viewing of a detail record.
	 * params $user_id - The user that is viewing the record.
	 */
	public function track_view($user_id, $current_module, $id = '') {
	}

	/**
	 * @param string $module - module name for which query needs to be generated.
	 * @param Users $user - user for which query needs to be generated.
	 * @return String Access control Query for the user.
	 */
	public function getNonAdminAccessControlQuery($module, $user, $scope = '') {
		$userprivs = $user->getPrivileges();
		$query = ' ';
		if (!$userprivs->isAdmin()) {
			$query = ' and false ';
		}
		return $query;
	}
}
?>