{*<!--
/*************************************************************************************************
 * Copyright 2014 JPL TSolucio, S.L. -- This file is a part of TSOLUCIO coreBOS Customizations.
 * Licensed under the vtiger CRM Public License Version 1.1 (the "License"); you may not use this
 * file except in compliance with the License. You can redistribute it and/or modify it
 * under the terms of the License. JPL TSolucio, S.L. reserves all rights not expressly
 * granted by the License. coreBOS distributed by JPL TSolucio S.L. is distributed in
 * the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied
 * warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. Unless required by
 * applicable law or agreed to in writing, software distributed under the License is
 * distributed on an "AS IS" BASIS, WITHOUT ANY WARRANTIES OR CONDITIONS OF ANY KIND,
 * either express or implied. See the License for the specific language governing
 * permissions and limitations under the License. You may obtain a copy of the License
 * at <http://corebos.org/documentation/doku.php?id=en:devel:vpl11>
 *************************************************************************************************
 *  Extension    : Language Editor
 *  Version      : 5.5.0
 *  Author       : Opencubed
 * the code is based on the work of Gaëtan KRONEISEN technique@expert-web.fr and Pius Tschümperlin ep-t.ch
 *************************************************************************************************/
-->*}
<script type="text/javascript" src="include/js/customview.js"></script>
<script type="text/javascript">
{literal}
function lang_changeEditTab(obj1, obj2, SelTab, unSelTab) {
	var tabName1 = document.getElementById(obj1);
	var tabName2 = document.getElementById(obj2);
	var tagName1 = document.getElementById(SelTab);
	var tagName2 = document.getElementById(unSelTab);
	if (tabName1.className == "dvtUnSelectedCell") { tabName1.className = "dvtSelectedCell"; } 
	if (tabName2.className == "dvtSelectedCell") { tabName2.className = "dvtUnSelectedCell"; } 
	tagName1.style.display = "block"; 
	tagName2.style.display = "none";
}
{/literal}
{literal}
    var k=0;
	function changeModule(form){
		form.action.value='LanguageEdit'; 
		form.parenttab.value='Settings';
		form.submit();
	}
	function addLabel(){
		document.getElementById("tableofLabels").innerHTML+='<tr style="background-color:#FFF;"><td valign="top" class="listTableRow small"><input style="width:100%" name="newLabels['+k+'][key]"/></td><td valign="top" class="listTableRow small"><input style="width:100%" /></td><td class="listTableRow small" valign=top><input type="text" name="newLabels['+k+'][value]"  class="small" style="width:100%"></td></tr>';   
		k++; 
	}
{/literal}
</script>
<br>
<table align="center" border="0" cellpadding="0" cellspacing="0" width="98%">
	<tbody>
		<tr>
			<td valign="top"><img src="{$IMAGE_PATH}showPanelTopLeft.gif"></td>
			<td class="showPanelBg" style="padding: 10px;" valign="top" width="100%">
				<br>
				<div align=center>
					{include file='SetMenu.tpl'}
					<!-- table --> <!-- SetMenu.tpl -->
						<!-- tr --> <!-- SetMenu.tpl -->
							<!-- td --> <!-- SetMenu.tpl -->
								<!-- table --> <!-- SetMenu.tpl -->
									<!-- tr --> <!-- SetMenu.tpl -->
										<!-- td --> <!-- SetMenu.tpl -->
											<form id="modulefrom" action="index.php" method="POST">
												<input type="hidden" name="action" value="SettingsAjax">
												<input type="hidden" name="module" value="Settings">
												<input type="hidden" name="file">
												<input type="hidden" name="languageid" value="{$LANGUAGEID}">
												<input type="hidden" name="parenttab" value="Settings">
												<table class="settingsSelUITopLine" border="0" cellpadding="5" cellspacing="0" width="100%">
													<tbody>
														<tr>
															<td rowspan="2" valign="top" width="50"><img src="themes/images/languages.gif" alt="Language Translation" title="Language Translation" border="0" height="48" width="48"></td>
															<td class="heading2" valign="bottom"><b>{'LBL_SETTINGS'|@getTranslatedString} &gt; {$UMOD.LBL_LANGUAGES_PACKS} &gt; {$LANGUAGE}</b></td>
														</tr>
														<tr>
															<td class="small" valign="top">{$UMOD.LBL_EDIT_LANGUAGE_FILE}</td>
														</tr>
													</tbody>
												</table>
												<br>
												{if $APP.$MODULE eq ''}
													{if $UMOD.$MODULE eq ''}
														{assign var = "module_trad" value=$MODULE}
													{else}
														{assign var = "module_trad" value=$UMOD.$MODULE}
													{/if}
												{else}
													{assign var = "module_trad" value=$APP.$MODULE}
												{/if}
												<table class="tableHeading" border="0" cellpadding="5" cellspacing="0" width="100%">
													<tbody>
														<tr>
															<td class="big" nowrap><strong><span id="module_info">{$UMOD.LBL_TRANSLATE} "{$module_trad}" {$APP.LBL_MODULE}</span></strong> </td>
															<td class="small" align="right">
																{$UMOD.LBL_SELECT_TRANSLATE_MODULE}{$sel_value}
																<select name="pick_module" class="importBox" onChange="changeModule(this.form);">
																	{foreach key=sel_value item=value from=$MODULES}
																		{if $MODULE eq $sel_value}
																			{assign var = "selected_val" value="selected"}
																		{else}
																			{assign var = "selected_val" value=""}
																		{/if}
																		<option value="{$sel_value}" {$selected_val}>{$value}</option>
																	{/foreach}
																</select>
															</td>
														</tr>
													</tbody>
												</table>
												<div style="text-align:right;margin:3px;">
													<span style="color:#F00;font-weight:bold;">{$ERROR}</span>
													{$UMOD.LBL_TRADE_PERCENTAGE} <strong>{$PERC_TRANSALTED}</strong>
													<input value="{$APP.LBL_ADD_BUTTON}" class="crmButton small save" onclick="addLabel();" type="button"/>
													<input value="{$APP.LBL_SAVE_BUTTON_LABEL}" class="crmButton small save" onclick="this.form.file.value='LanguageSave';" type="submit"/>
													<input type="button" value="{$APP.LBL_CANCEL_BUTTON_LABEL}" class="crmButton small cancel" onclick="gotourl('index.php?module=Settings&action=ModuleManager&parenttab=Settings')"/>
												</div>
												<div style="text-align:right;margin:3px;"> {$APP.LBL_VIEW} 
													<select name='filter_translate' id='filter_translate' class="importBox" onchange='changeModule(this.form);'>
														<option value='all' {if $FILTER == 'all'}selected{/if}> {$APP.SHOW_ALL} </option>
														<option value='fieldsnontranslated' {if $FILTER == 'fieldsnontranslated'}selected{/if}>{$UMOD.FieldsNotTranslated}</option>
														<option value='fieldstranslated' {if $FILTER == 'fieldstranslated'}selected{/if}>{$UMOD.FieldsTranslated}</option>
														<option value='rltranslated' {if $FILTER == 'rltranslated'}selected{/if}> {$UMOD.RLTranslated}</option>
														<option value='rlnontranslated' {if $FILTER == 'rlnontranslated'}selected{/if}>{$UMOD.RLNotTranslated}</option>
													</select>
												</div>
												{if count($TRANSLATION_LIST_STRING) > 0}
													{assign var = "has_multivalue_strings" value=true}
												{else}
													{assign var = "has_multivalue_strings" value=false}
												{/if}
												<table border="0" cellspacing="0" cellpadding="0" width="100%">
													<tr>
														{if $has_multivalue_strings == true}
														<td class="dvtTabCache" style="width:10px" nowrap>&nbsp;</td>
														<td width=75 style="width:15%" align="center" nowrap class="dvtSelectedCell" id="bi" onclick="lang_changeEditTab('bi','mi','basicTab','moreTab')"><b>{$UMOD.LBL_SINGLEVALUE_STRINGS}</b></td>
														<td style="width: 10px;" class="dvtTabCache">&nbsp;</td>
														<td class="dvtUnSelectedCell" style="width:15%" align="center" nowrap id="mi" onclick="lang_changeEditTab('mi','bi','moreTab','basicTab')"><b>{$UMOD.LBL_MULTIVALUE_STRINGS}</b></td>
														{/if}
														<td class="dvtTabCache" style="width:65%" nowrap>&nbsp;</td>
													</tr>
												</table>
												<table border="0" cellspacing="0" cellpadding="5" width="100%">
												<tr>
													<td width="32%">{$UMOD.Legend}:</td>
													<td width="32%"><table width=100%><tr><td style="width:50%;height:30px;background-color:#87CEFA;text-align:center;">{$UMOD.RLTranslated}</td><td style="width:50%;height:30px;background-color:#0276FD;text-align:center;">{$UMOD.RLNotTranslated}</td></tr></table></td>
													<td width="33%"><table width=100%><tr><td style="width:50%;height:30px;background-color:#e3f9d9;text-align:center;">{$UMOD.FieldsTranslated}</td><td style="width:50%;height:30px;background-color:#FFC1C1;text-align:center;">{$UMOD.FieldsNotTranslated}</td></tr></table></td>
												</tr>
													<tr>
														<td width="32%" class="colHeader small">{$UMOD.LBL_KEY}</td>
														<td width="32%" class="colHeader small">{$REF_LANGUAGE}</td>
														<td width="33%" class="colHeader small">{$LANGUAGE}</td>
													</tr>
												</table>
												<div id="basicTab" style="width:100%;height:700px;overflow:auto;">
													<table style="width:100%" id="tableofLabels">
														<colgroup>
															<col width="33.33%">
															<col width="33.33%">
															<col width="33.33%">
														</colgroup>
														{foreach key=var_name item=string from=$TRANSLATION_STRING}
														{if $string[3] eq 'new'}
															{assign var = "color" value="#ffc4c4"}
														{elseif $string[3] eq 'not_translated'}
															{assign var = "color" value="#DADADA"}
														{elseif $string[3] eq 'rltranslated'}
															{assign var = "color" value="#87CEFA"}
														{elseif $string[3] eq 'rlnontranslated'}
															{assign var = "color" value="#0276FD"}
														{elseif $string[3] eq 'fieldsnontranslated'}
															{assign var = "color" value="#FFC1C1"}
														{elseif $string[3] eq 'fieldstranslated'}
															{assign var = "color" value="#e3f9d9"}
														{else}
															{assign var = "color" value="#FFF"}
														{/if}
														<tr style="background-color:{$color};">
															<td valign="top" class="listTableRow small">
																<span style="width:100%" >{$var_name}</span>
															</td>
															<td valign="top" class="listTableRow small">
																<span style="width:100%" >{$string[0]}</span>
															</td>
															<td class="listTableRow small" valign=top>
																{if $string[1]|count_paragraphs != 1 || $string[0]|count_characters:true > 50 || $string[1]|count_characters:true > 50}
																	<textarea style="width:100%" name="translate_value[{$string[2]}]" class="small">{if $string[3] eq 'new'}{$string[0]}{else}{$string[1]}{/if}</textarea>
																{else}
																	<input type="text" name="translate_value[{$string[2]}]" value="{if $string[3] eq 'new'}{$string[0]}{else}{$string[1]}{/if}" class="small" style="width:100%">
																{/if}
															</td>
														</tr>
														{/foreach}
														{foreach key=var_name item=string from=$HIDDEN_FIELDS}
															<input type="hidden" name="translate_value[{$string[2]}]" value="{if $string[3] eq 'new'}{$string[0]}{else}{$string[1]}{/if}">
														{/foreach}
													</table>
												</div>
												<div id="moreTab" style="width:100%;height:500px;overflow:auto;display:none;">
													<table style="width:100%">
														<colgroup>
															<col width="33.33%">
															<col width="33.33%">
															<col width="33.33%">
														</colgroup>
														{foreach key=list item=list_tab from=$TRANSLATION_LIST_STRING}
														<tr style="background-color:#CCC;"><td colspan="2"><div style="width:100%"><strong>{$list}</strong></div></td></tr>
														{foreach key=key_val item=string from=$list_tab}
														{if $string[3] eq 'new'}
															{assign var = "color" value="#ffc4c4"}
														{elseif $string[3] eq 'not_translated'}
															{assign var = "color" value="#DADADA"}
														{else}
															{assign var = "color" value="#FFF"}
														{/if}
														<tr style="background-color:{$color};">
															<td valign="top" class="listTableRow small">
																<span style="width:100%" >{$key_val}</span>
															</td>
															<td class="listTableRow small">
																<span style="width:100%" >{$string[0]}</span>
															</td>
															<td class="listTableRow small" valign=top>
																{if $string[1]|count_paragraphs != 1 || $string[0]|count_characters:true > 50 || $string[1]|count_characters:true > 50}
																<textarea style="width:100%" name="translate_list_value[{$list}][{$string[2]}]" class="small">{if $string[3] eq 'new'}{$string[0]}{else}{$string[1]}{/if}</textarea>
																{else}
																<input type="text" name="translate_list_value[{$list}][{$string[2]}]" value="{if $string[3] eq 'new'}{$string[0]}{else}{$string[1]}{/if}" class="small" style="width:100%">
																{/if}
															</td>
														</tr>
														{/foreach}
														{/foreach}
													</table>
												</div>
											</form>
										</td> <!-- SetMenu.tpl -->
									</tr> <!-- SetMenu.tpl -->
								</table> <!-- SetMenu.tpl -->
							</td> <!-- SetMenu.tpl -->
						</tr> <!-- SetMenu.tpl -->
					</table> <!-- SetMenu.tpl -->
				</div>
			</td>
			<td valign="top"><img src="{$IMAGE_PATH}showPanelTopRight.gif"></td>
		</tr>
	</tbody>
</table>