<?php
/* +***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 * *********************************************************************************** */
require_once 'modules/WSAPP/synclib/controllers/SynchronizeController.php';

class Google_Contacts_Controller extends WSAPP_SynchronizeController {

	/**
	 * Returns the connector of the google contacts
	 * @return Google_Contacts_Connector
	 */
	public function getTargetConnector() {
		global $current_user;
		$oauth2Connector = new Google_Oauth2_Connector($this->getSourceType(), $current_user->id);
		$oauth2Connection = $oauth2Connector->authorize();
		$connector = new Google_Contacts_Connector($oauth2Connection);
		$connector->setSynchronizeController($this);
		return $connector;
	}

	public function getSourceConnector() {
		$connector = new Google_Vtiger_Connector();
		$connector->setSynchronizeController($this);
		$targetName = $this->targetConnector->getName();
		if (empty($targetName)) {
			throw new Exception('Target Name cannot be empty');
		}
		return $connector->setName('Vtiger_'.$targetName);
	}

	/**
	 * Return the types of snyc
	 * @return object
	 */
	public function getSyncType() {
		return WSAPP_SynchronizeController::WSAPP_SYNCHRONIZECONTROLLER_USER_SYNCTYPE;
	}

	/**
	 * Returns source type of Controller
	 * @return string
	 */
	public function getSourceType() {
		return 'Contacts';
	}
}
