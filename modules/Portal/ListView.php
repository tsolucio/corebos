<?php
/*********************************************************************************
 ** The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ********************************************************************************/
require_once 'Smarty_setup.php';
global $theme;
$theme_path='themes/'.$theme.'/';
$image_path=$theme_path.'images/';

global $app_strings, $mod_strings, $currentModule, $current_language, $adb;
$current_module_strings = return_module_language($current_language, 'Portal');

$result=$adb->pquery('select * from vtiger_portal', array());
//Getting the Default URL Value if any
$default_url = $adb->query_result($result, 0, 'portalurl');
$no_of_portals=$adb->num_rows($result);
$portal_info=array();
$def_ault_embed = '';
//added as an enhancement to set default
?>
<script type="text/javascript">
var mysitesArray = new Array()
<?php
//added object in javascript array to define if site can be included in iframe
for ($i=0; $i<$no_of_portals; $i++) {
	$portalname = $adb->query_result($result, $i, 'portalname');
	$portalurl = $adb->query_result($result, $i, 'portalurl');
	//this call slows down the page but is the only way I have found to test
	//should include some type of waiting sign
	$embed = isEmbbedeable($portalurl);
	//added as an enhancement to set default value
	$portalid = $adb->query_result($result, $i, 'portalid');
	$set_default = $adb->query_result($result, $i, 'setdefault');
	$portal_array['set_def'] = $set_default;
	$portal_array['portalid'] = $portalid;
	if ($set_default == 1) {
		$def_ault = $portalurl;
		$def_ault_embed = $embed;
	}
	$portal_array['portalname'] = (strlen($portalname) > 100) ? (substr($portalname, 0, 100).'...') : $portalname;
	$portal_array['portalurl'] = $portalurl;
	$portal_array['portaldisplayurl'] = (strlen($portalurl) > 100) ? (substr($portalurl, 0, 100).'...') : $portalurl;
	//added item in php array to define if site can be included in iframe
	$portal_array['embed'] = $embed;
	$portal_info[] = $portal_array;
	?>
	mysitesArray['<?php echo $portalid;?>'] = {url: "<?php echo $portalurl;?>", embed: <?php echo $embed;?>};
<?php
}
?>
</script>
<?php
if (empty($def_ault)) {
	$def_ault = $default_url;
}
$smarty = new vtigerCRM_Smarty;
$smarty->assign('THEME', $theme);
$smarty->assign('IMAGE_PATH', $image_path);
$smarty->assign('MOD', $mod_strings);
$smarty->assign('DEFAULT_URL', $def_ault);
//added default embed
$smarty->assign('DEFAULT_EMBED', $def_ault_embed);
$smarty->assign('APP', $app_strings);
$smarty->assign('PORTAL_COUNT', count($portal_info));
$smarty->assign('PORTALS', $portal_info);
$smarty->assign('MODULE', $currentModule);
$smarty->assign('DEFAULT', 'yes');
$smarty->assign('CATEGORY', getParentTab());
$tool_buttons = array(
	'EditView' => 'no',
	'CreateView' => 'no',
	'index' => 'no',
	'Import' => 'no',
	'Export' => 'no',
	'Merge' => 'no',
	'DuplicatesHandling' => 'no',
	'Calendar' => 'no',
	'moduleSettings' => 'no',
);
$smarty->assign('CHECK', $tool_buttons);
$smarty->assign('CUSTOM_MODULE', false);
if (isset($_REQUEST['datamode']) && $_REQUEST['datamode'] == 'data') {
	$smarty->display('MySitesContents.tpl');
} elseif (isset($_REQUEST['datamode']) && $_REQUEST['datamode'] == 'manage') {
	$smarty->display('MySitesManage.tpl');
} else {
	$smarty->display('MySites.tpl');
}

//read http response variables to determine if site can be embedded
function isEmbbedeable($url) {
	$ch = curl_init();
	$options = array(
		CURLOPT_URL => $url,
		CURLOPT_RETURNTRANSFER => true,
		CURLOPT_NOBODY => true,
		CURLOPT_HEADER => true,
		CURLOPT_CONNECTTIMEOUT => 120,
		CURLOPT_TIMEOUT => 120,
		CURLOPT_SSL_VERIFYHOST => false
	);
	curl_setopt_array($ch, $options);
	$response = curl_exec($ch);
	$error = curl_errno($ch);
	//if it needs a certificate to be able so show, send to another window
	if ($error == CURLE_SSL_PEER_CERTIFICATE || $error == CURLE_SSL_CACERT || $error == 77) {
		//it is a https request
		return 0;
	} else {
		$header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
		$headers = substr($response, 0, $header_size);
		return (strpos($headers, 'X-Frame-Options: deny') > -1 || strpos($headers, 'X-Frame-Options: SAMEORIGIN') > -1 ? 0 : 1);
	}
	curl_close($ch);
}
?>