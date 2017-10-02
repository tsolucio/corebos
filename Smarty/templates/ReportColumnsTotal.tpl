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
<table class="small" bgcolor="#ffffff" border="0" cellpadding="5" cellspacing="0" height="532" width="100%" valign="top">
	<tbody>
		<tr>
			<td colspan="2">
				<span class="genHeaderGray">{$MOD.LBL_CALCULATIONS}</span><br>
				<span id="cbreptypenotctsubtitle">{$MOD.LBL_SELECT_COLUMNS_TO_TOTAL}</span>
				<span id="cbreptypectsubtitle" style="display:none">{$MOD.LBL_AGG_FUNCTION_CHOOSE}</span>
				<hr>
			</td>
		</tr>
		<tr id="cbreptypecttrow" style="display:none">
		<td colspan="2"><div style="overflow:auto;height:448px">
			<b>{$MOD.LBL_AGG_COLUMN}</b><br><br>
			<select id="aggfield" name="aggfield" class="txtBox"></select><br><br>
			<b>{$MOD.LBL_AGG_FUNCTION}</b><br><br>
			<select name="crosstabaggfunction" id="crosstabaggfunction">
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
					<table style="background-color: rgb(204, 204, 204);" class="small" border="0" cellpadding="5" cellspacing="1" width="100%" valign="top">
						<tbody>
							<tr>
								<td class="lvtCol" nowrap width="40%">{$MOD.LBL_COLUMNS}</td>
								<td class="lvtCol" nowrap width="15%">{$MOD.LBL_COLUMNS_SUM}</td>
								<td class="lvtCol" nowrap width="15%">{$MOD.LBL_COLUMNS_AVERAGE}</td>
								<td class="lvtCol" nowrap width="15%">{$MOD.LBL_COLUMNS_LOW_VALUE}</td>
								<td class="lvtCol" nowrap width="15%">{$MOD.LBL_COLUMNS_LARGE_VALUE}</td>
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
