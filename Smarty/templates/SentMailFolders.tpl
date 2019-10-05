<script type="text/javascript" src="modules/Emails/Emails.js"></script>

<script>
function ShowFolders(folderid)
{ldelim}

	if(document.getElementById('mail_fldrname')!=null){ldelim}
		var mail_folder=document.getElementById('mail_fldrname').innerHTML;
	{rdelim}
	gselectedrowid = 0;
	document.getElementById("status").style.display="inline";
	gFolderid = folderid;
	folder='';
//	getObj('search_text').value = '';
	switch(folderid)
	{ldelim}
		case 1:
			getObj('mail_fldrname').innerHTML = '<b>{'LBL_ALLMAILS'|@getTranslatedString:$MODULE}</b>';
			folder='<b>{'LBL_ALLMAILS'|@getTranslatedString:$MODULE}</b>';
			break;
		case 2:
			getObj('mail_fldrname').innerHTML = '<b>{'LBL_TO_CONTACTS'|@getTranslatedString:$MODULE}</b>';
			folder='<b>{'LBL_TO_CONTACTS'|@getTranslatedString:$MODULE}</b>';
			break;
		case 3:
			getObj('mail_fldrname').innerHTML = '<b>{'LBL_TO_ACCOUNTS'|@getTranslatedString:$MODULE}</b>';
			folder='<b>{'LBL_TO_ACCOUNTS'|@getTranslatedString:$MODULE}</b>';
			break;
		case 4:
			getObj('mail_fldrname').innerHTML = '<b>{'LBL_TO_LEADS'|@getTranslatedString:$MODULE}</b>';
			folder='<b>{'LBL_TO_LEADS'|@getTranslatedString:$MODULE}</b>';
			break;
		case 5:
			getObj('mail_fldrname').innerHTML = '<b>{'LBL_TO_USERS'|@getTranslatedString:$MODULE}</b>';
			folder='<b>{'LBL_TO_USERS'|@getTranslatedString:$MODULE}</b>';
			break;
		case 6:
			getObj('mail_fldrname').innerHTML = '<b>{'LBL_QUAL_CONTACT'|@getTranslatedString:$MODULE}</b>';
			folder='<b>{'LBL_QUAL_CONTACT'|@getTranslatedString:$MODULE}</b>';
	{rdelim}

	jQuery.ajax({ldelim}
				method:"POST",
				url:'index.php?module=Emails&ajax=true&action=EmailsAjax&file=ListView&folderid='+folderid
	{rdelim}).done(function(response) {ldelim}
										document.getElementById("status").style.display="none";
										if(document.getElementById('mail_fldrname')!=null){ldelim}
											if(document.getElementById('_mailfolder_'+mail_folder)!=null && document.getElementById('_mailfolder_'+mail_folder).className!='mm_folder'){ldelim}
												document.getElementById('_mailfolder_'+mail_folder).className='mm_folder';
												document.getElementById('_mailfolder_'+mail_folder).parentNode.className='';
											{rdelim}
										{rdelim}
										if(document.getElementById('_replydiv_')!=null){ldelim}
											if(document.getElementById('_replydiv_').style.display!='none')
											{ldelim}
											document.getElementById('_replydiv_').style.display='none';
											document.getElementById('_contentdiv_').style.display='block';
												document.getElementById('_mailfolder_mm_compose').parentNode.className='';
												document.getElementById('_mailfolder_mm_compose').className='';
											{rdelim}
										{rdelim}
										if(document.getElementById('_settingsdiv_')!=null){ldelim}
											if(document.getElementById('_settingsdiv_').style.display!='none')
											{ldelim}document.getElementById('_settingsdiv_').style.display='none';
											document.getElementById('_contentdiv_').style.display='block';
												document.getElementById('_mailfolder_mm_settings').parentNode.className='';
												document.getElementById('_mailfolder_mm_settings').className='';
											{rdelim}
										{rdelim}
										if(document.getElementById('_contentdiv2_')!=null){ldelim}
											if(document.getElementById('_contentdiv2_').style.display!='none')
											{ldelim}
											document.getElementById('_contentdiv2_').style.display='none';
											document.getElementById('_contentdiv_').style.display='block';
												document.getElementById('_mailfolder_mm_settings').parentNode.className='';
											{rdelim}
										{rdelim}
										result = response.split('&#&#&#');
										if (result[1] != '') alert(result[1]);
										if(gFolderid == folderid) {ldelim}
											gselectedrowid = 0;
											document.getElementById("email_con").innerHTML=result[2];
											var emaildtl = document.getElementById('EmailDetails');
											if (emaildtl) {
												emaildtl.innerHTML = '<table valign="top" border="0" cellpadding="0" cellspacing="0" width="100%"><tbody><tr><td class="forwardBg"><table border="0" cellpadding="0" cellspacing="0" width="100%"><tbody><tr><td colspan="2">&nbsp;</td></tr></tbody></table></td></tr><tr><td style="padding-top:10px;" bgcolor="#ffffff" height="300" valign="top"></td></tr></tbody></table>';
											}
											execJS(document.getElementById('email_con'));
										{rdelim} else {ldelim}
											if (emaildtl) {
												emaildtl.innerHTML = '<table valign="top" border="0" cellpadding="0" cellspacing="0" width="100%"><tbody><tr><td class="forwardBg"><table border="0" cellpadding="0" cellspacing="0" width="100%"><tbody><tr><td colspan="2">&nbsp;</td></tr></tbody></table></td></tr><tr><td style="padding-top:10px;" bgcolor="#ffffff" height="300" valign="top"></td></tr></tbody></table>';
											}
											document.getElementById("email_con").innerHTML=result[2];
											execJS(document.getElementById('email_con'));
										{rdelim}
										if(document.getElementById('_contentdiv_')!=null){ldelim}
											if(document.getElementById('_mailfolder_mm_drafts').parentNode.className=='mm_folder_selected'){ldelim}
												document.getElementById('_mailfolder_mm_drafts').parentNode.className='';
												document.getElementById('mm_folder mm_folder_selected').className='';
											{rdelim}
										{rdelim}
	{rdelim});
{rdelim}
</script>

<!-- Sent mail -->
{if $SHOW_SENTTO_LINKS}
<img src="{'sentmail.gif'|@vtiger_imageurl:$THEME}" align="absmiddle" />&nbsp;<b class="txtGreen">{'LBL_SENT_MAILS'|@getTranslatedString:$MODULE}</b>
<ul style="list-style-type:none;margin-left:10px;margin-top:5px;padding:2px">
	<li><img src="{'folder1.gif'|@vtiger_imageurl:$THEME}" align="absmiddle" />&nbsp;&nbsp;
	<a href="javascript:;" onClick="ShowFolders(1)" class="webMnu">{'LBL_ALLMAILS'|@getTranslatedString:$MODULE}</a>&nbsp;<b></b>
	<li><img src="{'folder1.gif'|@vtiger_imageurl:$THEME}" align="absmiddle" />&nbsp;&nbsp;
	<a href="javascript:;" onClick="ShowFolders(2)" class="webMnu">{'LBL_TO_CONTACTS'|@getTranslatedString:$MODULE}</a>&nbsp;<b></b>
	</li>
	<li><img src="{'folder1.gif'|@vtiger_imageurl:$THEME}" align="absmiddle" />&nbsp;&nbsp;
	<a href="javascript:;" onClick="ShowFolders(3)" class="webMnu">{'LBL_TO_ACCOUNTS'|@getTranslatedString:$MODULE}</a>&nbsp;<b></b>
	</li>
	<li><img src="{'folder1.gif'|@vtiger_imageurl:$THEME}" align="absmiddle" />&nbsp;&nbsp;
	<a href="javascript:;" onClick="ShowFolders(4)" class="webMnu">{'LBL_TO_LEADS'|@getTranslatedString:$MODULE}</a>&nbsp;
	</li>
	<li><img src="{'folder1.gif'|@vtiger_imageurl:$THEME}" align="absmiddle" />&nbsp;&nbsp;
	<a href="javascript:;" onClick="ShowFolders(5)" class="webMnu">{'LBL_TO_USERS'|@getTranslatedString:$MODULE}</a>&nbsp;
	</li>
</ul>
{/if}