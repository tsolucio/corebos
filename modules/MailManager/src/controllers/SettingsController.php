<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/
include_once __DIR__ . '/MainUIController.php';

/**
 * Class used to control the Mail Manager Settings
 */
class MailManager_SettingsController extends MailManager_MainUIController {

	/**
	* Process the request for Settings Operations
	* @param MailManager_Request $request
	* @return MailManager_Response
	*/
	public function process(MailManager_Request $request) {
		$response = new MailManager_Response();
		if ('edit' == $request->getOperationArg()) {
			$model = $this->getMailBoxModel();
			$serverName = $model->serverName();
			$viewer = $this->getViewer();
			$viewer->assign('SERVERNAME', $serverName);
			$response->setResult($viewer->fetch($this->getModuleTpl('Settings.tpl')));
		} elseif ('save' == $request->getOperationArg()) {
			$model = $this->getMailBoxModel();
			$model->setServer($request->get('_mbox_server'));
			$model->setUsername($request->get('_mbox_user'));
			$request->set('_mbox_pwd', urlencode($_REQUEST['_mbox_pwd']));
			$model->setPassword($request->get('_mbox_pwd'));
			$model->setProtocol($request->get('_mbox_protocol', 'imap2'));
			$model->setSSLType($request->get('_mbox_ssltype', 'tls'));
			$model->setCertValidate($request->get('_mbox_certvalidate', 'novalidate-cert'));
			$model->setRefreshTimeOut($request->get('_mbox_refresh_timeout'));
			$connector = $this->getConnector();
			if ($connector->isConnected()) {
				$model->save();
				$request->set('_operation', 'mainui');
				return parent::process($request);
			} elseif ($connector->hasError()) {
				$response->isJSON(true);
				$response->setError(101, $connector->lastError());
			}
		} elseif ('valconfig' == $request->getOperationArg()) {
			$model = $this->getMailBoxModel();
			$model->setServer($request->get('ic_mail_server_name'));
			$model->setUsername($request->get('ic_mail_server_username'));
			$model->setPassword($request->get('ic_mail_server_password'));
			$model->setProtocol($request->get('ic_mail_server_protocol', 'imap2'));
			$model->setSSLType($request->get('ic_mail_server_ssltype', 'tls'));
			$model->setCertValidate($request->get('ic_mail_server_sslmeth', 'novalidate-cert'));
			$model->setRefreshTimeOut($request->get('ic_mail_server_refresh_time'));
			$connector = $this->getConnector();
			if ($connector->isConnected()) {
				$response->setResult(array('status' => true));
			} elseif ($connector->hasError()) {
				$response->isJSON(true);
				$response->setError(101, $connector->lastError());
			}
		} elseif ('remove' == $request->getOperationArg()) {
			$model = $this->getMailBoxModel();
			$model->delete();
			$response->isJSON(true);
			$response->setResult(array('status' => true));
		}
		return $response;
	}
}
?>