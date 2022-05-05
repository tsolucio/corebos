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
<script type="text/javascript" src="include/js/dtlviewajax.js"></script>
{if $FIELD_DEPENDENCY_DATASOURCE neq ''}
<script type="text/javascript" src="include/js/FieldDependencies.js"></script>
<script type="text/javascript" src="include/js/FieldDepFunc.js"></script>
<script type="text/javascript">
	jQuery(document).ready(function() {ldelim} (new FieldDependencies({$FIELD_DEPENDENCY_DATASOURCE})).init(document.forms['DetailView']) {rdelim});
</script>
{/if}
<script type="text/javascript" src="include/js/clipboard.min.js"></script>
<span id="crmspanid" style="display:none;position:absolute;" onmouseover="show('crmspanid');">
	<a class="link" id="clipcopylink" href="javascript:;" onclick="handleCopyClipboard(event);" data-clipboard-text="">{$APP.LBL_COPY_BUTTON}</a>
</span>
<div id="convertleaddiv" style="display:block;position:absolute;left:225px;top:150px;"></div>
<script>
{literal}
var clipcopyobject = new ClipboardJS('#clipcopylink');
clipcopyobject.on('success', function(e) { clipcopyclicked = false; });
clipcopyobject.on('error', function(e) { clipcopyclicked = false; });
{/literal}
</script>

<div id="lstRecordLayout" class="layerPopup" style="display:none;width:325px;height:300px;"></div>

{if $MODULE eq 'Accounts' || $MODULE eq 'Contacts'}
	{if $MODULE eq 'Accounts'}
		{assign var=address1 value=$MOD.LBL_BILLING_ADDRESS}
		{assign var=address2 value=$MOD.LBL_SHIPPING_ADDRESS}
	{/if}
	{if $MODULE eq 'Contacts'}
		{assign var=address1 value=$MOD.LBL_PRIMARY_ADDRESS}
		{assign var=address2 value=$MOD.LBL_ALTERNATE_ADDRESS}
	{/if}
	<div class="slds-card" id="locateMap" onMouseOut="fninvsh('locateMap')" onMouseOver="fnvshNrm('locateMap')">
		<div class="slds-card__body slds-card__body_inner">
			<a href="javascript:;" onClick="fninvsh('locateMap'); searchMapLocation('Main');" class="calMnu">{$address1}</a>
		</div>
		<div class="slds-card__body slds-card__body_inner">
			<a href="javascript:;" onClick="fninvsh('locateMap'); searchMapLocation('Other');" class="calMnu">{$address2}</a>
		</div>
	</div>
{/if}

<table width="100%" cellpadding="2" cellspacing="0" border="0" class="detailview_wrapper_table">
	<tr>
		<td class="detailview_wrapper_cell">

			{include file='Buttons_List.tpl' isDetailView=true}

			<!-- Contents -->
			<table class="slds-m-around_medium" style="width: 98%;">
				<tr>
					<td class="showPanelBg" valign=top width=100%>
						<!-- PUBLIC CONTENTS STARTS-->
						<div class="small" style="padding:14px" onclick="hndCancelOutsideClick();";>
						{include file='applicationmessage.tpl'}
						<!-- Entity and More information tabs -->
						<table border=0 cellspacing=0 cellpadding=0 width=95% align=center>
							<tr>
								<td>
									<div class="small detailview_utils_table_top">
										<div class="detailview_utils_table_tabs noprint">
											<div class="detailview_utils_table_tab detailview_utils_table_tab_selected detailview_utils_table_tab_selected_top">{$SINGLE_MOD|@getTranslatedString:$MODULE} {$APP.LBL_INFORMATION}</div>
											{if $SinglePane_View eq 'false' && $IS_REL_LIST neq false && $IS_REL_LIST|@count > 0 && empty($Module_Popup_Edit)}
												{if $HASRELATEDPANES eq 'true'}
													{include file='RelatedPanes.tpl' tabposition='top' RETURN_RELATEDPANE=''}
												{else}
												<div class="detailview_utils_table_tab detailview_utils_table_tab_unselected detailview_utils_table_tab_unselected_top" onmouseout="fnHideDrop('More_Information_Modules_List');" onmouseover="fnDropDown(this,'More_Information_Modules_List');">
													<a href="index.php?action=CallRelatedList&module={$MODULE}&record={$ID}">{$APP.LBL_MORE} {$APP.LBL_INFORMATION}</a>
													<div onmouseover="fnShowDrop('More_Information_Modules_List')" onmouseout="fnHideDrop('More_Information_Modules_List')"
														 id="More_Information_Modules_List" class="drop_mnu" style="left: 502px; top: 76px; display: none;">
														<table border="0" cellpadding="0" cellspacing="0" width="100%">
															{foreach key=_RELATION_ID item=_RELATED_MODULE from=$IS_REL_LIST}
																<tr><td><a class="drop_down" href="index.php?action=CallRelatedList&module={$MODULE}&record={$ID}&selected_header={$_RELATED_MODULE}&relation_id={$_RELATION_ID}#tbl_{$MODULE}_{$_RELATED_MODULE}">{$_RELATED_MODULE|@getTranslatedString:$_RELATED_MODULE}</a></td></tr>
															{/foreach}
														</table>
													</div>
												</div>
												{/if}
											{/if}
										</div>
										<div class="detailview_utils_table_tabactionsep detailview_utils_table_tabactionsep_top" id="detailview_utils_table_tabactionsep_top"></div>
										<div class="detailview_utils_table_actions detailview_utils_table_actions_top" id="detailview_utils_actions_top">
										<div class="slds-button-group" role="group">
													
											{if empty($Module_Popup_Edit)}
												<div class="slds-button-group" role="group">
													{include file='Components/DetailViewPirvNext.tpl'}
												</div>
											{/if}
										</div>
									</div>
								</td>
							</tr>
							<tr>
								<td valign=top align=left >
									<table border=0 cellspacing=0 cellpadding=3 width=100% class="dvtContentSpace" style="border-bottom:0;">
										<tr valign=top>

											<td align=left>
												<!-- content cache -->

												<table border=0 cellspacing=0 cellpadding=0 width=100%>
													<tr valign=top>
														<td style="padding:5px">
															<!-- Command Buttons -->
															<form action="index.php" method="post" name="DetailView" id="formDetailView">
																<input type="hidden" id="hdtxt_IsAdmin" value="{if isset($hdtxt_IsAdmin)}{$hdtxt_IsAdmin}{else}0{/if}">
																{include file='DetailViewHidden.tpl'}
															<table border=0 cellspacing=0 cellpadding=0 width=100%>
																	{foreach key=header item=detail from=$BLOCKS name=BLOCKS}
																		<tr><td style="padding:5px">
																				<!-- Detailed View Code starts here-->
																				<table border=0 cellspacing=0 cellpadding=0 width=100% class="small detailview_header_table">
																					<tr>
																						<td>&nbsp;</td>
																						<td>&nbsp;</td>
																						<td>&nbsp;</td>
																						<td class="cblds-t-align_right" align=right>
																							{if isset($MOD.LBL_ADDRESS_INFORMATION) && $header eq $MOD.LBL_ADDRESS_INFORMATION && ($MODULE eq 'Accounts' || $MODULE eq 'Contacts' || $MODULE eq 'Leads') }
																								{if $MODULE eq 'Leads'}
																								<div class="slds-dropdown-trigger slds-dropdown-trigger_click slds-is-open slds-button_last">
																									<button
																										class="slds-button slds-button_neutral "
																										title="{$APP.LBL_LOCATE_MAP}"
																										value="{$APP.LBL_LOCATE_MAP}"
																										onClick="searchMapLocation('Main')"
																										type="button"
																										name="mapbutton"
																										>
																										<svg class="slds-button__icon slds-button__icon_left" aria-hidden="true">
																											<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#down"></use>
																										</svg>
																										{$APP.LBL_LOCATE_MAP}
																									</button>
																									<div class="slds-dropdown slds-dropdown_right slds-dropdown_actions">
																										<ul class="slds-dropdown__list" role="menu">
																											<li class="slds-dropdown__item" role="presentation">
																											<a href="#" role="menuitem" tabindex="0">
																												<span class="slds-truncate" title="Overflow Item One">Overflow Item One</span>
																											</a>
																											</li>
																											<li class="slds-dropdown__item" role="presentation">
																											<a href="#" role="menuitem" tabindex="-1">
																												<span class="slds-truncate" title="Overflow Item Two">Overflow Item Two</span>
																											</a>
																											</li>
																											<li class="slds-dropdown__item" role="presentation">
																											<a href="#" role="menuitem" tabindex="-1">
																												<span class="slds-truncate" title="Overflow Item Three">Overflow Item Three</span>
																											</a>
																											</li>
																										</ul>
																									</div>
																								</div>
																								{else}
																								<div class="slds-dropdown-trigger slds-dropdown-trigger_click slds-is-open slds-button_last">
																									<button
																										class="slds-button slds-button_neutral "
																										title="{$APP.LBL_LOCATE_MAP}"
																										value="{$APP.LBL_LOCATE_MAP}"
																										onClick="fnvshobj(this,'locateMap');" onMouseOut="fninvsh('locateMap');"
																										type="button"
																										name="mapbutton"
																										>
																										<svg class="slds-button__icon slds-button__icon_left" aria-hidden="true">
																											<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#down"></use>
																										</svg>
																										{$APP.LBL_LOCATE_MAP}
																									</button>
																								</div>
																								{/if}
																							{/if}
																						</td>
																					</tr>

																					<!-- This is added to display the existing comments -->
																					{if $header eq $APP.LBL_COMMENTS || (isset($MOD.LBL_COMMENTS) && $header eq $MOD.LBL_COMMENTS) || (isset($MOD.LBL_COMMENT_INFORMATION) && $header eq $MOD.LBL_COMMENT_INFORMATION)}
																						<tr>
																							<td colspan=4 class="dvInnerHeader">
																								<b>{if isset($MOD.LBL_COMMENT_INFORMATION)}{$MOD.LBL_COMMENT_INFORMATION}{else}{$APP.LBL_COMMENTS}{/if}</b>
																							</td>
																						</tr>
																						<tr>
																							<td colspan=4 class="dvtCellInfo">{$COMMENT_BLOCK}</td>
																						</tr>
																						<tr><td>&nbsp;</td></tr>
																					{/if}

																					{if $header neq 'Comments' && (!isset($BLOCKS.$header.relatedlist) || $BLOCKS.$header.relatedlist eq 0)}
																						<tr class="detailview_block_header">{strip}
																							<td colspan=4 class="dvInnerHeader">
																								<div style="float:left;font-weight:bold;"><div style="float:left;"><a href="javascript:showHideStatus('tbl{$header|replace:' ':''}','aid{$header|replace:' ':''}','{$IMAGE_PATH}');">
																											{if isset($BLOCKINITIALSTATUS[$header]) && $BLOCKINITIALSTATUS[$header] eq 1}
																												<span class="exp_coll_block inactivate">
																												<img id="aid{$header|replace:' ':''}" src="{'activate.gif'|@vtiger_imageurl:$THEME}" style="border: 0px solid #000000;" alt="{'LBL_Hide'|@getTranslatedString:'Settings'}" title="{'LBL_Hide'|@getTranslatedString:'Settings'}"/>
																												</span>
																											{else}
																												<span class="exp_coll_block activate">
																												<img id="aid{$header|replace:' ':''}" src="{'inactivate.gif'|@vtiger_imageurl:$THEME}" style="border: 0px solid #000000;" alt="{'LBL_Show'|@getTranslatedString:'Settings'}" title="{'LBL_Show'|@getTranslatedString:'Settings'}"/>
																												</span>
																											{/if}
																										</a></div><b>&nbsp;
																										{$header}
																									</b></div>
																							</td>{/strip}
																						</tr>
																					{/if}
																				</table>
																				{if $header neq 'Comments'}
																					{if (isset($BLOCKINITIALSTATUS[$header]) && $BLOCKINITIALSTATUS[$header] eq 1) || !empty($BLOCKS.$header.relatedlist)}
																						<div style="width:auto;display:block;" id="tbl{$header|replace:' ':''}" >
																						{else}
																						<div style="width:auto;display:none;" id="tbl{$header|replace:' ':''}" >
																						{/if}
																							<table border=0 cellspacing=0 cellpadding=0 width="100%" class="small detailview_table">
																							{if !empty($CUSTOMBLOCKS.$header.custom)}
																								{include file=$CUSTOMBLOCKS.$header.tpl}
																							{elseif isset($BLOCKS.$header.relatedlist) && $IS_REL_LIST neq false && $IS_REL_LIST|@count > 0}
																								{foreach key=bhkey item=bhitem from=$BLOCKS.$header}
																									{if isPermitted($bhkey, 'index')=='yes'}
																										{assign var='RELBINDEX' value=$BLOCKS.$header.relatedlist}
																										{include file='RelatedListNew.tpl' RELATEDLISTS=$RELATEDLISTBLOCK.$RELBINDEX RELLISTID=$RELBINDEX}
																									{/if}
																									{break}
																								{/foreach}
																							{else}
																								{foreach item=detailInfo from=$detail}
																									<tr style="height:25px" class="detailview_row">
																										{assign var=numfieldspainted value=0}
																										{foreach key=label item=data from=$detailInfo}
																											{assign var=numfieldspainted value=$numfieldspainted+1}
																											{assign var=keyid value=$data.ui}
																											{assign var=keyval value=$data.value}
																											{assign var=keytblname value=$data.tablename}
																											{assign var=keyfldname value=$data.fldname}
																											{assign var=keyfldid value=$data.fldid}
																											{assign var=keyoptions value=$data.options}
																											{assign var=keysecid value=$data.secid}
																											{assign var=keyseclink value=$data.link}
																											{assign var=keycursymb value=$data.cursymb}
																											{assign var=keysalut value=$data.salut}
																											{assign var=keyaccess value=$data.notaccess}
																											{assign var=keycntimage value=$data.cntimage}
																											{assign var=keyadmin value=$data.isadmin}
																											{assign var=display_type value=$data.displaytype}
																											{assign var=_readonly value=$data.readonly}
																											{assign var=extendedfieldinfo value=$data.extendedfieldinfo}

																											{if $label ne '' && ($keyid ne 83 || count($TAX_DETAILS)>0)}
																												<td class="dvtCellLabel" align=right width=25% style="white-space: normal;">{strip}
																												{if $keycntimage ne ''}
																													{$keycntimage}
																												{elseif $keyid eq '71' || $keyid eq '72'}<!-- Currency symbol -->
																													{$label} ({$keycursymb})
																												{elseif $keyid eq '9'}
																													{$label} {$APP.COVERED_PERCENTAGE}
																												{elseif $keyid eq '14'}
																													{$label} {"LBL_TIMEFIELD"|@getTranslatedString}
																												{else}
																													{$label}
																												{/if}
																												{/strip}</td>
																												{if $EDIT_PERMISSION eq 'yes' && $display_type neq '2' && $display_type neq '4' && $display_type neq '5' && $_readonly eq '0'}
																													{* Performance Optimization Control *}
																													{if !empty($DETAILVIEW_AJAX_EDIT) }
																														{include file="DetailViewUI.tpl"}
																													{else}
																														{include file="DetailViewFields.tpl"}
																													{/if}
																													{* END *}
																												{else}
																													{include file="DetailViewFields.tpl"}
																												{/if}
																											{/if}
																										{/foreach}
																										{if $numfieldspainted eq 1 && $keyid neq 19 && $keyid neq 20}<td colspan=2></td>{/if}
																									</tr>
																								{/foreach}
																							{/if}
																							</table>
																						</div>
																					{/if}
																			</td>
																		</tr>
																	{* vtlib Customization: Embed DetailViewWidget block:// type if any *}
																	{if $CUSTOM_LINKS && !empty($CUSTOM_LINKS.DETAILVIEWWIDGET)}
																		{foreach item=CUSTOM_LINK_DETAILVIEWWIDGET from=$CUSTOM_LINKS.DETAILVIEWWIDGET}
																			{if preg_match("/^block:\/\/.*/", $CUSTOM_LINK_DETAILVIEWWIDGET->linkurl)}
																			{if ($smarty.foreach.BLOCKS.first && $CUSTOM_LINK_DETAILVIEWWIDGET->sequence <= 1)
																				|| ($CUSTOM_LINK_DETAILVIEWWIDGET->sequence == $smarty.foreach.BLOCKS.iteration + 1)
																				|| ($smarty.foreach.BLOCKS.last && $CUSTOM_LINK_DETAILVIEWWIDGET->sequence >= $smarty.foreach.BLOCKS.iteration + 1)}
																				<tr>
																					<td style="padding:5px;">{process_widget widgetLinkInfo=$CUSTOM_LINK_DETAILVIEWWIDGET}</td>
																				</tr>
																			{/if}
																			{/if}
																		{/foreach}
																	{/if}
																	{* END *}
																	{/foreach}
																	{*-- End of Blocks--*}

																	<!-- Inventory - Product Details informations -->
																	{if isset($ASSOCIATED_PRODUCTS) && $ShowInventoryLines}
																	<tr><td>
																		{$ASSOCIATED_PRODUCTS}
																	</td></tr>
																	{/if}
																{if $SinglePane_View eq 'true' && $IS_REL_LIST neq false && $IS_REL_LIST|@count > 0}
																	{include file= 'RelatedListNew.tpl'}
																{/if}
															</table>
														</td></tr></table>
											</td>
											{if empty($Module_Popup_Edit)}
											<td width=22% valign=top style="border-left:1px dashed #cccccc;padding:13px;{$DEFAULT_ACTION_PANEL_STATUS}" class="noprint" id="actioncolumn">
												<!-- right side relevant info -->
												<table width="100%" border="0" cellpadding="5" cellspacing="0" class="detailview_actionlinks actionlinks_events_todo">
													<tr><td align="left" class="genHeaderSmall">{$APP.LBL_ACTIONS}</td></tr>

													{if in_array($MODULE, getInventoryModules())}
														<!-- Inventory Actions -->
														{include file="Inventory/InventoryActions.tpl"}
													{/if}
												</table>
												{* vtlib customization: Avoid line break if custom links are present *}
												{if !isset($CUSTOM_LINKS) || empty($CUSTOM_LINKS)}
													<br>
												{/if}

												{* vtlib customization: Custom links on the Detail view basic links *}
												{if $CUSTOM_LINKS && $CUSTOM_LINKS.DETAILVIEWBASIC}
													<ul>
														{foreach item=CUSTOMLINK from=$CUSTOM_LINKS.DETAILVIEWBASIC}
															<li class="actionlink actionlink_customlink actionlink_{$CUSTOMLINK->linklabel|lower|replace:' ':'_'}">
																{assign var="customlink_href" value=$CUSTOMLINK->linkurl}
																{assign var="customlink_label" value=$CUSTOMLINK->linklabel}
																{if $customlink_label eq ''}
																	{assign var="customlink_label" value=$customlink_href}
																{else}
																	{* Pickup the translated label provided by the module *}
																	{assign var="customlink_label" value=$customlink_label|@getTranslatedString:$CUSTOMLINK->module()}
																{/if}
																{if $customlink_href=='ACTIONSUBHEADER'}
																	<span class="genHeaderSmall slds-truncate">{$customlink_label}</span>
																{else}
																	{if $CUSTOMLINK->linkicon}
																		{if strpos($CUSTOMLINK->linkicon, '}')>0}
																			{assign var="customlink_iconinfo" value=$CUSTOMLINK->linkicon|json_decode:true}
																			<span class="slds-icon_container slds-icon-{$customlink_iconinfo.library}-{$customlink_iconinfo.icon}" title="{$customlink_label}">
																			<svg class="slds-icon slds-icon-text-default slds-icon_x-small" aria-hidden="true">
																				<use xlink:href="include/LD/assets/icons/{$customlink_iconinfo.library}-sprite/svg/symbols.svg#{$customlink_iconinfo.icon}"></use>
																			</svg>
																			<span class="slds-assistive-text">{$customlink_label}</span>
																			</span>
																		{else}
																			<a class="webMnu" href="{$customlink_href}">
																			<img hspace=5 align="absmiddle" border=0 src="{$CUSTOMLINK->linkicon}">
																			</a>
																		{/if}
																	{else}
																		<a class="webMnu" href="{$customlink_href}"><img hspace=5 align="absmiddle" border=0 src="themes/images/no_icon.png"></a>
																	{/if}
																	&nbsp;<a class="slds-text-link_reset" href="{$customlink_href}">{$customlink_label}</a>
																{/if}
															</li>
														{/foreach}
													</ul>
												{/if}

												{* vtlib customization: Custom links on the Detail view *}
												{if $CUSTOM_LINKS && $CUSTOM_LINKS.DETAILVIEW}
													<br>
													{if !empty($CUSTOM_LINKS.DETAILVIEW)}
														<table>
															<tr><td class="dvtUnSelectedCell" style="background-color: rgb(204, 204, 204); padding: 5px;">
																<a href="javascript:;" onmouseover="fnvshobj(this,'vtlib_customLinksLay');" onclick="fnvshobj(this,'vtlib_customLinksLay');"><b>{$APP.LBL_MORE} {$APP.LBL_ACTIONS} &#187;</b></a>
															</td></tr>
														</table>
														<br>
														<div style="display: none; left: 193px; top: 106px;width:215px; position:absolute;" class="slds-box_border slds-card" id="vtlib_customLinksLay"
															 onmouseout="fninvsh('vtlib_customLinksLay')" onmouseover="fnvshNrm('vtlib_customLinksLay')">
															<table class="slds-p-around_xx-small">
																<tr><td style="border-bottom: 1px solid rgb(204, 204, 204); padding: 5px;"><b>{$APP.LBL_MORE} {$APP.LBL_ACTIONS} &#187;</b></td></tr>
																<tr>
																	<td class="slds-p-around_xx-small">
																	<ul>
																		{foreach item=CUSTOMLINK from=$CUSTOM_LINKS.DETAILVIEW}
																			{assign var="customlink_href" value=$CUSTOMLINK->linkurl}
																			{assign var="customlink_label" value=$CUSTOMLINK->linklabel}
																			{if $customlink_label eq ''}
																				{assign var="customlink_label" value=$customlink_href}
																			{else}
																				{* Pickup the translated label provided by the module *}
																				{assign var="customlink_label" value=$customlink_label|@getTranslatedString:$CUSTOMLINK->module()}
																			{/if}
																			<li>
																			{if $CUSTOMLINK->linkicon}
																				{if strpos($CUSTOMLINK->linkicon, '}')>0}
																					{assign var="customlink_iconinfo" value=$CUSTOMLINK->linkicon|json_decode:true}
																					<span class="slds-icon_container slds-icon-{$customlink_iconinfo.library}-{$customlink_iconinfo.icon}" title="{$customlink_label}">
																					<svg class="slds-icon slds-icon-text-default slds-icon_x-small" aria-hidden="true">
																						<use xlink:href="include/LD/assets/icons/{$customlink_iconinfo.library}-sprite/svg/symbols.svg#{$customlink_iconinfo.icon}"></use>
																					</svg>
																					<span class="slds-assistive-text">{$customlink_label}</span>
																					</span>
																				{else}
																					<a class="webMnu" href="{$customlink_href}"><img hspace=5 align="absmiddle" border=0 src="{$CUSTOMLINK->linkicon}"></a>
																				{/if}
																			{else}
																				<a class="webMnu" href="{$customlink_href}"><img hspace=5 align="absmiddle" border=0 src="themes/images/no_icon.png"></a>
																			{/if}
																			&nbsp;<a class="slds-text-link_reset" href="{$customlink_href}">{$customlink_label}</a>
																			</li>
																		{/foreach}
																		</ul>
																	</td>
																</tr>
															</table>
														</div>
													{/if}
												{/if}
												{* END *}
												<!-- Action links END -->

												{include file="TagCloudDisplay.tpl"}

												{if !empty($CUSTOM_LINKS.DETAILVIEWWIDGET)}
													{foreach key=CUSTOMLINK_NO item=CUSTOMLINK from=$CUSTOM_LINKS.DETAILVIEWWIDGET}
														{assign var="customlink_href" value=$CUSTOMLINK->linkurl}
														{assign var="customlink_label" value=$CUSTOMLINK->linklabel}
														{* Ignore block:// type custom links which are handled earlier *}
														{if !preg_match("/^block:\/\/.*/", $customlink_href)}
															{if $customlink_label eq ''}
																{assign var="customlink_label" value=$customlink_href}
															{else}
																{* Pickup the translated label provided by the module *}
																{assign var="customlink_label" value=$customlink_label|@getTranslatedString:$CUSTOMLINK->module()}
															{/if}
															<br/>
															<input type="hidden" id="{$CUSTOMLINK->linklabel|replace:' ':''}LINKID" value="{$CUSTOMLINK->linkid}">
															<table style="border:0;width:100%" class="rightMailMerge" id="{$CUSTOMLINK->linklabel}">
																<tr>
																	<td class="rightMailMergeHeader">
																		<div>
																		<b>{$customlink_label}</b>&nbsp;
																		<img id="detailview_block_{$CUSTOMLINK->linkid}_indicator" style="display:none;" src="{'vtbusy.gif'|@vtiger_imageurl:$THEME}" border="0" align="absmiddle" />
																		</div>
																	</td>
																</tr>
																<tr style="height:25px">
																	<td class="rightMailMergeContent"><div id="detailview_block_{$CUSTOMLINK->linkid}"></div></td>
																</tr>
																<script type="text/javascript">
																	vtlib_loadDetailViewWidget("{$customlink_href}", "detailview_block_{$CUSTOMLINK->linkid}", "detailview_block_{$CUSTOMLINK->linkid}_indicator");
																</script>
															</table>
														{/if}
													{/foreach}
												{/if}
											</td>
											{/if}
										</tr>
									</table>

									<!-- PUBLIC CONTENTS STOPS-->
								</td>
							</tr>
							<tr>
								<td>
									<div class="small detailview_utils_table_bottom noprint">
										{if empty($Module_Popup_Edit)}
										<div class="detailview_utils_table_tabs">
											<div class="detailview_utils_table_tab detailview_utils_table_tab_selected detailview_utils_table_tab_selected_bottom">{$SINGLE_MOD|@getTranslatedString:$MODULE} {$APP.LBL_INFORMATION}</div>
											{if $SinglePane_View eq 'false' && $IS_REL_LIST neq false && $IS_REL_LIST|@count > 0}
												{if $HASRELATEDPANES eq 'true'}
													{include file='RelatedPanes.tpl' tabposition='bottom' RETURN_RELATEDPANE=''}
												{else}
												<div class="detailview_utils_table_tab detailview_utils_table_tab_unselected detailview_utils_table_tab_unselected_bottom"><a href="index.php?action=CallRelatedList&module={$MODULE}&record={$ID}">{$APP.LBL_MORE} {$APP.LBL_INFORMATION}</a></div>
												{/if}
											{/if}
										</div>
										<div class="detailview_utils_table_tabactionsep detailview_utils_table_tabactionsep_bottom" id="detailview_utils_table_tabactionsep_bottom"></div>
										<div class="detailview_utils_table_actions detailview_utils_table_actions_bottom" id="detailview_utils_actions_bottom">
												<div class="slds-button-group" role="group">
													{if $EDIT_PERMISSION eq 'yes'}
														<button
															class="slds-button slds-button_neutral"
															title="{$APP.LBL_EDIT_BUTTON_TITLE}"
															value="{$APP.LBL_EDIT_BUTTON_TITLE}"
															accessKey="{$APP.LBL_EDIT_BUTTON_KEY}"
															onclick="DetailView.return_module.value='{$MODULE}'; 
																	DetailView.return_action.value='DetailView';
																	DetailView.return_id.value='{$ID}';
																	DetailView.module.value='{$MODULE}';
																	submitFormForAction('DetailView','EditView');"
															type="button"
															name="Edit"
															>
															<svg class="slds-button__icon slds-button__icon_left" aria-hidden="true">
																<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#edit"></use>
															</svg>
															{$APP.LBL_EDIT_BUTTON_LABEL}
														</button>
													{/if}
													{if ((isset($CREATE_PERMISSION) && $CREATE_PERMISSION eq 'permitted') || (isset($EDIT_PERMISSION) && $EDIT_PERMISSION eq 'yes')) && $MODULE neq 'Documents'}
														<button
															class="slds-button slds-button_neutral"
															title="{$APP.LBL_DUPLICATE_BUTTON_TITLE}"
															value="{$APP.LBL_DUPLICATE_BUTTON_TITLE}"
															accessKey="{$APP.LBL_DUPLICATE_BUTTON_KEY}"
															onclick="DetailView.return_module.value='{$MODULE}'; 
																	DetailView.return_action.value='DetailView'; 
																	DetailView.isDuplicate.value='true';
																	DetailView.module.value='{$MODULE}'; 
																	submitFormForAction('DetailView','EditView');" 
															type="submit"
															name="Duplicate"
															>
															<svg class="slds-button__icon slds-button__icon_left" aria-hidden="true">
																<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#file"></use>
															</svg>
															{$APP.LBL_DUPLICATE_BUTTON_LABEL}
														</button>
													{/if}
													{if $DELETE eq 'permitted'}	
														<button
															class="slds-button slds-button_text-destructive"
															title="{$APP.LBL_DELETE_BUTTON_TITLE}"
															value="{$APP.LBL_DELETE_BUTTON_TITLE}"
															accessKey="{$APP.LBL_DELETE_BUTTON_KEY}"
															onclick="DetailView.return_module.value='{$MODULE}'; 
																DetailView.return_action.value='index'; 
																{if $MODULE eq 'Accounts'} var confirmMsg = '{$APP.NTC_ACCOUNT_DELETE_CONFIRMATION}' {else} var confirmMsg = '{$APP.NTC_DELETE_CONFIRMATION}' {/if}; submitFormForActionWithConfirmation('DetailView', 'Delete', confirmMsg);"
															type="submit"
															name="Delete" 
															>
															<svg class="slds-button__icon slds-button__icon_left" aria-hidden="true">
																<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#delete"></use>
															</svg>
															{$APP.LBL_DELETE_BUTTON_LABEL}
														</button>
													{/if}
												</div>
												{if empty($Module_Popup_Edit)}
												<div class="slds-button-group" role="group">
													{include file='Components/DetailViewPirvNext.tpl'}
												</div>
												{/if}
											</div>
										{/if}
									</div>
								</td>
							</tr>
						</table>
<script>
	var fieldname = new Array({$VALIDATION_DATA_FIELDNAME});
	var fieldlabel = new Array({$VALIDATION_DATA_FIELDLABEL});
	var fielddatatype = new Array({$VALIDATION_DATA_FIELDDATATYPE});
</script>
</td>
</tr></table>
{if $MODULE|hasEmailField}
	<form name="SendMail" method="post"><div id="sendmail_cont" style="z-index:100001;position:absolute;"></div></form>
{/if}