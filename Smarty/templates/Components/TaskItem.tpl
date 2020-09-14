{*<!--
/*********************************************************************************
  ** The contents of this file are subject to the vtiger CRM Public License Version 1.0
   * ("License"); You may not use this file except in compliance with the License
   * The Original Code is:  vtiger CRM Open Source
   * The Initial Developer of the Original Code is vtiger.
   * Portions created by vtiger are Copyright (C) vtiger.
   * All Rights Reserved.
 ********************************************************************************/
-->

TASKItemID id of the task
TASKItemRead if already read or not
TASKTitle
TASKSubtitle
TASKStatus
TASKType
TASKActions
*}

<li id="{$TASKItemID}" class="slds-global-header__notification {if !$TASKItemRead}slds-global-header__notification_unread{/if}">
<div class="slds-media slds-has-flexi-truncate slds-p-around_xx-small">
	<div class="slds-media__figure">
		<span class="slds-icon_container slds-icon_container_circle slds-icon-action-description" title="{$TASKType}">
			<svg class="slds-icon slds-icon_xx-small" aria-hidden="true">
				<use xlink:href="include/LD/assets/icons/{$TASKImage[0]}-sprite/svg/symbols.svg#{$TASKImage[1]|strtolower}"></use>
			</svg>
			<span class="slds-assistive-text">{$TASKType}</span>
		</span>
	</div>
	<div class="slds-media__body">
	<div class="slds-grid slds-grid_align-spread">
		<a href="javascript:void(0);" class="slds-text-link_reset slds-has-flexi-truncate">
		<h3 class="slds-truncate" title="{$TASKTitle}">
			<strong>{$TASKTitle}</strong>
		</h3>
		<p class="slds-truncate" title="{$TASKSubtitle}">{if $TASKSubtitleColor==''}{$TASKSubtitle}{else}<span style="color:{$TASKSubtitleColor}">{$TASKSubtitle}</span>{/if}</p>
		<p class="slds-m-top_x-small slds-text-color_weak">{$TASKStatus}
			{if !$TASKItemRead}<abbr class="slds-text-link slds-m-horizontal_xxx-small" title="unread">●</abbr>{/if}
		</p>
		</a>
	</div>
	{if count($TASKActions)>0}
	<div class="slds-media__footer">
	<div class="slds-grid slds-grid_align-spread">
	{foreach from=$TASKActions item=taction key=tlabel}
		<a href="{if $taction.type=='link'}{$taction.action}{else}javascript:void(0);{/if}" class="slds-text-link_reset slds-has-flexi-truncate" {if $taction.type=='click'}onclick="{$taction.action}"{/if}>
		<p class="slds-m-top_x-small slds-text-color_weak">{$tlabel}</p>
		</a>
	{/foreach}
	</div>
	</div>
	{/if}
</div>
</li>
