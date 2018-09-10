<?php
/*************************************************************************************************
 * Copyright 2015 JPL TSolucio, S.L. -- This file is a part of TSOLUCIO coreBOS Customizations.
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
 *  Module       : coreBOS Events Example helper script
 *  Version      : 1.0
 *  Author       : JPL TSolucio, S. L.
 *************************************************************************************************/
class coreBOSEventsExample extends VTEventHandler {
	private $_moduleCache = array();

	/**
	 * @param $handlerType
	 * @param $entityData VTEntityData
	 */
	public function handleEvent($handlerType, $entityData) {
		global $log;
		switch ($handlerType) {
			case 'corebos.audit.action':
				$log->fatal('corebos.audit.action');
				$log->fatal($entityData);
				break;
			case 'corebos.audit.authenticate':
				$log->fatal('corebos.audit.authenticate');
				$log->fatal($entityData);
				break;
			case 'corebos.audit.login':
				$log->fatal('corebos.audit.login');
				$log->fatal($entityData);
				break;
			case 'corebos.audit.logout':
				$log->fatal('corebos.audit.logout');
				$log->fatal($entityData);
				break;
			case 'corebos.audit.login.attempt':
				$log->fatal('corebos.audit.login.attempt');
				$log->fatal($entityData);
				break;
			case 'corebos.entity.link.before':
				$log->fatal($entityData['sourceModule'].' '.$entityData['sourceRecordId'].' WILL BE related with '.$entityData['destinationModule'].' '.$entityData['destinationRecordId']);
				break;
			case 'corebos.entity.link.after':
				$log->fatal($entityData['sourceModule'].' '.$entityData['sourceRecordId'].' HAS BEEN related with '.$entityData['destinationModule'].' '.$entityData['destinationRecordId']);
				break;
			case 'corebos.entity.link.delete':
				$log->fatal($entityData['sourceModule'].' '.$entityData['sourceRecordId'].' WILL BE UNRELATED with '.$entityData['destinationModule'].' '.$entityData['destinationRecordId']);
				break;
			case 'corebos.footer':
			case 'corebos.header':
			case 'corebos.footer.prefooter':
			case 'corebos.header.premenu':
				// Write something into the footer/header
				echo "<div style='text-align:left;font-size:11px;padding:0 30px;color:rgb(153, 153, 153);'>Extended Header/Footer message</div>";
				break;
		}
	}

	public function handleFilter($handlerType, $parameter) {
		global $currentModule;
		switch ($handlerType) {
			case 'corebos.filter.listview.querygenerator.before':
				// $parameter is the QueryGenerator Object
				$fields = $parameter->getFields();
				// Add homephone field to load from database
				$fields[] = 'homephone';
				$parameter->setFields($fields);
				break;
			case 'corebos.filter.listview.querygenerator.after':
				$fields = $parameter->getFields();
				$newFields = array();
				// remove homephone, because we don't want to have this column in the visible area
				foreach ($fields as $value) {
					if ($value != 'homephone') {
						$newFields[] = $value;
					}
				}
				$parameter->setFields($newFields);
				break;
			case 'corebos.filter.listview.querygenerator.query':
				// show query
				echo $parameter;
				break;
			case 'corebos.filter.listview.render':
				// 0 -> Row
				// 1 -> complete Data from Query
				// 2 -> recordID
				if ($currentModule=='Contacts') {
					# add homephone in a text field in every row
					$parameter[0][] = "<input type='text' id='colorizer_value_homephone_".$parameter[2]."' value='".$parameter[1]["homephone"]."'>";
					$parameter[1][] = $parameter[1]['homephone'];
					$parameter[1]['homephone_text'] = $parameter[1]['homephone'];
				}
				if ($currentModule=='Accounts') {
					$actionpos = count($parameter[0])-1;
					$parameter[0][$actionpos] = '<a href="index.php?action=CallRelatedList&module=Accounts&record='.$parameter[2].'&parenttab=Marketing">+Info</a> | '.$parameter[0][$actionpos];
					//$actionelem = explode(' | ', $action);
				}
				break;
			case 'corebos.filter.listview.header':
				if ($currentModule=='Contacts') {
					$parameter[] = 'Home Phone Text';
				}
				break;
			case 'corebos.filter.listview.filter.show':
				$parameter = ($parameter['viewname'] != 'Contacts Address');  // do not show filter named: Contacts Address
				break;
			case 'corebos.filter.editview.setObjectValues':
				$parameter->column_fields['leadsource']='Cold Call';
				$parameter->column_fields['assistant']='Value from event hook';
				break;
			case 'corebos.filter.link.show':
				$parameter = !($parameter[1][1]=='DETAILVIEW');  // hide all DETAILVIEW links
				break;
			case 'corebos.filter.ModComments.canAdd':
				if ($parameter[0]==2) {
					$parameter[1]=false;  // crmid==2 cannot add comments
				}
				break;
			case 'corebos.filter.ModComments.queryCriteria':
				if ($parameter[0]==2) {
					$parameter[1]=' AND vtiger_crmentity.smownerid!=1';  // crmid==2 cannot see comments from admin 1
				}
				break;
		}
		return $parameter;
	}
}
