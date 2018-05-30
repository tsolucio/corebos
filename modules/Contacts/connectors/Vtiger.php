<?php
/* +***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 * *********************************************************************************** */
require_once 'modules/WSAPP/synclib/connectors/VtigerConnector.php';
require_once 'modules/WSAPP/SyncServer.php';
include_once 'include/Webservices/Query.php';
include_once 'include/Webservices/Create.php';
include_once 'include/Webservices/Retrieve.php';

class Google_Vtiger_Connector extends WSAPP_VtigerConnector {

	/**
	 * function to push data to vtiger
	 * @param array $recordList
	 * @param object $syncStateModel
	 * @return array
	 */
	public function push($recordList, $syncStateModel) {
		return parent::push($recordList, $syncStateModel);
	}

	/**
	 * function to get data from vtiger
	 * @param object $syncStateModel
	 * @return object
	 */
	public function pull(WSAPP_SyncStateModel $syncStateModel) {
		return parent::pull($syncStateModel);
	}

	/**
	 * function that returns syncTrackerhandler name
	 * @return string
	 */
	public function getSyncTrackerHandlerName() {
		return 'Google_vtigerSyncHandler';
	}
}
