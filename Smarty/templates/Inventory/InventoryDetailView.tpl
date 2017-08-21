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
<span id="crmspanid" style="display:none;position:absolute;"  onmouseover="show('crmspanid');">
	<a class="link" href="javascript:;" style="padding:10px 5px 0 0;">{$APP.LBL_EDIT_BUTTON}</a>
</span>
<div id="convertleaddiv" style="display:block;position:absolute;left:225px;top:150px;"></div>
<script>
{literal}
function showHideStatus(sId,anchorImgId,sImagePath)
{
	oObj = document.getElementById(sId);
	if(oObj.style.display == 'block')
	{
		oObj.style.display = 'none';
		if(anchorImgId !=null){
{/literal}
			document.getElementById(anchorImgId).src = 'themes/images/chevronright_60.png';
			document.getElementById(anchorImgId).alt = '{'LBL_Show'|@getTranslatedString:'Settings'}';
			document.getElementById(anchorImgId).title = '{'LBL_Show'|@getTranslatedString:'Settings'}';
			document.getElementById(anchorImgId).parentElement.className = 'exp_coll_block activate';
{literal}
		}
	}
	else
	{
		oObj.style.display = 'block';
		if(anchorImgId !=null){
{/literal}
			document.getElementById(anchorImgId).src = 'themes/images/chevrondown_60.png';
			document.getElementById(anchorImgId).alt = '{'LBL_Hide'|@getTranslatedString:'Settings'}';
			document.getElementById(anchorImgId).title = '{'LBL_Hide'|@getTranslatedString:'Settings'}';
			document.getElementById(anchorImgId).parentElement.className = 'exp_coll_block inactivate';
{literal}
		}
	}
}
function setCoOrdinate(elemId){
	oBtnObj = document.getElementById(elemId);
	var tagName = document.getElementById('lstRecordLayout');
	leftpos  = 0;
	toppos = 0;
	aTag = oBtnObj;
	do {
		leftpos += aTag.offsetLeft;
		toppos += aTag.offsetTop;
	} while(aTag = aTag.offsetParent);
	tagName.style.top= toppos + 20 + 'px';
	tagName.style.left= leftpos - 276 + 'px';
}

function getListOfRecords(obj, sModule, iId,sParentTab) {
	jQuery.ajax({
				method:"POST",
				url:'index.php?module=Users&action=getListOfRecords&ajax=true&CurModule='+sModule+'&CurRecordId='+iId+'&CurParentTab='+sParentTab,
	}).done(function(response) {
				sResponse = response;
				document.getElementById("lstRecordLayout").innerHTML = sResponse;
				Lay = 'lstRecordLayout';
				var tagName = document.getElementById(Lay);
				var leftSide = findPosX(obj);
				var topSide = findPosY(obj);
				var maxW = tagName.style.width;
				var widthM = maxW.substring(0,maxW.length-2);
				var getVal = parseInt(leftSide) + parseInt(widthM);
				if(getVal  > document.body.clientWidth ){
					leftSide = parseInt(leftSide) - parseInt(widthM);
					tagName.style.left = leftSide + 230 + 'px';
					tagName.style.top = topSide + 20 + 'px';
				}else{
					tagName.style.left = leftSide + 388 + 'px';
				}
				setCoOrdinate(obj.id);

				tagName.style.display = 'block';
				tagName.style.visibility = "visible";
			}
	);
}
{/literal}
function tagvalidate()
{ldelim}
	if(trim(document.getElementById('txtbox_tagfields').value) != '')
		SaveTag('txtbox_tagfields','{$ID}','{$MODULE}');
	else
	{ldelim}
		alert("{$APP.PLEASE_ENTER_TAG}");
		return false;
	{rdelim}
{rdelim}
function DeleteTag(id,recordid)
{ldelim}
	document.getElementById("vtbusy_info").style.display="inline";
	jQuery('#tag_'+id).fadeOut();
	jQuery.ajax({ldelim}
			method:"POST",
			url:"index.php?file=TagCloud&module={$MODULE}&action={$MODULE}Ajax&ajxaction=DELETETAG&recordid="+recordid+"&tagid=" +id,
	{rdelim}).done(function(response) {ldelim}
				getTagCloud();
				jQuery("#vtbusy_info").hide();
	{rdelim}
	);
{rdelim}

</script>

<div id="lstRecordLayout" class="layerPopup" style="display:none;width:325px;height:300px;"></div>
<table width="100%" cellpadding="2" cellspacing="0" border="0" class="detailview_wrapper_table">
	<tr>
		<td class="detailview_wrapper_cell">
		{include file='Buttons_List.tpl'}

		<!-- Contents -->
			<table border=0 cellspacing=0 cellpadding=0 width=98% align=center>
				<tr>
					<td>
						<!-- PUBLIC CONTENTS STARTS-->
						<div class="small" onclick="hndCancelOutsideClick();";>
							<table class="slds-table slds-no-row-hover slds-table--cell-buffer slds-table-moz" style="background-color: #f7f9fb;">
								<tr class="slds-text-title--caps">
									<td style="padding: 0;">
										{* Module Record numbering, used MOD_SEQ_ID instead of ID *}
										{assign var="USE_ID_VALUE" value=$MOD_SEQ_ID}
										{if $USE_ID_VALUE eq ''} {assign var="USE_ID_VALUE" value=$ID} {/if}
										<div class="slds-page-header s1FixedFullWidth s1FixedTop forceHighlightsStencilDesktop" style="height: 70px;">
											<div class="slds-grid primaryFieldRow" style="transform: translate3d(0, -8.65823px, 0);">
												<div class="slds-grid slds-col slds-has-flexi-truncate slds-media--center">
													<div class="profilePicWrapper slds-media slds-no-space" style="transform: scale3d(0.864715, 0.864715, 1) translate3d(4.32911px, 2.16456px, 0);">
														<div class="slds-media__figure slds-icon forceEntityIcon">
															<span class="photoContainer forceSocialPhoto">
																<div class="small roundedSquare forceEntityIcon img-background">
																	<span class="uiImage">
																		{if $MODULE eq 'Quotes'}
																			<img src="{'quotes_120.png'|@vtiger_imageurl:$THEME}" class="icon " alt="Quotes" title="Quotes">
																		{elseif $MODULE eq 'SalesOrder'}
																			<img src="{'salesorder_120.png'|@vtiger_imageurl:$THEME}" class="icon " alt="SalesOrder" title="SalesOrder">
																		{elseif $MODULE eq 'Invoice'}
																			<img src="{'invoice_120.png'|@vtiger_imageurl:$THEME}" class="icon " alt="Invoice" title="Invoice" style="height:1.8rem; padding-top: 1px;">
																		{elseif $MODULE eq 'PriceBooks'}
																			<img src="{'pricebook_120.png'|@vtiger_imageurl:$THEME}" class="icon " alt="PriceBooks" title="PriceBooks">
																		{/if}
																	</span>
																</div>
															</span>
														</div>
													</div>
													<div class="slds-media__body">
														<p class="slds-text-heading--label slds-line-height--reset" style="opacity: 1;">{$SINGLE_MOD|@getTranslatedString:$MODULE} {$APP.LBL_INFORMATION}</p>
														<h1 class="slds-page-header__title slds-m-right--small slds-truncate slds-align-middle">
														<span class="uiOutputText">[ {$USE_ID_VALUE} ] {$NAME}</span>
															<span class="small" style="text-transform: capitalize;">{$UPDATEINFO}</span>&nbsp;&nbsp;&nbsp;
															<span id="vtbusy_info" style="display:none;" valign="bottom">
																<img src="{'vtbusy.gif'|@vtiger_imageurl:$THEME}" border="0">
															</span>
														</h1>
													</div>
												</div>
												<div class="slds-col slds-no-flex slds-grid slds-align-middle actionsContainer" id="detailview_utils_thirdfiller">
													<!-- buttons here -->
													<div class="slds-grid forceActionsContainer">
														{if $EDIT_PERMISSION eq 'yes'}
														<input title="{$APP.LBL_EDIT_BUTTON_TITLE}" accessKey="{$APP.LBL_EDIT_BUTTON_KEY}" class="slds-button slds-button--neutral not-selected slds-not-selected uiButton" onclick="DetailView.return_module.value='{$MODULE}'; DetailView.return_action.value='DetailView'; DetailView.return_id.value='{$ID}';DetailView.module.value='{$MODULE}'; submitFormForAction('DetailView','EditView');" type="button" name="Edit" value="&nbsp;{$APP.LBL_EDIT_BUTTON_LABEL}&nbsp;">&nbsp;
														{/if}

														{if ((isset($CREATE_PERMISSION) && $CREATE_PERMISSION eq 'permitted') || (isset($EDIT_PERMISSION) && $EDIT_PERMISSION eq 'yes')) && $MODULE neq 'Documents'}
														<input title="{$APP.LBL_DUPLICATE_BUTTON_TITLE}" accessKey="{$APP.LBL_DUPLICATE_BUTTON_KEY}" class="slds-button slds-button--neutral not-selected slds-not-selected uiButton"  onclick="DetailView.return_module.value='{$MODULE}'; DetailView.return_action.value='DetailView'; DetailView.isDuplicate.value='true';DetailView.module.value='{$MODULE}'; submitFormForAction('DetailView','EditView');" type="button" name="Duplicate" value="{$APP.LBL_DUPLICATE_BUTTON_LABEL}">&nbsp;
														{/if}

														{if $DELETE eq 'permitted'}
														<input title="{$APP.LBL_DELETE_BUTTON_TITLE}" accessKey="{$APP.LBL_DELETE_BUTTON_KEY}" class="slds-button slds-button--neutral not-selected slds-not-selected uiButton" onclick="DetailView.return_module.value='{$MODULE}'; DetailView.return_action.value='index'; {if $MODULE eq 'Accounts'} var confirmMsg = '{$APP.NTC_ACCOUNT_DELETE_CONFIRMATION}' {else} var confirmMsg = '{$APP.NTC_DELETE_CONFIRMATION}' {/if}; submitFormForActionWithConfirmation('DetailView', 'Delete', confirmMsg);" type="button" name="Delete" value="{$APP.LBL_DELETE_BUTTON_LABEL}">&nbsp;
														{/if}

														<p class="slds-text-heading--label slds-line-height--reset" style="text-align: right; margin: 7px 0 0 5px ;">
															{if $privrecord neq ''}
																<span class="detailview_utils_prev" onclick="location.href='index.php?module={$MODULE}&viewtype={if isset($VIEWTYPE)}{$VIEWTYPE}{/if}&action=DetailView&record={$privrecord}&parenttab={$CATEGORY}&start={$privrecordstart}'" title="{$APP.LNK_LIST_PREVIOUS}">
																	<img align="absmiddle" title="{$APP.LNK_LIST_PREVIOUS}" accessKey="{$APP.LNK_LIST_PREVIOUS}"  name="privrecord" value="{$APP.LNK_LIST_PREVIOUS}" src="{'rec_prev.gif'|@vtiger_imageurl:$THEME}">
																</span>&nbsp;
															{else}
																<span class="detailview_utils_prev" title="{$APP.LNK_LIST_PREVIOUS}">
																	<img align="absmiddle" width="23" src="{'rec_prev_disabled.gif'|@vtiger_imageurl:$THEME}">
																</span>&nbsp;
															{/if}

															{if $privrecord neq '' || $nextrecord neq ''}
																<span class="detailview_utils_jumpto" id="jumpBtnIdTop" onclick="var obj = this;var lhref = getListOfRecords(obj, '{$MODULE}',{$ID},'{$CATEGORY}');" title="{$APP.LBL_JUMP_BTN}">
																	<img align="absmiddle" title="{$APP.LBL_JUMP_BTN}" accessKey="{$APP.LBL_JUMP_BTN}" name="jumpBtnIdTop" id="jumpBtnIdTop" src="{'replace_60.png'|@vtiger_imageurl:$THEME}" width="18">
																</span>&nbsp;
															{/if}

															{if $nextrecord neq ''}
																<span class="detailview_utils_next" onclick="location.href='index.php?module={$MODULE}&viewtype={if isset($VIEWTYPE)}{$VIEWTYPE}{/if}&action=DetailView&record={$nextrecord}&parenttab={$CATEGORY}&start={$nextrecordstart}'" title="{$APP.LNK_LIST_NEXT}">
																	<img align="absmiddle" title="{$APP.LNK_LIST_NEXT}" accessKey="{$APP.LNK_LIST_NEXT}"  name="nextrecord" src="{'rec_next.gif'|@vtiger_imageurl:$THEME}">
																</span>&nbsp;
															{else}
																<span class="detailview_utils_next" title="{$APP.LNK_LIST_NEXT}">
																	<img align="absmiddle" title="{$APP.LNK_LIST_NEXT}" width="23" src="{'rec_next_disabled.gif'|@vtiger_imageurl:$THEME}"/>
																</span>&nbsp;
															{/if}
																{*<!-- <span class="detailview_utils_toggleactions"><img align="absmiddle" title="{$APP.TOGGLE_ACTIONS}" src="{'menu-icon.png'|@vtiger_imageurl:$THEME}" width="16px;" onclick="{literal}if (document.getElementById('actioncolumn').style.display=='none') {document.getElementById('actioncolumn').style.display='table-cell';}else{document.getElementById('actioncolumn').style.display='none';}window.dispatchEvent(new Event('resize'));{/literal}"></span>&nbsp; -->*}
														</p>
													</div>
												</div>
											</div>
										</div>
									</td>
								</tr>
							</table>
							<br>
								{include file='applicationmessage.tpl'}
								<!-- Entity and More information tabs -->
								<table border=0 cellspacing=0 cellpadding=0 width=100% align=center>
									<tr>
										<td valign=top align=left>
											<div class="slds-truncate">
												<table class="slds-table slds-no-row-hover dvtContentSpace">
													<tr>
														<td valign="top" style="padding: 0;">
															<div class="detailview_utils_table_tabs" style="display: none;">
																<div class="detailview_utils_table_tab detailview_utils_table_tab_selected detailview_utils_table_tab_selected_top">{$SINGLE_MOD|@getTranslatedString:$MODULE} {$APP.LBL_INFORMATION}</div>
																{if $SinglePane_View eq 'false' && $IS_REL_LIST neq false && $IS_REL_LIST|@count > 0}
																	{if $HASRELATEDPANES eq 'true'}
																		{include file='RelatedPanes.tpl' tabposition='top' RETURN_RELATEDPANE=''}
																	{else}
																	<div class="detailview_utils_table_tab detailview_utils_table_tab_unselected detailview_utils_table_tab_unselected_top" onmouseout="fnHideDrop('More_Information_Modules_List');" onmouseover="fnDropDown(this,'More_Information_Modules_List');">
																		<a href="index.php?action=CallRelatedList&module={$MODULE}&record={$ID}&parenttab={$CATEGORY}">{$APP.LBL_MORE} {$APP.LBL_INFORMATION}</a>
																		<div onmouseover="fnShowDrop('More_Information_Modules_List')" onmouseout="fnHideDrop('More_Information_Modules_List')"
																		 id="More_Information_Modules_List" class="drop_mnu" style="left: 502px; top: 76px; display: none;">
																		<table border="0" cellpadding="0" cellspacing="0" width="100%">
																		{foreach key=_RELATION_ID item=_RELATED_MODULE from=$IS_REL_LIST}
																			<tr><td><a class="drop_down" href="index.php?action=CallRelatedList&module={$MODULE}&record={$ID}&parenttab={$CATEGORY}&selected_header={$_RELATED_MODULE}&relation_id={$_RELATION_ID}#tbl_{$MODULE}_{$_RELATED_MODULE}">{$_RELATED_MODULE|@getTranslatedString:$_RELATED_MODULE}</a></td></tr>
																		{/foreach}
																		</table>
																		</div>
																	</div>
																	{/if}
																{/if}
															</div>
															<!-- content cache -->
															<div class="slds-table--scoped">
																<ul class="slds-tabs--scoped__nav" role="tablist" style="margin-bottom: 0;">
																	<li class="slds-tabs--scoped__item active" onclick="openCity(event, 'tab--scoped-1')" title="{$SINGLE_MOD|@getTranslatedString:$MODULE} {$APP.LBL_INFORMATION}" role="presentation">
																		<a class="slds-tabs--scoped__link " href="javascript:void(0);" role="tab" tabindex="0" aria-selected="true" aria-controls="tab--scoped-1" id="tab--scoped--1__item">{$SINGLE_MOD|@getTranslatedString:$MODULE} {$APP.LBL_INFORMATION}</a>
																	</li>
																	{if $SinglePane_View eq 'false' && $IS_REL_LIST neq false && $IS_REL_LIST|@count > 0}
																		{if $HASRELATEDPANES eq 'true'} {include file='RelatedPanes.tpl' tabposition='top' RETURN_RELATEDPANE=''} {else}
																			<li class="slds-tabs--scoped__item slds-dropdown-trigger slds-dropdown-trigger_click slds-is-open" title="{$APP.LBL_MORE} {$APP.LBL_INFORMATION}" role="presentation">
																				<a class="slds-tabs--scoped__link" href="index.php?action=CallRelatedList&module={$MODULE}&record={$ID}&parenttab={$CATEGORY}" role="tab" tabindex="-1" aria-selected="false" aria-controls="tab--scoped-2" id="tab--scoped-2__item">{$APP.LBL_MORE} {$APP.LBL_INFORMATION}</a>
																					<div class="slds-dropdown slds-dropdown--left" style="margin-top: 0;">
																						<ul class="slds-dropdown__list slds-dropdown--length-7" role="menu">
																							{foreach key=_RELATION_ID item=_RELATED_MODULE from=$IS_REL_LIST}
																								<li class="slds-dropdown__item" role="presentation">
																									<a role="menuitem" tabindex="-1" class="drop_down" href="index.php?action=CallRelatedList&module={$MODULE}&record={$ID}&parenttab={$CATEGORY}&selected_header={$_RELATED_MODULE}&relation_id={$_RELATION_ID}#tbl_{$MODULE}_{$_RELATED_MODULE}">
																										{$_RELATED_MODULE|@getTranslatedString:$_RELATED_MODULE}</a>
																								</li>
																							{/foreach}
																						</ul>
																					</div>
																			</li>
																		{/if}
																	{/if}
																</ul>
															</div>
															<div id="tab--scoped-1" role="tabpanel" aria-labelledby="tab--scoped-1__item" class="slds-tabs--scoped__content slds-truncate">
																<table class="slds-table slds-no-row-hover slds-table-moz" ng-controller="detailViewng" style="border-collapse:separate; border-spacing: 1rem 2rem;">
																	<form action="index.php" method="post" name="DetailView" id="formDetailView">
																		<input type="hidden" id="hdtxt_IsAdmin" value="{if isset($hdtxt_IsAdmin)}{$hdtxt_IsAdmin}{else}0{/if}">
																		{include file='DetailViewHidden.tpl'}
																			{foreach key=header item=detail from=$BLOCKS name=BLOCKS}
																				<tr class="blockStyleCss">
																					<td class="detailViewContainer" valign="top">
																						<!-- This is added to display the existing comments -->
																						{if $header eq $APP.LBL_COMMENTS || (isset($MOD.LBL_COMMENT_INFORMATION) && $header eq $MOD.LBL_COMMENT_INFORMATION)}
																							<div class="flexipageComponent" style="background-color: #fff;">
																								<article class="slds-card container MEDIUM forceBaseCard runtime_sales_mergeMergeCandidatesPreviewCard" aria-describedby="header" style="margin: 0;">
																									<div class="slds-card__header slds-grid">
																										<header class="slds-media slds-media--center slds-has-flexi-truncate">
																											<div class="slds-media__body">
																												<h2 class="header-title-container" >
																													<span class="slds-text-heading--small slds-truncate actionLabel">
																														<b>{if isset($MOD.LBL_COMMENT_INFORMATION)}{$MOD.LBL_COMMENT_INFORMATION}{else}{$APP.LBL_COMMENTS}{/if}</b>
																													</span>
																												</h2>
																											</div>
																										</header>
																										<div class="slds-no-flex" data-aura-rendered-by="1224:0">
																											<div class="actionsContainer mapButton">
																											{if isset($MOD.LBL_ADDRESS_INFORMATION) && $header eq $MOD.LBL_ADDRESS_INFORMATION && ($MODULE eq 'Accounts' || $MODULE eq 'Contacts' || $MODULE eq 'Leads') } {if $MODULE eq 'Leads'}
																												<input name="mapbutton" type="button" value="{$APP.LBL_LOCATE_MAP}" class="slds-button slds-button--small slds-button--brand" onClick="searchMapLocation( 'Main' )" title="{$APP.LBL_LOCATE_MAP}"> {else}
																												<input name="mapbutton" value="{$APP.LBL_LOCATE_MAP}" class="slds-button slds-button--small slds-button--brand" type="button" onClick="fnvshobj(this,'locateMap');" onMouseOut="fninvsh('locateMap');" title="{$APP.LBL_LOCATE_MAP}"> {/if} {/if}
																											</div>
																										</div>
																									</div>
																										<div class="slds-card__body slds-card__body--inner">
																											<div class="commentData">{$COMMENT_BLOCK}</div>
																										</div>
																								</article>
																							</div>
																						{/if}

																						{if $header neq 'Comments' && (!isset($BLOCKS.$header.relatedlist) || $BLOCKS.$header.relatedlist eq 0)}
																							{strip}
																							<div class="forceRelatedListSingleContainer">
																								<article class="slds-card forceRelatedListCardDesktop" aria-describedby="header">
																									<div class="slds-card__header slds-grid">
																										<header class="slds-media slds-media--center slds-has-flexi-truncate">
																											<div class="slds-media__figure">
																												<div class="extraSmall forceEntityIcon" data-aura-rendered-by="3:1782;a" data-aura-class="forceEntityIcon">
																													<span data-aura-rendered-by="6:1782;a" class="uiImage" data-aura-class="uiImage">
																														<a href="javascript:showHideStatus('tbl{$header|replace:' ':''}','aid{$header|replace:' ':''}','{$IMAGE_PATH}');">
																															{if $BLOCKINITIALSTATUS[$header] eq 1}
																																<span class="exp_coll_block inactivate">
																																	<img id="aid{$header|replace:' ':''}" src="{'chevrondown_60.png'|@vtiger_imageurl:$THEME}" width="16" alt="{'LBL_Hide'|@getTranslatedString:'Settings'}" title="{'LBL_Hide'|@getTranslatedString:'Settings'}"/>
																																</span>
																															{else}
																																<span class="exp_coll_block activate">
																																	<img id="aid{$header|replace:' ':''}" src="{'chevronright_60.png'|@vtiger_imageurl:$THEME}" width="16" alt="{'LBL_Show'|@getTranslatedString:'Settings'}" title="{'LBL_Show'|@getTranslatedString:'Settings'}"/>
																																</span>
																															{/if}
																														</a>
																													</span>
																												</div>
																											</div>
																											<div class="slds-media__body">
																												<h2>
																													<span class="slds-text-title--caps slds-truncate slds-m-right--xx-small" title="{$header}">
																														<b>{$header}</b>
																													</span>
																												</h2>
																											</div>
																										</header>
																									</div>
																								</article>
																							</div>
																							{/strip}
																						{/if}

																						{if $header neq 'Comments'}
																							{if $BLOCKINITIALSTATUS[$header] eq 1 || !empty($BLOCKS.$header.relatedlist)}
																								<div style="display:block;" id="tbl{$header|replace:' ':''}" >
																							{else}
																								<div style="display:none;" id="tbl{$header|replace:' ':''}" >
																							{/if}
																									<table class="slds-table slds-table--cell-buffer slds-no-row-hover slds-table--bordered slds-table--fixed-layout small detailview_table">
																										{if $CUSTOMBLOCKS.$header.custom}
																											{include file=$CUSTOMBLOCKS.$header.tpl}
																										{elseif isset($BLOCKS.$header.relatedlist) && $IS_REL_LIST|@count > 0}
																											{assign var='RELBINDEX' value=$BLOCKS.$header.relatedlist}
																											{include file='RelatedListNew.tpl' RELATEDLISTS=$RELATEDLISTBLOCK.$RELBINDEX RELLISTID=$RELBINDEX}
																										{else}
																											{foreach item=detailInfo from=$detail}
																												<tr class="slds-line-height--reset">
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
																																{if $label ne ''}
																																	<td class="dvtCellLabel" align=right width=25%>
																																		{if $keycntimage ne ''}
																																			{$keycntimage}
																																		{elseif $label neq 'Tax Class'}<!-- Avoid to display the label Tax Class -->
																																			{if $keyid eq '71' || $keyid eq '72'}<!-- Currency symbol -->
																																				{$label} ({$keycursymb})
																																			{elseif $keyid eq '9'}
																																				{$label} {$APP.COVERED_PERCENTAGE}
																																			{elseif $keyid eq '14'}
																																				{"LBL_TIMEFIELD"|@getTranslatedString}
																																			{else}
																																				{$label}
																																			{/if}
																																		{/if}
																																	</td>
																																	{if $EDIT_PERMISSION eq 'yes' && $display_type neq '2' && $_readonly eq '0'}
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
																															{if $numfieldspainted eq 1 && $keyid neq 19 && $keyid neq 20}
																																<td colspan=2></td>
																															{/if}
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
																								{if ($smarty.foreach.BLOCKS.first && $CUSTOM_LINK_DETAILVIEWWIDGET->sequence <= 1) || ($CUSTOM_LINK_DETAILVIEWWIDGET->sequence == $smarty.foreach.BLOCKS.iteration + 1) || ($smarty.foreach.BLOCKS.last && $CUSTOM_LINK_DETAILVIEWWIDGET->sequence >= $smarty.foreach.BLOCKS.iteration + 1)}
																									<tr class="blockStyleCss">
																										<td class="detailViewContainer">{process_widget widgetLinkInfo=$CUSTOM_LINK_DETAILVIEWWIDGET}</td>
																									</tr>
																								{/if}
																							{/if}
																						{/foreach}
																					{/if}
																				{* END *}
																			{/foreach}
																		{*-- End of Blocks--*} 
																		<br>
																			<!-- Product Details informations -->
																			{if isset($ASSOCIATED_PRODUCTS)}
																				<tr class="blockStyleCss">
																					<td class="detailViewContainer">
																						{$ASSOCIATED_PRODUCTS}
																					</td>
																				</tr>
																			{/if}

																			<!-- The following table is used to display the buttons -->
																			{if $SinglePane_View eq 'true'}
																				{include file= 'RelatedListNew.tpl'}
																			{/if}
																</table>
															</div>
														</td>
														<!-- Inventory Actions -->
														<td style="{$DEFAULT_ACTION_PANEL_STATUS}" class="noprint action-block" id="actioncolumn">
															{include file="Inventory/InventoryActions.tpl"}
														<br>
															<!-- To display the Tag Clouds -->
															<div>
																{include file="TagCloudDisplay.tpl"}
															</div>
														</td>
													</tr>
												</table>
											</div>
										</td>
									</tr>
								</table>
						</div>
					</td>
				</tr>
			</table>
		<!-- PUBLIC CONTENTS STOPS-->
		<!-- Contents - end -->
		</td>
	</tr>
</table>
<script>
function getTagCloud()
{ldelim}
	var obj = document.getElementById("tagfields");
	if(obj != null && typeof(obj) != undefined) {ldelim}
		jQuery.ajax({ldelim}
				method:"POST",
				url:'index.php?module={$MODULE}&action={$MODULE}Ajax&file=TagCloud&ajxaction=GETTAGCLOUD&recordid={$ID}',
{rdelim}).done(function(response) {ldelim}
			document.getElementById("tagfields").innerHTML=response;
			document.getElementById("txtbox_tagfields").value ='';
{rdelim}
		);
	{rdelim}
{rdelim}
getTagCloud();
</script>

		</td>
	</tr>
</table>
<script>
  var fieldname = new Array({$VALIDATION_DATA_FIELDNAME});
  var fieldlabel = new Array({$VALIDATION_DATA_FIELDLABEL});
  var fielddatatype = new Array({$VALIDATION_DATA_FIELDDATATYPE});
</script>
