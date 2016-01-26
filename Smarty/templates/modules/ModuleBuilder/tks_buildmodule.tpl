{*<!--
/***********************************************************************************************
** The contents of this file are subject to the Vtiger Module-Builder License Version 1.0
 * ( "License" ); You may not use this file except in compliance with the License
 * The Original Code is:  Technokrafts Labs Pvt Ltd
 * The Initial Developer of the Original Code is Technokrafts Labs Pvt Ltd.
 * Portions created by Technokrafts Labs Pvt Ltd are Copyright ( C ) Technokrafts Labs Pvt Ltd.
 * All Rights Reserved.
**
*************************************************************************************************/
-->*}
{include file="Buttons_List1.tpl"}
<script language="JavaScript" type="text/javascript" src="include/js/smoothscroll.js"></script>
<script language="JavaScript">
{literal}

//To check and uncheck Select and Add action for "Select Related Modules" field
function tks_action(module,id)
{
	
	if(getObj(id).checked == true)
	{
		if(module == 'Contacts'){
		getObj(module+'_add').checked=true;
		getObj(module+'_add').disabled=true;
		getObj(module+'_sel').disabled=false;
		}
		else{
		getObj(module+'_sel').disabled=false;
		getObj(module+'_add').disabled=false;
		}
	}
	else
	{
		getObj(module+'_sel').checked=false;
		getObj(module+'_add').checked=false;
		getObj(module+'_sel').disabled=true;
		getObj(module+'_add').disabled=true;
	}
}

//To save module after validation
function copytks(list)
{
	var modulename 			= $("modulename").value;
	var moduleLable 		= $("moduleLeable").value;
	var parent 				= getObj('parent').value;
	var cnt					= getObj('tks_related_mod_cnt').value;
	var related_mod_list 	= '&related_mod=';
	var tks_mod				= '';
	var tks_entity_value 	= $('tks_entity').value;
	
	
	
	for(var t=0;t<cnt;t++)
	{
		if(document.getElementById(t).checked == true)
		{
			tks_mod=document.getElementById(t).value;
			related_mod_list = related_mod_list +":"+tks_mod;
			if(getObj(tks_mod+'_sel').checked == true)
			{
				related_mod_list = related_mod_list +"_1";
			}
			else
			{
				related_mod_list = related_mod_list +"_0";
			}
			if(getObj(tks_mod+'_add').checked == true)
			{
				related_mod_list = related_mod_list +"_1";
			}
			else
			{
				related_mod_list = related_mod_list +"_0";
			}
		}	
	}
	
	new Ajax.Request(
	'index.php',
	{queue: {position: 'end', scope: 'command'},
		method: 'post',
		postBody:'module=ModuleBuilder&action=ModuleBuilderAjax&file=tks_copyfolder&modulename='+modulename+'&parenttab=Tools&ajax=true'+related_mod_list+'&moduleLable='+moduleLable+'&parent='+parent+'&tks_entity='+tks_entity_value,
		onComplete: function(response) {
		if(response.responseText.indexOf('MODULE_NAME_ERROR') > -1){
			alert('{/literal}{$MOD.MODULE_NAME_ERROR}{literal}');
			return false;
		}	
		else if(response.responseText.indexOf("BLOCK") > -1 )
			alert('{/literal}{$MOD.TKS_ADD_ATLEAST_ONE_BLOCK}{literal}');
		else if(response.responseText.indexOf("FIELD") > -1 ){
			var splitval=response.responseText.split(":");	
				alert('{/literal}{$MOD.TKS_ADD_ATLEAST_ONE_FIELD}{literal}'+"'"+splitval[1]+"' !");
			}
		else if(response.responseText.indexOf("MANDATORY_FILTER") > -1 )
			alert('{/literal}{$MOD.TKS_MAEK_ATLEAST_ONE_FIELD_MANDATORY_FILTER}{literal}');
		else if(response.responseText.indexOf("TKSENTITY") > -1)
		{
			alert('{/literal}{$MOD.TKS_SELECT_ENTITY_IDENTIFIER_FIELD}{literal}');
		}	
		else if(response.responseText.indexOf("DIR_CRETION_ERROR") > -1)
		{
			alert('{/literal}{$MOD.DIR_CREATION_ERROR}{literal}');
		}	
		
		else 
			tks_confirmation();
		}
	}
	);	
}

//Confirmation before saving module
function tks_confirmation()
{
	var tks = confirm({/literal}'{$MOD.TKS_ARE_YOU_SURE_TO_SAVE_MODULE}'{literal});
		if(tks== true)
			setTimeout( deletetks, 1000 );
		else
			alert({/literal}'{$MOD.TKS_YOU_CANCELLED_TO_SAVE_MODULE}'{literal});	
}


{/literal}</script>
<br>
<table align="center" border="0" cellpadding="0" cellspacing="0" width="98%">
<tbody><tr>
        <td valign="top"><img src="{'showPanelTopLeft.gif'|@vtiger_imageurl:$THEME}"></td>
        <td class="showPanelBg" style="padding: 10px;" valign="top" width="100%">
        <br>

	<div align=center>
				<!-- DISPLAY -->
				<form action="index.php" method="post" name="new" id="form" onsubmit="VtigerJS_DialogBox.block();">
				<table border=0 cellspacing=0 cellpadding=5 width=100% class="settingsSelUITopLine">
				
				<input type="hidden" name="module" value="ModuleBuilder">
				<input type="hidden" name="action" value="tks_copyfolder">
				<input type="hidden" name="mode" value="create">
				<input type="hidden" name="parenttab" value="Tools">
				<tr>
					<td width=50 rowspan=2 valign=top><img src="{$TKS_LOGO}" alt="{$MOD.LBL_MODULE_BUILDER}" width="48" height="48" border=0 title="{$MOD.LBL_MODULE_BUILDER}"></td>
					<td class=heading2 valign=bottom><b>{$MOD.LBL_MODULE_BUILDER}</b></td>
				</tr>
				<tr>
					<td valign=top class="small">{$MOD.LBL_CREATE_AND_EXPORT_MODULE_TKS_DESCRIPTION}</td>
				</tr>
				</table>
				<br>
				<table border=0 cellspacing=0 cellpadding=10 width=100% >
				<tr>
					<td colspan="2">
						<div align="center">
								<input type="button" value="Save" onclick="if(valid() != false) copytks({$RELATED_LIST});" class="crmbutton small save">
							<input type="button" onclick="location.href='index.php?module=ModuleBuilder&amp;action=index'" value="Cancel" class="crmbutton small cancel">
							<input type="button" onclick="download_zip();" value="Download" class="crmbutton small save" id="download" style="display:none;">	
						
						</div>
					</td>
				</tr>
				<tr>
															<td colspan="2" class="detailedViewHeader">
																
															</td>
														</tr>
				<tr valign="center" bgcolor="white" style="height:25px;">
				<td width="15%" class="dvtCellLabel">{$MOD.MODULE_NAME}</td>
				<td class="dvtCellInfo"><input type="text" name="modulename" id="modulename" value="{$MODULENAME}" onblur="valid_label_modulename(this,'modulename');" class=detailedViewTextBox></td>
				</tr>
				
				<tr valign="center" bgcolor="white" style="height:25px;">
				<td width="15%" class="dvtCellLabel">{$MOD.MODULE_LABEL}</td>
				<td class="dvtCellInfo"><input type="text" name="moduleLeable" id="moduleLeable" value="{$MODULE_LABLE}" onchange="valid_label_modulename(this,'modulelabel');" class=detailedViewTextBox></td>
				</tr>
				
				<tr valign="center" bgcolor="white" style="height:25px;">
				<td width="15%" class="dvtCellLabel">{$MOD.PARENT_TAB_NAME}</td>
				<td class="dvtCellInfo">
					<select name="parent" id="parent" class="small">
					 {foreach item=module from=$PARENT_MODULE}
						<option value="{$module}">{$module}</option>
					 {/foreach}
					</select></td>
				</tr>
				<tr valign="center" bgcolor="white" style="height:25px;">
				<td width="15%" class="dvtCellLabel" valign="top">{$MOD.SELECT_RELATED_MODULES}Select Related Modules </td>
				<td class="dvtCellInfo">
				<table border=0 cellspacing=0 cellpadding=10 width=100% >
				<tr>
				<td valign="top" class="detailedViewHeader">&nbsp;</td>
				<td valign="top" class="detailedViewHeader">{$MOD.MODULE_NAME} </td>
				<td valign="top" class="detailedViewHeader">{$MOD.SELECT_ACTION} </td>
				<td valign="top" class="detailedViewHeader">{$MOD.ADD_ACTION}</td>
				</tr>
				<input type="hidden" name="tks_related_mod_cnt" value="{$RELATED_LIST|@count}"  />
				{assign var=i value=0}
				{foreach from=$RELATED_LIST key=k item=v}
				{assign var=moduletks value=$k|@getTranslatedString:'$MODULE'}
				<tr>
				<td><input type="checkbox" tabindex="" value="{$k}" id="{$i}" onclick="tks_action('{$k}','{$i}');"/></td>
				<td>{$moduletks}</td>
				<td>
				  <input type="checkbox" tabindex="" name="{$k}_sel" disabled="disabled" class="small"></td>
				<td>
				<input type="checkbox" tabindex="" name="{$k}_add"  disabled="disabled" class="small"></td>
				</tr>
				{assign var=i value=$i+1}
				{/foreach}
				</table>
				  </td>
				</tr>
				
				</table>
				<div id="cfList">
                {include file="modules/ModuleBuilder/tks_buildmodulelayout.tpl"}
            	</div>	
				
				
	</form>
	</div>
	</td>
	</tr>
	</tbody>
	</table>
<br />
{*<!--TECHNOKRAFTS START -- FOOTER -->*}
<br />
<div align="center" style="color: rgb(153, 153, 153);">
	{$MOD.LBL_FOOTER} :: v {$MOD.LBL_MODULE_VERSION}
	<br/>
	<a href="{$MOD.Order_URL}" name="TECHNOKRAFTS">{$MOD.COPYRIGHT}</a>
	<br/>
	{$MOD.LBL_COUNTRY}
</div>
{*<!--TECHNOKRAFTS END -- FOOTER -->*}
<script type="text/javascript">

function valid_label_modulename(tks , type)
{ldelim}
	if(type == 'modulename')
	{ldelim}
		 var modulename = $("modulename").value;
		 if (!modulename.match(/^[a-zA-Z]+$/) && modulename != '')
				  {ldelim}
				   window.setTimeout(function ()
					 {ldelim}
						alert('{$MOD.TKS_PLEASE_ENTER_ONLY_ALPHABETS_IN_MODULE_NAME}');
						$("modulename").value="";
						tks.focus();
					 {rdelim}, 0);
				   return false;
				  {rdelim}
		else 
			{ldelim}
			 module_duplication(modulename);
			 {rdelim}
	{rdelim}

	else if(type == 'modulelabel')
	{ldelim}
		var moduleLeable = $("moduleLeable").value;
		 if (!moduleLeable.match(/^[a-zA-Z ]+$/))
				  {ldelim}
				   window.setTimeout(function ()
					 {ldelim}
						alert('{$MOD.TKS_PLEASE_ENTER_ONLY_ALPHABETS_IN_MODULE_LABEL}');
						$("moduleLeable").value="";
						tks.focus();
					 {rdelim}, 0);
				   return false;
				  {rdelim}
	{rdelim}
{rdelim}


//To avoid module name duplication
function module_duplication(tks_modulename)
{ldelim}
var modulename = tks_modulename;
	new Ajax.Request(
		'index.php',
		{ldelim}queue: {ldelim}position: 'end', scope: 'command'{rdelim},
			method: 'post',
			postBody:'module=ModuleBuilder&action=ModuleBuilderAjax&file=tks_copyfolder&tks_delete=true&modulename='+modulename+'&parenttab=Tools&ajax=true&duplicate=duplicate',
			onComplete: function(response) {ldelim}
			$str = response.responseText;
			if( $str.indexOf("EXISTS") > -1 ){ldelim}
				window.setTimeout(function ()
					 {ldelim}
						alert('{$MOD.TKS_MODULE_EXISTS}');	
						$("modulename").value="";
						$("modulename").focus();
					 {rdelim}, 0);
				   return false;
				{rdelim}		
			{rdelim}
		{rdelim}
		);	
{rdelim}

//To download zip 
function download_zip()
{ldelim}       
	var module_name = $("modulename").value;
	var url = 'module=ModuleBuilder&action=ModuleBuilderAjax&file=tks_copyfolder&download=download&modulename='+module_name+'&parenttab=Tools&ajax=true';
	var req = new Ajax.Request('index.php',
    {ldelim}
     	asynchronous: false,
     	method: 'post',
     	postBody: url
	{rdelim});

	var str = req.transport.responseText;
	
	if(str.indexOf('SUCCESS')  == -1)
	{ldelim}
		if( str == 'NOT_EXISTS' )
			alert('{$MOD.TKS_PLEASE_SAVE_AGAIN_TO_DOWNLOAD_ZIP}');
		else if( str == 'READY' )
			window.open('index.php?module=ModuleBuilder&action=ModuleBuilderAjax&file=downloadzip&download=download&modulename='+module_name+'&parenttab=Tools&ajax=true', '_blank');
	{rdelim}
{rdelim}

//Validation
function valid()
{ldelim}
	if( !emptyCheck( "modulename", "Module Name","text" ) )
		return false;
	else if	( !emptyCheck( "moduleLeable", "Module Label","text" ) )
		return false;
	else if	( !emptyCheck( "parent", "Parent Tab Name","text" ) )
		return false;
	
	return true;
{rdelim}

//To delete module folder after creating module zip file
function deletetks()
{ldelim}
var modulename = $("modulename").value;
		new Ajax.Request(
		'index.php',
		{ldelim}queue: {ldelim}position: 'end', scope: 'command'{rdelim},
			method: 'post',
			postBody:'module=ModuleBuilder&action=ModuleBuilderAjax&file=tks_deletefolder&tks_delete=true&modulename='+modulename+'&parenttab=Tools&ajax=true',
			onComplete: function(response) {ldelim}		
			alert('{$MOD.TKS_MODULE_IS_CREATED}');
					if(response.responseText.indexOf("DELETED") > -1){ldelim}
					$('download').style.display = "inline";
					$('download1').style.display = "inline";
					{rdelim}
			{rdelim}
		{rdelim}
		);	
{rdelim}
</script>