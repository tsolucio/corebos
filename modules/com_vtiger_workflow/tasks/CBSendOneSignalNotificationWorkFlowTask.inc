<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/

include_once 'include/integrations/onesignal/onesignal.php';

class CBSendOneSignalNotificationWorkFlowTask extends VTTask {
	public $executeImmediately = true;
	public $queable = true;

	public function getFieldNames() {
		return array(
			'notification_heading', 'notification_subtitle',
			'notification_include_segment', 'notification_excluded_segment',
			'notification_web_url', 'notification_content', 'external_user_id'
		);
	}

	public function doTask(&$entityData) {
		global $current_user;

		if (coreBOS_Settings::getSetting('onesignal_isactive', '') == '1') {
			$entityCache = new VTEntityCache($current_user);
			$ws_id = $entityData->getId();
			$pt = new VTSimpleTemplate($this->notification_content);
			$body = str_replace(
				['<p>','</p>','<strong>','</strong>','<em>','</em>'],
				['','','*','*','_','_'],
				$pt->render($entityCache, $ws_id, [], $entityData->WorkflowContext)
			);
			$externalUser = new VTSimpleTemplate($this->external_user_id);
			$users_list = $externalUser->render($entityCache, $ws_id, [], $entityData->WorkflowContext);

			$users_ws_id_list =  explode(',', $users_list);
			$idsList = array();
			if (count($users_ws_id_list) > 1) {
				foreach ($users_ws_id_list as $wsid) {
					array_push($idsList, explode('x', $wsid)[1]);
				}
				$external_user_id = $idsList;
			} else {
				$wsid = explode('x', $users_list);
				if (count($wsid) == 2) {
					if ((is_numeric($wsid[0]) && is_numeric($wsid[1]))) {
						$external_user_id = array($wsid[1]);
					}
				} else {
					global $adb;
					$result = $adb->pquery('SELECT id FROM vtiger_users WHERE user_name=?', array(htmlentities(strip_tags($users_list))));
					$data = $adb->fetch_array($result);
					$external_user_id = array($data['id']);
				}
			}

			try {
				require_once 'include/integrations/onesignal/onesignal.php';
				$contents = array('en' => $body);
				$headings = array('en' => $this->notification_heading);
				$subtitle = array('en' => $this->notification_subtitle);
				$web_url = $this->notification_web_url;
				$web_buttons = array();
				$filters = array();
				$include_segment = explode(',', $this->notification_include_segment);
				$excluded_segment = explode(',', $this->notification_excluded_segment);
				corebos_onesignal::sendDesktopNotification(
					$contents,
					$headings,
					$subtitle,
					$filters,
					$external_user_id,
					$web_url,
					$web_buttons
				);
			} catch (Exception $e) {
				global $log;
				$log->debug('Error on Send OneSignal Notification Task');
			}
		}
	}
}
?>