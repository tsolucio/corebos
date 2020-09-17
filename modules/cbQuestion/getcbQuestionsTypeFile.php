<?php
/*********************************************************************************
** The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ********************************************************************************/
require_once 'modules/cbQuestion/cbQuestion.php';
require_once 'include/logging.php';
require_once 'include/database/PearDatabase.php';

global $current_user,$adb;
$result = $adb->pquery(
	'SELECT cbquestionid, qname
		from vtiger_cbquestion
		INNER JOIN vtiger_crmentity on vtiger_crmentity.crmid=vtiger_cbquestion.cbquestionid
		WHERE vtiger_crmentity.deleted = 0 AND qtype = ?',
	array('File')
);
$num_rows = $adb->num_rows($result);
$question_name_arr = array();
for ($x=0; $x<$num_rows; $x++) {
	$questionId = $adb->query_result($result, $x, 'cbquestionid');
	$questionName = $adb->query_result($result, $x, 'qname');
	$questionInfo = array(
		'questionid' => $questionId,
		'qname' => $questionName
	);
	array_push($question_name_arr, $questionInfo);
}
$response['result'] = $question_name_arr;
echo json_encode($response);
?>
