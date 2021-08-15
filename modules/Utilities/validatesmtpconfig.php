<?php
include_once 'modules/MailManager/src/controllers/Controller.php';
include_once 'modules/MailManager/src/connectors/Connector.php';
include_once 'modules/MailManager/MailManager.php';

$verification_status_object = array();

// Out Going Mail Server Configuration Verification
function verifyOutGoingMailServer() {
	global $log;
	// those are fiels id's used on Setting Outgoing Mail Server in CRM Setting
	$_REQUEST['server'] = $_REQUEST['og_mail_server_name'];
	$_REQUEST['server_username'] = $_REQUEST['og_mail_server_username'];
	$_REQUEST['server_password'] = $_REQUEST['og_mail_server_password'];
	$_REQUEST['smtp_auth'] = $_REQUEST['og_mail_server_smtp_auth'];

	require_once 'modules/Emails/mail.php';
	global $current_user;
	if (isset($_REQUEST['og_mail_sever_from_email'])) {
		$from_email = $_REQUEST['og_mail_sever_from_email'];
	} else {
		$to_email = getUserEmailId('id', $current_user->id);
		$from_email = $to_email;
	}

	$HELPDESK_SUPPORT_NAME = GlobalVariable::getVariable('HelpDesk_Support_Name', 'your-support name', 'HelpDesk');
	$subject = 'Test mail about the mail server configuration.';
	$description = 'Dear '.$current_user->user_name.', <br><br><b> This is a test mail sent to confirm if a mail is actually being sent through the smtp server that you'
		.' have configured. </b><br>Feel free to delete this mail.<br><br>Thanks and Regards,<br> '.$HELPDESK_SUPPORT_NAME.' <br>';

	if ($to_email != '') {
		$mail_status = send_mail('Users', $to_email, $current_user->user_name, $from_email, $subject, $description);
		$mail_status_str = $to_email.'='.$mail_status.'&&&';
	} else {
		$mail_status_str = "'".$to_email."'=0&&&";
	}
	$error_str = getMailErrorString($mail_status_str);
	$error_code = str_replace('mail_error=', '', $error_str);
	$error_msg = strip_tags(parseEmailErrorString($error_code));
	return array(
		'og_server_status' => $mail_status,
		'og_server_message' => $error_msg
	);
}

// Incoming Mail Server Configuration Verification
function verifyIncomingMailServer() {
	$controllers = array(
		'settings'=>array('file' => 'src/controllers/SettingsController.php', 'class'=> 'MailManager_SettingsController')
	);
	$request = new MailManager_Request($_REQUEST);
	$request->set('_operation', 'settings');
	$request->set('_operationarg', 'valconfig');
	$operation = $request->getOperation();
	$controllerInfo = $controllers['settings'];

	$controllerFile = 'modules/MailManager/' . $controllerInfo['file'];
	checkFileAccessForInclusion($controllerFile);
	include_once $controllerFile;
	$controller = new $controllerInfo['class'];

	if ($controller) {
		$controller->closeConnector();
	}

	$response = $controller->process($request);
	if ($response->hasError()) {
		$error_array = $response->getError();
		return array(
			'ic_server_status' => $error_array['code'],
			'ic_server_message' => $error_array['message']
		);
	} elseif (($response->getError() == null) && ($response->getResult()!=null)) {
		$array_result = $response->getResult();
		return array(
			'ic_server_status' => $array_result['status'],
			'ic_server_message' => 'Success'
		);
	}
}

// Check Which Server Configuration to Validate
if (isset($_REQUEST['og_mail_server_active']) && $_REQUEST['og_mail_server_active'] == 'on') {
	$og_verification_status_object = verifyOutGoingMailServer();
	if ($og_verification_status_object['og_server_status'] == 1) {
		$og_config_validation_error_message = 'success';
		$og_config_has_error = false;
	} else {
		$og_config_validation_error_message = $og_verification_status_object['og_server_message'];
		$og_config_has_error = true;
	}

	if ($og_config_validation_error_message == 'success') {
		$og_mail_server_validation_error = false;
		$og_mail_server_validation_success = true;
		$og_mail_server_validation_warning = false;
		$og_mail_server_validation_error_message = '';
	} elseif ($og_config_has_error) {
		$og_mail_server_validation_error = true;
		$og_mail_server_validation_success = false;
		$og_mail_server_validation_warning = false;
		$og_mail_server_validation_error_message = $og_config_validation_error_message;
	}
} else {
	$og_mail_server_validation_error = false;
	$og_mail_server_validation_success = false;
	$og_mail_server_validation_error_message = '';
}

if (isset($_REQUEST['ic_mail_server_active']) && $_REQUEST['ic_mail_server_active'] == 'on') {
	$ic_verification_status_object = verifyIncomingMailServer();
	if ($ic_verification_status_object['ic_server_message'] == 'Success') {
		$ic_config_validation_error_message = 'success';
		$ic_config_has_error = false;
	} else {
		$ic_config_validation_error_message = $ic_verification_status_object['ic_server_message'];
		$ic_config_has_error = true;
	}

	if ($ic_config_validation_error_message == 'success') {
		$ic_mail_server_validation_error = false;
		$ic_mail_server_validation_success = true;
		$ic_mail_server_validation_warning = false;
		$ic_mail_server_validation_error_message = '';
	} elseif ($ic_config_has_error) {
		$ic_mail_server_validation_error = true;
		$ic_mail_server_validation_success = false;
		$ic_mail_server_validation_warning = false;
		$ic_mail_server_validation_error_message = $ic_config_validation_error_message;
	}
} else {
	$ic_mail_server_validation_error = false;
	$ic_mail_server_validation_success = false;
	$ic_mail_server_validation_error_message = '';
}
include_once 'include/integrations/smtp/settings.php';
?>