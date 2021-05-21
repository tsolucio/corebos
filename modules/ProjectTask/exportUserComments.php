<?php
global $adb, $current_user, $root_directory, $tmp_dir, $currentModule;
if ($_REQUEST['recordid']) {
	$sql = "select * from vtiger_projecttask inner join Comments on vtiger_projecttask.projecttaskid = Comments.related_to left join vtiger_modcomments on Comments.modcommentsid = vtiger_modcomments.modcommentsid left join 
    vtiger_crmentity on vtiger_crmentity.crmid = vtiger_modcomments.modcommentsid left join vtiger_users on vtiger_crmentity.smownerid = vtiger_users.id where vtiger_projecttask.projecttaskid = ? and vtiger_users.ename = ?";
	$result = $adb->pquery($sql, array($_REQUEST['recordid'], $current_user->ename));
	$mapname = 'Comments_Export_Columns';
	$cbMapid = GlobalVariable::getVariable('BusinessMapping_'.$mapname, cbMap::getMapIdByName($mapname));
	if ($cbMapid) {
		$xlsrows = array();
		$new_arr = array();
		$cbMap = cbMap::getMapByID($cbMapid);
		$arr = $cbMap->FieldSetMapping()->getFieldSetModule($currentModule);
		while ($row = $adb->fetchByAssoc($result)) {
			for ($i=0; $i<sizeof($arr); $i++) {
				if (isset($row[$arr[$i]['name']])) {
					$new_arr[$arr[$i]['name']] = $row[$arr[$i]['name']];
				}
			}
			array_push($xlsrows, $new_arr);
		}
	}
	$fname = tempnam($root_directory.$tmp_dir, 'comm.xls');
	$totalxclinfo = array();
	$fieldinfo = array();
	$fldname = $currentModule.' Comments';
	$workbook = exportExcelFileRows($xlsrows, $totalxclinfo, $fldname, $fieldinfo, $currentModule);
	$workbookWriter = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($workbook, 'Xls');
	$workbookWriter->save($fname);
	header('Content-Type: application/x-msexcel');
	header('Content-Length: '.@filesize($fname));
	header('Content-disposition: attachment; filename="Comments'.$_REQUEST['recordid'].'.xls"');
	$fh=fopen($fname, 'rb');
	fpassthru($fh);
}

?>