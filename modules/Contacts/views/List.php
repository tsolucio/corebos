<?php
/* +***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 * *********************************************************************************** */

class Google_List_View  {

    protected $noRecords = false;

    public function __construct() {

    }

    function process($request) {
        switch ($request['operation']) {
            case "signin" : return $this->signin($request);
                break;
            case "sync" : return $this->renderSyncUI($request);
                break;
            case "removeSync" : 
                return $this->deleteSync($request);
                break;
            default: $this->renderWidgetUI($request);
                break;
        }
    }

    function renderWidgetUI(Vtiger_Request $request) {
        $sourceModule = $request->get('sourcemodule');
        $viewer = new vtigerCRM_Smarty();
        $oauth2 = new Google_Oauth2_Connector($sourceModule);
        $firstime = $oauth2->hasStoredToken();
        $viewer->assign('MODULE_NAME', $request->getModule());
        $viewer->assign('FIRSTTIME', $firstime);
        $viewer->assign('STATE', 'home');
        $viewer->assign('SYNCTIME', Google_Utils_Helper::getLastSyncTime($sourceModule));
        $viewer->assign('SOURCEMODULE', $request->get('sourcemodule'));
        $viewer->assign('SCRIPTS',$this->getHeaderScripts($request));
        $viewer->view('Contents.tpl', $request->getModule());
    }

    function signin( $request) {
        $viewer = new vtigerCRM_Smarty();
        $sourceModule = $request['sourcemodule'];
        $oauth2 = new Google_Oauth2_Connector($sourceModule);
        $oauth2->authorize();
    }
    function renderSyncUI( $request) {
        $viewer = new vtigerCRM_Smarty();
        $sourceModule = $request['sourcemodule'];
        $oauth2 = new Google_Oauth2_Connector($sourceModule);
        $oauth2->authorize();
        if (!empty($sourceModule)) {
            try {
                $records = $this->Contacts();
            } catch (Zend_Gdata_App_HttpException $e) {
                $errorCode = $e->getResponse()->getStatus();
                if($errorCode == 401) {
                    $this->removeSynchronization($request);
                    $response = new Vtiger_Response();
                    $response->setError(401);
                    $response->emit();
                    return false;
                }
            }
        }
        $firstime = $oauth2->hasStoredToken();
        $viewer->assign('MODULE_NAME', 'Contacts');
        $viewer->assign('FIRSTTIME', $firstime);
        $viewer->assign('RECORDS', $records);
        $viewer->assign('NORECORDS', $this->noRecords);
        global $mod_strings;
        $viewer->assign('MOD', $mod_strings);
        //$viewer->assign('SYNCTIME', Google_Utils_Helper::getLastSyncTime($sourceModule));
        //$viewer->assign('STATE', $request->get('operation'));
        $viewer->assign('SOURCEMODULE', 'Contacts');
        if (!$firstime) {
            $viewer->display("modules/Contacts/Contents.tpl");
            //$viewer->display('Contents.tpl', $request->getModule());
        } else {
            $viewer->display("modules/Contacts/ContentDetails.tpl");
//            $viewer->fetch(vtlib_getModuleTemplate("Contacts","ContentDetails.tpl"));
            //echo $viewer->view('ContentDetails.tpl', $request->getModule(), true);
        }
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
        $records = $controller->synchronize(true,$syncDirection[0],$syncDirection[1]);
        $syncRecords = $this->getSyncRecordsCount($records);
        $syncRecords['vtiger']['more'] = $controller->targetConnector->moreRecordsExits();
        $syncRecords['google']['more'] = $controller->sourceConnector->moreRecordsExits();
        return $syncRecords;
    }

    /**
     * Sync Calendar Records
     * @return <array> Count of Calendar Records
     */
    public function Calendar($userId = false) {
        global $current_user;
        $user = $current_user;
        $controller = new Google_Calendar_Controller($user);
        $records = $controller->synchronize();
        $syncRecords = $this->getSyncRecordsCount($records);
        $syncRecords['vtiger']['more'] = $controller->targetConnector->moreRecordsExits();
        $syncRecords['google']['more'] = $controller->sourceConnector->moreRecordsExits();
        return $syncRecords;
    }

    /**
     * Removes Synchronization
     */
    function removeSynchronization($request) {
        global $current_user;
        $user = $current_user;
        $sourceModule = $request['sourcemodule'];
        Google_Module_Model::removeSync($sourceModule, $user->id);
    }

    function deleteSync($request) {
        global $current_user;
        $user = $current_user;
        $sourceModule = $request['sourcemodule'];
        Google_Module_Model::deleteSync($sourceModule, $user->id);
    }

    /**
     * Return the sync record added,updated and deleted count
     * @param type $syncRecords
     * @return array
     */
    public function getSyncRecordsCount($syncRecords) {
        $countRecords = array('vtiger' => array('update' => 0, 'create' => 0, 'delete' => 0), 'google' => array('update' => 0, 'create' => 0, 'delete' => 0));
        foreach ($syncRecords as $key => $records) {
            if ($key == 'push') {
                $pushRecord = false;
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
            } else if ($key == 'pull') {
                $pullRecord = false;
                if (count($records) == 0) {
                    $pullRecord = true;
                }
                foreach ($records as $type => $record) {
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

	/**
	 * Function to get the list of Script models to be included
	 * @param Vtiger_Request $request
	 * @return <Array> - List of Vtiger_JsScript_Model instances
	 */
	public function getHeaderScripts(Vtiger_Request $request) {
		$moduleName = $request->getModule();
		return $this->checkAndConvertJsScripts(array("~libraries/bootstrap/js/bootstrap-popover.js","modules.$moduleName.resources.List"));
	}

	public function validateRequest(Vtiger_Request $request) {
		//don't do validation because there is a redirection from google
	}
}

