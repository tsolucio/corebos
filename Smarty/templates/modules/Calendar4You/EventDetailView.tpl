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
<link rel="stylesheet" type="text/css" media="all" href="jscalendar/calendar-win2k-cold-1.css">
<script type="text/javascript" src="jscalendar/calendar.js"></script>
<script type="text/javascript" src="jscalendar/lang/calendar-en.js"></script>
<script type="text/javascript" src="jscalendar/calendar-setup.js"></script>
<script type="text/javascript" src="include/js/dtlviewajax.js"></script>
<span id="crmspanid" style="display:none;position:absolute;"  onmouseover="show('crmspanid');">
	<a class="link"  align="right" href="javascript:;">{$APP.LBL_EDIT_BUTTON}</a>
</span>

<div id="convertleaddiv" style="display:block;position:absolute;left:225px;top:150px;"></div>
<script>
{literal}
function callConvertLeadDiv(id){
		jQuery.ajax({
			method:"POST",
			url:'index.php?module=Leads&action=LeadsAjax&file=ConvertLead&record='+id
		}).done(function(response) {
			document.getElementById("convertleaddiv").innerHTML=response;
			eval(document.getElementById("conv_leadcal").innerHTML);
		});
}
function showHideStatus(sId,anchorImgId,sImagePath)
{
	oObj = document.getElementById(sId);
	if(oObj.style.display == 'block')
	{
		oObj.style.display = 'none';
		if(anchorImgId !=null){
{/literal}
			document.getElementById(anchorImgId).src = 'themes/images/inactivate.gif';
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
			document.getElementById(anchorImgId).src = 'themes/images/activate.gif';
			document.getElementById(anchorImgId).alt = '{'LBL_Hide'|@getTranslatedString:'Settings'}';
			document.getElementById(anchorImgId).title = '{'LBL_Hide'|@getTranslatedString:'Settings'}';
			document.getElementById(anchorImgId).parentElement.className = 'exp_coll_block inactivate';
{literal}
		}
	}
}
<!-- End Of Code modified by SAKTI on 10th Apr, 2008 -->

<!-- Start of code added by SAKTI on 16th Jun, 2008 -->
function setCoOrdinate(elemId){
	oBtnObj = document.getElementById(elemId);
	var tagName = document.getElementById('lstRecordLayout');
	leftpos  = 0;
	toppos = 0;
	aTag = oBtnObj;
	do{
	  leftpos  += aTag.offsetLeft;
	  toppos += aTag.offsetTop;
	} while(aTag = aTag.offsetParent);

	tagName.style.top= toppos + 20 + 'px';
	tagName.style.left= leftpos - 276 + 'px';
}

function getListOfRecords(obj, sModule, iId,sParentTab){
		jQuery.ajax({
			method:"POST",
			url:'index.php?module=Users&action=getListOfRecords&ajax=true&CurModule='+sModule+'&CurRecordId='+iId+'&CurParentTab='+sParentTab
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
					tagName.style.left = leftSide + 230 + 'px';
				}
				setCoOrdinate(obj.id);

				tagName.style.display = 'block';
				tagName.style.visibility = "visible";
		});
}
{/literal}
function tagvalidate(){ldelim}
	if(trim(document.getElementById('txtbox_tagfields').value) != '')
		SaveTag('txtbox_tagfields','{$ID}','{$MODULE}');
	else {ldelim}
		alert("{$APP.PLEASE_ENTER_TAG}");
		return false;
	{rdelim}
{rdelim}
function DeleteTag(id,recordid) {ldelim}
	document.getElementById("vtbusy_info").style.display="inline";
	jQuery('#tag_'+id).fadeOut();
	jQuery.ajax({ldelim}
			method:"POST",
			url:"index.php?file=TagCloud&module={$MODULE}&action={$MODULE}Ajax&ajxaction=DELETETAG&recordid="+recordid+"&tagid=" +id
	{rdelim}).done(function(response) {ldelim}
			getTagCloud();
			document.getElementById("vtbusy_info").style.display="none";
	{rdelim}
	);
{rdelim}

//Added to send a file, in Documents module, as an attachment in an email
function sendfile_email() {ldelim}
	filename = document.getElementById('dldfilename').value;
	document.DetailView.submit();
	OpenCompose(filename,'Documents');
{rdelim}  
</script>

<div id="lstRecordLayout" class="layerPopup" style="display:none;width:325px;height:300px;"></div>

{if $MODULE eq 'Accounts' || $MODULE eq 'Contacts' || $MODULE eq 'Leads'}
	{if $MODULE eq 'Accounts'}
		{assign var=address1 value='$MOD.LBL_BILLING_ADDRESS'}
		{assign var=address2 value='$MOD.LBL_SHIPPING_ADDRESS'}
	{/if}
	{if $MODULE eq 'Contacts'}
		{assign var=address1 value='$MOD.LBL_PRIMARY_ADDRESS'}
		{assign var=address2 value='$MOD.LBL_ALTERNATE_ADDRESS'}
	{/if}
	<div id="locateMap" onMouseOut="fninvsh('locateMap')" onMouseOver="fnvshNrm('locateMap')">
		<table bgcolor="#ffffff" border="0" cellpadding="0" cellspacing="0" width="100%">
			<tr>
				<td>
					{if $MODULE eq 'Accounts'}
						<a href="javascript:;" onClick="fninvsh('locateMap'); searchMapLocation( 'Main' );" class="calMnu">{$MOD.LBL_BILLING_ADDRESS}</a>
						<a href="javascript:;" onClick="fninvsh('locateMap'); searchMapLocation( 'Other' );" class="calMnu">{$MOD.LBL_SHIPPING_ADDRESS}</a>
					{/if}

					{if $MODULE eq 'Contacts'}
						<a href="javascript:;" onClick="fninvsh('locateMap'); searchMapLocation( 'Main' );" class="calMnu">{$MOD.LBL_PRIMARY_ADDRESS}</a>
						<a href="javascript:;" onClick="fninvsh('locateMap'); searchMapLocation( 'Other' );" class="calMnu">{$MOD.LBL_ALTERNATE_ADDRESS}</a>
					{/if}
				</td>
			</tr>
		</table>
	</div>
{/if}

<table width="100%" cellpadding="2" cellspacing="0" border="0">
	<tr>
		<td>
			{include file='Buttons_List.tpl'}
			<!-- Contents -->
			<table border=0 cellspacing=0 cellpadding=0 width=98% align=center>
				<tr>
					<td valign=top><img src="{'showPanelTopLeft.gif'|@vtiger_imageurl:$THEME}"></td>
					<td class="showPanelBg" valign=top width=100%>
						<!-- PUBLIC CONTENTS STARTS-->
						<div class="small" style="padding:10px" >
							<table align="center" border="0" cellpadding="0" cellspacing="0" width="95%">
								<tr><td>
										{* Module Record numbering, used MOD_SEQ_ID instead of ID *}
										{assign var="USE_ID_VALUE" value=$MOD_SEQ_ID}
									{if $USE_ID_VALUE eq ''} {assign var="USE_ID_VALUE" value=$ID} {/if}
									<span class="dvHeaderText">[ {$USE_ID_VALUE} ] {$NAME} -  {$SINGLE_MOD|@getTranslatedString:$MODULE} {$APP.LBL_INFORMATION}</span>&nbsp;&nbsp;&nbsp;<span class="small">{$UPDATEINFO}</span>&nbsp;<span id="vtbusy_info" style="display:none;" valign="bottom"><img src="{'vtbusy.gif'|@vtiger_imageurl:$THEME}" border="0"></span><span id="vtbusy_info" style="visibility:hidden;" valign="bottom"><img src="{'vtbusy.gif'|@vtiger_imageurl:$THEME}" border="0"></span>
								</td></tr>
							</table>
						<br>

						<!-- Account details tabs -->
						<table border=0 cellspacing=0 cellpadding=0 width=95% align=center>
							<tr>
								<td>
									<table border=0 cellspacing=0 cellpadding=3 width=100% class="small">
										<tr>
											<td class="dvtTabCache" style="width:10px" nowrap>&nbsp;</td>

											<td class="dvtSelectedCell" align=center nowrap>{$SINGLE_MOD|@getTranslatedString:$MODULE} {$APP.LBL_INFORMATION}</td>
											<td class="dvtTabCache" style="width:10px">&nbsp;</td>
											{if $SinglePane_View eq 'false' && $IS_REL_LIST neq false && $IS_REL_LIST|@count > 0}
												<td class="dvtUnSelectedCell" onmouseout="fnHideDrop('More_Information_Modules_List');" onmouseover="fnDropDown(this,'More_Information_Modules_List');" align="center" nowrap>
													<a href="index.php?action=CallRelatedList&module={$MODULE}&record={$ID}&parenttab={$CATEGORY}">{$APP.LBL_MORE} {$APP.LBL_INFORMATION}</a>
													<div onmouseover="fnShowDrop('More_Information_Modules_List')" onmouseout="fnHideDrop('More_Information_Modules_List')"
														 id="More_Information_Modules_List" class="drop_mnu" style="left: 502px; top: 76px; display: none;">
														<table border="0" cellpadding="0" cellspacing="0" width="100%">
															{foreach key=_RELATION_ID item=_RELATED_MODULE from=$IS_REL_LIST}
																<tr><td><a class="drop_down" href="index.php?action=CallRelatedList&module={$MODULE}&record={$ID}&parenttab={$CATEGORY}&selected_header={$_RELATED_MODULE}&relation_id={$_RELATION_ID}">{$_RELATED_MODULE|@getTranslatedString:$MODULE}</a></td></tr>
															{/foreach}
														</table>
													</div>
												</td>
											{/if}
											<td class="dvtTabCache" align="right" style="width:100%">
												{if $EDIT_PERMISSION eq 'permitted'}
													<input title="{$APP.LBL_EDIT_BUTTON_TITLE}" accessKey="{$APP.LBL_EDIT_BUTTON_KEY}" class="crmbutton small edit" onclick="DetailView.return_module.value='{$MODULE}'; DetailView.return_action.value='EventDetailView'; DetailView.return_id.value='{$ID}';DetailView.module.value='{$MODULE}';submitFormForAction('DetailView','EventEditView');" type="button" name="Edit" value="&nbsp;{$APP.LBL_EDIT_BUTTON_LABEL}&nbsp;">&nbsp;
												{/if}
												{if $CREATE_PERMISSION eq 'permitted'}
													<input title="{$APP.LBL_DUPLICATE_BUTTON_TITLE}" accessKey="{$APP.LBL_DUPLICATE_BUTTON_KEY}" class="crmbutton small create" onclick="DetailView.return_module.value='{$MODULE}'; DetailView.return_action.value='EventDetailView'; DetailView.isDuplicate.value='true';DetailView.module.value='{$MODULE}'; submitFormForAction('DetailView','EventEditView');" type="button" name="Duplicate" value="{$APP.LBL_DUPLICATE_BUTTON_LABEL}">&nbsp;
												{/if}
												{if $DELETE eq 'permitted'}
													<input title="{$APP.LBL_DELETE_BUTTON_TITLE}" accessKey="{$APP.LBL_DELETE_BUTTON_KEY}" class="crmbutton small delete" onclick="DetailView.return_module.value='{$MODULE}'; DetailView.return_action.value='index'; {if $MODULE eq 'Accounts'} var confirmMsg = '{$APP.NTC_ACCOUNT_DELETE_CONFIRMATION}' {else} var confirmMsg = '{$APP.NTC_DELETE_CONFIRMATION}' {/if}; submitFormForActionWithConfirmation('DetailView', 'Delete', confirmMsg);" type="button" name="Delete" value="{$APP.LBL_DELETE_BUTTON_LABEL}">&nbsp;
												{/if}

												{if $privrecord neq ''}
													<img align="absmiddle" title="{$APP.LNK_LIST_PREVIOUS}" accessKey="{$APP.LNK_LIST_PREVIOUS}" onclick="location.href='index.php?module={$MODULE}&viewtype={$VIEWTYPE}&action=EventDetailView&record={$privrecord}&parenttab={$CATEGORY}&start={$privrecordstart}'" name="privrecord" value="{$APP.LNK_LIST_PREVIOUS}" src="{'rec_prev.gif'|@vtiger_imageurl:$THEME}">&nbsp;
												{else}
													<img align="absmiddle" title="{$APP.LNK_LIST_PREVIOUS}" src="{'rec_prev_disabled.gif'|@vtiger_imageurl:$THEME}">
												{/if}
												{if $privrecord neq '' || $nextrecord neq ''}
													<img align="absmiddle" title="{$APP.LBL_JUMP_BTN}" accessKey="{$APP.LBL_JUMP_BTN}" onclick="var obj = this;var lhref = getListOfRecords(obj, '{$MODULE}',{$ID},'{$CATEGORY}');" name="jumpBtnIdTop" id="jumpBtnIdTop" src="{'rec_jump.gif'|@vtiger_imageurl:$THEME}">&nbsp;
												{/if}
												{if $nextrecord neq ''}
													<img align="absmiddle" title="{$APP.LNK_LIST_NEXT}" accessKey="{$APP.LNK_LIST_NEXT}" onclick="location.href='index.php?module={$MODULE}&viewtype={$VIEWTYPE}&action=EventDetailView&record={$nextrecord}&parenttab={$CATEGORY}&start={$nextrecordstart}'" name="nextrecord" src="{'rec_next.gif'|@vtiger_imageurl:$THEME}">&nbsp;
												{else}
													<img align="absmiddle" title="{$APP.LNK_LIST_NEXT}" src="{'rec_next_disabled.gif'|@vtiger_imageurl:$THEME}">&nbsp;
												{/if}
											</td>
										</tr>
									</table>
								</td>
							</tr>
							<tr>
								<td valign=top align=left >
									<table border=0 cellspacing=0 cellpadding=3 width=100% class="dvtContentSpace" style="border-bottom:0;">
										<tr valign=top>

											<td align=left>
												<!-- content cache -->

                                                {include file='modules/Calendar4You/EventDetailViewContent.tpl'}
											</td>
											<td width=22% valign=top style="border-left:1px dashed #cccccc;padding:13px">

												<!-- right side relevant info -->
												<!-- Action links for Event & Todo START-by Minnie -->
												<table width="100%" border="0" cellpadding="5" cellspacing="0">
													<tr><td align="left" class="genHeaderSmall">{$APP.LBL_ACTIONS}</td></tr>

													{if $MODULE eq 'HelpDesk'}
														{if $CONVERTASFAQ eq 'permitted'}
															<tr>
																<td align="left" style="padding-left:10px;">
																	<a class="webMnu" href="index.php?return_module={$MODULE}&return_action=EventDetailView&record={$ID}&return_id={$ID}&module={$MODULE}&action=ConvertAsFAQ"><img src="{'convert.gif'|@vtiger_imageurl:$THEME}" hspace="5" align="absmiddle"  border="0"/></a>
																	<a class="webMnu" href="index.php?return_module={$MODULE}&return_action=EventDetailView&record={$ID}&return_id={$ID}&module={$MODULE}&action=ConvertAsFAQ">{$MOD.LBL_CONVERT_AS_FAQ_BUTTON_LABEL}</a>
																</td>
															</tr>
														{/if}
													{elseif $MODULE eq 'Potentials'}
														{if $CONVERTINVOICE eq 'permitted'}
															<tr>
																<td align="left" style="padding-left:10px;">
																	<a class="webMnu" href="index.php?return_module={$MODULE}&return_action=EventDetailView&return_id={$ID}&convertmode={$CONVERTMODE}&module=Invoice&action=EventEditView&account_id={$ACCOUNTID}"><img src="{'actionGenerateInvoice.gif'|@vtiger_imageurl:$THEME}" hspace="5" align="absmiddle"  border="0"/></a>
																	<a class="webMnu" href="index.php?return_module={$MODULE}&return_action=EventDetailView&return_id={$ID}&convertmode={$CONVERTMODE}&module=Invoice&action=EventEditView&account_id={$ACCOUNTID}">{$APP.LBL_CREATE} {$APP.Invoice}</a>
																</td>
															</tr>
														{/if}
													{elseif $TODO_PERMISSION eq 'true' || $EVENT_PERMISSION eq 'true' || $CONTACT_PERMISSION eq 'true'|| $MODULE eq 'Contacts' || ($MODULE eq 'Documents')}

														{if $MODULE eq 'Contacts'}
															{assign var=subst value="contact_id"}
															{assign var=acc value="&account_id=$accountid"}
														{else}
															{assign var=subst value="parent_id"}
															{assign var=acc value=""}
														{/if}

														{if $MODULE eq 'Leads' || $MODULE eq 'Contacts' || $MODULE eq 'Accounts'}
															{if $SENDMAILBUTTON eq 'permitted'}
																<tr>
																	<td align="left" style="padding-left:10px;">
																		{foreach key=index item=email from=$EMAILS}
																			<input type="hidden" name="email_{$index}" value="{$email}"/>
																		{/foreach}
																		<a href="javascript:void(0);" class="webMnu" onclick="{$JS}"><img src="{'sendmail.png'|@vtiger_imageurl:$THEME}" hspace="5" align="absmiddle"  border="0"/></a>&nbsp;
																		<a href="javascript:void(0);" class="webMnu" onclick="{$JS}">{$APP.LBL_SENDMAIL_BUTTON_LABEL}</a>
																	</td>
																</tr>
															{/if}
														{/if}

														{if $MODULE eq 'Contacts' || $EVENT_PERMISSION eq 'true'}
															<tr>
																<td align="left" style="padding-left:10px;">
																	<a href="index.php?module=Calendar4You&action=EventEditView&return_module={$MODULE}&return_action=EventDetailView&activity_mode=Events&return_id={$ID}&{$subst}={$ID}{$acc}&parenttab={$CATEGORY}" class="webMnu"><img src="{'AddEvent.gif'|@vtiger_imageurl:$THEME}" hspace="5" align="absmiddle"  border="0"/></a>
																	<a href="index.php?module=Calendar4You&action=EventEditView&return_module={$MODULE}&return_action=EventDetailView&activity_mode=Events&return_id={$ID}&{$subst}={$ID}{$acc}&parenttab={$CATEGORY}" class="webMnu">{$APP.LBL_ADD_NEW} {$APP.Event}</a>
																</td>
															</tr>
														{/if}

														{if $TODO_PERMISSION eq 'true' && ($MODULE eq 'Accounts' || $MODULE eq 'Leads')}
															<tr>
																<td align="left" style="padding-left:10px;">
																	<a href="index.php?module=Calendar4You&action=EventEditView&return_module={$MODULE}&return_action=EventDetailView&activity_mode=Task&return_id={$ID}&{$subst}={$ID}{$acc}&parenttab={$CATEGORY}" class="webMnu"><img src="{'AddToDo.gif'|@vtiger_imageurl:$THEME}" hspace="5" align="absmiddle" border="0"/></a>
																	<a href="index.php?module=Calendar4You&action=EventEditView&return_module={$MODULE}&return_action=EventDetailView&activity_mode=Task&return_id={$ID}&{$subst}={$ID}{$acc}&parenttab={$CATEGORY}" class="webMnu">{$APP.LBL_ADD_NEW} {$APP.Todo}</a>
																</td>
															</tr>
														{/if}

														{if $MODULE eq 'Contacts' && $CONTACT_PERMISSION eq 'true'}
															<tr>
																<td align="left" style="padding-left:10px;">
																	<a href="index.php?module=Calendar4You&action=EventEditView&return_module={$MODULE}&return_action=EventDetailView&activity_mode=Task&return_id={$ID}&{$subst}={$ID}{$acc}&parenttab={$CATEGORY}" class="webMnu"><img src="{'AddToDo.gif'|@vtiger_imageurl:$THEME}" hspace="5" align="absmiddle" border="0"/></a>
																	<a href="index.php?module=Calendar4You&action=EventEditView&return_module={$MODULE}&return_action=EventDetailView&activity_mode=Task&return_id={$ID}&{$subst}={$ID}{$acc}&parenttab={$CATEGORY}" class="webMnu">{$APP.LBL_ADD_NEW} {$APP.Todo}</a>
																</td>
															</tr>
														{/if}

														{if $MODULE eq 'Leads'}
															{if $CONVERTLEAD eq 'permitted'}
																<tr>
																	<td align="left" style="padding-left:10px;">
																		<a href="javascript:void(0);" class="webMnu" onclick="callConvertLeadDiv('{$ID}');"><img src="{'Leads.gif'|@vtiger_imageurl:$THEME}" hspace="5" align="absmiddle"  border="0"/></a>
																		<a href="javascript:void(0);" class="webMnu" onclick="callConvertLeadDiv('{$ID}');">{$APP.LBL_CONVERT_BUTTON_LABEL}</a>
																	</td>
																</tr>
															{/if}
														{/if}

														<!-- Start: Actions for Documents Module -->
														{if $MODULE eq 'Documents'}
															<tr><td align="left" style="padding-left:10px;">
																	{if $DLD_TYPE eq 'I' && $FILE_STATUS eq '1' && $FILE_EXIST eq 'yes'}
																		<br><a href="index.php?module=uploads&action=downloadfile&fileid={$FILEID}&entityid={$NOTESID}"  onclick="javascript:dldCntIncrease({$NOTESID});" class="webMnu"><img src="{'fbDownload.gif'|@vtiger_imageurl:$THEME}" hspace="5" align="absmiddle" title="{$APP.LNK_DOWNLOAD}" border="0"/></a>
																		<a href="index.php?module=uploads&action=downloadfile&fileid={$FILEID}&entityid={$NOTESID}" onclick="javascript:dldCntIncrease({$NOTESID});">{$MOD.LBL_DOWNLOAD_FILE}</a>
																	{elseif $DLD_TYPE eq 'E' && $FILE_STATUS eq '1'}
																		<br><a target="_blank" href="{$DLD_PATH}" onclick="javascript:dldCntIncrease({$NOTESID});"><img src="{'fbDownload.gif'|@vtiger_imageurl:$THEME}"" align="absmiddle" title="{$APP.LNK_DOWNLOAD}" border="0"></a>
																		<a target="_blank" href="{$DLD_PATH}" onclick="javascript:dldCntIncrease({$NOTESID});">{$MOD.LBL_DOWNLOAD_FILE}</a>
																	{/if}
																</td></tr>
																{if $CHECK_INTEGRITY_PERMISSION eq 'yes'}
																<tr><td align="left" style="padding-left:10px;">
																		<br><a href="javascript:;" onClick="checkFileIntegrityDetailView({$NOTESID});"><img id="CheckIntegrity_img_id" src="{'yes.gif'|@vtiger_imageurl:$THEME}" alt="Check integrity of this file" title="Check integrity of this file" hspace="5" align="absmiddle" border="0"/></a>
																		<a href="javascript:;" onClick="checkFileIntegrityDetailView({$NOTESID});">{$MOD.LBL_CHECK_INTEGRITY}</a>&nbsp;
																		<input type="hidden" id="dldfilename" name="dldfilename" value="{$FILEID}-{$FILENAME}">
																		<span id="vtbusy_integrity_info" style="display:none;">
																			<img src="{'vtbusy.gif'|@vtiger_imageurl:$THEME}" border="0"></span>
																		<span id="integrity_result" style="display:none"></span>
																	</td></tr>
																{/if}
															<tr><td align="left" style="padding-left:10px;">
																	{if $DLD_TYPE eq 'I' &&  $FILE_STATUS eq '1' && $FILE_EXIST eq 'yes'}
																		<input type="hidden" id="dldfilename" name="dldfilename" value="{$FILEID}-{$FILENAME}">
																		<br><a href="javascript: document.DetailView.return_module.value='Documents'; document.DetailView.return_action.value='EventDetailView'; document.DetailView.module.value='Documents'; document.DetailView.action.value='EmailFile'; document.DetailView.record.value={$NOTESID}; document.DetailView.return_id.value={$NOTESID}; sendfile_email();" class="webMnu"><img src="{'attachment.gif'|@vtiger_imageurl:$THEME}" hspace="5" align="absmiddle" border="0"/></a>
																		<a href="javascript: document.DetailView.return_module.value='Documents'; document.DetailView.return_action.value='EventDetailView'; document.DetailView.module.value='Documents'; document.DetailView.action.value='EmailFile'; document.DetailView.record.value={$NOTESID}; document.DetailView.return_id.value={$NOTESID}; sendfile_email();">{$MOD.LBL_EMAIL_FILE}</a>
																	{/if}
																</td></tr>
															<tr><td>&nbsp;</td></tr>

														{/if}
													{/if}
												</table>
												{* vtlib customization: Avoid line break if custom links are present *}
												{if !isset($CUSTOM_LINKS) || empty($CUSTOM_LINKS)}
													<br>
												{/if}

												{* vtlib customization: Custom links on the Detail view basic links *}
												{if $CUSTOM_LINKS && $CUSTOM_LINKS.DETAILVIEWBASIC}
													<table width="100%" border="0" cellpadding="5" cellspacing="0">
														{foreach item=CUSTOMLINK from=$CUSTOM_LINKS.DETAILVIEWBASIC}
															<tr>
																<td align="left" style="padding-left:10px;">
																	{assign var="customlink_href" value=$CUSTOMLINK->linkurl}
																	{assign var="customlink_label" value=$CUSTOMLINK->linklabel}
																	{if $customlink_label eq ''}
																		{assign var="customlink_label" value=$customlink_href}
																	{else}
																		{* Pickup the translated label provided by the module *}
																		{assign var="customlink_label" value=$customlink_label|@getTranslatedString:$CUSTOMLINK->module()}
																	{/if}
																	{if $CUSTOMLINK->linkicon}
																		<a class="webMnu" href="{$customlink_href}"><img hspace=5 align="absmiddle" border=0 src="{$CUSTOMLINK->linkicon}"></a>
																		{/if}
																	<a class="webMnu" href="{$customlink_href}">{$customlink_label}</a>
																</td>
															</tr>
														{/foreach}
													</table>
												{/if}

												{* vtlib customization: Custom links on the Detail view *}
												{if $CUSTOM_LINKS && $CUSTOM_LINKS.DETAILVIEW}
													<br>
													{if !empty($CUSTOM_LINKS.DETAILVIEW)}
														<table width="100%" border="0" cellpadding="5" cellspacing="0">
															<tr><td align="left" class="dvtUnSelectedCell dvtCellLabel">
																	<a href="javascript:;" onmouseover="fnvshobj(this,'vtlib_customLinksLay');" onclick="fnvshobj(this,'vtlib_customLinksLay');"><b>{$APP.LBL_MORE} {$APP.LBL_ACTIONS} &#187;</b></a>
																</td></tr>
														</table>
														<br>
														<div style="display: none; left: 193px; top: 106px;width:155px; position:absolute;" id="vtlib_customLinksLay"
															 onmouseout="fninvsh('vtlib_customLinksLay')" onmouseover="fnvshNrm('vtlib_customLinksLay')">
															<table bgcolor="#ffffff" border="0" cellpadding="0" cellspacing="0" width="100%">
																<tr><td style="border-bottom: 1px solid rgb(204, 204, 204); padding: 5px;"><b>{$APP.LBL_MORE} {$APP.LBL_ACTIONS} &#187;</b></td></tr>
																<tr>
																	<td>
																		{foreach item=CUSTOMLINK from=$CUSTOM_LINKS.DETAILVIEW}
																			{assign var="customlink_href" value=$CUSTOMLINK->linkurl}
																			{assign var="customlink_label" value=$CUSTOMLINK->linklabel}
																			{if $customlink_label eq ''}
																				{assign var="customlink_label" value=$customlink_href}
																			{else}
																				{* Pickup the translated label provided by the module *}
																				{assign var="customlink_label" value=$customlink_label|@getTranslatedString:$CUSTOMLINK->module()}
																			{/if}
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
													<table border=0 cellspacing=0 cellpadding=0 width=100% class="tagCloud">
														<tr>
															<td class="tagCloudTopBg"><img src="{$IMAGE_PATH}tagCloudName.gif" border=0></td>
														</tr>
														<tr>
															<td><div id="tagdiv" style="display:visible;"><form method="POST" action="javascript:void(0);" onsubmit="return tagvalidate();"><input class="textbox"  type="text" id="txtbox_tagfields" name="textbox_First Name" value="" style="width:100px;margin-left:5px;"></input>&nbsp;&nbsp;<input name="button_tagfileds" type="submit" class="crmbutton small save" value="{$APP.LBL_TAG_IT}" /></form></div></td>
														</tr>
														<tr>
															<td class="tagCloudDisplay" valign=top> <span id="tagfields">{$ALL_TAG}</span></td>
														</tr>
													</table>
													<!-- End Tag cloud display -->
												{/if}
												<!-- Mail Merge-->
												<br>
												{if $MERGEBUTTON eq 'permitted'}
													<form action="index.php" method="post" name="TemplateMerge" id="form">
														<input type="hidden" name="module" value="{$MODULE}">
														<input type="hidden" name="parenttab" value="{$CATEGORY}">
														<input type="hidden" name="record" value="{$ID}">
														<input type="hidden" name="action">
														<table border=0 cellspacing=0 cellpadding=0 width=100% class="rightMailMerge">
															<tr>
																<td class="rightMailMergeHeader"><b>{$WORDTEMPLATEOPTIONS}</b></td>
															</tr>
															<tr style="height:25px">
																<td class="rightMailMergeContent">
																	{if $TEMPLATECOUNT neq 0}
																		<select name="mergefile">{foreach key=templid item=tempflname from=$TOPTIONS}<option value="{$templid}">{$tempflname}</option>{/foreach}</select>
																		<input class="crmbutton small create" value="{$APP.LBL_MERGE_BUTTON_LABEL}" onclick="this.form.action.value='Merge';" type="submit"></input>
																	{else}
																		<a href=index.php?module=Settings&action=upload&tempModule={$MODULE}&parenttab=Settings>{$APP.LBL_CREATE_MERGE_TEMPLATE}</a>
																	{/if}
																</td>
															</tr>
														</table>
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
															<br/>
															<table border=0 cellspacing=0 cellpadding=0 width=100% class="rightMailMerge">
																<tr>
																	<td class="rightMailMergeHeader">
																		<b>{$customlink_label}</b>
																		<img id="detailview_block_{$CUSTOMLINK_NO}_indicator" style="display:none;" src="{'vtbusy.gif'|@vtiger_imageurl:$THEME}" border="0" align="absmiddle" />
																	</td>
																</tr>
																<tr style="height:25px">
																	<td class="rightMailMergeContent"><div id="detailview_block_{$CUSTOMLINK_NO}"></div></td>
																</tr>
																<script type="text/javascript">
																			vtlib_loadDetailViewWidget("{$customlink_href}", "detailview_block_{$CUSTOMLINK_NO}", "detailview_block_{$CUSTOMLINK_NO}_indicator");
																</script>
															</table>
														{/if}
													{/foreach}
												{/if}
                                                
                                                {if $SinglePane_View eq 'true' && $IS_REL_LIST|@count > 0}
													{include file= 'modules/Calendar4You/RelatedListNew.tpl'}
												{/if}
											</td>
										</tr>
									</table>
									<!-- PUBLIC CONTENTS STOPS-->
								</td>
							</tr>
							<tr>
								<td>
									<table border=0 cellspacing=0 cellpadding=3 width=100% class="small">
										<tr>
											<td class="dvtTabCacheBottom" style="width:10px" nowrap>&nbsp;</td>

											<td class="dvtSelectedCellBottom" align=center nowrap>{$SINGLE_MOD|@getTranslatedString:$MODULE} {$APP.LBL_INFORMATION}</td>
											<td class="dvtTabCacheBottom" style="width:10px">&nbsp;</td>
											{if $SinglePane_View eq 'false' && $IS_REL_LIST neq false && $IS_REL_LIST|@count > 0}
												<td class="dvtUnSelectedCell" align=center nowrap><a href="index.php?action=CallRelatedList&module={$MODULE}&record={$ID}&parenttab={$CATEGORY}">{$APP.LBL_MORE} {$APP.LBL_INFORMATION}</a></td>
											{/if}
											<td class="dvtTabCacheBottom" align="right" style="width:100%">
												&nbsp;
												{if $EDIT_PERMISSION eq 'permitted'}
													<input title="{$APP.LBL_EDIT_BUTTON_TITLE}" accessKey="{$APP.LBL_EDIT_BUTTON_KEY}" class="crmbutton small edit" onclick="DetailView.return_module.value='{$MODULE}'; DetailView.return_action.value='EventDetailView'; DetailView.return_id.value='{$ID}';DetailView.module.value='{$MODULE}';submitFormForAction('DetailView','EventEditView');" type="submit" name="Edit" value="&nbsp;{$APP.LBL_EDIT_BUTTON_LABEL}&nbsp;">&nbsp;
												{/if}
												{if $CREATE_PERMISSION eq 'permitted'}
													<input title="{$APP.LBL_DUPLICATE_BUTTON_TITLE}" accessKey="{$APP.LBL_DUPLICATE_BUTTON_KEY}" class="crmbutton small create" onclick="EventDetailView.return_module.value='{$MODULE}'; DetailView.return_action.value='EventDetailView'; DetailView.isDuplicate.value='true';DetailView.module.value='{$MODULE}'; submitFormForAction('DetailView','EventEditView');" type="submit" name="Duplicate" value="{$APP.LBL_DUPLICATE_BUTTON_LABEL}">&nbsp;
												{/if}
												{if $DELETE eq 'permitted'}
													<input title="{$APP.LBL_DELETE_BUTTON_TITLE}" accessKey="{$APP.LBL_DELETE_BUTTON_KEY}" class="crmbutton small delete" onclick="DetailView.return_module.value='{$MODULE}'; DetailView.return_action.value='index'; {if $MODULE eq 'Accounts'} var confirmMsg = '{$APP.NTC_ACCOUNT_DELETE_CONFIRMATION}' {else} var confirmMsg = '{$APP.NTC_DELETE_CONFIRMATION}' {/if}; submitFormForActionWithConfirmation('DetailView', 'Delete', confirmMsg);" type="button" name="Delete" value="{$APP.LBL_DELETE_BUTTON_LABEL}">&nbsp;
												{/if}

												{if $privrecord neq ''}
													<img align="absmiddle" title="{$APP.LNK_LIST_PREVIOUS}" accessKey="{$APP.LNK_LIST_PREVIOUS}" onclick="location.href='index.php?module={$MODULE}&viewtype={$VIEWTYPE}&action=EventDetailView&record={$privrecord}&parenttab={$CATEGORY}'" name="privrecord" value="{$APP.LNK_LIST_PREVIOUS}" src="{'rec_prev.gif'|@vtiger_imageurl:$THEME}">&nbsp;
												{else}
													<img align="absmiddle" title="{$APP.LNK_LIST_PREVIOUS}" src="{'rec_prev_disabled.gif'|@vtiger_imageurl:$THEME}">
												{/if}
												{if $privrecord neq '' || $nextrecord neq ''}
													<img align="absmiddle" title="{$APP.LBL_JUMP_BTN}" accessKey="{$APP.LBL_JUMP_BTN}" onclick="var obj = this;var lhref = getListOfRecords(obj, '{$MODULE}',{$ID},'{$CATEGORY}');" name="jumpBtnIdBottom" id="jumpBtnIdBottom" src="{'rec_jump.gif'|@vtiger_imageurl:$THEME}">&nbsp;
												{/if}
												{if $nextrecord neq ''}
													<img align="absmiddle" title="{$APP.LNK_LIST_NEXT}" accessKey="{$APP.LNK_LIST_NEXT}" onclick="location.href='index.php?module={$MODULE}&viewtype={$VIEWTYPE}&action=EventDetailView&record={$nextrecord}&parenttab={$CATEGORY}'" name="nextrecord" src="{'rec_next.gif'|@vtiger_imageurl:$THEME}">&nbsp;
												{else}
													<img align="absmiddle" title="{$APP.LNK_LIST_NEXT}" src="{'rec_next_disabled.gif'|@vtiger_imageurl:$THEME}">&nbsp;
												{/if}
											</td>
										</tr>
									</table>
								</td>
							</tr>
						</table>


                    {if $MODULE eq 'Products'}
                    <script type="text/javascript" src="modules/Products/Productsslide.js"></script>
                    <script type="text/javascript">Carousel();</script>
                    {/if}
                    
                    <script>
                    
                    function getTagCloud()
                    {ldelim}
                    	var obj = document.getElementById("tagfields");
                    	if(obj != null && typeof(obj) != undefined) {ldelim}
					jQuery.ajax({ldelim}
							method:"POST",
							url:'index.php?module={$MODULE}&action={$MODULE}Ajax&file=TagCloud&ajxaction=GETTAGCLOUD&recordid={$ID}'
                    {rdelim}).done(function(response) {ldelim}
							document.getElementById("tagfields").innerHTML=response;
							document.getElementById("txtbox_tagfields").value ='';
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
	                <td align=right valign=top><img src="{'showPanelTopRight.gif'|@vtiger_imageurl:$THEME}"></td>
                </tr>
            </table>
</td></tr></table>
{if $MODULE eq 'Leads' or $MODULE eq 'Contacts' or $MODULE eq 'Accounts' or $MODULE eq 'Campaigns' or $MODULE eq 'Vendors'}
	<form name="SendMail"><div id="sendmail_cont" style="z-index:100001;position:absolute;"></div></form>
{/if}
