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
<link rel="stylesheet" type="text/css" media="all" href="jscalendar/calendar-win2k-cold-1.css">
<table class="small" bgcolor="#ffffff" border="0" cellpadding="5" cellspacing="0" height='530' width="100%">
	<tbody>
	<tr valign='top'>
		<td colspan="2">
			<span class="genHeaderGray">{$MOD.LBL_SHARING_TYPE}</span><br>
			{$MOD.LBL_SELECT_REPORT_TYPE_TO_CONTROL_ACCESS}
			<hr>
		</td>
	</tr>
	<tr>
	<td colspan="2">
	<table class="small" border="0" cellpadding="0" cellspacing="0" width="100%">
		<tbody>
			<tr>
				<td colspan="4">
					<table width="100%" cellspacing="0" cellpadding="0" border="0" class="small">
						<tbody>
							<tr>
								<td nowrap="" align="center" id="mi" style="width: 100px;" colspan="2" class="detailedViewHeader">
									<b>{$MOD.LBL_SHARING}</b>
								</td>
							</tr>
						</tbody>
					</table>
				</td>
			</tr>
			<tr>
				<td colspan=4>
					<table width="100%" cellspacing="0" cellpadding="0" class="small" height="190px">
						<tr valign=top>
							<td colspan="2">
								<table width="100%" border="0" cellpadding="5" class="small" cellspacing="0" align="center">
									<tr>
										<td align="right" class="dvtCellLabel" width="50%">{$MOD.SELECT_FILTER_TYPE} :</td>
										<td class="dvtCellInfo" width="50%" align="left">
											<select name="stdtypeFilter" id="stdtypeFilter" class="small" onchange='toggleAssignType(this.options[this.selectedIndex].value );'>
											</select>
										</td>
									</tr>
									<tr>
										<td colspan="2">
											<div id="assign_team" style="display:none">
												<table width="100%" border="0" cellpadding="0" class="small" cellspacing="0" align="center">
													<tr>
														<td align=left colspan=2 class='dvtCellLabel' valign=top>
															<select id="memberType" name="memberType" class="small" onchange="show_Options()">
															<option value="groups" selected>{$MOD.LBL_GROUPS}</option>
															<option value="users">{$MOD.LBL_USERS}</option>
															</select>
															<input type="hidden" name="findStr" class="small">&nbsp;
														</td>
														<td align=right colspan=1 class='dvtCellLabel' valign=top>
															<b>{$MOD.LBL_MEMBERS}</b>
														</td>
													</tr>
													<tr>
														<td valign=top width=45%>
																<select id="availableList" name="availableList" multiple size="5" class="small crmFormList"></select>
																<input type="hidden" name="selectedColumnsStr"/>
														</td>
														<td width="10%">
															<div align="center">
																<input type="button" name="Button" value="&nbsp;&rsaquo;&rsaquo;&nbsp;" onClick="addColumns()" class="crmButton small"/><br /><br />
																<input type="button" name="Button1" value="&nbsp;&lsaquo;&lsaquo;&nbsp;" onClick="removeColumns()" class="crmButton small"/>
															</div>
														</td>
														<td class="small" width="45%" align='right' valign=top>
															<select id="columnsSelected" name="columnsSelected" multiple size="5" class="small crmFormList">
															</select>
														</td>
													</tr>
												</table>
											</div>
										</td>
									</tr>
								</table>
							</td>
						</tr>
					</table>
				</td>
			</tr>
		</tbody>
	</table>
	</td></tr>
	<tr><td colspan="2" height="205">&nbsp;</td></tr>
	</tbody>
</table>
<script>
stdfilterTypeDisplay();
var moveupLinkObj,moveupDisabledObj,movedownLinkObj,movedownDisabledObj;
</script>

