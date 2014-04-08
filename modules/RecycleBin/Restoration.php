<?php
/*+********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ********************************************************************************/

require_once('RecycleBinUtils.php');

$idlist=vtlib_purify($_REQUEST['idlist']);
$excludedRecords=vtlib_purify($_REQUEST['excludedRecords']);
$selected_module = vtlib_purify($_REQUEST['selectmodule']);
$idlists = getSelectedRecordIds($_REQUEST,$selected_module,$idlist,$excludedRecords);

require_once('data/CRMEntity.php');
$focus = CRMEntity::getInstance($selected_module);

for($i=0;$i<count($idlists);$i++) {
	if(!empty($idlists[$i])) {
		$focus->restore($mod_name, $idlists[$i]);
	}
}

$parenttab = getParentTab();

header("Location: index.php?module=RecycleBin&action=RecycleBinAjax&file=index&parenttab=$parenttab&mode=ajax&selected_module=$selected_module");
?>