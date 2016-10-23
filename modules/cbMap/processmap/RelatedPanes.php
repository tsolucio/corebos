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
        <type></type> RelatedList | Widget | CodeBlock | FullControl
        <loadfrom></loadfrom> related list label or id | file to load | wdiget reference
       </block>
      </blocks>
    </pane>
    .....
  </panes>
 </map>
 *************************************************************************************************/

class RelatedPanes extends processcbMap {

	function getRelationIds($origintab,$mlist) {
		global $adb;
		if (!is_array($mlist)) $mlist = array($mlist);
		$mlist_ids=array();
		foreach ($mlist as $rellabel) {
			$tid=getTabid($rellabel);
			if (is_numeric($tid)) {
				$resid=$adb->pquery('select relation_id,label from vtiger_relatedlists where tabid=? and related_tabid=?',array($origintab,$tid));
			} else {
				$resid=$adb->pquery('select relation_id,label from vtiger_relatedlists where tabid=? and label=?',array($origintab,$rellabel));
			}
			if($resid) {
				$relid=$adb->fetch_row($resid);
				if ($relid) $mlist_ids[$rellabel]=$relid[0];
			}
		}
		return $mlist_ids;
	}

	function processMap() {
		return $this->convertMap2Array();
	}

	function convertMap2Array() {
		$xml = $this->getXMLContent();
		$mapping=array();
		$mapping['origin'] = (String)$xml->originmodule->originname;
		$origintab=getTabid($mapping['origin']);
		$mapping['panes'] = array();
		foreach($xml->panes->pane as $k=>$v) {
			$pane = array('label'=>getTranslatedString((String)$v->label,$mapping['origin']));
			$pane['blocks'] = $restrictedRelations = array();
			if (isset($v->defaultMoreInformation)) {
				$pane['label'] = getTranslatedString('LBL_MORE').' '.getTranslatedString('LBL_INFORMATION');
				$rltb = getRelatedLists($mapping['origin'], '');
				$seq=0;
				foreach ($rltb as $label=>$relinfo) {
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
				foreach($v->blocks->block as $key=>$value) {
					$block = array();
					$block['type'] = (String)$value->type;
					$block['sequence'] = (String)$value->sequence;
					$block['label'] = getTranslatedString((String)$value->label,$mapping['origin']);
					$block['loadfrom'] = (String)$value->loadfrom;
					if ($block['type']=='RelatedList') {
						$rels = $this->getRelationIds($origintab,$block['loadfrom']);
						$block['relatedid'] = $rels[$block['loadfrom']];
						if (empty($block['label'])) $block['label'] = getTranslatedString($block['loadfrom'],$block['loadfrom']);
						$restrictedRelations[] = $rels[$block['loadfrom']];
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