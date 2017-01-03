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

<!-- This file is used to display the fields based on the ui type in detailview -->
		{if $keyid eq '1' || $keyid eq 2 || $keyid eq '11' || $keyid eq '7' || $keyid eq '9' || $keyid eq '55' || $keyid eq '71' || $keyid eq '72' || $keyid eq '103' || $keyid eq '255' || $keyid eq '14'} <!--TextBox-->
			<td width=25% class="dvtCellInfo" align="left" id="mouseArea_{$keyfldname}" onmouseover="hndMouseOver({$keyid},'{$keyfldname}');" onmouseout="fnhide('crmspanid');" onclick='handleEdit(event);'>
				{if $keyid eq '55' || $keyid eq '255'}<!--SalutationSymbol-->
					{if $keyaccess eq $APP.LBL_NOT_ACCESSIBLE}
						<font color='red'>{$APP.LBL_NOT_ACCESSIBLE}</font>
					{else}
						{$keysalut}
					{/if}
				{/if}

				{if $keyid eq 11 && $USE_ASTERISK eq 'true'}
					&nbsp;&nbsp;<span id="dtlview_{$keyfldname}"><a href='javascript:;' onclick='startCall("{$keyval}", "{$ID}")'>{$keyval}</a></span>
				{else}
					&nbsp;&nbsp;<span id="dtlview_{$keyfldname}">{$keyval}</span>
				{/if}
                <div id="editarea_{$keyfldname}" style="display:none;">
                	<input class="detailedViewTextBox" onFocus="this.className='detailedViewTextBoxOn'" onBlur="this.className='detailedViewTextBox'" type="text" id="txtbox_{$keyfldname}" name="{$keyfldname}" maxlength='100' value="{$keyval}"></input>
                    <br>
                    <a href="javascript:;" class="detailview_ajaxbutton ajax_save_detailview" onclick="dtlViewAjaxSave('{$keyfldname}','{$MODULE}',{$keyid},'{$keytblname}','{$keyfldname}','{$ID}');fnhide('crmspanid');event.stopPropagation();"/>
                    	{$APP.LBL_SAVE_LABEL}
                    </a>
                    <a href="javascript:;" onclick="hndCancel('dtlview_{$keyfldname}','editarea_{$keyfldname}','{$keyfldname}');event.stopPropagation();" class="detailview_ajaxbutton ajax_cancelsave_detailview">
                    	{$APP.LBL_CANCEL_BUTTON_LABEL}
                    </a>
                </div>
                {if $keyid eq '71' && $keyfldname eq 'unit_price'}
                	{if $PRICE_DETAILS|@count > 0}
						<span id="multiple_currencies" width="38%" style="align:right;">
							&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="javascript:void(0);" onclick="toggleShowHide('currency_class','multiple_currencies');">{$APP.LBL_MORE_CURRENCIES} &raquo;</a>
						</span>

						<div id="currency_class" class="multiCurrencyDetailUI">
							<table width="100%" height="100%" class="small" cellpadding="5">
							<tr>
								<th colspan="2">
									<b>{$MOD.LBL_PRODUCT_PRICES}</b>
								</th>
								<th align="right">
									<img border="0" style="cursor: pointer;" onclick="toggleShowHide('multiple_currencies','currency_class');" src="{'close.gif'|@vtiger_imageurl:$THEME}"/>
								</th>
							</tr>
							<tr class="detailedViewHeader">
								<th>{$APP.LBL_CURRENCY}</th>
								<th colspan="2">{$APP.LBL_PRICE}</th>
							</tr>
							{foreach item=price key=count from=$PRICE_DETAILS}
								<tr>
									{*if $price.check_value eq 1*}
									<td class="dvtCellLabel" width="40%">
										{$price.currencylabel|@getTranslatedCurrencyString} ({$price.currencysymbol})
									</td>
									<td class="dvtCellInfo" width="60%" colspan="2">
										{$price.curvalue}
									</td>
								</tr>
							{/foreach}
							</table>
						</div>
					{/if}
                {/if}
            </td>
        {elseif $keyid eq '13' || $keyid eq '104'} <!--Email-->
            <td width=25% class="dvtCellInfo" align="left" id="mouseArea_{$keyfldname}" onmouseover="hndMouseOver({$keyid},'{$keyfldname}');" onmouseout="fnhide('crmspanid');" onclick='handleEdit(event);'><span id="dtlview_{$keyfldname}">
				{if $smarty.session.internal_mailer eq 1}
					<a href="javascript:InternalMailer({$ID},{$keyfldid},'{$keyfldname}','{$MODULE}','record_id');" onclick="event.stopPropagation();">{$keyval}</a>
				{else}
					<a href="mailto:{$keyval}" target="_blank" onclick="event.stopPropagation();">{$keyval}</a>
				{/if}
				</span>
                <div id="editarea_{$keyfldname}" style="display:none;">
                	<input class="detailedViewTextBox" onFocus="this.className='detailedViewTextBoxOn'" onBlur="this.className='detailedViewTextBox'" type="text" id="txtbox_{$keyfldname}" name="{$keyfldname}" maxlength='100' value="{$keyval}"></input>
                	<br><a href="javascript:;" class="detailview_ajaxbutton ajax_save_detailview" onclick="dtlViewAjaxSave('{$keyfldname}','{$MODULE}',{$keyid},'{$keytblname}','{$keyfldname}','{$ID}');fnhide('crmspanid');event.stopPropagation();">{$APP.LBL_SAVE_LABEL}</a>
                	<a href="javascript:;" onclick="hndCancel('dtlview_{$keyfldname}','editarea_{$keyfldname}','{$keyfldname}');event.stopPropagation();" class="detailview_ajaxbutton ajax_cancelsave_detailview">{$APP.LBL_CANCEL_BUTTON_LABEL}</a>
                </div>
				<div id="internal_mailer_{$keyfldname}" style="display: none;">{$keyfldid}####{$smarty.session.internal_mailer}</div>
                                                  </td>
	 {elseif ($keyid eq '15' || $keyid eq '16' || $keyid eq '1613' || $keyid eq '1614') && !picklistHasDependency($keyfldname,$MODULE)} <!--ComboBox-->
						{foreach item=arr from=$keyoptions}
							{if $arr[0] eq $APP.LBL_NOT_ACCESSIBLE && $arr[2] eq 'selected'}
								{assign var=keyval value=$APP.LBL_NOT_ACCESSIBLE}
								{assign var=fontval value='red'}
							{else}
                                                                {assign var=fontval value=''}
							{/if}
						{/foreach}
							<td width=25% class="dvtCellInfo" align="left" id="mouseArea_{$keyfldname}" onmouseover="hndMouseOver({$keyid},'{$keyfldname}');" onmouseout="fnhide('crmspanid');" onclick='handleEdit(event);'><span id="dtlview_{$keyfldname}"><font color="{$fontval}">{$keyval|@getTranslatedString:$MODULE}</font></span>
                                              		<div id="editarea_{$keyfldname}" style="display:none;">
                    							   <select id="txtbox_{$keyfldname}" name="{$keyfldname}" class="small" style="width:280px;">
                    								{foreach item=arr from=$keyoptions}
											{if $arr[0] eq $APP.LBL_NOT_ACCESSIBLE}
                    									<option value="{$arr[0]}" {$arr[2]}>{$arr[0]}</option>
											{else}
        							                                <option value="{$arr[1]}" {$arr[2]}>
							                                                {$arr[0]}
								                                </option>
							                                {/if}

										{/foreach}
                    							   </select>
                    							   <br><a class="detailview_ajaxbutton ajax_save_detailview" onclick="dtlViewAjaxSave('{$keyfldname}','{$MODULE}',{$keyid},'{$keytblname}','{$keyfldname}','{$ID}');fnhide('crmspanid');event.stopPropagation();">{$APP.LBL_SAVE_LABEL}</a>
                                              		   <a href="javascript:;" onclick="hndCancel('dtlview_{$keyfldname}','editarea_{$keyfldname}','{$keyfldname}');event.stopPropagation();" class="detailview_ajaxbutton ajax_cancelsave_detailview">{$APP.LBL_CANCEL_BUTTON_LABEL}</a>
                    							</div>
               							</td>
                                          {elseif $keyid eq '33' || $keyid eq '3313' || $keyid eq '3314'}<!--Multi Select Combo box-->
						<!--{assign var="MULTISELECT_COMBO_BOX_ITEM_SEPARATOR_STRING" value=", "}  {* Separates Multi-Select Combo Box items *}
						{assign var="DETAILVIEW_WORDWRAP_WIDTH" value="70"} {* No. of chars for word wrapping long lines of Multi-Select Combo Box items *}-->
                                          <td width=25% class="dvtCellInfo" align="left" id="mouseArea_{$keyfldname}" onmouseover="hndMouseOver({$keyid},'{$keyfldname}');" onmouseout="fnhide('crmspanid');" onclick='handleEdit(event);'>&nbsp;<span id="dtlview_{$keyfldname}">
					{assign var=selected_val value=''}
					{foreach item=sel_val from=$keyoptions }
						{if $sel_val[2] eq 'selected'}
							{if $selected_val neq ''}
							{assign var=selected_val value=$selected_val|cat:', '}
							{/if}
							{assign var=selected_val value=$selected_val|cat:$sel_val[0]}
						{/if}
					{/foreach}
						{$selected_val|replace:"\n":"<br>&nbsp;&nbsp;"}
						<!-- commented to fix ticket4631 -using wordwrap will affect Not Accessible font color -->
						<!--{$selected_val|replace:$MULTISELECT_COMBO_BOX_ITEM_SEPARATOR_STRING:"\x1"|replace:" ":"\x0"|replace:"\x1":$MULTISELECT_COMBO_BOX_ITEM_SEPARATOR_STRING|wordwrap:$DETAILVIEW_WORDWRAP_WIDTH:"<br>&nbsp;"|replace:"\x0":"&nbsp;"}-->
						</span>
						<!--code given by Neil End-->
                                          <div id="editarea_{$keyfldname}" style="display:none;">
                                          <select MULTIPLE id="txtbox_{$keyfldname}" name="{$keyfldname}" size="4" style="width:280px;" class="small">
				                                    {foreach item=arr from=$keyoptions}
										<option value="{$arr[1]}" {$arr[2]}>{$arr[0]}</option>
				                                    {/foreach}
			                                   </select>
			                                   <br><a class="detailview_ajaxbutton ajax_save_detailview" onclick="dtlViewAjaxSave('{$keyfldname}','{$MODULE}',{$keyid},'{$keytblname}','{$keyfldname}','{$ID}');fnhide('crmspanid');event.stopPropagation();"/>{$APP.LBL_SAVE_LABEL}</a>
                                              		   <a href="javascript:;" onclick="hndCancel('dtlview_{$keyfldname}','editarea_{$keyfldname}','{$keyfldname}');event.stopPropagation();" class="detailview_ajaxbutton ajax_cancelsave_detailview">{$APP.LBL_CANCEL_BUTTON_LABEL}</a>
                    							</div>
               							</td>
						{elseif $keyid eq '115'} <!--ComboBox Status edit only for admin Users-->
               							<td width=25% class="dvtCellInfo" align="left">{$keyval}</td>
						{elseif $keyid eq '116' || $keyid eq '117'} <!--ComboBox currency id edit only for admin Users-->
								{if $keyadmin eq 1 || $keyid eq '117'}
									<td width=25% class="dvtCellInfo" align="left" id="mouseArea_{$keyfldname}" onmouseover="hndMouseOver({$keyid},'{$keyfldname}');" onmouseout="fnhide('crmspanid');" onclick='handleEdit(event);'>&nbsp;<span id="dtlview_{$keyfldname}">{$keyval}</span>
								<div id="editarea_{$keyfldname}" style="display:none;">
                    							   <select id="txtbox_{$keyfldname}" name="{$keyfldname}" class="small">
									{foreach item=arr key=uivalueid from=$keyoptions}
									{foreach key=sel_value item=value from=$arr}
										<option value="{$uivalueid}" {$value}>{$sel_value|@getTranslatedCurrencyString}</option>
									{/foreach}
									{/foreach}
                    							   </select>
                    							   <br><a class="detailview_ajaxbutton ajax_save_detailview" onclick="dtlViewAjaxSave('{$keyfldname}','{$MODULE}',{$keyid},'{$keytblname}','{$keyfldname}','{$ID}');fnhide('crmspanid');event.stopPropagation();">{$APP.LBL_SAVE_LABEL}</a>
                                              		   <a href="javascript:;" onclick="hndCancel('dtlview_{$keyfldname}','editarea_{$keyfldname}','{$keyfldname}');event.stopPropagation();" class="detailview_ajaxbutton ajax_cancelsave_detailview">{$APP.LBL_CANCEL_BUTTON_LABEL}</a>
                    							</div>
								{else}
               							<td width=25% class="dvtCellInfo" align="left">{$keyval}
								{/if}


               							</td>
                                             {elseif $keyid eq '17'} <!--WebSite-->
                                                  <td width=25% class="dvtCellInfo" align="left" id="mouseArea_{$keyfldname}" onmouseover="hndMouseOver({$keyid},'{$keyfldname}');" onmouseout="fnhide('crmspanid');" onclick='handleEdit(event);'>&nbsp;<span id="dtlview_{$keyfldname}"><a href="{$keyval}" target="_blank" onclick="event.stopPropagation();">{$keyval}</a></span>
                                              		<div id="editarea_{$keyfldname}" style="display:none;">
                                              		  <input class="detailedViewTextBox" onFocus="this.className='detailedViewTextBoxOn'" onBlur="this.className='detailedViewTextBox'" onkeyup="validateUrl('{$keyfldname}');" type="text" id="txtbox_{$keyfldname}" name="{$keyfldname}" maxlength='100' value="{$keyval}"></input>
                                              		  <br><a class="detailview_ajaxbutton ajax_save_detailview" onclick="dtlViewAjaxSave('{$keyfldname}','{$MODULE}',{$keyid},'{$keytblname}','{$keyfldname}','{$ID}');fnhide('crmspanid');event.stopPropagation();">{$APP.LBL_SAVE_LABEL}</a>
                                              		  <a href="javascript:;" onclick="hndCancel('dtlview_{$keyfldname}','editarea_{$keyfldname}','{$keyfldname}');event.stopPropagation();" class="detailview_ajaxbutton ajax_cancelsave_detailview">{$APP.LBL_CANCEL_BUTTON_LABEL}</a>
                                                       </div>
                                                  </td>
					     {elseif $keyid eq '85'}<!--Skype-->
                                                <td width=25% class="dvtCellInfo" align="left" id="mouseArea_{$keyfldname}" onmouseover="hndMouseOver({$keyid},'{$keyfldname}');" onmouseout="fnhide('crmspanid');" onclick='handleEdit(event);'>&nbsp;<span id="dtlview_{$keyfldname}"><a href="skype:{$keyval}?call" onclick="event.stopPropagation();"><img src="{'skype.gif'|@vtiger_imageurl:$THEME}" alt="{$APP.LBL_SKYPE}" title="{$APP.LBL_SKYPE}"  align="absmiddle"></img>&nbsp;{$keyval}</a></span>
                                                        <div id="editarea_{$keyfldname}" style="display:none;">
                                                          <input class="detailedViewTextBox" onFocus="this.className='detailedViewTextBoxOn'" onBlur="this.className='detailedViewTextBox'" type="text" id="txtbox_{$keyfldname}" name="{$keyfldname}" maxlength='100' value="{$keyval}"></input>
                                                          <br><a class="detailview_ajaxbutton ajax_save_detailview" onclick="dtlViewAjaxSave('{$keyfldname}','{$MODULE}',{$keyid},'{$keytblname}','{$keyfldname}','{$ID}');fnhide('crmspanid');event.stopPropagation();">{$APP.LBL_SAVE_LABEL}</a>
                                                          <a href="javascript:;" onclick="hndCancel('dtlview_{$keyfldname}','editarea_{$keyfldname}','{$keyfldname}');event.stopPropagation();" class="detailview_ajaxbutton ajax_cancelsave_detailview">{$APP.LBL_CANCEL_BUTTON_LABEL}</a>
                                                       </div>
                                                  </td>
                                             {elseif $keyid eq '19' || $keyid eq '20'} <!--TextArea/Description-->
						<!-- we will empty the value of ticket and faq comment -->
						{if isset($MOD.LBL_ADD_COMMENT) && $label eq $MOD.LBL_ADD_COMMENT}
							{assign var=keyval value=''}
						{/if}
							<!--{assign var="DESCRIPTION_SEPARATOR_STRING" value=" "}  {* Separates Description *}-->
							<!--{assign var="DESCRIPTION_WORDWRAP_WIDTH" value="70"} {* No. of chars for word wrapping long lines of Description *}-->
							{if $MODULE eq 'Documents'}
							<!--To give hyperlink to URL-->
								<td width="100%" colspan="3" class="dvtCellInfo" align="left">{$keyval|regex_replace:"/(^|[\n ])([\w]+?:\/\/.*?[^ \"\n\r\t<]*)/":"\\1<a href=\"\\2\" target=\"_blank\">\\2</a>"|regex_replace:"/(^|[\n ])((www|ftp)\.[\w\-]+\.[\w\-.\~]+(?:\/[^ \"\t\n\r<]*)?)/":"\\1<a href=\"http://\\2\" target=\"_blank\">\\2</a>"|regex_replace:"/(^|[\n ])([a-z0-9&\-_.]+?)@([\w\-]+\.([\w\-\.]+\.)*[\w]+)/i":"\\1<a href=\"mailto:\\2@\\3\">\\2@\\3</a>"|regex_replace:"/,\"|\.\"|\)\"|\)\.\"|\.\)\"/":"\""}&nbsp;</td>
							{else}
								<td width="100%" colspan="3" class="dvtCellInfo" align="left" id="mouseArea_{$keyfldname}" onmouseover="hndMouseOver({$keyid},'{$keyfldname}');" onmouseout="fnhide('crmspanid');" onclick='handleEdit(event);'>&nbsp;
									<span id="dtlview_{$keyfldname}">
										{$keyval|regex_replace:"/(^|[\n ])([\w]+?:\/\/.*?[^ \"\n\r\t<]*)/":"\\1<a href=\"\\2\" target=\"_blank\">\\2</a>"|regex_replace:"/(^|[\n ])((www|ftp)\.[\w\-]+\.[\w\-.\~]+(?:\/[^ \"\t\n\r<]*)?)/":"\\1<a href=\"http://\\2\" target=\"_blank\">\\2</a>"|regex_replace:"/(^|[\n ])([a-z0-9&\-_.]+?)@([\w\-]+\.([\w\-\.]+\.)*[\w]+)/i":"\\1<a href=\"mailto:\\2@\\3\">\\2@\\3</a>"|regex_replace:"/,\"|\.\"|\)\"|\)\.\"|\.\)\"/":"\""|replace:"\n":"<br>&nbsp;"}
									</span>
									<div id="editarea_{$keyfldname}" style="display:none;">
									<textarea id="txtbox_{$keyfldname}" name="{$keyfldname}"  class=detailedViewTextBox onFocus="this.className='detailedViewTextBoxOn'"onBlur="this.className='detailedViewTextBox'" cols="90" rows="8">{$keyval|replace:"<br>":"\n"}</textarea>
									<br><a class="detailview_ajaxbutton ajax_save_detailview" onclick="dtlViewAjaxSave('{$keyfldname}','{$MODULE}',{$keyid},'{$keytblname}','{$keyfldname}','{$ID}');fnhide('crmspanid');event.stopPropagation();">{$APP.LBL_SAVE_LABEL}</a>
									<a href="javascript:;" onclick="hndCancel('dtlview_{$keyfldname}','editarea_{$keyfldname}','{$keyfldname}');event.stopPropagation();" class="detailview_ajaxbutton ajax_cancelsave_detailview">{$APP.LBL_CANCEL_BUTTON_LABEL}</a>
									</div>
								</td>
							{/if}
                                             {elseif $keyid eq '21' || $keyid eq '24' || $keyid eq '22'} <!--TextArea/Street-->
                                                  <td width=25% class="dvtCellInfo" align="left" id="mouseArea_{$keyfldname}" onmouseover="hndMouseOver({$keyid},'{$keyfldname}');" onmouseout="fnhide('crmspanid');" onclick='handleEdit(event);'>&nbsp;<span id="dtlview_{$keyfldname}">{$keyval}</span>
                                              		<div id="editarea_{$keyfldname}" style="display:none;">
                                              		  <textarea id="txtbox_{$keyfldname}" name="{$keyfldname}"  class=detailedViewTextBox onFocus="this.className='detailedViewTextBoxOn'"onBlur="this.className='detailedViewTextBox'" rows=2>{$keyval|regex_replace:"/<br\s*\/>/":""}</textarea>
                                              		  <br><a class="detailview_ajaxbutton ajax_save_detailview" onclick="dtlViewAjaxSave('{$keyfldname}','{$MODULE}',{$keyid},'{$keytblname}','{$keyfldname}','{$ID}');fnhide('crmspanid');event.stopPropagation();">{$APP.LBL_SAVE_LABEL}</a>
                                              		  <a href="javascript:;" onclick="hndCancel('dtlview_{$keyfldname}','editarea_{$keyfldname}','{$keyfldname}');event.stopPropagation();" class="detailview_ajaxbutton ajax_cancelsave_detailview">{$APP.LBL_CANCEL_BUTTON_LABEL}</a>
                                                       </div>
                                                  </td>
                                             {elseif $keyid eq '50' || $keyid eq '73' || $keyid eq '51'} <!--AccountPopup-->
                                                  <td width=25% class="dvtCellInfo" align="left" id="mouseArea_{$keyfldname}" onmouseover="vtlib_listview.trigger('cell.onmouseover', this);" onmouseout="vtlib_listview.trigger('cell.onmouseout', this)">&nbsp;<a href="{$keyseclink}">{$keyval}</a>
                                                  </td>
                                             {elseif $keyid eq '57'} <!--ContactPopup-->
						<!-- Ajax edit link not provided for contact - Reports To -->
                                                  	<td width=25% class="dvtCellInfo" align="left" id="mouseArea_{$keyfldname}">&nbsp;<a href="{$keyseclink}">{$keyval}</a></td>
                                             {elseif $keyid eq '59'} <!--ProductPopup-->
                                                  <td width=25% class="dvtCellInfo" align="left" id="mouseArea_{$keyfldname}" onmouseover="hndMouseOver({$keyid},'{$keyfldname}');" onmouseout="fnhide('crmspanid');" onclick='handleEdit(event);'>&nbsp;<span id="dtlview_{$keyfldname}"><a href="{$keyseclink}" onclick="event.stopPropagation();">{$keyval}</a></span>
                                              		<div id="editarea_{$keyfldname}" style="display:none;">
                                                         <input id="popuptxt_{$keyfldname}" name="product_name" readonly type="text" value="{$keyval}"><input id="txtbox_{$keyfldname}" name="{$keyfldname}" type="hidden" value="{$keysecid}">&nbsp;<img src="{'select.gif'|@vtiger_imageurl:$THEME}" alt="{$APP.LBL_SELECT}" title="{$APP.LBL_SELECT}"  onclick='return window.open("index.php?module=Products&action=Popup&html=Popup_picker&form=HelpDeskEditView&popuptype=specific","test","width=600,height=602,resizable=1,scrollbars=1,top=150,left=200");' align="absmiddle" style='cursor:hand;cursor:pointer'>&nbsp;<input type="image" src="{'clear_field.gif'|@vtiger_imageurl:$THEME}" alt="{$APP.LBL_CLEAR}" title="{$APP.LBL_CLEAR}"  onClick="this.form.product_id.value=''; this.form.product_name.value=''; return false;" align="absmiddle" style='cursor:hand;cursor:pointer'>
                                                         <br><a class="detailview_ajaxbutton ajax_save_detailview" onclick="dtlViewAjaxSave('{$keyfldname}','{$MODULE}',{$keyid},'{$keytblname}','{$keyfldname}','{$ID}');fnhide('crmspanid');event.stopPropagation();">{$APP.LBL_SAVE_LABEL}</a>
                                              		  <a href="javascript:;" onclick="hndCancel('dtlview_{$keyfldname}','editarea_{$keyfldname}','{$keyfldname}');event.stopPropagation();" class="detailview_ajaxbutton ajax_cancelsave_detailview">{$APP.LBL_CANCEL_BUTTON_LABEL}</a>
                                                       </div>
                                                  </td>
                                             {elseif $keyid eq '75' || $keyid eq '81'} <!--VendorPopup-->
                                                  <td width=25% class="dvtCellInfo" align="left" id="mouseArea_{$keyfldname}">&nbsp;<a href="{$keyseclink}">{$keyval}</a>
                                                  </td>
                                             {elseif $keyid eq 76} <!--PotentialPopup-->
                                                  <td width=25% class="dvtCellInfo" align="left" id="mouseArea_{$keyfldname}">&nbsp;<a href="{$keyseclink}">{$keyval}</a>
                                                  </td>
                                             {elseif $keyid eq 78} <!--QuotePopup-->
                                                  <td width=25% class="dvtCellInfo" align="left" id="mouseArea_{$keyfldname}">&nbsp;<a href="{$keyseclink}">{$keyval}</a>
                                                  </td>
                                             {elseif $keyid eq 82} <!--Email Body-->
                                                  <td colspan="3" width=100% class="dvtCellInfo" align="left"><div id="dtlview_{$keyfldname}" style="width:100%;height:200px;overflow:hidden;border:1px solid gray" class="detailedViewTextBox" onmouseover="this.className='detailedViewTextBoxOn'" onmouseout="this.className='detailedViewTextBox'">{$keyval}</div>
                                                  </td>
                                             {elseif $keyid eq 80} <!--SalesOrderPopup-->
                                                  <td width=25% class="dvtCellInfo" align="left" id="mouseArea_{$keyfldname}">&nbsp;<a href="{$keyseclink}">{$keyval}</a>
                                                  </td>
					     {elseif $keyid eq '52' || $keyid eq '77'}
                                                                <td width=25% class="dvtCellInfo" align="left" id="mouseArea_{$keyfldname}" onmouseover="hndMouseOver({$keyid},'{$keyfldname}');" onmouseout="fnhide('crmspanid');" onclick='handleEdit(event);'>&nbsp;<span id="dtlview_{$keyfldname}">{$keyval}</span>
                                                        <div id="editarea_{$keyfldname}" style="display:none;">
                                                                           <select id="txtbox_{$keyfldname}" name="{$keyfldname}" class="small">
                                                                                {foreach item=arr key=uid from=$keyoptions}
                                                                                        {foreach key=sel_value item=value from=$arr}
                                                                                                <option value="{$uid}" {$value}>{if $APP.$sel_value}{$APP.$sel_value}{else}{$sel_value}{/if}</option>

                                                                                        {/foreach}
                                                                                {/foreach}
                                                                           </select>
                                                                           <br><a class="detailview_ajaxbutton ajax_save_detailview" onclick="dtlViewAjaxSave('{$keyfldname}','{$MODULE}',{$keyid},'{$keytblname}','{$keyfldname}','{$ID}');fnhide('crmspanid');event.stopPropagation();">{$APP.LBL_SAVE_LABEL}</a>
                                                           <a href="javascript:;" onclick="hndCancel('dtlview_{$keyfldname}','editarea_{$keyfldname}','{$keyfldname}');event.stopPropagation();" class="detailview_ajaxbutton ajax_cancelsave_detailview">{$APP.LBL_CANCEL_BUTTON_LABEL}</a>
                                                                        </div>
                                                                </td>
						{elseif $keyid eq '53'} <!--Assigned To-->
							<td width=25% class="dvtCellInfo" align="left" id="mouseArea_{$keyfldname}" onmouseover="hndMouseOver({$keyid},'{$keyfldname}');" onmouseout="fnhide('crmspanid');" onclick='handleEdit(event);'>&nbsp;<span id="dtlview_{$keyfldname}">
							{if $keyadmin eq 1}
								<a href="{$keyseclink.0}" onclick="event.stopPropagation();">{$keyval}</a>
							{else}
								{$keyval}
							{/if}
							&nbsp;</span>
							<div id="editarea_{$keyfldname}" style="display:none;">
							<input type="hidden" id="hdtxt_{$keyfldname}" value="{$keyval}"></input>
						{if $keyoptions.0 eq 'User'}
							<input name="assigntype" id="assigntype" checked="checked" value="U" onclick="toggleAssignType(this.value),setSelectValue('{$keyfldname}');" type="radio">&nbsp;{$APP.LBL_USER}
							{if $keyoptions.2 neq ''}
								<input name="assigntype" id="assigntype" value="T" onclick="toggleAssignType(this.value),setSelectValue('{$keyfldname}');" type="radio">&nbsp;{$APP.LBL_GROUP_NAME}
							{/if}
							<span id="assign_user" style="display: block;">
						{else}
							<input name="assigntype" id="assigntype" value="U" onclick="toggleAssignType(this.value),setSelectValue('{$keyfldname}');" type="radio">&nbsp;{$APP.LBL_USER}
							<input name="assigntype" checked="checked" id="assigntype" value="T" onclick="toggleAssignType(this.value),setSelectValue('{$keyfldname}');" type="radio">&nbsp;{$APP.LBL_GROUP_NAME}
							<span id="assign_user" style="display: none;">
						{/if}
                   				<select id="txtbox_U{$keyfldname}" onchange="setSelectValue('{$keyfldname}')" name="{$keyfldname}" class="small">
				                    {foreach item=arr key=id from=$keyoptions.1}
				                    	{foreach key=sel_value item=value from=$arr}
                       						 <option value="{$id}" {$value}>{$sel_value}</option>
				                        {/foreach}
				                    {/foreach}
			                    	</select>
						</span>
					{if $keyoptions.0 eq 'Group'}
						<span id="assign_team" style="display: block;">
					{else}
						<span id="assign_team" style="display: none;">
					{/if}
                   	<select id="txtbox_G{$keyfldname}" onchange="setSelectValue('{$keyfldname}')" name="assigned_group_id" class="groupname small">
                    {foreach item=arr key=id from=$keyoptions.2}
                    	{foreach key=sel_value item=value from=$arr}
                       		 <option value="{$id}" {$value}>{$sel_value}</option>
                        {/foreach}
                    {/foreach}
                    </select>
					</span>

                    <br>
                    <a class="detailview_ajaxbutton ajax_save_detailview" onclick="dtlViewAjaxSave('{$keyfldname}','{$MODULE}',{$keyid},'{$keytblname}','{$keyfldname}','{$ID}');event.stopPropagation();">{$APP.LBL_SAVE_LABEL}</a>
                    <a href="javascript:;" onclick="hndCancel('dtlview_{$keyfldname}','editarea_{$keyfldname}','{$keyfldname}');event.stopPropagation();" class="detailview_ajaxbutton ajax_cancelsave_detailview">{$APP.LBL_CANCEL_BUTTON_LABEL}</a>
                    </div>
                    </td>
						{elseif $keyid eq '99'}<!-- Password Field-->
						<td width=25% class="dvtCellInfo" align="left">{$CHANGE_PW_BUTTON}</td>	
					    {elseif $keyid eq '56'} <!--CheckBox--> 
                      <td width=25% class="dvtCellInfo" align="left" id="mouseArea_{$keyfldname}" onMouseOver="hndMouseOver({$keyid},'{$keyfldname}');" onmouseout="fnhide('crmspanid');" onclick='handleEdit(event);'>&nbsp;<span id="dtlview_{$keyfldname}">{$keyval}&nbsp;</span>
                    	<div id="editarea_{$keyfldname}" style="display:none;">
                    	{if $MODULE neq 'Documents'}
                        	{if $keyval eq $APP.yes}
                            	<input id="txtbox_{$keyfldname}" name="{$keyfldname}" type="checkbox" style="border:1px solid #bababa;" checked value="1">
                        	{else}
                          		<input id="txtbox_{$keyfldname}" type="checkbox" name="{$keyfldname}" style="border:1px solid #bababa;" value="0">
                       		{/if}
                       	{else}
                         	{if $keyval eq $APP.yes}
                            	<input id="txtbox_{$keyfldname}" name="{$keyfldname}" type="checkbox" style="border:1px solid #bababa;" checked value="0">
                        	{else}
                          		<input id="txtbox_{$keyfldname}" type="checkbox" name="{$keyfldname}" style="border:1px solid #bababa;" value="1">
                       		{/if}
                       	{/if}
                       	{if $keyfldname eq 'portal'}<input type="hidden" name="existing_portal" id="existing_portal" value="{if $keyval eq $APP.yes}1{else}0{/if}">{/if}
                         <br><a class="detailview_ajaxbutton ajax_save_detailview" onclick="dtlViewAjaxSave('{$keyfldname}','{$MODULE}',{$keyid},'{$keytblname}','{$keyfldname}','{$ID}');event.stopPropagation();">{$APP.LBL_SAVE_LABEL}</a>
                          <a href="javascript:;" onclick="hndCancel('dtlview_{$keyfldname}','editarea_{$keyfldname}','{$keyfldname}');event.stopPropagation();" class="detailview_ajaxbutton ajax_cancelsave_detailview">{$APP.LBL_CANCEL_BUTTON_LABEL}</a>
                        </div>
                        </td>
			{elseif $keyid eq '156'} <!--CheckBox for is admin-->
			{if $smarty.request.record neq $CURRENT_USERID && $keyadmin eq 1}
                      <td width=25% class="dvtCellInfo" align="left" id="mouseArea_{$keyfldname}" onMouseOver="hndMouseOver({$keyid},'{$keyfldname}');" onmouseout="fnhide('crmspanid');" onclick='handleEdit(event);'>&nbsp;<span id="dtlview_{$keyfldname}">{if $APP.$keyval!=''}{$APP.$keyval}{elseif $MOD.$keyval!=''}{$MOD.$keyval}{else}{$keyval}{/if}&nbsp;</span>
                    	<div id="editarea_{$keyfldname}" style="display:none;">
                        {if $keyval eq 'on'}
                            <input id="txtbox_{$keyfldname}" name="{$keyfldname}" type="checkbox" style="border:1px solid #bababa;" checked value="1">
                        {else}
                          <input id="txtbox_{$keyfldname}" type="checkbox" name="{$keyfldname}" style="border:1px solid #bababa;" value="0">
                       	{/if}
                         <br><a class="detailview_ajaxbutton ajax_save_detailview" onclick="dtlViewAjaxSave('{$keyfldname}','{$MODULE}',{$keyid},'{$keytblname}','{$keyfldname}','{$ID}');event.stopPropagation();">{$APP.LBL_SAVE_LABEL}</a>
                          <a href="javascript:;" onclick="hndCancel('dtlview_{$keyfldname}','editarea_{$keyfldname}','{$keyfldname}');event.stopPropagation();" class="detailview_ajaxbutton ajax_cancelsave_detailview">{$APP.LBL_CANCEL_BUTTON_LABEL}</a>
                        </div>
			{else}
				 <td width=25% class="dvtCellInfo" align="left">{$keyval}
			{/if}
                        </td>

						{elseif $keyid eq 83}<!-- Handle the Tax in Inventory -->
							<td class="dvtCellInfo" colspan=3>&nbsp;</td></tr>
							{foreach item=tax key=count from=$TAX_DETAILS}
							<tr style="height:25px">
								<td align="right" class="dvtCellLabel">{$tax.taxlabel} {$APP.COVERED_PERCENTAGE}</td>
								<td class="dvtCellInfo" align="left">{$tax.percentage}</td>
								<td colspan="2" class="dvtCellInfo">&nbsp;</td>
							</tr>
							{/foreach}

				{elseif $keyid eq 5}
					{* Initialize the date format if not present *}
					{if empty($dateFormat)}
						{assign var="dateFormat" value=$APP.NTC_DATE_FORMAT|@parse_calendardate}
					{/if}
					<td width=25% class="dvtCellInfo" align="left" id="mouseArea_{$keyfldname}" onmouseover="hndMouseOver({$keyid},'{$keyfldname}');" onmouseout="fnhide('crmspanid');" onclick='handleEdit(event);'>
						&nbsp;&nbsp;<span id="dtlview_{$keyfldname}">
							{$keyval}
						</span>
						<div id="editarea_{$keyfldname}" style="display:none;">
							<input style="border:1px solid #bababa;" size="10" maxlength="10" type="text" id="txtbox_{$keyfldname}" name="{$keyfldname}" value="{$keyval|regex_replace:'/[^-]*(--)[^-]*$/':''}"></input>
							<img src="{'btnL3Calendar.gif'|@vtiger_imageurl:$THEME}" id="jscal_trigger_{$keyfldname}">
							<br><a class="detailview_ajaxbutton ajax_save_detailview" onclick="dtlViewAjaxSave('{$keyfldname}','{$MODULE}',{$keyid},'{$keytblname}','{$keyfldname}','{$ID}');fnhide('crmspanid');event.stopPropagation();">{$APP.LBL_SAVE_LABEL}</a>
							<a href="javascript:;" onclick="hndCancel('dtlview_{$keyfldname}','editarea_{$keyfldname}','{$keyfldname}');event.stopPropagation();" class="detailview_ajaxbutton ajax_cancelsave_detailview">{$APP.LBL_CANCEL_BUTTON_LABEL}</a>
							<script type="text/javascript">
								Calendar.setup ({ldelim}
									inputField : "txtbox_{$keyfldname}", ifFormat : '{$dateFormat}', showsTime : false, button : "jscal_trigger_{$keyfldname}", singleClick : true, step : 1
								{rdelim})
							</script>
						</div>
					</td>

				{elseif $keyid eq 69}<!-- for Image Reflection -->
					<td align="left" width=25%>&nbsp;{$keyval}</td>
				{elseif $keyid eq 68 || $keyid eq 101}
					<td class="dvtCellInfo" id="mouseArea_{$keyfldname}" align="left" width=25% onmouseover="vtlib_listview.trigger('cell.onmouseover', this);" onmouseout="vtlib_listview.trigger('cell.onmouseout', this)">&nbsp;{$keyval}</td>
				{elseif $keyid eq 10}<!-- for vtlib reference field -->
					<td class="dvtCellInfo" id="mouseArea_{$keyfldname}" align="left" width=25% onmouseover="hndMouseOver({$keyid},'{$keyfldname}');vtlib_listview.trigger('cell.onmouseover', this);" onmouseout="fnhide('crmspanid');vtlib_listview.trigger('cell.onmouseout', this)" onclick='handleEdit(event);'>
						&nbsp;<span id="dtlview_{$keyfldname}" onclick='event.stopPropagation();'>{$keyval}</span>
						<div id="editarea_{$keyfldname}" style="display:none;">
							{if count($data.extendedfieldinfo.options) eq 1}
								{assign var="use_parentmodule" value=$data.extendedfieldinfo.options.0}
								<input type='hidden' class='small' id="{$keyfldname}_type" name="{$keyfldname}_type" value="{$use_parentmodule}">
								{assign var=vtui10func value=$use_parentmodule|getvtlib_open_popup_window_function:$keyfldname:$MODULE}
							{else}
								{assign var=vtui10func value="vtlib_open_popup_window"}
								<br>
								<select id="{$keyfldname}_type" class="small" name="{$keyfldname}_type" onChange='document.getElementById("{$keyfldname}_display").value=""; document.getElementById("txtbox_{$keyfldname}").value="";'>
								{foreach item=option from=$data.extendedfieldinfo.options}
									<option value="{$option}"
									{if $data.extendedfieldinfo.selected == $option}selected{/if}>
									{$option|@getTranslatedString:$option}
									</option>
								{/foreach}
								</select>
							{/if}
							<input id="txtbox_{$keyfldname}" name="{$keyfldname}" type="hidden" value="{$data.extendedfieldinfo.entityid}">
							<input id="{$keyfldname}_display" name="{$keyfldname}_display" readonly type="text" style="border:1px solid #bababa;" value="{$data.extendedfieldinfo.displayvalue}">&nbsp;
							<img src="{'select.gif'|@vtiger_imageurl:$THEME}"
								alt="{'LBL_SELECT'|@getTranslatedString}" title="{'LBL_SELECT'|@getTranslatedString}" onclick='return {$vtui10func}("DetailView","{$keyfldname}","{$MODULE}","{$ID}");' align="absmiddle" style='cursor:hand;cursor:pointer'>&nbsp;
							<input type="image" src="{'clear_field.gif'|@vtiger_imageurl:$THEME}"
								alt="{'LBL_CLEAR'|@getTranslatedString}" title="{'LBL_CLEAR'|@getTranslatedString}" onClick="document.getElementById('txtbox_{$keyfldname}').value='0'; document.getElementById('{$keyfldname}_display').value=''; return false;" align="absmiddle" style='cursor:hand;cursor:pointer'>
							<br><a class="detailview_ajaxbutton ajax_save_detailview" onclick="dtlViewAjaxSave('{$keyfldname}','{$MODULE}',{$keyid},'{$keytblname}','{$keyfldname}','{$ID}');fnhide('crmspanid');event.stopPropagation();">{$APP.LBL_SAVE_LABEL}</a>
							<a href="javascript:;" onclick="hndCancel('dtlview_{$keyfldname}','editarea_{$keyfldname}','{$keyfldname}');event.stopPropagation();" class="detailview_ajaxbutton ajax_cancelsave_detailview">{$APP.LBL_CANCEL_BUTTON_LABEL}</a>
						</div>
					</td>
				{else}
					<td class="dvtCellInfo" id="mouseArea_{$keyfldname}" align="left" width=25%>&nbsp;{$keyval}</td>
				{/if}
