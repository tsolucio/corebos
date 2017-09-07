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
<span id="crmspanid" style="display:none;position:absolute;" onmouseover="show('crmspanid');">
	<a class="link" href="javascript:;" style="padding:10px 5px 0 0;">{$APP.LBL_EDIT_BUTTON}</a>
</span>
<div id="convertleaddiv" style="display:block;position:absolute;left:225px;top:150px;"></div>
<script>
{literal}
function callConvertLeadDiv(id){
		jQuery.ajax({
				method:"POST",
				url:'index.php?module=Leads&action=LeadsAjax&file=ConvertLead&record='+id,
		}).done(function(response) {
				jQuery("#convertleaddiv").html(response);
				jQuery("#conv_leadcal").html();
			}
		);
}
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
				jQuery("#lstRecordLayout").html(sResponse);
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
					tagName.style.left = leftSide + 230 + 'px';
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

//Added to send a file, in Documents module, as an attachment in an email
function sendfile_email()
{ldelim}
	filename = document.getElementById('dldfilename').value;
	document.DetailView.submit();
	OpenCompose(filename,'Documents');
{rdelim}

</script>
<div id="lstRecordLayout" class="layerPopup" style="display:none;width:325px;height:300px;"></div>
{if $MODULE eq 'Accounts' || $MODULE eq 'Contacts' || $MODULE eq 'Leads'} {if $MODULE eq 'Accounts'} {assign var=address1 value='$MOD.LBL_BILLING_ADDRESS'} {assign var=address2 value='$MOD.LBL_SHIPPING_ADDRESS'} {/if} {if $MODULE eq 'Contacts'} {assign var=address1 value='$MOD.LBL_PRIMARY_ADDRESS'} {assign var=address2 value='$MOD.LBL_ALTERNATE_ADDRESS'} {/if}
<div id="locateMap" onMouseOut="fninvsh('locateMap')" onMouseOver="fnvshNrm('locateMap')">
	<table bgcolor="#ffffff" border="0" cellpadding="0" cellspacing="0" width="100%">
		<tr>
			<td>
				{if $MODULE eq 'Accounts'}
				<a href="javascript:;" onClick="fninvsh('locateMap'); searchMapLocation( 'Main' );" class="calMnu">{$MOD.LBL_BILLING_ADDRESS}</a>
				<a href="javascript:;" onClick="fninvsh('locateMap'); searchMapLocation( 'Other' );" class="calMnu">{$MOD.LBL_SHIPPING_ADDRESS}</a> {/if} {if $MODULE eq 'Contacts'}
				<a href="javascript:;" onClick="fninvsh('locateMap'); searchMapLocation( 'Main' );" class="calMnu">{$MOD.LBL_PRIMARY_ADDRESS}</a>
				<a href="javascript:;" onClick="fninvsh('locateMap'); searchMapLocation( 'Other' );" class="calMnu">{$MOD.LBL_ALTERNATE_ADDRESS}</a> {/if}
			</td>
		</tr>
	</table>
</div>
{/if}


<table width="100%" cellpadding="2" cellspacing="0" border="0" class="detailview_wrapper_table">
	<tr>
		<td class="detailview_wrapper_cell">
			{include file='Buttons_List.tpl'}
				<!-- Contents -->
			<table border=0 cellspacing=0 cellpadding=0 width=98% align=center>
				<tr>
					<td>
						<!-- PUBLIC CONTENTS STARTS-->
						<div class="small" onclick="hndCancelOutsideClick();">
							<table class="slds-table slds-no-row-hover slds-table--cell-buffer slds-table-moz" style="background-color: #f7f9fb;">
								<tr class="slds-text-title--caps">
									<td style="padding: 0;">
										{* Module Record numbering, used MOD_SEQ_ID instead of ID *} {assign var="USE_ID_VALUE" value=$MOD_SEQ_ID} {if $USE_ID_VALUE eq ''} {assign var="USE_ID_VALUE" value=$ID} {/if}
										<div class="slds-page-header s1FixedFullWidth s1FixedTop forceHighlightsStencilDesktop" style="height: 70px;">
											<div class="slds-grid primaryFieldRow" style="transform: translate3d(0, -8.65823px, 0);">
												<div class="slds-grid slds-col slds-has-flexi-truncate slds-media--center">
													<div class="profilePicWrapper slds-media slds-no-space" style="transform: scale3d(0.864715, 0.864715, 1) translate3d(4.32911px, 2.16456px, 0);">
														<div class="slds-media__figure slds-icon forceEntityIcon">
															<span class="photoContainer forceSocialPhoto">
																<div class="small roundedSquare forceEntityIcon img-background">
																	<span class="uiImage">
																		{if $MODULE eq 'Contacts'}
																			<img src="{'contact_120.png'|@vtiger_imageurl:$THEME}" class="icon " alt="Contact" title="Contact">
																		{elseif $MODULE eq 'Accounts'}
																			<img src="{'account_120.png'|@vtiger_imageurl:$THEME}" class="icon " alt="Organization" title="Organization">
																		{elseif $MODULE eq 'Leads'}
																			<img src="{'lead_120.png'|@vtiger_imageurl:$THEME}" class="icon " alt="Leads" title="Leads">
																		{elseif $MODULE eq 'Campaigns'}
																			<img src="{'campaign_120.png'|@vtiger_imageurl:$THEME}" class="icon " alt="Campaigns" title="Campaigns">
																		{elseif $MODULE eq 'Potentials'}
																			<img src="{'opportunity_120.png'|@vtiger_imageurl:$THEME}" class="icon " alt="Opportunity" title="Opportunity">
																		{elseif $MODULE eq 'Documents'}
																			<img src="{'document_120.png'|@vtiger_imageurl:$THEME}" class="icon " alt="Documents" title="Documents">
																		{elseif $MODULE eq 'HelpDesk'}
																			<img src="{'help_desk_120.png'|@vtiger_imageurl:$THEME}" class="icon " alt="HelpDesk" title="HelpDesk">
																		{elseif $MODULE eq 'Faq'}
																			<img src="{'faq_120.png'|@vtiger_imageurl:$THEME}" class="icon " alt="Faq" title="Faq">
																		{elseif $MODULE eq 'ServiceContracts'}
																			<img src="{'service_contract_120.png'|@vtiger_imageurl:$THEME}" class="icon " alt="ServiceContracts" title="ServiceContracts">
																		{elseif $MODULE eq 'ModComments'}
																			<img src="{'quick_text_120.png'|@vtiger_imageurl:$THEME}" class="icon " alt="Comments" title="Comments">
																		{elseif $MODULE eq 'InventoryDetails'}
																			<img src="{'inventory_details_120.png'|@vtiger_imageurl:$THEME}" class="icon " alt="InventoryDetails" title="InventoryDetails">
																		{elseif $MODULE eq 'GlobalVariable'}
																			<img src="{'global_120.png'|@vtiger_imageurl:$THEME}" class="icon " alt="GlobalVariable" title="GlobalVariable">
																		{elseif $MODULE eq 'cbCalendar'}
																			<img src="{'todo_120.png'|@vtiger_imageurl:$THEME}" class="icon " alt="Todo" title="Todo">
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
															<span class="small" style="text-transform: capitalize;">{$UPDATEINFO}</span>
															<span id="vtbusy_info" style="display:none; text-transform: capitalize;" valign="bottom">
																<img src="{'vtbusy.gif'|@vtiger_imageurl:$THEME}" border="0">
															</span>
														</h1>
													</div>
												</div>
												<div class="slds-col slds-no-flex slds-grid slds-align-middle actionsContainer" id="detailview_utils_thirdfiller">
													<div class="slds-grid forceActionsContainer">
														{if $EDIT_PERMISSION eq 'yes'}
															<input class="slds-button slds-button--neutral not-selected slds-not-selected uiButton" aria-live="assertive" type="button" name="Edit" title="{$APP.LBL_EDIT_BUTTON_TITLE}" accessKey="{$APP.LBL_EDIT_BUTTON_KEY}" onclick="DetailView.return_module.value='{$MODULE}'; DetailView.return_action.value='DetailView'; DetailView.return_id.value='{$ID}';DetailView.module.value='{$MODULE}';submitFormForAction('DetailView','EditView');" value="&nbsp;{$APP.LBL_EDIT_BUTTON_LABEL}&nbsp;" />&nbsp;
														{/if}

														{if ((isset($CREATE_PERMISSION) && $CREATE_PERMISSION eq 'permitted') || (isset($EDIT_PERMISSION) && $EDIT_PERMISSION eq 'yes')) && $MODULE neq 'Documents'}
														<input title="{$APP.LBL_DUPLICATE_BUTTON_TITLE}" accessKey="{$APP.LBL_DUPLICATE_BUTTON_KEY}" class="slds-button slds-button--neutral not-selected slds-not-selected uiButton" onclick="DetailView.return_module.value='{$MODULE}'; DetailView.return_action.value='DetailView'; DetailView.isDuplicate.value='true';DetailView.module.value='{$MODULE}'; submitFormForAction('DetailView','EditView');" type="button" name="Duplicate" value="{$APP.LBL_DUPLICATE_BUTTON_LABEL}" />&nbsp;
														{/if}

														{if $DELETE eq 'permitted'}
															<input title="{$APP.LBL_DELETE_BUTTON_TITLE}" accessKey="{$APP.LBL_DELETE_BUTTON_KEY}" 
														class="slds-button slds-button--neutral not-selected slds-not-selected uiButton" onclick="DetailView.return_module.value='{$MODULE}'; DetailView.return_action.value='index'; {if $MODULE eq 'Accounts'} var confirmMsg = '{$APP.NTC_ACCOUNT_DELETE_CONFIRMATION}' {else} var confirmMsg = '{$APP.NTC_DELETE_CONFIRMATION}' {/if}; submitFormForActionWithConfirmation('DetailView', 'Delete', confirmMsg);" type="button" name="Delete" value="{$APP.LBL_DELETE_BUTTON_LABEL}" />&nbsp; {/if} 
														
														{*
														<span class="detailview_utils_toggleactions">
															<img align="absmiddle" title="{$APP.TOGGLE_ACTIONS}" src="{'list_60.png'|@vtiger_imageurl:$THEME}"
															onclick="
															{literal} 
															if (document.getElementById('actioncolumn').style.display=='none')
															{
																document.getElementById('actioncolumn').style.display='table-cell';
															}
															else
															{
																document.getElementById('actioncolumn').style.display='none';
															}
															window.dispatchEvent(new Event('resize'));
															{/literal}">
														</span>
														*}

														<p class="slds-text-heading--label slds-line-height--reset" style="text-align: right; margin: 7px 0 0 5px ;">
															{if $privrecord neq ''}
																<span class="detailview_utils_prev" onclick="location.href='index.php?module={$MODULE}&viewtype={if isset($VIEWTYPE)}{$VIEWTYPE}{/if}&action=DetailView&record={$privrecord}&parenttab={$CATEGORY}&start={$privrecordstart}'" title="{$APP.LNK_LIST_PREVIOUS}">
																	<img align="absmiddle" accessKey="{$APP.LNK_LIST_PREVIOUS}" name="privrecord" value="{$APP.LNK_LIST_PREVIOUS}" src="{'rec_prev.gif'|@vtiger_imageurl:$THEME}"/>
																</span>&nbsp;
															{else}
																<span class="detailview_utils_prev" title="{$APP.LNK_LIST_PREVIOUS}">
																	<img align="absmiddle" width="23" src="{'rec_prev_disabled.gif'|@vtiger_imageurl:$THEME}">
																</span>&nbsp;
															{/if} 
															{if $privrecord neq '' || $nextrecord neq ''}
															<span class="detailview_utils_jumpto" id="jumpBtnIdTop"
															onclick="
															var obj = this;
															var lhref = getListOfRecords(obj, '{$MODULE}',{$ID},'{$CATEGORY}');"
															title="{$APP.LBL_JUMP_BTN}">
																<img align="absmiddle" title="{$APP.LBL_JUMP_BTN}" accessKey="{$APP.LBL_JUMP_BTN}" name="jumpBtnIdTop"
																src="{'replace_60.png'|@vtiger_imageurl:$THEME}" width="18" id="jumpBtnIdTop"  />
															</span>&nbsp;
															{/if}
															{if $nextrecord neq ''}
															<span class="detailview_utils_next" onclick="location.href='index.php?module={$MODULE}&viewtype={if isset($VIEWTYPE)}{$VIEWTYPE}{/if}&action=DetailView&record={$nextrecord}&parenttab={$CATEGORY}&start={$nextrecordstart}'" title="{$APP.LNK_LIST_NEXT}">
																<img align="absmiddle" accessKey="{$APP.LNK_LIST_NEXT}" name="nextrecord" src="{'rec_next.gif'|@vtiger_imageurl:$THEME}">
															</span>&nbsp;
															{else}
															<span class="detailview_utils_next" title="{$APP.LNK_LIST_NEXT}">
																<img align="absmiddle" title="{$APP.LNK_LIST_NEXT}"
																width="23" src="{'rec_next_disabled.gif'|@vtiger_imageurl:$THEME}"/>
															</span>&nbsp;
															{/if}
														</p>
													</div> {*/.forceActionsContainer*}
												</div> {*/#detailview_utils_thirdfiller*}
											</div> {*/primaryFieldRow*}
										</div> {*/forceHighlightsStencilDesktop*}
									</td>
								</tr>
							</table>
							<br> {include file='applicationmessage.tpl'}
							<!-- Entity and More information tabs -->
							<table border=0 cellspacing=0 cellpadding=0 width=100% align=center>
								<tr>
									<td valign=top align=left>
										<div class="slds-truncate">
											<table class="slds-table slds-no-row-hover dvtContentSpace">
												<tr>
													<td valign="top" style="padding: 0;">
														<!-- content cache -->
														<div class="slds-table--scoped">
															<ul class="slds-tabs--scoped__nav" role="tablist" style="margin-bottom: 0;">
																<li class="slds-tabs--scoped__item active" title="{$SINGLE_MOD|@getTranslatedString:$MODULE} {$APP.LBL_INFORMATION}" role="presentation">
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
															<div id="tab--scoped-1" role="tabpanel" aria-labelledby="tab--scoped-1__item" class="slds-tabs--scoped__content slds-truncate">
																<table class="slds-table slds-no-row-hover slds-table-moz" ng-controller="detailViewng" style="border-collapse:separate; border-spacing: 1rem;">
																	<form action="index.php" method="post" name="DetailView" id="formDetailView">
																		<input type="hidden" id="hdtxt_IsAdmin" value="{if isset($hdtxt_IsAdmin)}{$hdtxt_IsAdmin}{else}0{/if}">
																			{include file='DetailViewHidden.tpl'}
																			{foreach key=header item=detail from=$BLOCKS name=BLOCKS}
																				<tr class="blockStyleCss">
																					<td class="detailViewContainer" valign="top">
																						<!-- Detailed View Code starts here-->
																						<!-- This is added to display the existing comments -->
																						{if $header eq $APP.LBL_COMMENTS || (isset($MOD.LBL_COMMENTS) && $header eq $MOD.LBL_COMMENTS) || (isset($MOD.LBL_COMMENT_INFORMATION) && $header eq $MOD.LBL_COMMENT_INFORMATION)}
																						<div class="forceRelatedListSingleContainer">
																							<article class="slds-card forceRelatedListCardDesktop" aria-describedby="header">
																								<div class="slds-card__header slds-grid">
																									<header class="slds-media slds-media--center slds-has-flexi-truncate">
																										<div class="slds-media__body">
																											<h2>
																												<span class="slds-text-title--caps slds-truncate slds-m-right--xx-small actionLabel">
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
																						<br/>
																						<br/>
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
																														{if isset($BLOCKINITIALSTATUS[$header]) || $BLOCKINITIALSTATUS[$header] eq 1}
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
																									<div class="slds-no-flex" data-aura-rendered-by="1224:0">
																										<div class="actionsContainer mapButton">
																											{if isset($MOD.LBL_ADDRESS_INFORMATION) && $header eq $MOD.LBL_ADDRESS_INFORMATION && ($MODULE eq 'Accounts' || $MODULE eq 'Contacts' || $MODULE eq 'Leads') }
																												{if $MODULE eq 'Leads'}
																													<input name="mapbutton" type="button" value="{$APP.LBL_LOCATE_MAP}" class="slds-button slds-button--small slds-button--brand" onClick="searchMapLocation( 'Main' )" title="{$APP.LBL_LOCATE_MAP}">
																												{else}
																													<input name="mapbutton" value="{$APP.LBL_LOCATE_MAP}" class="slds-button slds-button--small slds-button--brand" type="button" onClick="fnvshobj(this,'locateMap');" onMouseOut="fninvsh('locateMap');" title="{$APP.LBL_LOCATE_MAP}">
																												{/if}
																											{/if}
																										</div>
																									</div>
																								</div>
																							</article>
																						</div>
																						{/strip}
																						{/if}

																						{if $header neq 'Comments'}
																							{if (isset($BLOCKINITIALSTATUS[$header]) && $BLOCKINITIALSTATUS[$header] eq 1) || !empty($BLOCKS.$header.relatedlist)}
																								<div class="slds-truncate" style="display:block;" id="tbl{$header|replace:' ':''}">
																							{else}
																								<div class="slds-truncate" style="display:block;" id="tbl{$header|replace:' ':''}">
																							{/if}
																									<table class="slds-table slds-table--cell-buffer slds-no-row-hover slds-table--fixed-layout small detailview_table">
																										{if !empty($CUSTOMBLOCKS.$header.custom)}
																											{include file=$CUSTOMBLOCKS.$header.tpl}
																										{elseif isset($BLOCKS.$header.relatedlist) && $IS_REL_LIST|@count > 0}
																											{assign var='RELBINDEX' value=$BLOCKS.$header.relatedlist}
																											{include file='RelatedListNew.tpl' RELATEDLISTS=$RELATEDLISTBLOCK.$RELBINDEX RELLISTID=$RELBINDEX}
																										{else}
																											{foreach item=detailInfo from=$detail}
																												<tr class="slds-line-height--reset">
																													{foreach key=label item=data from=$detailInfo}
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
																															{elseif $keyid eq '71' || $keyid eq '72'}<!-- Currency symbol -->
																																{$label} ({$keycursymb})
																															{elseif $keyid eq '9'}
																																{$label} {$APP.COVERED_PERCENTAGE}
																															{elseif $keyid eq '14'}
																																{$label} {"LBL_TIMEFIELD"|@getTranslatedString}
																															{else}
																																{$label}
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

																				<!-- Inventory - Product Details informations -->
																			{if isset($ASSOCIATED_PRODUCTS)}
																				<tr class="blockStyleCss">
																					<td class="detailViewContainer">
																						{$ASSOCIATED_PRODUCTS}
																					</td>
																				</tr>
																			{/if}

																			{if $SinglePane_View eq 'true' && $IS_REL_LIST|@count > 0}
																				{include file= 'RelatedListNew.tpl'}
																			{/if}
																</table>
															</div>
														</div><!-- /.slds-table--scoped -->
														<!-- end content cache -->
													</td>

													<td class="noprint action-block" style="{$DEFAULT_ACTION_PANEL_STATUS}" id="actioncolumn">
														<div class="flexipageComponent">
															<!-- right side relevant info -->
															<!-- Action links for Event & Todo START-by Minnie -->
															<article class="slds-card container MEDIUM forceBaseCard runtime_sales_mergeMergeCandidatesPreviewCard" aria-describedby="header" style="margin: 0;">
																<div class="slds-card__header slds-grid">
																	<header class="slds-media slds-media--center slds-has-flexi-truncate">
																		<div class="slds-media__body">
																			<h2 class="header-title-container">
																				<span class="slds-text-heading--small slds-truncate actionLabel"><b>{$APP.LBL_ACTIONS}</b></span>
																			</h2>
																		</div>
																	</header>
																</div>
																<div class="slds-card__body slds-card__body--inner">
																	{if $MODULE eq 'HelpDesk'} {if $CONVERTASFAQ eq 'permitted'}
																	<div class="actionData actionlink_converttofaq">
																		<a class="webMnu" href="index.php?return_module={$MODULE}&return_action=DetailView&record={$ID}&return_id={$ID}&module={$MODULE}&action=ConvertAsFAQ"><img src="{'convert.gif'|@vtiger_imageurl:$THEME}" hspace="5" align="absmiddle"  border="0"/></a>
																		<a class="webMnu" href="index.php?return_module={$MODULE}&return_action=DetailView&record={$ID}&return_id={$ID}&module={$MODULE}&action=ConvertAsFAQ">{$MOD.LBL_CONVERT_AS_FAQ_BUTTON_LABEL}</a>
																	</div>
																	{/if}
																	{elseif $MODULE eq 'Potentials'} {if $CONVERTINVOICE eq 'permitted'}
																	<div class="actionData actionlink_converttoinvoice">
																		<a class="webMnu" href="index.php?return_module={$MODULE}&return_action=DetailView&return_id={$ID}&convertmode={$CONVERTMODE}&module=Invoice&action=EditView&account_id={$ACCOUNTID}"><img src="{'actionGenerateInvoice.gif'|@vtiger_imageurl:$THEME}" hspace="5" align="absmiddle"  border="0"/></a>
																		<a class="webMnu" href="index.php?return_module={$MODULE}&return_action=DetailView&return_id={$ID}&convertmode={$CONVERTMODE}&module=Invoice&action=EditView&account_id={$ACCOUNTID}">{$APP.LBL_CREATE} {$APP.Invoice}</a>
																	</div>
																	{/if}
																	
																	{elseif $TODO_PERMISSION eq 'true' || $EVENT_PERMISSION eq 'true' || $CONTACT_PERMISSION eq 'true'|| $MODULE eq 'Contacts' || $MODULE eq 'Leads' || ($MODULE eq 'Documents')}
																		{if $MODULE eq 'Contacts'} {assign var=subst value="contact_id"} {assign var=acc value="&account_id=$accountid"} {else} {assign var=subst value="parent_id"} {assign var=acc value=""} {/if}
																			{if $MODULE eq 'Leads' || $MODULE eq 'Contacts' || $MODULE eq 'Accounts'}
																			{if $SENDMAILBUTTON eq 'permitted'}
																				<div class="actionData actionlink_sendemail">
																					{foreach key=index item=email from=$EMAILS}
																						<input type="hidden" name="email_{$index}" value="{$email}" />
																					{/foreach}
																					<a href="javascript:void(0);" class="webMnu" onclick="{$JS}"><img src="{'sendmail.png'|@vtiger_imageurl:$THEME}" hspace="5" align="absmiddle"  border="0"/></a>
																					<a href="javascript:void(0);" class="webMnu" onclick="{$JS}">{$APP.LBL_SENDMAIL_BUTTON_LABEL}</a>
																				</div>
																			{/if}
																		{/if}
																		{if $MODULE eq 'Contacts' || $EVENT_PERMISSION eq 'true'}
																			<div class="actionData actionlink_addevent">
																				<a href="index.php?module=Calendar4You&action=EventEditView&return_module={$MODULE}&return_action=DetailView&activity_mode=Events&return_id={$ID}&{$subst}={$ID}{$acc}&parenttab={$CATEGORY}" class="webMnu"><img src="{'AddEvent.gif'|@vtiger_imageurl:$THEME}" hspace="5" align="absmiddle"  border="0"/></a>
																				<a href="index.php?module=Calendar4You&action=EventEditView&return_module={$MODULE}&return_action=DetailView&activity_mode=Events&return_id={$ID}&{$subst}={$ID}{$acc}&parenttab={$CATEGORY}" class="webMnu">{$APP.LBL_ADD_NEW} {$APP.Event}</a>
																			</div>
																		{/if}
																		{if $TODO_PERMISSION eq 'true' && ($MODULE eq 'Accounts' || $MODULE eq 'Leads')}
																			<div class="actionData actionlink_addtodo">
																				<a href="index.php?module=Calendar4You&action=EventEditView&return_module={$MODULE}&return_action=DetailView&activity_mode=Task&return_id={$ID}&{$subst}={$ID}{$acc}&parenttab={$CATEGORY}" class="webMnu"><img src="{'AddToDo.gif'|@vtiger_imageurl:$THEME}" hspace="5" align="absmiddle" border="0"/></a>
																				<a href="index.php?module=Calendar4You&action=EventEditView&return_module={$MODULE}&return_action=DetailView&activity_mode=Task&return_id={$ID}&{$subst}={$ID}{$acc}&parenttab={$CATEGORY}" class="webMnu">{$APP.LBL_ADD_NEW} {$APP.Todo}</a>
																			</div>
																		{/if}
																		{if $MODULE eq 'Contacts' && $CONTACT_PERMISSION eq 'true'}
																			<div class="actionData actionlink_addtodo">
																				<a href="index.php?module=Calendar4You&action=EventEditView&return_module={$MODULE}&return_action=DetailView&activity_mode=Task&return_id={$ID}&{$subst}={$ID}{$acc}&parenttab={$CATEGORY}" class="webMnu"><img src="{'AddToDo.gif'|@vtiger_imageurl:$THEME}" hspace="5" align="absmiddle" border="0"/></a>
																				<a href="index.php?module=Calendar4You&action=EventEditView&return_module={$MODULE}&return_action=DetailView&activity_mode=Task&return_id={$ID}&{$subst}={$ID}{$acc}&parenttab={$CATEGORY}" class="webMnu">{$APP.LBL_ADD_NEW} {$APP.Todo}</a>
																			</div>
																		{/if}
																		{if $MODULE eq 'Leads'} {if $CONVERTLEAD eq 'permitted'}
																			<div class="actionData actionlink_convertlead">
																				<a href="javascript:void(0);" class="webMnu" onclick="callConvertLeadDiv('{$ID}');"><img src="{'Leads.gif'|@vtiger_imageurl:$THEME}" hspace="5" align="absmiddle"  border="0"/></a>
																				<a href="javascript:void(0);" class="webMnu" onclick="callConvertLeadDiv('{$ID}');">{$APP.LBL_CONVERT_BUTTON_LABEL}</a>
																			</div>
																		{/if}
																	{/if}
																		{*<!-- Start: Actions for Documents Module -->*}
																		{if $MODULE eq 'Documents'}
																			<div class="actionData actionlink_downloaddocument">
																				{if $DLD_TYPE eq 'I' && $FILE_STATUS eq '1' && $FILE_EXIST eq 'yes'}
																					<a href="index.php?module=uploads&action=downloadfile&fileid={$FILEID}&entityid={$NOTESID}" onclick="javascript:dldCntIncrease({$NOTESID});" class="webMnu"><img src="{'fbDownload.gif'|@vtiger_imageurl:$THEME}" hspace="5" align="absmiddle" title="{$MOD.LNK_DOWNLOAD}" border="0"/></a>
																					<a href="index.php?module=uploads&action=downloadfile&fileid={$FILEID}&entityid={$NOTESID}" onclick="javascript:dldCntIncrease({$NOTESID});">{$MOD.LBL_DOWNLOAD_FILE}</a> {elseif $DLD_TYPE eq 'E' && $FILE_STATUS eq '1'}
																					<a target="_blank"  class="webMnu" href="{$DLD_PATH}" onclick="javascript:dldCntIncrease({$NOTESID});"><img src="{'fbDownload.gif'|@vtiger_imageurl:$THEME}" align="absmiddle" title="{$MOD.LNK_DOWNLOAD}" border="0"></a>
																					<a target="_blank" href="{$DLD_PATH}" onclick="javascript:dldCntIncrease({$NOTESID});">{$MOD.LBL_DOWNLOAD_FILE}</a>
																				{/if}
																			</div>

																			{if $CHECK_INTEGRITY_PERMISSION eq 'yes'}
																				<div class="actionData actionlink_checkdocinteg">
																					<a class="webMnu" href="javascript:;" onClick="checkFileIntegrityDetailView({$NOTESID});"><img id="CheckIntegrity_img_id" src="{'yes.gif'|@vtiger_imageurl:$THEME}" alt="Check integrity of this file" title="Check integrity of this file" hspace="5" align="absmiddle" border="0"/></a>
																					<a href="javascript:;" onClick="checkFileIntegrityDetailView({$NOTESID});">{$MOD.LBL_CHECK_INTEGRITY}</a>&nbsp;
																					<input type="hidden" id="dldfilename" name="dldfilename" value="{$FILEID}-{$FILENAME}">
																					<span id="vtbusy_integrity_info" style="display:none;"><img src="{'vtbusy.gif'|@vtiger_imageurl:$THEME}" border="0"></span>
																					<span id="integrity_result" style="display:none"></span>
																				</div>
																			{/if}


																			<div class="actionData actionlink_emaildocument">
																				{if $DLD_TYPE eq 'I' && $FILE_STATUS eq '1' && $FILE_EXIST eq 'yes'}
																					<input type="hidden" id="dldfilename" name="dldfilename" value="{$FILEID}-{$FILENAME}">
																						<a href="javascript: document.DetailView.return_module.value='Documents'; document.DetailView.return_action.value='DetailView'; document.DetailView.module.value='Documents'; document.DetailView.action.value='EmailFile'; document.DetailView.record.value={$NOTESID}; document.DetailView.return_id.value={$NOTESID}; sendfile_email();" class="webMnu"><img src="{'attachment.gif'|@vtiger_imageurl:$THEME}" hspace="5" align="absmiddle" border="0"/></a>
																						<a href="javascript: document.DetailView.return_module.value='Documents'; document.DetailView.return_action.value='DetailView'; document.DetailView.module.value='Documents'; document.DetailView.action.value='EmailFile'; document.DetailView.record.value={$NOTESID}; document.DetailView.return_id.value={$NOTESID}; sendfile_email();">{$MOD.LBL_EMAIL_FILE}</a> 
																				{/if}
																			</div>
																		{/if}
																	{/if}

																	{if $MODULE eq 'Contacts'}
																		{assign var=subst value="cto_id"}
																		{assign var=acc value="&rel_id=$accountid"}
																	{else}
																		{assign var=subst value="rel_id"}
																		{assign var=acc value=""}
																	{/if}


																	{* vtlib customization: Avoid line break if custom links are present *}
																	{if !isset($CUSTOM_LINKS) || empty($CUSTOM_LINKS)} <br> {/if}
																	{* vtlib customization: Custom links on the Detail view basic links *}
																	{if $CUSTOM_LINKS && $CUSTOM_LINKS.DETAILVIEWBASIC}
																		{foreach item=CUSTOMLINK from=$CUSTOM_LINKS.DETAILVIEWBASIC}
																			<div class="actionData actionlink_{$CUSTOMLINK->linklabel|lower|replace:' ':'_'}">
																				{assign var="customlink_href" value=$CUSTOMLINK->linkurl} {assign var="customlink_label" value=$CUSTOMLINK->linklabel} {if $customlink_label eq ''} {assign var="customlink_label" value=$customlink_href}
																				{else}
																					{* Pickup the translated label provided by the module *}
																					{assign var="customlink_label" value=$customlink_label|@getTranslatedString:$CUSTOMLINK->module()}
																				{/if}
																				{if $CUSTOMLINK->linkicon}
																					<a class="webMnu" href="{$customlink_href}"><img hspace=5 align="absmiddle" border=0 src="{$CUSTOMLINK->linkicon}"></a>
																				{else}
																					<a class="webMnu" href="{$customlink_href}"><img hspace=5 align="absmiddle" border=0 src="themes/images/no_icon.png"></a>
																				{/if}
																					<a class="webMnu" href="{$customlink_href}">{$customlink_label}</a>
																			</div>
																		{/foreach}
																	{/if}
																</div>
															</article>
														</div>

														{* vtlib customization: Custom links on the Detail view *}
														{if $CUSTOM_LINKS && $CUSTOM_LINKS.DETAILVIEW} <br> 
															{if !empty($CUSTOM_LINKS.DETAILVIEW)}
																<table width="100%" border="0" cellpadding="5" cellspacing="0">
																	<tr>
																		<td align="left" class="dvtUnSelectedCell dvtCellLabel">
																			<a href="javascript:;" onmouseover="fnvshobj(this,'vtlib_customLinksLay');" onclick="fnvshobj(this,'vtlib_customLinksLay');"><b>{$APP.LBL_MORE} {$APP.LBL_ACTIONS} &#187;</b></a>
																		</td>
																	</tr>
																</table>
																<br>
																<div style="display: none; left: 193px; top: 106px;width:155px; position:absolute;" id="vtlib_customLinksLay" onmouseout="fninvsh('vtlib_customLinksLay')" onmouseover="fnvshNrm('vtlib_customLinksLay')">
																	<table bgcolor="#ffffff" border="0" cellpadding="0" cellspacing="0" width="100%">
																		<tr>
																			<td style="border-bottom: 1px solid rgb(204, 204, 204); padding: 5px;"><b>{$APP.LBL_MORE} {$APP.LBL_ACTIONS} &#187;</b></td>
																		</tr>
																		<tr>
																			<td>
																				{foreach item=CUSTOMLINK from=$CUSTOM_LINKS.DETAILVIEW} {assign var="customlink_href" value=$CUSTOMLINK->linkurl} {assign var="customlink_label" value=$CUSTOMLINK->linklabel} {if $customlink_label eq ''} {assign var="customlink_label" value=$customlink_href} {else} {* Pickup the translated label provided by the module *} {assign var="customlink_label" value=$customlink_label|@getTranslatedString:$CUSTOMLINK->module()} {/if}
																					<a href="{$customlink_href}" class="drop_down">{$customlink_label}</a>
																				{/foreach}
																			</td>
																		</tr>
																	</table>
																</div>
															{/if}
														{/if}
														{* END *}
														<!-- Action links END -->
														{if $TAG_CLOUD_DISPLAY eq 'true'}
															<!-- Tag cloud display -->
															<br>
															<div class="flexipageComponent tagCloud">
																<article class="slds-card container MEDIUM forceBaseCard runtime_sales_mergeMergeCandidatesPreviewCard" aria-describedby="header">
																	<div class="slds-card__header slds-grid">
																		<header class="slds-media slds-media--center slds-has-flexi-truncate">
																			<div class="slds-media__body">
																				<img src="{$IMAGE_PATH}tagCloudName.gif" border=0>
																			</div>
																		</header>
																	</div>
																	<div class="slds-card__body slds-card__body--inner">
																		<div id="tagdiv" style="display:visible;">
																			<form method="POST" action="javascript:void(0);" onsubmit="return tagvalidate();">
																				<input class="textbox slds-input" type="text" id="txtbox_tagfields" name="textbox_First Name" value="" style="width:150px;margin-left:5px;"></input>&nbsp;&nbsp;
																				<input name="button_tagfileds" type="submit" class="slds-button slds-button_success slds-button--small" value="{$APP.LBL_TAG_IT}" />
																			</form>
																		</div>
																		<div class="tagCloudDisplay actionData">
																			<span id="tagfields"></span>
																		</div>
																	</div>
																</article>
															</div>
															<!-- End Tag cloud display -->
														{/if}
														<!-- Mail Merge-->
														<br>
														{if isset($MERGEBUTTON) && $MERGEBUTTON eq 'permitted'}
															<form action="index.php" method="post" name="TemplateMerge" id="form">
																<input type="hidden" name="module" value="{$MODULE}">
																<input type="hidden" name="parenttab" value="{$CATEGORY}">
																<input type="hidden" name="record" value="{$ID}">
																<input type="hidden" name="action">

																<div class="flexipageComponent">
																	<article class="slds-card container MEDIUM forceBaseCard runtime_sales_mergeMergeCandidatesPreviewCard" aria-describedby="header">
																		<div class="slds-card__header slds-grid">
																			<header class="slds-media slds-media--center slds-has-flexi-truncate">
																				<div class="slds-media__body">
																					<h4 class="header-title-container">
																						<span class="slds-text-heading--small slds-truncate actionLabel"><b>{$WORDTEMPLATEOPTIONS}</b></span>
																					</h4>
																				</div>
																			</header>
																		</div>
																		<div class="slds-card__body slds-card__body--inner">
																			<div class="actionData">
																				{if $TEMPLATECOUNT neq 0}
																					<select name="mergefile" class="slds-select">
																						{foreach key=templid item=tempflname from=$TOPTIONS}
																							<option value="{$templid}">{$tempflname}</option>
																						{/foreach}
																					</select>
																					<input class="slds-button slds-button--small slds-button_success" value="{$APP.LBL_MERGE_BUTTON_LABEL}" onclick="this.form.action.value='Merge';" type="submit"></input>
																				{else}
																					<a href=index.php?module=Settings&action=upload&tempModule={$MODULE}&parenttab=Settings>{$APP.LBL_CREATE_MERGE_TEMPLATE}</a> 
																				{/if}
																			</div>
																		</div>
																	</article>
																</div>
															</form>
														{/if}

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
														 			<div class="flexipageComponent" id="{$CUSTOMLINK->linklabel}">
																		<article class="slds-card container MEDIUM forceBaseCard runtime_sales_mergeMergeCandidatesPreviewCard" aria-describedby="header">
																			<div class="slds-card__header slds-grid">
																				<header class="slds-media slds-media--center slds-has-flexi-truncate">
																					<div class="slds-media__body">
																						<b>{$customlink_label}</b>
													 									<img id="detailview_block_{$CUSTOMLINK_NO}_indicator" style="display:none;" src="{'vtbusy.gif'|@vtiger_imageurl:$THEME}" border="0" align="absmiddle" />
																					</div>
																				</header>
																			</div>
																			<div class="slds-card__body slds-card__body--inner">
																				<div id="detailview_block_{$CUSTOMLINK_NO}"></div>
																				<script type="text/javascript">
																				vtlib_loadDetailViewWidget("{$customlink_href}", "detailview_block_{$CUSTOMLINK_NO}", "detailview_block_{$CUSTOMLINK_NO}_indicator");
																				</script>
																			</div>
																		</article>
																	</div>
																{/if}
															{/foreach}
														{/if}
													</td>
												</tr>
											</table>
										</div>
									</td>
								</tr>
							</table>
						</div>
						<!-- PUBLIC CONTENTS STOPS-->
						<script>
						function getTagCloud()
						{ldelim}
							var obj = document.getElementById("tagfields");
							if(obj != null && typeof(obj) != undefined) {ldelim}
								jQuery.ajax({ldelim}
										method:"POST",
										url:'index.php?module={$MODULE}&action={$MODULE}Ajax&file=TagCloud&ajxaction=GETTAGCLOUD&recordid={$ID}',
								{rdelim}).done(function(response) {ldelim}
											jQuery("#tagfields").html(response);
											jQuery("#txtbox_tagfields").val('');
								{rdelim}
								);
							{rdelim}
						{rdelim}
						getTagCloud();
						</script>
						<!-- added for validation -->
						<script>
							var fieldname = new Array({$VALIDATION_DATA_FIELDNAME});
							var fieldlabel = new Array({$VALIDATION_DATA_FIELDLABEL});
							var fielddatatype = new Array({$VALIDATION_DATA_FIELDDATATYPE});
						</script>
					</td>
				</tr>
			</table>
{if $MODULE|hasEmailField}
	<form name="SendMail">
		<div id="sendmail_cont" style="z-index:100001;position:absolute;"></div>
	</form>
{/if}
