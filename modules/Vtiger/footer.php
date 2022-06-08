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
<footer class="slds-grid slds-card__footer slds-badge" style="width:100%;">
<div class="slds-col slds-size_1-of-2 slds-p-left_x-large" style='text-align:left;'>
<span class='small' style='color: rgb(153, 153, 153);'>
	<?php echo $coreBOS_uiapp_name; ?> <span id='_vtiger_product_version_'><?php echo $coreBOS_uiapp_version; ?></span>
</span>
<span class='small'>
<?php
if ($coreBOS_uiapp_showgitversion || $coreBOS_uiapp_showgitdate) {
	list($gitversion,$gitdate) = explode(' ', file_get_contents('include/sw-precache/gitversion'));
	$gitdate = trim(str_replace('-', '', $gitdate));
	echo '&nbsp;('.($coreBOS_uiapp_showgitversion ? $gitversion : '').($coreBOS_uiapp_showgitdate ? $gitdate : '').')';
}
if ($calculate_response_time) {
	echo '&nbsp;&nbsp;Server response time: '.round(microtime(true) - $startTime, 2).' seconds.';
}
?>
	</span>
</span>
</div>
<div class="slds-col slds-size_1-of-2 cblds-t-align_right">
<span class='cblds-t-align_right small slds-p-right_small'>
	&copy; 2004-<?php echo date('Y'); ?> <a href='<?php echo $coreBOS_uiapp_url; ?>' target='_blank' rel='noopener'><?php echo $coreBOS_uiapp_companyname; ?></a>
</span>
</div>
</footer>
<?php
if (isset($adb) && !empty($current_user->id)) {
	$COMMONFTRLINKS = Vtiger_Link::getAllByType(Vtiger_Link::IGNORE_MODULE, array('FOOTERSCRIPT'), array('MODULE'=>$currentModule));
	foreach ($COMMONFTRLINKS['FOOTERSCRIPT'] as $fscript) {
		echo '<script type="text/javascript" src="' . $fscript->linkurl . '"></script>';
	}
}
cbEventHandler::do_action('corebos.footer');
?>
</body>
</html>
