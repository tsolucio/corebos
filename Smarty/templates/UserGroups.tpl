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
<table class="slds-table slds-table--bordered slds-table--fixed-layout ld-font">
	<thead>
		<tr>
			<th class="slds-text-title--caps" scope="col">#</th>
			<th class="slds-text-title--caps" scope="col" style="padding: .5rem">{$UMOD.LBL_GROUP_NAME}</th>
			<th class="slds-text-title--caps" scope="col">{$UMOD.LBL_DESCRIPTION}</th>
		</tr>
	</thead>
	<tbody>
		{foreach name=groupiter key=id item=groupname from=$GROUPLIST}
			<tr class="slds-hint-parent slds-line-height--reset">
				<td class="slds-text-align--left">{$smarty.foreach.groupiter.iteration}</td>
				{if $IS_ADMIN}
					<td class="slds-text-align--left">
						<a href="index.php?module=Settings&action=GroupDetailView&parenttab=Settings&groupId={$id}">
							{$groupname.1}
						</a>
					</td>
				{else}
					<td class="slds-text-align--left">{$groupname.1}</td>
				{/if}
				<td class="slds-text-align--left">{$groupname.2}</td>
			</tr>
		{/foreach}
	</tbody>
</table>
