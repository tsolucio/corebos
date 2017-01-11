{*<!--
/*********************************************************************************
  ** The contents of this file are subject to the vtiger CRM Public License Version 1.0
   * ("License"); You may not use this file except in compliance with the License
   * The Original Code is:  vtiger CRM Open Source
   * The Initial Developer of the Original Code is vtiger.
   * Portions created by vtiger are Copyright (C) vtiger.
   * All Rights Reserved.
  *
 ********************************************************************************/
-->*}
<table style="display: none;" id="grouping_section" class="small" bgcolor="#ffffff" border="0" cellpadding="5" cellspacing="0" height='500' width="100%">
	<tbody>
		<tr>
			<td colspan="2">
				<span class="genHeaderGray">{$MOD.LBL_SPECIFY_GROUPING}</span><br>
				{$MOD.LBL_SELECT_COLUMNS_TO_GROUP_REPORTS}
				<hr>
			</td>
		</tr>
		<tr>
			<td  align="left" width="33%">
				{$MOD.LBL_GROUPING_SUMMARIZE}
				<select id="Group1" name="Group1" class="txtBox" onchange="getDateFieldGrouping('Group1')">
				</select>
			</td>
			<td id='Group1time'  align="left" width="33%">
				{$MOD.LBL_GROUPING_TIME}<br>
				<select id='groupbytime1' name='groupbytime1' class='txtBox'>
				</select>
			</td>
			<td  align="left" width="33%">
				{$MOD.LBL_GROUPING_SORT}<br>
				<select name="Sort1" id="Sort1" class="importBox">
				</select>
			</td>
		</tr>
		<tr>
			<td colspan="2">&nbsp;</td>
		</tr>
		<tr>
			<td  align="left">
				{$MOD.LBL_GROUPING_THEN_BY}<br>
				<select id="Group2" name="Group2" class="txtBox" onchange="getDateFieldGrouping('Group2')">
				</select>
			</td>
			<td id='Group2time'  align="left">
				{$MOD.LBL_GROUPING_TIME}<br>
				<select id='groupbytime2' name='groupbytime2' class='txtBox'>
				</select>
			</td>
			<td  align="left">
				{$MOD.LBL_GROUPING_SORT}<br>
				<select name="Sort2" id="Sort2" class="importBox">
				</select>
			</td>
		</tr>
		<tr>
			<td colspan="2">&nbsp;</td>
		</tr>
		<tr>
			<td  align="left">
				{$MOD.LBL_GROUPING_FINALLY_BY}<br>
				<select id="Group3" name="Group3" class="txtBox" onchange="getDateFieldGrouping('Group3')">
					{if isset($BLOCK3)}{$BLOCK3}{/if}
				</select>
			</td>
			<td id='Group3time' align="left">
				{$MOD.LBL_GROUPING_TIME}<br>
				<select id='groupbytime3' name='groupbytime3'  class='txtBox'>
				</select>
			</td>
			<td align="left">
				{$MOD.LBL_GROUPING_SORT}<br>
				<select name="Sort3" id="Sort3" class="importBox">
				</select>
			</td>
		</tr>
		<tr>
			<td colspan="2" height="305">&nbsp;</td>
		</tr>
	</tbody>

</table>
