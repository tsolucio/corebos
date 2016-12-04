<?php
// Turn on debugging level
$Vtiger_Utils_Log = true;

require_once 'include/utils/utils.php';
include_once('vtlib/Vtiger/Module.php');
require_once('modules/CustomView/CustomView.php');
require_once('modules/Reports/Reports.php');
include_once('modules/Reports/ReportRun.php');
$current_user = new Users();
$current_user->retrieveCurrentUserInfoFromFile(Users::getActiveAdminId());
if(isset($_SESSION['authenticated_user_language']) && $_SESSION['authenticated_user_language'] != '') {
	$current_language = $_SESSION['authenticated_user_language'];
} else {
	if(!empty($current_user->language)) {
		$current_language = $current_user->language;
	} else {
		$current_language = $default_language;
	}
}
$app_strings = return_application_language($current_language);

$reportid = 29;
$currentModule = 'Reports';
$ogReport = new Reports($reportid);
$oReportRun = new ReportRun($reportid);
$oReportRun->page = $_REQUEST['page'];
$__oReportRunReturnValue = $oReportRun->GenerateReport("JSONPAGED", false);

echo $__oReportRunReturnValue;
