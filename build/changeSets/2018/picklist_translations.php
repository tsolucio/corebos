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

class picklist_translations extends cbupdaterWorker {

	public function applyChange() {
		global $adb, $default_charset;
		if ($this->hasError()) {
			$this->sendError();
		}
		if ($this->isApplied()) {
			$this->sendMsg('Changeset '.get_class($this).' already applied!');
		} else {
			$current_user = Users::getActiveAdminUser();
			set_time_limit(0);
			ini_set('memory_limit', '1024M');
			if (!vtlib_isModuleActive('cbtranslation')) {
				include_once 'include/utils/VtlibUtils.php';
				vtlib_toggleModuleAccess('cbtranslation', true);
			}
			include_once 'include/Webservices/Create.php';
			include_once 'modules/cbtranslation/cbtranslation.php';
			$usrwsid = vtws_getEntityId('Users').'x'.$current_user->id;
			$rec = array(
				'proofread' => '1',
				'assigned_user_id' => $usrwsid,
			);
			$modsDoneStr = coreBOS_Settings::getSetting('picklistTranslationLanguageModulesDone', '');
			$modsDone = explode(';', $modsDoneStr);
			$import_langs = array('en_us','es_es','de_de','en_gb','es_mx','fr_fr','hu_hu','it_it','nl_nl','pt_br');
			$import_modules = getAllowedPicklistModules(1);
			$import_modules = array_merge($import_modules, array('Rss','Recyclebin'));
			$chksql = 'select cbtranslationid
				from vtiger_cbtranslation
				where translation_module=? and translation_key=? and forpicklist=? and locale=?';
			foreach ($import_modules as $impmod) {  // FOREACH MODULE
				if (in_array($impmod, $modsDone) || $impmod=='Emails') {
					continue;
				}
				set_time_limit(0);
				$rec['translation_module'] = $impmod;
				foreach ($import_langs as $lang) {  // FOREACH LANGUAGE
					if (file_exists('modules/' . $impmod . '/language/' . $lang . '.lang.php')) {
						include 'modules/' . $impmod . '/language/' . $lang . '.lang.php';
						include 'include/language/' . $lang . '.lang.php';
						if (file_exists("modules/$impmod/language/$lang.custom.php")) {
							@include "modules/$impmod/language/$lang.custom.php";
							$mod_strings = $custom_strings + $mod_strings;
						}
						$rec['locale'] = $lang;
						$query = $adb->pquery(
							"select fieldname
								from vtiger_tab
								join vtiger_field on vtiger_tab.tabid=vtiger_field.tabid
								where uitype in ('15','33','16') and name=?",
							array($impmod)
						);
						$count = $adb->num_rows($query);
						for ($i=0; $i<$count; $i++) {  // FOREACH PICKLIST IN THE MODULE
							$fieldname = $adb->query_result($query, $i, 0);
							$rec['forpicklist'] = $impmod.'::'.$fieldname;
							$table = 'vtiger_'.$fieldname;
							$columns = $adb->query("select $fieldname from $table");
							$countcol = $adb->num_rows($columns);
							for ($j=0; $j<$countcol; $j++) {  // FOREACH PICKLIST VALUE IN THE PICKLIST
								$key = $adb->query_result($columns, $j, 0);
								if (empty($key)) {
									continue;
								}
								$key = html_entity_decode($key, ENT_QUOTES, $default_charset);
								if (isset($mod_strings[$key])) {
									$value = $mod_strings[$key];
								} elseif (isset($app_strings[$key])) {
									$value = $app_strings[$key];
								} else {
									$value = $key;
								}
								$rec['translation_key'] = $key;
								$rs = $adb->pquery($chksql, array($rec['translation_module'], $rec['translation_key'], $rec['forpicklist'], $rec['locale']));
								if ($adb->num_rows($rs)>0) {
									$cbtrid = $adb->query_result($rs, 0, 'cbtranslationid');
									$adb->pquery('update vtiger_cbtranslation set i18n=? where cbtranslationid=?', array($value, $cbtrid));
								} else {
									$rec['i18n'] = $value;
									vtws_create('cbtranslation', $rec, $current_user);
								}
							}
						}
					}
				}
				$modsDone[] = $impmod;
				coreBOS_Settings::setSetting('picklistTranslationLanguageModulesDone', implode(';', $modsDone));
			}
			$this->ExecuteQuery("update vtiger_cbupdater set blocked='1', systemupdate='0' where cbupdaterid=?", array($this->cbupdid));
			coreBOS_Settings::delSetting('picklistTranslationLanguageModulesDone');
			$this->sendMsg('Changeset '.get_class($this).' applied!');
			$this->markApplied(false);
		}
		$this->finishExecution();
	}
}
?>
