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
{* ITS4YOU TT0093 VlMe N *}
<script language="JAVASCRIPT" type="text/javascript" src="include/js/smoothscroll.js"></script>
{include file='modules/PDFMaker/Buttons_List.tpl'}
<table align="center" border="0" cellpadding="0" cellspacing="0" width="98%">
<form name="PDFMakerEdit" action="index.php?module=PDFMaker&action=SavePDFTemplate" method="post" enctype="multipart/form-data" onsubmit="VtigerJS_DialogBox.block();">
<input type="hidden" name="return_module" value="PDFMaker">
<input type="hidden" name="parenttab" value="{$PARENTTAB}">
<input type="hidden" name="templateid" value="{$SAVETEMPLATEID}">
<input type="hidden" name="action" value="SavePDFTemplate">
<input type="hidden" name="redirect" value="true">
<tr>        
        <td class="showPanelBg" style="padding: 10px;" valign="top" width="100%">

				<!-- DISPLAY -->
				<table border=0 cellspacing=0 cellpadding=5 width=100%>
				<tr>
                      <td class=heading2 valign=bottom>&nbsp;&nbsp;<b>{$MOD.LBL_EDIT} &quot;{$MODULENAME}&quot; </b></td>
				</tr>
				</table>
				<table border=0 cellspacing=0 cellpadding=10 width=100% >
				<tr>
				<td> 
					{if $DISPLAY_PRODUCT_DIV eq 'none'}
                      {assign var=DISPLAY_NO_PRODUCT_DIV value='block'}
                      {assign var=DISPLAY_PRODUCT_TPL_ROW value='none'}                      
                    {else}
                      {assign var=DISPLAY_NO_PRODUCT_DIV value='none'}
                      {assign var=DISPLAY_PRODUCT_TPL_ROW value='table-row'}
                    {/if}
                    
                    <table width="100%" align="center" border="0" cellpadding="0" cellspacing="0">
          		    <tr><td>
                    
                      <table class="small" width="100%" border="0" cellpadding="3" cellspacing="0"><tr>
                          <td class="dvtTabCache" style="width: 10px;" nowrap="nowrap">&nbsp;</td>
                          <td style="width: 15%;" class="dvtSelectedCell" id="properties_tab" onclick="showHideTab('properties');" width="75" align="center" nowrap="nowrap"><b>{$MOD.LBL_PROPERTIES_TAB}</b></td>
                          <td class="dvtUnSelectedCell" id="company_tab" onclick="showHideTab('company');" align="center" nowrap="nowrap"><b>{$MOD.LBL_OTHER_INFO}</b></td>
                          <td class="dvtUnSelectedCell" id="labels_tab" onclick="showHideTab('labels');" align="center" nowrap="nowrap"><b>{$MOD.LBL_LABELS}</b></td>
                          <td class="dvtUnSelectedCell" id="products_tab" onclick="showHideTab('products');" align="center" nowrap="nowrap"><b>{$MOD.LBL_ARTICLE}</b></td>
           				  <td class="dvtUnSelectedCell" id="settings_tab" onclick="showHideTab('settings');" align="center" nowrap="nowrap"><b>{$MOD.LBL_SETTINGS_TAB}</b></td>           				  
                          <td class="dvtTabCache" style="width: 30%;" nowrap="nowrap">&nbsp;</td> 
                      </tr></table>
                    </td></tr>
					
                     <tr><td align="left" valign="top">
                      {*********************************************PROPERTIES DIV*************************************************}
                      <div style="diplay:block;" id="properties_div">       
                        <table class="dvtContentSpace" width="100%" border="0" cellpadding="3" cellspacing="0" style="padding:10px;">                        
     				
            				 {* pdf source module and its available fields *}
            				 <tr>
            						<td width="20%" valign=top class="small cellLabel">{if $TEMPLATEID eq ""}<font color="red">*</font>{/if}<strong>{$MOD.LBL_MODULENAMES}:</strong></td>
            						<td class="cellText small" valign="top">
                                	<input type="hidden" name="modulename" id="modulename" value="{$SELECTMODULE}" >
	                                <select name="modulefields" id="modulefields" class="classname">
                                        {html_options  options=$SELECT_MODULE_FIELD}
                               		</select>
        					    	<input type="button" value="{$MOD.LBL_INSERT_TO_TEXT}" class="crmButton small create" onclick="InsertIntoTemplate('modulefields');" />
                        			</td>      						
          					 </tr>    					
          				 	 {* related modules and its fields *}                					
                        <tr id="body_variables">
                          	<td valign=top class="small cellLabel"><strong>{$MOD.LBL_RELATED_MODULES}:</strong></td>
                          	<td class="cellText small" valign=top>
                          
                                <select name="relatedmodulesorce" id="relatedmodulesorce" class="classname" onChange="change_relatedmodule(this,'relatedmodulefields');">
                                        <option value="none">{$MOD.LBL_SELECT_MODULE}</option>
                                        {html_options  options=$RELATED_MODULES}
                                </select>
                                &nbsp;&nbsp;
                          
                                <select name="relatedmodulefields" id="relatedmodulefields" class="classname">
                                    <option>{$MOD.LBL_SELECT_MODULE_FIELD}</option>
                                </select>
                              	<input type="button" value="{$MOD.LBL_INSERT_TO_TEXT}" class="crmButton small create" onclick="InsertIntoTemplate('relatedmodulefields');">
                          	</td>      						
                        </tr>
                    {* product bloc tpl *}
                        <tr id="product_bloc_tpl_row" style="display:{$DISPLAY_PRODUCT_TPL_ROW};">                            
      					           	<td valign=top class="small cellLabel"><strong>{$MOD.LBL_PRODUCT_BLOC_TPL}:</strong></td>
      					          	<td class="cellText small" valign=top>
      					                 <select name="productbloctpl" id="productbloctpl" class="classname">
                              		   {html_options  options=$PRODUCT_BLOC_TPL}
                                 </select>
                                 <input type="button" value="{$MOD.LBL_INSERT_TO_TEXT}" class="crmButton small create" onclick="InsertIntoTemplate('productbloctpl');"/>
        				            </td>              					
                        </tr>
                     {* pdf header variables*}
                        <tr id="header_variables" style="display:none">
                            <td valign="top" width="200px" class="cellLabel small"><strong>{$MOD.LBL_HEADER_VARIABLE}:</strong></td>
                            <td class="cellText small" valign=top>
                                <select name="header_var" id="header_var" class="classname">
                                    {html_options  options=$HEAD_FOOT_VARS selected=""}
                                </select>
                                <input type="button" value="{$MOD.LBL_INSERT_TO_TEXT}" class="crmButton small create" onclick="InsertIntoTemplate('header_var');">
      				          		</td>
                        </tr>
                     {* pdf footer variables*}
                        <tr id="footer_variables" style="display:none">
                            <td valign="top" width="200px" class="cellLabel small"><strong>{$MOD.LBL_FOOTER_VARIABLE}:</strong></td>
                            <td class="cellText small" valign=top>
                                <select name="footer_var" id="footer_var" class="classname">
                                    {html_options  options=$HEAD_FOOT_VARS selected=""}
                                </select>
                                <input type="button" value="{$MOD.LBL_INSERT_TO_TEXT}" class="crmButton small create" onclick="InsertIntoTemplate('footer_var');">
      						</td>
                        </tr>
                                                    					
                        </table>
                      </div>
                      
                      {*********************************************Labels DIV*************************************************}
                      <div style="display:none;" id="labels_div">
                        <table class="dvtContentSpace" width="100%" border="0" cellpadding="3" cellspacing="0" style="padding:10px;">
                        <tr>
    						<td width="200px" valign=top class="small cellLabel"><strong>{$MOD.LBL_GLOBAL_LANG}:</strong></td>
    						<td class="cellText small" valign=top>
        						<select name="global_lang" id="global_lang" class="classname" style="width:80%">
                                		{html_options  options=$GLOBAL_LANG_LABELS}
                                </select>
    					       	<input type="button" value="{$MOD.LBL_INSERT_TO_TEXT}" class="crmButton small create" onclick="InsertIntoTemplate('global_lang');">
      						</td>
    					</tr>
    					<tr>
    						<td valign=top class="small cellLabel"><strong>{$MOD.LBL_MODULE_LANG}:</strong></td>
    						<td class="cellText small" valign=top>
        						<select name="module_lang" id="module_lang" class="classname" style="width:80%">
                                		{html_options  options=$MODULE_LANG_LABELS}
                                </select>
        						<input type="button" value="{$MOD.LBL_INSERT_TO_TEXT}" class="crmButton small create" onclick="InsertIntoTemplate('module_lang');">
      						</td>
    					</tr>
                        </table>
                      </div>
                      
                      {*********************************************Company and User information DIV*************************************************}
                      <div style="display:none;" id="company_div">
                        <table class="dvtContentSpace" width="100%" border="0" cellpadding="3" cellspacing="0" style="padding:10px;">
                        <tr>
    						<td width="200px" valign=top class="small cellLabel"><strong>{$MOD.LBL_COMPANY_USER_INFO}:</strong></td>
    						<td class="cellText small" valign=top>
        						<select name="acc_info" id="acc_info" class="classname">
                                	<optGroup label="{$MOD.LBL_COMPANY_INFO}">
                                    {html_options  options=$ACCOUNTINFORMATIONS}
                                  </optGroup>
                                  <optGroup label="{$MOD.LBL_USER_INFO}">
                                    {html_options  options=$USERINFORMATIONS}
                                  </optGroup>
                                  <optGroup label="{$MOD.LBL_LOGGED_USER_INFO}">
                                    {html_options  options=$LOGGEDUSERINFORMATION}
                                  </optGroup>
                                </select>
    					       	<input type="button" value="{$MOD.LBL_INSERT_TO_TEXT}" class="crmButton small create" onclick="InsertIntoTemplate('acc_info');">
      						</td>
    					</tr>
    					<tr>
    						<td valign=top class="small cellLabel"><strong>{$MOD.TERMS_AND_CONDITIONS}:</strong></td>
    						<td class="cellText small" valign=top>
        						<select name="invterandcon" id="invterandcon" class="classname">
                                		{html_options  options=$INVENTORYTERMSANDCONDITIONS}
                                </select>
        						<input type="button" value="{$MOD.LBL_INSERT_TO_TEXT}" class="crmButton small create" onclick="InsertIntoTemplate('invterandcon');">
      						</td>
    					</tr>
    					<tr>
    						<td valign=top class="small cellLabel"><strong>{$MOD.LBL_CURRENT_DATE}:</strong></td>
    						<td class="cellText small" valign=top>
        						<select name="dateval" id="dateval" class="classname">
                                		{html_options  options=$DATE_VARS}
                                </select>
        						<input type="button" value="{$MOD.LBL_INSERT_TO_TEXT}" class="crmButton small create" onclick="InsertIntoTemplate('dateval');">
      						</td>
    					</tr>
    					{***** BARCODES *****}
    					<tr>
    						<td valign=top class="small cellLabel"><strong>{$MOD.LBL_BARCODES}:</strong></td>
    						<td class="cellText small" valign=top>
        						<select name="barcodeval" id="barcodeval" class="classname">
                                		<optgroup label="{$MOD.LBL_BARCODES_TYPE1}">
                                		     <option value="EAN13">EAN13</option>
                                		     <option value="ISBN">ISBN</option>
                                		     <option value="ISSN">ISSN</option>
                                		</optgroup>
                                		
                                		<optgroup label="{$MOD.LBL_BARCODES_TYPE2}">
                                		     <option value="UPCA">UPCA</option>
                                		     <option value="UPCE">UPCE</option>
                                		     <option value="EAN8">EAN8</option>
                                		</optgroup>
                                		
                                		<optgroup label="{$MOD.LBL_BARCODES_TYPE3}">
                                		     <option value="EAN2">EAN2</option>
                                		     <option value="EAN5">EAN5</option>
                                		     <option value="EAN13P2">EAN13P2</option>
                                		     <option value="ISBNP2">ISBNP2</option>
                                		     <option value="ISSNP2">ISSNP2</option>
                                		     <option value="UPCAP2">UPCAP2</option>
                                		     <option value="UPCEP2">UPCEP2</option>
                                		     <option value="EAN8P2">EAN8P2</option>
                                		     <option value="EAN13P5">EAN13P5</option>
                                		     <option value="ISBNP5">ISBNP5</option>
                                		     <option value="ISSNP5">ISSNP5</option>
                                		     <option value="UPCAP5">UPCAP5</option>
                                		     <option value="UPCEP5">UPCEP5</option>
                                		     <option value="EAN8P5">EAN8P5</option>
                                		</optgroup>
                                		
                                        <optgroup label="{$MOD.LBL_BARCODES_TYPE4}">     
                                		     <option value="IMB">IMB</option>
                                		     <option value="RM4SCC">RM4SCC</option>
                                		     <option value="KIX">KIX</option>
                                		     <option value="POSTNET">POSTNET</option>
                                		     <option value="PLANET">PLANET</option>
                                		</optgroup>
                                		
                                		<optgroup label="{$MOD.LBL_BARCODES_TYPE5}">    
                                		     <option value="C128A">C128A</option>
                                		     <option value="C128B">C128B</option>
                                		     <option value="C128C">C128C</option>
                                		     <option value="EAN128C">EAN128C</option>
                                		     <option value="C39">C39</option>
                                		     <option value="C39+">C39+</option>
                                		     <option value="C39E">C39E</option>
                                		     <option value="C39E+">C39E+</option>
                                		     <option value="S25">S25</option>
                                		     <option value="S25+">S25+</option>
                                		     <option value="I25">I25</option>
                                		     <option value="I25+">I25+</option>
                                		     <option value="I25B">I25B</option>
                                		     <option value="I25B+">I25B+</option>
                                		     <option value="C93">C93</option>
                                		     <option value="MSI">MSI</option>
                                		     <option value="MSI+">MSI+</option>
                                		     <option value="CODABAR">CODABAR</option>
                                		     <option value="CODE11">CODE11</option>
                                		</optgroup>
                                </select>
        						<input type="button" value="{$MOD.LBL_INSERT_BARCODE_TO_TEXT}" class="crmButton small create" onclick="InsertIntoTemplate('barcodeval');">&nbsp;&nbsp;<a href="modules/PDFMaker/Barcodes.php" target="_new"><img src="themes/images/help_icon.gif" border="0" align="absmiddle"></a>
      						</td>
    					</tr>
                        </table>
                      </div>
                      {*********************************************Products bloc DIV*************************************************}
                      <div style="display:none;" id="products_div">
                        <table class="dvtContentSpace" width="100%" border="0" cellpadding="3" cellspacing="0" style="padding:10px;">
                        <tr><td>
                          
                          <div id="product_div" style="display:{$DISPLAY_PRODUCT_DIV};">
                          <table width="100%"  border="0" cellspacing="0" cellpadding="5" >
            					<tr>
            						<td valign=top class="small cellLabel" width="200px"><strong>{$MOD.LBL_ARTICLE}:</strong></td>
            						<td class="cellText small" valign=top>
            						<select name="articelvar" id="articelvar" class="classname">
                                    		{html_options  options=$ARTICLE_STRINGS}
                                    </select>
                                    <input type="button" value="{$MOD.LBL_INSERT_TO_TEXT}" class="crmButton small create" onclick="InsertIntoTemplate('articelvar');">
              						</td>
            					</tr>
            			        {* insert products & services fields into text *}
                                <tr>
            						<td valign=top class="small cellLabel"><strong>*{$MOD.LBL_PRODUCTS_AVLBL}:</strong></td>
            						<td class="cellText small" valign=top>
                                    <select name="psfields" id="psfields" class="classname">
                                        {html_options  options=$SELECT_PRODUCT_FIELD}
                                    </select>
            						<input type="button" value="{$MOD.LBL_INSERT_TO_TEXT}" class="crmButton small create" onclick="InsertIntoTemplate('psfields');">            						
              						</td>
            					</tr>
            					{* products fields *}                                
                                <tr>
            						<td valign=top class="small cellLabel"><strong>*{$MOD.LBL_PRODUCTS_FIELDS}:</strong></td>
            						<td class="cellText small" valign=top>
                                    <select name="productfields" id="productfields" class="classname">
                                        {html_options  options=$PRODUCTS_FIELDS}
                                    </select>
            						<input type="button" value="{$MOD.LBL_INSERT_TO_TEXT}" class="crmButton small create" onclick="InsertIntoTemplate('productfields');">            						
              						</td>
            					</tr>
                                {* services fields *}                                
                                <tr>
            						<td valign=top class="small cellLabel"><strong>*{$MOD.LBL_SERVICES_FIELDS}:</strong></td>
            						<td class="cellText small" valign=top>
                                    <select name="servicesfields" id="servicesfields" class="classname">
                                        {html_options  options=$SERVICES_FIELDS}
                                    </select>
            						<input type="button" value="{$MOD.LBL_INSERT_TO_TEXT}" class="crmButton small create" onclick="InsertIntoTemplate('servicesfields');">            						
              						</td>
            					</tr>            					
            					{* product bloc tpl which is the same as in main Properties tab*}
            					<tr>                            
            						<td valign=top class="small cellLabel"><strong>{$MOD.LBL_PRODUCT_BLOC_TPL}:</strong></td>
            						<td class="cellText small" valign=top>
            						<select name="productbloctpl2" id="productbloctpl2" class="classname">
                                    		{html_options  options=$PRODUCT_BLOC_TPL}
                                   </select>
                                   <input type="button" value="{$MOD.LBL_INSERT_TO_TEXT}" class="crmButton small create" onclick="InsertIntoTemplate('productbloctpl2');"/>
              		               </td>              					
                               </tr>
                               <tr>
                                <td colspan="2"><small>{$MOD.LBL_PRODUCT_FIELD_INFO}</small></td>
                               </tr>
            			  </table>
                          </div>                          
                          
                          <div id="no_product_div" style="padding:15px;text-align:center;display:{$DISPLAY_NO_PRODUCT_DIV};">
                          <b>{$MOD.LBL_NOPRODUCT_BLOC}</b>
                          </div>
                          
                        </td></tr>
                        </table>
                      </div>
                      
                      
                      {*********************************************Settings DIV*************************************************}
                      <div style="display:none;" id="settings_div">
                        <table class="dvtContentSpace" width="100%" border="0" cellpadding="3" cellspacing="0" style="padding:10px;">
                        {* pdf format settings *}
    					<tr>
    						<td valign=top class="small cellLabel"><strong>{$MOD.LBL_PDF_FORMAT}:</strong></td>
    						<td class="cellText small" valign=top>
                                <select name="pdf_format" id="pdf_format" class="classname">
                                    {html_options  options=$FORMATS selected=$SELECT_FORMAT}
                                </select>
      						</td>
    					</tr>
    					{* pdf orientation settings *}
                        <tr>
    						<td valign=top class="small cellLabel"><strong>{$MOD.LBL_PDF_ORIENTATION}:</strong></td>
    						<td class="cellText small" valign=top>
                                <select name="pdf_orientation" id="pdf_orientation" class="classname">
                                    {html_options  options=$ORIENTATIONS selected=$SELECT_ORIENTATION}
                                </select>
      						</td>
    					</tr>
    					{* encoding *}
    					{*
                        <tr>
    					   <td valign=top class="small cellLabel" title="{$MOD.LBL_ENCODING_TITLE}"><strong>{$MOD.LBL_ENCODING}:</strong></td>
    					   <td class="cellText small" valign=top>
                            <select name="encoding" id="encoding" class="classname">
                                {html_options  options=$ENCODINGS selected=$SELECT_ENCODING}
                            </select>
      					   </td>
    					</tr>
    					*}
    					{* ignored picklist values settings *}
    					<tr>
    					   <td valign=top class="small cellLabel" title="{$MOD.LBL_IGNORE_PICKLIST_VALUES_DESC}"><strong>{$MOD.LBL_IGNORE_PICKLIST_VALUES}:</strong></td>
    					   <td class="cellText small" valign="top" title="{$MOD.LBL_IGNORE_PICKLIST_VALUES_DESC}"><input type="text" name="ignore_picklist_values" value="{$IGNORE_PICKLIST_VALUES}" class="detailedViewTextBox"/></td>
    					</tr>
                        {* pdf margin settings *}
                        {assign var=margin_input_width value='50px'}
                        {assign var=margin_label_width value='50px'}
                        <tr>
    						<td valign=top class="small cellLabel"><strong>{$MOD.LBL_MARGINS}:</strong></td>
    						<td class="cellText small" valign="top">
                                <table>
                                   <tr>
                                       <td align="right" nowrap><b>{$MOD.LBL_TOP}</b></td>
                                       <td>
                                           <input type="text" name="margin_top" id="margin_top" class="detailedViewTextBox" value="{$MARGINS.top}" style="width:{$margin_input_width}" onKeyUp="ControlNumber('margin_top',false);">
                                       </td>
                                       <td align="right" nowrap><b>{$MOD.LBL_BOTTOM}</b></td>
                                       <td>
                                           <input type="text" name="margin_bottom" id="margin_bottom" class="detailedViewTextBox" value="{$MARGINS.bottom}" style="width:{$margin_input_width}" onKeyUp="ControlNumber('margin_bottom',false);">
                                       </td>
                                       <td align="right" nowrap><b>{$MOD.LBL_LEFT}</b></td>
                                       <td>
                                           <input type="text" name="margin_left"  id="margin_left" class="detailedViewTextBox" value="{$MARGINS.left}" style="width:{$margin_input_width}" onKeyUp="ControlNumber('margin_left',false);">
                                       </td>
                                       <td align="right" nowrap><b>{$MOD.LBL_RIGHT}</b></td>
                                       <td>
                                           <input type="text" name="margin_right" id="margin_right" class="detailedViewTextBox" value="{$MARGINS.right}" style="width:{$margin_input_width}" onKeyUp="ControlNumber('margin_right',false);">
                                       </td>
                                   </tr>
                                </table>
                          	</td>
    					</tr>
                        {* decimal settings *}    					
    					<tr>
    					   <td valign=top class="small cellLabel"><strong>{$MOD.LBL_DECIMALS}:</strong></td>
    						<td class="cellText small" valign="top">
                                <table>
                                   <tr>
                                       <td align="right" nowrap><b>{$MOD.LBL_DEC_POINT}</b></td>
                                       <td><input type="text" maxlength="2" name="dec_point" class="detailedViewTextBox" value="{$DECIMALS.point}" style="width:{$margin_input_width}"/></td>
                                       
                                       <td align="right" nowrap><b>{$MOD.LBL_DEC_DECIMALS}</b></td>
                                       <td><input type="text" maxlength="2" name="dec_decimals" class="detailedViewTextBox" value="{$DECIMALS.decimals}" style="width:{$margin_input_width}"/></td>
                                       
                                       <td align="right" nowrap><b>{$MOD.LBL_DEC_THOUSANDS}</b></td>
                                       <td><input type="text" maxlength="2" name="dec_thousands"  class="detailedViewTextBox" value="{$DECIMALS.thousands}" style="width:{$margin_input_width}"/></td>                                       
                                   </tr>
                                </table>
                          	</td>
    					</tr>    					
                        </table>
                      </div>
                      
                     {************************************** END OF TABS BLOCK *************************************}                         
                    </td></tr>
                    <tr><td class="small" style="text-align:center;padding:15px 0px 10px 0px;">
					   <input type="submit" value="{$APP.LBL_APPLY_BUTTON_LABEL}" class="crmButton small create" onclick="document.PDFMakerEdit.redirect.value='false'; return savePDF();" >&nbsp;&nbsp;
                       <input type="submit" value="{$APP.LBL_SAVE_BUTTON_LABEL}" class="crmButton small save" onclick="return savePDF();" >&nbsp;&nbsp;            			
            		   {if $smarty.request.applied eq 'true'}
            		     <input type="button" value="{$APP.LBL_CANCEL_BUTTON_LABEL}" class="crmButton small cancel" onclick="window.location.href='index.php?action=DetailViewPDFTemplate&module=PDFMaker&templateid={$SAVETEMPLATEID}&parenttab=Tools';" />
            		   {else}
                         <input type="button" value="{$APP.LBL_CANCEL_BUTTON_LABEL}" class="crmButton small cancel" onclick="window.history.back()" />
                       {/if}            			
					</td></tr>
                    </table>
                    
                   
                    
                    <table class="small" width="100%" border="0" cellpadding="3" cellspacing="0"><tr>
                          <td style="width: 10px;" nowrap="nowrap">&nbsp;</td>
                          <td style="width: 15%;" class="dvtSelectedCell" id="body_tab2" onclick="showHideTab2('body');" width="75" align="center" nowrap="nowrap"><b>{$MOD.LBL_BODY}</b></td>
           				  <td class="dvtUnSelectedCell" id="header_tab2" onclick="showHideTab2('header');" align="center" nowrap="nowrap"><b>{$MOD.LBL_HEADER_TAB}</b></td>
           				  <td class="dvtUnSelectedCell" id="footer_tab2" onclick="showHideTab2('footer');" align="center" nowrap="nowrap"><b>{$MOD.LBL_FOOTER_TAB}</b></td>
                          <td style="width: 50%;" nowrap="nowrap">&nbsp;</td> 
                    </tr></table>
 
                    {literal}   
                        <script type="text/javascript" src="include/ckeditor/ckeditor.js"></script>
                    {/literal} 

                    {*********************************************BODY DIV*************************************************}
                    <div style="diplay:block;" id="body_div2"> 
                        <textarea name="body" id="body" style="width:90%;height:700px" class=small tabindex="5">{$BODY}</textarea>
                    </div>
                    
                    <script type="text/javascript">
                    	{php} if (file_exists("kcfinder/browse.php")) { {/php}
                            {literal} CKEDITOR.replace( 'body',{customConfig:'../../../modules/PDFMaker/fck_config_kcfinder.js'} );  {/literal} 
                        {php} } else { {/php} 
                            {literal} CKEDITOR.replace( 'body',{customConfig:'../../../modules/PDFMaker/fck_config.js'} ); {/literal} 
                        {php} } {/php}
                    </script>
                    
                    {*********************************************Header DIV*************************************************}
                    <div style="display:none;" id="header_div2">
                        <textarea name="header_body" id="header_body" style="width:90%;height:200px" class="small">{$HEADER}</textarea>
                    </div>
                    
                    <script type="text/javascript">
                    	{php} if (file_exists("kcfinder/browse.php")) { {/php}
                            {literal} CKEDITOR.replace( 'header_body',{customConfig:'../../../modules/PDFMaker/fck_config_kcfinder.js'} );  {/literal} 
                        {php} } else { {/php} 
                            {literal} CKEDITOR.replace( 'header_body',{customConfig:'../../../modules/PDFMaker/fck_config.js'} ); {/literal} 
                        {php} } {/php}
                    </script>
                    {*********************************************Footer DIV*************************************************}
                    <div style="display:none;" id="footer_div2">
                        <textarea name="footer_body" id="footer_body" style="width:90%;height:200px" class="small">{$FOOTER}</textarea>
                    </div>

                    <script type="text/javascript">
                    	{php} if (file_exists("kcfinder/browse.php")) { {/php}
                            {literal} CKEDITOR.replace( 'footer_body',{customConfig:'../../../modules/PDFMaker/fck_config_kcfinder.js'} );  {/literal} 
                        {php} } else { {/php} 
                            {literal} CKEDITOR.replace( 'footer_body',{customConfig:'../../../modules/PDFMaker/fck_config.js'} ); {/literal} 
                        {php} } {/php}
                    </script>
                     
                    {php} if (file_exists("kcfinder/browse.php")) { {/php}
                            {literal} <script type="text/javascript" src="modules/PDFMaker/fck_config_kcfinder.js"></script> {/literal} 
                    {php} } else { {/php} 
                            {literal} <script type="text/javascript" src="modules/PDFMaker/fck_config.js"></script> {/literal} 
                    {php} } {/php}                                        
                        

                    <table width="100%"  border="0" cellspacing="0" cellpadding="5" >
                        <tr><td class="small" style="text-align:center;padding:10px 0px 10px 0px;" colspan="3">
    					   <input type="submit" value="{$APP.LBL_APPLY_BUTTON_LABEL}" class="crmButton small create" onclick="document.PDFMakerEdit.redirect.value='false'; return savePDF();" >&nbsp;&nbsp;
                           <input type="submit" value="{$APP.LBL_SAVE_BUTTON_LABEL}" class="crmButton small save" onclick="return savePDF();" >&nbsp;&nbsp;            			
                		   {if $smarty.request.applied eq 'true'}
                		     <input type="button" value="{$APP.LBL_CANCEL_BUTTON_LABEL}" class="crmButton small cancel" onclick="window.location.href='index.php?action=DetailViewPDFTemplate&module=PDFMaker&templateid={$SAVETEMPLATEID}&parenttab=Tools';" />
                		   {else}
                             <input type="button" value="{$APP.LBL_CANCEL_BUTTON_LABEL}" class="crmButton small cancel" onclick="window.history.back()" />
                           {/if}            			
    		   	        </td></tr>
                    </table>                                  
                    
				</td>
				</tr><tr><td align="center" class="small" style="color: rgb(153, 153, 153);">{$MOD.PDF_MAKER} {$VERSION} {$MOD.COPYRIGHT}</td></tr>
				</table>
			</td>
			</tr>
                        </form>
			</table>
 
<script>

var selectedTab='properties';
var selectedTab2='body';

function check4null(form)
{ldelim}

        var isError = false;
        var errorMessage = "";
        // Here we decide whether to submit the form.
        if (trim(form.templatename.value) =='') {ldelim}
                isError = true;
                errorMessage += "\n template name";
                form.templatename.focus();
        {rdelim}
        if (trim(form.foldername.value) =='') {ldelim}
                isError = true;
                errorMessage += "\n folder name";
                form.foldername.focus();
        {rdelim}
        if (trim(form.subject.value) =='') {ldelim}
                isError = true;
                errorMessage += "\n subject";
                form.subject.focus();
        {rdelim}

        // Here we decide whether to submit the form.
        if (isError == true) {ldelim}
                alert("{$MOD.LBL_MISSING_FIELDS}" + errorMessage);
                return false;
        {rdelim}
 return true;

{rdelim}

var module_blocks = new Array();

{foreach item=moduleblocks key=blockname from=$MODULE_BLOCKS}
    module_blocks["{$blockname}"] = new Array({$moduleblocks});
{/foreach}

var module_fields = new Array();

{foreach item=modulefields key=modulename from=$MODULE_FIELDS}
    module_fields["{$modulename}"] = new Array({$modulefields});
{/foreach}

var selected_module='{$SELECTMODULE}';

function fillModuleFields(first,second_name)
{ldelim}
    second = document.getElementById(second_name);    
    optionTest = true;
    lgth = second.options.length - 1;

    second.options[lgth] = null;
    if (second.options[lgth]) optionTest = false;
    if (!optionTest) return;
    var box = first;
    var module = box.options[box.selectedIndex].value;
    if (!module) return;

    var box2 = second;

    //box2.options.length = 0;

    var optgroups = box2.childNodes;
    for(i = optgroups.length - 1 ; i >= 0 ; i--)
    {ldelim}
            box2.removeChild(optgroups[i]);
    {rdelim}

    var blocks = module_blocks[module];
    var blocks_length = blocks.length;
    if(second_name=='filename_fields')
    {ldelim}
        objOption=document.createElement("option");
        objOption.innerHTML = '{$MOD.LBL_SELECT_MODULE_FIELD}';
        objOption.value = '';
        box2.appendChild(objOption);
        
        optGroup = document.createElement('optgroup');
        optGroup.label = '{$MOD.LBL_COMMON_FILEINFO}';
        box2.appendChild(optGroup); 
        
        {foreach item=field key=field_val from=$FILENAME_FIELDS}
            objOption=document.createElement("option");
            objOption.innerHTML = '{$field}';
            objOption.value = '{$field_val}';
            optGroup.appendChild(objOption);
        {/foreach}
        
        if(module=='Invoice' || module=='Quotes' || module=='SalesOrder' || module=='PurchaseOrder' || module=='Issuecards' || module=='Receiptcards' || module=="Creditnote" || module=="StornoInvoice")
            blocks_length-=2;
    {rdelim}  
     
    for(b=0;b<blocks_length;b+=2)
    {ldelim}
            optGroup = document.createElement('optgroup');
            optGroup.label = blocks[b];
            box2.appendChild(optGroup);

            var list = module_fields[module+'|'+ blocks[b+1]];

    		for(i=0;i<list.length;i+=2)
    		{ldelim}
    		      //<optgroup label="Addresse" class=\"select\" style=\"border:none\">

                  objOption=document.createElement("option");
                  objOption.innerHTML = list[i];
                  objOption.value = list[i+1];

                  optGroup.appendChild(objOption);
    		{rdelim}
    {rdelim}
    
    return module;    
{rdelim}

var all_related_modules = new Array();

{foreach item=related_modules key=relatedmodulename from=$ALL_RELATED_MODULES}
    all_related_modules["{$relatedmodulename}"] = new Array('{$MOD.LBL_SELECT_MODULE}','none'{foreach item=module1 from=$related_modules} ,'{$APP.$module1|escape}','{$module1}'{/foreach});
{/foreach}

function change_relatedmodulesorce(first,second_name)
{ldelim} 
    second = document.getElementById(second_name);

    optionTest = true;
    lgth = second.options.length - 1;

    second.options[lgth] = null;
    if (second.options[lgth]) optionTest = false;
    if (!optionTest) return;
    var box = first;
    var number = box.options[box.selectedIndex].value;
    if (!number) return;

    var box2 = second;

    //box2.options.length = 0;

    var optgroups = box2.childNodes;
    for(i = optgroups.length - 1 ; i >= 0 ; i--)
    {ldelim}
            box2.removeChild(optgroups[i]);
    {rdelim}

    var list = all_related_modules[number];

    for(i=0;i<list.length;i+=2)
    {ldelim}
          objOption=document.createElement("option");
          objOption.innerHTML = list[i];
          objOption.value = list[i+1];

          box2.appendChild(objOption);
    {rdelim}

    clearRelatedModuleFields();
{rdelim}

function clearRelatedModuleFields()
{ldelim}
    second = document.getElementById("relatedmodulefields");

    lgth = second.options.length - 1;

    second.options[lgth] = null;
    if (second.options[lgth]) optionTest = false;
    if (!optionTest) return;

    var box2 = second;

    var optgroups = box2.childNodes;
    for(i = optgroups.length - 1 ; i >= 0 ; i--)
    {ldelim}
            box2.removeChild(optgroups[i]);
    {rdelim}

    objOption=document.createElement("option");
    objOption.innerHTML = "{$MOD.LBL_SELECT_MODULE_FIELD}";
    objOption.value = "";

    box2.appendChild(objOption);

{rdelim}

var related_module_fields = new Array();

{foreach item=relatedmodulefields key=relatedmodulename from=$RELATED_MODULE_FIELDS}
    related_module_fields["{$relatedmodulename}"] = new Array({$relatedmodulefields});
{/foreach}

function change_relatedmodule(first,second_name)
{ldelim}
    second = document.getElementById(second_name);

    optionTest = true;
    lgth = second.options.length - 1;

    second.options[lgth] = null;
    if (second.options[lgth]) optionTest = false;
    if (!optionTest) return;
    var box = first;
    var number = box.options[box.selectedIndex].value;
    if (!number) return;

    var box2 = second;

    //box2.options.length = 0;

    var optgroups = box2.childNodes;
    for(i = optgroups.length - 1 ; i >= 0 ; i--)
    {ldelim}
            box2.removeChild(optgroups[i]);
    {rdelim}

    if (number == "none")
    {ldelim}
        objOption=document.createElement("option");
        objOption.innerHTML = "{$MOD.LBL_SELECT_MODULE_FIELD}";
        objOption.value = "";

        box2.appendChild(objOption);
    {rdelim}
    else
    {ldelim}
        var blocks = module_blocks[number];

        for(b=0;b<blocks.length;b+=2)
        {ldelim}
            var list = related_module_fields[number+'|'+ blocks[b+1]];

    		if (list.length > 0)
    		{ldelim}

    		    optGroup = document.createElement('optgroup');
                optGroup.label = blocks[b];
                box2.appendChild(optGroup);

        		for(i=0;i<list.length;i+=2)
        		{ldelim}
                      objOption=document.createElement("option");
                      objOption.innerHTML = list[i];
                      objOption.value = list[i+1];


                      optGroup.appendChild(objOption);
        		{rdelim}
    		{rdelim}
        {rdelim}
    {rdelim}
{rdelim}


function InsertIntoTemplate(element)
{ldelim}

    selectField =  document.getElementById(element).value;

    if (selectedTab2 == "body")
        var oEditor = CKEDITOR.instances.body;    
    else if (selectedTab2 == "header")
        var oEditor = CKEDITOR.instances.header_body;
    else if (selectedTab2 == "footer")
        var oEditor = CKEDITOR.instances.footer_body;
    

    if(element!='header_var' && element!='footer_var' && element!='hmodulefields' && element!='fmodulefields' && element!='dateval')
    {ldelim}
      	 if (selectField != '')
      	 {ldelim}
               if (selectField == 'ORGANIZATION_STAMP_SIGNATURE')
       	       {ldelim}
       	           insert_value = '{$COMPANY_STAMP_SIGNATURE}';
      	       {rdelim}
               else if (selectField == 'COMPANY_LOGO')
       	       {ldelim}
       	           insert_value = '{$COMPANYLOGO}';
      	       {rdelim}
               else if (selectField == 'ORGANIZATION_HEADER_SIGNATURE')
       	       {ldelim}
       	           insert_value = '{$COMPANY_HEADER_SIGNATURE}';
      	       {rdelim}
               else
      	       {ldelim}
                   if (element == "articelvar")
                      insert_value = '#'+selectField+'#';
                   else if (element == "relatedmodulefields")
                      insert_value = '$R_'+selectField+'$';                   
                   else if(element == "productbloctpl" || element == "productbloctpl2")
                      insert_value = selectField;
                   else if(element == "global_lang")
                      insert_value = '%G_'+selectField+'%';
                   else if(element == "module_lang")
                      insert_value = '%M_'+selectField+'%';  
                   else if(element == "barcodeval")
                      insert_value = '[BARCODE|'+selectField+'=YOURCODE|BARCODE]'; 
                   else if(element == "customfunction")
                      insert_value = '[CUSTOMFUNCTION|'+selectField+'|CUSTOMFUNCTION]'; 
                   else
                      insert_value = '$'+selectField+'$';


               {rdelim}
               oEditor.insertHtml(insert_value);
      	 {rdelim}

    {rdelim}
    else
    {ldelim}
        
        if (selectField != '')
        {ldelim}
            if(element=='hmodulefields' || element=='fmodulefields' )
                oEditor.insertHtml('$'+selectField+'$');
            else
                oEditor.insertHtml(selectField);
        {rdelim}
    {rdelim}
{rdelim}



function savePDF()
{ldelim}
    var error = 0;

    if (!ControlNumber('margin_top',true) || !ControlNumber('margin_bottom',true) || !ControlNumber('margin_left',true) || !ControlNumber('margin_right',true))
       return false;
    else
       return true;
    
{rdelim}

function ControlNumber(elid,final)
{ldelim}

    var control_number = document.getElementById(elid).value;

    {literal}

    var re = new Array();
    re[1] = new RegExp("^([0-9])");

    re[2] = new RegExp("^[0-9]{1}[.]$");

    re[3] = new RegExp("^[0-9]{1}[.][0-9]{1}$");

    {/literal}

    if (control_number.length > 3 || !re[control_number.length].test(control_number) || (final == true && control_number.length == 2))
    {ldelim}
        alert("{$MOD.LBL_MARGIN_ERROR}");
        document.getElementById(elid).focus();
        return false;
    {rdelim}
    else
    {ldelim}
        return true;
    {rdelim}

{rdelim}

function refreshPosition(type)
{ldelim}

    var i;

    selectbox = document.getElementById(type + "_position");
    selectbox_value = selectbox.value;

    for(i=selectbox.options.length-1;i>=0;i--)
    {ldelim}
        selectbox.remove(i);
    {rdelim}


    el1 = document.getElementById(type + "_function_left").value;
    el2 = document.getElementById(type + "_function_center").value;
    el3 = document.getElementById(type + "_function_right").value;


    selectbox.options[selectbox.options.length] = new Option("{$MOD.LBL_EMPTY_IMAGE}", "empty");
    if (el1 == "hf_function_1") selectbox.options[selectbox.options.length] = new Option("{$MOD.LBL_LEFT}", "left");
    if (el2 == "hf_function_1") selectbox.options[selectbox.options.length] = new Option("{$MOD.LBL_CENTER}", "center");
    if (el3 == "hf_function_1") selectbox.options[selectbox.options.length] = new Option("{$MOD.LBL_RIGHT}", "right");

    selectbox.value = selectbox_value;

{rdelim}

function showHideTab(tabname)
{ldelim}
    document.getElementById(selectedTab+'_tab').className="dvtUnSelectedCell";    
    document.getElementById(tabname+'_tab').className='dvtSelectedCell';
    
    document.getElementById(selectedTab+'_div').style.display='none';
    document.getElementById(tabname+'_div').style.display='block';
    var formerTab=selectedTab;
    selectedTab=tabname;     
{rdelim}



function showHideTab2(tabname)
{ldelim}
    document.getElementById(selectedTab2+'_tab2').className="dvtUnSelectedCell";    
    document.getElementById(tabname+'_tab2').className='dvtSelectedCell';
    
    document.getElementById(selectedTab2+'_variables').style.display='none';  
    document.getElementById(tabname+'_variables').style.display='';
    
    document.getElementById(selectedTab2+'_div2').style.display='none';
    document.getElementById(tabname+'_div2').style.display='block';

    if (tabname == "body")
    {ldelim}
        document.getElementById('no_product_div').style.display='none';
        document.getElementById('product_div').style.display = '';
        document.getElementById('product_bloc_tpl_row').style.display='';        
    {rdelim}    
    else
    {ldelim}
        document.getElementById('product_bloc_tpl_row').style.display='none';
        document.getElementById('product_div').style.display = 'none';
        document.getElementById('no_product_div').style.display='';         
    {rdelim}
    
    
    var formerTab=selectedTab2;
    selectedTab2=tabname;
{rdelim}


{literal}
function insertFieldIntoFilename(val)
{
    if(val!='')
        document.getElementById('nameOfFile').value+='$'+val+'$';    
}
{/literal}
</script>
