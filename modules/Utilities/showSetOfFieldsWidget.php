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

	protected $widgetName = 'showSetOfFieldsWidget';

	public function process($context = false) {
		global $current_user;
		$smarty = $this->getViewer();
		$this->context = $context;
		$recordid = $crmid = $this->getFromContext('RECORDID');
		$module = $this->getFromContext('MODULE');
		if (empty($recordid)) {
			$data = null;
		} else {
			$recordid = vtws_getEntityId($module).'x'.$recordid;
			$data = vtws_retrieve($recordid, $current_user);
		}
		$layoutdataArr = array();
		$layoutdataArr['data'] = array();
		$cbmapid = $this->getFromContext('mapid');
		$mapres = cbMap::getMapByID($cbmapid);
		if ($mapres) {
			$map = $mapres->DetailViewLayoutMapping($this->getFromContext('RECORDID'));
			$blockinfo = reset($map['blocks']);
			$type = isset($blockinfo['type']) ? $blockinfo['type'] : '';
			$layoutdataArr['type'] = $type;
			if (!empty($type)) {
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
								'labelraw' => $blockinfo['layout'][$i]['labelraw'],
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
				} elseif ($type == 'RelatedList' && !empty($blockinfo['relatedid'])) {
					$layoutdataArr['relatedlistname'] = $blockinfo['loadfrom'];
					$layoutdataArr['relatedlistid'] = $blockinfo['relatedid'];
					$layoutdataArr['data'] = getRelatedLists($module, null, [$blockinfo['relatedid']]);
					require_once 'include/ListView/RelatedListViewSession.php';
					if (!empty($_REQUEST['selected_header']) && !empty($_REQUEST['relation_id'])) {
						RelatedListViewSession::addRelatedModuleToSession(vtlib_purify($blockinfo['relatedid']), vtlib_purify($blockinfo['loadfrom']));
					}
					$open_related_modules = RelatedListViewSession::getRelatedModulesFromSession();
					$smarty->assign('MODULE', $module);
					$smarty->assign('ID', $crmid);
					$smarty->assign('SELECTEDHEADERS', $open_related_modules);
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
		$smarty->assign('LAYOUTMODULE', $module);
		$smarty->assign('LAYOUT_DATA', $layoutdataArr);
		return $smarty->fetch('showSetOfFields.tpl');
	}
}

if (isset($_REQUEST['action']) && $_REQUEST['action']==$currentModule.'Ajax') {
	$setfield = new showSetOfFields_DetailViewBlock();
	echo $setfield->process($_REQUEST);
}
