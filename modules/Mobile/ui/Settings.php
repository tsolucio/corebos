<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/

?>
<!doctype html>
<head>
	<title>Settings</title> 
	<link REL="SHORTCUT ICON" HREF="../../themes/images/crm-now_icon.ico">	
	<meta name="viewport" content="width=device-width, initial-scale=1"> 
	<script src="Js/jquery-1.8.3.min.js"></script>
	<link rel="stylesheet" href="Css/jquery.mobile.structure-1.4.5.min.css" />
	<link rel="stylesheet" href="Css/jquery.mobile.icons.min.css" />
	<link rel="stylesheet" href="Css/theme.css" />
	<script src="Js/jquery.mobile-1.4.5.min.js"></script>
	<script type="text/javascript" src="Mobile.js"></script>
</head>

<body>
<div data-role="page" data-theme="b" id="home_page">
	<div data-role="header" class="ui-bar" data-theme="b" data-position="fixed">
		<a href="index.php?_operation=logout" data-mini='true' data-role='button' data-inline='true'>Logout</a>
		<h4>Settings</h4>
	</div>
	<!-- /header -->
		
<a href="#popupMenu" data-rel="popup" data-transition="pop" class="ui-btn ui-corner-all ui-shadow ui-btn-inline ui-icon-bars ui-btn-icon-left ui-btn-c">Language</a>
	<div data-role="popup" id="popupMenu" data-theme="b">
        <ul data-role="listview" data-inset="true" style="min-width:190px;">
            <li style="border-bottom-left-radius: 0px; border-bottom-right-radius: 0px;" data-role="list-divider">Choose an language</li>
				<form>
					<fieldset data-role="controlgroup" style="margin: 0px;" data-mini="true">
						<input name="radio-choice-v-6" id="radio-choice-v-6a" value="on"  type="radio">
						<label style="border-top-right-radius: 0px; border-top-left-radius: 0px;" for="radio-choice-v-6a">Deutsch</label>
						<input name="radio-choice-v-6" id="radio-choice-v-6b" value="off" type="radio">
						<label for="radio-choice-v-6b">English</label>
					</fieldset>
				</form>
        </ul>
	</div>
	
<form>
	<ul data-role="listview" data-inset="true" data-theme = "c">
    &nbsp Save Password: <input data-role="flipswitch" name="flip-checkbox-1" id="flip-checkbox-1" type="checkbox"></ul>
</form>	

<form>
    <fieldset data-role="controlgroup" data-type="horizontal">
        <input name="radio-choice-h-2" id="radio-choice-h-2a" value="on" checked="checked" type="radio">
        <label for="radio-choice-h-2a">Default</label>
        <input data-theme="a" name="radio-choice-h-2" id="radio-choice-h-2b" value="off" type="radio">
        <label for="radio-choice-h-2b">Theme 1</label>
        <input data-theme="c" name="radio-choice-h-2" id="radio-choice-h-2c" value="other" type="radio">
        <label for="radio-choice-h-2c">Theme 2</label>
		<input data-theme="d" name="radio-choice-h-2" id="radio-choice-h-2d" value="other" type="radio">
        <label for="radio-choice-h-2d">Theme 3</label>
    </fieldset>
</form>

</div>		
</body>

