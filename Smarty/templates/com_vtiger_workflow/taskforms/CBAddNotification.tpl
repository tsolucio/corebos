{*<!--
/*************************************************************************************************
 * Copyright 2022 Spike. -- This file is a part of Spike coreBOS Customizations.
 * Licensed under the vtiger CRM Public License Version 1.1 (the "License"); you may not use this
 * file except in compliance with the License. You can redistribute it and/or modify it
 * under the terms of the License. Spike. reserves all rights not expressly
 * granted by the License. coreBOS distributed by Spike. is distributed in
 * the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied
 * warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. Unless required by
 * applicable law or agreed to in writing, software distributed under the License is
 * distributed on an "AS IS" BASIS, WITHOUT ANY WARRANTIES OR CONDITIONS OF ANY KIND,
 * either express or implied. See the License for the specific language governing
 * permissions and limitations under the License. You may obtain a copy of the License
 * at <http://corebos.org/documentation/doku.php?id=en:devel:vpl11>
 *************************************************************************************************
 * Author : Spike
 *************************************************************************************************//
-->*}

<script src="modules/com_vtiger_workflow/resources/vtigerwebservices.js" type="text/javascript" charset="utf-8"></script>
<script src="modules/com_vtiger_workflow/resources/addnotification.js" type="text/javascript" charset="utf-8"></script>
<script type="text/javascript" charset="utf-8">
var moduleName = '{if empty($task->cbmodule)}{$entityName}{else}{$task->cbmodule}{/if}';
var wfUser = '{if empty($task->ownerid)}wfuser{else}{$task->ownerid}{/if}';
</script>

<div class="slds-grid slds-gutters slds-p-around_large">
<div class="slds-col slds-size_1-of-2">
	<div class="slds-form-element">
		<label class="slds-form-element__label" for="cbmodule">
			{'LBL_MODULE'|@getTranslatedString:'com_vtiger_workflow'}
		</label>
		<div class="slds-form-element__control">
			<select class="slds-select" name="cbmodule" id="cbmodule">
			</select>
		</div>
	</div>
	<div class="slds-form-element">
		<label class="slds-form-element__label" for="cbrecord">
			{'record'|@getTranslatedString:'com_vtiger_workflow'}
		</label>
		<div class="slds-form-element__control">
			<input type="text" name="cbrecord" id="cbrecord" class="slds-input" value="{if isset($task->cbrecord)}{$task->cbrecord}{/if}"/>
		</div>
	</div>
	<div class="slds-form-element">
		<label class="slds-form-element__label" for="relwith">
			{'LBL_RELATED_TO'|@getTranslatedString:'com_vtiger_workflow'}
		</label>
		<div class="slds-form-element__control">
			<input type="text" name="relwith" id="relwith" class="slds-input" value="{if isset($task->relwith)}{$task->relwith}{/if}" />
		</div>
	</div>
	<div class="slds-form-element">
		<label class="slds-form-element__label" for="moreaction">
			{'LBL_ACTION'|@getTranslatedString:'com_vtiger_workflow'}
		</label>
		<div class="slds-form-element__control">
			<textarea name="moreaction" id="moreaction" class="slds-textarea">{if isset($task->moreaction)}{$task->moreaction}{/if}</textarea>
		</div>
	</div>
</div>
<div class="slds-col slds-size_1-of-2">
	<div class="slds-form-element">
		<label class="slds-form-element__label" for="cbdate">
			{'LBL_ACTION_DATE'|@getTranslatedString:'com_vtiger_workflow'}
		</label>
		<div class="slds-form-element__control">
			<input type="text" name="cbdate" id="cbdate" class="slds-input" value="{if isset($task->cbdate)}{$task->cbdate}{/if}" />
		</div>
	</div>
	<div class="slds-form-element">
		<label class="slds-form-element__label" for="cbtime">
			{'LBL_TIME'|@getTranslatedString:'com_vtiger_workflow'}
		</label>
		<div class="slds-form-element__control">
			<input type="text" name="cbtime" id="cbtime" class="slds-input" value="{if isset($task->cbtime)}{$task->cbtime}{/if}" />
		</div>
	</div>
	<div class="slds-form-element">
		<label class="slds-form-element__label" for="ownerid">
			{'Notify Owner'|@getTranslatedString:'com_vtiger_workflow'}
		</label>
		<div class="slds-form-element__control">
			<select class="slds-select" name="ownerid" id="ownerid">
			</select>
		</div>
	</div>
	<div class="slds-form-element">
		<label class="slds-form-element__label" for="moreinfo">
			{'LBL_USER_MORE_INFN'|@getTranslatedString:'Users'}
		</label>
		<div class="slds-form-element__control">
			<textarea name="moreinfo" id="moreinfo" class="slds-textarea">{if isset($task->moreinfo)}{$task->moreinfo}{/if}</textarea>
		</div>
	</div>
</div>
</div>