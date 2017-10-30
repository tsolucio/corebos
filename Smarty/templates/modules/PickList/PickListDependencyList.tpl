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
<div id="pickListDependencyList">
	<table class="slds-table slds-no-row-hover tableHeading" style="background-color: #fff;">
		<tr class="blockStyleCss">
			<td class="detailViewContainer" valign="top">
				<div class="forceRelatedListSingleContainer">
					<article class="slds-card forceRelatedListCardDesktop" aria-describedby="header">
						<div class="slds-card__header slds-grid">
							<header class="slds-media slds-media--center slds-has-flexi-truncate">
								<div class="slds-media__body">
									<h2>
										<span class="slds-text-title--caps slds-truncate">
											<strong>{$MOD.LBL_SELECT_MODULE}</strong>
											&nbsp;
											<select name="pickmodule" id="pickmodule" class="slds-select" style="width: 25%" onChange="changeDependencyPicklistModule();">
												<option value="">{$APP.LBL_ALL}</option>
												{foreach key=modulelabel item=module from=$MODULE_LISTS}
													<option value="{$module}" {if $MODULE eq $module} selected {/if}>
														{$modulelabel|@getTranslatedString:$module}
													</option>
												{/foreach}
											</select>
										</span>
									</h2>
								</div>
							</header>
							<div class="slds-no-flex">
								<input title="{$MOD_PICKLIST.LBL_NEW_DEPENDENCY}" class="slds-button slds-button--small slds-button_success" type="button" name="New" value="{$MOD_PICKLIST.LBL_NEW_DEPENDENCY}" onclick="addNewDependencyPicklist();"/>
							</div>
						</div>
					</article>
				</div>
				<div class="slds-truncate">
					<table class="slds-table slds-table--bordered listTable">
						<thead>
							<tr>
								<th class="slds-text-title--caps" scope="col"><span class="slds-truncate">#</span></th>
								<th class="slds-text-title--caps" scope="col"><span class="slds-truncate">{$APP.LBL_MODULE}</span></th>
								<th class="slds-text-title--caps" scope="col"><span class="slds-truncate">{$MOD_PICKLIST.LBL_SOURCE_FIELD}</span></th>
								<th class="slds-text-title--caps" scope="col"><span class="slds-truncate">{$MOD_PICKLIST.LBL_TARGET_FIELD}</span></th>
								<th class="slds-text-title--caps" scope="col"><span class="slds-truncate">{$MOD_PICKLIST.LBL_TOOLS}</span></th>
							</tr>
						</thead>
						<tbody>
							{foreach name=dependencylist item=dependencyvalues from=$DEPENDENT_PICKLISTS}
							{assign var="FIELD_MODULE" value=$dependencyvalues.module}
							<tr class="slds-hint-parent slds-line-height--reset">
								<th scope="row"><div class="slds-truncate">{$smarty.foreach.dependencylist.iteration}</div></th>
								<th scope="row"><div class="slds-truncate">{$FIELD_MODULE|@getTranslatedString:$FIELD_MODULE}</div></th>
								<th scope="row"><div class="slds-truncate">{$dependencyvalues.sourcefieldlabel|@getTranslatedString:$FIELD_MODULE}</div></th>
								<th scope="row"><div class="slds-truncate">{$dependencyvalues.targetfieldlabel|@getTranslatedString:$FIELD_MODULE}</div></th>
								<th scope="row">
									<div class="slds-truncate">
										<a href="javascript:void(0);" onclick="editDependencyPicklist('{$FIELD_MODULE}','{$dependencyvalues.sourcefield}','{$dependencyvalues.targetfield}');">
											<img src="{'editfield.gif'|@vtiger_imageurl:$THEME}" alt="{$APP.LBL_EDIT}" title="{$APP.LBL_EDIT}" border="0" align="absmiddle">
										</a>
										&nbsp;|&nbsp;
										<a href="javascript:void(0);" onClick="deleteDependencyPicklist('{$FIELD_MODULE}','{$dependencyvalues.sourcefield}','{$dependencyvalues.targetfield}','{'NTC_DELETE_CONFIRMATION'|@getTranslatedString}');">
											<img src="{'delete.gif'|@vtiger_imageurl:$THEME}" alt="{$APP.LBL_DELETE}" title="{$APP.LBL_DELETE}" border="0" align="absmiddle">
										</a>
									</div>
								</th>
							</tr>
							{/foreach}
						</tbody>
					</table>
				</div>
			</td>
		</tr>
	</table>
</div>