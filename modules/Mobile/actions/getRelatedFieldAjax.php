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
include_once dirname(__FILE__) . '/../views/models/SearchFilter.php';
include_once 'include/Webservices/Query.php';

class crmtogo_UI_getRelatedFieldAjax extends crmtogo_WS_Controller{

	function process(crmtogo_API_Request $request) {
		$db = PearDatabase::getInstance();
		$response = new crmtogo_API_Response();
		$searchvalue = vtlib_purify($request->get('searchvalue'));
		$modulename = vtlib_purify($request->get('modulename'));
		$parentselector = vtlib_purify($request->get('parentselector'));
		$parentid=  str_replace('_selector','',$parentselector);
		$parentid=  crmtogo_WS_Utils::fixReferenceIdByModule($modulename, $parentid);
		$current_user = $this->getActiveUser();

		// get module fields
		$query = "SELECT modulename,fieldname FROM vtiger_entityname WHERE entityidcolumn = ?";
		$result = $db->pquery($query, array($parentid));
		$modulename = $db->query_result($result,0,'modulename');
		$fieldname = $db->query_result($result,0,'fieldname');
		$config = crmtogo_WS_Controller::getUserConfigSettings();
		$limit = $config['NavigationLimit'];
		//START DATABASE SEARCH
		if ($modulename=='Contacts') {
			$searchqueryresult = vtws_query("SELECT firstname, lastname FROM Contacts WHERE lastname like '%".$searchvalue."%' OR firstname like  '%".$searchvalue."%' LIMIT ".$limit.";", $current_user);
			for($i=0;$i<count($searchqueryresult);$i++){
				$searchresult[] = Array($searchqueryresult[$i]['id'],decode_html($searchqueryresult[$i]['lastname']).', '.decode_html($searchqueryresult[$i]['firstname']));
			}
		}
		else {
			$searchqueryresult = vtws_query("SELECT ".$fieldname." FROM ".$modulename." WHERE ".$fieldname." like '%".$searchvalue."%' LIMIT ".$limit.";", $current_user);
			for($i=0;$i<count($searchqueryresult);$i++){
				$searchresult[] = Array($searchqueryresult[$i]['id'],decode_html($searchqueryresult[$i][$fieldname]));
			}
		}
		$sResult = $searchresult;
		$sResult = json_encode($sResult);
		$response->setResult($sResult);
		return $response;
	}
}
?>