<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/

/** Function to get the 5 New Leads
 *return array $values - array with the title, header and entries like
 *  Array('Title'=>$title,'Header'=>$listview_header,'Entries'=>$listview_entries)
 *  where listview_header and listview_entries are arrays of header and entity values which are returned from function getListViewHeader and getListViewEntries
*/
function getNewLeads($maxval, $calCnt) {
	global $log, $adb, $current_language, $current_user;
	$log->debug('> getNewLeads');
	require_once 'data/Tracker.php';
	require_once 'include/utils/utils.php';

	$current_module_strings = return_module_language($current_language, 'Leads');

	if (empty($_REQUEST['lead_view'])) {
		$query = 'select lead_view from vtiger_users where id =?';
		$result=$adb->pquery($query, array($current_user->id));
		$lead_view=$adb->query_result($result, 0, 'lead_view');
	} else {
		$lead_view=$_REQUEST['lead_view'];
	}

	$today = date('Y-m-d', time());

	if ($lead_view == 'Last 2 Days') {
		$dbStartDateTime = new DateTimeField(date('Y-m-d H:i:s', strtotime('-2  days')));
	} elseif ($lead_view == 'Last Week') {
		$dbStartDateTime = new DateTimeField(date('Y-m-d H:i:s', strtotime('-1 week')));
	} else {
		$dbStartDateTime = new DateTimeField(date('Y-m-d H:i:s', strtotime('$today')));
	}
	$userStartDate = $dbStartDateTime->getDisplayDate();
	$userStartDateTime = new DateTimeField($userStartDate.' 00:00:00');
	$startDateTime = $userStartDateTime->getDBInsertDateTimeValue();

	$val_conv = ((isset($_COOKIE['LeadConv']) && $_COOKIE['LeadConv'] == 'true') ? 1 : 0);
	$list_query = 'select vtiger_leaddetails.firstname, vtiger_leaddetails.lastname, vtiger_leaddetails.leadid, vtiger_leaddetails.company
		from vtiger_leaddetails inner join vtiger_crmentity on vtiger_leaddetails.leadid = vtiger_crmentity.crmid
		where vtiger_crmentity.deleted =0 AND vtiger_leaddetails.converted = '.$val_conv.' AND vtiger_leaddetails.leadid > 0 AND
		vtiger_leaddetails.leadstatus not in ("Lost Lead", "Junk Lead","'.$current_module_strings['Lost Lead'].'","'.$current_module_strings['Junk Lead'].'")
		AND vtiger_crmentity.createdtime >=? AND vtiger_crmentity.smownerid = ?';

	$list_query .= " LIMIT 0," . $adb->sql_escape_string($maxval);

	if ($calCnt == 'calculateCnt') {
		$list_result_rows = $adb->pquery(mkCountQuery($list_query), array($startDateTime, $current_user->id));
		return $adb->query_result($list_result_rows, 0, 'count');
	}

	$list_result = $adb->pquery($list_query, array($startDateTime, $current_user->id));
	$noofrows = $adb->num_rows($list_result);

	$open_lead_list =array();
	if ($noofrows > 0) {
		for ($i=0; $i<$noofrows && $i<$maxval; $i++) {
			$open_lead_list[] = array(
				'leadname' => $adb->query_result($list_result, $i, 'firstname').' '.$adb->query_result($list_result, $i, 'lastname'),
				'company' => $adb->query_result($list_result, $i, 'company'),
				'id' => $adb->query_result($list_result, $i, 'leadid'),
			);
		}
	}

	$header=array();
	$header[] =$current_module_strings['LBL_LIST_LEAD_NAME'];
	$header[] =$current_module_strings['Company'];

	$entries=array();
	foreach ($open_lead_list as $lead) {
		$value=array();
		$lead_fields = array(
			'LEAD_NAME' => $lead['leadname'],
			'COMPANY' => $lead['company'],
			'LEAD_ID' => $lead['id'],
		);

		$Top_Leads = (strlen($lead['leadname']) > 20) ? (substr($lead['leadname'], 0, 20).'...') : $lead['leadname'];
		$value[]= '<a href="index.php?action=DetailView&module=Leads&record='.$lead_fields['LEAD_ID'].'">'.$Top_Leads.'</a>';
		$value[]=$lead_fields['COMPANY'];

		$entries[$lead_fields['LEAD_ID']]=$value;
	}

	$advft_criteria_groups = array('1' => array('groupcondition' => null));
	$advft_criteria = array(
		array(
			'groupid' => 1,
			'columnname' => 'vtiger_leaddetails:leadstatus:leadstatus:Leads_Lead_Status:V',
			'comparator' => 'n',
			'value' => 'Lost Lead',
			'columncondition' => 'and'
		),
		array(
			'groupid' => 1,
			'columnname' => 'vtiger_leaddetails:leadstatus:leadstatus:Leads_Lead_Status:V',
			'comparator' => 'n',
			'value' => 'Junk Lead',
			'columncondition' => 'and'
		),
		array(
			'groupid' => 1,
			'columnname' => 'vtiger_crmentity:smownerid:assigned_user_id:Leads_Assigned_To:V',
			'comparator' => 'e',
			'value' => getFullNameFromArray('Users', $current_user->column_fields),
			'columncondition' => 'and'
		),
		array(
			'groupid' => 1,
			'columnname' => 'vtiger_crmentity:createdtime:createdtime:Leads_Created_Time:DT',
			'comparator' => 'h',
			'value' => $userStartDate.' 00:00:00',
			'columncondition' => null
		)
	);
	$search_qry = '&advft_criteria='.json_encode($advft_criteria).'&advft_criteria_groups='.json_encode($advft_criteria_groups).'&searchtype=advance&query=true';

	$values=array('ModuleName'=>'Leads','Header'=>$header,'Entries'=>$entries,'search_qry'=>$search_qry);
	$log->debug('< getNewLeads');
	if ((count($entries) == 0 ) || (count($entries)>0)) {
		return $values;
	}
}
?>
