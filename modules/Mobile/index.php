<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 * Modified by crm-now GmbH, www.crm-now.com
 ************************************************************************************/
if ($_SERVER['QUERY_STRING']=='module=Mobile&action=index') {
?>
<script type="text/javascript">gotourl('modules/Mobile/index.php');</script>
<?php
die();
}
header('Content-Type: text/html;charset=utf-8');
chdir (dirname(__FILE__) . '/../../');

/**
 * URL Verfication - Required to overcome Apache mis-configuration and leading to shared setup mode.
 */
require_once 'config.php';
if (file_exists('config_override.php')) {
	include_once 'config_override.php';
}
//Relations sets the GetRelatedList function to local
//require_once dirname(__FILE__) . '/api/Relation.php';
include_once dirname(__FILE__) . '/api/Request.php';
include_once dirname(__FILE__) . '/api/Response.php';


include_once dirname(__FILE__) . '/api/ws/Controller.php';

include_once dirname(__FILE__) . '/Mobile.php';
include_once dirname(__FILE__) . '/views/Viewer.php';
include_once dirname(__FILE__) . '/views/models/Module.php'; // Required for auto de-serializatio of session data

class crmtogo_Index_Controller {

	static $opControllers = array(
		'logout'                  => array('file' => '/views/Logout.php', 'class' => 'crmtogo_UI_Logout'),
		'login'                   => array('file' => '/views/Login.php', 'class' => 'crmtogo_UI_Login'),
		'loginAndFetchModules'    => array('file' => '/views/LoginAndFetchModules.php', 'class' => 'crmtogo_UI_LoginAndFetchModules'),
		'listModuleRecords'       => array('file' => '/views/ListModuleRecords.php', 'class' => 'crmtogo_UI_ListModuleRecords'),
		'fetchRecord' 			  => array('file' => '/views/DetailView.php', 'class' => 'crmtogo_UI_DetailView'),
		'edit'                    => array('file' => '/views/EditView.php', 'class' => 'crmtogo_UI_EditView' ),
		'create'                  => array('file' => '/views/EditView.php', 'class' => 'crmtogo_UI_EditView' ), 
		'createActivity'          => array('file' => '/views/createActivity.php', 'class' => 'crmtogo_UI_DecideActivityType' ), 
		'globalsearch'            => array('file' => '/views/ListGlobalSearchResults.php', 'class' => 'crmtogo_UI_GlobalSearch' ),
		'deleteConfirmation'  	  => array('file' => '/views/deleteConfirmation.php', 'class' => 'crmtogo_UI_Delete' ),
		'deleteRecords'  		  => array('file' => '/views/ListModuleRecords.php', 'class' => 'crmtogo_UI_ListModuleRecords' ),
		'saveRecord'              => array('file' => '/views/SaveRecord.php', 'class' => 'crmtogo_UI_ProcessRecordCreation'),
		'getrelatedlists'         => array('file' => '/views/getRelationList.php', 'class' => 'crmtogo_UI_GetRelatedLists'),
		'addComment'			  => array('file' => '/views/addComment.php', 'class' => 'crmtogo_UI_AddComment'),
		'configCRMTOGO'			  => array('file' => '/views/editConfiguration.php', 'class' => 'crmtogo_UI_Configuration'),
		'downloadFile'			  => array('file' => '/actions/getFileForDownload.php', 'class' => 'crmtogo_UI_DownLoadFile'),
		'getRelatedFieldAjax'  	  => array('file' => '/actions/getRelatedFieldAjax.php', 'class' => 'crmtogo_UI_getRelatedFieldAjax' ), 
		'getScrollcontent'        => array('file' => '/actions/getScrollContent.php', 'class' => 'crmtogo_UI_GetScrollRecords' ),
		'changeGUISettings'         => array('file' => '/actions/changeGUISettings.php', 'class' => 'crmtogo_UI_ChangeSettings' ),
	);

	static function process(crmtogo_API_Request $request) {
		$operation = $request->getOperation();
		if (empty($operation)) $operation = 'login';

		$response = false;
		if(isset(self::$opControllers[$operation])) {
			$operationFile = self::$opControllers[$operation]['file'];
			$operationClass= self::$opControllers[$operation]['class'];

			include_once dirname(__FILE__) . $operationFile;
			$operationController = new $operationClass;

			$operationSession = false;
			if($operationController->requireLogin()) {
				$operationSession = coreBOS_Session::init();
				if($operationController->hasActiveUser() === false) {
					$operationSession = false;
				}
			} else {
				// By-pass login
				$operationSession = true;
			}

			if($operationSession === false) {
				$response = new crmtogo_API_Response();
				$response->setError(1501, 'Login required');
			} else {
				try {
					$response = $operationController->process($request);
				} catch(Exception $e) {
					$response = new crmtogo_API_Response();
					$response->setError($e->getCode(), $e->getMessage());
				}
			}
		} else {
			$response = new crmtogo_API_Response();
			$response->setError(1404, 'Operation not found: ' . $operation);
		}

		if($response !== false) {
			if ($response->hasError()) {
				include_once dirname(__FILE__) . '/views/Error.php';
				$errorController = new crmtogo_UI_Error();
				$errorController->setError($response->getError());
				echo $errorController->process($request)->emitHTML();
			} 
			else {
				$result = $response->getResult();
				if ( !empty($result['type']) AND $result['type'] =='json') {
					echo $response->emitJSON();
				}
				else {
					echo $response->emitHTML();
				}
			}
		}
	}
}

/** Take care of stripping the slashes */
function stripslashes_recursive($value) {
	$value = is_array($value) ? array_map('stripslashes_recursive', $value) : stripslashes($value);
	return $value;
}
if (get_magic_quotes_gpc()) {
	//$_GET     = stripslashes_recursive($_GET   );
	//$_POST    = stripslashes_recursive($_POST  );
	$_REQUEST = stripslashes_recursive($_REQUEST);
}

if(!defined('CRMTOGO_INDEX_CONTROLLER_AVOID_TRIGGER')) {
	crmtogo_Index_Controller::process(new crmtogo_API_Request($_REQUEST));
}
?>
