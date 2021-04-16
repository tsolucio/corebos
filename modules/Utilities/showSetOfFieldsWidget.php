<?php
/*************************************************************************************************
 * Copyright 2021 JPL TSolucio, S.L. -- This file is a part of TSOLUCIO coreBOS customizations.
 * You can copy, adapt and distribute the work under the "Attribution-NonCommercial-ShareAlike"
 * Vizsage Public License (the "License"). You may not use this file except in compliance with the
 * License. Roughly speaking, non-commercial users may share and modify this code, but must give credit
 * and share improvements. However, for proper details please read the full License, available at
 * http://vizsage.com/license/Vizsage-License-BY-NC-SA.html and the handy reference for understanding
 * the full license at http://vizsage.com/license/Vizsage-Deed-BY-NC-SA.html. Unless required by
 * applicable law or agreed to in writing, any software distributed under the License is distributed
 * on an  "AS IS" BASIS, WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and limitations under the
 * License terms of Creative Commons Attribution-NonCommercial-ShareAlike 3.0 (the License).
 *************************************************************************************************/

// BA block://showSetOfFieldsWidget:modules/Utilities/showSetOfFieldsWidget.php:record_id=$RECORD$&mapid=XXXXXX

require_once 'include/utils/utils.php';
require_once 'modules/Vtiger/DeveloperWidget.php';
require_once 'modules/cbMap/cbMap.php';
require_once 'include/utils/CommonUtils.php';
require_once 'data/CRMEntity.php';
require_once 'include/Webservices/DescribeObject.php';
global $currentModule;

class showSetOfFieldsWidget {
	public static function getWidget($name) {
		return (new showSetOfFields_DetailViewBlock());
	}
}

class showSetOfFields_DetailViewBlock extends DeveloperBlock {
	private $widgetName = 'showSetOfFieldsWidget';
	public function process($context = false) {
		global $adb, $current_user, $log;
		$this->context = $context;
		$recordid = $this->getFromContext('RECORDID');
		$module = $this->getFromContext('MODULE');
		$tabid = getTabid($module);
		$recordId = vtws_getEntityId($module).'x'.$recordid;
		$data = vtws_retrieve($recordId, $current_user);
		$cbmapid = $this->getFromContext('mapid');
		$mapres = cbMap::getMapByID($cbmapid);
		if ($mapres) {
			$xmlcontent = $mapres->column_fields['contentjson'];
			$decodedcontent = html_entity_decode($xmlcontent);
			$decodedcontent = json_decode($decodedcontent, true);
			$layoutdataArr = array();
			$type =  isset($decodedcontent['blocks']['block']['type']) ? $decodedcontent['blocks']['block']['type'] : '';
			$layoutdataArr['type'] = $type;
			$layoutdataArr['section_header'] = 'Generated from DetailView Layout Map';
			if (!empty($type)) {
				$layoutdataArr['data'] = array();
				if ($type == 'ApplicationFields' && $data) {
					$blockid = isset($decodedcontent['blocks']['block']['blockid']) ? $decodedcontent['blocks']['block']['blockid']: '';
					if (!empty($blockid)) {
						$blocklabel = getBlockName($blockid);
						$layoutdataArr['blocklabel'] = $blocklabel;
						$sql = 'select * from vtiger_field where vtiger_field.tabid=? and vtiger_field.block = ? 
							and vtiger_field.displaytype in (1,3,4) order by sequence';
						$result = $adb->pquery($sql, array($tabid, $blockid));
						$noOfRows = $adb->num_rows($result);
						if ($noOfRows > 0) {
							for ($i=0; $i < $noOfRows; $i++) {
								$fieldname = $adb->query_result($result, $i, 'fieldname');
								$info = getFieldDetails($fieldname, $module, $data);
								if (!empty($info)) {
									$layoutdataArr['data'][$i] = $info;
								}
							}
						}
					}
				} elseif ($type == 'FieldList') {
					if (isset($decodedcontent['blocks']['block']['layout']['row']) && !empty($decodedcontent['blocks']['block']['layout']['row'])) {
						$rowdetail = $decodedcontent['blocks']['block']['layout']['row'];
						$y = 0;
						foreach ($rowdetail as $row) {
							$layoutdataArr['data'][$y] = isset($layoutdataArr['data'][$y]) ? $layoutdataArr['data'][$y] : array();
							if (!empty($row['column'])) {
								$columns = $row['column'];
								if (!is_array($columns)) {
									$info = getFieldDetails($columns, $module, $data);
									if (!empty($info)) {
										$layoutdataArr['data'][$y][] = getFieldDetails($columns, $module, $data);
									}
								}
								if (is_array($columns) && count($columns) > 0) {
									for ($i=0; $i < count($columns); $i++) {
										$fieldname = $columns[$i];
										$info = getFieldDetails($fieldname, $module, $data);
										if (!empty($info)) {
											$layoutdataArr['data'][$y][] = $info;
										}
									}
								}
							} elseif (!isset($row['column']) && is_array($row)) {
								for ($x=0; $x < count($row); $x++) {
									$fieldname = $row[$i];
									$info = getFieldDetails($fieldname, $module, $data);
									if (!empty($info)) {
										$layoutdataArr['data'][$y][] = $info;
									}
								}
							} else {
								$info = getFieldDetails($row, $module, $data);
								if (!empty($info)) {
									$layoutdataArr['data'][$y][] = $info;
								}
							}
							$y++;
						}
					}
				} elseif ($type == 'Widget') {
					$loadfrom = isset($decodedcontent['blocks']['block']['loadfrom']) ? $decodedcontent['blocks']['block']['loadfrom']: '';
					if (!empty($loadfrom)) {
						$details = explode(':', $loadfrom);
						if (strpos($details[1], '//') !== false && isset($details[2]) && file_exists($details[2])) {
							$filepath = $details[2];
							include $filepath;
							$classname = trim(str_replace('//', '', $details[1]));
							$classinstance = new $classname();
							$layoutdataArr['data'] = $classinstance->process();
						}
					}
				} elseif ($type == 'CodeWithHeader') {
					$dataArr = !empty($decodedcontent['blocks']['block']) ? $decodedcontent['blocks']['block']: array();
					if (isset($dataArr['loadfrom']) && !empty($dataArr['loadfrom'])) {
						$layoutdataArr['casetype'] = 'LOADFROM_ISSET';
						$layoutdataArr['data'] = trim($dataArr['loadfrom']);
					}
					if (isset($dataArr['handler_path']) && !empty($dataArr['handler_path'])) {
						$handlerpath = $dataArr['handler_path'];
						if (isset($dataArr['handler_class']) && !empty($dataArr['handler_class']) && isset($dataArr['handler']) && !empty($dataArr['handler'])) {
							$handlerclass = $dataArr['handler_class'];
							$handler = $dataArr['handler'];
							if (file_exists($handlerpath)) {
								include $handlerpath;
								$classhandler = new $handlerclass();
								$response = $classhandler->$handler();
								$layoutdataArr['data'] = formatDatatoDisplay($response);
								$layoutdataArr['casetype'] = 'HANDLER_ISSET';
							}
						}
						if (empty($dataArr['handler_class']) && empty($dataArr['handler'])) {
							include $handlerpath;
						}
					}
					if (isset($dataArr['loadcode']) && !empty($dataArr['loadcode'])) {
						$codes = $dataArr['loadcode'];
						$layoutdataArr['casetype'] = 'LOADCODE_ISSET';
						try {
							eval($codes);
						} catch (Exeption $e) {
							$log->debug('debug > showSetOfFields_DetailViewBlock');
						}
					}
				} elseif ($type == 'CodeWithoutHeader') {
					$dataArr = !empty($decodedcontent['blocks']['block']) ? $decodedcontent['blocks']['block']: array();
					if (isset($dataArr['handler_path']) && !empty($dataArr['handler_path'])) {
						$handlerpath = $dataArr['handler_path'];
						if (isset($dataArr['handler_class']) && !empty($dataArr['handler_class']) && isset($dataArr['handler']) && !empty($dataArr['handler'])) {
							$handlerclass = $dataArr['handler_class'];
							$handler = $dataArr['handler'];
							if (file_exists($handlerpath)) {
								include $handlerpath;
								$classhandler = new $handlerclass();
								$response = $classhandler->$handler();
								$layoutdataArr['data'] = formatDatatoDisplay($response);
								$layoutdataArr['casetype'] = 'HANDLER_ISSET';
							}
						}
						if (empty($dataArr['handler_class']) && empty($dataArr['handler'])) {
							include $handlerpath;
						}
					}
					if (isset($dataArr['loadfrom']) && !empty($dataArr['loadfrom'])) {
						$layoutdataArr['casetype'] = 'LOADFROM_ISSET';
						$layoutdataArr['data'] = trim($dataArr['loadfrom']);
					}
					if (isset($dataArr['loadcode']) && !empty($dataArr['loadcode'])) {
						$codes = $dataArr['loadcode'];
						$layoutdataArr['casetype'] = 'LOADCODE_ISSET';
						try {
							eval($codes);
						} catch (Exeption $e) {
							$log->debug('debug > showSetOfFields_DetailViewBlock');
						}
					}
				}
			}
		}
		if (isset($layoutdataArr['data']['type'])) {
			unset($layoutdataArr['data']['type']);
		}
		if (empty($layoutdataArr['data'])) {
			unset($layoutdataArr['data']);
		}
		$module = $this->getFromContext('MODULE');
		$smarty = $this->getViewer();
		$smarty->assign('LAYOUTMODULE', $module);
		$smarty->assign('LAYOUT_DATA', $layoutdataArr);
		return $smarty->fetch('showSetOfFields.tpl');
	}
}

if (isset($_REQUEST['action']) && $_REQUEST['action']==$currentModule.'Ajax') {
	$setfield = new showSetOfFields_DetailViewBlock();
	echo $setfield->process($_REQUEST);
}

function formatDatatoDisplay($data) {
	$datatype = gettype($data);
	switch ($datatype) {
		case 'array':
		case 'object':
			return json_encode($data);
			break;
		case 'string':
		case 'double':
		case 'integer':
		default:
			return $data;
			break;
	}
}

function getFieldDetails($fieldname, $module, $data) {
	global $current_user;
	$response = array();
	$wsfieldsinfo = vtws_describe($module, $current_user);
	$fieldsinfo = $wsfieldsinfo['fields'];
	foreach ($fieldsinfo as $ret => $finfo) {
		if ($finfo['name']==$fieldname) {
			break;
		}
	}
	$label = $fieldsinfo[$ret]['label'];
	$uitype = isset($fieldsinfo[$ret]['uitype']) ? $fieldsinfo[$ret]['uitype']: '';
	if (isset($fieldsinfo[$ret]['uitype']) && ($fieldsinfo[$ret]['uitype']==10 || $fieldsinfo[$ret]['uitype']==52)) {
		$refmod = $fieldsinfo[$ret]['type']['refersTo'][0];
		$rmod = CRMEntity::getInstance($refmod);
		$WSCodeID = vtws_getEntityId($refmod);
		$fieldsinfo[$ret]['searchin'] = $refmod;
		$fieldsinfo[$ret]['searchby'] = $refmod.$rmod->list_link_field;
		$fieldsinfo[$ret]['searchwsid'] = $WSCodeID;
		$index = $fieldname.'ename';
		$fieldval = '';
		if (isset($data[$index]['reference'])) {
			$fieldval = $data[$index]['reference'];
		}
		$response = array('label'=>$label, 'value'=>$fieldval, 'uitype' => $uitype);
	} else {
		$response = array('label'=>$label, 'value'=>$data[$fieldname], 'uitype' => $uitype);
	}
	return $response;
}