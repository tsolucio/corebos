<?php
/* +***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 * *********************************************************************************** */

class Google_List_View {

	protected $noRecords = false;

	public function __construct() {
	}

	public function process($request) {
		switch ($request['operation']) {
			case 'signin':
				return $this->signin($request);
				break;
			case 'sync':
				return $this->renderSyncUI($request);
				break;
			case 'removeSync':
				return $this->deleteSync($request);
				break;
			default:
				$this->renderWidgetUI($request);
				break;
		}
	}

	public function renderWidgetUI(Vtiger_Request $request) {
		$sourceModule = $request->get('sourcemodule');
		$viewer = new vtigerCRM_Smarty();
		$oauth2 = new Google_Oauth2_Connector($sourceModule);
		$firstime = $oauth2->hasStoredToken();
		$viewer->assign('MODULE_NAME', $request->getModule());
		$viewer->assign('FIRSTTIME', $firstime);
		$viewer->assign('STATE', 'home');
		$viewer->assign('SYNCTIME', Google_Utils_Helper::getLastSyncTime($sourceModule));
		$viewer->assign('SOURCEMODULE', $request->get('sourcemodule'));
		$viewer->assign('SCRIPTS', '');
		global $coreBOS_app_name;
		$coreBOS_uiapp_name = GlobalVariable::getVariable('Application_UI_Name', $coreBOS_app_name);
		$viewer->assign('coreBOS_uiapp_name', $coreBOS_uiapp_name);
		$viewer->view('Contents.tpl', $request->getModule());
	}

	public function signin($request) {
		$sourceModule = $request['sourcemodule'];
		$oauth2 = new Google_Oauth2_Connector($sourceModule);
		$oauth2->authorize();
	}

	public function renderSyncUI($request) {
		global $theme;
		$viewer = new vtigerCRM_Smarty();
		$sourceModule = $request['sourcemodule'];
		$oauth2 = new Google_Oauth2_Connector($sourceModule);
		$oauth2->authorize();
		if (!empty($sourceModule)) {
			try {
				$records = $this->Contacts();
			} catch (Exception $e) {
				$errorMessage = $e->getMessage();
				$this->removeSynchronization($request);
				$viewer->assign('ERROR_MESSAGE_CLASS', 'cb-alert-danger');
				$viewer->assign('ERROR_MESSAGE', getTranslatedString('ERR_GContactsSync', 'Contacts').' '.$errorMessage);
				$viewer->display('applicationmessage.tpl');
				return false;
			}
		}
		$firstime = $oauth2->hasStoredToken();
		$viewer->assign('MODULE_NAME', 'Contacts');
		$viewer->assign('FIRSTTIME', $firstime);
		$viewer->assign('THEME', $theme);
		$viewer->assign('RECORDS', $records);
		$viewer->assign('NORECORDS', $this->noRecords);
		global $mod_strings, $app_strings;
		$viewer->assign('APP', $app_strings);
		$viewer->assign('MOD', $mod_strings);
		$viewer->assign('SOURCEMODULE', 'Contacts');
		$viewer->display('modules/Contacts/ContentDetails.tpl');
	}

	/**
	 * Sync Contacts Records
	 * @return <array> Count of Contacts Records
	 */
	public function Contacts() {
		global $current_user;
		$user = $current_user;
		$controller = new Google_Contacts_Controller($user);
		$syncDirection = Google_Utils_Helper::getSyncDirectionForUser($user);
		$records = $controller->synchronize(true, $syncDirection[0], $syncDirection[1]);
		$syncRecords = $this->getSyncRecordsCount($records);
		$syncRecords['vtiger']['more'] = $controller->targetConnector->moreRecordsExits();
		$syncRecords['google']['more'] = $controller->sourceConnector->moreRecordsExits();
		return $syncRecords;
	}

	/**
	 * Removes Synchronization
	 */
	public function removeSynchronization($request) {
		global $current_user;
		$user = $current_user;
		$sourceModule = $request['sourcemodule'];
		Google_Module_Model::removeSync($sourceModule, $user->id);
	}

	public function deleteSync($request) {
		global $current_user;
		$user = $current_user;
		$sourceModule = $request['sourcemodule'];
		Google_Module_Model::deleteSync($sourceModule, $user->id);
	}

	/**
	 * Return the sync record added,updated and deleted count
	 * @param array $syncRecords
	 * @return array
	 */
	public function getSyncRecordsCount($syncRecords) {
		$countRecords = array('vtiger' => array('update' => 0, 'create' => 0, 'delete' => 0), 'google' => array('update' => 0, 'create' => 0, 'delete' => 0));
		$pushRecord = false;
		$pullRecord = false;
		foreach ($syncRecords as $key => $records) {
			if ($key == 'push') {
				if (count($records) == 0) {
					$pushRecord = true;
				}
				foreach ($records as $record) {
					foreach ($record as $type => $data) {
						if ($type == 'source') {
							if ($data->getMode() == WSAPP_SyncRecordModel::WSAPP_UPDATE_MODE) {
								$countRecords['vtiger']['update']++;
							} elseif ($data->getMode() == WSAPP_SyncRecordModel::WSAPP_CREATE_MODE) {
								$countRecords['vtiger']['create']++;
							} elseif ($data->getMode() == WSAPP_SyncRecordModel::WSAPP_DELETE_MODE) {
								$countRecords['vtiger']['delete']++;
							}
						}
					}
				}
			} elseif ($key == 'pull') {
				if (count($records) == 0) {
					$pullRecord = true;
				}
				foreach ($records as $record) {
					foreach ($record as $type => $data) {
						if ($type == 'target') {
							if ($data->getMode() == WSAPP_SyncRecordModel::WSAPP_UPDATE_MODE) {
								$countRecords['google']['update']++;
							} elseif ($data->getMode() == WSAPP_SyncRecordModel::WSAPP_CREATE_MODE) {
								$countRecords['google']['create']++;
							} elseif ($data->getMode() == WSAPP_SyncRecordModel::WSAPP_DELETE_MODE) {
								$countRecords['google']['delete']++;
							}
						}
					}
				}
			}
		}

		if ($pullRecord && $pushRecord) {
			$this->noRecords = true;
		}
		return $countRecords;
	}

	public function validateRequest(Vtiger_Request $request) {
		//don't do validation because there is a redirection from google
	}
}
