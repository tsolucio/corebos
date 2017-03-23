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
		{assign var="uitype" value=$maindata[0][0]}
		{assign var="fldlabel" value=$maindata[1][0]}
		{assign var="fldlabel_sel" value=$maindata[1][1]}
		{assign var="fldlabel_combo" value=$maindata[1][2]}
		{assign var="fldlabel_other" value=$maindata[1][3]}
		{assign var="fldname" value=$maindata[2][0]|cat:$row_no}
		{assign var="fldvalue" value=$maindata[3][0]}
		{assign var="secondvalue" value=$maindata[3][1]}
		{assign var="thirdvalue" value=$maindata[3][2]}
		{assign var="typeofdata" value=$maindata[4]}
		{assign var="vt_tab" value=$maindata[5][0]}

		{if $typeofdata eq 'M'}
			{assign var="mandatory_field" value="*"}
		{else}
			{assign var="mandatory_field" value=""}
		{/if}

		{* vtlib customization: Help information for the fields *}
		{assign var="usefldlabel" value=$fldlabel}
		{assign var="fldhelplink" value=""}
		{if $FIELDHELPINFO && $FIELDHELPINFO.$fldname}
			{assign var="fldhelplinkimg" value='help_icon.gif'|@vtiger_imageurl:$THEME}
			{assign var="fldhelplink" value="<img style='cursor:pointer' onclick='vtlib_field_help_show(this, \"$fldname\");' border=0 src='$fldhelplinkimg'>"}
			{if $uitype neq '10'}
				{assign var="usefldlabel" value="$fldlabel $fldhelplink"}
			{/if}
		{/if}
		{* END *}

		{* vtlib customization *}
		{if $uitype eq '10'}
			<span class="mdCellLabel{if $mandatory_field == '*'} mandatory_field_label{/if}">
			<font color="red">{$mandatory_field}</font>
			{$fldlabel.displaylabel}

			{if count($fldlabel.options) eq 1}
				{assign var="use_parentmodule" value=$fldlabel.options.0}
				<input type='hidden' class='small' name="{$fldname}_type" id="{$fldname}_type" value="{$use_parentmodule}">
				{assign var=vtui10func value=$use_parentmodule|getvtlib_open_popup_window_function:$fldname:$MODULE}
			{else}
			{assign var=vtui10func value="vtlib_open_popup_window"}
			<br>
			{if $fromlink eq 'qcreate'}
			<select id="{$fldname}_type" class="small" name="{$fldname}_type" onChange='document.QcEditView.{$fldname}_display.value=""; document.QcEditView.{$fldname}.value="";'>
			{else}
			<select id="{$fldname}_type" class="small" name="{$fldname}_type" onChange='document.EditView.{$fldname}_display.value=""; document.EditView.{$fldname}.value="";document.getElementById("qcform").innerHTML=""'>
			{/if}
			{foreach item=option from=$fldlabel.options}
				<option value="{$option}"
				{if $fldlabel.selected == $option}selected{/if}>
				{$option|@getTranslatedString:$option}
				</option>
			{/foreach}
			</select>
			{/if}
			{$fldhelplink}

			</span>
			<span width="30%" class="mdCellInfo">
				<input id="{$fldname}" name="{$fldname}" type="hidden" value="{$fldvalue.entityid}">
				<input id="{$fldname}_display" name="{$fldname}_display" readonly type="text" style="border:1px solid #bababa;" value="{$fldvalue.displayvalue}">&nbsp;
				<img src="{'select.gif'|@vtiger_imageurl:$THEME}" tabindex="{$vt_tab}"
alt="{'LBL_SELECT'|@getTranslatedString}" title="{'LBL_SELECT'|@getTranslatedString}" onclick='return {$vtui10func}("{$fromlink}","{$fldname}","{$MODULE}","{$ID}");' align="absmiddle" style='cursor:hand;cursor:pointer'>&nbsp;
				<input type="image" src="{'clear_field.gif'|@vtiger_imageurl:$THEME}"
alt="{'LBL_CLEAR'|@getTranslatedString}" title="{'LBL_CLEAR'|@getTranslatedString}" onClick="this.form.{$fldname}.value=''; this.form.{$fldname}_display.value=''; return false;" align="absmiddle" style='cursor:hand;cursor:pointer'>&nbsp;
			</span>

		{elseif $uitype eq 2}
			<span class="mdCellLabel{if $mandatory_field == '*'} mandatory_field_label{/if}">
				<font color="red">{$mandatory_field}</font>{$usefldlabel}
			</span>
			<span class="mdCellInfo">
				<input type="text" name="{$fldname}" tabindex="{$vt_tab}" value="{$fldvalue}" tabindex="{$vt_tab}" class=detailedViewTextBox onFocus="this.className='detailedViewTextBoxOn'" onBlur="this.className='detailedViewTextBox'">
			</span>
		{elseif $uitype eq 3 || $uitype eq 4}<!-- Non Editable field, only configured value will be loaded -->
			<span class="mdCellLabel{if $mandatory_field == '*'} mandatory_field_label{/if}"><font color="red">{$mandatory_field}</font>{$usefldlabel}</span>
			<span class="mdCellInfo"><input readonly type="text" tabindex="{$vt_tab}" name="{$fldname}" id ="{$fldname}" {if $MODE eq 'edit'} value="{$fldvalue}" {else} value="{$MOD_SEQ_ID}" {/if} class=detailedViewTextBox onFocus="this.className='detailedViewTextBoxOn'" onBlur="this.className='detailedViewTextBox'"></span>
		{elseif $uitype eq 11 || $uitype eq 1 || $uitype eq 13 || $uitype eq 7}
			<span class="mdCellLabel{if $mandatory_field == '*'} mandatory_field_label{/if}"><font color="red">{$mandatory_field}</font>{$usefldlabel}</span>
			<span class="mdCellInfo"><input type="text" tabindex="{$vt_tab}" name="{$fldname}" id ="{$fldname}" value="{$fldvalue}" class=detailedViewTextBox onFocus="this.className='detailedViewTextBoxOn'" onBlur="this.className='detailedViewTextBox'"></span>
		{elseif $uitype eq 9}
			<span class="mdCellLabel{if $mandatory_field == '*'} mandatory_field_label{/if}"><font color="red">{$mandatory_field}</font>{$usefldlabel} {$APP.COVERED_PERCENTAGE}</span>
			<span class="mdCellInfo"><input type="text" tabindex="{$vt_tab}" name="{$fldname}" id ="{$fldname}" value="{$fldvalue}" class=detailedViewTextBox onFocus="this.className='detailedViewTextBoxOn'" onBlur="this.className='detailedViewTextBox'"></span>
		{elseif $uitype eq 19 || $uitype eq 20}
			<!-- In Add Comment are we should not display anything -->
			{if $fldlabel eq $MOD.LBL_ADD_COMMENT}
				{assign var=fldvalue value=""}
			{/if}
			<span class="mdCellLabel{if $mandatory_field == '*'} mandatory_field_label{/if}">
					<font color="red">{$mandatory_field}</font>
				{$usefldlabel}
			</span>
			<span colspan=3 class="mdCellInfo">
				<textarea class="detailedViewTextBox" tabindex="{$vt_tab}" onFocus="this.className='detailedViewTextBoxOn'" name="{$fldname}"  onBlur="this.className='detailedViewTextBox'" cols="90" rows="8">{$fldvalue}</textarea>
				{if $fldlabel eq $MOD.Solution}
				<input type = "hidden" name="helpdesk_solution" value = '{$fldvalue}'>
				{/if}
			</span>
		{elseif $uitype eq 21 || $uitype eq 24}
			<span class="mdCellLabel{if $mandatory_field == '*'} mandatory_field_label{/if}">
					<font color="red">{$mandatory_field}</font>
				{$usefldlabel}
			</span>
			<span class="mdCellInfo">
				<textarea value="{$fldvalue}" name="{$fldname}" tabindex="{$vt_tab}" class=detailedViewTextBox onFocus="this.className='detailedViewTextBoxOn'" onBlur="this.className='detailedViewTextBox'" rows=2>{$fldvalue}</textarea>
			</span>
		{elseif $uitype eq 15 || $uitype eq 16  || $uitype eq '31' || $uitype eq '32' || $uitype eq '1613' || $uitype eq '1614'}
			<span width="20%" class="mdCellLabel{if $mandatory_field == '*'} mandatory_field_label{/if}">
				<font color="red">{$mandatory_field}</font>
				{$usefldlabel}
			</span>
			<span width="30%" class="mdCellInfo">
				{if $MODULE eq 'Calendar'}
					<select name="{$fldname}" id="{$fldname}" tabindex="{$vt_tab}" class="small" style="width:160px;">
				{else}
					<select name="{$fldname}" id="{$fldname}" tabindex="{$vt_tab}" class="small" style="width:280px;">
				{/if}
				{foreach item=arr from=$fldvalue}
					{if $arr[0] eq $APP.LBL_NOT_ACCESSIBLE}
					<option value="{$arr[0]}" {$arr[2]}>{$arr[0]}</option>
					{else}
					<option value="{$arr[1]}" {$arr[2]}>{$arr[0]}</option>
					{/if}
				{foreachelse}
					<option value=""></option>
					<option value="" style='color: #777777' disabled>{$APP.LBL_NONE}</option>
				{/foreach}
				</select>
			</span>
		{elseif $uitype eq 33 || $uitype eq 3313 || $uitype eq 3314 || $uitype eq 1024}
			<span width="20%" class="mdCellLabel{if $mandatory_field == '*'} mandatory_field_label{/if}">
				<font color="red">{$mandatory_field}</font>{$usefldlabel}
			</span>
			<span width="30%" class="mdCellInfo">
				<select MULTIPLE name="{$fldname}[]" size="4" style="width:280px;" tabindex="{$vt_tab}" class="small">
				{foreach item=arr from=$fldvalue}
					<option value="{$arr[1]}" {$arr[2]}>{$arr[0]}</option>
				{/foreach}
				</select>
			</span>

		{elseif $uitype eq 53}
			<span width="20%" class="mdCellLabel{if $mandatory_field == '*'} mandatory_field_label{/if}">
				<font color="red">{$mandatory_field}</font>{$usefldlabel}
			</span>
			<span width="30%" class="mdCellInfo">
				{assign var=check value=1}
				{foreach key=key_one item=arr from=$fldvalue}
					{foreach key=sel_value item=value from=$arr}
						{if $value ne ''}
							{assign var=check value=$check*0}
						{else}
							{assign var=check value=$check*1}
						{/if}
					{/foreach}
				{/foreach}

				{if $check eq 0}
					{assign var=select_user value='checked'}
					{assign var=style_user value='display:block'}
					{assign var=style_group value='display:none'}
				{else}
					{assign var=select_group value='checked'}
					{assign var=style_user value='display:none'}
					{assign var=style_group value='display:block'}
				{/if}

				<input type="radio" tabindex="{$vt_tab}" name="assigntype" {$select_user} value="U" onclick="toggleAssignType(this.value)" >&nbsp;{$APP.LBL_USER}

				{if $secondvalue neq ''}
					<input type="radio" name="assigntype" {$select_group} value="T" onclick="toggleAssignType(this.value)">&nbsp;{$APP.LBL_GROUP}
				{/if}

				<span id="assign_user" style="{$style_user}">
					<select name="{$fldname}" class="small">
						{foreach key=key_one item=arr from=$fldvalue}
							{foreach key=sel_value item=value from=$arr}
								<option value="{$key_one}" {$value}>{$sel_value}</option>
							{/foreach}
						{/foreach}
					</select>
				</span>

				{if $secondvalue neq ''}
					<span id="assign_team" style="{$style_group}">
						<select name="assigned_group_id" class="small">
							{foreach key=key_one item=arr from=$secondvalue}
								{foreach key=sel_value item=value from=$arr}
									<option value="{$key_one}" {$value}>{$sel_value}</option>
								{/foreach}
							{/foreach}
						</select>
					</span>
				{/if}
			</span>
		{elseif $uitype eq 52 || $uitype eq 77}
			<span width="20%" class="mdCellLabel{if $mandatory_field == '*'} mandatory_field_label{/if}{if $mandatory_field == '*'} mandatory_field_label{/if}">
				<font color="red">{$mandatory_field}</font>{$usefldlabel}
			</span>
			<span width="30%" class="mdCellInfo">
				{if $uitype eq 52}
					<select name="{$fldname}" tabindex="{$vt_tab}" class="small">
				{elseif $uitype eq 77}
					<select name="{$fldname}" tabindex="{$vt_tab}" class="small">
				{else}
					<select name="{$fldname}" tabindex="{$vt_tab}" class="small">
				{/if}

				{foreach key=key_one item=arr from=$fldvalue}
					{foreach key=sel_value item=value from=$arr}
						<option value="{$key_one}" {$value}>{$sel_value}</option>
					{/foreach}
				{/foreach}
				</select>
			</span>
		{elseif $uitype eq 51}
			{if $MODULE eq 'Accounts'}
				{assign var='popuptype' value = 'specific_account_address'}
			{else}
				{assign var='popuptype' value = 'specific_contact_account_address'}
			{/if}
			<span width="20%" class="mdCellLabel{if $mandatory_field == '*'} mandatory_field_label{/if}{if $mandatory_field == '*'} mandatory_field_label{/if}">
				<font color="red">{$mandatory_field}</font>{$usefldlabel}
			</span>
			<span width="30%" class="mdCellInfo">
				<input readonly name="account_name" style="border:1px solid #bababa;" type="text" value="{$fldvalue}"><input name="{$fldname}" type="hidden" value="{$secondvalue}">&nbsp;<img tabindex="{$vt_tab}" src="{'select.gif'|@vtiger_imageurl:$THEME}" alt="{$APP.LBL_SELECT}" title="{$APP.LBL_SELECT}" onclick='return window.open("index.php?module=Accounts&action=Popup&popuptype={$popuptype}&form=TasksEditView&form_submit=false&fromlink={$fromlink}&recordid={$ID}","test{if $fromlink eq 'qcreate'}qc{/if}","width=640,height=602,resizable=0,scrollbars=0");' align="absmiddle" style='cursor:hand;cursor:pointer'>&nbsp;<input type="image" src="{'clear_field.gif'|@vtiger_imageurl:$THEME}" alt="{$APP.LBL_CLEAR}" title="{$APP.LBL_CLEAR}" onClick="this.form.account_id.value=''; this.form.account_name.value='';return false;" align="absmiddle" style='cursor:hand;cursor:pointer'>
			</span>
		{elseif $uitype eq 50}
			<span width="20%" class="mdCellLabel{if $mandatory_field == '*'} mandatory_field_label{/if}{if $mandatory_field == '*'} mandatory_field_label{/if}">
				<font color="red">{$mandatory_field}</font>{$usefldlabel}
			</span>
			<span width="30%" class="mdCellInfo">
				<input readonly name="account_name" type="text" value="{$fldvalue}"><input name="{$fldname}" type="hidden" value="{$secondvalue}">&nbsp;<img src="{'select.gif'|@vtiger_imageurl:$THEME}" alt="{$APP.LBL_SELECT}" title="{$APP.LBL_SELECT}" onclick='return window.open("index.php?module=Accounts&action=Popup&popuptype=specific&form=TasksEditView&form_submit=false&fromlink={$fromlink}","test","width=640,height=602,resizable=0,scrollbars=0");' align="absmiddle" style='cursor:hand;cursor:pointer'>
				<input type="image" src="{'clear_field.gif'|@vtiger_imageurl:$THEME}" alt="{$APP.LBL_CLEAR}" title="{$APP.LBL_CLEAR}" onClick="this.form.account_id.value=''; this.form.account_name.value='';return false;" align="absmiddle" style='cursor:hand;cursor:pointer'>
			</span>
		{elseif $uitype eq 73}
			<span width="20%" class="mdCellLabel{if $mandatory_field == '*'} mandatory_field_label{/if}">
				<font color="red">{$mandatory_field}</font>{$usefldlabel}
			</span>
			<span width="30%" class="mdCellInfo">
				<input readonly name="account_name" id = "single_accountid" type="text" value="{$fldvalue}"><input name="{$fldname}" type="hidden" value="{$secondvalue}">&nbsp;<img src="{'select.gif'|@vtiger_imageurl:$THEME}" alt="{$APP.LBL_SELECT}" title="{$APP.LBL_SELECT}" onclick='return window.open("index.php?module=Accounts&action=Popup&popuptype=specific_account_address&form=TasksEditView&form_submit=false&fromlink={$fromlink}","test","width=640,height=602,resizable=0,scrollbars=0");' align="absmiddle" style='cursor:hand;cursor:pointer'>
				<input type="image" src="{'clear_field.gif'|@vtiger_imageurl:$THEME}" alt="{$APP.LBL_CLEAR}" title="{$APP.LBL_CLEAR}" onClick="this.form.account_id.value=''; this.form.account_name.value='';return false;" align="absmiddle" style='cursor:hand;cursor:pointer'>
			</span>

		{elseif $uitype eq 75 || $uitype eq 81}
			<span width="20%" class="mdCellLabel{if $mandatory_field == '*'} mandatory_field_label{/if}">
				{if $uitype eq 81}
					{assign var="pop_type" value="specific_vendor_address"}
					{else}{assign var="pop_type" value="specific"}
				{/if}
				<font color="red">{$mandatory_field}</font>{$usefldlabel}
			</span>
			<span width="30%" class="mdCellInfo">
				<input name="vendor_name" id="vendor_name" readonly type="text" style="border:1px solid #bababa;" value="{$fldvalue}"><input name="{$fldname}" id="{$fldname}" type="hidden" value="{$secondvalue}">&nbsp;<img src="{'select.gif'|@vtiger_imageurl:$THEME}" alt="{$APP.LBL_SELECT}" title="{$APP.LBL_SELECT}" onclick='return window.open("index.php?module=Vendors&action=Popup&html=Popup_picker&popuptype={$pop_type}&form=EditView&fromlink={$fromlink}","test","width=640,height=602,resizable=0,scrollbars=0");' align="absmiddle" style='cursor:hand;cursor:pointer'>
				<input type="image" tabindex="{$vt_tab}" src="{'clear_field.gif'|@vtiger_imageurl:$THEME}" alt="{$APP.LBL_CLEAR}" title="{$APP.LBL_CLEAR}" onClick="this.form.vendor_id.value='';this.form.vendor_name.value='';return false;" align="absmiddle" style='cursor:hand;cursor:pointer'>
			</span>
		{elseif $uitype eq 57}
			<span width="20%" class="mdCellLabel{if $mandatory_field == '*'} mandatory_field_label{/if}">
				<font color="red">{$mandatory_field}</font>{$usefldlabel}
			</span>
			<span width="30%" class="mdCellInfo">
				{if $fromlink eq 'qcreate'}
					<input name="contact_name" readonly type="text" style="border:1px solid #bababa;" value="{$fldvalue}"><input name="{$fldname}" type="hidden" value="{$secondvalue}">&nbsp;<img src="{'select.gif'|@vtiger_imageurl:$THEME}" alt="{$APP.LBL_SELECT}" title="{$APP.LBL_SELECT}" onclick='selectContact("false","general",document.QcEditView)' align="absmiddle" style='cursor:hand;cursor:pointer'>&nbsp;<input type="image" tabindex="{$vt_tab}" src="{'clear_field.gif'|@vtiger_imageurl:$THEME}" alt="{$APP.LBL_CLEAR}" title="{$APP.LBL_CLEAR}" onClick="this.form.contact_id.value=''; this.form.contact_name.value='';return false;" align="absmiddle" style='cursor:hand;cursor:pointer'>
				{else}
					<input name="contact_name" readonly type="text" style="border:1px solid #bababa;" value="{$fldvalue}"><input name="{$fldname}" type="hidden" value="{$secondvalue}">&nbsp;<img src="{'select.gif'|@vtiger_imageurl:$THEME}" alt="{$APP.LBL_SELECT}" title="{$APP.LBL_SELECT}" onclick='selectContact("false","general",document.EditView)' align="absmiddle" style='cursor:hand;cursor:pointer'>&nbsp;<input type="image" tabindex="{$vt_tab}" src="{'clear_field.gif'|@vtiger_imageurl:$THEME}" alt="{$APP.LBL_CLEAR}" title="{$APP.LBL_CLEAR}" onClick="this.form.contact_id.value=''; this.form.contact_name.value='';return false;" align="absmiddle" style='cursor:hand;cursor:pointer'>
				{/if}
			</span>

		{elseif $uitype eq 80}
			<span width="20%" class="mdCellLabel{if $mandatory_field == '*'} mandatory_field_label{/if}">
				<font color="red">{$mandatory_field}</font>{$usefldlabel}
			</span>
			<span width="30%" class="mdCellInfo">
				<input name="salesorder_name" readonly type="text" style="border:1px solid #bababa;" value="{$fldvalue}"><input name="{$fldname}" type="hidden" value="{$secondvalue}">&nbsp;<img src="{'select.gif'|@vtiger_imageurl:$THEME}" alt="{$APP.LBL_SELECT}" title="{$APP.LBL_SELECT}" onclick='selectSalesOrder();' align="absmiddle" style='cursor:hand;cursor:pointer'>&nbsp;<input type="image" tabindex="{$vt_tab}" src="{'clear_field.gif'|@vtiger_imageurl:$THEME}" alt="{$APP.LBL_CLEAR}" title="{$APP.LBL_CLEAR}" onClick="this.form.salesorder_id.value=''; this.form.salesorder_name.value='';return false;" align="absmiddle" style='cursor:hand;cursor:pointer'>
			</span>

		{elseif $uitype eq 78}
			<span width="20%" class="mdCellLabel{if $mandatory_field == '*'} mandatory_field_label{/if}">
				<font color="red">{$mandatory_field}</font>{$usefldlabel}
			</span>
			<span width="30%" class="mdCellInfo">
				<input name="quote_name" readonly type="text" style="border:1px solid #bababa;" value="{$fldvalue}"><input name="{$fldname}" type="hidden" value="{$secondvalue}" >&nbsp;<img src="{'select.gif'|@vtiger_imageurl:$THEME}" alt="{$APP.LBL_SELECT}" title="{$APP.LBL_SELECT}" onclick='selectQuote()' align="absmiddle" style='cursor:hand;cursor:pointer' >&nbsp;<input type="image" tabindex="{$vt_tab}" src="{'clear_field.gif'|@vtiger_imageurl:$THEME}" alt="{$APP.LBL_CLEAR}" title="{$APP.LBL_CLEAR}" onClick="this.form.quote_id.value=''; this.form.quote_name.value='';return false;" align="absmiddle" style='cursor:hand;cursor:pointer'>
			</span>

		{elseif $uitype eq 76}
			<span width="20%" class="mdCellLabel{if $mandatory_field == '*'} mandatory_field_label{/if}">
				<font color="red">{$mandatory_field}</font>{$usefldlabel}
			</span>
			<span width="30%" class="mdCellInfo">
				<input name="potential_name" readonly type="text" style="border:1px solid #bababa;" value="{$fldvalue}"><input name="{$fldname}" type="hidden" value="{$secondvalue}">&nbsp;<img tabindex="{$vt_tab}" src="{'select.gif'|@vtiger_imageurl:$THEME}" alt="{$APP.LBL_SELECT}" title="{$APP.LBL_SELECT}" onclick='selectPotential()' align="absmiddle" style='cursor:hand;cursor:pointer'>&nbsp;<input type="image" src="{'clear_field.gif'|@vtiger_imageurl:$THEME}" alt="{$APP.LBL_CLEAR}" title="{$APP.LBL_CLEAR}" onClick="this.form.potential_id.value=''; this.form.potential_name.value='';return false;" align="absmiddle" style='cursor:hand;cursor:pointer'>
			</span>

		{elseif $uitype eq 17}
			<span width="20%" class="mdCellLabel{if $mandatory_field == '*'} mandatory_field_label{/if}">
				<font color="red">{$mandatory_field}</font>{$usefldlabel}
			</span>
			<span width="30%" class="mdCellInfo">
			<input style="width:74%;" class = 'detailedViewTextBox' type="text" tabindex="{$vt_tab}" name="{$fldname}" style="border:1px solid #bababa;" size="27" onFocus="this.className='detailedViewTextBoxOn'"onBlur="this.className='detailedViewTextBox'" onkeyup="validateUrl('{$fldname}');" value="{$fldvalue}">
			</span>

		{elseif $uitype eq 85}
			<span width="20%" class="mdCellLabel{if $mandatory_field == '*'} mandatory_field_label{/if}">
				<font color="red">{$mandatory_field}</font>
				{$usefldlabel}
			</span>
			<span width="30%" class="mdCellInfo">
				<img src="{'skype.gif'|@vtiger_imageurl:$THEME}" alt="Skype" title="Skype" align="absmiddle"></img>
				<input class='detailedViewTextBox' type="text" tabindex="{$vt_tab}" name="{$fldname}" style="border:1px solid #bababa;" size="27" onFocus="this.className='detailedViewTextBoxOn'" onBlur="this.className='detailedViewTextBox'" value="{$fldvalue}">
			</span>

		{elseif $uitype eq 71 || $uitype eq 72}
			<span width="20%" class="mdCellLabel{if $mandatory_field == '*'} mandatory_field_label{/if}">
				<font color="red">{$mandatory_field}</font>{$usefldlabel}
			</span>
			<span width="30%" class="mdCellInfo">
				<input name="{$fldname}" tabindex="{$vt_tab}" type="text" class=detailedViewTextBox onFocus="this.className='detailedViewTextBoxOn'" onBlur="this.className='detailedViewTextBox'"  value="{$fldvalue}">
			</span>

		{elseif $uitype eq 56}
			<span width="20%" class="mdCellLabel{if $mandatory_field == '*'} mandatory_field_label{/if}">
				<font color="red">{$mandatory_field}</font>{$usefldlabel}
			</span>

			{if $fldname eq 'notime' && $ACTIVITY_MODE eq 'Events'}
				{if $fldvalue eq 1}
					<span width="30%" class="mdCellInfo">
						<input name="{$fldname}" type="checkbox" tabindex="{$vt_tab}" onclick="toggleTime()" checked>
					</span>
				{else}
					<span width="30%" class="mdCellInfo">
						<input name="{$fldname}" tabindex="{$vt_tab}" type="checkbox" onclick="toggleTime()" >
					</span>
				{/if}
			<!-- For Portal Information we need a hidden field existing_portal with the current portal value -->
			{elseif $fldname eq 'portal'}
				<span width="30%" class="mdCellInfo">
					<input type="hidden" name="existing_portal" value="{$fldvalue}">
					<input name="{$fldname}" type="checkbox" tabindex="{$vt_tab}" {if $fldvalue eq 1}checked{/if}>
				</span>
			{else}
				{if $fldvalue eq 1}
					<span width="30%" class="mdCellInfo">
						<input name="{$fldname}" type="checkbox" tabindex="{$vt_tab}" checked>
					</span>
				{elseif $fldname eq 'filestatus'&& $MODE eq 'create'}
					<span width="30%" class="mdCellInfo">
						<input name="{$fldname}" type="checkbox" tabindex="{$vt_tab}" checked>
					</span>
				{else}
					<span width="30%" class="mdCellInfo">
						<input name="{$fldname}" tabindex="{$vt_tab}" type="checkbox">
					</span>
				{/if}
			{/if}
		{elseif $uitype eq 23 || $uitype eq 5 || $uitype eq 6}
			<span width="20%" class="mdCellLabel{if $mandatory_field == '*'} mandatory_field_label{/if}">
				<font color="red">{$mandatory_field}</font>{$usefldlabel}
			</span>
			<span width="30%" class="mdCellInfo">
				{foreach key=date_value item=time_value from=$fldvalue}
					{assign var=date_val value="$date_value"}
					{assign var=time_val value="$time_value"}
				{/foreach}

				<input name="{$fldname}" tabindex="{$vt_tab}" id="jscal_field_{$fldname}" type="text" style="border:1px solid #bababa;" size="11" maxlength="10" value="{$date_val}">
				<img src="{'btnL3Calendar.gif'|@vtiger_imageurl:$THEME}" id="jscal_trigger_{$fldname}">

				{if $uitype eq 6}
					<input name="time_start" tabindex="{$vt_tab}" style="border:1px solid #bababa;" size="5" maxlength="5" type="text" value="{$time_val}">
				{/if}

				{if $uitype eq 6 && $QCMODULE eq 'Event'}
					<input name="dateFormat" type="hidden" value="{$dateFormat}">
				{/if}
				{if $uitype eq 23 && $QCMODULE eq 'Event'}
					<input name="time_end" style="border:1px solid #bababa;" size="5" maxlength="5" type="text" value="{$time_val}">
				{/if}

				{foreach key=date_format item=date_str from=$secondvalue}
					{assign var=dateFormat value="$date_format"}
					{assign var=dateStr value="$date_str"}
				{/foreach}

				{if $uitype eq 5 || $uitype eq 23}
					<br><font size=1><em old="(yyyy-mm-dd)">({$dateStr})</em></font>
				{else}
					<br><font size=1><em old="(yyyy-mm-dd)">({$dateStr})</em></font>
				{/if}

				<script type="text/javascript" id='massedit_calendar_{$fldname}'>
					Calendar.setup ({ldelim}
						inputField : "jscal_field_{$fldname}", ifFormat : "{$dateFormat}", showsTime : false, button : "jscal_trigger_{$fldname}", singleClick : true, step : 1
					{rdelim})
				</script>
			</span>

		{elseif $uitype eq 63}
			<span width="20%" class="mdCellLabel{if $mandatory_field == '*'} mandatory_field_label{/if}">
				<font color="red">{$mandatory_field}</font>{$usefldlabel}
			</span>
			<span width="30%" class="mdCellInfo">
				<input name="{$fldname}" type="text" size="2" value="{$fldvalue}" tabindex="{$vt_tab}" >&nbsp;
				<select name="duration_minutes" tabindex="{$vt_tab}" class="small">
					{foreach key=labelval item=selectval from=$secondvalue}
						<option value="{$labelval}" {$selectval}>{$labelval}</option>
					{/foreach}
				</select>

		{elseif $uitype eq 68 || $uitype eq 66 || $uitype eq 62}
			<span width="20%" class="mdCellLabel{if $mandatory_field == '*'} mandatory_field_label{/if}">
				<font color="red">{$mandatory_field}</font>
				{if $fromlink eq 'qcreate'}
					<select class="small" name="parent_type" onChange='document.QcEditView.parent_name.value=""; document.QcEditView.parent_id.value=""'>
				{else}
					<select class="small" name="parent_type" onChange='document.EditView.parent_name.value=""; document.EditView.parent_id.value=""'>
				{/if}
					{section name=combo loop=$fldlabel}
						<option value="{$fldlabel_combo[combo]}" {$fldlabel_sel[combo]}>{$fldlabel[combo]} </option>
					{/section}
				</select>
			</span>
			<span width="30%" class="mdCellInfo">
				<input name="{$fldname}" type="hidden" value="{$secondvalue}">
				<input name="parent_name" readonly id = "parentid" type="text" style="border:1px solid #bababa;" value="{$fldvalue}">
				&nbsp;
				{if $fromlink eq 'qcreate'}
					<img src="{'select.gif'|@vtiger_imageurl:$THEME}" tabindex="{$vt_tab}" alt="{$APP.LBL_SELECT}" title="{$APP.LBL_SELECT}" onclick='return window.open("index.php?module="+ document.QcEditView.parent_type.value +"&action=Popup&html=Popup_picker&form=HelpDeskEditView&fromlink={$fromlink}","test","width=640,height=602,resizable=0,scrollbars=0,top=150,left=200");' align="absmiddle" style='cursor:hand;cursor:pointer'>&nbsp;<input type="image" src="{'clear_field.gif'|@vtiger_imageurl:$THEME}" alt="{$APP.LBL_CLEAR}" title="{$APP.LBL_CLEAR}" onClick="this.form.parent_id.value=''; this.form.parent_name.value=''; return false;" align="absmiddle" style='cursor:hand;cursor:pointer'>
				{else}
					<img src="{'select.gif'|@vtiger_imageurl:$THEME}" tabindex="{$vt_tab}" alt="{$APP.LBL_SELECT}" title="{$APP.LBL_SELECT}" onclick='return window.open("index.php?module="+ document.EditView.parent_type.value +"&action=Popup&html=Popup_picker&form=HelpDeskEditView&fromlink={$fromlink}","test","width=640,height=602,resizable=0,scrollbars=0,top=150,left=200");' align="absmiddle" style='cursor:hand;cursor:pointer'>&nbsp;<input type="image" src="{'clear_field.gif'|@vtiger_imageurl:$THEME}" alt="{$APP.LBL_CLEAR}" title="{$APP.LBL_CLEAR}" onClick="this.form.parent_id.value=''; this.form.parent_name.value=''; return false;" align="absmiddle" style='cursor:hand;cursor:pointer'>
					{/if}
			</span>

		{elseif $uitype eq 59}
			<span width="20%" class="mdCellLabel{if $mandatory_field == '*'} mandatory_field_label{/if}">
				<font color="red">{$mandatory_field}</font>{$usefldlabel}
			</span>
			<span width="30%" class="mdCellInfo">
				<input name="{$fldname}" type="hidden" value="{$secondvalue}">
				<input name="product_name" readonly type="text" value="{$fldvalue}">&nbsp;<img tabindex="{$vt_tab}" src="{'select.gif'|@vtiger_imageurl:$THEME}" alt="{$APP.LBL_SELECT}" title="{$APP.LBL_SELECT}" onclick='return window.open("index.php?module=Products&action=Popup&html=Popup_picker&form=HelpDeskEditView&popuptype=specific&fromlink={$fromlink}","test","width=640,height=602,resizable=0,scrollbars=0,top=150,left=200");' align="absmiddle" style='cursor:hand;cursor:pointer'>&nbsp;<input type="image" src="{'clear_field.gif'|@vtiger_imageurl:$THEME}" alt="{$APP.LBL_CLEAR}" title="{$APP.LBL_CLEAR}" onClick="this.form.product_id.value=''; this.form.product_name.value=''; return false;" align="absmiddle" style='cursor:hand;cursor:pointer'>
			</span>

		{elseif $uitype eq 55 || $uitype eq 255}
			<span width="20%" class="mdCellLabel{if $mandatory_field == '*'} mandatory_field_label{/if}">
			{if $uitype eq 55}
				{$usefldlabel}
			{elseif $uitype eq 255}
				<font color="red">{$mandatory_field}</font>{$usefldlabel}
			{/if}
			</span>

			<span width="30%" class="mdCellInfo">
			{if $fldvalue neq ''}
			<select name="salutationtype" class="small">
				{foreach item=arr from=$fldvalue}
				<option value="{$arr[1]}" {$arr[2]}>
				{$arr[0]}
				</option>
				{/foreach}
			</select>
			{/if}
			<input type="text" name="{$fldname}" tabindex="{$vt_tab}" class=detailedViewTextBox onFocus="this.className='detailedViewTextBoxOn'" onBlur="this.className='detailedViewTextBox'" style="width:58%;" value= "{$secondvalue}" >
			</span>

		{elseif $uitype eq 22}
			<span width="20%" class="mdCellLabel{if $mandatory_field == '*'} mandatory_field_label{/if}">
				<font color="red">{$mandatory_field}</font>{$usefldlabel}
			</span>
			<span width="30%" class="mdCellInfo">
				<textarea name="{$fldname}" cols="30" tabindex="{$vt_tab}" rows="2">{$fldvalue}</textarea>
			</span>
		{elseif $uitype eq 14}
			<span width="20%" class="mdCellLabel{if $mandatory_field == '*'} mandatory_field_label{/if}">
				<font color="red">{$mandatory_field}</font>{$usefldlabel} {"LBL_TIMEFIELD"|@getTranslatedString}
			</span>
			<span width=10% class="mdCellInfo">
				<input type="text" tabindex="{$vt_tab}" name="{$fldname}" id ="{$fldname}" value="{$fldvalue}" class=detailedViewTextBox onFocus="this.className='detailedViewTextBoxOn'" onBlur="this.className='detailedViewTextBox'">
			</span>

		{elseif $uitype eq 69}
			<span width="20%" class="mdCellLabel{if $mandatory_field == '*'} mandatory_field_label{/if}">
				<font color="red">{$mandatory_field}</font>{$usefldlabel}
			</span>
			<span colspan="1" width="30%" class="mdCellInfo">
				{if $MODULE eq 'Products'}
					<input name="del_file_list" type="hidden" value="">
					<div id="files_list" style="border: 1px solid grey; width: 500px; padding: 5px; background: rgb(255, 255, 255) none repeat scroll 0%; -moz-background-clip: initial; -moz-background-origin: initial; -moz-background-inline-policy: initial; font-size: x-small">
						<span id="limitmsg" style= "color:red;"> {'LBL_MAX_SIZE'|@getTranslatedString:$MODULE} {$UPLOADSIZE}{'LBL_FILESIZEIN_MB'|@getTranslatedString:$MODULE}, {$APP.Files_Maximum}{$Product_Maximum_Number_Images}</span>
						<input id="my_file_element" type="file" name="file_1" tabindex="{$vt_tab}"  onchange="validateFilename(this)"/>
						<!--input type="hidden" name="file_1_hidden" value=""/-->
						{assign var=image_count value=0}
						{if isset($maindata[3].0.name) && $maindata[3].0.name neq '' && $DUPLICATE neq 'true'}
							{foreach name=image_loop key=num item=image_details from=$maindata[3]}
							<div align="center">
								<img src="{$image_details.path}{$image_details.name}" height="50">&nbsp;&nbsp;[{$image_details.orgname}]<input id="file_{$num}" value="{'LBL_DELETE_BUTTON'|@getTranslatedString}" type="button" class="crmbutton small delete" onclick='this.parentNode.parentNode.removeChild(this.parentNode);delRowEmt("{$image_details.orgname}")'>
							</div>
							{assign var=image_count value=$smarty.foreach.image_loop.iteration}
							{/foreach}
						{/if}
					</div>

					<script>
						{*<!-- Create an instance of the multiSelector class, pass it the output target and the max number of files -->*}
						var multi_selector = new MultiSelector( document.getElementById( 'files_list' ), {$Product_Maximum_Number_Images} );
						multi_selector.count = {$image_count};
						{*<!-- Pass in the file element -->*}
						multi_selector.addElement( document.getElementById( 'my_file_element' ) );
					</script>
				{else}
					<span id="limitmsg" style= "color:red;"> {'LBL_MAX_SIZE'|@getTranslatedString:$MODULE} {$UPLOADSIZE}{'LBL_FILESIZEIN_MB'|@getTranslatedString:$MODULE}<br /></span>
					{if isset($maindata[3].0.name) && $maindata[3].0.name != "" && $DUPLICATE neq 'true'}
					  {assign var=imagevalueexists value=true}
					{else}
					  {assign var=imagevalueexists value=false}
					{/if}
					<input name="{$fldname}"  type="file" value="{if $imagevalueexists}{$maindata[3].0.name}{/if}" tabindex="{$vt_tab}" onchange="validateFilename(this);" />
					<input name="{$fldname}_hidden"  type="hidden" value="{if $imagevalueexists}{$maindata[3].0.name}{/if}" />
					{if $imagevalueexists}
						<div id="{$fldname}_replaceimage">[{$maindata[3].0.orgname}] <input id="{$fldname}_attach" value="{'LBL_DELETE_BUTTON'|@getTranslatedString}" type="button" class="crmbutton small delete" onclick='delimage({$ID},"{$fldname}","{$maindata[3].0.orgname}");'></div>
					{/if}
				{/if}
			</span>

		{elseif $uitype eq 61}
			<span width="20%" class="mdCellLabel{if $mandatory_field == '*'} mandatory_field_label{/if}">
				<font color="red">{$mandatory_field}</font>{$usefldlabel}
			</span>

			<span colspan="1" width="30%" class="mdCellInfo">
				<input name="{$fldname}"  type="file" value="{$secondvalue}" tabindex="{$vt_tab}" onchange="validateFilename(this)"/>
				<input type="hidden" name="{$fldname}_hidden" value="{$secondvalue}"/>
				<input type="hidden" name="id" value=""/>{$fldvalue}
			</span>
		{elseif $uitype eq 156}
			<span width="20%" class="mdCellLabel{if $mandatory_field == '*'} mandatory_field_label{/if}">
				<font color="red">{$mandatory_field}</font>{$usefldlabel}
			</span>
				{if $fldvalue eq 'on'}
					<span width="30%" class="mdCellInfo">
						{if ($secondvalue eq 1 && $CURRENT_USERID != $smarty.request.record) || ($MODE == 'create')}
							<input name="{$fldname}" tabindex="{$vt_tab}" type="checkbox" checked>
						{else}
							<input name="{$fldname}" type="hidden" value="on">
							<input name="{$fldname}" disabled tabindex="{$vt_tab}" type="checkbox" checked>
						{/if}
					</span>
				{else}
					<span width="30%" class="mdCellInfo">
						{if ($secondvalue eq 1 && $CURRENT_USERID != $smarty.request.record) || ($MODE == 'create')}
							<input name="{$fldname}" tabindex="{$vt_tab}" type="checkbox">
						{else}
							<input name="{$fldname}" disabled tabindex="{$vt_tab}" type="checkbox">
						{/if}
					</span>
				{/if}
		{elseif $uitype eq 98}<!-- Role Selection Popup -->
			<span width="20%" class="mdCellLabel{if $mandatory_field == '*'} mandatory_field_label{/if}">
				<font color="red">{$mandatory_field}</font>{$usefldlabel}
			</span>
			<span width="30%" class="mdCellInfo">
			{if $thirdvalue eq 1}
				<input name="role_name" id="role_name" readonly class="txtBox" tabindex="{$vt_tab}" value="{$secondvalue}" type="text">&nbsp;
				<a href="javascript:openPopup();"><img src="{'select.gif'|@vtiger_imageurl:$THEME}" align="absmiddle" border="0"></a>
			{else}
				<input name="role_name" id="role_name" tabindex="{$vt_tab}" class="txtBox" readonly value="{$secondvalue}" type="text">&nbsp;
			{/if}
			<input name="user_role" id="user_role" value="{$fldvalue}" type="hidden">
			</span>
		{elseif $uitype eq 104}<!-- Mandatory Email Fields -->
			 <span class="mdCellLabel{if $mandatory_field == '*'} mandatory_field_label{/if}">
			<font color="red">{$mandatory_field}</font>{$usefldlabel}
			</span>
			<span class="mdCellInfo"><input type="text" name="{$fldname}" id ="{$fldname}" value="{$fldvalue}" tabindex="{$vt_tab}" class=detailedViewTextBox onFocus="this.className='detailedViewTextBoxOn'" onBlur="this.className='detailedViewTextBox'"></span>
		{elseif $uitype eq 115}<!-- for Status field Disabled for nonadmin -->
			<span width="20%" class="mdCellLabel{if $mandatory_field == '*'} mandatory_field_label{/if}">
				<font color="red">{$mandatory_field}</font>{$usefldlabel}
			</span>
			<span width="30%" class="mdCellInfo">
			{if $secondvalue eq 1 && $CURRENT_USERID != $smarty.request.record}
				<select id="user_status" name="{$fldname}" tabindex="{$vt_tab}" class="small">
			{else}
				<select id="user_status" disabled name="{$fldname}" class="small">
			{/if}
				{foreach item=arr from=$fldvalue}
				<option value="{$arr[1]}" {$arr[2]}>{$arr[0]}</option>
				{/foreach}
			</select>
			</span>
		{elseif $uitype eq 105}
			<span width="20%" class="mdCellLabel{if $mandatory_field == '*'} mandatory_field_label{/if}">
				<font color="red">{$mandatory_field}</font>{$usefldlabel}
			</span>
			<span width="30%" class="mdCellInfo">
				{if $MODE eq 'edit' && $IMAGENAME neq ''}
					<input name="{$fldname}" type="file" value="{$maindata[3].0.name}" tabindex="{$vt_tab}" onchange="validateFilename(this);" /><div id="replaceimage">[{$IMAGENAME}]&nbsp;<a href="javascript:;" onClick="delUserImage({$ID})">Del</a></div>
					<br>{'LBL_IMG_FORMATS'|@getTranslatedString:$MODULE}
					<input name="{$fldname}_hidden"  type="hidden" value="{$maindata[3].0.name}" />
				{else}
					<input name="{$fldname}" type="file" value="" tabindex="{$vt_tab}" onchange="validateFilename(this);" /><br>{'LBL_IMG_FORMATS'|@getTranslatedString:$MODULE}
					<input name="{$fldname}_hidden"  type="hidden" value="" />
				{/if}
					<div id="displaySize"></div>
					<input type="hidden" name="id" value=""/>
					{if isset($maindata[3].0.name) }
					{$maindata[3].0.name}
					{/if}
			</span>
			{elseif $uitype eq 103}
			<span width="20%" class="mdCellLabel{if $mandatory_field == '*'} mandatory_field_label{/if}">
				<font color="red">{$mandatory_field}</font>{$usefldlabel}
			</span>
			<span width="30%" colspan="3" class="mdCellInfo">
				<input type="text" name="{$fldname}" value="{$fldvalue}" tabindex="{$vt_tab}" class=detailedViewTextBox onFocus="this.className='detailedViewTextBoxOn'" onBlur="this.className='detailedViewTextBox'">
			</span>
			{elseif $uitype eq 101}<!-- for reportsto field USERS POPUP -->
				<span width="20%" class="mdCellLabel{if $mandatory_field == '*'} mandatory_field_label{/if}">
					<font color="red">{$mandatory_field}</font>{$usefldlabel}
				</span>
				<span width="30%" class="mdCellInfo">
					<input id="{$fldname}_display" name="{$fldname}_display" readonly type="text" style="border:1px solid #bababa;" value="{$fldvalue}" class="small" />&nbsp;
					<input id="{$fldname}" name="{$fldname}" type="hidden" value="{$secondvalue}" id="{$fldname}" />
					&nbsp;<input title="{$APP.LBL_CHANGE_TITLE}" accessKey="C" type="button" class="small" value='{$APP.LBL_CHANGE}' name="btn1" onclick='return window.open("index.php?module=Users&action=Popup&html=Popup_picker&form=vtlibPopupView&form_submit=false&fromlink={$fromlink}&recordid={$ID}&forfield={$fldname}","test","width=640,height=603,resizable=0,scrollbars=0");'>
					&nbsp;<input type="image" src="{'clear_field.gif'|@vtiger_imageurl:$THEME}" alt="{$APP.LBL_CLEAR}" title="{$APP.LBL_CLEAR}" onClick="this.form.{$fldname}.value=''; this.form.{$fldname}_display.value=''; return false;" align="absmiddle" style='cursor:hand;cursor:pointer'>
				</span>
			{elseif $uitype eq 116 || $uitype eq 117}<!-- for currency in users details-->
			<span width="20%" class="mdCellLabel{if $mandatory_field == '*'} mandatory_field_label{/if}">
				<font color="red">{$mandatory_field}</font>{$usefldlabel}
			</span>
			<span width="30%" class="mdCellInfo">
			   {if $secondvalue eq 1 || $uitype eq 117}
			   	<select name="{$fldname}" tabindex="{$vt_tab}" class="small">
			   {else}
			   	<select disabled name="{$fldname}" tabindex="{$vt_tab}" class="small">
			   {/if}

				{foreach item=arr key=uivalueid from=$fldvalue}
					{foreach key=sel_value item=value from=$arr}
						<option value="{$uivalueid}" {$value}>{$sel_value|@getTranslatedCurrencyString}</option>
						<!-- code added to pass Currency field value, if Disabled for nonadmin -->
						{if $value eq 'selected' && $secondvalue neq 1}
							{assign var="curr_stat" value="$uivalueid"}
						{/if}
						<!--code ends -->
					{/foreach}
				{/foreach}
			   </select>
			<!-- code added to pass Currency field value, if Disabled for nonadmin -->
			{if $curr_stat neq '' && $uitype neq 117}
				<input name="{$fldname}" type="hidden" value="{$curr_stat}">
			{/if}
			<!--code ends -->
			</span>
			{elseif $uitype eq 106}
			<span class="mdCellLabel{if $mandatory_field == '*'} mandatory_field_label{/if}">
				<font color="red">{$mandatory_field}</font>{$usefldlabel}
			</span>
			<span class="mdCellInfo">
				{if $MODE eq 'edit'}
				<input type="text" readonly name="{$fldname}" value="{$fldvalue}" tabindex="{$vt_tab}" class=detailedViewTextBox onFocus="this.className='detailedViewTextBoxOn'" onBlur="this.className='detailedViewTextBox'">
				{else}
				<input type="text" name="{$fldname}" value="{$fldvalue}" tabindex="{$vt_tab}" class=detailedViewTextBox onFocus="this.className='detailedViewTextBoxOn'" onBlur="this.className='detailedViewTextBox'">
				{/if}
			</span>
			{elseif $uitype eq 99}
				{if $MODE eq 'create'}
				<span class="mdCellLabel{if $mandatory_field == '*'} mandatory_field_label{/if}">
					<font color="red">{$mandatory_field}</font>{$usefldlabel}
				</span>
				<span class="mdCellInfo">
					<input type="password" name="{$fldname}" tabindex="{$vt_tab}" value="{$fldvalue}" class=detailedViewTextBox onFocus="this.className='detailedViewTextBoxOn'" onBlur="this.className='detailedViewTextBox'">
				</span>
				{/if}
		{elseif $uitype eq 30}
			<span width="20%" class="mdCellLabel{if $mandatory_field == '*'} mandatory_field_label{/if}">
				<font color="red">{$mandatory_field}</font>{$usefldlabel}
			</span>
			<span colspan="3" width="30%" class="mdCellInfo">
				{assign var=check value=$secondvalue[0]}
				{assign var=yes_val value=$secondvalue[1]}
				{assign var=no_val value=$secondvalue[2]}

				<input type="radio" name="set_reminder" tabindex="{$vt_tab}" value="Yes" {$check}>&nbsp;{$yes_val}&nbsp;
				<input type="radio" name="set_reminder" value="No">&nbsp;{$no_val}&nbsp;

				{foreach item=val_arr from=$fldvalue}
					{assign var=start value=$val_arr[0]}
					{assign var=end value=$val_arr[1]}
					{assign var=sendname value=$val_arr[2]}
					{assign var=disp_text value=$val_arr[3]}
					{assign var=sel_val value=$val_arr[4]}
					<select name="{$sendname}" class="small">
						{section name=reminder start=$start max=$end loop=$end step=1 }
							{if $smarty.section.reminder.index eq $sel_val}
								{assign var=sel_value value="SELECTED"}
							{else}
								{assign var=sel_value value=""}
							{/if}
							<OPTION VALUE="{$smarty.section.reminder.index}" "{$sel_value}">{$smarty.section.reminder.index}</OPTION>
						{/section}
					</select>
					&nbsp;{$disp_text}
				{/foreach}
			</span>
		{elseif $uitype eq 26}
		<span width="20%" class="mdCellLabel{if $mandatory_field == '*'} mandatory_field_label{/if}">
		<font color="red">{$mandatory_field}</font>{$fldlabel}
		</span>
		<span width="30%" class="mdCellInfo">
			<select name="{$fldname}" tabindex="{$vt_tab}" class="small">
				{foreach item=v key=k from=$fldvalue}
				<option value="{$k}">{$v}</option>
				{/foreach}
			</select>
		</span>
		{elseif $uitype eq 27}
		<span width="20%" class="mdCellLabel{if $mandatory_field == '*'} mandatory_field_label{/if}" align="right" >
			<font color="red">{$mandatory_field}</font>{$fldlabel_other}&nbsp;
		</span>
		<span width="30%" class="mdCellInfo">
			<select class="small" name="{$fldname}" onchange="changeDldType((this.value=='I')? 'file': 'text');">
				{section name=combo loop=$fldlabel}
					<option value="{$fldlabel_combo[combo]}" {$fldlabel_sel[combo]} >{$fldlabel[combo]} </option>
				{/section}
			</select>
			<script>
				function vtiger_{$fldname}Init(){ldelim}
					var d = document.getElementsByName('{$fldname}')[0];
					var type = (d.value=='I')? 'file': 'text';

					changeDldType(type, true);
				{rdelim}
				if(typeof window.onload =='function'){ldelim}
					var oldOnLoad = window.onload;
					document.body.onload = function(){ldelim}
						vtiger_{$fldname}Init();
						oldOnLoad();
					{rdelim}
				{rdelim}else{ldelim}
					window.onload = function(){ldelim}
						vtiger_{$fldname}Init();
					{rdelim}
				{rdelim}

			</script>
		</span>
		{else}
			{* just show field on screen *}
			<span class="mdCellLabel{if $mandatory_field == '*'} mandatory_field_label{/if}">{$fldlabel}</span>
			<span class="mdCellInfo">
				{if $fldname neq ''}<input type="hidden" name="{$fldname}" id="{$fldname}" value="{$fldvalue.fieldsavevalue}">{/if}{$fldvalue.fieldshowvalue}
			</span>
		{/if}
