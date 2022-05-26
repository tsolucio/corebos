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
	 <originname></originname>
   </originmodule>
   <blocks>
	 <block>
	   <label></label>
	   <sequence></sequence>
	   <type></type> ApplicationFields | FieldList | RelatedList | Widget | CodeWithHeader | CodeWithoutHeader
	   <blockid></blockid>
	   <layout>
		 <row>
		   <column>fieldname</column>
		 </row>
	   </layout>
	   <loadfrom></loadfrom> related list label or id | file to load | widget reference
	   <handler_class></handler_class>
	   <handler></handler>
	 </block>
	 .....
   </blocks>
 </map>
 *************************************************************************************************/
include_once 'vtlib/Vtiger/Link.php';

class DetailViewLayoutMapping extends processcbMap {

	private function getRelationIds($origintab, $mlist) {
		global $adb;
		$mlist = (array)$mlist;
		$mlist_ids=array();
		foreach ($mlist as $rellabel) {
			$tid=getTabid($rellabel);
			$sql = 'select relation_id,fieldname from vtiger_relatedlists
				left join vtiger_field on relationfieldid=fieldid where vtiger_relatedlists.tabid=? and ';
			if (is_numeric($tid)) {
				$resid=$adb->pquery($sql.'related_tabid=?', array($origintab, $tid));
			} else {
				$resid=$adb->pquery($sql.'label=?', array($origintab, $rellabel));
			}
			if ($resid) {
				$relid=$adb->fetch_row($resid);
				if ($relid) {
					$mlist_ids[$rellabel]=$relid[0];
					$mlist_ids['fieldname']=$relid[1];
				}
			}
		}
		return $mlist_ids;
	}

	public function processMap($arguments) {
		return $this->convertMap2Array(empty($arguments) ? 0 : $arguments[0]);
	}

	private function convertMap2Array($crmid) {
		global $adb, $current_user;
		$xml = $this->getXMLContent();
		if (empty($xml)) {
			return array();
		}
		$userPrivs = $current_user->getPrivileges();
		$mapping = array();
		$restrictedRelations = array();
		$mapping['blocks'] = array();
		$mapping['origin'] = (string)$xml->originmodule->originname;
		$origintab = getTabid($mapping['origin']);

		foreach ($xml->blocks->block as $value) {
			$block = array();
			$block['type'] = (string)$value->type;
			$block['mode'] = (string)$value->mode;
			$block['position'] = (empty($value->position) ? '' : (string)$value->position);
			$block['sequence'] = (string)$value->sequence;
			$block['label'] = getTranslatedString((string)$value->label, $mapping['origin']);
			$block['loadfrom'] = (string)$value->loadfrom;
			$block['blockid'] = (isset($value->blockid) ? (string)$value->blockid : '');

			if ($block['type']=='RelatedList') {
				if (is_numeric($block['loadfrom']) && !vtlib_isModuleActive($block['loadfrom'])) {
					continue;
				}
				$rels = $this->getRelationIds($origintab, $block['loadfrom']);
				if (empty($block['label'])) {
					$block['label'] = getTranslatedString($block['loadfrom'], $block['loadfrom']);
				}
				$block['relatedfield'] = empty($rels['fieldname']) ? '' : $rels['fieldname'];
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
				$row['onlyonmymodule'] = 1;
				$row['handler_path'] = (isset($value->loadfrom) ? (string)$value->loadfrom : '');
				$row['handler_class'] = (isset($value->handler_class) ? (string)$value->handler_class : '');
				$row['handler'] = (isset($value->handler) ? (string)$value->handler : '');
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
			} elseif ($block['type']=='FieldList') {
				$orgtabid = getTabid($mapping['origin']);
				if (empty(VTCacheUtils::$_fieldinfo_cache[$orgtabid])) {
					getColumnFields($mapping['origin']);
				}
				$block['layout'] = array();
				$idx = 0;
				foreach ($value->layout->row as $v) {
					$block['layout'][$idx] = array();
					foreach ($v->column as $column) {
						if (getFieldVisibilityPermission($mapping['origin'], $current_user->id, (string)$column) != '0') {
							continue;
						}
						$finfo = VTCacheUtils::lookupFieldInfo($orgtabid, (string)$column);
						$lblraw = $finfo['fieldlabel'];
						$block['layout'][$idx][] = array(
							'columnname' => $finfo['columnname'],
							'fieldname' => $finfo['fieldname'],
							'label' => getTranslatedString($lblraw, $mapping['origin']),
							'labelraw' => $lblraw,
							'uitype' => $finfo['uitype'],
						);
					}
					$idx++;
				}
			} elseif ($block['type']=='ApplicationFields') {
				if (empty($block['label'])) {
					$block['label'] = getTranslatedString(getBlockName($block['blockid']), $mapping['origin']);
				}
				if ($userPrivs->hasGlobalWritePermission() || $mapping['origin'] == 'Users' || $mapping['origin'] == 'Emails') {
					$sql = 'SELECT distinct vtiger_field.columnname, vtiger_field.fieldname, vtiger_field.fieldlabel, vtiger_field.uitype, sequence
						FROM vtiger_field
						WHERE vtiger_field.fieldid IN (
							SELECT MAX(vtiger_field.fieldid) FROM vtiger_field WHERE vtiger_field.tabid=? GROUP BY vtiger_field.columnname
						) AND vtiger_field.block=? AND vtiger_field.displaytype IN (1,2,4) AND vtiger_field.presence IN (0,2) ORDER BY sequence';
					$params = array($origintab, $block['blockid']);
				} elseif ($userPrivs->hasGlobalViewPermission()) { // view all
					$profileList = getCurrentUserProfileList();
					$sql = 'SELECT distinct vtiger_field.columnname, vtiger_field.fieldname, vtiger_field.fieldlabel, vtiger_field.uitype, sequence
						FROM vtiger_field
						INNER JOIN vtiger_profile2field ON vtiger_profile2field.fieldid=vtiger_field.fieldid
						WHERE vtiger_field.tabid=? AND vtiger_field.block=? AND vtiger_field.displaytype IN (1,2,4)
							AND vtiger_field.presence IN (0,2) AND vtiger_profile2field.profileid IN (' . generateQuestionMarks($profileList) . ') ORDER BY sequence';
					$params = array($origintab, $block['blockid'], $profileList);
				} else {
					$profileList = getCurrentUserProfileList();
					$sql = 'SELECT distinct vtiger_field.columnname, vtiger_field.fieldname, vtiger_field.fieldlabel, vtiger_field.uitype, sequence
						FROM vtiger_field
						INNER JOIN vtiger_profile2field ON vtiger_profile2field.fieldid=vtiger_field.fieldid
						INNER JOIN vtiger_def_org_field ON vtiger_def_org_field.fieldid=vtiger_field.fieldid
						WHERE vtiger_field.tabid=? AND vtiger_field.block=?
							AND vtiger_field.displaytype IN (1,2,4) AND vtiger_field.presence IN (0,2)
							AND vtiger_profile2field.visible=0 AND vtiger_def_org_field.visible=0
							AND vtiger_profile2field.profileid IN (' . generateQuestionMarks($profileList) . ') ORDER BY sequence';
					$params = array($origintab, $block['blockid'], $profileList);
				}

				$result = $adb->pquery($sql, $params);
				$noofrows = $adb->num_rows($result);
				$block['layout'] = array();
				for ($idx = 0; $idx < $noofrows; $idx++) {
					$lblraw = $adb->query_result($result, $idx, 'fieldlabel');
					$block['layout'][$idx] = array(
						'columnname' => $adb->query_result($result, $idx, 'columnname'),
						'fieldname' => $adb->query_result($result, $idx, 'fieldname'),
						'label' => getTranslatedString($lblraw, $mapping['origin']),
						'labelraw' => $lblraw,
						'uitype' => $adb->query_result($result, $idx, 'uitype'),
					);
				}
			} elseif ($block['type']=='CodeWithHeader' || $block['type']=='CodeWithoutHeader') {
				$block['loadfrom'] = (isset($value->loadfrom) ? (string)$value->loadfrom : '');
				$block['handler_class'] = '';
				$block['handler'] = '';
				if (!empty($block['loadfrom']) && file_exists($block['loadfrom']) && isInsideApplication($block['loadfrom'])) {
					$block['handler_class'] = (isset($value->handler_class) ? (string)$value->handler_class : '');
					$block['handler'] = (isset($value->handler) ? (string)$value->handler : '');
				} else {
					$block['loadfrom'] = '';
				}
			}
			$mapping['blocks'][$block['sequence']] = $block;
		}
		ksort($mapping['blocks']);
		$mapping['restrictedRelations'] = $restrictedRelations;
		return $mapping;
	}
}