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
{include file='SetMenu.tpl'}
<section role="dialog" tabindex="-1" class="slds-fade-in-open slds-modal_large slds-app-launcher" aria-labelledby="header43" aria-modal="true">
<div class="slds-modal__container slds-p-around_none">
<table style="border:0;padding: 2px 10px;">
<tbody><tr>
	<td class="showPanelBg" style="padding: 10px;" valign="top" width="100%">
	<br>
	<table border=0 cellspacing=0 cellpadding=20 width=90% class="settingsUI">
	<tr>
		<td>
		<table border=0 cellspacing=0 cellpadding=0 width=100%>
			{foreach key=BLOCKID item=BLOCKLABEL from=$BLOCKS}
				{if $BLOCKLABEL neq 'LBL_MODULE_MANAGER'}
				<tr>
					<td class="settingsTabHeader">
						{$MOD.$BLOCKLABEL}
					</td>
				</tr>
				<tr>
				<td class="settingsIconDisplay small">
					<table border=0 cellspacing=0 cellpadding=10 width=100%>
						<tr>
						{foreach item=data from=$FIELDS.$BLOCKID name=itr}
							<td width=25% valign=top>
							{if $data.name eq ''}
								&nbsp;
							{else}
							<table border=0 cellspacing=0 cellpadding=5 width=100%>
								<tr>
									{assign var=label value=$data.name|@getTranslatedString:$data.module}
									{if $data.name eq $label}
									{assign var=label value=$data.name|@getTranslatedString:'Settings'}
									{/if}
									{assign var=count value=$smarty.foreach.itr.iteration}
									<td width="74px" rowspan=2 valign=top>
										<a href="{$data.link}">
											<img src="{$data.icon|@vtiger_imageurl:$THEME}" alt="{$label}" width="48" height="48" border=0 title="{$label}">
										</a>
									</td>
									<td class=big valign=top>
										<a href="{$data.link}">
											{$label}
										</a>
									</td>
								</tr>
								<tr>
									{assign var=description value=$data.description|@getTranslatedString:$data.module}
									{if $data.description eq $description}
									{assign var=description value=$data.description|@getTranslatedString:'Settings'}
									{/if}
									<td class="small" valign=top>
										{$description}
									</td>
								</tr>
							</table>
							{/if}
							</td>
						{if $count mod $NUMBER_OF_COLUMNS eq 0}
							</tr><tr>
						{/if}
				{/foreach}
						</table>
					</td>
					</tr>
				{/if}
			{/foreach}
		</table>
		</td>
	</tr>
	</table>
	</td>
</tr>
</table>

	</div>
</section>
