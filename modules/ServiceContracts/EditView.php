<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/

require_once 'modules/Vtiger/EditView.php';

if(!empty($_REQUEST['service_id'])) {
	$serviceObj = CRMEntity::getInstance('Services');
	if(isRecordExists($_REQUEST['service_id'])) {
		$serviceObj->retrieve_entity_info($_REQUEST['service_id'], 'Services');
		$focus->column_fields['tracking_unit'] = $serviceObj->column_fields['service_usageunit'];
	}	
}
if(!empty($_REQUEST['return_id']) && !empty($_REQUEST['return_module'])) {
	$invModule = $_REQUEST['return_module'];
	$inventoryObj = CRMEntity::getInstance($invModule);
	$inventoryObj->retrieve_entity_info($_REQUEST['return_id'], $invModule);
	if(empty($_REQUEST['sc_related_to'])) {
		if(!empty($inventoryObj->column_fields['account_id'])) {
			$focus->column_fields['sc_related_to_type'] = 'Accounts';
			$focus->column_fields['sc_related_to'] = $inventoryObj->column_fields['account_id'];
		} else if(!empty($inventoryObj->column_fields['contact_id'])) {
			$focus->column_fields['sc_related_to_type'] = 'Contacts';
			$focus->column_fields['sc_related_to'] = $inventoryObj->column_fields['contact_id'];		
		}
	}
}

	$smarty->display('salesEditView.tpl');

?>