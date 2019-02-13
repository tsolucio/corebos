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

<form id="UnifiedSearch_moduleform" name="UnifiedSearch_moduleform" method="post" >
	<ul class="slds-listbox slds-listbox_vertical" role="group" aria-label="Suggested for you">
		<li role="presentation" class="slds-listbox__item">
			<div id="object0" class="slds-media slds-listbox__option slds-listbox__option_plain slds-media_small" role="presentation">
				<h3 class="slds-listbox__option-header" role="presentation"><strong>Suggested for you</strong></h3>
			</div>
		</li>
		{foreach item=SEARCH_MODULEINFO key=SEARCH_MODULENAME from=$ALLOWED_MODULES name=allowed_modulesloop}								 
			<li role="presentation" class="slds-listbox__item">
				<div id="option{$smarty.foreach.allowed_modulesloop.iteration}" class="slds-media slds-listbox__option slds-listbox__option_plain slds-media_small slds-is-selected" role="option">
					<span class="slds-media__figure slds-listbox__option-icon">
						<span class="slds-icon_container slds-icon-utility-check slds-current-color">
							<svg class="slds-icon slds-icon_x-small" aria-hidden="true">
							<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#check"></use>
							</svg>
						</span>
					</span>
					<span class="slds-media__body">
						<span class="slds-truncate" title="Accounts">
						<span class="slds-assistive-text">Current Selection:</span> 
							{$SEARCH_MODULEINFO.label}
						</span>
					</span>
				</div>
			</li>													
		{/foreach}
	</ul>
</form>
