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
<script language="JavaScript" type="text/javascript" src="include/js/customview.js"></script>
<script language="JavaScript" type="text/javascript" src="include/js/general.js"></script>
<script language="JavaScript">
{literal}
//Validation for Block Name
function check(){
	var blocklabel = document.getElementById('blocklabel');
	var val = trim(blocklabel.value);
	if(val == "")
	{
		alert('{/literal}{$MOD.BLOCK_NAME_CANNOT_BE_BLANK}{literal}');
		return false;
	}
	if(!val.match(/^[a-zA-Z ]+$/))
	{
		alert('{/literal}{$MOD.TKS_ENTER_ONLY_ALPHABETS}{literal}');
		return false;
	}
	return true;
}
{/literal}</script>
<script language="javascript">

function getCustomFieldList(customField)
{ldelim}
	var modulename = customField.options[customField.options.selectedIndex].value;
	$('module_info').innerHTML = '{$MOD.LBL_CUSTOM_FILED_IN} "'+modulename+'" {$APP.LBL_MODULE}';
	new Ajax.Request(
		'index.php',
		{ldelim}queue: {ldelim}position: 'end', scope: 'command'{rdelim},
			method: 'post',
			postBody: 'module=ModuleBuilder&action=ModuleBuilderAjax&file=LayoutBlockList&fld_module='+modulename+'&parenttab=Tools&ajax=true',
			onComplete: function(response) {ldelim}
				$("cfList").update(response.responseText);
			{rdelim}
		{rdelim}
	);	
{rdelim}

function changeFieldorder(what_to_do,fieldid,blockid,modulename)
{ldelim}
	$('vtbusy_info').style.display = "block";
	new Ajax.Request(
		'index.php',
		{ldelim}queue: {ldelim}position: 'end', scope: 'command'{rdelim},
			method: 'post',
			postBody: 'module=ModuleBuilder&action=ModuleBuilderAjax&file=LayoutBlockList&sub_mode=changeOrder&fld_module='+modulename+'&parenttab=Tools&what_to_do='+what_to_do+'&fieldid='+fieldid+'&blockid='+blockid+'&ajax=true',
			onComplete: function(response) {ldelim}
				$("cfList").update(response.responseText);
				$('vtbusy_info').style.display = "none";
			{rdelim}
		{rdelim}
	);	
{rdelim}


function changeShowstatus(tabid,blockid,modulename)	
{ldelim}
	var display_status = $('display_status_'+blockid).value;
	new Ajax.Request(
		'index.php',
		{ldelim}queue: {ldelim}position: 'end', scope: 'command'{rdelim},
			method: 'post',
			postBody: 'module=ModuleBuilder&action=ModuleBuilderAjax&file=LayoutBlockList&sub_mode=changeOrder&fld_module='+modulename+'&parenttab=Tools&what_to_do='+display_status+'&tabid='+tabid+'&blockid='+blockid+'&ajax=true',
			onComplete: function(response) {ldelim}
				$("cfList").update(response.responseText);
			{rdelim}
		{rdelim}
		
	);	
{rdelim}

function changeBlockorder(what_to_do,tabid,blockid,modulename)	
{ldelim}
	$('vtbusy_info').style.display = "block";
		new Ajax.Request(
		'index.php',
		{ldelim}queue: {ldelim}position: 'end', scope: 'command'{rdelim},
			method: 'post',
			postBody: 'module=ModuleBuilder&action=ModuleBuilderAjax&file=LayoutBlockList&sub_mode=changeOrder&fld_module='+modulename+'&parenttab=Tools&what_to_do='+what_to_do+'&tabid='+tabid+'&blockid='+blockid+'&ajax=true',
			onComplete: function(response) {ldelim}
				$("cfList").update(response.responseText);
				$('vtbusy_info').style.display = "none";
			{rdelim}
		{rdelim}
		
	);	
{rdelim}


{literal}
function deleteCustomField(id, fld_module, colName, uitype)
{
       if(confirm('{/literal}{$MOD.ARE_YOU_SURE_YOU_WANT_TO_DELETE}{literal}')){
        $('vtbusy_info').style.display = "block";
			new Ajax.Request(
				'index.php',
				{queue: {position: 'end', scope: 'command'},
					method: 'post',
					postBody: 'module=ModuleBuilder&action=ModuleBuilderAjax&file=tks_buildmodulelayout&sub_mode=deleteCustomField&ajax=true&fld_module='+fld_module+'&fld_id='+id+'&colName='+colName+'&uitype='+uitype,
					onComplete: function(response) {
						$("cfList").update(response.responseText);
						gselected_fieldtype = '';
						$('vtbusy_info').style.display = "none";
					}
				}
			);		
		}else{
		fninvsh('editfield_'+id);
		}
}

function deleteCustomBlock(module,blockid){
	if(confirm('{/literal}{$MOD.ARE_YOU_SURE_YOU_WANT_TO_DELETE_BLOCK}{literal}')){
			$('vtbusy_info').style.display = "block";
			new Ajax.Request(
				'index.php',
				{queue : {position : 'end', scope: 'command'},
				method : 'post',
				postBody: 'module=ModuleBuilder&action=ModuleBuilderAjax&fld_module='+module+'&file=tks_buildmodulelayout&sub_mode=deleteCustomBlock&ajax=true&blockid='+blockid,
				onComplete: function(response) {
					$("cfList").update(response.responseText);
					$('vtbusy_info').style.display = "none";
				}
				}	
			);	
		}
	
}
//To create custom block
function getCreateCustomBlockForm(modulename,mode)
{
	var checlabel = check();
	if(checlabel == false)
		return false;
	var blocklabel = document.getElementById('blocklabel');
	var tks_modulename = document.getElementById('modulename').value;
	var val = trim(blocklabel.value);
	if(document.getElementById('after_blockid')){
		var blockid = document.getElementById('after_blockid').value;
	}
	else{
		var blockid = '';
	}
	$('vtbusy_info').style.display = "block";
			new Ajax.Request(
		'index.php',
		{queue: {position: 'end', scope: 'command'},
			method: 'post',
			postBody: 'module=ModuleBuilder&action=ModuleBuilderAjax&file=tks_buildmodulelayout&sub_mode=addBlock&tks_modulename='+tks_modulename+'&parenttab=Tools&ajax=true&mode='+mode+'&blocklabel='+encodeURIComponent(val)+'&after_blockid='+blockid,
			onComplete: function(response) {
				$('vtbusy_info').style.display = "none";
				var str = response.responseText;
				if(str == 'MODULE_NAME_ERROR'){
					alert('{/literal}{$MOD.MODULE_NAME_ERROR}{literal}');
					return false;
				}
				else if(str == 'ERROR'){
					alert('{/literal}{$MOD.LABEL_ALREADY_EXISTS}{literal}');
					return false;
				}else if(str == 'LENGTH_ERROR'){
					alert('{/literal}{$MOD.LENGTH_OUT_OF_RANGE}{literal}');
					return false;
				}
				else{
					$("cfList").update(str);
				}		
				gselected_fieldtype = '';
			}
		}
	);


}


//To save field information
function saveFieldInfo(fieldid,module,sub_mode,typeofdata){
	urlstring = '';
	var mandatory_check = $('mandatory_check_'+fieldid);
	var filter_check = $('filter_check_'+fieldid);
	var tks_fldname = $('rename_fld_'+fieldid).value;
	
	 if($('rename_fld_'+fieldid).value.match(/^[a-zA-Z ]+$/))
	  var tks_fldname = $('rename_fld_'+fieldid).value;
  	 else {
   		alert('{/literal}{$MOD.TKS_INVALID_FIELD_LABEL}{literal}');
		return false;
	 }
		
	var tks_modulename = document.getElementById('modulename').value;
	if(mandatory_check != null){
		urlstring = urlstring+'&ismandatory=' + mandatory_check.checked;
	}
	
	if(tks_fldname != '') {
	
		urlstring = urlstring + '&tks_fldname=' + encodeURIComponent(tks_fldname);
	}
	if(filter_check != null){
		urlstring = urlstring+'&isfilter=' + filter_check.checked;
	}
	$('vtbusy_info').style.display = "block";
	new Ajax.Request(
			'index.php',
			{queue : {position: 'end',scope:'command'},
				method:'post',
				postBody:'module=ModuleBuilder&action=ModuleBuilderAjax&file=tks_buildmodulelayout&sub_mode='+sub_mode+'&parenttab=Tools'+
					'&fieldid='+fieldid+'&tks_modulename='+tks_modulename+'&ajax=true'+urlstring,
				onComplete: function(response) {
					$("cfList").update(response.responseText);
					$('vtbusy_info').style.display = "none";
					fnvshNrm('editfield_+"fieldid"');
				}
			}
	);
}


function enableDisableCheckBox(obj, elementName) {
	
	var ele = $(elementName);
	if (obj == null || ele == null) return;
	if (obj.checked == true) {
		ele.checked = true;
		ele.disabled = true;
	} else {
		ele.disabled = false;
	}	
}

function showHideTextBox(obj, elementName) {
	var ele = $(elementName);
	if (obj == null || ele == null) return;
	if (obj.checked == true) {
		ele.disabled = false;
	} else {
		ele.disabled = true;
	}
}

//To create custom field
function getCreateCustomFieldForm(modulename,blockid,mode)
{
   var check	= tks_validate(blockid);
   if(check == false)
   return false;
   var type				= document.getElementById("fieldType_"+blockid).value;
   var tks_modulename	= document.getElementById('modulename').value;
  
   if(document.getElementById("fldLabel_"+blockid).value.match(/^[a-zA-Z ]+$/))
	   var label = document.getElementById("fldLabel_"+blockid).value;
   else {
   		alert('{/literal}{$MOD.TKS_INVALID_FIELD_LABEL}{literal}');
		return false;
		}
  
   var fldLength 		= document.getElementById("fldLength_"+blockid).value;  
   var fldDecimal 		= document.getElementById("fldDecimal_"+blockid).value;
   var tks_mfield 		= document.getElementById("mandatory_"+blockid).checked;
   var tks_filterfield 	= document.getElementById("filter_"+blockid).checked;
   var tks_relatemodule = document.getElementById("module_"+blockid);
   var relatedmoduleslength	= document.getElementById("module_"+blockid).options.length;
   var tks_selected 	= '';
   for(var i=0; i < relatedmoduleslength; i++) {
			if (tks_relatemodule[i].selected == true) {
				tks_selected = tks_selected + tks_relatemodule[i].value + ":";
			}
		}
	if(type	== 'Relate'){	//for uitype 10
		if(tks_selected == ''){
			alert('{/literal}{$MOD.TKS_SELECT_ATLEAST_ONE_MODULE}{literal}');
			return false;
		}
	}
   var fldPickList = encodeURIComponent(document.getElementById("fldPickList_"+blockid).value);
   VtigerJS_DialogBox.block();
   new Ajax.Request(
		'index.php',
		{queue: {position: 'end', scope: 'command'},
			method: 'post',
			postBody: 'module=ModuleBuilder&action=ModuleBuilderAjax&file=tks_buildmodulelayout&sub_mode=addCustomField&tks_modulename='+tks_modulename+'&ajax=true&blockid='+blockid+'&fieldType='+type+'&fldLabel='+label+'&fldLength='+fldLength+'&fldDecimal='+fldDecimal+'&fldPickList='+fldPickList+'&tks_mfield='+tks_mfield+'&tks_filterfield='+tks_filterfield+'&tks_relatemodule='+tks_selected,
			onComplete: function(response) {
				VtigerJS_DialogBox.unblock();
				var str = response.responseText;
				if(str == 'MODULE_NAME_ERROR'){
					alert('{/literal}{$MOD.MODULE_NAME_ERROR}{literal}');
					return false;
				}
				else 
				if(str == 'ERROR'){
					alert('{/literal}{$MOD.LABEL_ALREADY_EXISTS}{literal}');
					return false;
				}
				else if(str == 'PICKLIST_EXIST'){
					alert('{/literal}{$MOD.TKS_PICKLIST_NAME_ALLREADY_EXISTS}{literal}');
				}
				else{
					$("cfList").update(str);
				}	
				gselected_fieldtype = '';
			}
		}
	);
}



function tks_validate(blockid) {

	var fieldValueArr=new Array('Text','Number','Decimal','Percent','Currency','Date','Email','Phone','Picklist','URL','Checkbox','TextArea','MultiSelectCombo','Skype','Time');
	var fieldTypeArr=new Array('text','number','decimal','percent','currency','date','email','phone','picklist','url','checkbox','textarea','multiselectcombo','skype','time');
	var currFieldIdx=0,totFieldType;
	var focusFieldType;
	var nummaxlength = 255;
	var fieldtype = document.getElementById('selectedfieldtype_'+blockid).value;
	var mode = document.getElementById('cfedit_mode').value;
	if(fieldtype == "" && mode != 'edit')
	{
		alert('{/literal}{$MOD.FIELD_TYPE_NOT_SELECTED}{literal}');
		return false;
	}
	lengthLayer=document.getElementById("lengthdetails_"+blockid)
	decimalLayer=document.getElementById("decimaldetails_"+blockid)
	var pickListLayer=document.getElementById("fldPickList_"+blockid);
	var fldlbl = document.getElementById("fldLabel_"+blockid);
	var str = fldlbl.value;
	if (!emptyCheck("fldLabel_"+blockid,"Label","text"))
		return false
	var re2=/[&\<\>\:\'\"\,\_]/
	if (re2.test(str))
	{
		alert("{/literal}{$MOD.SPECIAL_CHARACTERS}{literal} & < > ' \" : , _  {/literal}{$MOD.NOT_ALLOWED}{literal}");
		return false;
	}
	var fieldlength = document.getElementById('fldLength_'+blockid);
	if (lengthLayer != null && lengthLayer.style.visibility=="visible") {
		if (!emptyCheck('fldLength_'+blockid,"Length"))
			return false

		if (!intValidate('fldLength_'+blockid,"Length"))
			return false

		if (!numConstComp('fldLength_'+blockid,"Length","G",0))
			return false

	}

	if (decimalLayer != null && decimalLayer.style.visibility=="visible") {
		if (document.getElementById("fldDecimal_"+blockid).value.replace(/^\s+/g, '').replace(/\s+$/g, '').length>0)
			if (!intValidate("fldDecimal_"+blockid,"Decimal"))
				return false
		if (!numConstComp("fldDecimal_"+blockid,"Decimal","GE",0))
			return false

		if (!numConstComp("fldDecimal_"+blockid,"Decimal","LE",30))
			return false
		
		if( document.getElementById("fldLength_"+blockid).value < document.getElementById("fldDecimal_"+blockid).value )	
		{
			alert("{/literal}{$MOD.GREATER_THEN}{literal}");
			return false;
		}
	}
	var decimallength = '';
	if (decimalLayer != null && decimalLayer.style.visibility=="visible" && document.getElementById('fldDecimal_'+blockid) != null)
		decimallength = document.getElementById("fldDecimal_"+blockid).value;
        
	if(fieldValueArr[fieldtype] == 'Percent' || fieldValueArr[fieldtype] == 'Currency' || fieldValueArr[fieldtype] == 'Decimal')
	{
		if(decimallength == '')
			decimallength = 0;
		nummaxlength = 65 - (eval(decimallength) + 1);
	}
	var lengthObj = document.getElementById("lengthdetails_"+blockid);
	if ( lengthObj != null && lengthObj.style.visibility == "visible" && !numConstComp('fldLength_'+blockid,"Length","LE",nummaxlength))
		return false
	var picklistObj=document.getElementById("fldPickList_"+blockid)
	if (pickListLayer != null && getObj("picklistdetails_"+blockid).style.visibility=="visible") {
		var pickListAry=new Array();
		pickListAry=splitValues(pickListLayer);
		if (emptyCheck("fldPickList_"+blockid,"Picklist values"))        {

			//Empty Check validation
			for (i=0;i<pickListAry.length;i++) {
				if (pickListAry[i]=="") {
					alert('{/literal}{$MOD.PICKLIST_CANNOT_BE_EMPTY}{literal}');
					picklistObj.focus();
					return false
				}
			}

			//Duplicate Values' Validation
			for (i=0;i<pickListAry.length;i++) {
				for (j=i+1;j<pickListAry.length;j++) {
					if (trim(pickListAry[i].toUpperCase())== trim(pickListAry[j].toUpperCase())) {
						alert('{/literal}{$MOD.DUPLICATE_VALUES_FOUND}{literal}');
						picklistObj.focus();
						return false
					}
				}
			}

			//Empty Check validation
			for (i=0;i<pickListAry.length;i++) {
				if (pickListAry[i].search(/(\<|\>|\\|\/)/gi)!=-1) {
					alert('{/literal}{$MOD.SPECIAL_CHARACTERS}{literal}'+'"<" ">" "\\" "/"'+'{/literal}{$MOD.NOT_ALLOWED}{literal}');

					picklistObj.focus();
					return false
				}
			}

			return true
		} else return false
	}
	return true;
}



function makeFieldSelected(oField,fieldid,blockid)
{
	if(gselected_fieldtype != '')
	{
		$(gselected_fieldtype).className = 'customMnu';
	}
	oField.className = 'customMnuSelected';	
	gselected_fieldtype = oField.id;	
	selFieldType_tks(fieldid,'','',blockid);
	document.getElementById('selectedfieldtype_'+blockid).value = fieldid;
}

function selFieldType_tks(id,scrollLayer,bool,blockid) {
var fieldValueArr	=	new Array('Text','Number','Decimal','Percent','Currency','Date','Email','Phone','Picklist','URL','Checkbox','TextArea','MultiSelectCombo','Skype','Time','Relate');
var fieldTypeArr	=	new Array('text','number','decimal','percent','currency','date','email','phone','picklist','url','checkbox','textarea','multiselectcombo','skype','time','relate');

	currFieldIdx	=	id
	var type		=	fieldTypeArr[id]
	var lengthLayer	=	document.getElementById("lengthdetails_"+blockid);
	var decimalLayer	=	document.getElementById("decimaldetails_"+blockid);
	var pickListLayer	=	document.getElementById("picklistdetails_"+blockid);
	var relatemodule	=	document.getElementById("relatedmodule_"+blockid);
	if (type == 'text' || type == 'number') {
		lengthLayer.style.visibility	=	"visible"
		decimalLayer.style.visibility	=	"hidden"
		pickListLayer.style.visibility	=	"hidden"
		relatemodule.style.visibility	=	"hidden"
	} else if (type == 'date' || type == 'percent' || type == 'email' || type == 'phone' || type == 'url' || type == 'checkbox' || type == 'textarea' || type == 'skype' || type == 'time') {
		document.getElementById("lengthdetails_"+blockid).style.visibility	=	"hidden"
		decimalLayer.style.visibility	=	"hidden"
		pickListLayer.style.visibility	=	"hidden"
		relatemodule.style.visibility	=	"hidden"
	} else if (type == 'decimal' || type == 'currency') {
		lengthLayer.style.visibility	=	"visible"
		decimalLayer.style.visibility	=	"visible"
		pickListLayer.style.visibility	=	"hidden"
		relatemodule.style.visibility	=	"hidden"
	} else if (type == 'picklist' || type == 'multiselectcombo') {
		lengthLayer.style.visibility	=	"hidden"
		decimalLayer.style.visibility	=	"hidden"
		pickListLayer.style.visibility	=	"visible"
		relatemodule.style.visibility	=	"hidden"
	}
	else if (type == 'relate') {
		lengthLayer.style.visibility	=	"hidden"
		decimalLayer.style.visibility	=	"hidden"
		pickListLayer.style.visibility	=	"hidden"
		relatemodule.style.visibility	=	"visible"
	}
	document.getElementById("fieldType_"+blockid).value = fieldValueArr[id];
}

function show_move_hiddenfields(modulename,tabid,blockid,sub_mode){
	
	if(sub_mode == 'showhiddenfields'){
	var selectedfields 	= document.getElementById('hiddenfield_assignid_'+blockid);
	var selectedids_str = '';
	for(var i=0; i<selectedfields.length; i++) {
		if (selectedfields[i].selected == true) {
			selectedids_str = selectedids_str + selectedfields[i].value + ":";
		}
	}
	}else{
		var selectedfields 	= document.getElementById('movefield_assignid_'+blockid);
		var selectedids_str = '';
		for(var i=0; i<selectedfields.length; i++) {
			if (selectedfields[i].selected == true) {
				selectedids_str = selectedids_str + selectedfields[i].value + ":";
			}
		}
	}
	$('vtbusy_info').style.display = "block";
	new Ajax.Request(
		'index.php',
		{queue: {position: 'end', scope: 'command'},
			method: 'post',
			postBody: 'module=ModuleBuilder&action=ModuleBuilderAjax&file=LayoutBlockList&sub_mode='+sub_mode+'&fld_module='+modulename+'&parenttab=Tools&ajax=true&tabid='+tabid+'&blockid='+blockid+'&selected='+selectedids_str,
			onComplete: function(response) {
				$("cfList").update(response.responseText);
				$('vtbusy_info').style.display = "none";
				}
			}
		);
}
	
function changeRelatedListorder(what_to_do,tabid,sequence,id,module)	
{
	$('vtbusy_info').style.display = "block";
	new Ajax.Request(
		'index.php',
		{queue: {position: 'end', scope: 'command'},
			method: 'post',
			postBody: 'module=ModuleBuilder&action=ModuleBuilderAjax&file=LayoutBlockList&sub_mode=changeRelatedInfoOrder&sequence='+sequence+'&fld_module='+module+'&parenttab=Tools&what_to_do='+what_to_do+'&tabid='+tabid+'&id='+id+'&ajax=true',
			onComplete: function(response) {
			$("relatedlistdiv").innerHTML=response.responseText;
			$('vtbusy_info').style.display = "none";
			}
		}
		
	);	
}	

function callRelatedList(module){
	$('vtbusy_info').style.display = "block";
	new Ajax.Request(
		'index.php',
		{queue: {position: 'end', scope: 'command'},
			method: 'post',
			postBody: 'module=ModuleBuilder&action=ModuleBuilderAjax&file=LayoutBlockList&sub_mode=getRelatedInfoOrder&parenttab=Tools&formodule='+module+'&ajax=true',
			onComplete: function(response) {
			$("relatedlistdiv").innerHTML=response.responseText;
			fnvshNrm('relatedlistdiv');
			$('vtbusy_info').style.display = "none";
			}
		}
		
	);
}

function showProperties(field,man,uitype){
	var str='<table class="small" cellpadding="2" cellspacing="0" border="0"><tr><th>'+field+'</th></tr>';
	if (man == 'M')
 		str = str+'<tr><td>{/literal}{$MOD.FIELD_IS_MANDATORY}{literal}</td></tr>';
	if(uitype!='')
	str = str+'<tr><td>'+uitype+'</td></tr>';
	str = str + '</table>';
	return str;
}



var gselected_fieldtype = '';


{/literal}
</script>
<div id = "layoutblock">
<div id="relatedlistdiv" style="display:none; position: absolute; width: 225px; left: 300px; top: 300px;"></div>
<br>

			
<table align="center" border="0" cellpadding="0" cellspacing="0" width="98%">
	<tr>
       	
			<table class="settingsSelUITopLine" border="0" cellpadding="5" cellspacing="0" width="100%">
				<tr>
					
					<td align="right" width="15%">
					</td>
					<td align="right" width="15%"><input type="button" class="crmButton create small" onclick="if(valid())fnvshobj(this,'addblock');" alt="{$MOD.ADD_BLOCK}" title="{$MOD.ADD_BLOCK}" value="{$MOD.ADD_BLOCK}"/>
					</td>
					&nbsp; <img src="{'vtbusy.gif'|@vtiger_imageurl:$THEME}" id="vtbusy_info" style="display:none;position:absolute;top:180px;right:100px;" border="0" />
				</tr>
				
			</table>
				
			<div id="cfList">
				{include file="modules/ModuleBuilder/tks_layoutblockentries.tpl"}
            </div>	
              
			
		</td>
	</tr>
</table>
		<!-- End of Display for field -->

</div>