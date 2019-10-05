<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html><head><title>TSolucio::coreBOS Customizations</title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<style type="text/css">@import url("themes/softed/style.css");br { display: block; margin: 2px; }</style>
</head><body class=small style="font-size: 12px; margin: 2px; padding: 2px; background-color:#f7fff3; ">
<table width="100%" border=0><tr><td><span style='color:red;float:right;margin-right:30px;'><h2>Proud member of the <a href='http://corebos.org'>coreBOS</a> family!</h2></span></td></tr></table>
<hr style="height: 1px">
<?php
// Turn on debugging level
$Vtiger_Utils_Log = true;

include_once 'vtlib/Vtiger/Module.php';

if (empty($_REQUEST['modulename'])) {
	echo "<br/><H2>'modulename' parameter is mandatory</H2>";
	echo "<br/><H2>Es obligatorio introducir el parámetro 'modulename'</H2>";
	die();
}
$module = Vtiger_Module::getInstance($_REQUEST['modulename']);

if ($module) {
	$module->addLink('DETAILVIEWBASIC', 'Photo2Document', 'javascript:window.open(\'index.php?module=Utilities&action=UtilitiesAjax&file=Photo2Document&formodule=$MODULE$&forrecord=$RECORD$\',\'photo2doc\',\'width=800,height=760\');', 'themes/images/webcam16.png');
} else {
	echo '<br/><H2>Failed to find '.$_REQUEST['modulename'].' module.</h2><br>';
	echo '<br/><H2>No se ha podido encontrar el módulo '.$_REQUEST['modulename'].'.</h2><br>';
}
?>
</body>
</html>
