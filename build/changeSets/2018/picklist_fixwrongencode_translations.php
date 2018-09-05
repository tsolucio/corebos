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

class picklist_fixwrongencode_translations extends cbupdaterWorker {

	public function applyChange() {
		global $adb;
		if ($this->hasError()) {
			$this->sendError();
		}
		if ($this->isApplied()) {
			$this->sendMsg('Changeset '.get_class($this).' already applied!');
		} else {
			$current_user = Users::getActiveAdminUser();
			global $mod_strings, $app_strings, $current_language;
			set_time_limit(0);
			ini_set('memory_limit', '1024M');
			if (!vtlib_isModuleActive('cbtranslation')) {
				include_once 'include/utils/VtlibUtils.php';
				vtlib_toggleModuleAccess('cbtranslation', true);
			}
			include_once 'include/Webservices/Create.php';
			include_once 'include/Webservices/Revise.php';
			include_once 'modules/cbtranslation/cbtranslation.php';
			$usrwsid = vtws_getEntityId('Users').'x'.$current_user->id;
			$default_values =  array(
				'proofread' => '1',
				'assigned_user_id' => $usrwsid,
			);
			$rec = $default_values;
			$query = $adb->query(
				"select *
					from vtiger_cbtranslation
					join vtiger_crmentity on crmid=cbtranslationid
					where forpicklist is not null and forpicklist<>'' and deleted=0"
			);
			$count = $adb->num_rows($query);
			$wsentityid = vtws_getEntityID('cbtranslation').'x';
			for ($j=0; $j<$count; $j++) {
				$impmod = $adb->query_result($query, $j, 'translation_module');
				$lang = $adb->query_result($query, $j, 'locale');
				//$valtranslated = $adb->query_result($query, $j, 'i18n');
				$forpicklist1 = explode('::', $adb->query_result($query, $j, 'forpicklist'));
				$forpicklistname = $forpicklist1[1];
				$forpicklist = $forpicklist1[0].'::'.$forpicklist1[1];
				$current_language = $lang;
				$translationid = $adb->query_result($query, $j, 'cbtranslationid');
				if (file_exists('modules/' . $impmod . '/language/' . $lang . '.lang.php')) {
					include 'modules/' . $impmod . '/language/' . $lang . '.lang.php';
					include 'include/language/' . $lang . '.lang.php';

					$key = $adb->query_result($query, $j, 'translation_key');
					if (empty($key)) {
						continue;
					}
					if (isset($mod_strings[$key])) {
						$value = $mod_strings[$key];
					} elseif (isset($app_strings[$key])) {
						$value = $app_strings[$key];
					} else {
						$value = $key;
					}
					$fname = $adb->pquery(
						'select fieldname
							from vtiger_tab
							join vtiger_field on vtiger_tab.tabid=vtiger_field.tabid
							where columnname=? and name=? and fieldname<>columnname',
						array($forpicklistname,$impmod)
					);
					$cnt = $adb->num_rows($fname);

					if ($cnt>0) {
						$fieldname = $adb->query_result($fname, 0, 0);
						$adb->pquery('delete from vtiger_cbtranslation where forpicklist=?', array($forpicklist));
						$adb->query('delete from vtiger_cbtranslationcf where cbtranslationid not in (select cbtranslationid from vtiger_cbtranslation)');
						$adb->query("delete from vtiger_crmentity where setype='cbtranslation' and crmid not in (select cbtranslationid from vtiger_cbtranslation)");
						$table = 'vtiger_'.$fieldname;
						$columns = $adb->query("select $fieldname from $table");
						$countcol = $adb->num_rows($columns);
						for ($i=0; $i<$countcol; $i++) {
							$key1 = $adb->query_result($columns, $i, 0);
							if (isset($mod_strings[$key1])) {
								$value1 = $mod_strings[$key1];
							} elseif (isset($app_strings[$key1])) {
								$value1 = $app_strings[$key1];
							} else {
								$value1 = $key1;
							}
							$rec['translation_module'] = $impmod;
							$rec['translation_key'] = $key1;
							$rec['forpicklist'] = $impmod.'::'.$fieldname;
							$rec['i18n'] = $value1;
							$rec['locale'] = $lang;
							if (empty($key1)) {
								continue;
							}
							vtws_create('cbtranslation', $rec, $current_user);
						}
					} else {
							$rec['i18n'] = htmlspecialchars_decode($value);
							$rec['id'] = $wsentityid.$translationid;
							vtws_revise($rec, $current_user);
					}
				}
			}

			$this->sendMsg('Changeset '.get_class($this).' applied!');
			$this->markApplied(false);
		}
		$this->finishExecution();
	}
}
?>
