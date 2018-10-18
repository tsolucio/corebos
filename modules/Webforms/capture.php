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
chdir(__DIR__ . '/../..');

include_once 'vtlib/Vtiger/Module.php';
include_once 'include/utils/VtlibUtils.php';
include_once 'include/Webservices/Create.php';
include_once 'modules/Webforms/model/WebformsModel.php';
include_once 'modules/Webforms/model/WebformsFieldModel.php';
include_once 'include/QueryGenerator/QueryGenerator.php';

class Webform_Capture {

	public function captureNow($request, $server) {
		global $adb;
		$returnURL = false;
		try {
			if (!vtlib_isModuleActive('Webforms')) {
				throw new Exception('webforms is not active');
			}

			$webform = Webforms_Model::retrieveWithPublicId(vtlib_purify($request['publicid']));
			if (empty($webform)) {
				throw new Exception('Webform not found.');
			}

			$returnURL = $webform->getReturnUrl();

			$webDomain = $webform->getWebDomain();
			$incomingOrigin = parse_url($server['HTTP_REFERER']);
			if (!empty($webDomain) && stripos($incomingOrigin['host'], $webDomain) === false) {
				throw new Exception('The domain of the form does not match with the webform');
			}
			// Retrieve user information
			$user = CRMEntity::getInstance('Users');
			$user->id=$user->getActiveAdminId();
			$user->retrieve_entity_info($user->id, 'Users');

			// Prepare the parametets
			$parameters = array();
			$webformFields = $webform->getFields();
			foreach ($webformFields as $webformField) {
				if (is_array(vtlib_purify($request[$webformField->getNeutralizedField()]))) {
					$fieldData=implode(' |##| ', vtlib_purify($request[$webformField->getNeutralizedField()]));
				} else {
					$fieldData=vtlib_purify($request[$webformField->getNeutralizedField()]);
				}
				$parameters[$webformField->getFieldName()] = stripslashes($fieldData);
				if (empty($parameters[$webformField->getFieldName()]) && $webformField->getDefaultValue()!=null) {
					$parameters[$webformField->getFieldName()] = decode_html($webformField->getDefaultValue());
				}
				if ($webformField->getRequired()) {
					if (empty($parameters[$webformField->getFieldName()])) {
						throw new Exception('Required fields not filled');
					}
				}
			}
			switch ($webform->getTargetModule()) {
				case 'Potentials':
					if (isset($request['related_to']) && $request['related_to'] != null) {
						$setype = getSalesEntityType($request['related_to']);
						$result = $adb->pquery('SELECT id FROM vtiger_ws_entity WHERE name = ?', array($setype));
						$wsid = $adb->query_result($result, 0, 'id');
						$parameters['related_to'] = $wsid.'x'.$request['related_to'];
					} else {
						throw new Exception('Required field Related To not filled');
					}
					if (isset($request['campaignid']) && $request['campaignid'] != null) {
						$result = $adb->pquery('SELECT id FROM vtiger_ws_entity WHERE name = ?', array('Campaigns'));
						$wsid = $adb->query_result($result, 0, 'id');
						$parameters['campaignid'] = $wsid.'x'.$request['campaignid'];
					}
					break;
				case 'HelpDesk':
					if (isset($request['product_id']) && $request['product_id'] != null) {
						$setype = getSalesEntityType($request['product_id']);
						$result = $adb->pquery('SELECT id FROM vtiger_ws_entity WHERE name = ?', array($setype));
						$wsid = $adb->query_result($result, 0, 'id');
						$parameters['product_id'] = $wsid.'x'.$request['product_id'];
					}
					if (isset($request['parent_id']) && $request['parent_id'] != null) {
						$setype = getSalesEntityType($request['parent_id']);
						$result = $adb->pquery('SELECT id FROM vtiger_ws_entity WHERE name = ?', array($setype));
						$wsid = $adb->query_result($result, 0, 'id');
						$parameters['parent_id'] = $wsid.'x'.$request['parent_id'];
					}
					break;
			}
			$parameters['assigned_user_id'] = vtws_getWebserviceEntityId('Users', $webform->getOwnerId());

			// Create the record
			vtws_create($webform->getTargetModule(), $parameters, $user);

			$this->sendResponse($returnURL, 'ok');
			return;
		} catch (Exception $e) {
			$this->sendResponse($returnURL, false, $e->getMessage());
			return;
		}
	}

	protected function sendResponse($url, $success = false, $failure = false) {
		if (empty($url)) {
			if ($success) {
				$response = json_encode(array('success' => true, 'result' => $success));
			} else {
				$response = json_encode(array('success' => false, 'error' => array('message' => $failure)));
			}

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
$webformCapture->captureNow($_REQUEST, $_SERVER);
?>
