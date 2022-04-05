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
<link rel="stylesheet" href="include/gridstack/gridstack.min.css" type="text/css">
<link rel="stylesheet" href="include/gridstack/gridstack-extra.min.css" type="text/css">
<script src="include/gridstack/gridstack-h5.js" type="text/javascript"></script>

<div style="display: none;left:193px;top:106px;width:155px;z-index:10;" id="folderLay" onmouseout="fninvsh('folderLay')" onmouseover="fnvshNrm('folderLay')">
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

{include file='applicationmessage.tpl'}
<form onsubmit="return false;">
<div class="slds-grid reportsideexpandable">
	<div class="slds-col slds-size_12-of-12 mainbar" id="mainbar">
		<div class="grid-stack" data-gs-animate="yes">
			<input id="folder_ids" class="slds-input" name="folderId" type="hidden" value='{$FOLDE_IDS}'>
			{assign var=BOXEXPAND value="{ 'w':6,'h':3 }"}
			{assign var=BOXCOLLAPSE value="{ 'w':4,'h':2 }"}
			{assign var=poscount value=0}
			{foreach item=reportfolder from=$REPT_FLDR}
			{assign var=poscount value=$poscount+1}
			<div class="grid-stack-item" {if isset($REPORT_LAYOUT[$reportfolder.id])}{$REPORT_LAYOUT[$reportfolder.id]}{else}{$DEFAULT_LAYOUT}{/if} gs-id="{$reportfolder.id}" id="gridcard{$poscount}">
				<div class="grid-stack-item-content draggable_bordered"> 
					<div class="slds-grid">
						<div class="slds-col slds-size_1-of-1">
							<div class="slds-page-header">
								<div class="slds-grid">
									<div class="slds-col slds-size_5-of-6 slds-p-vertical_small">
										<div class="slds-page-header__col-title">
											<div class="slds-page-header__name">
												<div class="slds-text-title">
													<h1>
														<span id='folder{$reportfolder.id}'><strong>{$reportfolder.name|@getTranslatedString:$MODULE}</strong> <span class="slds-text-color_weak"><em> - {$reportfolder.description|@getTranslatedString:$MODULE} </em></span> </span>
													</h1>
												</div>
											</div>
										</div>
									</div>
									<div class="slds-col slds-size_1-of-6 slds-p-vertical_small slds-text-align_right">
										<svg class="slds-icon slds-icon_x-small slds-icon-text-light" aria-hidden="true" onclick="grid.update('gridcard{$poscount}', {$BOXCOLLAPSE})">
											<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#contract"></use>
										</svg>
										<svg class="slds-icon slds-icon_x-small slds-icon-text-light" aria-hidden="true" onclick="grid.update('gridcard{$poscount}', {$BOXEXPAND})">
											<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#expand"></use>
										</svg>
									</div>
								</div>
								<div class="slds-grid">
									<div class="slds-col slds-size_7-of-12" id="repposition{$poscount}">
										<button class="slds-button slds-button_brand" name="newReportInThisModule" onclick="gcurrepfolderid={$reportfolder.id};fnvshobj(this,'reportLay')" type="button"> {$MOD.LBL_CREATE_REPORT}... </button>
									</div>
									<div class="slds-col slds-size_5-of-12">
										<div class="slds-grid slds-grid_align-end">
											<div class="slds-col slds-size_1-of-2 slds-text-align_right">
												<button class="slds-button slds-button_success" name="Edit" onClick='EditFolder("{$reportfolder.id}","{$reportfolder.fname}","{$reportfolder.fdescription}"),fnvshobj(this,"orgLay");'> {$MOD.LBL_RENAME_FOLDER} </button>
											</div>
											<div class="slds-col slds-size_1-of-2 slds-text-align_right">
												{if $ISADMIN}
												<button class="slds-button slds-button_destructive" name="delete" onClick="DeleteFolder('{$reportfolder.id}');">  {$MOD.LBL_DELETE_FOLDER} </button>
												{/if}
											</div>
										</div>
									</div>
								</div>
							</div>
							<div class="slds-grid grid_reports_container">
								<div class="slds-col slds-size_1-of-1" style="overflow-x: auto;">
									<table class="slds-table slds-table_cell-buffer slds-table_bordered slds-table_striped" role="grid">
										<thead>
											<tr class="slds-line-height_reset">
												<th class="" scope="col" width="5%">
													<div class="slds-truncate" title="#">
														{if $reportfolder.state neq 'SAVED'}
														<input type="checkbox" name="selectall" onclick='toggleSelect(this.checked, "selected_id{$reportfolder.id}")' value="checkbox" />
														{else}
														#
														{/if}
													</div>
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
												<tr class="lvtColData slds-hint-parent" onmouseover="this.className='lvtColDataHover'" onmouseout="this.className='lvtColData'">
													<td>
														{if $reportdetails.customizable eq '1' && $reportdetails.editable eq 'true'}
														<input name="selected_id{$reportfolder.id}" value="{$reportdetails.reportid}" onclick='toggleSelectAll(this.name, "selectall")' type="checkbox">
														{else}
														{$smarty.foreach.reportdtls.iteration}
														{/if}
													</td>
													<td>
													{if $reportdetails.cbreporttype eq 'external'}
														<a href="{$reportdetails.moreinfo}" target="_blank">{$reportdetails.reportname|@getTranslatedString:$MODULE}</a>
													{else}
														<a href="index.php?module=Reports&action=SaveAndRun&record={$reportdetails.reportid}&folderid={$reportfolder.id}">{$reportdetails.reportname|@getTranslatedString:$MODULE}</a>
													{/if}
													{if $reportdetails.sharingtype eq 'Shared'}
														<svg class="slds-icon slds-icon_x-small slds-icon-text-light" aria-hidden="true" alt="shared">
															<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#privately_shared">
																<title>{'Shared'|@getTranslatedString}</title>
															</use>
														</svg>
													{/if}
													{if $reportdetails.isscheduled}
														<svg class="slds-icon slds-icon_x-small slds-icon-text-light" aria-hidden="true" >
															<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#clock">
																<title>{'Scheduled'|@getTranslatedString}</title>
															</use>
														</svg>
													{/if}
													</td>
													<td>{$reportdetails.description|@getTranslatedString:$MODULE}</td>
													<td align="center" nowrap>
														{if $reportdetails.customizable eq '1' && $reportdetails.editable eq 'true'}
															<a href="javascript:;" title="{$MOD.LBL_CUSTOMIZE_BUTTON}..." class="slds-button" onClick="editReport('{$reportdetails.reportid}');">
																<svg class="slds-icon slds-icon_x-small slds-icon-text-light" aria-hidden="true" >
																	<use xlink:href="include/LD/assets/icons/action-sprite/svg/symbols.svg#edit"></use> 
																</svg>
															</a>
														{/if}
														{if $ISADMIN || ($reportdetails.state neq 'SAVED' && $reportdetails.editable eq 'true')}
															<a href="javascript:;" title="{$MOD.LBL_DELETE}..." class="slds-button" onclick="DeleteReport('{$reportdetails.reportid}');">
																<svg class="slds-icon slds-icon_x-small slds-icon-text-light" aria-hidden="true" >
																	<use xlink:href="include/LD/assets/icons/action-sprite/svg/symbols.svg#close"></use> 
																</svg>
															</a>
														{/if}
														{if $reportdetails.cbreporttype neq 'external' && $reportdetails.export eq 'yes'}
														<a href="javascript:void(0);" class="slds-button" title="{$MOD.LBL_EXPORTCSV}" onclick="gotourl('index.php?module=Reports&action=ReportsAjax&file=CreateCSV&record={$reportdetails.reportid}');">
															<svg class="slds-icon slds-icon_x-small" aria-hidden="true" >
																<use xlink:href="include/LD/assets/icons/doctype-sprite/svg/symbols.svg#csv"></use> 
															</svg>
														</a>
														<a href="javascript:void(0);" class="slds-button" title="{$MOD.LBL_EXPORTXL_BUTTON}" onclick="gotourl('index.php?module=Reports&action=CreateXL&record={$reportdetails.reportid}');">
															<svg class="slds-icon slds-icon_x-small" aria-hidden="true" >
																<use xlink:href="include/LD/assets/icons/doctype-sprite/svg/symbols.svg#excel"></use> 
															</svg>
														</a>
														<a href="javascript:void(0);" class="slds-button" title="{$MOD.LBL_EXPORTPDF_BUTTON}" onclick="gotourl('index.php?module=Reports&action=CreatePDF&record={$reportdetails.reportid}');">
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
						</div>
					</div>
				</div>
			</div>
			{/foreach}
		</div>
	</div>
</div>
</form>
<script type="text/javascript">
var grid = GridStack.init({
	alwaysShowResizeHandle: /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(
		navigator.userAgent
	),
	resizable: {
		handles: 'e, se, s, sw, w'
	},
	cellHeight: 'auto',
	removable: '#trash',
	removeTimeout: 100,
	acceptWidgets: '.newWidget'
});
grid.on('change', saveReportGridLayout);
</script>
