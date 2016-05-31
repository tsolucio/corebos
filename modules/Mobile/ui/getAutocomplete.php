<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 * Modified by crm-now GmbH, www.crm-now.com
 ************************************************************************************/
require_once('include/utils/utils.php');
include_once dirname(__FILE__) . '/../api/ws/Controller.php';
include_once dirname(__FILE__) . '/../api/ws/Utils.php';

class Mobile_UI_GetAutocomplete extends Mobile_WS_Controller{
	function process(Mobile_API_Request $request) {
		$response = new Mobile_API_Response();
		global $adb;
		global $current_language;
		$current_language = Mobile_API_Session::get('language');
		
		//never trust an entry
		$sSearch = vtlib_purify($request->get('term'));
		$sResult = "";
		$arrayName = array();
		$ModuleArray = explode(',', vtlib_purify($request->get('relmodule')));
		$ModuleLabels = array();

		foreach($ModuleArray as $Module){
			$translatedModule = getTranslatedString($Module, 'Mobile');
			$arrayName[$translatedModule] = array();
			$moduleWSID = Mobile_WS_Utils::getEntityModuleWSId($Module);

			// get related module fields
			
			$query = "SELECT tablename,fieldname,entityidfield FROM vtiger_entityname WHERE modulename = ?";
			$result = $adb->pquery($query, array($Module));
			if (!$result OR $adb->num_rows($result)==0) {
				$response->setError(1407, 'Error: Could not fetch entity info');
				return $response;
			}
			$tablename = $adb->query_result($result,0,'tablename');
			$fieldname = $adb->query_result($result,0,'fieldname');
			$entityidfield = $adb->query_result($result,0,'entityidfield');
			
			$fieldname = explode(',', $fieldname);
			$fieldname = $fieldname[0];
			//START DATABASE ACCOUNT SEARCH
			$minhaquery = "SELECT ".$fieldname.",".$entityidfield." FROM ".$tablename." 
							INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = ".$tablename.".".$entityidfield."
							WHERE deleted = 0 AND ".$fieldname." LIKE ? ORDER BY ".$fieldname."";
			$params = $sSearch ."%";
			$result = $adb->pquery($minhaquery, array($params));
			if (!$result) {
				$response->setError(1408, 'Error: Could not fetch entity data');
				return $response;
			}
			for($i=0;$i<$adb->num_rows($result);$i++){
				$arrayName[$translatedModule][] = Array($moduleWSID.'x'.$adb->query_result($result,$i,$entityidfield),decode_html($adb->query_result($result,$i,$fieldname)));
			}
		}
		$sResult = $arrayName;
		$sResult = json_encode($sResult);
		

		
		$response->setResult($sResult);
		return $response;
	}
}
?>