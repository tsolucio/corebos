<?php
 /*************************************************************************************************
 * Copyright 2019 JPL TSolucio, S.L. -- This file is a part of TSOLUCIO coreBOS Customizations.
 * Licensed under the vtiger CRM Public License Version 1.1 (the "License"); you may not use this
 * file except in compliance with the License. You can redistribute it and/or modify it
 * under the terms of the License. JPL TSolucio, S.L. reserves all rights not expressly
 * granted by the License. coreBOS distributed by JPL TSolucio S.L. is distributed in
 * the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied
 * warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. Unless required by
 * applicable law or agreed to in writing, software distributed under the License is
 * distributed on an "AS IS" BASIS, WITHOUT ANY WARRANTIES OR CONDITIONS OF ANY KIND,
 * either express or implied. See the License for the specific language governing
 * permissions and limitations under the License. You may obtain a copy of the License
 * at <http://corebos.org/documentation/doku.php?id=en:devel:vpl11>
 *************************************************************************************************
 *  Author       : AT Consulting
 *************************************************************************************************/
include 'vendor/autoload.php';
class ElasticsearchEventsHandler extends VTEventHandler {

	/**
	 * @param $handlerType
	 * @param $entityData VTEntityData
	 */
	public function handleEvent($handlerType, $entityData) {
		global $adb;
		$moduleName = $entityData->getModuleName();
		$ip = GlobalVariable::getVariable('ip_elastic_server', '', $moduleName);
		$table = $adb->pquery('select indexname,mapid,fieldlabels,fieldnames from elasticsearch_indexes where module=?', array($moduleName));
		if ($ip != '' && $table && $adb->num_rows($table)>0) {
			require_once 'modules/cbMap/cbMap.php';
			$username = GlobalVariable::getVariable('esusername', '');
			$password = GlobalVariable::getVariable('espassword', '');
			$id = $entityData->getId();
			$indexname = $adb->query_result($table, 0, 0);
			$mapid = $adb->query_result($table, 0, 1);
			$fieldlabels = explode('##', $adb->query_result($table, 0, 2));
			$fieldnames = explode('##', $adb->query_result($table, 0, 3));
			include_once "modules/$moduleName/$moduleName.php";
			$focus = new $moduleName;
			$entityidfield = $focus->table_index;
			$file = 'logs/elasticsearch.log';
			switch ($handlerType) {
				case 'vtiger.entity.aftersave':
					$msg = '==========='.date('Y-m-d H:i:s').'===========';
					error_log($msg."\n", 3, $file);
					if ($mapid != 0 && $mapid != '') {
						$cbMap = cbMap::getMapByID($mapid);
						$results = $cbMap->ConditionQuery(array($id));
						if ($results && !empty($results)) {
							$result = $results[0];
							$resultnew[$entityidfield] = $result[$entityidfield];
							foreach ($fieldnames as $key => $value) {
								$label = $fieldlabels[$key];
								$ui = getUItype($moduleName, $value);
								if ($ui == 69) {
									$attch = $adb->pquery(
										'select vtiger_attachments.attachmentsid,path from vtiger_attachments'
										.' join vtiger_crmentity on vtiger_crmentity.crmid=vtiger_attachments.attachmentsid'
										.' join vtiger_seattachmentsrel where deleted=0 and name=? and vtiger_seattachmentsrel.crmid=?',
										array($result[$value], $id)
									);
									$attid = $adb->query_result($attch, 0, 0);
									$path = $adb->query_result($attch, 0, 1).$attid.'_'.$result[$value];
									try {
										if (file_exists($path)) {
											$parser = new \Smalot\PdfParser\Parser();
											$pdf = $parser->parseFile($path);
											$text = $pdf->getText();
										} else {
											$text = '';
										}
									} catch (Exception $e) {
										$text = '';
									}
									$resultnew[$label] = $text;
								} else {
									$resultnew[$label] = $result[$value];
								}
							}
							error_log(json_encode($resultnew)."\n", 3, $file);
							$endpointUrl = "http://$ip:9200/$indexname/import/_search?pretty";
							$fieldssearch =array('query'=>array('term'=>array($entityidfield => $id)));
							$channel1 = curl_init();
							curl_setopt($channel1, CURLOPT_URL, $endpointUrl);
							curl_setopt($channel1, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
							curl_setopt($channel1, CURLOPT_USERPWD, $username . ':' . $password);
							curl_setopt($channel1, CURLOPT_RETURNTRANSFER, true);
							curl_setopt($channel1, CURLOPT_POST, true);
							curl_setopt($channel1, CURLOPT_POSTFIELDS, json_encode($fieldssearch));
							curl_setopt($channel1, CURLOPT_CONNECTTIMEOUT, 100);
							curl_setopt($channel1, CURLOPT_SSL_VERIFYPEER, false);
							curl_setopt($channel1, CURLOPT_TIMEOUT, 1000);
							$resp = curl_exec($channel1);
							if ($resp===false) {
								error_log("Create $indexname Record\n", 3, $file);
								error_log("ERROR** curl call returned false\n", 3, $file);
								return false;
							}
							$response = json_decode($resp);
							if (isset($response->error)) {
								error_log("Create $indexname Record\n", 3, $file);
								error_log('ERROR** '.$response->error->type.' '.$response->error->reason."\n", 3, $file);
							} else {
								if ($response->hits->total!=0) {
									$eid = $response->hits->hits[0]->_id;
									$endpointUrl = "http://$ip:9200/$indexname/import/$eid";
									$channel11 = curl_init();
									curl_setopt($channel11, CURLOPT_URL, $endpointUrl);
									curl_setopt($channel11, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
									curl_setopt($channel11, CURLOPT_USERPWD, $username . ':' . $password);
									curl_setopt($channel11, CURLOPT_RETURNTRANSFER, true);
									curl_setopt($channel11, CURLOPT_CUSTOMREQUEST, 'PUT');
									curl_setopt($channel11, CURLOPT_POSTFIELDS, json_encode($resultnew));
									curl_setopt($channel11, CURLOPT_CONNECTTIMEOUT, 100);
									curl_setopt($channel11, CURLOPT_SSL_VERIFYPEER, false);
									curl_setopt($channel11, CURLOPT_TIMEOUT, 1000);
									$responseupdate = curl_exec($channel11);
									error_log("Update $indexname Record\n", 3, $file);
									error_log($responseupdate."\n", 3, $file);
								} else {
									$endpointUrl = "http://$ip:9200/$indexname/import";
									$channel11 = curl_init();
									curl_setopt($channel11, CURLOPT_URL, $endpointUrl);
									curl_setopt($channel11, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
									curl_setopt($channel11, CURLOPT_USERPWD, $username . ':' . $password);
									curl_setopt($channel11, CURLOPT_RETURNTRANSFER, true);
									curl_setopt($channel11, CURLOPT_POST, true);
									curl_setopt($channel11, CURLOPT_POSTFIELDS, json_encode($resultnew));
									curl_setopt($channel11, CURLOPT_CONNECTTIMEOUT, 100);
									curl_setopt($channel11, CURLOPT_SSL_VERIFYPEER, false);
									curl_setopt($channel11, CURLOPT_TIMEOUT, 1000);
									$responsecreate = curl_exec($channel11);
									error_log("Create $indexname Record\n", 3, $file);
									error_log($responsecreate."\n", 3, $file);
								}
							}
						}
					}
					break;

				case 'vtiger.entity.beforedelete':
					$msg = '==========='.date('Y-m-d H:i:s').'===========';
					error_log($msg."\n", 3, $file);
					$endpointUrl = "http://$ip:9200/$indexname/import/_search?pretty";
					$fieldssearch =array('query'=>array('term'=>array($entityidfield => $id)));
					$channel1 = curl_init();
					curl_setopt($channel1, CURLOPT_URL, $endpointUrl);
					curl_setopt($channel1, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
					curl_setopt($channel1, CURLOPT_USERPWD, $username . ':' . $password);
					curl_setopt($channel1, CURLOPT_RETURNTRANSFER, true);
					curl_setopt($channel1, CURLOPT_POST, true);
					curl_setopt($channel1, CURLOPT_POSTFIELDS, json_encode($fieldssearch));
					curl_setopt($channel1, CURLOPT_CONNECTTIMEOUT, 100);
					curl_setopt($channel1, CURLOPT_SSL_VERIFYPEER, false);
					curl_setopt($channel1, CURLOPT_TIMEOUT, 1000);
					$resp = curl_exec($channel1);
					if ($resp===false) {
						error_log("Delete $indexname Record\n", 3, $file);
						error_log("ERROR** curl call returned false\n", 3, $file);
						return false;
					}
					$response = json_decode($resp);
					if (isset($response->error)) {
						error_log("Delete $indexname Record $eid\n", 3, $file);
						error_log('ERROR** '.$response->error->type.' '.$response->error->reason."\n", 3, $file);
					} else {
						if ($response->hits->total!=0) {
							$eid = $response->hits->hits[0]->_id;
							$endpointUrl = "http://$ip:9200/$indexname/import/$eid";
							$channel11 = curl_init();
							curl_setopt($channel11, CURLOPT_URL, $endpointUrl);
							curl_setopt($channel11, CURLOPT_RETURNTRANSFER, true);
							curl_setopt($channel11, CURLOPT_USERPWD, $username . ':' . $password);
							curl_setopt($channel11, CURLOPT_CUSTOMREQUEST, 'DELETE');
							curl_setopt($channel11, CURLOPT_CONNECTTIMEOUT, 100);
							curl_setopt($channel11, CURLOPT_SSL_VERIFYPEER, false);
							curl_setopt($channel11, CURLOPT_TIMEOUT, 1000);
							$responsedel = curl_exec($channel11);
							error_log("Delete $indexname Record $eid\n", 3, $file);
							error_log($responsedel."\n", 3, $file);
						} else {
							error_log("Delete no hits\n", 3, $file);
						}
					}
					break;
			}
		}
	}
}

