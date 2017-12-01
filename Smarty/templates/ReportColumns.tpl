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
<table class="small" bgcolor="#ffffff" border="0" cellpadding="5" cellspacing="0"  valign="top" height="500" width="100%">
	<tbody>
		<tr>
			<td colspan="4">
				<div class="forceRelatedListSingleContainer">
					<article class="slds-card forceRelatedListCardDesktop" aria-describedby="header">
						<div class="slds-card__header slds-grid">
							<header class="slds-media slds-media--center slds-has-flexi-truncate">
								<div class="slds-media__body">
									<h2>
										<span class="prvPrfBigText slds-text-title--caps slds-truncate slds-m-right--xx-small actionLabel">
											<span class="genHeaderGray">{$MOD.LBL_SELECT_COLUMNS}</span>
										</span>
									</h2>
								</div>
							</header>
						</div>
							<div class="slds-card__body slds-card__body--inner">
								<div class="commentData"><font class="small"> {$MOD.LBL_SELECT_COLUMNS_TO_GENERATE_REPORTS} </font></div>
							</div>
					</article>
				</div>
				<table class="slds-table slds-no-row-hover">
					<tr id="aggfieldtablerow" style="display:none">
						<td colspan="2" ><b>{$MOD.LBL_AGG_FIELD}</b></td>
						<td colspan="2">
							<select id="pivotfield" name="pivotfield" class="slds-select"></select>
						</td>
					</tr>
					<tr align="center">
						<td></td>
						<td height="26" class="dvtCellLabel text-left"><b>{$MOD.LBL_AVAILABLE_FIELDS}</b></td>
						<td></td>
						<td class="dvtCellLabel text-left"><b>{$MOD.LBL_SELECTED_FIELDS}</b></td>
						<td></td>
					</tr>
					<tr valign="top">
						<td align="left" width="2%"></td>
						<td style="padding-right: 5px;" align="right" class="dvtCellInfo" width="40%">
							<select id="availList" size="5" multiple name="availList" class="slds-select"></select>
						</td>
						<td style="padding: 5px;" align="center" valign="middle" width="16%">
							<input name="add" value=" {$APP.LBL_ADD_ITEM} &gt " class=" slds-button slds-button--small slds-button_success" type="button" onClick="addColumn()">
						</td>
						<input type="hidden" name="selectedColumnsString"/>
						<td style="padding-left: 5px;" class="dvtCellInfo" align="left" width="40%">
							<select id="selectedColumns" size="5" name="selectedColumns" onchange="selectedColumnClick(this);" multiple class="slds-select"></select>
						</td>
						<td align="right" width="2%">
							<table border="0" cellpadding="0" cellspacing="0" class="moveColonsArrows">
								<tbody>
									<tr>
										<td>
											<img src="themes/images/movecol_up.gif" onmouseover="this.src='themes/images/movecol_up.gif'" onmouseout="this.src='themes/images/movecol_up.gif'" onclick="moveUp()" onmousedown="this.src='themes/images/movecol_up.gif'" align="absmiddle" border="0"> 
										</td>
									</tr>
									<tr>
										<td>
											<img src="themes/images/movecol_down.gif" onmouseover="this.src='themes/images/movecol_down.gif'" onmouseout="this.src='themes/images/movecol_down.gif'" onclick="moveDown()" onmousedown="this.src='themes/images/movecol_down.gif'" align="absmiddle" border="0">
										</td>
									</tr>
									<tr>
										<td>
											<img src="themes/images/movecol_del.gif" onmouseover="this.src='themes/images/movecol_del.gif'" onmouseout="this.src='themes/images/movecol_del.gif'" onclick="delColumn()" onmousedown="this.src='themes/images/movecol_del.gif'" align="absmiddle" border="0">
										</td>
									</tr>
								</tbody>
							</table>
						</td>
					</tr>
					<tr>
						<td align="center" colspan="4" height="60" class="step_error" id="step4_error" style="color:red;">&nbsp;</td>
					</tr>
				</table>
			</td>
		</tr>
		<tr><td colspan="4" height="215"></td></tr>
	</tbody>
</table>
