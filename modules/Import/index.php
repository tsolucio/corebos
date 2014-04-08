<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

require_once 'modules/Import/api/Request.php';
require_once 'modules/Import/controllers/Import_Index_Controller.php';
require_once 'modules/Import/controllers/Import_ListView_Controller.php';
require_once 'modules/Import/controllers/Import_Controller.php';

global $current_user;

$previousBulkSaveMode = $VTIGER_BULK_SAVE_MODE;
$VTIGER_BULK_SAVE_MODE = true;

$requestObject = new Import_API_Request($_REQUEST);

Import_Index_Controller::process($requestObject, $current_user);

$VTIGER_BULK_SAVE_MODE = $previousBulkSaveMode;

?>