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
<table align="center" border="0" cellpadding="0" cellspacing="0" width="99%" class="showPanelBg">
	<tbody><tr>
	<td valign=top><img src="{'showPanelTopLeft.gif'|@vtiger_imageurl:$THEME}"></td>
	<td valign="top" width="50%" style="padding: 10px;border-right:1px dashed #CCCCCC">
	<!-- Reports Table Starts Here  -->
	{assign var=poscount value=0}
	{foreach item=reportfolder from=$REPT_FLDR}
	{assign var=poscount value=$poscount+1}
	<table width="100%" border="0" cellpadding="0" cellspacing="0" align="center" class="reportsListTable">
		<tr>
		<td class="mailSubHeader"><b>
		<span id='folder{$reportfolder.id}'>{$reportfolder.name|@getTranslatedString:$MODULE}</span>
		</b>
		<i><font color='#C0C0C0'> - {$reportfolder.description|@getTranslatedString:$MODULE}</font></i>
		</td>
		</tr>
		<tr>
			<td class="hdrNameBg" colspan="3" style="padding: 5px;" align="right" >
				<table width="100%" border="0" cellpadding="0" cellspacing="0">
				<tr>
					<td id="repposition{$poscount}" width="5%" align="right">
						<input name="newReportInThisModule" value="{$MOD.LBL_CREATE_REPORT}..." class="crmButton small create" onclick="gcurrepfolderid={$reportfolder.id};fnvshobj(this,'reportLay')" type="button">
					</td>
					<td width="75%" align="right">
						<input type="button" name="Edit" value=" {$MOD.LBL_RENAME_FOLDER} " class="crmbutton small edit" onClick='EditFolder("{$reportfolder.id}","{$reportfolder.fname}","{$reportfolder.fdescription}"),fnvshobj(this,"orgLay");'>&nbsp;
					</td>
					<td align="right">
						{if $ISADMIN}<input type="button" name="delete" value=" {$MOD.LBL_DELETE_FOLDER} " class="crmbutton small delete" onClick="DeleteFolder('{$reportfolder.id}');">{/if}
					</td>
				</tr>
				</table>
			</td>
		</tr>
		<tr>
		<td>
		<table class="small" border="0" cellpadding="5" cellspacing="1" width="100%">
			<tbody>
			<tr>
			<td class="lvtCol" width="5%">#</td>
			<td class="lvtCol" width="35%">{$MOD.LBL_REPORT_NAME}</td>
			<td class="lvtCol" width="50%">{$MOD.LBL_DESCRIPTION}</td>
			<td class="lvtCol" width="10%">{$MOD.LBL_TOOLS}</td>
			</tr>
			{foreach name=reportdtls item=reportdetails from=$reportfolder.details}
				<tr class="lvtColData" onmouseover="this.className='lvtColDataHover'" onmouseout="this.className='lvtColData'" bgcolor="white">
				<td>{$smarty.foreach.reportdtls.iteration}</td>
				<td><a href="index.php?module=Reports&action=SaveAndRun&record={$reportdetails.reportid}&folderid={$reportfolder.id}">{$reportdetails.reportname|@getTranslatedString:$MODULE}</a>
				{if $reportdetails.sharingtype eq 'Shared'}
					<img src="{'Meetings.gif'|@vtiger_imageurl:$THEME}" align="absmiddle" border=0 height=12 width=12 />
				{/if}
				</td>
				<td>{$reportdetails.description|@getTranslatedString:$MODULE}</td>
				<td align="center" nowrap>
					{if $reportdetails.customizable eq '1' && $reportdetails.editable eq 'true'}
						<a href="javascript:;" onClick="editReport('{$reportdetails.reportid}');"><img src="{'editfield.gif'|@vtiger_imageurl:$THEME}" align="absmiddle" title="{$MOD.LBL_CUSTOMIZE_BUTTON}..." border="0"></a>
					{/if}
					{if $ISADMIN || ($reportdetails.state neq 'SAVED' && $reportdetails.editable eq 'true')}
						&nbsp;|&nbsp;<a href="javascript:;" onclick="DeleteReport('{$reportdetails.reportid}');"><img src="{'delete.gif'|@vtiger_imageurl:$THEME}" align="absmiddle" title="{$MOD.LBL_DELETE}..." border="0"></a>
					{/if}
					&nbsp;|&nbsp;<a href="javascript:void(0);" onclick="gotourl('index.php?module=Reports&action=ReportsAjax&file=CreateCSV&record={$reportdetails.reportid}');"><img src="{'csv_text.png'|@vtiger_imageurl:$THEME}" align="abmiddle" alt="{$MOD.LBL_EXPORTCSV}" title="{$MOD.LBL_EXPORTCSV}" border="0"></a>
					&nbsp;|&nbsp;<a href="javascript:void(0);" onclick="gotourl('index.php?module=Reports&action=CreateXL&record={$reportdetails.reportid}');"><img src="{'excel.png'|@vtiger_imageurl:$THEME}" align="abmiddle" alt="{$MOD.LBL_EXPORTXL_BUTTON}" title="{$MOD.LBL_EXPORTXL_BUTTON}" border="0"></a>
					&nbsp;|&nbsp;<a href="javascript:void(0);" onclick="gotourl('index.php?module=Reports&action=CreatePDF&record={$reportdetails.reportid}');"><img src="{'pdf.png'|@vtiger_imageurl:$THEME}" align="abmiddle" alt="{$MOD.LBL_EXPORTPDF_BUTTON}" title="{$MOD.LBL_EXPORTPDF_BUTTON}" border="0"></a>
				</td>
				</tr>
			{/foreach}
			</tbody>
		</table>
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
	<td valign=top><img src="{'showPanelTopRight.gif'|@vtiger_imageurl:$THEME}"></td>
	</tr>
	<tr>
	<td colspan="2" align="center">&nbsp;</td>
	</tr>
	</tbody>
</table>
