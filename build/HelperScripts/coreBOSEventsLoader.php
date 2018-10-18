<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html><head><title>coreBOS Utility loader</title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<style type="text/css">@import url("themes/softed/style.css");br { display: block; margin: 2px; }</style>
</head><body class=small style="font-size: 12px; margin: 2px; padding: 2px; background-color:#f7fff3; ">
<table width="100%" border=0><tr><td><span style='color:red;float:right;margin-right:30px;'><h2>Proud member of the <a href='http://corebos.org'>coreBOS</a> family!</h2></span></td></tr></table>
<hr style="height: 1px">
<?php
// Turn on debugging level
$Vtiger_Utils_Log = true;
include_once 'vtlib/Vtiger/Module.php';
require_once 'include/events/include.inc';
global $adb;

function registerEvent($event) {
	global $adb;
	$em = new VTEventsManager($adb);
	$em->registerHandler($event, 'build/HelperScripts/coreBOSEventsExample.php', 'coreBOSEventsExample');
	echo "<h4>Event $event registered.</h4>";
}

registerEvent('corebos.footer');
registerEvent('corebos.header');
registerEvent('corebos.footer.prefooter');
registerEvent('corebos.header.premenu');
registerEvent('corebos.filter.listview.querygenerator.before');
registerEvent('corebos.filter.listview.querygenerator.after');
registerEvent('corebos.filter.listview.querygenerator.query');
registerEvent('corebos.filter.listview.render');
registerEvent('corebos.filter.listview.header');
registerEvent('corebos.filter.listview.filter.show');
registerEvent('corebos.filter.editview.setObjectValues');
registerEvent('corebos.filter.link.show');
registerEvent('corebos.filter.ModComments.canAdd');
registerEvent('corebos.filter.ModComments.queryCriteria');
registerEvent('corebos.entity.link.before');
registerEvent('corebos.entity.link.after');
registerEvent('corebos.entity.link.delete');
registerEvent('corebos.audit.action');
registerEvent('corebos.audit.authenticate');
registerEvent('corebos.audit.login');
registerEvent('corebos.audit.logout');
registerEvent('corebos.audit.login.attempt');

// Permission examples live in their own file
$em = new VTEventsManager($adb);
$em->registerHandler('corebos.permissions.accessquery', 'build/HelperScripts/coreBOSEventsPermission.php', 'coreBOSEventsPermissionExample');
$em->registerHandler('corebos.permissions.ispermitted', 'build/HelperScripts/coreBOSEventsPermission.php', 'coreBOSEventsPermissionExample');
echo "<h4>Permission Events accessquery and ispermitted registered.</h4>";

echo '</body></html>';
?>
