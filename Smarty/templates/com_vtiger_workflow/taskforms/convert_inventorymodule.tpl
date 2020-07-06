{*<!--
/*************************************************************************************************
 * Copyright 2016 MajorLabel -- This file is a part of MajorLabel coreBOS Customizations.
 * Licensed under the vtiger CRM Public License Version 1.1 (the "License"); you may not use this
 * file except in compliance with the License. You can redistribute it and/or modify it
 * under the terms of the License. MajorLabel reserves all rights not expressly
 * granted by the License. coreBOS distributed by MajorLabel is distributed in
 * the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied
 * warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. Unless required by
 * applicable law or agreed to in writing, software distributed under the License is
 * distributed on an "AS IS" BASIS, WITHOUT ANY WARRANTIES OR CONDITIONS OF ANY KIND,
 * either express or implied. See the License for the specific language governing
 * permissions and limitations under the License. You may obtain a copy of the License
 * at <http://corebos.org/documentation/doku.php?id=en:devel:vpl11>
 *************************************************************************************************
 *  Author       : MajorLabel
 *************************************************************************************************/
 -->*}
<script src="modules/com_vtiger_workflow/resources/vtigerwebservices.js" type="text/javascript" charset="utf-8"></script>
<script type="text/javascript" charset="utf-8">
var moduleName = '{$entityName}';
</script>

<div class="slds-grid slds-p-horizontal_x-large">
	<div class="slds-col slds-size_3-of-12 slds-p-around_x-small">
		<div class="slds-form">
			<div class="slds-form-element">
					<div class="slds-form-element__control">
						<div class="slds-select_container">
							<select class="slds-select slds-page-header__meta-text" name="convertto">
								<option value='Quotes'{if isset($task->convertto) && $task->convertto eq 'Quotes'} selected{/if}{if $entityName eq 'Quotes'} disabled{/if}>{'Quotes'|@getTranslatedString:'Quotes'}</option>
								<option value='SalesOrder'{if isset($task->convertto) && $task->convertto eq 'SalesOrder'} selected{/if}{if $entityName eq 'SalesOrder'} disabled{/if}>{'SalesOrder'|@getTranslatedString:'SalesOrder'}</option>
								<option value='Invoice'{if isset($task->convertto) && $task->convertto eq 'Invoice'} selected{/if}{if $entityName eq 'Invoice'} disabled{/if}>{'Invoice'|@getTranslatedString:'Invoice'}</option>
								<option value='PurchaseOrder'{if isset($task->convertto) && $task->convertto eq 'PurchaseOrder'} selected{/if}{if $entityName eq 'PurchaseOrder'} disabled{/if}>{'PurchaseOrder'|@getTranslatedString:'PurchaseOrder'}</option>
							</select>
						</div>
					</div>
			</div>
		</div>
	</div>
</div>

<div class="slds-grid slds-p-horizontal_x-large">
	<div class="slds-col slds-size_1-of-1 slds-p-around_x-small">
		<div class="slds-notify slds-notify_alert slds-theme_alert-texture slds-theme_warning" role="alert">
			<span>{'ConvertInventoryModuleMessage'|@getTranslatedString:'com_vtiger_workflow'}</span>
		</div>
	</div>
</div>
