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
		global $adb, $logbg, $from_wf;
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
		$target_fields = $cbMap->WebserviceMapping($focus2->column_fields, $focus2->column_fields);

		$wsconfig = $target_fields['WSConfig'];
		$url = $wsconfig['wsurl'];
		$method = $wsconfig['wshttpmethod'];
		$methodname = $wsconfig['methodname'];
		$wstype = $wsconfig['wstype'];
		$inputtype = $wsconfig['inputtype'];
		$outputtype = $wsconfig['outputtype'];
		$headers = $wsconfig['wsheader'];
		$wsuser = $wsconfig['wsuser'];
		$wspass = $wsconfig['wspass'];

		if ($wstype == 'REST') {
			$arrconv = array();
			foreach ($target_fields as $key2 => $value2) {
				if ($key2 != 'Response' && $key2 != 'WSConfig') {
					if ($inputtype == 'JSON') {
						$arrconv[$key2] = $value2;
					} elseif ($inputtype == 'URL') {
						$arrconv = "$key2=$value2";
					} elseif ($inputtype == 'URLRESTFUL') {
						$url = str_replace("$$key2", "$value2", $url);
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
				if ($findauth[0] == 'Authorization') {
					$replace = 'Authorization:Bearer:'.$focus2->column_fields[$findauth[2]];
					$headers[$nr] = $replace;
				}
				$nr++;
			}

			if ($inputtype == 'URL') {
				$url.= '?'.$listreq;
			}

			//call REST Webservice
			$channel = curl_init();
			curl_setopt($channel, CURLOPT_URL, $url);
			curl_setopt($channel, CURLOPT_HTTPHEADER, $headers);
			if ($method == 'POST') {
				curl_setopt($channel, CURLOPT_POST, true);
			} else {
				curl_setopt($channel, CURLOPT_CUSTOMREQUEST, "$method");
			}
			if ($inputtype != 'URL') {
				if (in_array('Content-type:application/x-www-form-urlencoded', $headers)) {
					curl_setopt($channel, CURLOPT_POSTFIELDS, http_build_query($listreq));
				} else {
					curl_setopt($channel, CURLOPT_POSTFIELDS, json_encode($listreq));
				}
			}
			curl_setopt($channel, CURLOPT_RETURNTRANSFER, true);
			$respp = curl_exec($channel);
			if (json_decode(json_decode($respp)) != null) {
				$response2 = json_decode(json_decode($respp));
			} else {
				$response2 = json_decode($respp);
			}

			curl_close($channel);

			//write logs for response
			$dateofresponse = date('l jS \of F Y h:i:s A');
			error_log("\n\nResponse header: ".json_encode($headers), 3, $logname.'.log');
			error_log("\n\nResponse: ".$respp, 3, $logname.'.log');
			error_log("\n\n".$dateofresponse, 3, $logname.'.log');

			//write response to record fields
			if (count($target_fields['Response']) > 0) {
				if ($outputtype == 'JSON') {
					foreach ($target_fields['Response'] as $k => $v) {
						$fld = $v['field']['fieldname'];
						$ar = explode(',', $k);
						if (count($ar) > 1) {
							if (is_array($response2)) {
								$r1 = $response2[0];
							} else {
								$r1 = $response2;
							}
							for ($j = 0; $j < count($ar); $j++) {
								$r1 = $r1->$ar[$j];
							}
							$r2 = str_replace('"', '', $r1);
						} else {
							if (is_array($response2)) {
								$r2 = str_replace(array('"','/','\\'), array('',' ',' '), json_encode($response2[0]->$k));
							} else {
								$r2 = str_replace(array('"','/','\\'), array('',' ',' '), json_encode($response2->$k));
							}
						}
						if ($r2 == 'null') {
							$r2 = '';
						}
						$conds[] = "$fld=\"$r2\"";
					}
					$conditions = implode(',', $conds);
					$q = $adb->pquery('select * from vtiger_entityname where modulename=?', array($origin_module));
					$table = $adb->query_result($q, 0, 'tablename');
					$id = $adb->query_result($q, 0, 'entityidfield');
					$adb->pquery("update $table set $conditions where $id=$therecid", array());
				}
			}
		}

		if ($wstype == 'SOAP') {
			$arrconv = array();
			foreach ($target_fields as $key => $value) {
				if ($key != 'Response' && $key != 'WSConfig') {
					$arrconv[$key] = $value;
					$url = str_replace("$key", "$value", $url);
				}
			}

			//write logs for request
			$logname = 'logs/'.$methodname;
			$dateofcall = date('l jS \of F Y h:i:s A');
			$LogContent = "\n*****************\nDate of call $dateofcall";
			error_log($LogContent, 3, $logname.'.log');
			error_log("\nRecordid: ".$therecid, 3, $logname.'.log');
			error_log("\nMethod Called: $method $url", 3, $logname.'.log');
			error_log("\URL: ".$url, 3, $logname.'.log');

			try {
				if ($wsuser != '' && $wspass != '') {
					$options = array(
						'login' => $wsuser,
						'password' => $wspass,
					);
					$soapclient = new SoapClient($url, $options);
				} else {
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
						foreach ($target_fields['Response'] as $k => $v) {
							$fld = $v['field']['fieldname'];
							$ar = explode(',', $k);
							if (count($ar) > 1) {
								if (is_array($response)) {
									$res = $response;
									$r1 = json_encode($res);
									$r1 = json_decode($r1);
								} else {
									$r = $response;
									$r1 = json_encode($r);
								}
								for ($j = 0; $j < count($ar); $j++) {
									$r1 = $res == null ? json_decode($r1) : $r1;
									$r = $r1->$ar[$j];
								}
								$r2 = str_replace('"', '', $r);
							} else {
								if (is_array($response)) {
									foreach ($response as $key => $value) {
										if (is_array($response[$key])) {
											$r = $value[$ar[0]];
											if ($r != null) {
												$r2 = $r;
											}
										} else {
											$r2 = $value;
										}
									}
								} else {
									$r = json_decode($response);
									if ($r->$k != null) {
										$r2 = $r->$k;
									} else {
										foreach ($r as $v1) {
											$r2 = $v1->$ar[0];
										}
									}
								}
							}
							if ($r2 == 'null') {
								$r2 = '';
							}
							$conds[] = "$fld=\"$r2\"";
						}
						$conditions = implode(',', $conds);
						$q = $adb->query('select * from vtiger_entityname where modulename=?', array($origin_module));
						$table = $adb->query_result($q, 0, 'tablename');
						$id = $adb->query_result($q, 0, 'entityidfield');
						$adb->pquery("update $table set $conditions where $id=$therecid", array());
					}
				}

				//write logs for response
				$dateofresponse = date('l jS \of F Y h:i:s A');
				error_log("\n\nResponse header: ".json_encode($options), 3, $logname.'.log');
				error_log("\n\nResponse: ".json_encode($response), 3, $logname.'.log');
				error_log("\n\n".$dateofresponse, 3, $logname.'.log');
			} catch (Exception $e) {
				echo $e->getMessage();
				$dateofresponse = date('l jS \of F Y h:i:s A');
				error_log("\n\Error: ".json_encode($e->getMessage()), 3, $logname.'.log');
				error_log("\n\n".$dateofresponse, 3, $logname.'.log');
			}
		}

		$util->revertUser();
		$logbg->debug('< RunWebservice');
	}
}
?>
