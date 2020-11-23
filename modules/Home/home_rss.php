<?php
/*+********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *******************************************************************************/
require_once 'config.inc.php';
require_once 'include/utils/utils.php';
global $current_user, $adb;
header('Cache-Control: private, pre-check=0, post-check=0, max-age=0');

header('Expires: ' . gmdate('D, d M Y H:i:s', time()) . ' GMT');
header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
header('Content-Type: text/xml');

echo "<?xml version=\"1.0\" encoding=\"utf-8\" ?>\n";
echo "<rss version=\"2.0\">\n";
echo "<channel>\n";
echo '  <title>'.GlobalVariable::getVariable('Application_UI_Name', 'coreBOS')."</title>\n";
echo "  <link><![CDATA[".$site_URL."/index.php?module=Home&action=home_rss]]></link>\n";
echo '  <description>'.GlobalVariable::getVariable('Application_UI_Name', 'coreBOS')." Record Feed</description>\n";
echo '  <lastBuildDate>' . gmdate('D, d M Y H:i:s', time()) . " GMT</lastBuildDate>\n";
echo '  <generator>'.GlobalVariable::getVariable('Application_UI_Name', 'coreBOS')."</generator>\n";

//retrieving notifications******************************
$query='select setype,vtiger_crmobject.crmid,vtiger_crmobject.smownerid,modifiedtime
	from vtiger_crmobject inner join vtiger_ownernotify on vtiger_crmobject.crmid=vtiger_ownernotify.crmid';
$result = $adb->pquery($query, array());
while ($mod_notify = $adb->fetch_array($result)) {
	$einfo = getEntityName($mod_notify['setype'], $mod_notify['crmid']);

	$author_id = $mod_notify['smownerid'];
	$entry_author = getUserFullName($author_id);
	$entry_author = htmlspecialchars($entry_author);

	$entry_link = $site_URL.'/index.php?modules='.$mod_notify['setype'].'&action=DetailView&record='.$mod_notify['crmid'];
	$entry_time = $mod_notify['modifiedtime'];

	echo "  <item>\n";
	echo '    <title>'.getTranslatedString($mod_notify['setype'], $mod_notify['setype'])."</title>\n";
	echo '    <link><![CDATA['.$entry_link."]]></link>\n";
	echo '    <description>'.htmlspecialchars($einfo[$mod_notify['crmid']])."</description>\n";
	echo '    <author>'.$entry_author."</author>\n";
	echo '    <pubDate>'.$entry_time."</pubDate>\n";
	echo "  </item>\n";
}
echo "	</channel>\n";
echo "  </rss>\n";
?>
