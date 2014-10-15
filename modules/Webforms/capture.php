<?php
/* +**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 * ********************************************************************************** */
// Switch the working directory to base
chdir(dirname(__FILE__) . '/../..');

include_once 'include/Zend/Json.php';
include_once 'vtlib/Vtiger/Module.php';
include_once 'include/utils/VtlibUtils.php';
include_once 'include/Webservices/Create.php';
include_once 'modules/Webforms/model/WebformsModel.php';
include_once 'modules/Webforms/model/WebformsFieldModel.php';
include_once 'include/QueryGenerator/QueryGenerator.php';

class Webform_Capture {
	
	function captureNow($request) {
		$returnURL = false;
		try {

			if(!vtlib_isModuleActive('Webforms')) throw new Exception('webforms is not active');
			
			$webform = Webforms_Model::retrieveWithPublicId(vtlib_purify($request['publicid']));
			if (empty($webform)) throw new Exception("Webform not found.");
			
			$returnURL = $webform->getReturnUrl();

			// Retrieve user information
			$user = CRMEntity::getInstance('Users');
			$user->id=$user->getActiveAdminId();
			$user->retrieve_entity_info($user->id, 'Users');

			// Prepare the parametets
			$parameters = array();
			$webformFields = $webform->getFields();
			foreach ($webformFields as $webformField) {
				if($webformField->getDefaultValue()!=null){
					$parameters[$webformField->getFieldName()] = decode_html($webformField->getDefaultValue());
				}else{
					if(is_array(vtlib_purify($request[$webformField->getNeutralizedField()]))){
						$fieldData=implode(" |##| ",vtlib_purify($request[$webformField->getNeutralizedField()]));
					}
					else{
						$fieldData=vtlib_purify($request[$webformField->getNeutralizedField()]);
					}
				
					$parameters[$webformField->getFieldName()] = stripslashes($fieldData);
				}
				if($webformField->getRequired()){
					if(empty($parameters[$webformField->getFieldName()]))  throw new Exception("Required fields not filled");
				}
			}

			$parameters['assigned_user_id'] = vtws_getWebserviceEntityId('Users', $webform->getOwnerId());
			// Create the record
			
			$record=vtws_create($webform->getTargetModule(), $parameters, $user);
			
			$this->sendResponse($returnURL, 'ok');
			return;

		} catch (Exception $e) {
			$this->sendResponse($returnURL, false, $e->getMessage());
			return;
		}
	}

	protected function sendResponse($url, $success=false, $failure=false) {
		if (empty($url)) {
			if ($success) $response = Zend_Json::encode(array('success' => true, 'result' => $success));
			else $response = Zend_Json::encode(array('success' => false, 'error' => array('message' => $failure)));

			// Support JSONP
			if (!empty($_REQUEST['callback'])) {
				$callback = vtlib_purify($_REQUEST['callback']);
				echo sprintf("%s(%s)", $callback, $response);
			} else {
				echo $response;
			}
		} else {
			header(sprintf("Location: http://%s?%s=%s", $url, ($success? 'success' : 'error'), ($success? $success: $failure)));
		}
	}
}

// NOTE: Take care of stripping slashes...
$webformCapture = new Webform_Capture();
$webformCapture->captureNow($_REQUEST);
?>