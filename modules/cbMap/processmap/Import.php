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
 *  Module       : Business Mappings:: Import
 *  Version      : 5.4.0
 *  Author       : JPL TSolucio, S. L.
 *************************************************************************************************
 * The accepted format is:
<map>
	<targetmodule>
		<targetname>ProductDetail</targetname>
	</targetmodule>
	*
	<fields>
		<field>
			<fieldname>productvin</fieldname>
			<fieldID></fieldID>
			<value>productvin</value>
			<predefined></predefined>
			<Orgfields>
				<Relfield>
					<RelfieldName>cf_880</RelfieldName>
					<RelModule>Products</RelModule>
					<linkfield>productvin</linkfield>
				</Relfield>
			</Orgfields>
		</field>
		...
	</fields>
	<matches>
		<match>
			<fieldname>productdetailname</fieldname>
			<fieldID> </fieldID>
			<value>productdetailname</value>
			<predefined></predefined>
		</match>
	...
	</matches>
	<options>
		<update>FIRST/LAST/ALL</update>
	</options>
</map>
 *************************************************************************************************/

require_once 'modules/cbMap/cbMap.php';
require_once 'modules/cbMap/processmap/processMap.php';

class Import extends processcbMap {
	private $mapping = array();
	private $input = array();
	private $output = array();

	public function processMap($arguments) {
		$this->convertMap2Array();
		$contentok = processcbMap::isXML(htmlspecialchars_decode($this->Map->column_fields['content']));
		if ($contentok !== true) {
			echo '<b>Incorrect Content</b>';
			die();
		}
		$table=$this->initializeImport($arguments[1]);
		$this->doImport($table);
		return $this;
	}

	public function getCompleteMapping() {
		return $this->mapping;
	}

	public function getMapTargetModule() {
		if (isset($this->mapping['targetmodule'])) {
			return $this->mapping['targetmodule'];
		}
		return array();
	}

	public function getMapUpdateFld() {
		if (isset($this->mapping['target'])) {
			return $this->mapping['target'];
		}
		return array();
	}

	public function getMapMatchFld() {
		if (isset($this->mapping['match'])) {
			return $this->mapping['match'];
		}
		return array();
	}

	public function getMapOptions() {
		if (isset($this->mapping['options'])) {
			return $this->mapping['options'];
		}
		return array();
	}

	private function convertMap2Array() {
		$xml = $this->getXMLContent();
		foreach ($xml->fields->field as $k => $v) {
			$fieldname= isset($v->fieldname) ? (String)$v->fieldname : '';
			$value= isset($v->value) ? (String)$v->value : '';
			$predefined= isset($v->predefined) ? (String)$v->predefined : '';
			if (empty($v->Orgfields[0]->Relfield)) {
				$fieldinfo[$fieldname] = array('value'=>$value,'predefined'=>$predefined);
			} elseif (!empty($v->Orgfields[0]->Relfield) && isset($v->Orgfields[0]->Relfield)) {
				$allRelValues=array();
				foreach ($v->Orgfields[0]->Relfield as $key => $value1) {
					if ($key == 'RelfieldName') {
						$allRelValues['fieldname']=(string) $value1;
					}
					if ($key == 'RelModule') {
						$allRelValues['relmodule']=(string) $value1;
					}
					if ($key == 'linkfield') {
						$allRelValues['linkfield']=(string) $value1;
					}
					$allmergeFields[]=$allRelValues;
				}
				$fieldinfo[$fieldname] = array('value'=>$value,'predefined'=>$predefined,"relatedFields" => $allmergeFields);
			}
		}
		foreach ($xml->matches[0] as $key => $value) {
			$fldname = (string) $value->fieldname;
			$fldval = (string) $value->value;
			$match_fields[$fldname] = $fldval;
		}
		foreach ($xml->options[0] as $key => $value) {
			$update_rules[$key] = (string) $value;
		}
		foreach ($xml->targetmodule[0] as $key => $value) {
			$target_module[$key] = (string) $value;
		}
		$mapping= array(
			'target' => $fieldinfo,
			'match' => $match_fields,
			'options'=>$update_rules,
			'targetmodule'=>$target_module,
		);
		$this->mapping = $mapping;
	}

	private function initializeImport($csvfile) {
		global $adb;
		$filename = "import/$csvfile";
		$table = pathinfo($filename);

		$tb=explode("=", $table['filename']);
		$table = "massivelauncher_" . $tb[0];
		$drop = "drop table if exists $table;";
		$adb->query($drop);
		$delimiter = ',';

		$fp = fopen($filename, 'r');
		$frow = fgetcsv($fp, 1000, $delimiter);

		$allHeaders = implode(",", $frow);
		$columns = "`id` INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY, `selected` varchar(3) ";
		foreach ($frow as $column) {
			if ($column=='') {
				$column='lastvalue';
			}
			$columns .= ", `$column` varchar(250)";
		}
		$create = "create table if not exists $table ($columns);";
		$adb->query($create);

		$irow=0;
		while (($data = fgetcsv($fp, 1000, $delimiter)) !== false) {
			$row_vals=implode("','", $data);
			$str="INSERT INTO $table  VALUES ('','','$row_vals')";
			echo $str;
			$adb->query($str);
			$irow++;
		}
		return $table;
	}

	private function doImport($table) {
		include_once 'modules/Users/Users.php';
		global $adb,$current_user;
		$adminUser = Users::getActiveAdminUser();
		$dataQuery = $adb->query("SELECT * FROM $table");
		$module = $this->getMapTargetModule();
		$focus = CRMEntity::getInstance($module);
		$customfld = $focus->customFieldTable;
		$matchFld=$this->getMapMatchFld();
		$updateFld=$this->getMapUpdateFld();
		$options=$this->getMapOptions();
		while ($dataQuery && $data = $adb->fetch_array($dataQuery)) {
			$id = $data['id'];
			$index_q = "SELECT $focus->table_name.$focus->table_index
				FROM $focus->table_name
				INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid=$focus->table_name.$focus->table_index
				INNER JOIN $customfld[0] ON $customfld[0].$customfld[1]=$focus->table_name.$focus->table_index
				WHERE vtiger_crmentity.deleted=0 ";
			foreach ($matchFld as $k => $v) {
				$params[] = $data[$v];
				$index_q.=" AND $k LIKE '" . $data[$v] . "' ";
			}
			$params = array();
			$index_query = $adb->pquery($index_q, $params);
			$nr_rows = $adb->num_rows($index_query);
			if ($nr_rows>0) {
				$allids = array();
				if ($options['update'] == 'FIRST') {
					$allids[] = $adb->query_result($index_query, 0, $focus->table_index);
				} elseif ($options['update'] == 'LAST') {
					$allids[] = $adb->query_result($index_query, $nr_rows - 1, $focus->table_index);
				}
				if ($options['update'] == 'ALL') {
					for ($i = 0; $i < $nr_rows; $i++) {
						$allids[] = $adb->query_result($index_query, $i, $focus->table_index);
					}
				}
				for ($el = 0; $el < count($allids); $el++) {
					$index_result = $adb->query_result($index_query, $el, $focus->table_index);
					if ($nr_rows>0) {
						$focus->retrieve_entity_info($index_result, $module);
					}
					foreach ($updateFld as $upkey => $upVal) {
						$predefined = $upVal['predefined'];
						$value = $upVal['value'];
						if ($predefined == 'AUTONUM') {
							$focus->column_fields[$upkey] = $el;
						} elseif (isset($upVal['relatedFields']) && !empty($upVal['relatedFields'])) {
							$relInformation = $upVal['relatedFields'][0];
							$relModule = $relInformation['relmodule'];
							$linkField = $relInformation['linkfield'];
							$fieldName = $relInformation['fieldname'];
							$otherid = $data[$linkField];

							if (!empty($otherid)) {
								include_once "modules/$relModule/$relModule.php";
								$otherModule = CRMEntity::getInstance($relModule);
								$customfld1 = $otherModule->customFieldTable;
								$index_rel = $adb->query("SELECT $otherModule->table_name.$otherModule->table_index
									FROM $otherModule->table_name
									INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid=$otherModule->table_name.$otherModule->table_index
									INNER JOIN $customfld1[0] ON $customfld1[0].$customfld1[1]=$otherModule->table_name.$otherModule->table_index
									WHERE vtiger_crmentity.deleted=0 and $fieldName='$otherid'");
								$focus->column_fields[$upkey] =$adb->query_result($index_rel, 0);
							}
						} elseif (!empty($data[$value])) {
							$focus->column_fields[$upkey] = $data[$value];
						} else {
							$focus->column_fields[$upkey] = $predefined;
						}
					}
					$focus->mode = 'edit';
					$focus->id = $index_result;
					$handler = vtws_getModuleHandlerFromName($module, $adminUser);
					$meta = $handler->getMeta();
					$focus->column_fields = DataTransform::sanitizeForInsert($focus->column_fields, $meta);
					$focus->column_fields = DataTransform::sanitizeTextFieldsForInsert($focus->column_fields, $meta);

					$focus->saveentity($module);
					if (!empty($focus->id)) {
						$adb->pquery("UPDATE $table SET selected=1 WHERE id=?", array($id));
					}
				}
			} else {
				$focus1=new $module();
				foreach ($updateFld as $upkey => $upVal) {
					$predefined = $upVal['predefined'];
					$value = $upVal['value'];
					if (isset($upVal['relatedFields']) && !empty($upVal['relatedFields'])) {
						$relInformation = $upVal['relatedFields'][0];
						$relModule = $relInformation['relmodule'];
						$linkField = $relInformation['linkfield'];
						$fieldName = $relInformation['fieldname'];
						$otherid = $data[$linkField];

						if (!empty($otherid)) {
							include_once "modules/$relModule/$relModule.php";
							$otherModule = CRMEntity::getInstance($relModule);
							$customfld1 = $otherModule->customFieldTable;
							$index_rel = $adb->query("SELECT $otherModule->table_name.$otherModule->table_index
								FROM $otherModule->table_name
								INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid=$otherModule->table_name.$otherModule->table_index
								INNER JOIN $customfld1[0] ON $customfld1[0].$customfld1[1]=$otherModule->table_name.$otherModule->table_index
								WHERE vtiger_crmentity.deleted=0 and $fieldName='$otherid'");
							$focus1->column_fields[$upkey] =$adb->query_result($index_rel, 0);
						}
					} elseif (!empty($data[$value])) {
						$focus1->column_fields[$upkey] = $data[$value];
					} else {
						$focus1->column_fields[$upkey] = $predefined;
					}
				}
				$focus1->column_fields["assigned_user_id"]=$current_user->id;
				$focus1->saveentity($module);
				$r++;
				if (!empty($focus1->id)) {
					$adb->pquery("UPDATE $table SET selected=1 WHERE id=?", array($id));
				}
			}
		}
	}
}
?>