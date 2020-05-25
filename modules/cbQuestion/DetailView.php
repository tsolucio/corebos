<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/
require_once 'Smarty_setup.php';

global $mod_strings, $app_strings, $currentModule, $current_user, $theme, $log;

$smarty = new vtigerCRM_Smarty();

require_once 'modules/Vtiger/DetailView.php';

if ($focus->column_fields['qtype']=='Mermaid') {
	$recordid = $focus->column_fields['record_id'];
	$propertyody = json_decode(html_entity_decode($focus->column_fields['typeprops']));
	if ($propertyody != null) {
		$smarty->assign('QSQL', 'graph '.$propertyody->graph."\n".$focus->column_fields['qcolumns']);
	} else {
		$smarty->assign('QSQL', 'graph '.$focus->column_fields['typeprops']."\n".$focus->column_fields['qcolumns']);
	}
} else {
	$smarty->assign('QSQL', cbQuestion::getSQL($record));
}

$smarty->display('DetailView.tpl');
if ($focus->column_fields['qtype']=='Mermaid') {
	echo '<script type="text/javascript" src="modules/cbQuestion/resources/mermaid.min.js"></script>';
}
?>