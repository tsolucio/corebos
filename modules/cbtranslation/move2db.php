<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html><head><title>coreBOS Utility loader</title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<style type="text/css">@import url("themes/softed/style.css");br { display: block; margin: 2px; }</style>
</head><body class=small style="font-size: 12px; margin: 2px; padding: 2px; background-color:#f7fff3; ">
<table width="100%" border=0>
<tr>
<td><span style='color:red;float:right;margin-right:30px;'><h2>Proud member of the <a href='http://corebos.org'>coreBOS</a> family!</h2></span></td>
</tr>
</table>
<hr style="height: 1px">
<?php

// Turn on debugging level
$Vtiger_Utils_Log = true;

include_once 'vtlib/Vtiger/Module.php';
error_reporting(E_ALL);
ini_set('display_errors', 'on');
$usr = new Users();
$current_user = Users::getActiveAdminUser();
set_time_limit(0);
ini_set('memory_limit', '1024M');

include_once 'include/Webservices/Create.php';
include_once 'modules/cbtranslation/cbtranslation.php';
$usrwsid = vtws_getEntityId('Users').'x'.$current_user->id;
$default_values =  array(
	'proofread' => '1',
	'assigned_user_id' => $usrwsid,
);
$rec = $default_values;

$import_langs = array('en_us','es_es','de_de','en_gb','es_mx','fr_fr','hu_hu','it_it','nl_nl','pt_br');
foreach ($import_langs as $lang) {
	include 'include/language/'.$lang.'.lang.php';
	foreach ($app_strings as $key => $value) {
		$rec['translation_module'] = 'cbtranslation';
		$rec['translation_key'] = $key;
		$rec['i18n'] = $value;
		$rec['locale'] = $lang;
		vtws_create('cbtranslation', $rec, $current_user);
	}
}

$import_modules = getAllowedPicklistModules(1);
$import_modules = array_merge($import_modules, array('Rss','Recyclebin'));
foreach ($import_modules as $impmod) {
	set_time_limit(0);
	foreach ($import_langs as $lang) {
		if (file_exists('modules/' . $impmod . '/language/' . $lang . '.lang.php')) {
			include 'modules/' . $impmod . '/language/' . $lang . '.lang.php';
			foreach ($mod_strings as $key => $value) {
				$rec['translation_module'] = $impmod;
				$rec['translation_key'] = $key;
				$rec['i18n'] = $value;
				$rec['locale'] = $lang;
				vtws_create('cbtranslation', $rec, $current_user);
			}
		}
	}
}

echo '</body></html>';
?>
