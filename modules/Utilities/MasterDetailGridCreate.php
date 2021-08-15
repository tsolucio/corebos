<?php
$currentModule = $module = 'Contacts';
$tabid = getTabid($module);
$QCFIELDS_QUERY = $adb->convert2sql(
	"select *
	from vtiger_field
	where tabid=? and vtiger_field.presence in (0,2) and displaytype<2 order by quickcreatesequence",
	array($tabid)
);
include 'include/quickcreate.php';
