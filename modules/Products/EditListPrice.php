<?php
/*+********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ********************************************************************************/
global $mod_strings, $app_strings, $theme;
$theme_path='themes/'.$theme.'/';
$image_path=$theme_path.'images/';

if (isset($_REQUEST['return_module']) && $_REQUEST['return_module']=='PriceBooks') {
	$pricebook_id = vtlib_purify($_REQUEST['pricebook_id']);
	$product_id = vtlib_purify($_REQUEST['record']);
	$listprice = vtlib_purify($_REQUEST['listprice']);
	$return_action = 'CallRelatedList';
	$return_id = vtlib_purify($_REQUEST['pricebook_id']);
} else {
	$product_id = vtlib_purify($_REQUEST['record']);
	$pricebook_id = vtlib_purify($_REQUEST['pricebook_id']);
	$listprice = getListPrice($product_id, $pricebook_id);
	$return_action = 'CallRelatedList';
	$return_id = vtlib_purify($_REQUEST['pricebook_id']);
}
$onSubmit = 'if (!verify_data()) { document.getElementById(\'roleLay\').style.display=\'inline\'; return false; }';
$onClose = 'document.getElementById(\'editlistprice\').style.display=\'none\';';
$output ='<div id="roleLay" style="display:block;border-radius:10px;" class="layerPopup">
	<form action="index.php" name="editpriceform" onSubmit="'.$onSubmit.'" method="post">
	<input type="hidden" name="module" value="Products">
	<input type="hidden" name="action" value="UpdateListPrice">
	<input type="hidden" name="return_module" value="PriceBooks">
	<input type="hidden" name="return_action" value="CallRelatedList">
	<input type="hidden" name="record" value="'.$return_id.'">
	<input type="hidden" name="pricebook_id" value="'.$pricebook_id.'">
	<input type="hidden" name="product_id" value="'.$product_id.'">
	<div class="slds-form">
	<div class="slds-form__row" style="margin:unset;">
		<span class="slds-page-header" style="flex:auto;">
			<strong>'.getTranslatedString('LBL_EDITLISTPRICE', 'Products').'</strong>
			<span class="cblds-float_right">
		<button type="button" class="slds-button slds-button_icon" title="'.$app_strings['LBL_CLOSE'].'" onClick="'.$onClose.'">
			<svg class="slds-button__icon slds-button__icon_small" aria-hidden="true">
				<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#close"></use>
			</svg>
			<span class="slds-assistive-text">'.$app_strings['LBL_CLOSE'].'</span>
		</button>
			</span>
		</span>
	</div>
	<div class="slds-form__row">
		<div class="slds-form__item" role="listitem">
		<div class="slds-form-element slds-form-element_edit slds-form-element_stacked slds-m-top_small">
			<input class="slds-input" type="text" id="list_price" name="list_price" value="'.$listprice.'" />
		</div>
		</div>
	</div>
	<div class="slds-form__row slds-align_absolute-center slds-m-bottom_small">
	<button class="slds-button slds-button_neutral" type="submit">'.$app_strings['LBL_SAVE_BUTTON_LABEL'].'</button>
	<button	class="slds-button slds-button_destructive" type="button" onClick="'.$onClose.'">'
		.$app_strings["LBL_CANCEL_BUTTON_LABEL"].'</button>
	</div>
</form>
</div>';
echo $output;
?>
