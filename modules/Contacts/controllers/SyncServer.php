<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/
require_once 'modules/WSAPP/SyncServer.php';

class Google_SyncServer_Controller extends SyncServer {

	public function getDestinationHandleDetails() {
		return wsapp_getHandler('Google_vtigerHandler');
	}
}
