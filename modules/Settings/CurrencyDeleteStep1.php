<?php
/*********************************************************************************
** The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ********************************************************************************/
require_once 'include/utils/utils.php';

global $mod_strings, $app_strings, $theme, $adb;
$theme_path='themes/'.$theme.'/';

$delete_currency_id = vtlib_purify($_REQUEST['id']);
$result = $adb->pquery('select * from vtiger_currency_info where id=?', array($delete_currency_id));
$delete_currencyname = $adb->query_result($result, 0, 'currency_name');

$output ='<section role="dialog" tabindex="-1" aria-labelledby="modal-heading-01" aria-modal="true" aria-describedby="modal-content-id-1" class="slds-modal slds-fade-in-open">
			<div class="slds-modal__container" id="CurrencyDeleteLay">
				<header class="slds-modal__header">
					<button class="slds-button  slds-button_icon slds-modal__close slds-button_icon-inverse" onClick="document.getElementById(\'currencydiv\').innerHTML=\'\'" title="Close">
						<svg class="slds-button__icon slds-button__icon_large" aria-hidden="true">
						<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#close"></use>
						</svg>
						<span class="slds-assistive-text">Close</span>
					</button>
					<h2 id="modal-heading-01" class="slds-modal__title slds-hyphenate">'.$mod_strings['LBL_DELETE_CURRENCY'].'</h2>
				</header>
				<div class="slds-modal__content slds-p-around_medium" id="modal-content-id-1">
					<form name="newCurrencyForm" action="index.php" style="margin="0" onsubmit="VtigerJS_DialogBox.block();">
						<input type="hidden" name="module" value="Settings">
						<input type="hidden" name="action" value="CurrencyDelete">
						<input type="hidden" name="delete_currency_id" value="'.$delete_currency_id.'">
						<table border=0 cellspacing=0 cellpadding=5 width=95% align="center" class="slds-table slds-table_cell-buffer slds-no-row-hover slds-table-no_bordered">
							<tr>
								<td class=small >
									<table border=0 celspacing=0 cellpadding=5 width=100% align=center bgcolor=white>
										<tr>
											<td width="50%" class=""><b>'.$mod_strings['LBL_CURRDEL'].'</b></td>
											<td width="50%" class=""><b>'.getTranslatedCurrencyString($delete_currencyname).'</b></td>
										</tr>
										<tr>
											<td><b>'.$mod_strings['LBL_TRANSCURR'].'</b></td>
											<td>';
											$output .= '<select class="slds-select" name="transfer_currency_id" id="transfer_currency_id">';
											$result = $adb->pquery('select * from vtiger_currency_info where currency_status = ? and deleted=0', array('Active'));
											$temprow = $adb->fetch_array($result);
do {
	$currencyname=$temprow['currency_name'];
	$currencyid=$temprow['id'];
	if ($delete_currency_id != $currencyid) {
		$output.='<option value="'.$currencyid.'">'.getTranslatedCurrencyString($currencyname).'</option>';
	}
} while ($temprow = $adb->fetch_array($result));
											$output .= '</td>
										</tr>
									</table>
								</td>
							</tr>
						</table>
						<table border=0 cellspacing=0 cellpadding=5 width=100% class="">
							<tr>
								<td>&nbsp;&nbsp;<input type="button" onclick="transferCurrency('.$delete_currency_id.'), document.getElementById(\'currencydiv\').innerHTML=\'\'" name="Delete" value="'.$app_strings['LBL_SAVE_BUTTON_LABEL']
									.'" class="slds-button slds-button_success save">
								</td>
							</tr>
						</table>
					</form>
				</div>
			</div>
		</section>
			<div class="slds-backdrop slds-backdrop_open"></div>';
echo $output;
?>