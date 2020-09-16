<script type="text/javascript" src="modules/evvtgendoc/evvtgendoc.js"></script>
{include file="Buttons_List.tpl"}
<form action="index.php?module=evvtgendoc&action=odt&record=" id="EditView" name="EditView" method="POST" onSubmit="return validateFields();">
<table class="showPanelBg" style="width:96%; margin: 5px auto; padding: 5px;">
<tr><td>&nbsp;</td></tr>
<tr><td style="padding:18px;"><div class="slds-form-element" style="width:30%;">
	<div class="slds-form-element__control">
	<label class="slds-form-element__label" for="recordval_type">{$APP.LBL_MODULE}</label>
	<div class="slds-select_container">
		<select name="recordval_type" id="recordval_type" onchange="this.form.recordval.value=''; this.form.recordval_display.value='';" class="slds-select slds-page-header__meta-text" required="">
		{foreach item=arr from=$MODULES}
			{if $arr neq 'Documents'}
				<option value="{$arr}">{$arr|@getTranslatedString:$arr}</option>
			{/if}
		{/foreach}
		</select>
	</div>
	</div>
</div>
</td><td>
<input id="recordval" name="recordval" type="hidden" value="">
<input type="text" id="recordval_display" name="recordval_display" readonly placeholder="{'LBL_SELECT'|@getTranslatedString}" class="slds-input" style="width:22%;border:1px solid #dddbda;" />
&nbsp;
<img id="entity"
	src="{'select.gif'|@vtiger_imageurl:$THEME}" alt="{'LBL_SELECT'|@getTranslatedString}" title="{'LBL_SELECT'|@getTranslatedString}" align="absmiddle" style='cursor:hand;cursor:pointer'
	onClick='return vtlib_open_popup_window("","recordval","evvtgendoc","");'>
<input type="image" src="{'clear_field.gif'|@vtiger_imageurl:$THEME}"
	alt="{'LBL_CLEAR'|@getTranslatedString}" title="{'LBL_CLEAR'|@getTranslatedString}" onClick="this.form.recordval.value=''; this.form.recordval_display.value=''; return false;" align="absmiddle" style='cursor:hand;cursor:pointer'>&nbsp;
</td></tr>

<tr><td></td><td><label id="template_error_msg" style="color:#D8000C;background-color: #FFBABA;"></label></td></tr>
<tr><td></td><td><label id="entity_error_msg" style="color:#D8000C;background-color: #FFBABA;"></label></td></tr>
<tr><td style="padding: 18px;"><label class="slds-form-element__label" for="form-element-01">{'LBL_DOCUMENT_TEMPLATE'|@getTranslatedString:'evvtgendoc'}</label></td>
<td><input id="gendoctemplate" name="gendoctemplate" type="hidden" value="">
<input id="gendoctemplate_type" name="gendoctemplate_type" type="hidden" value="Documents">
<input type="text" id="gendoctemplate_display" name="gendoctemplate_display" readonly placeholder="{'LBL_SELECT'|@getTranslatedString}" class="slds-input" style="width:22%;border:1px solid #dddbda;" />&nbsp;

<img src="{'select.gif'|@vtiger_imageurl:$THEME}"
	alt="{'LBL_SELECT'|@getTranslatedString}" title="{'LBL_SELECT'|@getTranslatedString}"
	{literal}onclick='window.open("index.php?module=Documents&action=Popup&html=Popup_picker&forfield=gendoctemplate&srcmodule=evvtgendoc&forrecord=&form=&query=true&search=true&searchtype=advance&advft_criteria=[{\"groupid\":\"1\",\"columnname\":\"vtiger_notes:template:template:Documents_Template:V\",\"comparator\":\"e\",\"value\":\"1\",\"columncondition\":\"and\"},{\"groupid\":\"1\",\"columnname\":\"vtiger_notes:template_for:template_for:Documents_Template_For:V\",\"comparator\":\"e\",\"value\":\""+document.getElementById("recordval_type").value+"\",\"columncondition\":\"\"}]&advft_criteria_groups=[null,{\"groupcondition\":\"\"}]", "vtlibui10", cbPopupWindowSettings);'{/literal}
	align="absmiddle" style='cursor:hand;cursor:pointer'>&nbsp;
<input type="image" src="{'clear_field.gif'|@vtiger_imageurl:$THEME}"
alt="{'LBL_CLEAR'|@getTranslatedString}" title="{'LBL_CLEAR'|@getTranslatedString}" onClick="this.form.gendoctemplate.value=''; this.form.gendoctemplate_display.value=''; return false;" align="absmiddle" style='cursor:hand;cursor:pointer'>&nbsp;
<br>
</td></tr>

<tr><td style="padding: 18px;"><label class="slds-form-element__label" for="form-element-01">{'LBL_LANG'|@getTranslatedString:'evvtgendoc'}</label></td><td>
<div class="slds-form-element" style="width:22%;">
	<label class="slds-form-element__label" for="select-01">
		<abbr class="slds-required" title="required"></abbr></label>
	<div class="slds-form-element__control">
		<div class="slds-select_container">
		<select name="compilelang" class="slds-select slds-page-header__meta-text" id="select-01" required="">
			{foreach item=clang from=$COMPILELANGS}
			<option value="{$clang}"{if $clang eq $USERLANG} selected{/if}>{$clang}</option>
			{/foreach}
		</select>
		</div>
	</div>
</div>
</td></tr>

<tr><td style="padding: 18px;">
	<label class="slds-form-element__label" for="form-element-01">{'LBL_DEBUG'|@getTranslatedString:'evvtgendoc'}</label>
	</td>
	<td>
	<div class="slds-form-element">
	<label class="slds-checkbox_toggle slds-grid">
		<span class="slds-form-element__label slds-m-bottom_none"></span>
		<input type="checkbox" name="debug" aria-describedby="checkbox-toggle-14" />
		<span id="debug" class="slds-checkbox_faux_container" aria-live="assertive">
		<span class="slds-checkbox_faux"></span>
		<span class="slds-checkbox_on"></span>
		<span class="slds-checkbox_off"></span>
		</span>
	</label>
	</div>
</td></tr>

<tr><td colspan=2 align="center">
<br>
<input name='gdformat' id='gdformat' type='hidden' value='oo'>
<input type='submit' class="slds-button slds-button_success" onclick="document.getElementById('gdformat').value='oo';" value='{'Export Doc'|@getTranslatedString:'evvtgendoc'}' title='{'Export Doc'|@getTranslatedString:'evvtgendoc'}'>
&nbsp;&nbsp;
<input type='submit' class="slds-button slds-button_success" onclick="document.getElementById('gdformat').value='pdf';" value='{'Export PDF'|@getTranslatedString:'evvtgendoc'}' title='{'Export PDF'|@getTranslatedString:'evvtgendoc'}'>
</td></tr>
</table>
</form>
<br><br>