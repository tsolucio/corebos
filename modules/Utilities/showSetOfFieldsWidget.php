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
require_once 'include/fields/metainformation.php';
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
			$map = $mapres->DetailViewLayoutMapping($this->getFromContext('RECORDID'));
			$blockinfo = reset($map['blocks']);
			$xmlcontent = $mapres->column_fields['contentjson'];
			$decodedcontent = html_entity_decode($xmlcontent);
			$decodedcontent = json_decode($decodedcontent, true);
			$layoutdataArr = array();
			$type = isset($blockinfo['type']) ? $blockinfo['type'] : '';
			$layoutdataArr['type'] = $type;
			if (!empty($type)) {
				$layoutdataArr['data'] = array();
				if ($type == 'ApplicationFields' && $data) {
					$blockid = isset($blockinfo['blockid']) ? $blockinfo['blockid'] : '';
					$dvrecord = $this->getFromContext('dvrecord');
					if (!empty($blockid) && !empty($dvrecord)) {
						$layoutdataArr['blocklabel'] = $blockinfo['label'];
						$dvdata = vtws_retrieve($dvrecord, $current_user);
						$layoutdataArr['blockmodule'] = $this->getFromContext('dvmodule');
						$noOfRows = count($blockinfo['layout']);
						for ($i=0; $i < $noOfRows; $i++) {
							$uitype = $blockinfo['layout'][$i]['uitype'];
							$fname = $blockinfo['layout'][$i]['fieldname'];
							if (in_array($uitype, Field_Metadata::RELATION_TYPES)) {
								$value = isset($dvdata[$fname.'ename']) ? $dvdata[$fname.'ename']['reference'] : '';
							} else {
								$value = $dvdata[$fname];
							}
							$layoutdataArr['data'][$i] = array(
								'label' => $blockinfo['layout'][$i]['label'],
								'uitype' => $uitype,
								'value' => $value
							);
						}
					}
				} elseif ($type == 'FieldList') {
					$layoutdataArr['blocklabel'] = $blockinfo['label'];
					$layoutdataArr['blockmodule'] = $this->getFromContext('dvmodule');
					$dvrecord = $this->getFromContext('dvrecord');
					if (!empty($dvrecord)) {
						$dvdata = vtws_retrieve($dvrecord, $current_user);
						foreach ($blockinfo['layout'] as &$row) {
							foreach ($row as &$column) {
								if (in_array($column['uitype'], Field_Metadata::RELATION_TYPES)) {
									$value = isset($dvdata[$column['fieldname'].'ename']) ? $dvdata[$column['fieldname'].'ename']['reference'] : '';
								} else {
									$value = $dvdata[$column['fieldname']];
								}
								$column['value'] = $value;
							}
						}
						$layoutdataArr['data'] = $blockinfo['layout'];
					}
				} elseif ($type == 'Widget') {
					$layoutdataArr['data'] = $blockinfo['instance'];
				} elseif ($type == 'CodeWithHeader' || $type == 'CodeWithoutHeader') {
					$layoutdataArr['data'] = '';
					$layoutdataArr['label'] = $blockinfo['label'];
					if (!empty($blockinfo['loadfrom'])) {
						if (empty($blockinfo['handler_class']) || empty($blockinfo['handler'])) {
							ob_start();
							include_once $blockinfo['loadfrom'];
							$layoutdataArr['data'] = ob_get_contents();
							ob_end_clean();
						} else {
							$handlerclass = $blockinfo['handler_class'];
							$handler = $blockinfo['handler'];
							include_once $blockinfo['loadfrom'];
							$classhandler = new $handlerclass();
							$layoutdataArr['data'] = $classhandler->$handler();
						}
					}
				}
			}
		}
		if (is_array($layoutdataArr['data']) && isset($layoutdataArr['data']['type'])) {
			unset($layoutdataArr['data']['type']);
		}
		if (empty($layoutdataArr['data'])) {
			unset($layoutdataArr['data']);
		}
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