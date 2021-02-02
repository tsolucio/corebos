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
require_once 'modules/com_vtiger_workflow/VTTaskQueue.inc';
require_once 'include/events/include.inc';
require_once 'modules/cbMap/cbMap.php';

class RunWebserviceWorkflowTask extends VTTask {
	public $executeImmediately = true;

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

		$focus2 = CRMEntity::getInstance($origin_module);
		$focus2->retrieve_entity_info($therecid, $origin_module);
		$target_fields = $cbMap->WebserviceMapping($focus2->column_fields, $entity->WorkflowContext);

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
					$url = str_replace('$'.$key2, $value2, $url);
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
				if ($findauth[0] == 'Authorization' || $findauth[0] == 'Token') {
					if (strstr($findauth[1], 'getContext')) {
						$context = str_replace(array('getContext(', ')'), array('', ''), $findauth[1]);
						$token = $entity->getContext();
						$token = $token[$context];
					} else {
						$token = $this->substituteSetting($findauth[1]);
					}
					if ($findauth[0] == 'Token') {
						$replace = 'Token: '.$token;
					} else {
						$replace = 'Authorization:Bearer: '.$token;
					}
					$headers[$nr] = $replace;
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
			if ($method == 'POST') {
				curl_setopt($channel, CURLOPT_POST, true);
			} else {
				curl_setopt($channel, CURLOPT_CUSTOMREQUEST, $method);
			}
			if ($inputtype != 'URL') {
				if ($inputtype=='XML' || in_array('Content-type:application/x-www-form-urlencoded', $headers) || in_array('Content-type:multipart/form-data', $headers)) {
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
						$r2 = '';
						if (is_array($response2)) {
							if (strpos($fld, '.')) {
								$elements = explode('.', $fld);
								$elemresponse = $elements[0];
								$elemfield = $elements[1];
								if (isset($response2[0]->$elemresponse)) {
									if (is_array($response2[0]->$elemresponse)) {
										$elemprop = $elements[2];
										if (count($elements)==2 && isset($response2[0]->$elemresponse[$elemfield])) {
											$r2 = str_replace(array('"','/','\\'), array('',' ',' '), json_encode($response2[0]->$elemresponse[$elemfield]));
										} elseif (count($elements)==3 && isset($response2[0]->$elemresponse[$elemfield]->$elemprop)) {
											$r2 = str_replace(array('"','/','\\'), array('',' ',' '), json_encode($response2[0]->$elemresponse[$elemfield]->$elemprop));
										}
									} elseif (isset($response2[0]->$elemresponse->$elemfield)) {
										$r2 = str_replace(array('"','/','\\'), array('',' ',' '), json_encode($response2[0]->$elemresponse->$elemfield));
									}
								}
							} else {
								if (isset($response2[0]->$fld)) {
									$r2 = str_replace(array('"','/','\\'), array('',' ',' '), json_encode($response2[0]->$fld));
								}
							}
						} else {
							if (strpos($fld, '.')) {
								$elements = explode('.', $fld);
								$elemresponse = $elements[0];
								$elemfield = $elements[1];
								if (isset($response2->$elemresponse)) {
									if (is_array($response2->$elemresponse)) {
									$elemprop = $elements[2];
									if (count($elements)==2 && isset($response2->$elemresponse[$elemfield])) {
											$r2 = str_replace(array('"','/','\\'), array('',' ',' '), json_encode($response2->$elemresponse[$elemfield]));
										} elseif (count($elements)==3 && isset($response2->$elemresponse[$elemfield]->$elemprop)) {
											$r2 = str_replace(array('"','/','\\'), array('',' ',' '), json_encode($response2->$elemresponse[$elemfield]->$elemprop));
										}
									} elseif (isset($response2->$elemresponse->$elemfield)) {
										$r2 = str_replace(array('"','/','\\'), array('',' ',' '), json_encode($response2->$elemresponse->$elemfield));
									}
								}
							} else {
								if (isset($response2->$fld)) {
									$r2 = str_replace(array('"','/','\\'), array('',' ',' '), json_encode($response2->$fld));
								}
							}
						}
						if ((string)$r2 == 'null' || is_null($r2)) {
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
						if ((string)$r2 == 'null' || is_null($r2)) {
							$r2 = '';
						}
						if (!empty($v['field'])) {
							$conds[$v['field']] = $r2;
						}
						$entity->WorkflowContext[$v['context']] = $r2;
					}
				}
				if (count($conds)>0) {
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
					$focus->saveentity($origin_module);
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
				if ($wsuser != '' && $wspass != '') {
					$options = array(
						'login' => $wsuser,
						'password' => $wspass,
					);
					$soapclient = new SoapClient($url, $options);
				} else {
					$options = array();
					$soapclient = new SoapClient($url);
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
				if (count($target_fields['Response']) > 0) {
					if ($outputtype == 'JSON') {
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
								$r = json_decode($response);
								if ($r->$fld != null) {
									$r2 = $r->$fld;
								} else {
									foreach ($r as $v1) {
										$r2 = $v1->$fld;
									}
								}
							}
							if ((string)$r2 == 'null' || is_null($r2)) {
								$r2 = '';
							}
							if (!empty($v['field'])) {
								$conds[$v['field']] = $r2;
							}
							$entity->WorkflowContext[$v['context']] = $r2;
						}
						if (count($conds)>0) {
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
							$focus->saveentity($origin_module);
						}
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
