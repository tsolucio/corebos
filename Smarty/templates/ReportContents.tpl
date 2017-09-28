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
<table align="center" border="0" cellpadding="0" cellspacing="0" width="98%" class="">
	<tbody>
		<tr>
			<td valign="top" width="50%" style="padding: 10px;border-right:1px dashed #CCCCCC">
			<!-- Reports Table Starts Here  -->
			{assign var=poscount value=0}
			{foreach item=reportfolder from=$REPT_FLDR}
				{assign var=poscount value=$poscount+1}
				<table width="100%" border="0" cellpadding="0" cellspacing="0" align="center" class="reportsListTable" style="border:none;">
					<tr>
						<td class="mailSubHeader">
							<div class="forceRelatedListSingleContainer">
								<article class="slds-card forceRelatedListCardDesktop" aria-describedby="header">
									<div class="slds-card__header slds-grid">
										<header class="slds-media slds-media--center slds-has-flexi-truncate">
											<div class="slds-media__body">
												<h2>
													<span class="slds-text-title--caps slds-truncate slds-m-right--xx-small actionLabel">
														<b><span id='folder{$reportfolder.id}'>{$reportfolder.name|@getTranslatedString:$MODULE}</span></b>
													</span>
													<small><i><font color='#C0C0C0'> - {$reportfolder.description|@getTranslatedString:$MODULE}</font></i></small>
												</h2>
											</div>
										</header>
									</div>
								</article>
							</div>
							<table width="100%" border="0" cellpadding="0" cellspacing="0" style="padding: 5px;background-color: #fff;">
								<tr>
									<td id="repposition{$poscount}" class="hdrNameBg" width="20%" align="left">
										<input name="newReportInThisModule" value="{$MOD.LBL_CREATE_REPORT}..." class="slds-button slds-button--small slds-button_success" onclick="gcurrepfolderid={$reportfolder.id};fnvshobj(this,'reportLay')" type="button">
									</td>
									<td width="80%" align="right" class="hdrNameBg">
										<input type="button" name="Edit" value=" {$MOD.LBL_RENAME_FOLDER} " class="slds-button slds-button--small slds-button--brand" onClick='EditFolder("{$reportfolder.id}","{$reportfolder.fname}","{$reportfolder.fdescription}"),fnvshobj(this,"orgLay");'>&nbsp;
										&nbsp;{if $ISADMIN}<input type="button" name="delete" value=" {$MOD.LBL_DELETE_FOLDER} " class="slds-button slds-button--small slds-button--destructive" onClick="DeleteFolder('{$reportfolder.id}');">{/if}
									</td>
								</tr>
							</table>
							<div class="slds-truncate" style="padding: 5px;background-color: #fff;">
								<table class="slds-table slds-table--bordered slds-table--fixed-layout ld-font reports-table">
									<thead>
										<tr>
											<th scope="col" class="slds-text-align--center" style="width: 3.25rem;text-align: center;" >
												<div class="slds-th_action slds-th__action_form"><span>#</span></div>
											</th>
											<th class="slds-text-title--caps" scope="col">
												<span class="slds-truncate slds-text-link--reset" style="padding: .5rem 0;">
													{$MOD.LBL_REPORT_NAME}
												</span>
											</th>
											<th class="slds-text-title--caps" scope="col">
												<span class="slds-truncate slds-text-link--reset" style="padding: .5rem 0;">
													{$MOD.LBL_DESCRIPTION}
												</span>
											</th>
											<th class="slds-text-title--caps" scope="col" style="width:160px;">
												<span class="slds-truncate slds-text-link--reset" style="padding: .5rem 0;">
													{$MOD.LBL_TOOLS}
												</span>
											</th>
										</tr>
									</thead>
									<tbody>
										{foreach name=reportdtls item=reportdetails from=$reportfolder.details}
										<tr class="slds-hint-parent slds-line-height--reset" id="row_{$entity_id}">
											<td role="gridcell" class="slds-text-align--center">{$smarty.foreach.reportdtls.iteration}</td>
											<td role="gridcell" class="slds-text-align--left">
												{if $reportdetails.cbreporttype eq 'external'}
													<a href="{$reportdetails.moreinfo}" target="_blank">{$reportdetails.reportname|@getTranslatedString:$MODULE}</a>
												{else}
													<a href="index.php?module=Reports&action=SaveAndRun&record={$reportdetails.reportid}&folderid={$reportfolder.id}">{$reportdetails.reportname|@getTranslatedString:$MODULE}</a>
												{/if}
												{if $reportdetails.sharingtype eq 'Shared'}
													<img src="{'Meetings.gif'|@vtiger_imageurl:$THEME}" align="absmiddle" border=0 height=12 width=12 />
												{/if}
											</td>
											<td role="gridcell" class="slds-text-align--left">{$reportdetails.description|@getTranslatedString:$MODULE}</td>
											<th scope="row" class="tools-cell" align="center">
												<div class="slds-truncate">
												{if $reportdetails.customizable eq '1' && $reportdetails.editable eq 'true'}
													<a href="javascript:;" onClick="editReport('{$reportdetails.reportid}');"><img src="{'editfield.gif'|@vtiger_imageurl:$THEME}" align="absmiddle" title="{$MOD.LBL_CUSTOMIZE_BUTTON}..." border="0"></a>
												{/if}
												{if $ISADMIN || ($reportdetails.state neq 'SAVED' && $reportdetails.editable eq 'true')}
													&nbsp;|&nbsp;<a href="javascript:;" onclick="DeleteReport('{$reportdetails.reportid}');"><img src="{'delete.gif'|@vtiger_imageurl:$THEME}" align="absmiddle" title="{$MOD.LBL_DELETE}..." border="0"></a>
												{/if}
												{if $reportdetails.cbreporttype neq 'external'}
												&nbsp;|&nbsp;<a href="javascript:void(0);" onclick="gotourl('index.php?module=Reports&action=ReportsAjax&file=CreateCSV&record={$reportdetails.reportid}');"><img src="{'csv_text.png'|@vtiger_imageurl:$THEME}" align="abmiddle" alt="{$MOD.LBL_EXPORTCSV}" title="{$MOD.LBL_EXPORTCSV}" border="0"></a>
												&nbsp;|&nbsp;<a href="javascript:void(0);" onclick="gotourl('index.php?module=Reports&action=CreateXL&record={$reportdetails.reportid}');"><img src="{'excel.png'|@vtiger_imageurl:$THEME}" align="abmiddle" alt="{$MOD.LBL_EXPORTXL_BUTTON}" title="{$MOD.LBL_EXPORTXL_BUTTON}" border="0"></a>
												&nbsp;|&nbsp;<a href="javascript:void(0);" onclick="gotourl('index.php?module=Reports&action=CreatePDF&record={$reportdetails.reportid}');"><img src="{'pdf.png'|@vtiger_imageurl:$THEME}" align="abmiddle" alt="{$MOD.LBL_EXPORTPDF_BUTTON}" title="{$MOD.LBL_EXPORTPDF_BUTTON}" border="0"></a>
												{/if}
												</div>
											</th>
										</tr>
										{/foreach}
									</tbody>
								</table>
							</div>
						</td>
					</tr>
				</table>
				<br />
			{/foreach}
			<!-- Reports Table Ends Here  -->
			</td>
			<td style="padding:10px;" valign="top" align="center" width="50%">
				<div id="customizedrep">
					{include file="ReportsCustomize.tpl"}
				</div>
			</td>
		</tr>
	</tbody>
</table>
