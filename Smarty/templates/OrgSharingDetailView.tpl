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
<script type="text/javascript" src="include/js/smoothscroll.js"></script>
{literal}
<style>DIV.fixedLay{border:3px solid #CCCCCC;background-color:#FFFFFF;width:500px;position:fixed;left:250px;top:98px;display:block;}</style>
{/literal}
{literal}
<!--[if lte IE 6]><STYLE type=text/css>DIV.fixedLay {POSITION: absolute;}</STYLE><![endif]-->
{/literal}
<br>
<table align="center" border="0" cellpadding="0" cellspacing="0" width="98%">
	<tbody>
		<tr>
			<td valign="top" width="100%">
				<div align=center>
					<br>
						{include file='SetMenu.tpl'}
									<!-- DISPLAY Sharing Access Settings-->
														<table class="slds-table slds-no-row-hover slds-table--cell-buffer slds-table-moz" style="background-color: #f7f9fb;">
															<tr class="slds-text-title--caps">
																<td style="padding: 0;">
																	<div class="slds-page-header s1FixedFullWidth s1FixedTop forceHighlightsStencilSettings" style="height: 70px;">
																		<div class="slds-grid primaryFieldRow" style="transform: translate3d(0, -8.65823px, 0);">
																			<div class="slds-grid slds-col slds-has-flexi-truncate slds-media--center">
																				<div class="slds-media slds-no-space" style="transform: scale3d(0.864715, 0.864715, 1) translate3d(4.32911px, 2.16456px, 0);">
																					<div class="slds-media__figure slds-icon forceEntityIcon">
																						<span class="photoContainer forceSocialPhoto">
																							<div class="small roundedSquare forceEntityIcon">
																								<span class="uiImage">
																									<img src="{'shareaccess.gif'|@vtiger_imageurl:$THEME}" alt="{$MOD.LBL_USERS}" width="48" height="48" border=0 title="{$MOD.LBL_USERS}">
																								</span>
																							</div>
																						</span>
																					</div>
																				</div>
																				<div class="slds-media__body">
																					<h1 class="slds-page-header__title slds-m-right--small slds-truncate slds-align-middle">
																						<span class="uiOutputText">
																							<b><a href="index.php?module=Settings&action=index&parenttab=Settings">{'LBL_SETTINGS'|@getTranslatedString}</a> > {$MOD.LBL_SHARING_ACCESS} </b>
																						</span>
																						<span class="small">{$MOD.LBL_SHARING_ACCESS_DESCRIPTION}</span>
																					</h1>
																				</div>
																			</div>
																		</div>
																	</div>
																</td>
															</tr>
														</table>

														<div class='helpmessagebox' style="margin: .5rem 0;">
															<b class="text-red">{$APP.NOTE}: </b> {$MOD.LBL_SHARING_ACCESS_HELPNOTE}
														</div>

														<!-- GLOBAL ACCESS MODULE -->
														<div id="globaldiv">

															<table class="slds-table  slds-no-row-hover">
																<form action="index.php" method="post" name="new" id="orgSharingform" onsubmit="VtigerJS_DialogBox.block();">
																	<input type="hidden" name="module" value="Users">
																	<input type="hidden" name="action" value="OrgSharingEditView">
																	<input type="hidden" name="parenttab" value="Settings">
																	<tr>
																		<td>
																			<div class="forceRelatedListSingleContainer">
																				<article class="slds-card forceRelatedListCardDesktop" aria-describedby="header">
																					<div class="slds-card__header slds-grid">
																						<header class="slds-media slds-media--center slds-has-flexi-truncate">
																							<div class="slds-media__body">
																								<h2>
																									<span class="slds-text-title--caps slds-truncate slds-m-right--xx-small actionLabel">
																										<strong>1. {$CMOD.LBL_GLOBAL_ACCESS_PRIVILEGES}</strong>
																									</span>
																								</h2>
																							</div>
																						</header>
																						<div class="slds-no-flex">
																							<div class="actionsContainer">
																								<input class="slds-button slds-button--small slds-button_success" title="{$CMOD.LBL_RECALCULATE_BUTTON}"  type="button" name="recalculate" value="{$CMOD.LBL_RECALCULATE_BUTTON}" onclick="return freezeBackground();">
																								&nbsp;
																								<input class="slds-button slds-button--small slds-button--brand" type="submit" name="Edit" value="{$CMOD.LBL_CHANGE} {$CMOD.LBL_PRIVILEGES}" >
																							</div>
																						</div>
																					</div>
																				</article>
																			</div>
																			<br/>
																			<table class="slds-table slds-table--bordered ">
																				{foreach item=module from=$DEFAULT_SHARING}
																					{assign var="MODULELABEL" value=$module.0|getTranslatedString:$module.0}
																					<tr class="slds-hint-parent slds-line-height--reset">
																						<th scope="row">
																							<div class="slds-truncate">
																								{$MODULELABEL}
																							</div>
																						</th>
																						<th scope="row">
																							<div class="slds-truncate">
																								{if $module.1 neq 'Private' && $module.1 neq 'Hide Details'}
																									<img src="{'public.gif'|@vtiger_imageurl:$THEME}" align="absmiddle">
																								{else}
																									<img src="{'private.gif'|@vtiger_imageurl:$THEME}" align="absmiddle">
																								{/if}
																								{$CMOD[$module.1]}
																							</div>
																						</th>
																						<th scope="row">
																							<div class="slds-truncate">
																								{$module.2}
																							</div>
																						</th>
																					</tr>
																				{/foreach}
																			</table>
																		</td>
																	</tr>
																</form>
															</table>
														</div>
														<!-- END OF GLOBAL -->

														<br><br>

														<!-- Custom Access Module Display Table -->
														<div id="customdiv">

															<div class="forceRelatedListSingleContainer">
																<article class="slds-card forceRelatedListCardDesktop" aria-describedby="header">
																	<div class="slds-card__header slds-grid">
																		<header class="slds-media slds-media--center slds-has-flexi-truncate">
																			<div class="slds-media__body">
																				<h2>
																					<span class="slds-text-title--caps slds-truncate slds-m-right--xx-small actionLabel">
																						<strong>2. {$CMOD.LBL_CUSTOM_ACCESS_PRIVILEGES}</strong>
																					</span>
																				</h2>
																			</div>
																		</header>
																	</div>
																</article>
															</div>

															<!-- Start of Module Display -->
															{foreach  key=modulename item=details from=$MODSHARING}
																{assign var="MODULELABEL" value=$modulename|@getTranslatedString:$modulename}
																{assign var="mod_display" value=$MODULELABEL}
																{if $mod_display eq $APP.Accounts}
																	{assign var="xx" value=$APP.Contacts}
																	{assign var="mod_display" value=$mod_display|cat:" & $xx"}
																{/if}

																{if !empty($details.0)}

																	<table width="100%" border="0" cellpadding="5" cellspacing="0" class="sharing-rules-img">
																		<tr>
																			<td>
																				<div class="forceRelatedListSingleContainer">
																					<article class="slds-card forceRelatedListCardDesktop" aria-describedby="header">
																						<div class="slds-card__header slds-grid">
																							<header class="slds-media slds-media--center slds-has-flexi-truncate">
																								<div class="slds-media__figure">
																									<div class="extraSmall forceEntityIcon" data-aura-rendered-by="3:1782;a" data-aura-class="forceEntityIcon">
																										<span data-aura-rendered-by="6:1782;a" class="uiImage" data-aura-class="uiImage">
																											<img src="{'arrow.jpg'|@vtiger_imageurl:$THEME}" />
																										</span>
																									</div>
																								</div>
																								<div class="slds-media__body">
																									<h2>
																										<span class="slds-text-title--caps slds-truncate slds-m-right--xx-small actionLabel">
																											<strong>{$mod_display}</strong>
																										</span>
																									</h2>
																								</div>
																							</header>
																							<div class="slds-no-flex">
																								<div class="actionsContainer">
																									<input class="slds-button slds-button--small slds-button_success" type="button" name="Create" value="{$CMOD.LBL_ADD_PRIVILEGES_BUTTON}" onClick="callEditDiv(this,'{$modulename}','create','')">
																								</div>
																							</div>
																						</div>
																					</article>
																				</div>
																				<table class="slds-table slds-table--bordered slds-table--fixed-layout">
																					<thead>
																						<tr>
																							<td role="gridcell" class="slds-text-align--center" style="width: 4rem;" >{$CMOD.LBL_RULE_NO}</td>
																							<th class="slds-text-title--caps" scope="col">
																								<a href="index.php?action=OrgSharingDetailView&module=Settings&sortrulesby=1">{$mod_display} {$CMOD.LBL_OF}</a>
																								</span>
																							</th>
																							<th class="slds-text-title--caps" scope="col">
																								<span class="slds-truncate" style="padding: .5rem 0;">
																									<a href="index.php?action=OrgSharingDetailView&module=Settings&sortrulesby=2">{$CMOD.LBL_CAN_BE_ACCESSED}</a>
																								</span>
																							</th>
																							<th class="slds-text-title--caps" scope="col">
																								<span class="slds-truncate" style="padding: .5rem 0;">{$CMOD.LBL_PRIVILEGES}</span>
																							</th>
																							<th class="slds-text-title--caps" scope="col">
																								<span class="slds-truncate" style="padding: .5rem 0;">{$APP.Tools}</span>
																							</th>
																						</tr>
																					</thead>
																					<tbody>
																						<tr class="slds-hint-parent slds-line-height--reset">
																							{foreach key=sno item=elements from=$details}
																								<td role="gridcell" class="slds-text-align--center">{$sno+1}</td>
																								<th scope="row"><div class="slds-truncate">{$elements.1}</div></th>
																								<th scope="row"><div class="slds-truncate">{$elements.2}</div></th>
																								<th scope="row"><div class="slds-truncate">{$elements.3}</div></th>
																								<th scope="row">
																									<div class="slds-truncate">
																										<a href="javascript:void(0);" onClick="callEditDiv(this,'{$modulename}','edit','{$elements.0}')">
																											<img src="{'editfield.gif'|@vtiger_imageurl:$THEME}" title='edit' align="absmiddle" border=0 style="padding-top:3px;">
																										</a>
																										&nbsp;|
																										<a href='javascript:confirmdelete("index.php?module=Users&action=DeleteSharingRule&shareid={$elements.0}")'>
																											<img src="{'delete.gif'|@vtiger_imageurl:$THEME}" title='del' align="absmiddle" border=0>
																										</a>
																									</div>
																								</th>
																							{/foreach}
																						</tr>
																					</tbody>
																				</table>
																				<!-- End of Module Display -->
																			</td>
																		</tr>
																	</table>

																	<!-- Start FOR NO DATA -->
																	<!-- <table border=0 cellspacing=0 cellpadding=5 width=100% class="tableHeading"><tr><td>&nbsp;</td></tr></table> -->
																	<br/>
																	<div class="forceRelatedListSingleContainer">
																		<article class="slds-card forceRelatedListCardDesktop" aria-describedby="header">
																			<div class="slds-card__header slds-grid">
																				<header class="slds-media slds-media--center slds-has-flexi-truncate">
																					<div class="slds-media__body">
																						<h2>
																							<span class="slds-text-title--caps slds-truncate slds-m-right--xx-small actionLabel">
																								&nbsp;
																							</span>
																						</h2>
																					</div>
																				</header>
																			</div>
																		</article>
																	</div>
																{else}
																	<table width="100%" cellpadding="0" cellspacing="0" >
																		<tr>
																			<td>

																				<table width="100%" border="0" cellpadding="5" cellspacing="0" class="sharing-rules-img">

																					<tr>
																						<td style="padding-left:5px;" class="big">

																							<div class="forceRelatedListSingleContainer">
																								<article class="slds-card forceRelatedListCardDesktop" aria-describedby="header">
																									<div class="slds-card__header slds-grid">
																										<header class="slds-media slds-media--center slds-has-flexi-truncate">
																											<div class="slds-media__figure">
																												<div class="extraSmall forceEntityIcon" data-aura-rendered-by="3:1782;a" data-aura-class="forceEntityIcon">
																													<span data-aura-rendered-by="6:1782;a" class="uiImage" data-aura-class="uiImage">
																														<img src="{'arrow.jpg'|@vtiger_imageurl:$THEME}" />
																													</span>
																												</div>
																											</div>
																											<div class="slds-media__body">
																												<h2>
																													<span class="slds-text-title--caps slds-truncate slds-m-right--xx-small actionLabel">
																														<strong>{$mod_display}</strong>
																													</span>
																												</h2>
																											</div>
																										</header>
																										<div class="slds-no-flex">
																											<div class="actionsContainer">
																												<input class="slds-button slds-button--small slds-button_success" type="button" name="Create" value="{$APP.LBL_ADD_ITEM} {$CMOD.LBL_PRIVILEGES}" onClick="callEditDiv(this,'{$modulename}','create','')">
																											</div>
																										</div>
																									</div>
																								</article>
																							</div>

																						</td>
																					</tr>

																					<table width="100%" cellpadding="5" cellspacing="0">
																						<tr>
																							<td colspan="2"  style="padding:20px ;" align="center" class="small">
																								{$CMOD.LBL_CUSTOM_ACCESS_MESG}
																								<a href="javascript:void(0);" onClick="callEditDiv(this,'{$modulename}','create','')">{$CMOD.LNK_CLICK_HERE}</a>
																								{$CMOD.LBL_CREATE_RULE_MESG}
																							</td>
																						</tr>
																					</table>

																				</table>

																				<!-- <table border=0 cellspacing=0 cellpadding=5 width=100% class="tableHeading"><tr><td>&nbsp;</td></tr></table> -->
																				<div class="forceRelatedListSingleContainer">
																					<article class="slds-card forceRelatedListCardDesktop" aria-describedby="header">
																						<div class="slds-card__header slds-grid">
																							<header class="slds-media slds-media--center slds-has-flexi-truncate">
																								<div class="slds-media__body">
																									<h2>
																										<span class="slds-text-title--caps slds-truncate slds-m-right--xx-small actionLabel">
																											&nbsp;
																										</span>
																									</h2>
																								</div>
																							</header>
																						</div>
																					</article>
																				</div>
																{/if}
															{/foreach}
																			</td>
																		</tr>
																	</table>
															<br>
														</div>

														<!-- Edit Button -->
														<table border=0 cellspacing=0 cellpadding=5 width=100% >
															<tr>
																<td class="small" >
																	<div align=right><a href="#top">{$MOD.LBL_SCROLL}</a></div>
																</td>
															</tr>
														</table>

													</td>
												</tr>
											</table>
										</td>
									</tr>
								</table>
							</td>
						</tr>
					</table>
				</div><!-- / -->
			</td>
		</tr>
	</tbody>
</table>
<div id="tempdiv" style="display:block;position:absolute;width:500px;"></div>

<!-- For Disabling Window -->
<div id="confId"  class='veil_new small' style="display:none;">
<table class="options small" border="0" cellpadding="18" cellspacing="0">
<tr>
	<td align="center" nowrap style="color:#FFFFFF;font-size:15px;">
		<b>{$CMOD.LBL_RECALC_MSG}</b>
	</td>
	<br>
	<tr>
		<td align="center"><input type="button" value="{$CMOD.LBL_YES}" onclick="return disableStyle('confId');">&nbsp;&nbsp;<input type="button" value="&nbsp;{$CMOD.LBL_NO}&nbsp;" onclick="showSelect();document.getElementById('confId').style.display='none';document.body.removeChild(document.getElementById('freeze'));"></td>
	</tr>
</tr>
</table>
</div>

<div id="divId" class="veil_new" style="position:absolute;width:100%;display:none;top:0px;left:0px;">
<table border="5" cellpadding="0" cellspacing="0" align="center" style="vertical-align:middle;width:100%;height:100%;">
<tbody><tr>
	<td class="big" align="center">
		<img src="{'plsWaitAnimated.gif'|@vtiger_imageurl:$THEME}">
	</td>
</tr></tbody>
</table>
</div>

<script>
function callEditDiv(obj,modulename,mode,id)
	{ldelim}
		document.getElementById("status").style.display="inline";
		jQuery.ajax({ldelim}
			method: 'POST',
			url: 'index.php?module=Settings&action=SettingsAjax&orgajax=true&mode='+mode+'&sharing_module='+modulename+'&shareid='+id,
		{rdelim}).done(function (response) {ldelim}
			document.getElementById("status").style.display="none";
			document.getElementById("tempdiv").innerHTML=response;
			fnvshobj(obj,"tempdiv");
			if(mode == 'edit')
			{ldelim}
				setTimeout("",10000);
				var related = document.getElementById('rel_module_lists').value;
				fnwriteRules(modulename,related);
			{rdelim}
		{rdelim});
	{rdelim}

function fnwriteRules(module,related)
{ldelim}
		var modulelists = new Array();
		modulelists = related.split('###');
		var relatedstring ='';
		var relatedtag;
		var relatedselect;
		var modulename;
		for(i=0;i < modulelists.length-1;i++)
		{ldelim}
			modulename = modulelists[i]+"_accessopt";
			relatedtag = document.getElementById(modulename);
			relatedselect = relatedtag.options[relatedtag.selectedIndex].text;
			relatedstring += modulelists[i]+':'+relatedselect+' ';
		{rdelim}
		var tagName = document.getElementById(module+"_share");
		var tagName2 = document.getElementById(module+"_access");
		var tagName3 = document.getElementById('share_memberType');
		var soucre =  document.getElementById("rules");
		var soucre1 =  document.getElementById("relrules");
		var select1 = tagName.options[tagName.selectedIndex].text;
		var select2 = tagName2.options[tagName2.selectedIndex].text;
		var select3 = tagName3.options[tagName3.selectedIndex].text;

		if(module == '{$APP.Accounts}')
		{ldelim}
			module = '{$APP.Accounts} & {$APP.Contacts}';
		{rdelim}

		soucre.innerHTML = module +" {$APP.LBL_LIST_OF} <b>\"" + select1 + "\"</b> {$CMOD.LBL_CAN_BE_ACCESSED} <b>\"" +select2 + "\"</b> {$CMOD.LBL_IN_PERMISSION} "+select3;
		soucre1.innerHTML = "<b>{$CMOD.LBL_RELATED_MODULE_RIGHTS}</b> " + relatedstring;
{rdelim}

		function confirmdelete(url)
		{ldelim}
			if(confirm("{$APP.ARE_YOU_SURE}"))
			{ldelim}
				document.location.href=url;
			{rdelim}
		{rdelim}

	function disableStyle(id)
	{ldelim}
			document.getElementById('orgSharingform').action.value = 'RecalculateSharingRules';
			document.getElementById('orgSharingform').submit();
			document.getElementById(id).style.display = 'none';

			if(browser_ie && (gBrowserAgent.indexOf("msie 7.")!=-1))//for IE 7
                        {ldelim}
                                document.body.removeChild(document.getElementById('freeze'));
                        {rdelim}else if(browser_ie)
                        {ldelim}
                             var oDivfreeze = document.getElementById('divId');
                             oDivfreeze.style.height = document.documentElement['clientHeight'] + 'px';

                        {rdelim}
                        document.getElementById('divId').style.display = 'block';
	{rdelim}

	function freezeBackground()
	{ldelim}
	    var oFreezeLayer = document.createElement("DIV");
	    oFreezeLayer.id = "freeze";
	    oFreezeLayer.className = "small veil";

	     if (browser_ie) oFreezeLayer.style.height = (document.body.offsetHeight + (document.body.scrollHeight - document.body.offsetHeight)) + "px";
	     else if (browser_nn4 || browser_nn6) oFreezeLayer.style.height = document.body.offsetHeight + "px";

	    oFreezeLayer.style.width = "100%";
	    document.body.appendChild(oFreezeLayer);
	    document.getElementById('confId').style.display = 'block';
	    hideSelect();
	{rdelim}

</script>
