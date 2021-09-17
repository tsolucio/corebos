{*<!--
/*********************************************************************************
  ** The contents of this file are subject to the vtiger CRM Public License Version 1.0
   * ("License"); You may not use this file except in compliance with the License
   * The Original Code is:  coreBOS Open Source
   * The Initial Developer of the Original Code is coreBOS.
   * Portions created by coreBOS are Copyright (C) coreBOS.
   * All Rights Reserved.
 ********************************************************************************/
-->*}

{extends file='Components/TooltipInfo.tpl'}
{block name=TOOLTIPInfo}
<div class="slds-tile__detail slds-p-around_x-small">
	<dl class="slds-list_horizontal slds-wrap">
	{foreach from=$Tile.morefields item=finfo key=fkey}
		<dt class="slds-item_label slds-text-color_weak slds-truncate">{$finfo.label}:</dt>
		<dd class="slds-item_detail slds-truncate">{$finfo.value}</dd>
	{/foreach}
	</dl>
</div>
{/block}
