<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/
require_once 'modules/com_vtiger_workflow/VTEntityCache.inc';
require_once 'modules/com_vtiger_workflow/VTWorkflowUtils.php';
require_once 'include/events/include.inc';
require_once 'modules/cbMap/cbMap.php';
require_once 'modules/com_vtiger_workflow/expression_functions/application.php';

class RunWebserviceWorkflowTask extends VTTask {
	public $executeImmediately = true;
	public $queable = true;

	public function getFieldNames() {
		return array('bmapid', 'bmapid_display');
	}

	public function doTask(&$entity) {
		global $adb, $logbg, $from_wf, $current_user;
		$from_wf = true;
		$logbg->debug('> RunWebservice');
		$util = new VTWorkflowUtils();
		$util->adminUser();

		$entityId = $entity->getId();
		$recordId = vtws_getIdComponents($entityId);
		$therecid = $recordId[1];
		$origin_module = $entity->getModuleName();
		$mapid = $this->bmapid;
		$cbMap = cbMap::getMapByID($mapid);

		$WSCall_ReturnResultWithNoSave = isset($entity->WorkflowContext['WSCall_ReturnResultWithNoSave']);
		unset($entity->WorkflowContext['WSCall_ReturnResultWithNoSave']);
		if (empty($therecid)) {
			$mergewith = [];
		} else {
			$focus2 = CRMEntity::getInstance($origin_module);
			$focus2->retrieve_entity_info($therecid, $origin_module);
			$mergewith = $focus2->column_fields;
		}
		$target_fields = $cbMap->WebserviceMapping($mergewith, $entity->WorkflowContext);

		$wsconfig = $target_fields['WSConfig'];
		$url = $this->substituteSetting($wsconfig['wsurl']);
		$method = $wsconfig['wshttpmethod'];
		$methodname = $wsconfig['methodname'];
		$wstype = $wsconfig['wstype'];
		$inputtype = $wsconfig['inputtype'];
		$outputtype = $wsconfig['outputtype'];
		$headers = $wsconfig['wsheader'];
		$wsuser = $wsconfig['wsuser'];
		$wspass = $wsconfig['wspass'];
		$certtype = isset($wsconfig['certtype']) ? $wsconfig['certtype'] : null;
		$cert = isset($wsconfig['cert']) ? $wsconfig['cert'] : null;
		$certinsecure = isset($wsconfig['certinsecure']) ? $wsconfig['certinsecure'] : 0;

		$handler = vtws_getModuleHandlerFromName($origin_module, $current_user);
		$meta = $handler->getMeta();

		if ($wstype == 'REST') {
			$arrconv = array();
			foreach ($target_fields['fields'] as $key2 => $value2) {
				if ($inputtype == 'JSON' || $inputtype == 'XML') {
					$arrconv[$key2] = $value2;
				} elseif ($inputtype == 'URL') {
					$arrconv[] = "$key2=$value2";
				} elseif ($inputtype == 'URLRESTFUL') {
					if (strpos($url, '$'.$key2) !== false) {
						$url = str_replace('$'.$key2, $value2, $url);
					} else {
						$arrconv[$key2] = $value2;
					}
				}
			}

			$listreq = $arrconv;

			//write logs for request
			$logname = 'logs/'.$methodname;
			$dateofcall = date('l jS \of F Y h:i:s A');
			$LogContent = "\n*****************\nDate of call $dateofcall";
			error_log($LogContent, 3, $logname.'.log');
			error_log("\nRecordid: ".$therecid, 3, $logname.'.log');
			error_log("\nMethod Called: $method $url", 3, $logname.'.log');
			error_log("\nParameters: ".json_encode($listreq), 3, $logname.'.log');

			//authorization code replacement
			$nr = 0;
			foreach ($headers as $head) {
				$findauth = explode(':', $head);
				if (isset($findauth[1]) && (strpos($findauth[1], 'getContext(')!==false || strpos($findauth[1], 'getSetting(')!==false)) {
					if (strstr($findauth[1], 'getContext')) {
						$context = str_replace(array('getContext(', ')'), array('', ''), $findauth[1]);
						$token = $entity->getContext();
						$token = $token[$context];
					} else {
						$token = $this->substituteSetting($findauth[1]);
					}
					$headers[$nr] = $findauth[0].':'.$token;
				}
				$nr++;
			}

			if ($inputtype == 'URL') {
				$url.= '?'.implode('&', $listreq);
			}

			//call REST Webservice
			$channel = curl_init();
			curl_setopt($channel, CURLOPT_URL, $url);
			curl_setopt($channel, CURLOPT_HTTPHEADER, $headers);
			curl_setopt($channel, CURLOPT_SSL_VERIFYHOST, $certinsecure);
			curl_setopt($channel, CURLOPT_SSL_VERIFYPEER, $certinsecure);
			if ($certtype !== null) {
				curl_setopt($channel, CURLOPT_PROXY_SSLCERTTYPE, $certtype);
			}
			if ($cert !== null) {
				curl_setopt($channel, CURLOPT_SSLCERT, $cert);
			}
			if ($method == 'POST') {
				curl_setopt($channel, CURLOPT_POST, true);
			} else {
				curl_setopt($channel, CURLOPT_CUSTOMREQUEST, $method);
			}
			if ($inputtype != 'URL') {
				if ($inputtype=='XML' || in_array('Content-type:application/x-www-form-urlencoded', $headers)
					|| in_array('Content-type:multipart/form-data', $headers) || in_array('Content-type:text/plain', $headers)
				) {
					curl_setopt($channel, CURLOPT_POSTFIELDS, http_build_query($listreq));
				} else {
					curl_setopt($channel, CURLOPT_POSTFIELDS, json_encode($listreq));
				}
			}
			curl_setopt($channel, CURLOPT_RETURNTRANSFER, true);
			$respp = curl_exec($channel);
			$response2 = json_decode($respp);
			if (!is_object($response2)) {
				$response2 = json_decode(json_decode($respp));
			}

			curl_close($channel);

			//write logs for response
			$dateofresponse = date('l jS \of F Y h:i:s A');
			error_log("\n\nResponse header: ".json_encode($headers), 3, $logname.'.log');
			error_log("\n\nResponse: ".$respp, 3, $logname.'.log');
			error_log("\n\n".$dateofresponse."\n", 3, $logname.'.log');

			//write response to record fields
			if (count($target_fields['Response']) > 0) {
				$conds = array();
				if ($outputtype == 'JSON') {
					foreach ($target_fields['Response'] as $fld => $v) {
						if ($fld == '.') { // they are asking for the full result
							$entity->WorkflowContext[$v['context']] = $conds[$v['field']] = $respp;
							continue;
						}
						$r2 = '';
						if (is_array($response2)) {
							if (strpos($fld, '.')) {
								$r2 = __cb_getfromcontextvalueinarrayobject($response2, $fld);
							} else {
								if (isset($response2[0]->$fld)) {
									$r2 = json_encode($response2[0]->$fld);
								}
							}
						} else {
							if (strpos($fld, '.')) {
								$r2 = __cb_getfromcontextvalueinarrayobject($response2, $fld);
							} else {
								if (isset($response2->$fld)) {
									$r2 = json_encode($response2->$fld);
								}
							}
						}
						if ((!is_array($r2) && (string)$r2 == 'null') || is_null($r2)) {
							$r2 = '';
						}
						if (!empty($v['field'])) {
							$conds[$v['field']] = $r2;
						}
						$entity->WorkflowContext[$v['context']] = $r2;
					}
				}
				if ($outputtype == 'XML') {
					$xml = simplexml_load_string($respp);
					$json = json_encode($xml);
					$response2 = json_decode($json, true);
					foreach ($target_fields['Response'] as $fld => $v) {
						if ($fld == '.') { // they are asking for the full result
							$entity->WorkflowContext[$v['context']] = $conds[$v['field']] = $respp;
							continue;
						}
						if (strpos($fld, '.')) {
							list ($elemresponse, $elemfield) = explode('.', $fld);
							if (is_array($response2[$elemresponse])) {
								$r2 = $response2[$elemresponse][$elemfield];
							} else {
								$r2 = $response2[$fld];
							}
						} else {
							$r2 = $response2[$fld];
						}
						if ((!is_array($r2) && (string)$r2 == 'null') || is_null($r2)) {
							$r2 = '';
						}
						if (!empty($v['field'])) {
							$conds[$v['field']] = $r2;
						}
						$entity->WorkflowContext[$v['context']] = $r2;
					}
				}
				if (!empty($conds) && !$WSCall_ReturnResultWithNoSave) {
					$q = $adb->pquery('select * from vtiger_entityname where modulename=?', array($origin_module));
					$table = $adb->query_result($q, 0, 'tablename');
					$id = $adb->query_result($q, 0, 'entityidfield');
					$focus = new $origin_module();
					$focus->mode = 'edit';
					$focus->retrieve_entity_info($therecid, $origin_module);
					$focus->id=$therecid;
					foreach ($conds as $key => $value) {
						$focus->column_fields[$key] = $value;
					}
					$focus->column_fields = DataTransform::sanitizeRetrieveEntityInfo($focus->column_fields, $meta);
					$hold_ajxaction = isset($_REQUEST['ajxaction']) ? $_REQUEST['ajxaction'] : '';
					$_REQUEST['ajxaction'] = 'Workflow';
					$focus->saveentity($origin_module);
					$_REQUEST['ajxaction'] = $hold_ajxaction;
				}
			}
		}

		if ($wstype == 'SOAP') {
			$arrconv = array();
			foreach ($target_fields['fields'] as $key => $value) {
				$arrconv[$key] = $value;
				$url = str_replace("$key", "$value", $url);
			}

			//write logs for request
			$logname = 'logs/'.$methodname;
			$dateofcall = date('l jS \of F Y h:i:s A');
			$LogContent = "\n*****************\nDate of call $dateofcall";
			error_log($LogContent, 3, $logname.'.log');
			error_log("\nRecordid: ".$therecid, 3, $logname.'.log');
			error_log("\nMethod Called: $method $url", 3, $logname.'.log');
			error_log("\nURL: ".$url, 3, $logname.'.log');

			try {
				$arrContextOptions=stream_context_create(array(
					'ssl' => array(
						'verify_peer' => false,
						'verify_peer_name' => false,
					)
				));
				if ($wsuser != '' && $wspass != '') {
					$options = array(
						'login' => $wsuser,
						'password' => $wspass,
						'stream_context' => $arrContextOptions,
					);
					$soapclient = new SoapClient($url, $options);
				} else {
					$options = array(
						//'soap_version' => SOAP_1_2,
						'trace'      => 1,      // enable trace to view what is happening
						'exceptions' => 0,      // disable exceptions
						'cache_wsdl' => 0,      // disable any caching on the wsdl, encase you alter the wsdl
						'stream_context' => $arrContextOptions,
					);
					$soapclient = new SoapClient($url, $options);
				}

				$response = $soapclient->__soapCall($methodname, array('parameters' => $arrconv));
				$methodResult = $methodname.'Result';
				if (isset($response->$methodResult)) {
					$fields = $response->$methodResult;
				} else {
					$fields = $response;
				}

				if (is_array($fields)) {
					foreach ($fields as $key => $value) {
						if ($value != '') {
							$xml = $value;
						}
					}
					$parseXML = json_decode(json_encode(new SimpleXMLElement($xml)), 1);
				} else {
					$parseXML = $fields;
				}
				if ($outputtype == 'JSON') {
					$response = json_encode($parseXML);
				}

				//write response to record fields
				if (count($target_fields['Response']) > 0 && ($outputtype == 'JSON' || $outputtype == 'XML')) {
					$conds = array();
					foreach ($target_fields['Response'] as $fld => $v) {
						if (is_array($response)) {
							foreach ($response as $key => $value) {
								if (is_array($response[$key])) {
									$r = $value[$fld];
									if ($r != null) {
										$r2 = $r;
									}
								} else {
									$r2 = $value;
								}
							}
						} else {
							if (!is_object($response)) {
								$r = json_decode($response);
							} else {
								$r = $response;
							}
							if (isset($r->$fld) && $r->$fld != null) {
								$r2 = $r->$fld;
							} else {
								if (is_array($r)) {
									foreach ($r as $v1) {
										$r2 = $v1->$fld;
									}
								}
							}
						}
						if (!isset($r2) || (string)$r2 == 'null' || is_null($r2)) {
							$r2 = '';
						}
						if (!empty($v['field'])) {
							$conds[$v['field']] = $r2;
						}
						$entity->WorkflowContext[$v['context']] = $r2;
					}
					if (!empty($conds) && !$WSCall_ReturnResultWithNoSave) {
						$q = $adb->pquery('select * from vtiger_entityname where modulename=?', array($origin_module));
						$table = $adb->query_result($q, 0, 'tablename');
						$id = $adb->query_result($q, 0, 'entityidfield');
						$focus = new $origin_module();
						$focus->mode = 'edit';
						$focus->retrieve_entity_info($therecid, $origin_module);
						$focus->id=$therecid;
						foreach ($conds as $key => $value) {
							$focus->column_fields[$key] = $value;
						}
						$focus->column_fields = DataTransform::sanitizeRetrieveEntityInfo($focus->column_fields, $meta);
						$hold_ajxaction = isset($_REQUEST['ajxaction']) ? $_REQUEST['ajxaction'] : '';
						$_REQUEST['ajxaction'] = 'Workflow';
						$focus->saveentity($origin_module);
						$_REQUEST['ajxaction'] = $hold_ajxaction;
					}
				}
				//write logs for response
				$dateofresponse = date('l jS \of F Y h:i:s A');
				error_log("\n\nResponse header: ".json_encode($options), 3, $logname.'.log');
				error_log("\n\nResponse: ".json_encode($response), 3, $logname.'.log');
			} catch (Exception $e) {
				echo $e->getMessage();
				$dateofresponse = date('l jS \of F Y h:i:s A');
				error_log("\n\nError: ".json_encode($e->getMessage()), 3, $logname.'.log');
			}
			error_log("\n\n".$dateofresponse."\n", 3, $logname.'.log');
		}

		$util->revertUser();
		$logbg->debug('< RunWebservice');
		if ($WSCall_ReturnResultWithNoSave) {
			return $conds;
		}
	}

	public function substituteSetting($url) {
		$start = strpos($url, 'getSetting(');
		if ($start!==false) {
			$end = strpos($url, ')', $start);
			$first = substr($url, 0, $start);
			$setting = substr($url, $start, $end-$start);
			$last =  substr($url, $end+1);
			$setkey = str_replace('getSetting(', '', $setting);
			$value = coreBOS_Settings::getSetting($setkey, '');
			$url = $first.$value.$last;
		}
		return $url;
	}
}
?>
