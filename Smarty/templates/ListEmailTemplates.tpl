{*<!--
/*********************************************************************************
  ** The contents of this file are subject to the vtiger CRM Public License Version 1.0
   * ("License"); You may not use this file except in compliance with the License
   * The Original Code is:  vtiger CRM Open Source
   * The Initial Developer of the Original Code is vtiger.
   * Portions created by vtiger are Copyright (C) vtiger.
   * All Rights Reserved.
  *
 ********************************************************************************/
-->*}

<script type="text/javascript" src="include/js/smoothscroll.js"></script>
<script>
function ifselected()
{ldelim}
  
	  
	  var sel =document.massdelete.selected_id.length;
	  var returnval=false;
	  
	 for(i=0; i < sel; i++)
	 {ldelim}
	 
	  if(document.massdelete.selected_id[i].checked == true)
		{ldelim}
			returnval=true;
			break;
		{rdelim}
		
	  {rdelim}
	  
	  
		  if(returnval==true)
		   {ldelim}
			   document.getElementById("myProfile").style.display="none";
		   {rdelim}
		  else
		   {ldelim}
			  document.getElementById("myProfile").style.display="block";
		  {rdelim}
		
{rdelim}


function massDelete()
{ldelim}
		
		x = document.massdelete.selected_id.length;
		idstring = "";

		if ( x == undefined)
		{ldelim}

				if (document.massdelete.selected_id.checked)
			   {ldelim}
						document.massdelete.idlist.value=document.massdelete.selected_id.value+';';
						  
						
			xx=1;
				{rdelim}
				else
				{ldelim}
						 document.massdelete.profile.style.display="none";
						alert("{$APP.SELECT_ATLEAST_ONE}");
						return false;
				{rdelim}
		{rdelim}
		else
		{ldelim}
				xx = 0;
				for(i = 0; i < x ; i++)
				{ldelim}
						if(document.massdelete.selected_id[i].checked)
						{ldelim}
								idstring = document.massdelete.selected_id[i].value +";"+idstring
						xx++
						{rdelim}
				{rdelim}
				if (xx != 0)
				{ldelim}
						document.massdelete.idlist.value=idstring;
				{rdelim}
			   else
				{ldelim}
						alert("{$APP.SELECT_ATLEAST_ONE}");
						return false;
				{rdelim}
	   {rdelim}
		if(confirm("{$APP.DELETE_CONFIRMATION}"+xx+"{$APP.RECORDS}"))
		{ldelim}
			document.massdelete.action="index.php?module=Settings&action=deleteemailtemplate&return_module=Settings&return_action=listemailtemplates";
		{rdelim}
		else
		{ldelim}
			return false;
		{rdelim}

{rdelim}
</script>
<br>
<table align="center" border="0" cellpadding="0" cellspacing="0" width="98%">
	<tbody>
		<tr>
			<td valign="top" width="100%">
				<div align=center>
					<br>
					{include file='SetMenu.tpl'}
						<!-- DISPLAY Settings Email Template-->
							<form name="massdelete" method="POST" onsubmit="VtigerJS_DialogBox.block();">
								<input name="idlist" type="hidden">
								<input name="module" type="hidden" value="Settings">
								<input name="action" type="hidden" value="deleteemailtemplate">


								<table class="slds-table slds-no-row-hover slds-table--cell-buffer slds-table-moz" style="background-color: #f7f9fb;">
									<tr class="slds-text-title--caps">
										<td style="padding: 0;">
											<div class="slds-page-header s1FixedFullWidth s1FixedTop forceHighlightsStencilSettings" style="height: 70px;">
												<div class="slds-grid primaryFieldRow" style="transform: translate3d(0, -8.65823px, 0);">
													<div class="slds-grid slds-col slds-has-flexi-truncate slds-media--center">
														<!-- Image -->
														<div class="slds-media slds-no-space" style="transform: scale3d(0.864715, 0.864715, 1) translate3d(4.32911px, 2.16456px, 0);">
															<div class="slds-media__figure slds-icon forceEntityIcon">
																<span class="photoContainer forceSocialPhoto">
																	<div class="small roundedSquare forceEntityIcon">
																		<span class="uiImage">
																			<img src="{'ViewTemplate.gif'|@vtiger_imageurl:$THEME}"/>
																		</span>
																	</div>
																</span>
															</div>
														</div>
														<!-- Title and help text -->
														<div class="slds-media__body">
															<h1 class="slds-page-header__title slds-m-right--small slds-truncate slds-align-middle">
																<span class="uiOutputText">
																	<b><a href="index.php?module=Settings&action=index&parenttab=Settings">{'LBL_SETTINGS'|@getTranslatedString}</a> > {$UMOD.LBL_EMAIL_TEMPLATES} </b>
																</span>
																<span class="small">{$MOD.LBL_EMAIL_TEMPLATE_DESC}</span>
															</h1>
														</div>
													</div>
												</div>
											</div>
										</td>
									</tr>
								</table>

								<table border=0 cellspacing=0 cellpadding=10 width=100% >
									<tr>
										<td>

										<table border=0 cellspacing=0 cellpadding=5 width=100% class="tableHeading">
											<tr>
												<td class="big">
													<div class="forceRelatedListSingleContainer">
														<article class="slds-card forceRelatedListCardDesktop" aria-describedby="header">
															<div class="slds-card__header slds-grid">
																<header class="slds-media slds-media--center slds-has-flexi-truncate">
																	<div class="slds-media__body">
																		<h2>
																			<span class="slds-text-title--caps slds-truncate slds-m-right--xx-small actionLabel">
																				<strong>{$UMOD.LBL_EMAIL_TEMPLATES}</strong>
																			</span>
																		</h2>
																	</div>
																</header>
																<div class="slds-no-flex" style="display: inherit;">
																	<input type="submit" value="{$UMOD.LBL_DELETE}" onclick="return massDelete();" class="slds-button slds-button--small slds-button--destructive">
																	&nbsp;
																	<div id="new_template">
																		<div id="myProfile">
																			<input class="slds-button slds-button--small slds-button_success classBtn" type="submit" value="{$UMOD.LBL_NEW_TEMPLATE}" name="profile" onclick="this.form.action.value='createemailtemplate';"/>
																		</div>
																	</div>
																</div>
															</div>
														</article>
													</div>

													<div class="slds-truncate">
														<table class="slds-table slds-table--bordered  slds-table--cell-buffer listTable">
														<thead>
															<tr>
																<th class="slds-text-title--caps" scope="col">
																	<span class="slds-truncate" style="padding: .5rem 0;">
																		#
																	</span>
																</th>
																<th class="slds-text-title--caps" scope="col">
																	<span class="slds-truncate" style="padding: .5rem 0;">
																		{$UMOD.LBL_LIST_SELECT}
																	</span>
																</th>
																<th class="slds-text-title--caps" scope="col">
																	<span class="slds-truncate" style="padding: .5rem 0;">
																		{$UMOD.LBL_EMAIL_TEMPLATE}
																	</span>
																</th>
																<th class="slds-text-title--caps" scope="col">
																	<span class="slds-truncate" style="padding: .5rem 0;">
																		{$UMOD.LBL_DESCRIPTION}
																	</span>
																</th>
															</tr>
															</thead>
															<tbody>
																{foreach name=emailtemplate item=template from=$TEMPLATES}
																	<tr class="slds-hint-parent slds-line-height--reset">
																		<th scope="row"><div class="slds-truncate">{$smarty.foreach.emailtemplate.iteration}</div></th>
																		<th scope="row">
																			<div class="slds-truncate">
																				<span class="slds-checkbox">
																					<input type="checkbox" name="selected_id" id="{$template.templateid}" value="{$template.templateid}" onClick="ifselected(); ">
																					<label class="slds-checkbox__label" for="{$template.templateid}">
																						<span class="slds-checkbox--faux"></span>
																					</label>
																				</span>
																			</div>
																		</th>
																		<th scope="row">
																			<div class="slds-truncate">
																				<a href="index.php?module=Settings&action=detailviewemailtemplate&parenttab=Settings&templateid={$template.templateid}" >
																					<b>{$template.templatename}</b>
																				</a>
																			</div>
																		</th>
																		<th scope="row"><div class="slds-truncate">{$template.description}</div></th>
																	</tr>
																{/foreach}
															</tbody>
														</table>
													</div>

												</td>
											</tr>
										</table>

										<table border=0 cellspacing=0 cellpadding=5 width=100% >
											<tr>
												<td class="small" nowrap align=right><a href="#top">{$MOD.LBL_SCROLL}</a></td>
											</tr>
										</table>

										</td>
									</tr>
								</table>
							</form>

					</td></tr></table><!-- close tables from setMenu -->
					</td></tr></table><!-- close tables from setMenu -->

				</div>
			</td>
		</tr>
	</tbody>
</table>