<?php
/*************************************************************************************************
 * Copyright 2016 JPL TSolucio, S.L. -- This file is a part of TSOLUCIO coreBOS Customizations.
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
 *  Module       : Business Mappings:: Related Panes
 *  Version      : 1.0
 *  Author       : JPL TSolucio, S. L.
 *************************************************************************************************
 * The accepted format is:
 <map>
  <originmodule>
	<originname>Accounts</originname>
  </originmodule>
  <panes>
	<pane>
	  <label></label>
	  <sequence></sequence>
	  <defaultMoreInformation></defaultMoreInformation> special marker to get default application more information, if present the blocks sections is ignored
	  <blocks>
	   <block>
		<label></label>
		<sequence></sequence>
		<type></type> RelatedList | Widget | CodeWithHeader | CodeWithoutHeader
		<loadfrom></loadfrom> related list label or module id | file to load | widget reference
		<loadphp></loadphp>
		<handler_path></handler_path>
		<handler_class></handler_class>
		<handler></handler>
	   </block>
	  </blocks>
	</pane>
	.....
  </panes>
 </map>
 *************************************************************************************************/
include_once 'vtlib/Vtiger/Link.php';

class RelatedPanes extends processcbMap {

	private function getRelationIds($origintab, $mlist) {
		global $adb;
		$mlist = (array)$mlist;
		$mlist_ids=array();
		foreach ($mlist as $rellabel) {
			$tid=getTabid($rellabel);
			if (is_numeric($tid)) {
				$resid=$adb->pquery('select relation_id,label from vtiger_relatedlists where tabid=? and related_tabid=?', array($origintab,$tid));
			} else {
				$resid=$adb->pquery('select relation_id,label from vtiger_relatedlists where tabid=? and label=?', array($origintab,$rellabel));
			}
			if ($resid) {
				$relid=$adb->fetch_row($resid);
				if ($relid) {
					$mlist_ids[$rellabel]=$relid[0];
				}
			}
		}
		return $mlist_ids;
	}

	public function processMap($arguments) {
		return $this->convertMap2Array($arguments[0]);
	}

	private function convertMap2Array($crmid) {
		global $current_user;
		$xml = $this->getXMLContent();
		$mapping=array();
		$mapping['origin'] = (String)$xml->originmodule->originname;
		$origintab=getTabid($mapping['origin']);
		$mapping['panes'] = array();
		foreach ($xml->panes->pane as $k => $v) {
			$pane = array('label'=>getTranslatedString((String)$v->label, $mapping['origin']));
			$pane['blocks'] = $restrictedRelations = array();
			if (isset($v->defaultMoreInformation)) {
				$pane['label'] = getTranslatedString('LBL_MORE').' '.getTranslatedString('LBL_INFORMATION');
				$rltb = getRelatedLists($mapping['origin'], '');
				$seq=0;
				foreach ($rltb as $label => $relinfo) {
					$block = array();
					$block['type'] = 'RelatedList';
					$block['sequence'] = $seq++;
					$block['label'] = $label;
					$block['loadfrom'] = $label;
					$block['relatedid'] = $relinfo['relationId'];
					$pane['blocks'][$block['sequence']] = $block;
				}
				$pane['restrictedRelations'] = null;
			} else {
				foreach ($v->blocks->block as $key => $value) {
					$block = array();
					$block['type'] = (String)$value->type;
					$block['sequence'] = (String)$value->sequence;
					$block['label'] = getTranslatedString((String)$value->label, $mapping['origin']);
					$block['loadfrom'] = (String)$value->loadfrom;
					$block['loadphp'] = (isset($value->loadphp) ? (String)$value->loadphp : '');
					if ($block['type']=='RelatedList') {
						if (is_numeric($block['loadfrom']) && !vtlib_isModuleActive($block['loadfrom'])) {
							continue;
						}
						$rels = $this->getRelationIds($origintab, $block['loadfrom']);
						if (empty($block['label'])) {
							$block['label'] = getTranslatedString($block['loadfrom'], $block['loadfrom']);
						}
						if (!empty($rels[$block['loadfrom']])) {
							$block['relatedid'] = $rels[$block['loadfrom']];
							$restrictedRelations[] = $rels[$block['loadfrom']];
						} else {
							$block['relatedid'] = 0;
						}
					} elseif ($block['type']=='Widget') {
						$instance = new Vtiger_Link();
						$row['tabid'] = $origintab;
						$row['linkid'] = 0;
						$row['linktype'] = 'DETAILVIEWWIDGET';
						$row['linklabel'] = $block['label'];
						if ($block['label']=='DetailViewBlockCommentWidget') {
							$block['label'] = getTranslatedString('ModComments', 'ModComments');
						}
						$row['linkurl']  = decode_html($block['loadfrom']);
						$row['linkicon'] = '';
						$row['sequence'] = $block['sequence'];
						//$row['status'] = '';
						$row['handler_path'] = (isset($value->handler_path) ? (String)$value->handler_path : '');
						$row['handler_class'] = (isset($value->handler_class) ? (String)$value->handler_class : '');
						$row['handler'] = (isset($value->handler) ? (String)$value->handler : '');
						$instance->initialize($row);
						if (!empty($row['handler_path']) && isInsideApplication($row['handler_path'])) {
							checkFileAccessForInclusion($row['handler_path']);
							require_once $row['handler_path'];
							$linkData = new Vtiger_LinkData($instance, $current_user);
							$ignore = call_user_func(array($row['handler_class'], $row['handler']), $linkData);
							if (!$ignore) {
								continue; // Ignoring Link
							}
						}
						$strtemplate = new Vtiger_StringTemplate();
						$strtemplate->assign('MODULE', $mapping['origin']);
						$strtemplate->assign('RECORD', $crmid);
						$instance->linkurl = $strtemplate->merge($instance->linkurl);
						$block['instance'] = $instance;
					}
					$pane['blocks'][$block['sequence']] = $block;
				}
				$pane['restrictedRelations'] = $restrictedRelations;
			}
			ksort($pane['blocks']);
			$mapping['panes'][(Integer)$v->sequence]=$pane;
		}
		ksort($mapping['panes']);
		return $mapping;
	}
}
?>
