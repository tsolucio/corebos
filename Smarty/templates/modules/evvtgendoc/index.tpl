<script type="text/javascript" src="modules/evvtgendoc/evvtgendoc.js"></script>
{include file="Buttons_List.tpl"}
<form action="index.php?module=evvtgendoc&action=odt&record=" id="EditView" name="EditView" method="POST" onSubmit="return validateFields();">
<table class="showPanelBg" style="margin-left: 15px;">
<tr><td>&nbsp;</td></tr>
<tr><td style="padding: 18px;" width="50%" >
{$APP.LBL_MODULE} <select name="recordval_type" id="recordval_type" class="small" onchange="this.form.recordval.value=''; this.form.recordval_display.value='';">
{foreach item=arr from=$MODULES}
  {if $arr neq 'Documents'}<option value="{$arr}">{$arr|@getTranslatedString:$arr}</option>{/if}
{/foreach}
</select>
</td><td>
<input id="recordval" name="recordval" type="hidden" value="">
<input id="recordval_display" name="recordval_display" readonly type="text" style="border:1px solid #bababa;" value="">&nbsp;
<img id="entity"
  src="{'select.gif'|@vtiger_imageurl:$THEME}" alt="{'LBL_SELECT'|@getTranslatedString}" title="{'LBL_SELECT'|@getTranslatedString}" align="absmiddle" style='cursor:hand;cursor:pointer'
  onClick='return vtlib_open_popup_window("","recordval","evvtgendoc","");'>
<input type="image" src="{'clear_field.gif'|@vtiger_imageurl:$THEME}"
alt="{'LBL_CLEAR'|@getTranslatedString}" title="{'LBL_CLEAR'|@getTranslatedString}" onClick="this.form.recordval.value=''; this.form.recordval_display.value=''; return false;" align="absmiddle" style='cursor:hand;cursor:pointer'>&nbsp;
</td></tr>

  <tr><td></td><td><label id="template_error_msg" style="color:#D8000C;background-color: #FFBABA;"></label></td></tr>
  <tr><td></td><td><label id="entity_error_msg" style="color:#D8000C;background-color: #FFBABA;"></label></td></tr>
  <tr><td style="padding: 18px;" width="50%">{'LBL_DOCUMENT_TEMPLATE'|@getTranslatedString:'evvtgendoc'}</td>
<td><input id="gendoctemplate" name="gendoctemplate" type="hidden" value="">
<input id="gendoctemplate_type" name="gendoctemplate_type" type="hidden" value="Documents">
<input id="gendoctemplate_display" name="gendoctemplate_display" id="gendoctemplate_display" readonly type="text" style="border:1px solid #bababa;" value="">&nbsp;

<img src="{'select.gif'|@vtiger_imageurl:$THEME}"
alt="{'LBL_SELECT'|@getTranslatedString}" title="{'LBL_SELECT'|@getTranslatedString}" {literal}onclick='window.open("index.php?module=Documents&action=Popup&html=Popup_picker&forfield=gendoctemplate&srcmodule=evvtgendoc&forrecord=&form=&query=true&search=true&searchtype=advance&advft_criteria=[{\"groupid\":\"1\",\"columnname\":\"vtiger_notes:template:template:Documents_Template:V\",\"comparator\":\"e\",\"value\":\"1\",\"columncondition\":\"and\"},{\"groupid\":\"1\",\"columnname\":\"vtiger_notes:template_for:template_for:Documents_Template_For:V\",\"comparator\":\"e\",\"value\":\""+document.getElementById("recordval_type").value+"\",\"columncondition\":\"\"}]&advft_criteria_groups=[null,{\"groupcondition\":\"\"}]","vtlibui10","width=680,height=602,resizable=0,scrollbars=0,top=150,left=200");'{/literal} align="absmiddle" style='cursor:hand;cursor:pointer'>&nbsp;
<input type="image" src="{'clear_field.gif'|@vtiger_imageurl:$THEME}"
alt="{'LBL_CLEAR'|@getTranslatedString}" title="{'LBL_CLEAR'|@getTranslatedString}" onClick="this.form.gendoctemplate.value=''; this.form.gendoctemplate_display.value=''; return false;" align="absmiddle" style='cursor:hand;cursor:pointer'>&nbsp;
<br>
</td></tr>

<tr><td style="padding: 18px;" width="50%" >{'LBL_LANG'|@getTranslatedString:'evvtgendoc'}</td><td>
<select name="compilelang">
{foreach item=clang from=$COMPILELANGS}
<option value="{$clang}"{if $clang eq $USERLANG} selected{/if}>{$clang}</option>
{/foreach}
</select>
</td></tr>

<tr><td style="padding: 18px;" width="50%" >{'LBL_DEBUG'|@getTranslatedString:'evvtgendoc'}</td><td><input type="checkbox" name="debug"></td></tr>

<tr><td colspan=2 align="center">
<br>
<input name='gdformat' id='gdformat' type='hidden' value='oo'>
<input type='submit' class='crmbutton small edit' onclick="document.getElementById('gdformat').value='oo';" value='{'Export Doc'|@getTranslatedString:'evvtgendoc'}' title='{'Export Doc'|@getTranslatedString:'evvtgendoc'}'>
&nbsp;&nbsp;
<input type='submit' class='crmbutton small edit' onclick="document.getElementById('gdformat').value='pdf';" value='{'Export PDF'|@getTranslatedString:'evvtgendoc'}' title='{'Export PDF'|@getTranslatedString:'evvtgendoc'}'>
</td></tr>
</table>
</form>
<br><br>