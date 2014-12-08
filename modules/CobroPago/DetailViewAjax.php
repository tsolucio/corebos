<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/
global $currentModule;
$modObj = CRMEntity::getInstance($currentModule);

$ajaxaction = $_REQUEST["ajxaction"];
if($ajaxaction == 'DETAILVIEW')
{
	$crmid = $_REQUEST['recordid'];
	$tablename = $_REQUEST['tableName'];
	$fieldname = $_REQUEST['fldName'];
	$fieldvalue = utf8RawUrlDecode($_REQUEST['fieldValue']); 
	if($crmid != '')
	{
		$modObj->retrieve_entity_info($crmid, $currentModule);
		$modObj->column_fields[$fieldname] = $fieldvalue;
		$modObj->id = $crmid;
		$modObj->mode = 'edit';
		//Registro de historico
		if ($modObj->column_fields['paid'] == "1"){
			$SQL = "SELECT paid,update_log FROM vtiger_cobropago WHERE cobropagoid=?";
			$result = $adb->pquery($SQL,array($modObj->id));
			$old_paid = $adb->query_result($result,0,'paid');
			if ($old_paid == "0"){
				$update_log = $adb->query_result($result,0,'update_log');
				$update_log .= getTranslatedString('Payment Paid',$currenModule).$current_user->user_name.getTranslatedString('PaidOn',$currenModule).date("l dS F Y h:i:s A").'--//--';
				$SQL_UPD = "UPDATE vtiger_cobropago SET update_log=? WHERE cobropagoid=?";
				$adb->pquery($SQL_UPD,array($update_log,$modObj->id));
			}
		}
		$modObj->save($currentModule);
		if($modObj->id != '')
		{
			echo ':#:SUCCESS';
		}else
		{
			echo ':#:FAILURE';
		}   
	}else
	{
		echo ':#:FAILURE';
	}
} elseif($ajaxaction == "LOADRELATEDLIST" || $ajaxaction == "DISABLEMODULE"){
	require_once 'include/ListView/RelatedListViewContents.php';
}
?>