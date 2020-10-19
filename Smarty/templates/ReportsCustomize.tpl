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
<!-- Customized Reports Table Starts Here  -->
<form>
{if $DEL_DENIED neq ""}
<span id="action_msg_status" class="small" align="left"><font color=red><b>{$MOD.LBL_PERM_DENIED} {$DEL_DENIED}</b> </font></span>
{/if}
<input id="folder_ids" class="slds-input" name="folderId" type="hidden" value='{$FOLDE_IDS}'>
{assign var=poscount value=0}
{foreach item=reportfolder from=$REPT_CUSFLDR}
{assign var=poscount value=$poscount+1}
	<div class="slds-grid">
		<div class="slds-col slds-size_1-of-1">
			<div class="slds-page-header">
				<div class="slds-grid">
					<div class="slds-col slds-size_1-of-2 slds-p-vertical_small">
						<div class="slds-page-header__col-title">
							<div class="slds-page-header__name">
								<div class="slds-text-title">
									<h1>
										<span id='folder{$reportfolder.id}'> {$reportfolder.name} <span class="slds-text-color_weak"><em> {if $reportfolder.description neq ''} - {$reportfolder.description} {/if} </em></span> </span>
									</h1>
								</div>
							</div>
						</div>
					</div>
					<div class="slds-col slds-size_1-of-2 slds-p-vertical_small slds-text-align_right">
						<a class="cardinner maximize minmaxtoggle" onclick="toggleGridCard({$poscount})"> 
							<svg class="slds-icon slds-icon_x-small slds-icon-text-light minimizereport expandthis_svg" aria-hidden="true" >
								<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#contract"></use> 
							</svg>
							<svg class="slds-icon slds-icon_x-small slds-icon-text-light maximizereport hidethis_svg" aria-hidden="true" >
								<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#expand"></use> 
							</svg>
						</a>
					</div>
				</div>
				<div class="slds-grid">
					<div class="slds-col slds-size_7-of-12" id="repposition{$poscount}">
						<input name="newReportInThisModule" value="{$MOD.LBL_CREATE_REPORT}..." class="slds-button slds-button_brand" onclick="gcurrepfolderid={$reportfolder.id};fnvshobj(this,'reportLay')" type="button">
					</div>
					<div class="slds-col slds-size_5-of-12">
						<div class="slds-grid slds-grid_align-end">
							<div class="slds-col slds-size_1-of-2 slds-text-align_right">
								<input type="button" name="Edit" value=" {$MOD.LBL_RENAME_FOLDER} " class="slds-button slds-button_success" onClick='EditFolder("{$reportfolder.id}","{$reportfolder.fname}","{$reportfolder.fdescription}"),fnvshobj(this,"orgLay");'>
							</div>
							<div class="slds-col slds-size_1-of-2 slds-text-align_right">
								<input type="button" name="delete" value=" {$MOD.LBL_DELETE_FOLDER} " class="slds-button slds-button_destructive" onClick="DeleteFolder('{$reportfolder.id}');">
							</div>
						</div>
					</div>
			</div>
			</div>
		</div>
	</div>

	<div class="slds-grid grid_reports_container">
		<div class="slds-col slds-size_1-of-1">
			<table class="slds-table slds-table_cell-buffer slds-table_bordered slds-table_striped" role="grid">
				<thead>
					<tr class="slds-line-height_reset">
						<th class="" scope="col" width="5%">
							<div class="slds-truncate" title="#"> <input type="checkbox" name="selectall" onclick='toggleSelect(this.checked,"selected_id{$reportfolder.id}")' value="checkbox" /> </div>
						</th>
						<th class="" scope="col" width="35%">
							<div class="slds-truncate" title="{$MOD.LBL_REPORT_NAME}">{$MOD.LBL_REPORT_NAME}</div>
						</th>
						<th class="" scope="col" width="50%">
							<div class="slds-truncate" title="{$MOD.LBL_DESCRIPTION}">{$MOD.LBL_DESCRIPTION}</div>
						</th>
						<th class="" scope="col" width="10%">
							<div class="slds-truncate" title="{$MOD.LBL_TOOLS}">
								{$MOD.LBL_TOOLS}
							</div>
						</th>
					</tr>
				</thead>
				<tbody>
					{foreach name=reportdtls item=reportdetails from=$reportfolder.details}
						<tr class="lvtColData" onmouseover="this.className='lvtColDataHover'" onmouseout="this.className='lvtColData'" bgcolor="white">
							<td>
								{if $reportdetails.customizable eq '1' && $reportdetails.editable eq 'true'}
									<input name="selected_id{$reportfolder.id}" value="{$reportdetails.reportid}" onclick='toggleSelectAll(this.name,"selectall")' type="checkbox">
								{/if}
							</td>
							<td>
								{if $reportdetails.cbreporttype eq 'external'}
									<a href="{$reportdetails.moreinfo}" target="_blank">{$reportdetails.reportname|@getTranslatedString:$MODULE}</a>
								{else}
									<a href="index.php?module=Reports&action=SaveAndRun&record={$reportdetails.reportid}&folderid={$reportfolder.id}">{$reportdetails.reportname}</a>
								{/if}
								{if $reportdetails.sharingtype eq 'Shared'}
									<img src="{'Meetings.gif'|@vtiger_imageurl:$THEME}" align="absmiddle" border=0 height=12 width=12 />
								{/if}
							</td>
							<td>{$reportdetails.description}</td>
							<td>
								{if $reportdetails.customizable eq '1' && $reportdetails.editable eq 'true'}
									<a href="javascript:;" onClick="editReport('{$reportdetails.reportid}');" class="slds-button">
										<svg class="slds-icon slds-icon_x-small slds-icon-text-light" aria-hidden="true" >
											<use xlink:href="include/LD/assets/icons/action-sprite/svg/symbols.svg#edit"></use> 
										</svg>
									</a>
								{/if}
								{if $ISADMIN || ($reportdetails.state neq 'SAVED' && $reportdetails.editable eq 'true')}
								<a href="javascript:;" onClick="DeleteReport('{$reportdetails.reportid}');" class="slds-button">
									<svg class="slds-icon slds-icon_x-small slds-icon-text-light" aria-hidden="true" title="Delete...">
										<use xlink:href="include/LD/assets/icons/action-sprite/svg/symbols.svg#close"></use> 
									</svg>
								</a>
								{/if}
								{if $reportdetails.cbreporttype neq 'external' && $reportdetails.export eq 'yes'}
								<a href="javascript:void(0);" class="slds-button" onclick="gotourl('index.php?module=Reports&action=ReportsAjax&file=CreateCSV&record={$reportdetails.reportid}');" alt="{$MOD.LBL_EXPORTCSV}" title="{$MOD.LBL_EXPORTCSV}">
									<svg class="slds-icon slds-icon_x-small" aria-hidden="true" >
										<use xlink:href="include/LD/assets/icons/doctype-sprite/svg/symbols.svg#csv"></use> 
									</svg>
								</a>
								<a href="javascript:void(0);" class="slds-button" onclick="gotourl('index.php?module=Reports&action=CreateXL&record={$reportdetails.reportid}');" alt="{$MOD.LBL_EXPORTXL_BUTTON}" title="{$MOD.LBL_EXPORTXL_BUTTON}">
									<svg class="slds-icon slds-icon_x-small" aria-hidden="true" >
										<use xlink:href="include/LD/assets/icons/doctype-sprite/svg/symbols.svg#excel"></use> 
									</svg>
								</a>
								<a href="javascript:void(0);" class="slds-button" onclick="gotourl('index.php?module=Reports&action=CreatePDF&record={$reportdetails.reportid}');" alt="{$MOD.LBL_EXPORTPDF_BUTTON}" title="{$MOD.LBL_EXPORTPDF_BUTTON}">
									<svg class="slds-icon slds-icon_x-small" aria-hidden="true" >
										<use xlink:href="include/LD/assets/icons/doctype-sprite/svg/symbols.svg#pdf"></use> 
									</svg>
								</a>
								{/if}
							</td>
						</tr>
					{/foreach}
				</tbody>
			</table>
		</div>
	</div>
<br />
{foreachelse}
<div align="center" class="createbtn_design">
<a href="javascript:;" onclick="fnvshobj(this,'orgLay');">{$MOD.LBL_CLICK_HERE}</a>&nbsp;{$MOD.LBL_TO_ADD_NEW_GROUP}
</div>
{/foreach}
</form>
<!-- Customized Reports Table Ends Here  -->

<div style="display: none;left:193px;top:106px;width:155px;" id="folderLay" onmouseout="fninvsh('folderLay')" onmouseover="fnvshNrm('folderLay')">
	<table bgcolor="#ffffff" border="0" cellpadding="0" cellspacing="0" width="100%">
		<tr><td style="border-bottom: 1px solid rgb(204, 204, 204); padding: 5px;" align="left"><b>{$MOD.LBL_MOVE_TO} :</b></td></tr>
		<tr>
		<td align="left">
		{foreach item=folder from=$REPT_FOLDERS}
		<a href="javascript:;" onClick='MoveReport("{$folder.id}","{$folder.fname}");' class="drop_down">- {$folder.name}</a>
		{/foreach}
		</td>
		</tr>
	</table>
</div>
