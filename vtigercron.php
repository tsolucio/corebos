<?php
/*+*******************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ********************************************************************************/

/**
 * Start the cron services configured.
 */
chdir(__DIR__);
include_once 'vtlib/Vtiger/Cron.php';
require_once 'config.inc.php';
if(PHP_SAPI === "cli" || PHP_SAPI === "cgi-fcgi" || (isset($_SESSION["authenticated_user_id"]) && isset($_SESSION["app_unique_key"]) && $_SESSION["app_unique_key"] == $application_unique_key)){

$cronTasks = false;
if (isset($_REQUEST['service']) or ($argc==2 and !empty($argv[1]))) {
	// Run specific service
	$srv = empty($argv[1]) ? $_REQUEST['service'] : $argv[1];
	$srvcron = Vtiger_Cron::getInstance(vtlib_purify($srv));
	if ($srvcron !== false) {
		$cronTasks = array($srvcron);
	} else {
		echo "** Service $srv not found **";
		die();
	}
}
else {
	// Run all service
	$cronTasks = Vtiger_Cron::listAllActiveInstances();
}

foreach ($cronTasks as $cronTask) {
	try {
		$cronTask->setBulkMode(true);

		// Not ready to run yet?
		if (!$cronTask->isRunnable()) {
			echo sprintf("[INFO]: %s - not ready to run as the time to run again is not completed\n", $cronTask->getName());
			continue;
}

		// Timeout could happen if intermediate cron-tasks fails
		// and affect the next task. Which need to be handled in this cycle.
		if ($cronTask->hadTimedout()) {
			echo sprintf("[INFO]: %s - cron task had timedout as it is not completed last time it run- restarting\n", $cronTask->getName());
}

		// Mark the status - running
		$cronTask->markRunning();
		
		checkFileAccess($cronTask->getHandlerFile());
		require_once $cronTask->getHandlerFile();
		
		// Mark the status - finished
		$cronTask->markFinished();
		
	} catch (Exception $e) {
		echo sprintf("[ERROR]: %s - cron task execution throwed exception.\n", $cronTask->getName());
		echo $e->getMessage();
		echo "\n";
		//Send email with error.
		$mailto = GlobalVariable::getVariable('Debug_Send_VtigerCron_Error','');
		if ($mailto != '') {
			require_once('modules/Emails/mail.php');
			require_once('modules/Emails/Emails.php');
			global $HELPDESK_SUPPORT_EMAIL_ID,$HELPDESK_SUPPORT_NAME;
			
			$from_name = $HELPDESK_SUPPORT_NAME;
			$form_mail = $HELPDESK_SUPPORT_EMAIL_ID;
			$mailsubject = "[ERROR]: ".$cronTask->getName()." - cron task execution throwed exception.";
			$mailcontent = '<pre>'.$e.'</pre>';
			
			send_mail('Emails',$mailto,$from_name,$form_mail,$mailsubject,$mailcontent);
		}
	}
}
}

else{
	echo("Access denied!");
}

?>
