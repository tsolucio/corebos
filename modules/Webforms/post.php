<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/

function getRootDirectoryPath(){
	$path = pathinfo(__FILE__);
	$path = $path['dirname'];
	$path = substr($path,0,strrpos(substr($path,0,strlen($path)-1),DIRECTORY_SEPARATOR));
	$path = substr($path,0,strrpos(substr($path,0,strlen($path)-1),DIRECTORY_SEPARATOR));
	return $path;
}
//get vtiger root directory.
$path = getRootDirectoryPath();
chdir($path);

require_once("config.inc.php");
require_once('include/database/PearDatabase.php');
require_once 'include/Webservices/Utils.php';
require_once("modules/Users/Users.php");
require_once("include/Zend/Json.php");
require_once("include/Webservices/VtigerCRMObject.php");
require_once("include/Webservices/VtigerCRMObjectMeta.php");
require_once("include/Webservices/DataTransform.php");
require_once("include/Webservices/WebServiceError.php");
require_once 'include/utils/utils.php';
require_once 'include/Webservices/ModuleTypes.php';
require_once 'include/utils/VtlibUtils.php';
require_once('include/logging.php');
require_once 'include/Webservices/VtigerWebserviceObject.php';
require_once 'include/Webservices/WebserviceEntityOperation.php';
require_once "include/language/$default_language.lang.php";
require_once 'modules/Webforms/Webforms.config.php';
require_once 'include/Webservices/Login.php';
require_once 'include/Webservices/ModuleTypes.php';
require_once 'include/Webservices/AuthToken.php';
require_once 'include/Webservices/DescribeObject.php';
require_once 'include/Webservices/Create.php';
require_once 'include/Webservices/WebserviceField.php';
require_once 'include/Webservices/EntityMeta.php';
require_once('Smarty_setup.php');

function webforms_getUserData($ownerId, $describeFields,$source){
	$userData = Array();
	$len = sizeof($describeFields);
	for($i=0;$i<$len;++$i){
		$fieldName = $describeFields[$i]['name'];
		// Handle meta fields right away
		if ($describeFields[$i]['type']['name'] == 'owner') {
			$userData[$fieldName] = $ownerId;
			continue;
		}
		/* TODO: if($describeFields[$i]['type']['name'] == 'reference'){ continue; }*/
		
		/**
		 * Support for specifying (fieldname or label:fieldlabel)
		 */		

		// NOTE: Spaces in parameter key will be converted to _ 
		$transformedFieldLabel= str_replace(' ','_', $describeFields[$i]['label']);
								
		$valuekey = false;
		if (isset($source[$fieldName])) {
			$valuekey = $fieldName;
		} else if (isset($source["label:$transformedFieldLabel"])) {
			$valuekey = "label:$transformedFieldLabel";
		}
				
		if($valuekey) {
			$value = vtws_getParameter($source, $valuekey);
			if($value !== null){
				$userData[$fieldName] = $value;
			}
		} else if($describeFields[$i]['mandatory'] == true) {
			return null;
		}
	}
	return $userData;
}

function webforms_returnError($e,$moduleName){
	global $defaultSuccessAction,$failureURL;
	if(strlen($failureURL) > 0){
		header("Location: $failureURL");
	}
	if($defaultSuccessAction == 'JSON'){
		Zend_Json::$useBuiltinEncoderDecoder = true;
		$json = new Zend_Json();
		echo $json->encode(array('success'=>false,'error'=>$e));
	}else{
		webforms_displayTemplate(getExceptionArray($e),$moduleName,'modules/Webforms/ErrorPage.tpl');
	}
}

function getExceptionArray($e){
	if(is_array($e)){
		return $e;
	}
	return array('code'=>$e->code,'message'=>$e->message);
}

function webforms_displayTemplate($data,$moduleName,$path){
	$smarty = new vtigerCRM_Smarty;
	webforms_prepareSmarty($smarty,$data,$moduleName);
	$smarty->display($path);
}

function webforms_prepareSmarty($smarty,$data,$moduleName){
	global $default_language,$site_URL;
	$moduleStrings = return_module_language($default_language,'Webforms');
	$appStrings = return_application_language($default_language);
	$path = (strrpos($site_URL,'/') === strlen($site_URL))? $site_URL: $site_URL.'/';
	$smarty->assign("PATH", $path);
	$smarty->assign("IMAGEPATH", 'themes/images/');
	$smarty->assign("MODULE",$moduleName);
	$smarty->assign("MOD", $moduleStrings);
	$smarty->assign("DATA", $data);
	$smarty->assign("APP", $appStrings);
}

function webforms_returnSuccess($element,$moduleName){
	global $successURL;
	if(strlen($successURL) > 0){
		header("Location: $successURL");
	}
	if($defaultSuccessAction == 'JSON'){
		Zend_Json::$useBuiltinEncoderDecoder = true;
		$json = new Zend_Json();
		echo $json->encode(array('success'=>true,'result'=>$element));
	}else{
		webforms_displayTemplate($elemnet,$moduleName,'modules/Webforms/SuccessPage.tpl');
	}
}

function webforms_init(){
	global $defaultUserName,$defaultUserAccessKey,$defaultOwner,$adb,$enableAppKeyValidation,$application_unique_key;
	try{
		$active = vtlib_isModuleActive('Webforms');
		if($active === false){
			webforms_returnError(array('code'=>"WEBFORMS_DISABLED",'message'=>'Webforms module is disabled'),'Webforms');
		}
		
		if($enableAppKeyValidation ==true){
			if($application_unique_key !== $_REQUEST['appKey']){
				webforms_returnError(array('code'=>"WEBFORMS_INVALID_APPKEY",'message'=>'AppKey provided is invalid'),null);
				return ;
			}
		}
		
		$module = $_REQUEST['moduleName'];
		$challengeResult = vtws_getchallenge($defaultUserName);
		$challengeToken = $challengeResult['token'];
		$user = vtws_login($defaultUserName,md5($challengeToken.$defaultUserAccessKey));
		$describeResult = vtws_describe($module,$user);
		$fields = $describeResult['fields'];
		$assignedUser = new Users();
		$ownerId = $assignedUser->retrieve_user_id($defaultOwner);
		$userData = webforms_getUserData(vtws_getId(VtigerWebserviceObject::fromName($adb,"Users")->getEntityId(),$ownerId),$fields,$_REQUEST);
		
		if($userData === null){
			webforms_returnError(array('code'=>"WEBFORMS_INVALID_DATA",'message'=>'data provided is invalid'),$module);
			return ;
		}
		
		if(sizeof($userData)<1){
			webforms_returnError(array('code'=>"WEBFORMS_INVALID_DATA",'message'=>'data provided is invalid'),$module);
			return ;
		}
		$createResult = vtws_create($module,$userData,$user);
		webforms_returnSuccess($createResult,$module);
	}catch(WebServiceException $e){
		webforms_returnError($e,$module);
	}
}

webforms_init();
?>