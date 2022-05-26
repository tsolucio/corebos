<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/
require_once 'include/Webservices/Utils.php';

class OperationManager {
	private $format;
	private const FORMATDEFAULTS = array(
		'includePath'=>'include/Webservices/OperationManagerEnDecode.php',
		'class'=>'OperationManagerEnDecode',
		'encodeMethod'=>'encode',
		'decodeMethod'=>'decode',
		'postCreate'=>''
	);
	private $formatsData=array(
		'json'=> self::FORMATDEFAULTS,
		'stream'=> self::FORMATDEFAULTS,
		'streamraw'=> self::FORMATDEFAULTS,
	);
	private $formatObjects;
	private $inParamProcess;
	private $sessionManager;
	private $pearDB;
	private $operationName;
	private $type;
	private $handlerPath;
	private $handlerMethod;
	private $preLogin;
	private $operationId;
	private $operationParams;

	public function __construct($adb, $operationName, $format, $sessionManager) {
		$this->format = strtolower($format);
		$this->sessionManager = $sessionManager;
		$this->formatObjects = array();
		foreach ($this->formatsData as $frmt => $frmtData) {
			require_once $frmtData['includePath'];
			$instance = new $frmtData['class']();
			$this->formatObjects[$frmt]['encode'] = array(&$instance,$frmtData['encodeMethod']);
			$this->formatObjects[$frmt]['decode'] = array(&$instance,$frmtData['decodeMethod']);
			if ($frmtData['postCreate']) {
				call_user_func($frmtData['postCreate'], $instance);
			}
		}
		$this->pearDB = $adb;
		$this->operationName = $operationName;
		$this->inParamProcess = array();
		$this->inParamProcess['encoded'] = &$this->formatObjects[$this->format]['decode'];
		$this->fillOperationDetails($operationName);
	}

	public function isPreLoginOperation() {
		return $this->preLogin == 1;
	}

	private function fillOperationDetails($operationName) {
		$result = $this->pearDB->pquery('select * from vtiger_ws_operation where name=?', array($operationName));
		if ($result) {
			$rowCount = $this->pearDB->num_rows($result);
			if ($rowCount > 0) {
				$row = $this->pearDB->query_result_rowdata($result, 0);
				$this->type = $row['type'];
				$this->handlerMethod = $row['handler_method'];
				$this->handlerPath = $row['handler_path'];
				$this->preLogin = $row['prelogin'];
				$this->operationName = $row['name'];
				$this->operationId = $row['operationid'];
				$this->fillOperationParameters();
				return;
			}
		}
		throw new WebServiceException(WebServiceErrorCode::$UNKNOWNOPERATION, 'Unknown operation requested');
	}

	private function fillOperationParameters() {
		$sql = 'select name, type from vtiger_ws_operation_parameters where operationid=? order by sequence';
		$result = $this->pearDB->pquery($sql, array($this->operationId));
		$this->operationParams = array();
		if ($result) {
			$rowCount = $this->pearDB->num_rows($result);
			if ($rowCount > 0) {
				for ($i=0; $i<$rowCount; ++$i) {
					$row = $this->pearDB->query_result_rowdata($result, $i);
					$this->operationParams[] = array($row['name'] => $row['type']);
				}
			}
		}
	}

	public function getOperationInput() {
		$input = &$_REQUEST;
		return $input;
	}

	public function sanitizeOperation($input) {
		return $this->sanitizeInputForType($input);
	}

	public function sanitizeInputForType($input) {
		$sanitizedInput = array();
		foreach ($this->operationParams as $columnDetails) {
			foreach ($columnDetails as $columnName => $type) {
				$sanitizedInput[$columnName] = $this->handleType($type, vtws_getParameter($input, $columnName));
			}
		}
		return $sanitizedInput;
	}

	public function handleType($type, $value) {
		$value = vtws_stripSlashesRecursively($value);
		$type = strtolower($type);
		if (!empty($this->inParamProcess[$type])) {
			$result = call_user_func($this->inParamProcess[$type], $value);
		} else {
			$result = $value;
		}
		return $result;
	}

	public function runOperation($params, $user) {
		global $API_VERSION, $current_language, $default_language;
		try {
			if (empty($current_language)) {
				if (!empty($user->column_fields['language'])) {
					$current_language = $user->column_fields['language'];
				} elseif (!empty($default_language)) {
					$current_language = $default_language;
				} else {
					$current_language = 'en_us';
				}
			}
			if (!$this->preLogin) {
				$params['user'] = $user;
				return call_user_func_array($this->handlerMethod, array_values($params));
			} else {
				$userDetails = call_user_func_array($this->handlerMethod, array_values($params));
				if (is_array($userDetails)) {
					return $userDetails;
				} else {
					$this->sessionManager->set('authenticatedUserId', $userDetails->id);
					cbEventHandler::do_action('corebos.login', array($userDetails, $this->sessionManager, 'webservice'));
					global $adb;
					$webserviceObject = VtigerWebserviceObject::fromName($adb, 'Users');
					$userId = vtws_getId($webserviceObject->getEntityId(), $userDetails->id);
					$vtigerVersion = vtws_getVtigerVersion();
					return array('sessionName'=>$this->sessionManager->getSessionId(), 'userId'=>$userId, 'version'=>$API_VERSION, 'vtigerVersion'=>$vtigerVersion);
				}
			}
		} catch (WebServiceException $e) {
			throw $e;
		} catch (Exception $e) {
			throw new WebServiceException(WebServiceErrorCode::$INTERNALERROR, 'Unknown Error while processing request');
		}
	}

	public function encode($param) {
		return call_user_func($this->formatObjects[$this->format]['encode'], $param);
	}

	public function getOperationIncludes() {
		$includes = array();
		$includes[] = $this->handlerPath;
		return $includes;
	}
}
?>
