<br/>
<table align="center" border="0" cellpadding="0" cellspacing="0" width="100%">
	<tr>   		
   		<td class="showPanelBg" valign="top" width="100%">
   			<table  cellpadding="0" cellspacing="0" width="100%" border=0>
  				<tr>
				<td width="50%" valign=top>
					<form name="install"  method="POST" action="index.php">
						<input type="hidden" name="module" value="PDFMaker"/>
						<input type="hidden" name="action" value="install"/>
						<table align="center" cellpadding="15" cellspacing="0" width="85%" class="mailClient importLeadUI small" border="0">
							<tr>
								<td colspan="2" valign="middle" align="left" class="mailClientBg genHeaderSmall">{$MOD.LBL_MODULE_NAME} {$MOD.LBL_INSTALL}</td>
								<br/>
							</tr>
							{if $STEP eq "0"}
							<tr>
    							<td border="0" cellpadding="5" cellspacing="0" width="70%">
    							<table width="100%">
    							     <tr>
                                       <td align="left" valign="top" style="padding-left:40px;">
                                       <input type="hidden" name="installtype" value="validate"/>                                       
                                       <span class="genHeaderSmall">{$MOD.LBL_WELCOME}</span>
  									   </td>
     								 </tr>
                                     <tr>
     								   <td align="left" valign="top" class="small" style="padding-left:40px;color:black;">
     								   {$MOD.LBL_WELCOME_DESC}
                                       </td>  
     								 </tr>
     								  <tr>
     								   <td align="left" valign="top" class="small" style="padding-left:40px;color:black;">
     								   {$MOD.LBL_WELCOME_FINISH}
                                       </td>  
     								 </tr>
                                     
                                     <tr><td align="left" valign="top" class="small">&nbsp;</td></tr>
                                       
                                     <tr>
                                       <td align="left" valign="top" style="padding-left:40px;">
                                       <input type="hidden" name="installtype" value="validate"/>                                       
                                       <strong>{$MOD.LBL_INSERT_KEY}</strong>
  									   </td>
     								 </tr>
                                     <tr>
     								   <td align="left" valign="top" class="small" style="padding-left:40px;color:red;">
     								   {$MOD.LBL_ONLINE_ASSURE}
                                       </td>  
     								 </tr>   								
     								 <tr>
     								   <td align="left" valign="top" class="small" style="padding-left:40px;padding-top:10px;">
     								   <input type="text" class="small detailedViewTextBox" name="key" value="" style="width:200px;font-size:13px;"/>
                                       </td>  
     								 </tr>
    							</table>    							
    						    </td>
    						    <td border="0" cellpadding="5" cellspacing="0" width="30%">
    							&nbsp;
    							</td>
 							</tr>
 							{elseif $STEP eq "1"}
                            <tr>
    							<td border="0" cellpadding="5" cellspacing="0" width="70%">
    							<table width="100%">
    							     <tr>
                                       <td align="left" valign="top" style="padding-left:40px;">
                                       <input type="hidden" name="installtype" value="download_src"/>                                       
                                       <span class="genHeaderSmall">{$MOD.LBL_DOWNLOAD_SRC}</span>
  									   </td>
     								 </tr>
                                     <tr>
     								   <td align="left" valign="top" class="small" style="padding-left:40px;">
     								   {$MOD.LBL_DOWNLOAD_SRC_DESC}
                                       </td>                                         
     								 </tr>
     								 {if $MB_STRING_EXISTS eq 'false'}
                                       <tr>
                                        <td align="left" valign="top" class="small" style="padding-left:40px;color:red;">
                                        {$MOD.LBL_MB_STRING_ERROR}
                                        </td>                                       
                                       </tr>
                                     {/if}
    							</table>    							
    						    </td>
    						    <td border="0" cellpadding="5" cellspacing="0" width="30%">
    							&nbsp;
    							</td>
 							</tr>
 							{elseif $STEP eq "2"}
							<input type="hidden" name="installtype" value="redirect_recalculate" />
							<tr>
    							<td border="0" cellpadding="5" cellspacing="0" width="70%">
    							<table width="100%">
    							     <tr>
                                       <td align="left" valign="top" style="padding-left:40px;">                                                                               
                                       <span class="genHeaderSmall">{$MOD.LBL_FINAL_INSTRUCTIONS}</span>
  									   </td>
     								 </tr>
     								 <tr>
     								   <td align="left" valign="top" class="small" style="padding-left:40px;">   								    
                                        {$MOD.LBL_RECALCULATE_RIGHTS}
                                       </td>
                                     </tr>
    							</table>    							
    						    </td>
    						    <td border="0" cellpadding="5" cellspacing="0" width="50%">
    							&nbsp;
    							</td>
 							</tr>
 							{elseif $STEP eq 'error'}
 							<tr>
    							<td border="0" cellpadding="5" cellspacing="0" width="50%">
    							<table width="100%">
    							     <tr>
                                       <td align="left" valign="top" style="padding-left:40px;">                                       
                                       <span class="genHeaderSmall">{$MOD.LBL_INSTAL_ERROR}</span>
  									   </td>
     								 </tr>
                                     <tr>
     								   <td align="left" valign="top" class="small" style="padding-left:40px;">
     								    {if $INVALID neq "true"}
                                            {$MOD.LBL_ERROR_TBL}:<br/>
                                        {/if}
     								   {foreach item=tbl from=$ERROR_TBL}
     								   <pre>{$tbl}</pre><br />
     								   {/foreach}
                                       </td>  
     								 </tr>     								 
    							</table>    							
    						    </td>
    						    <td border="0" cellpadding="5" cellspacing="0" width="50%">
    							&nbsp;
    							</td>
 							</tr>
 							{/if}
 							<tr>
								<td align="center" colspan="2" border=0 cellspacing=0 cellpadding=5 width=98% class="layerPopupTransport">	
									{if $STEP eq '0'}
                                        <input type="submit" id="validate_button" value="{$MOD.LBL_VALIDATE}" class="crmbutton small create"/>&nbsp;&nbsp;
                                        <input type="button" id="order_button" value="{$MOD.LBL_ORDER_NOW}" class="crmbutton small cancel" onclick="window.location.href='http://www.vtiger.sk/purchase-order'"/>
              						{elseif $STEP eq '1'}
              						    <input type="submit" id="download_button" value="{$MOD.LBL_DOWNLOAD}" class="crmbutton small create"/>&nbsp;&nbsp;
                                    {elseif $STEP eq '2'}
              						    <input type="submit" id="next_button" value="{$MOD.LBL_FINISH}" class="crmbutton small create"/>&nbsp;&nbsp;
                                    {elseif $STEP eq "error" && $INVALID neq "true"}
              						    <input type="button" id="refresh_button" value="{$MOD.LBL_RELOAD}" class="crmbutton small create" onclick="window.location.reload();"/>&nbsp;&nbsp;
                                    {/if} 
                                                 						
                                    {if $STEP eq 'error'}
                                       {if $INVALID eq "true"}
                                        <input type="button" name="{$APP.LBL_BACK}" value="{$APP.LBL_BACK}" class="crmbutton small create" onclick="window.history.back()" />
                                       {/if}
                                       <input type="button" name="{$APP.LBL_CANCEL_BUTTON_LABEL}" value="{$APP.LBL_CANCEL_BUTTON_LABEL}" class="crmbutton small cancel" onclick="window.location.href='index.php?module=Home&action=index&parenttab=My Home Page'" />
                                    {/if} 
								</td>
							</tr>
 						</table>
 					</form>
 				</td>
 				</tr>
 			</table>
 		</td>
 	</tr>
</table>

<script>
function changeInstallType(type)
{ldelim}

   document.getElementById('next_button').disabled = false;
   document.getElementById('next_button').style.display = "inline";
    
   if (type == "express")
   {ldelim}
        bad_files_count = document.getElementById('bad_files').value;
        
        if (bad_files_count != "0") 
        {ldelim}
           document.getElementById('next_button').disabled = true;
           document.getElementById('next_button').style.display = "none";
        {rdelim}          
        
        document.getElementById('total_steps').innerHTML='4';
   {rdelim}
   else if (type == "custom")
   {ldelim}        
        document.getElementById('total_steps').innerHTML='5';
   {rdelim}
{rdelim}

{literal}    
function controlPermissions()
{
    
    
    var url = "module=PDFMaker&action=PDFMakerAjax&file=controlPermissions";
    new Ajax.Request(
                    'index.php',
                      {queue: {position: 'end', scope: 'command'},
                              method: 'post',
                              postBody:url,
                              onComplete: function(response) {
                                      document.getElementById('list_permissions').innerHTML = response.responseText;
                                      
                                      bad_files_count = document.getElementById('bad_files').value;
                                      
                                      type = document.getElementById('installexpress').checked;
                                      
                                      if (type == true && bad_files_count == "0")
                                      {
                                          document.getElementById('next_button').disabled = false;
                                          document.getElementById('next_button').style.display = "inline";
                                          
                                          document.getElementById('btn_control_permissions').style.display = "none"; 
                                      }
                              }
                      }
                      );
                  
}
{/literal}    
</script>
