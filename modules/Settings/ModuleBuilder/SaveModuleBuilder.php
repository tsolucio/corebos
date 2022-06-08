<?php
/*************************************************************************************************
 * Copyright 2020 JPL TSolucio, S.L. -- This file is a part of TSOLUCIO coreBOS Customizations.
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
*************************************************************************************************/

function SaveModuleBuilder($step) {
	global $mod_strings,$adb, $current_user;
	$moduleid = $_COOKIE['ModuleBuilderID'];
	switch ($step) {
		case '1':
			$modSql = $adb->pquery('SELECT * FROM vtiger_modulebuilder WHERE modulebuilder_name=?', array(
				vtlib_purify($_REQUEST['modulename'])
			));
			$modExsists = $adb->num_rows($modSql);
			if ($modExsists > 0) {
				$mb = new ModuleBuilder($moduleid);
				$mb->mode = 'edit';
			} else {
				$mb = new ModuleBuilder();
			}
			$modulename = vtlib_purify($_REQUEST['modulename']);
			$mb->column_data['modulename'] = str_replace(' ', '', $modulename);
			$mb->column_data['modulelabel'] = vtlib_purify($_REQUEST['modulelabel']);
			$mb->column_data['parentmenu'] = vtlib_purify($_REQUEST['parentmenu']);
			$mb->column_data['moduleicon'] = vtlib_purify($_REQUEST['moduleicon']);
			$mb->column_data['sharingaccess'] = vtlib_purify($_REQUEST['sharingaccess']);
			$mb->column_data['merge'] = vtlib_purify($_REQUEST['merge']);
			$mb->column_data['import'] = vtlib_purify($_REQUEST['import']);
			$mb->column_data['export'] = vtlib_purify($_REQUEST['export']);
			$ret = $mb->save($step);
			return $ret;
			break;
		case '2':
			$mb = new ModuleBuilder($moduleid);
			$mb->mode = 'edit';
			if (isset($_REQUEST['blocks'])) {
				$mb->column_data['blocks'] = vtlib_purify($_REQUEST['blocks']);
			}
			$ret = $mb->save($step);
			$adb->pquery('UPDATE vtiger_modulebuilder_name SET completed="40" WHERE userid=? AND modulebuilderid=?', array($current_user->id,$moduleid));
			return $ret;
			break;
		case '3':
			$moduleid = $_COOKIE['ModuleBuilderID'];
			if (isset($_REQUEST['fields'])) {
				$mb = new ModuleBuilder($moduleid);
				if (isset($_REQUEST['fields'][0]['fieldsid'])) {
					$mb->edit = 'edit';
					$mb->column_data['fieldsid'] = $_REQUEST['fields'][0]['fieldsid'];
				}
				$data = $mb->retrieve(1, $moduleid);
				$moduleName = $data['name'];
				$mb->column_data['modulename'] = $moduleName;
				$mb->column_data['blockid'] = vtlib_purify($_REQUEST['fields'][0]['blockid']);
				$mb->column_data['fieldname'] = vtlib_purify($_REQUEST['fields'][0]['fieldname']);
				$mb->column_data['uitype'] = vtlib_purify($_REQUEST['fields'][0]['uitype']);
				$mb->column_data['columnname'] = vtlib_purify($_REQUEST['fields'][0]['columnname']);
				$mb->column_data['fieldlabel'] = vtlib_purify($_REQUEST['fields'][0]['fieldlabel']);
				$mb->column_data['fieldlength'] = vtlib_purify($_REQUEST['fields'][0]['fieldlength']);
				$mb->column_data['presence'] = vtlib_purify($_REQUEST['fields'][0]['presence']);
				$mb->column_data['sequence'] = vtlib_purify($_REQUEST['fields'][0]['sequence']);
				$mb->column_data['typeofdata'] = vtlib_purify($_REQUEST['fields'][0]['typeofdata']);
				$mb->column_data['quickcreate'] = vtlib_purify($_REQUEST['fields'][0]['quickcreate']);
				$mb->column_data['displaytype'] = vtlib_purify($_REQUEST['fields'][0]['displaytype']);
				$mb->column_data['masseditable'] = vtlib_purify($_REQUEST['fields'][0]['masseditable']);
				$mb->column_data['relatedmodules'] = vtlib_purify($_REQUEST['fields'][0]['relatedmodules']);
				$mb->column_data['picklistvalues'] = vtlib_purify($_REQUEST['fields'][0]['picklistvalues']);
				$mb->column_data['generatedtype'] = vtlib_purify($_REQUEST['fields'][0]['generatedtype']);
				$ret = $mb->save($step);
				$adb->pquery('UPDATE vtiger_modulebuilder_name SET completed="60" WHERE userid=? AND modulebuilderid=?', array($current_user->id,$moduleid));
				return $ret;
			}
			return array('moduleid'=>$moduleid);
			break;
		case '4':
			$customview = isset($_REQUEST['customview']) ? vtlib_purify($_REQUEST['customview']) : array();
			$mb = new ModuleBuilder($moduleid);
			$mb->edit = 'edit';
			$mb->column_data['customview'] = $customview;
			$ret = $mb->save($step);
			$adb->pquery('UPDATE vtiger_modulebuilder_name SET completed="80" WHERE userid=? AND modulebuilderid=?', array($current_user->id,$moduleid));
			return $ret;
			break;
		case '5':
			$relatedlists = isset($_REQUEST['relatedlists']) ? vtlib_purify($_REQUEST['relatedlists']) : array();
			if (count($relatedlists) > 0) {
				$mb = new ModuleBuilder($moduleid);
				$mb->edit = 'edit';
				$mb->column_data['name'] = $relatedlists['name'];
				$mb->column_data['label'] = $relatedlists['label'];
				$mb->column_data['actions'] = $relatedlists['actions'];
				$mb->column_data['relatedmodule'] = $relatedlists['relatedmodule'];
				$ret = $mb->save($step);
				return $ret;
			}
			$adb->pquery('UPDATE vtiger_modulebuilder_name SET completed="Completed" WHERE userid=? AND modulebuilderid=?', array($current_user->id,$moduleid));
			return array('moduleid'=>$moduleid);
			break;
		default:
			//
			break;
	}
}
?>