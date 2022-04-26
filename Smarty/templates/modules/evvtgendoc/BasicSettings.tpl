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
<script type="text/javascript" src="modules/evvtgendoc/evvtgendoc.js"></script>
{assign var='MODULESECTIONDESC' value=$MOD.LBL_CONFIGURATION_DESCRIPTION}
{assign var='MODULESECTION' value=''}
{include file='SetMenu.tpl'}
<section role="dialog" tabindex="-1" class="slds-fade-in-open slds-modal_large slds-app-launcher">
<div class="slds-modal__container slds-p-around_none">
<table align="center" border="0" cellpadding="0" cellspacing="0" width="98%">
<tr>
	<td class="showPanelBg" style="padding: 10px;" valign="top" width="100%">
	<div align="center">
		<table border=0 cellspacing=1 cellpadding=0 width="100%" class="lvtBg">
		<tr>
			<td>
				<table border=0 cellspacing=0 cellpadding=2 width="100%" class="small">
				<tr>
					<td style="padding-right:20px" nowrap align="right"></td>
				</tr>
				</table>

				<table border=0 cellspacing=0 cellpadding=0 width="95%" class="small">

				<tr><td>
					<table border=0 cellspacing=0 cellpadding=10 width="100%" class="dvtContentSpace" style='border-bottom: 0'>
					<tr>
						<td>
							<div id='gendocContents'>
							{include file='modules/evvtgendoc/BasicSettingsContents.tpl'}
							</div>
						</td>
					</tr>
					</table>
				</td></tr>
				</table>
			</td>
		</tr>
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
</table>
</div>
</section>