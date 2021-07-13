<?php
/*********************************************************************************
** The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ********************************************************************************/
require_once 'config.inc.php';
require_once 'include/logging.php';
require_once 'include/database/PearDatabase.php';
require_once 'modules/Accounts/Accounts.php';
require_once 'modules/Contacts/Contacts.php';
require_once 'modules/Leads/Leads.php';
require_once 'modules/Contacts/Contacts.php';
require_once 'modules/Emails/Emails.php';
require_once 'modules/Documents/Documents.php';
require_once 'modules/Potentials/Potentials.php';
require_once 'modules/Users/Users.php';
require_once 'modules/Products/Products.php';
require_once 'modules/HelpDesk/HelpDesk.php';
require_once 'modules/Vendors/Vendors.php';
require_once 'include/utils/UserInfoUtil.php';
require_once 'include/utils/ExportUtils.php';
require_once 'modules/CustomView/CustomView.php';
require_once 'modules/PickList/PickListUtils.php';
require_once 'modules/Invoice/Invoice.php';
require_once 'modules/Quotes/Quotes.php';
require_once 'modules/PurchaseOrder/PurchaseOrder.php';
require_once 'modules/SalesOrder/SalesOrder.php';
require_once 'include/utils/Session.php';
require_once 'modules/com_vtiger_workflow/VTWorkflowManager.inc';
coreBOS_Session::init();

// Set the current language and the language strings, if not already set.
setCurrentLanguage();

global $app_strings;

$current_user = new Users();

if (isset($_SESSION['authenticated_user_id'])) {
	$result = $current_user->retrieveCurrentUserInfoFromFile($_SESSION['authenticated_user_id']);
	if ($result == null) {
		coreBOS_Session::destroy();
		header('Location: index.php?action=Login&module=Users');
		exit;
	}
}

$allow_exports = GlobalVariable::getVariable('Application_Allow_Exports', 'all');
//Security Check
if (isPermitted($_REQUEST['module'], 'Export') == 'no') {
	$allow_exports='none';
}

if ($allow_exports=='none' || ($allow_exports=='admin' && !is_admin($current_user))) {
	?>
	<script type='text/javascript'>
		alert("<?php echo $app_strings['NOT_PERMITTED_TO_EXPORT']?>");
		window.location="index.php?module=<?php echo vtlib_purify($_REQUEST['module']) ?>&action=index";
	</script>
	<?php exit;
}

// Function to obtain the visible columns from filter
function obtainVisibleColumnNames(&$l, $k) {
	//vtiger_contactaddress:mailingcountry:mailingcountry:Contacts_Mailing_Country:V
	$filter=explode(':', $l);
	$module_columnname=explode('_', $filter[3]);
	$l='';
	for ($i = 1; $i<count($module_columnname); $i++) {
		if ($i != 1) {
			$l .= ' '.$module_columnname[$i];
		} else {
			$l .=$module_columnname[$i];
		}
	}
}

/**
 * This function exports all the data for a given module
 * Param $type - module name
 * Return type text
 */
function export($type, $format = 'CSV') {
	global $log, $adb;
	$log->debug('> export '.$type);

	$focus = 0;

	if ($type != '') {
		// vtlib customization: Hook to dynamically include required module file.
		// Refer to the logic in setting $currentModule in index.php
		$focus = CRMEntity::getInstance($type);
	}
	$log = LoggerManager::getLogger('export_'.$type);

	$oCustomView = new CustomView($type);
	$viewid = $oCustomView->getViewId($type);
	$sorder = $focus->getSortOrder();
	$order_by = $focus->getOrderBy();

	$search_type = vtlib_purify($_REQUEST['search_type']);
	$export_data = vtlib_purify($_REQUEST['export_data']);
	$filtercolumns = (isset($_REQUEST['visiblecolumns']) && $_REQUEST['visiblecolumns']=='on');

	if (isset($_SESSION['export_where']) && $_SESSION['export_where']!='' && $search_type == 'includesearch') {
		$where =$_SESSION['export_where'];
	} else {
		$where = '';
	}

	$query = $focus->create_export_query($where);
	if ($search_type != 'includesearch') {
		$stdfiltersql = $oCustomView->getCVStdFilterSQL($viewid);
		$advfiltersql = $oCustomView->getCVAdvFilterSQL($viewid);
		if (isset($stdfiltersql) && $stdfiltersql != '') {
			$query .= ' and '.$stdfiltersql;
		}
		if (isset($advfiltersql) && $advfiltersql != '') {
			$query .= ' and '.$advfiltersql;
		}
	}
	$params = array();

	if (($search_type == 'withoutsearch' || $search_type == 'includesearch') && $export_data == 'selecteddata') {
		$idstring = explode(';', vtlib_purify($_REQUEST['idstring']));
		if ($type == 'Accounts' && count($idstring) > 0) {
			$query .= ' and vtiger_account.accountid in ('. generateQuestionMarks($idstring) .')';
			$params[] = $idstring;
		} elseif ($type == 'Contacts' && count($idstring) > 0) {
			$query .= ' and vtiger_contactdetails.contactid in ('. generateQuestionMarks($idstring) .')';
			$params[] = $idstring;
		} elseif ($type == 'Potentials' && count($idstring) > 0) {
			$query .= ' and vtiger_potential.potentialid in ('. generateQuestionMarks($idstring) .')';
			$params[] = $idstring;
		} elseif ($type == 'Leads' && count($idstring) > 0) {
			$query .= ' and vtiger_leaddetails.leadid in ('. generateQuestionMarks($idstring) .')';
			$params[] = $idstring;
		} elseif ($type == 'Products' && count($idstring) > 0) {
			$query .= ' and vtiger_products.productid in ('. generateQuestionMarks($idstring) .')';
			$params[] = $idstring;
		} elseif ($type == 'Documents' && count($idstring) > 0) {
			$query .= ' and vtiger_notes.notesid in ('. generateQuestionMarks($idstring) .')';
			$params[] = $idstring;
		} elseif ($type == 'HelpDesk' && count($idstring) > 0) {
			$query .= ' and vtiger_troubletickets.ticketid in ('. generateQuestionMarks($idstring) .')';
			$params[] = $idstring;
		} elseif ($type == 'Vendors' && count($idstring) > 0) {
			$query .= ' and vtiger_vendor.vendorid in ('. generateQuestionMarks($idstring) .')';
			$params[] = $idstring;
		} elseif ($type == 'Invoice' && count($idstring) > 0) {
			$query .= ' and vtiger_invoice.invoiceid in ('. generateQuestionMarks($idstring) .')';
			$params[] = $idstring;
		} elseif ($type == 'Quotes' && count($idstring) > 0) {
			$query .= ' and vtiger_quotes.quoteid in ('. generateQuestionMarks($idstring) .')';
			$params[] = $idstring;
		} elseif ($type == 'SalesOrder' && count($idstring) > 0) {
			$query .= ' and vtiger_salesorder.salesorderid in ('. generateQuestionMarks($idstring) .')';
			$params[] = $idstring;
		} elseif ($type == 'PurchaseOrder' && count($idstring) > 0) {
			$query .= ' and vtiger_purchaseorder.purchaseorderid in ('. generateQuestionMarks($idstring) .')';
			$params[] = $idstring;
		} elseif (count($idstring) > 0) {
			// vtlib customization: Hook to make the export feature available for custom modules.
			$query .= " and $focus->table_name.$focus->table_index in (" . generateQuestionMarks($idstring) . ')';
			$params[] = $idstring;
			// END
		}
	}

	if (isset($order_by) && $order_by != '') {
		if ($order_by == 'smownerid') {
			$query .= ' ORDER BY vtiger_users.user_name '.$sorder;
		} elseif ($order_by == 'lastname' && $type == 'Documents') {
			$query .= ' ORDER BY vtiger_contactdetails.lastname '. $sorder;
		} elseif ($order_by == 'crmid' && $type == 'HelpDesk') {
			$query .= ' ORDER BY vtiger_troubletickets.ticketid '. $sorder;
		} else {
			$tablename = getTableNameForField($type, $order_by);
			$tablename = (($tablename != '')?($tablename.'.'):'');
			$query .= ' ORDER BY '.$tablename.$order_by.' '.$sorder;
		}
	}

	if ($export_data == 'currentpage') {
		$list_max_entries_per_page = GlobalVariable::getVariable('Application_ListView_PageSize', 20, $type);
		$current_page = ListViewSession::getCurrentPage($type, $viewid);
		$limit_start_rec = ($current_page - 1) * $list_max_entries_per_page;
		if ($limit_start_rec < 0) {
			$limit_start_rec = 0;
		}
		$query .= ' LIMIT '.$limit_start_rec.','.$list_max_entries_per_page;
	}

	$result = $adb->pquery($query, $params, true, "Error exporting $type: <BR>$query");
	$fields_array = $adb->getFieldsArray($result);
	$fields_array = array_diff($fields_array, array('user_name'));
	if ($filtercolumns) {
		$visiblecolumns_array = $oCustomView->getColumnsListByCvid($viewid);
		array_walk($visiblecolumns_array, 'obtainVisibleColumnNames');
		$fields_array = array_filter($visiblecolumns_array, function ($efield) use ($fields_array) {
			return in_array($efield, $fields_array);
		});
		$fields_array[] = 'cbuuid';
	}

	$columnsToExport = array_map(
		function ($field) {
			return strtolower($field);
		},
		$fields_array
	);

	$processor = new ExportUtils($type, $fields_array);

	$translated_fields_array = array_map(
		function ($field) use ($type) {
			return getTranslatedString($field, $type);
		},
		$fields_array
	);

	if ($format == 'CSV') {
		$CSV_Separator = GlobalVariable::getVariable('Export_Field_Separator_Symbol', ',', $type);
		$header = '"'.implode('"'.$CSV_Separator.'"', $translated_fields_array)."\"\r\n";
		/** Output header information */
		echo $header;
		dumpRowsToCSV($type, $processor, $CSV_Separator, $columnsToExport, $result, $focus);
	}
	if ($format == 'XLS') {
		return dumpRowsToXLS($type, $processor, $columnsToExport, $translated_fields_array, $result);
	}

	$log->debug('< export');
	return true;
}

if (isset($_REQUEST['exportfile']) && $_REQUEST['exportfile']=='exportexcelfile') {
	global $root_directory, $cache_dir;
	$fname = tempnam($root_directory.$cache_dir, 'excel.xls');
	$xlsobject = export(vtlib_purify($_REQUEST['module']), 'XLS');
	$xlsobject->save($fname);
	$moduleName = getTranslatedString($_REQUEST['module'], $_REQUEST['module']);
	header('Content-Type: application/x-msexcel');
	header('Content-Length: '.@filesize($fname));
	header('Content-disposition: attachment; filename="'.$moduleName.'.xls"');
	$fh=fopen($fname, 'rb');
	fpassthru($fh);
} else {
	/** Send the output header and invoke function for contents output */
	$moduleName = vtlib_purify($_REQUEST['module']);
	$moduleName = getTranslatedString($moduleName, $moduleName);
	$moduleName = str_replace(' ', '_', $moduleName);
	header("Content-Disposition:attachment;filename=$moduleName.csv");
	header('Content-Type:text/csv;charset=UTF-8');
	header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
	header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
	header('Cache-Control: post-check=0, pre-check=0', false);
	export(vtlib_purify($_REQUEST['module']), 'CSV');
}
exit;

?>
