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

<div id="calc" style="z-index:10000002; border: 1px solid #ddd;">
	<table class="slds-table slds-no-row-hover" width="100%">
		<tr class="slds-text-title--header" style="cursor:move;">
			<th scope="col" id="calc_Handle">
				<div class="slds-truncate moduleName">
					<b>{$APP.LBL_CALCULATOR}</b>
				</div>
			</th>
			<th scope="col" style="padding: .5rem 0 .5rem 2.5rem;">
				<div class="slds-truncate">
					<a href="javascript:;">
						<img src="{'close.gif'|@vtiger_imageurl:$THEME}" border="0"  onClick="fninvsh('calc')" hspace="5" align="absmiddle">
					</a>
				</div>
			</th>
		</tr>
	</table>
	<table width="100%" class="layerPopup">
		<tr class="slds-line-height--reset">
			<td style="padding:10px;" colspan="2">{$CALC}</td>
		</tr>
	</table>
</div>

<script>jQuery("#calc").draggable({ldelim} handle: "#calc_Handle" {rdelim});</script>
