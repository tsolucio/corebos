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
<script type="text/javascript" src="modules/PriceBooks/PriceBooks.js"></script>
<script type="text/javascript" src="include/js/ListView.js"></script>
<script type="text/javascript" src="include/js/RelatedLists.js"></script>
{include file='Buttons_List.tpl'}
<!-- Contents -->
<div id="editlistprice" style="position:absolute;width:300px;"></div>
<table border=0 cellspacing=0 cellpadding=0 width=98% align=center>
<tr>
	<td valign=top><img src="{'showPanelTopLeft.gif'|@vtiger_imageurl:$THEME}"></td>
	<td class="showPanelBg" valign=top width=100%>
		<!-- PUBLIC CONTENTS STARTS-->
		<div class="small" style="padding:14px">
			<table align="center" border="0" cellpadding="0" cellspacing="0" width="95%">
				<tr><td>
				{* Module Record numbering, used MOD_SEQ_ID instead of ID *}
				{assign var="USE_ID_VALUE" value=$MOD_SEQ_ID}
				{if $USE_ID_VALUE eq ''} {assign var="USE_ID_VALUE" value=$ID} {/if}
				<span class="dvHeaderText">[ {$USE_ID_VALUE} ] {$NAME} -  {$SINGLE_MOD|@getTranslatedString:$MODULE} {$APP.LBL_INFORMATION}</span>&nbsp;&nbsp;&nbsp;<span class="small">{$UPDATEINFO}</span>&nbsp;<span id="vtbusy_info" style="display:none;" valign="bottom"><img src="{'vtbusy.gif'|@vtiger_imageurl:$THEME}" border="0"></span>
				</td></tr>
			</table>
			<br>

			<!-- Account details tabs -->
			<table border=0 cellspacing=0 cellpadding=0 width=95% align=center>
			<tr>
				<td>
					{if isset($OP_MODE) && $OP_MODE eq 'edit_view'}
						{assign var="action" value="EditView"}
					{else}
						{assign var="action" value="DetailView"}
					{/if}
					<div class="small detailview_utils_table_top">
						<div class="detailview_utils_table_tabs">
							<div class="detailview_utils_table_tab detailview_utils_table_tab_unselected detailview_utils_table_tab_unselected_top"><a href="index.php?action={$action}&module={$MODULE}&record={$ID}&parenttab={$CATEGORY}">{$SINGLE_MOD} {$APP.LBL_INFORMATION}</a></div>
							{if isset($HASRELATEDPANES) && $HASRELATEDPANES eq 'true'}
								{include file='RelatedPanes.tpl' tabposition='top'}
							{else}
								<div class="detailview_utils_table_tab detailview_utils_table_tab_selected detailview_utils_table_tab_selected_top">{$APP.LBL_MORE} {$APP.LBL_INFORMATION}</div>
							{/if}
						</div>
						<div class="detailview_utils_table_tabactionsep detailview_utils_table_tabactionsep_top" id="detailview_utils_table_tabactionsep_top"></div>
					</div>
				</td>
			</tr>
			<tr>
				<td style="vertical-align: top;align-content=left;">
					<table border=0 cellspacing=0 cellpadding=3 width=100% class="dvtContentSpace" style="border-bottom:0;">
						<tr>
							<td style="vertical-align: top;align-content=left;">
							<!-- content cache -->
								<table border=0 cellspacing=0 cellpadding=0 width=100%>
									<tr>
										<td style="padding:10px" class="contains_rel_modules">
										<!-- General details -->
												{include file='RelatedListsHidden.tpl'}
												<div id="RLContents">
												{include file='RelatedListContents.tpl'}
												</div>
												</form>
										{*-- End of Blocks--*}
										</td>
									</tr>
								</table>
							</td>
							{if isset($HASRELATEDPANESACTIONS) && $HASRELATEDPANESACTIONS eq 'true'}
								{include file='RelatedPaneActions.tpl'}
							{/if}
						</tr>
					</table>
				</td>
			</tr>
			<tr>
				<td>
					<div class="small detailview_utils_table_bottom">
						<div class="detailview_utils_table_tabs">
							<div class="detailview_utils_table_tab detailview_utils_table_tab_unselected detailview_utils_table_tab_unselected_bottom"><a href="index.php?action={$action}&module={$MODULE}&record={$ID}&parenttab={$CATEGORY}">{$SINGLE_MOD} {$APP.LBL_INFORMATION}</a></div>
							{if $HASRELATEDPANES eq 'true'}
								{include file='RelatedPanes.tpl' tabposition='bottom'}
							{else}
								<div class="detailview_utils_table_tab detailview_utils_table_tab_selected detailview_utils_table_tab_selected_bottom">{$APP.LBL_MORE} {$APP.LBL_INFORMATION}</div>
							{/if}
						</div>
						<div class="detailview_utils_table_tabactionsep detailview_utils_table_tabactionsep_bottom" id="detailview_utils_table_tabactionsep_bottom"></div>
					</div>
				</td>
			</tr>
			</table>
		</div>
	<!-- PUBLIC CONTENTS STOPS-->
	</td>
	<td align=right valign=top><img src="{'showPanelTopRight.gif'|@vtiger_imageurl:$THEME}"></td>
</tr>
</table>

{if $MODULE eq 'Leads' or $MODULE eq 'Contacts' or $MODULE eq 'Accounts' or $MODULE eq 'Campaigns' or $MODULE eq 'Vendors' or $MODULE eq 'Project' or $MODULE eq 'Potentials' or $MODULE eq 'ProjectTask' or $MODULE eq 'HelpDesk'}
<form name="SendMail" onsubmit="VtigerJS_DialogBox.block();"><div id="sendmail_cont" style="z-index:100001;position:absolute;width:300px;"></div></form>
{/if}
