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
if (PHP_SAPI === 'cli' || PHP_SAPI === 'cgi-fcgi' || PHP_SAPI === 'apache2handler'
	|| (isset($_SESSION['authenticated_user_id']) && isset($_SESSION['app_unique_key']) && $_SESSION['app_unique_key'] == $application_unique_key)
) {
	$cronTasks = false;
	if (isset($_REQUEST['service']) || ($argc==2 && !empty($argv[1]))) {
		// Run specific service
		$srv = empty($argv[1]) ? $_REQUEST['service'] : $argv[1];
		$srv = vtlib_purify($srv);
		$srvcron = Vtiger_Cron::getInstance($srv);
		if ($srvcron !== false) {
			$cronTasks = array($srvcron);
		} else {
			echo "** Service $srv not found **";
			die();
		}
	} else {
		// Run all service
		$cronTasks = Vtiger_Cron::listAllActiveInstances();
	}
	$app_strings = return_application_language($default_language);
	foreach ($cronTasks as $cronTask) {
		try {
			$cronTask->setBulkMode(true);

			// Not ready to run yet?
			if (!$cronTask->isRunnable()) {
				$msg = sprintf("[INFO]: %s - not ready to run as the time to run again is not completed\n", $cronTask->getName());
				echo $msg;
				$logbg->info($msg);
				continue;
			}

			// Timeout could happen if intermediate cron-tasks fails
			// and affect the next task. Which need to be handled in this cycle.
			if ($cronTask->hadTimedout()) {
				$msg = sprintf("[INFO]: %s - cron task had timedout as it is not completed last time it run- restarting\n", $cronTask->getName());
				echo $msg;
				$logbg->info($msg);
			}

			// Mark the status - running
			$cronTask->markRunning();

			checkFileAccess($cronTask->getHandlerFile());
			$logbg->info('Execute: '.$cronTask->getHandlerFile());
			require_once $cronTask->getHandlerFile();
			$daily=$cronTask->getdaily();
			$timestart=$cronTask->getLastStart();
			// Mark the status - finished
			$cronTask->markFinished($daily, $timestart);
		} catch (Exception $e) {
			$msg = sprintf("[ERROR]: %s - cron task execution throwed exception.\n", $cronTask->getName());
			$msg .= $e->getMessage();
			$msg .= "\n";
			echo $msg;
			$logbg->info($msg);
			//Send email with error.
			$mailto = GlobalVariable::getVariable('Debug_Send_VtigerCron_Error', '');
			if ($mailto != '') {
				require_once 'modules/Emails/mail.php';
				require_once 'modules/Emails/Emails.php';
				$HELPDESK_SUPPORT_EMAIL_ID = GlobalVariable::getVariable('HelpDesk_Support_EMail', 'support@your_support_domain.tld', 'HelpDesk');
				$HELPDESK_SUPPORT_NAME = GlobalVariable::getVariable('HelpDesk_Support_Name', 'your-support name', 'HelpDesk');
				$mailsubject = '[ERROR]: '.$cronTask->getName().' - cron task execution throwed exception.';
				$mailcontent = '<pre>'.$e.'</pre>';
				send_mail('Emails', $mailto, $HELPDESK_SUPPORT_NAME, $HELPDESK_SUPPORT_EMAIL_ID, $mailsubject, $mailcontent);
			}
		}
	}
} else {
	echo 'Access denied!';
}
?>
