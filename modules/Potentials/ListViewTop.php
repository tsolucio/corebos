<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/

/**Function to get the top 5 Potentials order by Amount in Descending Order
 *return array $values - array with the title, header and entries like
 *  Array('Title'=>$title,'Header'=>$listview_header,'Entries'=>$listview_entries)
 *  where listview_header and listview_entries are arrays of header and entity values which are returned from function getListViewHeader and getListViewEntries
*/
function getTopPotentials($maxval, $calCnt) {
	$log = LoggerManager::getLogger('top opportunity_list');
	$log->debug('Entering getTopPotentials() method ...');
	require_once 'data/Tracker.php';
	require_once 'modules/Potentials/Potentials.php';
	require_once 'include/logging.php';
	require_once 'include/ListView/ListView.php';

	global $adb, $current_language, $current_user;
	$current_module_strings = return_module_language($current_language, 'Potentials');

	$title=array();
	$title[]='myTopOpenPotentials.gif';
	$title[]=$current_module_strings['LBL_TOP_OPPORTUNITIES'];
	$title[]='home_mypot';
	$where = "AND vtiger_potential.potentialid > 0 AND vtiger_potential.sales_stage not in ('Closed Won','Closed Lost','".$current_module_strings['Closed Won']."','".
			$current_module_strings['Closed Lost']."') AND vtiger_crmentity.smownerid='".$current_user->id."' AND vtiger_potential.amount > 0";
	$header=array();
	$header[]=$current_module_strings['LBL_LIST_OPPORTUNITY_NAME'];
	//$header[]=$current_module_strings['LBL_LIST_ACCOUNT_NAME'];
	$currencyid=fetchCurrency($current_user->id);
	$rate_symbol = getCurrencySymbolandCRate($currencyid);
	$curr_symbol = $rate_symbol['symbol'];
	$header[]=$current_module_strings['LBL_LIST_AMOUNT'].'('.$curr_symbol.')';
	$list_query = 'SELECT vtiger_crmentity.crmid, vtiger_potential.potentialname,
		vtiger_potential.amount, potentialid
		FROM vtiger_potential
		IGNORE INDEX(PRIMARY) INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_potential.potentialid';
	$list_query .= getNonAdminAccessControlQuery('Potentials', $current_user);
	$list_query .= 'WHERE vtiger_crmentity.deleted = 0 '.$where;
	$list_query .=' ORDER BY amount DESC';

	$list_query .=' LIMIT ' . $adb->sql_escape_string($maxval);

	if ($calCnt == 'calculateCnt') {
		$list_result_rows = $adb->query(mkCountQuery($list_query));
		return $adb->query_result($list_result_rows, 0, 'count');
	}

	$list_result = $adb->query($list_query);

	$open_potentials_list = array();
	$noofrows = $adb->num_rows($list_result);

	$entries=array();
	if ($noofrows) {
		for ($i=0; $i<$noofrows; $i++) {
			$open_potentials_list[] = array(
				'name' => $adb->query_result($list_result, $i, 'potentialname'),
				'id' => $adb->query_result($list_result, $i, 'potentialid'),
				'amount' => $adb->query_result($list_result, $i, 'amount'),
			);
			$potentialid=$adb->query_result($list_result, $i, 'potentialid');
			$potentialname = $adb->query_result($list_result, $i, 'potentialname');
			$Top_Potential = (strlen($potentialname) > 20) ? (substr($potentialname, 0, 20).'...') : $potentialname;
			$value=array();
			$value[]='<a href="index.php?action=DetailView&module=Potentials&record='.$potentialid.'">'.$Top_Potential.'</a>';

			$value[] = CurrencyField::convertToUserFormat($adb->query_result($list_result, $i, 'amount'));
			$entries[$potentialid]=$value;
		}
	}

	$advft_criteria_groups = array('1' => array('groupcondition' => null));
	$advft_criteria = array(
		array (
			'groupid' => 1,
			'columnname' => 'vtiger_potential:sales_stage:sales_stage:Potentials_Sales_Stage:V',
			'comparator' => 'k',
			'value' => 'closed',
			'columncondition' => 'and'
		),
		array (
			'groupid' => 1,
			'columnname' => 'vtiger_potential:amount:amount:Potentials_Amount:N',
			'comparator' => 'g',
			'value' => '0',
			'columncondition' => 'and'
		),
		array (
			'groupid' => 1,
			'columnname' => 'vtiger_crmentity:smownerid:assigned_user_id:Leads_Assigned_To:V',
			'comparator' => 'e',
			'value' => getFullNameFromArray('Users', $current_user->column_fields),
			'columncondition' => null
		)
	);
	$search_qry = '&advft_criteria='.json_encode($advft_criteria).'&advft_criteria_groups='.json_encode($advft_criteria_groups).'&searchtype=advance&query=true';

	$values=array('ModuleName'=>'Potentials', 'Title'=>$title, 'Header'=>$header, 'Entries'=>$entries, 'search_qry'=>$search_qry);

	$log->debug('Exiting getTopPotentials method ...');
	if ((count($open_potentials_list) == 0 ) || (count($open_potentials_list)>0)) {
		return $values;
	}
}
?>
