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
 *  Author       : JPL TSolucio, S. L.
 *************************************************************************************************/
class UtilitiesEventsHandler extends VTEventHandler {

	/**
	 * @param $handlerType
	 * @param $entityData VTEntityData
	 */
	public function handleEvent($handlerType, $entityData) {
	}

	public function handleFilter($handlerType, $parameter) {
		if ($handlerType=='corebos.filter.listview.querygenerator.before' && GlobalVariable::getVariable('RecordVersioningModules', '')==1) {
			global $adb;
			$mod = CRMEntity::getInstance('GlobalVariable');
			$recexists = $adb->pquery(
				'select module_list from vtiger_globalvariable inner join '.$mod->crmentityTable.' as ce on ce.crmid=globalvariableid where ce.deleted=0 and gvname=?',
				array('RecordVersioningModules')
			);
			if ($adb->num_rows($recexists) > 0) {
				$modulelist = explode(Field_Metadata::MULTIPICKLIST_SEPARATOR, $adb->query_result($recexists, 0, 'module_list'));
				// $parameter is the QueryGenerator Object
				if (in_array($parameter->getModule(), $modulelist)) {
					$parameter->addCondition('revisionactiva', 1, 'e', 'and');
				}
			}
		}
		return $parameter;
	}
}

