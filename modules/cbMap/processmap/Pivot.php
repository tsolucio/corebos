<?php
/*************************************************************************************************
 * Copyright 2021 JPL TSolucio, S.L. -- This file is a part of TSOLUCIO coreBOS Customizations.
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
 *  Module       : Business Mappings:: Pivot View Mapping
 *  Version      : 1.0
 *************************************************************************************************
 * The accepted format is:
<map>
	<module>Module name</module>
	<filter>filter name</filter>
	<aggregate>aggregator</aggregate>
	<rendererName>Multifact Aggregators</rendererName>
	<aggregatorName>GT Table Heatmap and Barchart</aggregatorName>
	<aggregations>
		<aggregation>
			<aggType>Sum</aggType>
			<arguments>estimated_effort</arguments>
			<name>effort</name>
		</aggregation>
	</aggregations>
	<rows>
		<row>
			<name>value of module field name</name>
			<label>label of field in the table</label>
		</row>
	</rows>
	<cols>
		<col>
			<name>value of module field name</name>
			<label>label of field in the table</label>
		</col>
	</cols>
</map>
 *************************************************************************************************/

class Pivot extends processcbMap {
	private $mapping = array();

	public function processMap($arguments) {
		return $this->convertMap2Array();
	}

	private function convertMap2Array() {
		$xml = $this->getXMLContent();
		if (empty($xml) || empty($xml->module)) {
			return array();
		}
		$i = 0;
		$j = 0;
		$k = 0;
		$this->mapping['module'] = (string)$xml->module;
		$this->mapping['aggregate'] = (string)$xml->aggregate;
		$this->mapping['aggregatorName'] = (string)$xml->aggregatorName;
		$this->mapping['rendererName'] = (string)$xml->rendererName;
		$customView = new CustomView($this->mapping['module']);
		$rows = array();
		$aggregations = array();
		$this->mapping['filter'] = $customView->getViewIdByName((string)$xml->filter, $this->mapping['module']);
		if (isset($xml->aggregations) && is_object($xml->aggrgations)) {
			foreach ($xml->aggregations as $v) {
				foreach ($v->aggregation as $x) {
					$aggregations[$k]['aggType'] = (string)$x->aggType;
					$aggregations[$k]['arguments'] = [(string)$x->arguments];
					$aggregations[$k]['name'] = (string)$x->name;
					$k++;
				}
			}
		}
		if (isset($xml->rows) && is_object($xml->rows)) {
			foreach ($xml->rows as $v) {
				foreach ($v->row as $x) {
					$rows[$i]['name'] = (string)$x->name;
					$rows[$i]['label'] = (string)$x->label;
					$i++;
				}
			}
		}
		if (isset($xml->cols) && is_object($xml->cols)) {
			foreach ($xml->cols as $v) {
				foreach ($v->col as $x) {
					$cols[$j]['name'] = (string)$x->name;
					$cols[$j]['label'] = (string)$x->label;
					$j++;
				}
			}
		}
		$this->mapping['rows'] = $rows;
		$this->mapping['cols'] = $cols;
		$this->mapping['aggregations'] = $aggregations;
		return $this->mapping;
	}
}
?>
