<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/
require_once 'data/CRMEntity.php';
require_once 'data/Tracker.php';

class cbtranslation extends CRMEntity {
	public $db;
	public $log;

	public $table_name = 'vtiger_cbtranslation';
	public $table_index= 'cbtranslationid';
	public $column_fields = array();

	/** Indicator if this is a custom module or standard module */
	public $IsCustomModule = true;
	public $HasDirectImageField = false;
	/**
	 * Mandatory table for supporting custom fields.
	 */
	public $customFieldTable = array('vtiger_cbtranslationcf', 'cbtranslationid');

	/**
	 * Mandatory for Saving, Include tables related to this module.
	 */
	public $tab_name = array('vtiger_crmentity', 'vtiger_cbtranslation', 'vtiger_cbtranslationcf');

	/**
	 * Mandatory for Saving, Include tablename and tablekey columnname here.
	 */
	public $tab_name_index = array(
		'vtiger_crmentity' => 'crmid',
		'vtiger_cbtranslation'   => 'cbtranslationid',
		'vtiger_cbtranslationcf' => 'cbtranslationid',
	);

	/**
	 * Mandatory for Listing (Related listview)
	 */
	public $list_fields = array(
		/* Format: Field Label => array(tablename => columnname) */
		// tablename should not have prefix 'vtiger_'
		'cbtranslation No'=> array('cbtranslation' => 'autonum'),
		'Locale'=> array('cbtranslation' => 'locale'),
		'Module'=> array('cbtranslation' => 'cbtranslation_module'),
		'Key'=> array('cbtranslation' => 'translation_key'),
		'i18n'=> array('cbtranslation' => 'i18n'),
		'Proof Read'=> array('cbtranslation' => 'proofread'),
		'Assigned To' => array('crmentity' => 'smownerid')
	);
	public $list_fields_name = array(
		/* Format: Field Label => fieldname */
		'cbtranslation No'=> 'autonum',
		'Locale'=> 'locale',
		'Module'=> 'cbtranslation_module',
		'Key'=> 'translation_key',
		'i18n'=> 'i18n',
		'Proof Read'=> 'proofread',
		'Assigned To' => 'assigned_user_id'
	);

	// Make the field link to detail view from list view (Fieldname)
	public $list_link_field = 'autonum';

	// For Popup listview and UI type support
	public $search_fields = array(
		/* Format: Field Label => array(tablename => columnname) */
		// tablename should not have prefix 'vtiger_'
		'cbtranslation No'=> array('cbtranslation' => 'autonum'),
		'Locale'=> array('cbtranslation' => 'locale'),
		'Module'=> array('cbtranslation' => 'cbtranslation_module'),
		'Key'=> array('cbtranslation' => 'translation_key'),
		'i18n'=> array('cbtranslation' => 'i18n'),
		'Proof Read'=> array('cbtranslation' => 'proofread')
	);
	public $search_fields_name = array(
		/* Format: Field Label => fieldname */
		'cbtranslation No'=> 'autonum',
		'Locale'=> 'locale',
		'Module'=> 'cbtranslation_module',
		'Key'=> 'translation_key',
		'i18n'=> 'i18n',
		'Proof Read'=> 'proofread'
	);

	// For Popup window record selection
	public $popup_fields = array('autonum');

	// Placeholder for sort fields - All the fields will be initialized for Sorting through initSortFields
	public $sortby_fields = array();

	// For Alphabetical search
	public $def_basicsearch_col = 'translation_key';

	// Column value to use on detail view record text display
	public $def_detailview_recname = 'translation_key';

	// Required Information for enabling Import feature
	public $required_fields = array('translation_key'=>1);

	// Callback function list during Importing
	public $special_functions = array('set_import_assigned_user');

	public $default_order_by = 'translation_key';
	public $default_sort_order='ASC';
	// Used when enabling/disabling the mandatory fields for the module.
	// Refers to vtiger_field.fieldname values.
	public $mandatory_fields = array('createdtime', 'modifiedtime', 'translation_key','translation_module','locale');

	public function save_module($module) {
		if ($this->HasDirectImageField) {
			$this->insertIntoAttachment($this->id, $module);
		}
	}

	/**
	 * Invoked when special actions are performed on the module.
	 * @param String Module name
	 * @param String Event Type (module.postinstall, module.disabled, module.enabled, module.preuninstall)
	 */
	public function vtlib_handler($modulename, $event_type) {
		if ($event_type == 'module.postinstall') {
			// TODO Handle post installation actions
			$this->setModuleSeqNumber('configure', $modulename, 'cbtr-', '0000001');
			$module = Vtiger_Module::getInstance($modulename);
			$field = Vtiger_Field::getInstance('translates', $module);
			$field->setRelatedModules(getAllowedPicklistModules(false));
		} elseif ($event_type == 'module.disabled') {
			// TODO Handle actions when this module is disabled.
		} elseif ($event_type == 'module.enabled') {
			// TODO Handle actions when this module is enabled.
		} elseif ($event_type == 'module.preuninstall') {
			// TODO Handle actions when this module is about to be deleted.
		} elseif ($event_type == 'module.preupdate') {
			// TODO Handle actions before this module is updated.
		} elseif ($event_type == 'module.postupdate') {
			// TODO Handle actions after this module is updated.
		}
	}

	/**
	 * Handle saving related module information.
	 * NOTE: This function has been added to CRMEntity (base class).
	 * You can override the behavior by re-defining it here.
	 */
	// public function save_related_module($module, $crmid, $with_module, $with_crmid) { }

	/**
	 * Handle deleting related module information.
	 * NOTE: This function has been added to CRMEntity (base class).
	 * You can override the behavior by re-defining it here.
	 */
	//public function delete_related_module($module, $crmid, $with_module, $with_crmid) { }

	/**
	 * Handle getting related list information.
	 * NOTE: This function has been added to CRMEntity (base class).
	 * You can override the behavior by re-defining it here.
	 */
	//public function get_related_list($id, $cur_tab_id, $rel_tab_id, $actions=false) { }

	/**
	 * Handle getting dependents list information.
	 * NOTE: This function has been added to CRMEntity (base class).
	 * You can override the behavior by re-defining it here.
	 */
	//public function get_dependents_list($id, $cur_tab_id, $rel_tab_id, $actions=false) { }

	/**
	 * Returns the language string to be used for translation
	 * Try to get it from User, next from request headers and finally from global configuration
	 * @return	<String> Language string to be used
	 */
	public static function getLanguage() {
		global $current_user, $default_language;
		if (!empty($current_user) && !empty($current_user->column_fields['language'])) {
			return $current_user->column_fields['language'];
		}
		// Fallback : Read the Accept-Language header of the request (really useful for login screen)
		if (!empty($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
			//Getting all languages in an array
			$languages = Vtiger_LanguageExport::getAll();
			//Extracting locales strings from header
			preg_match_all("/([a-z-]+)[,;]/i", $_SERVER['HTTP_ACCEPT_LANGUAGE'], $locales);
			//Looping in found locales and test match against languages
			foreach ($locales[1] as $locale) {
				foreach ($languages as $code => $lang) {
					//First case insensitive comparison
					if (strcasecmp($code, $locale) === 0) {
						return $code;
					}
					//Second case with replacing '-' by '_'
					if (strcasecmp($code, str_replace('-', '_', $locale)) === 0) {
						return $code;
					}
					//Finally, try with short 2 letters country code
					if (strcasecmp(substr($code, 0, 2), $locale) === 0) {
						return $code;
					}
				}
			}
		}
		// Last fallback : global configuration
		return $default_language;
	}

	/**
	 * Function that returns given language short name, if none is given the current language short name will be returned
	 * @return <String>
	 */
	public static function getShortLanguageName($languageLongName = '') {
		global $current_language;
		if ($languageLongName == '') {
			$languageLongName = $current_language;
		}
		return substr($languageLongName, 0, 2);
	}

	/**
	 * Function that returns given language long name, if none is given the current language long name will be returned
	 * @return <String>
	 */
	public static function getLongLanguageName($languageShortName = '') {
		global $current_language;
		if ($languageShortName == '') {
			return $current_language;
		}
		$lang = array(
			'de' => 'de_de',
			'en' => 'en_us',
			'es' => 'es_es',
			'fr' => 'fr_fr',
			'hu' => 'hu_hu',
			'it' => 'it_it',
			'nl' => 'nl_nl',
			'pt' => 'pt_br',
		);
		return (isset($lang[$languageShortName]) ? $lang[$languageShortName] : '');
	}

	/*
	 * Retrieve a translated label from the database
	 * @param <String> $key   label to translate
	 * @param <String> $module module context to search for the label frist before fall back to main translation file
	 * @param <Array> $options
	 * 		language => <String> language to translate against, if not set $current_language will be used
	 * 		context => <String> label modifier for specific translation contexts like gender
	 * 		count => <Integer> label modifier for plurals
	 * @param if more parameters are given they will be passed in order to the translated label using sprintf
	 * @returns <String> translated string if found, same label if not
	 */
	public static function get($key, $module = '', $options = '') {
		global $adb, $current_language, $currentModule, $installationStrings;
		if (!is_object($adb) || is_null($adb->database)) {
			return $key;
		}
		if (isset($installationStrings)) {
			return $key;
		}
		if (empty($module)) {
			$module = $currentModule;
		}
		if (is_array($options)) {
			if (isset($options['language'])) {
				$lang = $options['language'];
			} else {
				$lang = $current_language;
			}
			if (isset($options['context'])) {
				$context = $options['context'];
			} else {
				$context = null;
			}
			if (isset($options['count'])) {
				$count = $options['count'];
			} else {
				$count = null;
			}
			if (isset($options['field'])) {
				$field = $options['field'];
			} else {
				$field = null;
			}
		} else {
			$lang = $current_language;
			$context = $count = $field =null;
		}
		$translatedString = null;
		if (!empty($context) && !is_null($count)) {
			$searchKey = $key.'_'.$context;
			$searchKey = self::getPluralizedKey($searchKey, $lang, $count);
			$translatedString = self::getTranslation($searchKey, $module, $lang, $field);
			if ($translatedString == $searchKey) {
				$translatedString = null;
			}
		}
		if (!empty($context) && $translatedString == null) {
			$searchKey = $key.'_'.$context;
			$translatedString = self::getTranslation($searchKey, $module, $lang, $field);
			if ($translatedString == $searchKey) {
				$translatedString = null;
			}
		}
		if (!is_null($count) && $translatedString == null) {
			$searchKey = self::getPluralizedKey($key, $lang, $count);
			$translatedString = self::getTranslation($searchKey, $module, $lang, $field);
			if ($translatedString == $searchKey) {
				$translatedString = null;
			}
		}
		if ($translatedString == null) {
			$translatedString = self::getTranslation($key, $module, $lang, $field);
		}
		$args = func_get_args();
		$args = array_slice($args, 3);
		if (is_array($args) && !empty($args)) {
			$translatedString = vsprintf($translatedString, $args);
		}
		return $translatedString;
	}

	protected static function getTranslation($key, $module, $lang, $field = null) {
		global $adb;
		$ckey = 'cbtcache'.$key.$module.$lang.(is_null($field) ? '' : $field);
		list($value,$found) = VTCacheUtils::lookupCachedInformation($ckey);
		if ($found) {
			return $value;
		}
		$params = array($lang,$key,$module);
		if (is_null($field)) {
			$sql = "SELECT i18n,translation_module
				FROM vtiger_cbtranslation WHERE locale=? and translation_key=? and (translation_module=? or translation_module='cbtranslation')";
		} else {
			$sql = "SELECT i18n,translation_module
				FROM vtiger_cbtranslation WHERE locale=? and translation_key=? and (translation_module=? or translation_module='cbtranslation') and forfield=?";
			$params[] = $field;
		}
		$trans = $adb->pquery($sql, $params);
		if ($trans && $adb->num_rows($trans)>0) {
			$i18n = $adb->query_result($trans, 0, 'i18n');
			if ($adb->num_rows($trans)==2) {
				$i18nmod = $adb->query_result($trans, 1, 'translation_module');
				if ($i18nmod != 'cbtranslation') {
					$i18n = $adb->query_result($trans, 1, 'i18n');
				}
			}
		} else {
			$i18n = $key;
		}
		VTCacheUtils::updateCachedInformation($ckey, $i18n);
		return $i18n;
	}

	public static function getPicklistValues($lang, $module, $picklist) {
		global $adb;
		$plvals = array();
		$sql = 'SELECT i18n FROM vtiger_cbtranslation WHERE locale=? and forpicklist=? and translation_module=?';
		$trans = $adb->pquery($sql, array($lang,$module.'::'.$picklist,$module));
		while ($i18n = $adb->fetch_array($trans)) {
			$plvals[] = $i18n['i18n'];
		}
		return $plvals;
	}

	public static function return_module_language($lang, $module) {
		global $adb, $current_language, $currentModule, $installationStrings;
		if (!is_object($adb) || is_null($adb->database)) {
			return array();
		}
		if (isset($installationStrings)) {
			return array();
		}
		if (empty($module)) {
			$module = $currentModule;
		}
		if (empty($lang)) {
			$lang = $current_language;
		}
		$ckey = 'cbtmodcache'.$module.$lang;
		list($value,$found) = VTCacheUtils::lookupCachedInformation($ckey);
		if ($found) {
			return $value;
		}
		$mstrings = array();
		$sql = 'SELECT i18n,translation_key FROM vtiger_cbtranslation WHERE locale=? and translation_module=?';
		$trans = $adb->pquery($sql, array($lang,$module));
		if ($trans && $adb->num_rows($trans)>0) {
			while ($tr = $adb->fetch_array($trans)) {
				$mstrings[$tr['translation_key']] = $tr['i18n'];
			}
		} else {
			$trans = $adb->pquery($sql, array('en_us',$module));
			if ($trans && $adb->num_rows($trans)>0) {
				while ($tr = $adb->fetch_array($trans)) {
					$mstrings[$tr['translation_key']] = $tr['i18n'];
				}
			}
		}
		VTCacheUtils::updateCachedInformation($ckey, $mstrings);
		return $mstrings;
	}

	public static function return_application_language($lang) {
		return cbtranslation::return_module_language($lang, 'cbtranslation');
	}

	/**
	 *  This function returns the modified keycode to match the plural form(s) of a given language and a given count with the same pattern used by i18next JS library
	 *  Global patterns for keycode are as below :
	 *  - No plural form : only one non modified key is needed :)
	 *  - 2 forms : unmodified key for singular values and 'key_PLURAL' for plural values
	 *  - 3 or more forms : key_X with X indented for each plural form
	 *  @see https://www.i18next.com/plurals.html for some examples
	 *  @see http://docs.translatehouse.org/projects/localization-guide/en/latest/l10n/pluralforms.html?id=l10n/pluralforms for whole plural rules used by getText
	 *
	 *	@param	<String>	$key		Key to be pluralized
	 *  @param	<String>	$locale		Locale/language value
	 *  @param	<Float>		$count		Quantity for plural determination
	 *	@return	<String>	Pluralized key to look for
	 */
	public static function getPluralizedKey($key, $locale, $count) {
		//Extract language code from locale with special cases
		if (strcasecmp($locale, 'pt_BR') === 0) {
			$lang='pt_BR';
		} else {
			preg_match("/^[a-z]+/i", $locale, $match);
			$lang = strtolower((empty($match[0]))?'en':$match[0]);
		}

		//No plural form
		if (in_array($lang, array(
			'ay','bo','cgg','dz','id','ja','jbo','ka','km','ko','lo','ms','my','sah','su','th','tt','ug','vi','wo','zh'
		))) {
			return $key;
		}

		//Two plural forms
		if (in_array($lang, array(
			'ach','ak','am','arn','br','fa','fil','fr','gun','ln','mfe','mg','mi','oc','pt_BR','tg','ti','tr','uz','wa'
		))) {
			return ($count > 1) ? $key.'_PLURAL' : $key;
		}

		if (in_array($lang, array(
			'af','an','anp','as','ast','az','bg','bn','brx','ca','da','de','doi','dz','el','en','eo','es','et','eu','ff','fi','fo','fur','fy',
			'gl','gu','ha','he','hi','hne','hu','hy','ia','it','kk','kl','kn','ku','ky','lb','mai','mk','ml','mn','mni','mr','nah','nap',
			'nb','ne','nl','nn','nso','or','pa','pap','pms','ps','pt','rm','rw','sat','sco','sd','se','si','so','son','sq','sv','sw',
			'ta','te','tk','ur','yo'
		))) {
			return ($count != 1) ? $key.'_PLURAL' : $key;
		}

		if ($lang == 'is') {
			return ($count%10 != 1 || $count%100 == 11)?$key.'_PLURAL':$key;
		}

		//3 or more plural forms
		if (in_array($lang, array(
			'be','bs','hr','ru','sr','uk'
		))) {
			$i = $count%10;
			$j = $count%100;
			if ($i == 1 && $j != 11) {
				return $key.'_0';
			}
			if ($i >= 2 && $i <= 4 && ($j < 10 || $j >= 20)) {
				return $key.'_1';
			}
			return $key.'_2';
		}

		if (in_array($lang, array(
			'cs','sk'
		))) {
			if ($count == 1) {
				return $key.'_0';
			}
			if ($count >= 2 && $count <= 4) {
				return $key.'_1';
			}
			return $key.'_2';
		}

		if ($lang == 'csb') {
			$i = $count%10;
			$j = $count%100;
			if ($count == 1) {
				return $key.'_0';
			}
			if ($i >= 2 && $i <= 4 && ($j < 10 || $j >= 20)) {
				return $key.'_1';
			}
			return $key.'_2';
		}

		if ($lang == 'lt') {
			$i = $count%10;
			$j = $count%100;
			if ($i == 1 && $j != 11) {
				return $key.'_0';
			}
			if ($i >= 2 && ($j < 10 || $j >= 20)) {
				return $key.'_1';
			}
			return $key.'_2';
		}

		if ($lang == 'lv') {
			$i = $count%10;
			$j = $count%100;
			if ($i == 1 && $j != 11) {
				return $key.'_0';
			}
			if ($count != 0) {
				return $key.'_1';
			}
			return $key.'_2';
		}

		if ($lang == 'me') {
			$i = $count%10;
			$j = $count%100;
			if ($i == 1 && $j != 11) {
				return $key.'_0';
			}
			if ($i >= 2 && $i <= 4 && ($j < 10 || $j >= 20)) {
				return $key.'_1';
			}
			return $key.'_2';
		}

		if ($lang == 'pl') {
			$i = $count%10;
			$j = $count%100;
			if ($count == 1) {
				return $key.'_0';
			}
			if ($i >= 2 && $i <=4 && ($j < 10 || $j >= 20)) {
				return $key.'_1';
			}
			return $key.'_2';
		}

		if ($lang == 'ro') {
			$j = $count%100;
			if ($count == 1) {
				return $key.'_0';
			}
			if ($count == 0 || ($j > 0 && $j < 20)) {
				return $key.'_1';
			}
			return $key.'_2';
		}

		if ($lang == 'cy') {
			if ($count == 1) {
				return $key.'_0';
			}
			if ($count == 2) {
				return $key.'_1';
			}
			if ($count != 8 && $count != 11) {
				return $key.'_2';
			}
			return $key.'_3';
		}

		if ($lang == 'gd') {
			if ($count == 1 || $count == 11) {
				return $key.'_0';
			}
			if ($count == 2 || $count == 12) {
				return $key.'_1';
			}
			if ($count > 2 && $count < 20) {
				return $key.'_2';
			}
			return $key.'_3';
		}

		if ($lang == 'kw') {
			if ($count == 1) {
				return $key.'_0';
			}
			if ($count == 2) {
				return $key.'_1';
			}
			if ($count == 3) {
				return $key.'_2';
			}
			return $key.'_3';
		}

		if ($lang == 'mt') {
			$j = $count%100;
			if ($count == 1) {
				return $key.'_0';
			}
			if ($count == 0 || ($j > 1 && $j < 11)) {
				return $key.'_1';
			}
			if ($j > 10 && $j <20) {
				return $key.'_2';
			}
			return $key.'_3';
		}

		if ($lang == 'sl') {
			$j = $count%100;
			if ($j == 1) {
				return $key.'_0';
			}
			if ($j == 2) {
				return $key.'_1';
			}
			if ($j == 3 || $j == 4) {
				return $key.'_2';
			}
			return $key.'_3';
		}

		if ($lang == 'ga') {
			if ($count == 1) {
				return $key.'_0';
			}
			if ($count == 2) {
				return $key.'_1';
			}
			if ($count > 2 && $count < 7) {
				return $key.'_2';
			}
			if ($count > 6 && $count < 11) {
				return $key.'_3';
			}
			return $key.'_4';
		}

		if ($lang == 'ar') {
			if ($count == 0) {
				return $key.'_0';
			}
			if ($count == 1) {
				return $key.'_1';
			}
			if ($count == 2) {
				return $key.'_2';
			}
			if ($count%100 >= 3 && $count%100 <= 10) {
				return $key.'_3';
			}
			if ($count*100 >= 11) {
				return $key.'_4';
			}
			return $key.'_5';
		}

		//Fallback if no language found
		return $key;
	}

	public static function getMonthName($month, $language = '') {
		if ($language=='') {
			global $current_language;
			$language = $current_language;
		} elseif (strlen($language) == 2) {
			$language = self::getLongLanguageName($language);
		}
		if (file_exists('modules/Reports/language/'.$language.'.lang.php')) {
			include 'modules/Reports/language/'.$language.'.lang.php';
			return $mod_strings['MONTH_STRINGS'][$month];
		} else {
			return '';
		}
	}

	public static function getDayOfWeekName($week, $language = '') {
		if ($language=='') {
			global $current_language;
			$language = $current_language;
		} elseif (strlen($language) == 2) {
			$language = self::getLongLanguageName($language);
		}
		if (file_exists('modules/Reports/language/'.$language.'.lang.php')) {
			include 'modules/Reports/language/'.$language.'.lang.php';
			return $mod_strings['WEEKDAY_STRINGS'][$week];
		} else {
			return '';
		}
	}

	public function trash($module, $record) {
		global $adb;
		parent::trash($module, $record);
		$adb->pquery('Delete from vtiger_cbtranslation where cbtranslationid=?', array($record));
		$adb->pquery('Delete from vtiger_cbtranslationcf where cbtranslationid=?', array($record));
		$adb->pquery('Delete from vtiger_crmentity where crmid=?', array($record));
	}
}
?>
