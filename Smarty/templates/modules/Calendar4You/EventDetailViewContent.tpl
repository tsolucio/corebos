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
<table border="0" cellpadding="0" cellspacing="0" width="100%">
	<tr>
		<td valign=top align=left >
            <table border=0 cellspacing=0 cellpadding=3 width=100%>
                <tr valign=top>
                    <td align=left>					
    					<form action="index.php" method="post" name="DetailView" id="form" onsubmit="VtigerJS_DialogBox.block();">
    					<input type="hidden" name="module" value="Calendar4You">
                        <input type="hidden" name="record" value="{$ID}">
                        <input type="hidden" name="activity_mode" value="{$ACTIVITY_MODE}">
                        <input type="hidden" name="isDuplicate" value=false>
                        <input type="hidden" name="action">
                        <input type="hidden" name="return_module">
                        <input type="hidden" name="return_action">
                        <input type="hidden" name="return_id">
                        <input type="hidden" name="user_id" value="{$USER_ID}">
    					<!-- content cache -->
    	
                        <table border=0 cellspacing=0 cellpadding=0 width=100%>
                            <tr>
                                <td style="padding:3px">
                                    <!-- General details -->
                                    {foreach key=header item=detail from=$BLOCKS}
                                        {if $header neq $APP.LBL_CUSTOM_INFORMATION}
                                            <table border=0 cellspacing=0 cellpadding=5 width=100% class="small">
                                                <tr>
                                                    {strip}<td colspan=4 class="dvInnerHeader"><b>{if $CMOD.$header neq ""}{$CMOD.$header}{else}{$header}{/if}</b></td>{/strip}
                                                </tr>
                                            </table>
                                        {/if}
                                    {/foreach}
                                    {if $ACTIVITYDATA.activitytype neq 'Task'}	
                                        <!-- display of fields starts -->
                                        <table border=0 cellspacing=0 cellpadding=5 width=100% >
                                            <tr>
                                                {if $LABEL.activitytype neq ''}
                                                    {assign var=type value=$ACTIVITYDATA.activitytype}
                                                    <td class="cellLabel" width="20%" align="right"><b>{$CMOD.LBL_EVENTTYPE}</b></td>
                                                    <td class="cellInfo" width="30%"align="left">{$type}</td>
                                                {/if}
                                                {if $LABEL.visibility neq ''}
                                                    {assign var=vblty value=$ACTIVITYDATA.visibility}
                                                    <td class="cellLabel" width="20%" align="right"><b>{$LABEL.visibility}</b></td>
                                                    <td class="cellInfo" width="30%" align="left" >{$vblty}</td>
                                                {/if}
                                            </tr>
                                            <tr>
                                                <td class="cellLabel" align="right"><b>{$CMOD.LBL_EVENTNAME}</b></td>
                                                <td class="cellInfo" colspan=3 align="left" >{$ACTIVITYDATA.subject}</td>
                                            </tr>
                                            {if $LABEL.description neq ''}
                                                <tr>
                                                    <td class="cellLabel" align="right" nowrap valign="top"><b>{$LABEL.description}</b></td>
                                                    <td class="cellInfo" valign="top" align="left" colspan="3" height="60px">{$ACTIVITYDATA.description}&nbsp;</td>
                                                </tr>
                                            {/if}
                							{if $LABEL.location neq ''}
                    							<tr>
                    								<td class="cellLabel" align="right" valign="top"><b>{$LABEL.location}</b></td>
                    								<td class="cellInfo" colspan=3 align="left" >{$ACTIVITYDATA.location}&nbsp;</td>
                    							</tr>
                							{/if}	
                                            <tr>
    							                {if $LABEL.eventstatus neq ''}
                                                    <td class="cellLabel" align="right" nowrap valign="top"><b>{$LABEL.eventstatus}</b></td>
                                                    <td class="cellInfo" align="left" nowrap valign="top">
                                                        {if $ACTIVITYDATA.eventstatus eq $APP.LBL_NOT_ACCESSIBLE}
                                                            <font color="red">{$ACTIVITYDATA.eventstatus}</font>
                                                        {else}
                                                            {$ACTIVITYDATA.eventstatus}
                                                        {/if}
    							                    </td>
                								{/if}
                								{if $LABEL.assigned_user_id neq ''}
                								    <td class="cellLabel" align="right" nowrap valign="top"><b>{$LABEL.assigned_user_id}</b></td>
                								    <td class="cellInfo" align="left" nowrap valign="top">{$ACTIVITYDATA.assigned_user_id}</td>
                								{/if}
                                            </tr>
                                            {if $LABEL.taskpriority neq '' || $LABEL.sendnotification neq ''}
                                                <tr>
    							                    {if $LABEL.taskpriority neq ''}
                                                        <td class="cellLabel" align="right" nowrap valign="top"><b>{$LABEL.taskpriority}</b></td>
                                                        <td class="cellInfo" align="left" nowrap valign="top">
                        									{if $ACTIVITYDATA.taskpriority eq $APP.LBL_NOT_ACCESSIBLE}
                        										<font color="red" >{$ACTIVITYDATA.taskpriority}</font>
                        									{else}
                        										{$ACTIVITYDATA.taskpriority}
                        									{/if}
    							                        </td>
    							                    {/if}
                                                    {if $LABEL.sendnotification neq ''}
                                                        <td class="cellLabel" align="right" nowrap valign="top"><b>{$LABEL.sendnotification}</b></td>
                                                        <td class="cellInfo" align="left" nowrap valign="top">{$ACTIVITYDATA.sendnotification}</td>
                                                    {/if}
                                                </tr>
                                            {/if}
                                            {if $LABEL.createdtime neq '' || $LABEL.modifiedtime neq ''}
                                                <tr>
                                                    <td class="cellLabel" align="right" nowrap valign="top"align="right">{if $LABEL.createdtime neq ''}<b>{$LABEL.createdtime}</b>{/if}</td>
                                                    <td class="cellInfo" align="left" nowrap valign="top">{if $LABEL.createdtime neq ''}{$ACTIVITYDATA.createdtime}{/if}</td>
                                                    <td class="cellLabel" align="right" nowrap valign="top"align="right">{if $LABEL.modifiedtime neq ''}<b>{$LABEL.modifiedtime}</b>{/if}</td>
                                                    <td class="cellInfo" align="left" nowrap valign="top">{if $LABEL.modifiedtime neq ''}{$ACTIVITYDATA.modifiedtime}{/if}</td>
                                                </tr>
                                            {/if}
                                        </table>
                                        <table border=0 cellspacing=1 cellpadding=0 width=100%>
                                            <tr>
                                                <td width=50% valign=top >
                                                    <table border=0 cellspacing=0 cellpadding=2 width=100%>
                                                        <tr><td class="mailSubHeader"><b>{$CMOD.LBL_EVENTSTAT}</b></td></tr>
                                                        <tr><td class=small>{$ACTIVITYDATA.starthr}:{$ACTIVITYDATA.startmin}{$ACTIVITYDATA.startfmt}</td></tr>
                                                        <tr><td class=small>{$ACTIVITYDATA.date_start}</td></tr>
                                                    </table>
                                                </td>
                                                <td width=50% valign=top >
                                                    <table border=0 cellspacing=0 cellpadding=2 width=100%>
                                                        <tr><td  class="mailSubHeader"><b>{$CMOD.LBL_EVENTEDAT}</b></td></tr>
                                                        <tr><td class=small>{$ACTIVITYDATA.endhr}:{$ACTIVITYDATA.endmin}{$ACTIVITYDATA.endfmt}</td></tr>
                                                        <tr><td class=small>{$ACTIVITYDATA.due_date}</td></tr>
                                                    </table>
                                                </td>
                                            </tr>
                                        </table>
                                        {if $CUSTOM_FIELDS_DATA|@count > 0}
                                            <table border=0 cellspacing=0 cellpadding=5 width=100% >
                                                <tr>{strip}
                                                    <td colspan=4 class="tableHeading">
                                                    <b>{$APP.LBL_CUSTOM_INFORMATION}</b>
                                                    </td>{/strip}
                                                </tr>
                                                <tr>
                                                    {foreach key=index item=custom_field from=$CUSTOM_FIELDS_DATA}
                                                        {assign var=keyid value=$custom_field.2}
                                                        {assign var=keyval value=$custom_field.1}
                                                        {assign var=keyfldname value=$custom_field.0}
                                                        {assign var=keyoptions value=$custom_field.options}
                                                        {if $keyid eq '9'}
                                                            <td class="cellLabel" align="right" width="20%"><b>{$keyfldname} {$APP.COVERED_PERCENTAGE}</b></td>
                                                        {else}
                                                            <td class="cellLabel" align="right" width="20%"><b>{$keyfldname}</b></td>
                                                        {/if}
                                                        {include file="DetailViewFields.tpl"}
                                                        {if ($index+1)% 2 == 0}
                                                            </tr>
                                                            <tr>
                                                        {/if}
                                                    {/foreach}
                                                    {if ($index+1)% 2 != 0}
                                                    <td width="20%"></td><td width="30%"></td>
                                                    {/if}
                                                </tr>
                                            </table>   
                                        {/if}
                 
                                        {* vtlib Customization: Embed DetailViewWidget block:// type if any *}
                                        {if $CUSTOM_LINKS && !empty($CUSTOM_LINKS.DETAILVIEWWIDGET)}
                                            <table border=0 cellspacing=0 cellpadding=5 width=100% >
                                                {foreach item=CUSTOM_LINK_DETAILVIEWWIDGET from=$CUSTOM_LINKS.DETAILVIEWWIDGET}
                                                    {if preg_match("/^block:\/\/.*/", $CUSTOM_LINK_DETAILVIEWWIDGET->linkurl)}
                                                        <tr>
                                                            <td style="padding:5px;" >
                                                            {php}
                                                            echo vtlib_process_widget($this->_tpl_vars['CUSTOM_LINK_DETAILVIEWWIDGET'], $this->_tpl_vars);
                                                            {/php}
                                                            </td>
                                                        </tr>
                                                    {/if}
                                                {/foreach}
                                            </table>
                                        {/if}
                                        {* END *}
    			    
                                        <br>
                                        <table border=0 cellspacing=0 cellpadding=0 width=100% align=center>
                                            <tr>
                                                <td>
                         				        	<table border=0 cellspacing=0 cellpadding=3 width=100%>
                                                        <tr>
                                        					<td class="dvtTabCache" style="width:10px" nowrap>&nbsp;</td>
    				                                        <td id="cellTabInvite" class="dvtSelectedCell" align=center nowrap><a href="javascript:doNothing()" onClick="switchClass('cellTabInvite','on');switchClass('cellTabAlarm','off');switchClass('cellTabRepeat','off');switchClass('cellTabRelatedto','off');ghide('addEventAlarmUI');dispLayer('addEventInviteUI');ghide('addEventRepeatUI');ghide('addEventRelatedtoUI');">{$CMOD.LBL_INVITE}</a></td>
                    										<td class="dvtTabCache" style="width:10px">&nbsp;</td>
                    										{if $LABEL.reminder_time neq ''}
                    										<td id="cellTabAlarm" class="dvtUnSelectedCell" align=center nowrap><a href="javascript:doNothing()" onClick="switchClass('cellTabInvite','off');switchClass('cellTabAlarm','on');switchClass('cellTabRepeat','off');switchClass('cellTabRelatedto','off');dispLayer('addEventAlarmUI');ghide('addEventInviteUI');ghide('addEventRepeatUI');ghide('addEventRelatedtoUI');">{$CMOD.LBL_REMINDER}</a></td>
                    										{/if}
                    										<td class="dvtTabCache" style="width:10px">&nbsp;</td>
                    										{if $LABEL.recurringtype neq ''}
                    										<td id="cellTabRepeat" class="dvtUnSelectedCell" align=center nowrap><a href="javascript:doNothing()" onClick="switchClass('cellTabInvite','off');switchClass('cellTabAlarm','off');switchClass('cellTabRepeat','on');switchClass('cellTabRelatedto','off');ghide('addEventAlarmUI');ghide('addEventInviteUI');dispLayer('addEventRepeatUI');ghide('addEventRelatedtoUI');">{$CMOD.LBL_REPEAT}</a></td>
                    										{/if}
                    										<td class="dvtTabCache" style="width:10px">&nbsp;</td>
                    										<td id="cellTabRelatedto" class="dvtUnSelectedCell" align=center nowrap><a href="javascript:doNothing()" onClick="switchClass('cellTabInvite','off');switchClass('cellTabAlarm','off');switchClass('cellTabRepeat','off');switchClass('cellTabRelatedto','on');ghide('addEventAlarmUI');ghide('addEventInviteUI');dispLayer('addEventRelatedtoUI');ghide('addEventRepeatUI');">{$CMOD.LBL_LIST_RELATED_TO}</a></td>
                    										<td class="dvtTabCache" style="width:100%">&nbsp;</td>
                    									</tr>
                    								</table>
                                                </td>
                                            </tr>
    				 
                                            <tr>
                                                <td width=100% valign=top align=left class="dvtContentSpace" style="padding:10px;height:120px">
                                                    <!-- Invite UI -->
                                                    <DIV id="addEventInviteUI" style="display:block;width:100%">
                                                        <table width="100%" cellpadding="5" cellspacing="0" border="0">
                                                            <tr>
                                                                <td width="30%" valign="top" align=right><b>{$CMOD.LBL_USERS}</b></td>
                                                                <td width="70%" align=left valign="top">
                                                                {foreach item=username key=userid from=$INVITEDUSERS}
                                                                    {$username}<br>
                                                                {/foreach}
                                                                </td>
                                                            </tr>
                                                        </table>
                                                    </DIV>
                                                    <!-- Reminder UI -->
                                                    <DIV id="addEventAlarmUI" style="display:none;width:100%">
                                                        {if $LABEL.reminder_time != ''}
                                                            <table width="100%" cellpadding="5" cellspacing="0" border="0">
                                                                <tr>
                                                                    <td width="30%" align=right><b>{$CMOD.LBL_SENDREMINDER}</b></td>
                                                                    <td width="70%" align=left>{$ACTIVITYDATA.set_reminder}</td>
                                                                </tr>
                                                                {if $ACTIVITYDATA.set_reminder != 'No'}
                                                                <tr>
                                                                    <td width="30%" align=right><b>{$CMOD.LBL_RMD_ON}</b></td>
                                                                    <td width="70%" align=left>{$ACTIVITYDATA.reminder_str}</td>
                                                                </tr>
                                                                {/if}
                                                            </table>
                                                        {/if}
                                                    </DIV>
                                                    <!-- Repeat UI -->
                                                    <div id="addEventRepeatUI" style="display:none;width:100%">
                                                        {if $LABEL.recurringtype neq ''}
                                                            <table width="100%" cellpadding="5" cellspacing="0" border="0">
                                                                <tr>
                                                                    <td width="30%" align=right><b>{$CMOD.LBL_ENABLE_REPEAT}</b></td>
                                                                    <td width="70%" align=left>{$ACTIVITYDATA.recurringcheck}</td>
                                                                </tr>
                                                                {if $ACTIVITYDATA.repeat_str neq ''}
                                                                <tr>
                                                                    <td width="30%" align=right>&nbsp;</td>
                                                                    <td>{$CMOD.LBL_REPEATEVENT}&nbsp;{$ACTIVITYDATA.repeat_frequency}&nbsp;{$MOD[$ACTIVITYDATA.recurringtype]}</td>
                                                                </tr>
                                                                <tr>
                                                                    <td width="30%" align=right>&nbsp;</td>
                                                                    <td>{$ACTIVITYDATA.repeat_str}</td>
                                                                </tr>
                                                                {/if}
                                                            </table>
                                                        {/if}
                                                    </div>
                                                    <!-- Relatedto UI -->
                                                    <div id="addEventRelatedtoUI" style="display:none;width:100%">
                                                        <table width="100%" cellpadding="5" cellspacing="0" border="0">
                                                            {if $LABEL.parent_id neq ''}
                                                            <tr>
                                                                <td width="30%" align=right valign="top"><b>{$LABEL.parent_id}</b></td>
                                                                <td width="70%" align=left valign="top">{$ACTIVITYDATA.parent_name}</td>
                                                            </tr>
                                                            {/if}
                                                            <tr>
                                                                <td width="30%" valign="top" align=right><b>{$CMOD.LBL_CONTACT_NAME}</b></td>	
                                                                <td width="70%" valign="top" align=left>
                                                                {foreach item=contactname key=cntid from=$CONTACTS}
                                                                    {$contactname}
                                                                    <br>
                                                                {/foreach}
                                                                </td>
                                                            </tr>
                                                        </table>
                                                    </div>
                                                </td>
                                            </tr>
                                        </table>
                                    {else}
                                        <!-- detailed view of a ToDo -->
                                        <table border="0" cellpadding="5" cellspacing="0" width="100%">
                                            <tr>
                                                <td class="cellLabel" width="20%" align="right"><b>{$CMOD.LBL_TODO}</b></td>
                                                <td class="cellInfo" width="80%" align="left">{$ACTIVITYDATA.subject}</td>
                                            </tr>
                                            {if $LABEL.description neq ''}
                                                <tr>
                                                    <td class="cellLabel" align="right" valign="top"><b>{$LABEL.description}</b></td>
                                                    <td class="cellInfo" align="left" colspan="3" valign="top" height="60px">{$ACTIVITYDATA.description}&nbsp;</td>
                                                </tr>
                                            {/if}
                                            <tr>
                                                <td colspan="2" align="center" style="padding:0px">
                                                    <table border="0" cellpadding="5" cellspacing="1" width="100%" >
                                                        <tr>
                                                        {if $LABEL.taskstatus neq ''}
                                                            <td class="cellLabel" width=33% align="left"><b>{$LABEL.taskstatus}</b></td>
                                                        {/if}
                                                        {if $LABEL.taskpriority neq ''}
                                                            <td class="cellLabel" width=33% align="left"><b>{$LABEL.taskpriority}</b></td>
                                                        {/if}
                                                            <td class="cellLabel" width=34% align="left"><b>{$LABEL.assigned_user_id}</b></td>
                                                        </tr>
                                                        <tr>
                                                            {if $LABEL.taskstatus neq ''}
                                                                <td class="cellInfo" align="left" valign="top">
                                                                    {if $ACTIVITYDATA.taskstatus eq $APP.LBL_NOT_ACCESSIBLE}
                                                                        <font color="red">{$ACTIVITYDATA.taskstatus}</font>
                                                                    {else}
                                                                        {$ACTIVITYDATA.taskstatus}
                                                                    {/if}
                                                                </td>
                                                            {/if}
                                                            {if $LABEL.taskpriority neq ''}		
                                                                <td class="cellInfo" align="left" valign="top">
                                                                    {if $ACTIVITYDATA.taskpriority eq $APP.LBL_NOT_ACCESSIBLE}
                                                                        <font color="red">{$ACTIVITYDATA.taskpriority}</font>
                                                                    {else}
                                                                        {$ACTIVITYDATA.taskpriority}
                                                                    {/if}
                                                                </td>
                                                            {/if}
                                                            <td class="cellInfo" align="left" valign="top">{$ACTIVITYDATA.assigned_user_id}</td>
                                                        </tr>
                                                    </table>
                                                </td>
                                            </tr>
                                        </table>
                                        <table border="0" cellpadding="0" cellspacing="0" width="100%" align=center>
                                            <tr>
                                                <td width=50% valign=top >
                                                    <table border=0 cellspacing=0 cellpadding=2 width=100% align=center>
                                                    <tr><td class="mailSubHeader" align=left ><b>{$CMOD.LBL_TIMEDATE}</b></td></tr>
                                                    <tr><td class="small" >{$ACTIVITYDATA.starthr}:{$ACTIVITYDATA.startmin}{$ACTIVITYDATA.startfmt}</td></tr>
                                                    <tr><td class="cellInfo" style="padding-left:0px">{$ACTIVITYDATA.date_start}</td></tr>
                                                    </table>
                                                </td>
                                                <td width=50% valign="top">
                                                    <table border=0 cellspacing=0 cellpadding=2 width=100% align=center>
                                                        <tr><td class="mailSubHeader"><b>{$LABEL.due_date}</b></td></tr>
                                                        <tr><td class="small">{$ACTIVITYDATA.due_date}</td></tr>
                                                        <tr><td class="cellInfo">&nbsp;</td></tr>
                                                    </table>
                                                </td>
                                            </tr>   
                                        </table>	
                                        <table border=0 cellspacing=0 cellpadding=5 width=100% >
                                            <tr>
    			                                <td class="cellLabel" align=right nowrap width=20%>{if $LABEL.createdtime neq ''}<b>{$LABEL.createdtime}</b>{/if}</td>
                                                <td class="cellInfo" align=left nowrap width=30%>{if $LABEL.createdtime neq ''}{$ACTIVITYDATA.createdtime}{/if}</td>
                                                <td class="cellLabel" align=right nowrap width=20%>{if $LABEL.modifiedtime neq ''}<b>{$LABEL.modifiedtime}</b>{/if}</td>
                                                <td class="cellInfo" align=left  nowrap width=30%>{if $LABEL.modifiedtime neq ''}{$ACTIVITYDATA.modifiedtime}{/if}</td>
                                            </tr>
                                        </table>
    
                                        {if $CUSTOM_FIELDS_DATA|@count > 0}
                                            <table border=0 cellspacing=0 cellpadding=5 width=100% >
                                            <tr>{strip}
                                            	<td colspan=4 class="tableHeading">
                                            	<b>{$APP.LBL_CUSTOM_INFORMATION}</b>
                                            	</td>{/strip}
                                            </tr>
                                            <tr>
                                            	{foreach key=index item=custom_field from=$CUSTOM_FIELDS_DATA}
                                            	{assign var=keyid value=$custom_field.2}
                                            	{assign var=keyval value=$custom_field.1}
                                            	{assign var=keyfldname value=$custom_field.0}
                                            	{assign var=keyoptions value=$custom_field.options}
                                            	{if $keyid eq '9'}
                                            		<td class="cellLabel" align="right" width="20%"><b>{$keyfldname} {$APP.COVERED_PERCENTAGE}</b></td>
                                            	{else}
                                            		<td class="cellLabel" align="right" width="20%"><b>{$keyfldname}</b></td>
                                            	{/if}
                                            	{include file="DetailViewFields.tpl"}
                                            		{if ($index+1)% 2 == 0}
                                            			</tr><tr>
                                            		{/if}
                                                {/foreach}
                                                {if ($index+1)% 2 != 0}
                                                	<td width="20%"></td><td width="30%"></td>
                                                {/if}
                                            </tr>
                                            </table>   
                                        {/if}  
                                        <br>
                                        {if $LABEL.sendnotification neq '' || ($LABEL.parent_id neq '') || ($LABEL.contact_id neq '') } 
                                            <table align="center" border="0" cellpadding="0" cellspacing="0" width="100%">
                                                <tr>
                                                    <td>
                                                    	<table border="0" cellpadding="3" cellspacing="0" width="100%">
                                                    	    <tr>
                                                    			<td class="dvtTabCache" style="width: 10px;" nowrap="nowrap">&nbsp;</td>
                                                    			{if $LABEL.sendnotification neq ''}
                                                                    {assign var='class_val' value='dvtUnSelectedCell'}
                                                                    <td id="cellTabInvite" class="dvtSelectedCell" align="center" nowrap="nowrap"><a href="javascript:doNothing()" onClick="switchClass('cellTabInvite','on');switchClass('cellTabRelatedto','off');dispLayer('addTaskAlarmUI');ghide('addTaskRelatedtoUI');">{$CMOD.LBL_NOTIFICATION}</td></a></td>
                                                    			{else}
                                                                    {assign var='class_val' value='dvtSelectedCell'}
                                                                {/if}
                                                    		 	<td class="dvtTabCache" style="width: 10px;" nowrap="nowrap">&nbsp;</td>
                                                    			{if ($LABEL.parent_id neq '') || ($LABEL.contact_id neq '') }
                                                                     <td id="cellTabRelatedto" class={$class_val} align=center nowrap><a href="javascript:doNothing()" onClick="switchClass('cellTabInvite','off');switchClass('cellTabRelatedto','on');dispLayer('addTaskRelatedtoUI');ghide('addTaskAlarmUI');">{$CMOD.LBL_RELATEDTO}</a></td>
                                                    			{/if}
                                                                <td class="dvtTabCache" style="width: 100%;">&nbsp;</td>
                                                    	    </tr>
                                                    	</table>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td width=100% valign=top align=left class="dvtContentSpace" style="padding:10px;height:120px">
                                                    <!-- Notification UI -->
                                                        <DIV id="addTaskAlarmUI" style="display:block;width:100%">
                                                    	{if $LABEL.sendnotification neq ''}
                                                    		{assign var='vision' value='none'}
                                                            <table width="100%" cellpadding="5" cellspacing="0" border="0">
                                                                    <tr>
                                                                            <td width="30%" align=right><b>{$CMOD.LBL_SENDNOTIFICATION}</b></td>
                                                                            <td width="70%" align=left>{$ACTIVITYDATA.sendnotification}</td>
                                                                    </tr>
                                                            </table>
                                                        {else}
                                                            {assign var='vision' value='block'}
                                                        {/if}
                                                        </DIV>
                                                        <div id="addTaskRelatedtoUI" style="display:{$vision};width:100%">
                                                            <table width="100%" cellpadding="5" cellspacing="0" border="0">
                                                                <tr>
                                                                {if $LABEL.parent_id neq ''}
                                                                    <td width="30%" align=right><b>{$LABEL.parent_id}</b></td>
                                                                    <td width="70%" align=left>{$ACTIVITYDATA.parent_name}</td>
                                                                {/if}
                                                                </tr>
                                                                <tr>
                                                                {if $LABEL.contact_id neq ''}
                                                                    <td width="30%" align=right><b>{$CMOD.LBL_CONTACT_NAME}</b></td>
                                                                    <td width="70%" align=left><a href="{$ACTIVITYDATA.contact_idlink}">{$ACTIVITYDATA.contact_id}</a></td>
                                                                {/if}
                                                                </tr>
                                                            </table>
                                                        </div>
                                                    </td>
                                                </tr>
                                            </table>
                                        {/if}
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