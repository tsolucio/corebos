<form id="license" name="license" action="index.php" method="POST" >
<input type="hidden" name="module" value="Users">
<?php
global $current_language, $app_strings;
if (!empty($_REQUEST['record'])) {
	echo '<input type="hidden" name="record" value="'.$_REQUEST['record'].'">';
}
?>
<input type="hidden" name="mode" value="<?php echo isset($_REQUEST['mode']) ? vtlib_purify($_REQUEST['mode']) : ''; ?>">
<input type="hidden" name="action" value="<?php echo vtlib_purify($_REQUEST['action']); ?>">
<input type="hidden" name="parenttab" value="<?php echo vtlib_purify($_REQUEST['parenttab']); ?>">
<input type="hidden" name="return_module" value="<?php echo vtlib_purify($_REQUEST['return_module']); ?>">
<input type="hidden" name="return_id" value="<?php echo isset($_REQUEST['return_id']) ? vtlib_purify($_REQUEST['return_id']) : ''; ?>">
<input type="hidden" name="return_action" value="<?php echo vtlib_purify($_REQUEST['return_action']); ?>">
<input type="hidden" name="return_viewname" value="<?php echo isset($_REQUEST['return_viewname']) ? vtlib_purify($_REQUEST['return_viewname']) : ''; ?>">
<input type="hidden" name="isDuplicate" value="<?php echo isset($_REQUEST['isDuplicate']) ? vtlib_purify($_REQUEST['isDuplicate']) : ''; ?>">
<input type="hidden" name="creation_accepted" value="yes">
<br/>
<div style="width:98%;padding:8px;">
<?php
if (file_exists("modules/Users/language/{$current_language}.showLicense.html")) {
	include "modules/Users/language/{$current_language}.showLicense.html";
} else {
	include "modules/Users/language/en_us.showLicense.html";
}
?>
</div>
<br/>
<p width=90% align=center><input type="checkbox" id="accept_charge">
	<span style="font-size: 12px;font-weight: bold;"><?php echo getTranslatedString('accept_charge', 'Documents'); ?></span>
	<br/>
	<input title="<?php echo $app_strings['LBL_SAVE_BUTTON_TITLE']; ?>" accessKey="<?php echo $app_strings['LBL_SAVE_BUTTON_KEY']; ?>"
		class="crmbutton small save" type="submit" name="button" value="  <?php echo $app_strings['LBL_SAVE_BUTTON_LABEL']; ?>  "
		style="width:70px;" align=center onclick="return jQuery('#accept_charge').is(':checked');">
</p>
</form>