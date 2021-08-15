<?php
/*************************************************************************************************
 * Copyright 2012-2014 JPL TSolucio, S.L.  --  This file is a part of coreBOSCP.
* You can copy, adapt and distribute the work under the "Attribution-NonCommercial-ShareAlike"
* Vizsage Public License (the "License"). You may not use this file except in compliance with the
* License. Roughly speaking, non-commercial users may share and modify this code, but must give credit
* and share improvements. However, for proper details please read the full License, available at
* http://vizsage.com/license/Vizsage-License-BY-NC-SA.html and the handy reference for understanding
* the full license at http://vizsage.com/license/Vizsage-Deed-BY-NC-SA.html. Unless required by
* applicable law or agreed to in writing, any software distributed under the License is distributed
* on an  "AS IS" BASIS, WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
* See the License for the specific language governing permissions and limitations under the
* License terms of Creative Commons Attribution-NonCommercial-ShareAlike 3.0 (the License).
*************************************************************************************************/

function vtws_gettranslation($totranslate, $portal_language, $module, $user) {
	global $log, $default_language, $current_language;
	$log->debug('> vtws_gettranslation');
	$language = $portal_language;
	$totranslate=(array)$totranslate;
	$mod_strings=array();
	$app_strings=array();
	// $app_strings
	$applanguage_used = $language;
	if (file_exists("include/language/$language.lang.php")) {
		@include "include/language/$language.lang.php";
	} else {
		$log->warn('Unable to find the application language file for language: '.$language);
		$applanguage_used = $default_language;
		if (file_exists("include/language/$default_language.lang.php")) {
			@include "include/language/$default_language.lang.php";
		} else {
			$applanguage_used=false;
		}
	}
	if ($applanguage_used && file_exists("include/language/$applanguage_used.custom.php")) {
		@include "include/language/$applanguage_used.custom.php";
		$app_strings = array_merge($app_strings, $custom_strings);
	}

	// $mod_strings
	$modlanguage_used = '';
	if (!empty($module)) {
		$modlanguage_used = $language;
		if (file_exists("modules/$module/language/$language.lang.php")) {
			@include "modules/$module/language/$language.lang.php";
		} else {
			$log->warn("Unable to find the module language file for language/module: $language/$module");
			$modlanguage_used = $default_language;
			if (file_exists("modules/$module/language/$default_language.lang.php")) {
				@include "modules/$module/language/$default_language.lang.php";
			} else {
				$modlanguage_used = false;
			}
		}
	}
	if ($modlanguage_used && file_exists("modules/$module/language/$modlanguage_used.custom.php")) {
		@include "modules/$module/language/$modlanguage_used.custom.php";
		$mod_strings = array_merge($mod_strings, $custom_strings);
	}

	if (!$applanguage_used && !$modlanguage_used) {
		return $totranslate; // We can't find language file so we return what we are given
	}
	$originalDefaultLanguage = $default_language;
	$originalCurrentLanguage = $current_language;
	$default_language = $current_language = $language;
	$translated=array();
	foreach ($totranslate as $key => $str) {
		$ismodule = vtlib_isModuleActive($key);
		if ($ismodule) {
			$i18nMod = getTranslatedString($key, $key);
		}
		if (!empty($mod_strings[$str])) {
			$tr = $mod_strings[$str];
		} elseif (!empty($app_strings[$str])) {
			$tr = $app_strings[$str];
		} elseif (!empty($mod_strings[$key])) {
			$tr = $mod_strings[$key];
		} elseif (!empty($app_strings[$key])) {
			$tr = $app_strings[$key];
		} elseif ($ismodule && $i18nMod != $key) {
			$tr = $i18nMod;
		} elseif ($ismodule) {
			$tr = getTranslatedString($str, $module);
		} else {
			$tr = $str;
		}
		$translated[$key] = $tr;
	}
	$default_language = $originalDefaultLanguage;
	$current_language = $originalCurrentLanguage;
	$log->debug('< vtws_gettranslation');
	return $translated;
}
?>