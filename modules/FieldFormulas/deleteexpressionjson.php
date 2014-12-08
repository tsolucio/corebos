<?php
/*+*******************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ******************************************************************************/
require_once("include/Zend/Json.php");
require 'include.inc';
function vtDeleteExpressionJson($adb, $request){
	$moduleName = $request['modulename'];
	$fieldName = $request['fieldname'];
	$mem = new VTModuleExpressionsManager($adb);
	$me = $mem->retrieve($moduleName);
	$me->remove($fieldName);
	$mem->save($me);
	echo Zend_Json::encode(array('status'=>'success'));
}

vtDeleteExpressionJson($adb, $_GET);
?>