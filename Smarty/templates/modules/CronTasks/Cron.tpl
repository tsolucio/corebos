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
<script type="text/javascript" src="include/js/smoothscroll.js"></script>
<script type="text/javascript" src="modules/CronTasks/CronTasks.js"></script><br>
<table align="center" border="0" cellpadding="0" cellspacing="0" width="98%">
<tbody>
	<tr>
		<td class="showPanelBg" style="padding: 10px;" valign="top" width="100%">
		<div align=center>
			<table border=0 cellspacing=0 cellpadding=0 width=100%>
			<tr>
			<td class="small " valign=top align=left>
			<div class="slds-page-header__row">
				<div class="slds-p-right_medium">
					<div class="slds-media">
						<div class="slds-media__figure">
							<a class="hdrLink" href="index.php?action=index&module=CronTasks">
							<span class="slds-icon_container" title="{$MOD.LBL_SCHEDULER}">
							<img width="48" height="48" border="0" src="{'Cron.png'|@vtiger_imageurl:$THEME}"/>
							<span class="slds-assistive-text">{$MOD.LBL_SCHEDULER}</span>
							</span>
							</a>
						</div>
						<div class="slds-media__body">
							<div class="slds-page-header__name">
								<div class="slds-page-header__name-title">
									<span class="slds-page-header__title slds-truncate" title="{$MOD.LBL_SCHEDULER}">
										<span class="slds-page-header__title slds-truncate" title="{$MOD.LBL_SCHEDULER}">
											<b>
											{if $ISADMIN}
											<a href="index.php?module=Settings&action=index&parenttab=Settings">
											{/if}
											{'Settings'|@getTranslatedString:'Settings'}
											{if $ISADMIN}
											</a>
											{/if}
											&nbsp;>&nbsp;
											<a href="index.php?module=CronTasks&action=index">{$MOD.LBL_SCHEDULER}</a>
											</b>
										</span>
									</span>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<br>
			<div id="notifycontents">
			{include file='modules/CronTasks/CronContents.tpl'}
			</div>

			<table border=0 cellspacing=0 cellpadding=5 width=100% >
				<tr><td class="small cblds-t-align_right" nowrap align=right><a href="#top">{$APP.LBL_SCROLL}</a></td></tr>
			</table>
		</td>
	</tr>
</table>
	</td>
	</tr>
	</table>
	</div>

</td>
	</tr>
</tbody>
</table>
<div id="editdiv" style="display:none;position:absolute;width:450px;"></div>
