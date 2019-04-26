<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/
global $adb;
if (!empty($_REQUEST['related_id'])) {
	switch ($_REQUEST['return_module']) {
		case 'Invoice':
		case 'Quotes':
		case 'SalesOrder':
			$lermod=strtolower($_REQUEST['return_module']);
			$lermods=($lermod=='quotes' ? 'quote':$lermod);
			$relq = $adb->pquery('select accountid,contactid,total from vtiger_'.$lermod.' where '.$lermods.'id=?', array($_REQUEST['related_id']));
			$relid = $_REQUEST['parent_id']=$adb->query_result($relq, 0, 'accountid');
			if (empty($relid) || GlobalVariable::getVariable('Application_B2B', '1')=='0') {
				$relid = $_REQUEST['parent_id']=$adb->query_result($relq, 0, 'contactid');
			}
			$_REQUEST['parent_id']=$relid;
			$_REQUEST['amount'] = $adb->query_result($relq, 0, 'total');
			break;
		case 'PurchaseOrder':
			$relq = $adb->pquery('select vendorid,total from vtiger_purchaseorder where purchaseorderid=?', array($_REQUEST['related_id']));
			$_REQUEST['parent_id']=$adb->query_result($relq, 0, 'vendorid');
			$_REQUEST['amount'] = $adb->query_result($relq, 0, 'total');
			break;
		case 'Potentials':
			$relq = $adb->pquery('select related_to from vtiger_potential where potentialid=?', array($_REQUEST['related_id']));
			$_REQUEST['parent_id']=$adb->query_result($relq, 0, 0);
			break;
		case 'HelpDesk':
			$relq = $adb->pquery('select parent_id from vtiger_troubletickets where ticketid=?', array($_REQUEST['related_id']));
			$_REQUEST['parent_id']=$adb->query_result($relq, 0, 0);
			break;
	}
}

require_once 'modules/Vtiger/EditView.php';

$app_strings['LBL_CHANGE']=$mod_strings['LBL_CHANGE'];

$smarty->display('salesEditView.tpl');
?>