<?php
/*********************************************************************************
 ** The contents of this file are subject to the vtiger CRM Public License Version 1.0
  * ("License"); You may not use this file except in compliance with the License
  * The Original Code is:  vtiger CRM Open Source
  * The Initial Developer of the Original Code is vtiger.
  * Portions created by vtiger are Copyright (C) vtiger.
  * All Rights Reserved.
  ********************************************************************************/
$ajaxaction = vtlib_purify($_REQUEST['ajxaction']);
global $current_user, $default_charset;

$crmid = vtlib_purify($_REQUEST["recordid"]);
$module = vtlib_purify($_REQUEST["module"]);
$userid = $current_user->id;
if($ajaxaction == "SAVETAG")
{
	if (isset($_REQUEST['tagfields']) && trim($_REQUEST['tagfields']) != '') {
		require_once('include/freetag/freetag.class.php');
		$tagfields = function_exists('iconv') ? @iconv("UTF-8",$default_charset,$_REQUEST['tagfields']) : $_REQUEST['tagfields'];
		$tagfields = str_replace(array("'",'"'),'',$tagfields);
		if($tagfields != '') {
			$freetag = new freetag();
			$freetag->tag_object($userid,$crmid,$tagfields,$module);
			$tagcloud = $freetag->get_tag_cloud_html($module,$userid,$crmid);
			echo $tagcloud;
		}
	}
	else
	{
		echo ":#:FAILURE";
	}
}
elseif($ajaxaction == 'GETTAGCLOUD')
{
	require_once('include/freetag/freetag.class.php');
	$freetag = new freetag();
	if(trim($module) != "")
	{
		$tagcloud = $freetag->get_tag_cloud_html($module,$userid,$crmid);
		echo $tagcloud;
	}else
	{
		$tagcloud = $freetag->get_tag_cloud_html("",$userid);
		echo $tagcloud;
	}
}elseif($ajaxaction == 'DELETETAG')
{
	if(is_numeric($_REQUEST['tagid'])) {
		$tagid = vtlib_purify($_REQUEST['tagid']);
		require_once('include/freetag/freetag.class.php');
		$freetag = new freetag();
		$tag = $freetag->get_tag_from_id($tagid);
		$delok = $freetag->delete_object_tag($userid, $crmid, $tag);
		if ($delok)
			echo 'SUCCESS';
		else
			die("An invalid tagid to delete.");
	} else {
		die("An invalid tagid to delete.");
	}
}
?>
