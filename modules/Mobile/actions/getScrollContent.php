<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is crm-now.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 * Modified by crm-now GmbH, www.crm-now.com
 ************************************************************************************/
include_once __DIR__ . '/../api/ws/getScrollContent.php';

//get new content for scrolling
class crmtogo_UI_GetScrollRecords extends crmtogo_WS_getScrollContent {

	public function process(crmtogo_API_Request $request) {
		require_once 'include/utils/utils.php';
		global $adb;
		//get scroll parameter
		$wsResponse = parent::process($request);

		if (is_numeric($request->get('offset'))) {
			$offset = $request->get('offset');
		} else {
			//do nothing
			echo '';
			return $wsResponse;
		}
		if (is_numeric($request->get('number'))) {
			$postnumbers = $request->get('number');
		} else {
			//do nothing
			echo '';
			return $wsResponse;
		}
		$wsResponseResult = $wsResponse->getResult();

		$module = $wsResponseResult['module'];
		$records = $wsResponseResult['records'];
		$noofrows = $adb->num_rows($records);
		$tabid = getTabid($module);

		$ws_entity1=$adb->pquery('select fieldname,entityidfield from vtiger_entityname where tabid =?', array($tabid));
		$fieldname= $adb->query_result($ws_entity1, 0, 'fieldname');
		$entityidfield= $adb->query_result($ws_entity1, 0, 'entityidfield');
		$fieldcontent = explode(',', $fieldname);

		$ws_entity=$adb->pquery('select id from vtiger_ws_entity where ismodule=1 and name =?', array($wsResponseResult['module']));
		$ws_entity2= $adb->query_result($ws_entity, 0, 'id');

		$output = '';
		for ($i=0; $i<$noofrows; $i++) {
			$firstname = $adb->query_result($records, $i, $fieldcontent[0]);
			$lastname = (isset($fieldcontent[1]) ? $adb->query_result($records, $i, $fieldcontent[1]) : '');

			if ($module == 'cbCalendar') {
				//for calendar display date and time
				$firstname = $firstname.' |';
				$date_start = $adb->query_result($records, $i, 'dtstart');
				$date = new DateTimeField($date_start);
				$lastname = $date->getDisplayDateTimeValue();
			}
			$id = $ws_entity2.'x'.$adb->query_result($records, $i, $entityidfield);

			$output .= '<li><a class="ui-btn ui-btn-icon-right ui-icon-carat-r" target="_self" href="?_operation=fetchRecord&record='.$id.'" target="_self">'
				.$firstname.'&nbsp;'.$lastname.'</a></li>';
		}
		$response = new crmtogo_API_Response();
		$response->setResult($output);
		return $response;
	}
}
?>
