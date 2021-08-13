{*<!--
/*************************************************************************************************
* Copyright 2012-2013 OpenCubed  --  This file is a part of vtMktDashboard.
* You can copy, adapt and distribute the work under the "Attribution-NonCommercial-ShareAlike"
* Vizsage Public License (the "License"). You may not use this file except in compliance with the
* License. Roughly speaking, non-commercial users may share and modify this code, but must give credit
* and share improvements. However, for proper details please read the full License, available at
* http://vizsage.com/license/Vizsage-License-BY-NC-SA.html and the handy reference for understanding
* the full license at http://vizsage.com/license/Vizsage-Deed-BY-NC-SA.html. Unless required by
* applicable law or agreed to in writing, any software distributed under the License is distributed
* on an  "AS IS" BASIS, WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
* See the License for the specific language governing permissions and limitations under the
* License terms of Creative Commons Attribution-NonCommercial-ShareAlike 3.0 (the License).
*************************************************************************************************
*  Module       : vtsendgrid
*  Version      : 1.9
*  Author       : OpenCubed
*************************************************************************************************/
-->*}
{include file="SetMenu.tpl"}
<section role="dialog" tabindex="-1" class="slds-fade-in-open slds-modal_large slds-app-launcher" aria-labelledby="header43">
<div class="slds-modal__container slds-p-around_none">
<table align="center" border="0" cellpadding="0" cellspacing="0" width="98%">
<tbody>
	<tr>
		<td class="showPanelBg" style="padding: 10px;" valign="top" width="100%">
		<br>
		{literal}
		<form action="index.php" method="post" name="GendocServer" id="form" onsubmit="">
		{/literal}
			<input type="hidden" id="module" name="module" value="evvtgendoc">
			<input type="hidden" name="action" value="Settings">
			<input type="hidden" name="return_module" value="Settings">
			<input type="hidden" name="return_action" value="Settings">
			<input type="hidden" name="mode" value="save">
			<input type="hidden" name="type" value="gendoc_server">
			<div align=center>


			<!-- DISPLAY -->
			<table border=0 cellspacing=0 cellpadding=5 width=100% class="settingsSelUITopLine">
				<tr>
					<td width=50 rowspan=2 valign=top><img src="modules/evvtgendoc/images/gendoc_server.png" alt="{'Evvtgendoc_title'|@getTranslatedString:$MODULE}" width="48" height="48" border=0 title="{'Evvtgendoc_title'|@getTranslatedString:$MODULE}"></td>
					<td class=heading2 valign=bottom><b><a href="index.php?module=Settings&action=index">{'LBL_SETTINGS'|@getTranslatedString:'Settings'}</a> > {'Evvtgendoc_title'|@getTranslatedString:$MODULE} </b></td>
				</tr>
				<tr>
					<td valign=top class="small">{'Evvtgendoc_subtitle'|@getTranslatedString:$MODULE}</td>
				</tr>
			</table>
			<br>
			<table border=0 cellspacing=0 cellpadding=10 width=100% class="tableHeading">
			<tr>
				<td class="small">
					<strong>{'PDF Links'|@getTranslatedString:$MODULE}:&nbsp;</strong>
					<input type="checkbox" name="pdflinkactive" id="pdflinkactive" {$pdflinkactive}/>&nbsp;
					{if $pdflinkactive eq 'checked'}
						{'Active'|@getTranslatedString:$MODULE}
					{else}
						{'Inactive'|@getTranslatedString:$MODULE}
					{/if}
				</td>
			</tr>
			</table>
			<table border=0 cellspacing=0 cellpadding=10 width=100% class="tableHeading">
			<tr>
				<td class="small">
					<strong>{'GenDoc URL'|@getTranslatedString:$MODULE}:&nbsp;</strong>
					{if empty($gendocurl)}
						{'is not set'|@getTranslatedString:$MODULE}
					{else}
						{'is set to'|@getTranslatedString:$MODULE} {$gendocurl}
					{/if}
				</td>
			</tr>
			</table>
			<table border=0 cellspacing=0 cellpadding=10 width=100% class="tableHeading">
			<tr>
				<td class="small">
					<input type="checkbox" name="active" id="active" {$active}/>&nbsp;<strong>{'External Server'|@getTranslatedString:$MODULE}</strong>
					<div style="float: right">
					<input title="{$APP.LBL_SAVE_BUTTON_LABEL}" accessKey="{$APP.LBL_SAVE_BUTTON_KEY}" class="crmButton small save" type="submit" name="button" value="{$APP.LBL_SAVE_BUTTON_LABEL}" >&nbsp;&nbsp;
					</div>
				</td>
			</tr>
			{if !empty($ERROR_MSG)}
			<tr>
			{$ERROR_MSG}
			</tr>
			{/if}
			</table>
			<table border=0 cellspacing=0 cellpadding=0 width=100% class="listRow">
				<tr>
					<td class="small" valign=top >
						<table width="100%" border="0" cellspacing="0" cellpadding="5">
							<tr valign="top">
								<td nowrap class="small cellLabel"><strong>{'LBL_URL_SERVER'|@getTranslatedString:$MODULE}</strong></td>
								<td class="small cellText">
								<input type="text" class="detailedViewTextBox small" value="{$server}" name="server" id="server">
								</td>
							</tr>
							<tr valign="top">
								<td nowrap class="small cellLabel"><strong>{'LBL_USERNAME'|@getTranslatedString:'Settings'}</strong></td>
								<td class="small cellText">
								<input type="text" class="detailedViewTextBox small" style="width:90%" value="{$user}" name="user" id="user">
								</td>
							</tr>
							<tr>
								<td nowrap class="small cellLabel"><strong>{'LBL_ACCESSKEY'|@getTranslatedString:$MODULE}</strong></td>
								<td class="small cellText">
								<input type="text" class="detailedViewTextBox small" value="{$key}" name="key" id="key">
								</td>
							</tr>
						</table>
					</td>
				</tr>
			</table>
		</div>
		</form>
	</td>
	</tr>
</tbody>
</table>
</div>
</section>