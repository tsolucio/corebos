<?php
/*********************************************************************************
** The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ********************************************************************************/

/**	Function to get the list of tickets for the currently loggedin user */
function getMyTickets($maxval, $calCnt) {
	global $log, $current_user, $current_language, $adb;
	$log->debug('> getMyTickets');
	$current_module_strings = return_module_language($current_language, 'HelpDesk');

	$search_query = 'SELECT vtiger_troubletickets.*, vtiger_crmentity.*
		FROM vtiger_troubletickets
		INNER JOIN vtiger_crmentity on vtiger_crmentity.crmid = vtiger_troubletickets.ticketid
		INNER JOIN vtiger_users on vtiger_users.id = vtiger_crmentity.smownerid
		where vtiger_crmentity.smownerid = ? and vtiger_crmentity.deleted = 0 and '.
		"vtiger_troubletickets.ticketid > 0 and vtiger_troubletickets.status <> 'Closed' ".
		"AND vtiger_crmentity.setype='HelpDesk' ORDER BY createdtime DESC";

	$search_query .= ' LIMIT 0,' . $adb->sql_escape_string($maxval);

	if ($calCnt == 'calculateCnt') {
		$list_result_rows = $adb->pquery(mkCountQuery($search_query), array($current_user->id));
		return $adb->query_result($list_result_rows, 0, 'count');
	}

	$tktresult = $adb->pquery($search_query, array($current_user->id));
	if ($adb->num_rows($tktresult)) {
		$title=array();
		$title[]='myTickets.gif';
		$title[]=$current_module_strings['LBL_MY_TICKETS'];
		$title[]='home_mytkt';

		$header=array();
		$header[]=$current_module_strings['LBL_SUBJECT'];
		$header[]=$current_module_strings['Related To'];

		$noofrows = $adb->num_rows($tktresult);
		for ($i=0; $i<$adb->num_rows($tktresult); $i++) {
			$value=array();
			$ticketid = $adb->query_result($tktresult, $i, 'ticketid');
			$viewstatus = $adb->query_result($tktresult, $i, 'severity');
			if ($viewstatus == 'Critical') {
				$value[]= '<a style="color:red;" href="index.php?action=DetailView&module=HelpDesk&record='.substr($adb->query_result($tktresult, $i, 'ticketid'), 0, 20).
					'">'.$adb->query_result($tktresult, $i, "title").'</a>';
			} elseif ($viewstatus == 'Major') {
				$value[]= '<a style="color:goldenrod;" href="index.php?action=DetailView&module=HelpDesk&record='.
					substr($adb->query_result($tktresult, $i, 'ticketid'), 0, 20).'">'.$adb->query_result($tktresult, $i, 'title').'</a>';
			} else {
				$value[]= '<a href="index.php?action=DetailView&module=HelpDesk&record='.substr($adb->query_result($tktresult, $i, 'ticketid'), 0, 20).
					'">'.substr($adb->query_result($tktresult, $i, "title"), 0, 20).'</a>';
			}

			$parent_id = $adb->query_result($tktresult, $i, "parent_id");
			$parent_name = '';
			if ($parent_id != '' && $parent_id != null) {
				$parent_name = getParentLink($parent_id);
			}

			$value[]=$parent_name;
			$entries[$ticketid]=$value;
		}

		$advft_criteria_groups = array('1' => array('groupcondition' => null));
		$advft_criteria = array(
			array (
				'groupid' => 1,
				'columnname' => 'vtiger_troubletickets:status:ticketstatus:HelpDesk_Status:V',
				'comparator' => 'n',
				'value' => 'Closed',
				'columncondition' => 'and'
			),
			array (
				'groupid' => 1,
				'columnname' => 'vtiger_crmentity:smownerid:assigned_user_id:HelpDesk_Assigned_To:V',
				'comparator' => 'e',
				'value' => getFullNameFromArray('Users', $current_user->column_fields),
				'columncondition' => null
			)
		);
		$search_qry = '&advft_criteria='.json_encode($advft_criteria).'&advft_criteria_groups='.json_encode($advft_criteria_groups).'&searchtype=advance&query=true';

		$values=array('ModuleName'=>'HelpDesk','Title'=>$title,'Header'=>$header,'Entries'=>$entries,'search_qry'=>$search_qry);
		if (($noofrows == 0 ) || ($noofrows>0)) {
			$log->debug('< getMyTickets');
			return $values;
		}
	}
	$log->debug('< getMyTickets');
}

/**	Function to get the parent (Account or Contact) link
 *	@param int $parent_id -- parent id of the ticket (accountid or contactid)
 *	return string $parent_name -- return the parent name as a link
**/
function getParentLink($parent_id) {
	global $log, $adb;
	$log->debug('> getParentLink '.$parent_id);

	// Static caching
	static $__cache_listtickets_parentlink = array();
	if (isset($__cache_listtickets_parentlink[$parent_id])) {
		return $__cache_listtickets_parentlink[$parent_id];
	}

	$parent_module = getSalesEntityType($parent_id);
	$parent_name = '';
	if ($parent_module == 'Contacts') {
		$res = $adb->pquery('select firstname,lastname from vtiger_contactdetails where contactid=?', array($parent_id));
		$parentname = $adb->query_result($res, 0, 'firstname');
		$parentname .= ' '.$adb->query_result($res, 0, 'lastname');
		$parent_name = '<a href="index.php?action=DetailView&module='.$parent_module.'&record='.$parent_id.'">'.$parentname.'</a>';
	}
	if ($parent_module == 'Accounts') {
		$rsac = $adb->pquery('select accountname from vtiger_account where accountid=?', array($parent_id));
		$parentname = $adb->query_result($rsac, 0, 'accountname');
		$parent_name = '<a href="index.php?action=DetailView&module='.$parent_module.'&record='.$parent_id.'">'.$parentname.'</a>';
	}

	// Add to cache
	$__cache_listtickets_parentlink[$parent_id] = $parent_name;

	$log->debug('< getParentLink');
	return $parent_name;
}
?>
