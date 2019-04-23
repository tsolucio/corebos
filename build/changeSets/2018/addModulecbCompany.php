<?php
/*************************************************************************************************
 * Copyright 2018 JPL TSolucio, S.L. -- This file is a part of TSOLUCIO coreBOS Customizations.
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

class addModulecbCompany extends cbupdaterWorker {

	public function applyChange() {
		if ($this->hasError()) {
			$this->sendError();
		}
		if ($this->isApplied()) {
			$this->sendMsg('Changeset '.get_class($this).' already applied!');
		} else {
			$toinstall = array('cbCompany');
			foreach ($toinstall as $module) {
				if ($this->isModuleInstalled($module)) {
					vtlib_toggleModuleAccess($module, true);
					$this->sendMsg("$module activated!");
				} else {
					$this->installManifestModule($module);
				}
				// remove the company settings section
				$this->ExecuteQuery('update vtiger_settings_field set active = 1  where name = "LBL_COMPANY_DETAILS"', array());

				global $adb, $default_charset;
				require_once "modules/$module/$module.php";
				require_once 'modules/Users/Users.php';
				$query = $adb->query('SELECT * FROM vtiger_organizationdetails limit 1');
				if ($query && $adb->num_rows($query) > 0) {
					$chk = $adb->pquery(
						'select 1 from vtiger_cbcompany where companyname=? and address=? and phone=? and defaultcompany=?',
						array(
							$adb->query_result($query, 0, 'organizationname'),
							$adb->query_result($query, 0, 'address'),
							$adb->query_result($query, 0, 'phone'),
							'1',
						)
					);
					if ($chk && $adb->num_rows($chk) == 0) {
						$userFocus = new Users();
						$adminId = $userFocus->retrieve_user_id('admin');
						$focus = new $module();
						$focus->column_fields['assigned_user_id'] = $adminId;
						$focus->column_fields['companyname'] = html_entity_decode($adb->query_result($query, 0, 'organizationname'), ENT_QUOTES, $default_charset);
						$focus->column_fields['address'] = html_entity_decode($adb->query_result($query, 0, 'address'), ENT_QUOTES, $default_charset);
						$focus->column_fields['city'] = html_entity_decode($adb->query_result($query, 0, 'city'), ENT_QUOTES, $default_charset);
						$focus->column_fields['state'] = html_entity_decode($adb->query_result($query, 0, 'state'), ENT_QUOTES, $default_charset);
						$focus->column_fields['country'] = html_entity_decode($adb->query_result($query, 0, 'country'), ENT_QUOTES, $default_charset);
						$focus->column_fields['postalcode'] = html_entity_decode($adb->query_result($query, 0, 'code'), ENT_QUOTES, $default_charset);
						$focus->column_fields['phone'] = html_entity_decode($adb->query_result($query, 0, 'phone'), ENT_QUOTES, $default_charset);
						$focus->column_fields['fax'] = html_entity_decode($adb->query_result($query, 0, 'fax'), ENT_QUOTES, $default_charset);
						$focus->column_fields['website'] = html_entity_decode($adb->query_result($query, 0, 'website'), ENT_QUOTES, $default_charset);
						$focus->column_fields['defaultcompany'] = 1;
						$imagesArray = array('companylogo'=>'logoname','applogo'=>'frontlogo','favicon'=>'faviconlogo');
						foreach ($imagesArray as $index => $name) {
							$filetyp =str_replace('.', '', strtolower(substr($adb->query_result($query, 0, $name), -4)));
							$_FILES[$index] = array(
								'name' => $adb->query_result($query, 0, $name),
								'type' => 'image/'.$filetyp,
								'tmp_name' => 'test/logo/'.$adb->query_result($query, 0, $name),
								'error' => 0,
								'size' => 1
							);
						}
						$focus->save($module);
					}
				}
			}
			$this->sendMsg('Changeset '.get_class($this).' applied!');
			$this->markApplied();
		}
		$this->finishExecution();
	}

	public function undoChange() {
		if ($this->hasError()) {
			$this->sendError();
		}
		if ($this->isApplied()) {
			vtlib_toggleModuleAccess('cbCompany', false);
			$this->sendMsg('cbCompany deactivated!');
			$this->ExecuteQuery('update vtiger_settings_field set active = 0  where name = "LBL_COMPANY_DETAILS"', array());
			$this->markUndone(false);
			$this->sendMsg('Changeset '.get_class($this).' undone!');
		} else {
			$this->sendMsg('Changeset '.get_class($this).' not applied, it cannot be undone!');
		}
		$this->finishExecution();
	}
}


