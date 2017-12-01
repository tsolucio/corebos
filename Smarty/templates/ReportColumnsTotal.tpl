{*<!--
/*********************************************************************************
  ** The contents of this file are subject to the vtiger CRM Public License Version 1.0
   * ("License"); You may not use this file except in compliance with the License
   * The Original Code is:  vtiger CRM Open Source
   * The Initial Developer of the Original Code is vtiger.
   * Portions created by vtiger are Copyright (C) vtiger.
   * All Rights Reserved.
 ********************************************************************************/
-->*}
<table class="small" border="0" cellpadding="5" cellspacing="0" height="532" width="100%" valign="top">
	<tbody>
		<tr>
			<td colspan="2">
				<div class="forceRelatedListSingleContainer">
					<article class="slds-card forceRelatedListCardDesktop" aria-describedby="header">
						<div class="slds-card__header slds-grid">
							<header class="slds-media slds-media--center slds-has-flexi-truncate">
								<div class="slds-media__body">
									<h2>
										<span class="prvPrfBigText slds-text-title--caps slds-truncate slds-m-right--xx-small actionLabel">
											<span class="genHeaderGray">{$MOD.LBL_CALCULATIONS}</span>
										</span>
									</h2>
								</div>
							</header>
						</div>
							<div class="slds-card__body slds-card__body--inner">
								<div class="commentData">
									<font class="small"> {$MOD.LBL_SELECT_COLUMNS_TO_TOTAL} </font>
									<span id="cbreptypectsubtitle" style="display:none">{$MOD.LBL_AGG_FUNCTION_CHOOSE}</span>
								</div>
							</div>
					</article>
				</div>
			</td>
		</tr>
		<tr id="cbreptypecttrow" style="display:none">
		<td colspan="2"><div style="overflow:auto;height:448px">
			<b>{$MOD.LBL_AGG_COLUMN}</b><br><br>
			<select id="aggfield" name="aggfield" class="slds-select"></select><br><br>
			<b>{$MOD.LBL_AGG_FUNCTION}</b><br><br>
			<select name="crosstabaggfunction" id="crosstabaggfunction" class="slds-select">
			<option value="count">{$MOD.LBL_COLUMNS_COUNT}</option>
			<option value="sum">{$MOD.LBL_COLUMNS_SUM}</option>
			<option value="avg">{$MOD.LBL_COLUMNS_AVERAGE}</option>
			<option value="min">{$MOD.LBL_COLUMNS_LOW_VALUE}</option>
			<option value="max">{$MOD.LBL_COLUMNS_LARGE_VALUE}</option>
			</select>
		</div></td>
		</tr>
		<tr id="cbreptypenotcttrow">
			<td colspan="2">
				<div style="overflow:auto;height:448px">
					<table class="slds-table slds-table--col-bordered slds-no-row-hover" border="0" cellpadding="5" cellspacing="1" width="100%" valign="top">
						<tbody>
							<tr class="slds-text-align--center slds-line-height--reset">
								<th class="slds-text-align--center" nowrap width="40%">{$MOD.LBL_COLUMNS}</th>
								<th class="slds-text-align--center" nowrap width="15%">{$MOD.LBL_COLUMNS_SUM}</th>
								<th class="slds-text-align--center" nowrap width="15%">{$MOD.LBL_COLUMNS_AVERAGE}</th>
								<th class="slds-text-align--center" nowrap width="15%">{$MOD.LBL_COLUMNS_LOW_VALUE}</th>
								<th class="slds-text-align--center" nowrap width="15%">{$MOD.LBL_COLUMNS_LARGE_VALUE}</th>
							</tr>
							<tbody id="totalcolumns">
							</tbody>
						</tbody>
					</table>
				</div>
			</td>
		</tr>
	</tbody>
</table>
