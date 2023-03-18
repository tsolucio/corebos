{*<!--
/*************************************************************************************************
 * Copyright 2022 JPL TSolucio, S.L. -- This file is a part of TSOLUCIO coreBOS Customizations.
 * Licensed under the vtiger CRM Public License Version 1.1 (the "License"); you may not use this
 * file except in compliance with the License. You can redistribute it and/or modify it
 * under the terms of the License. JPL TSolucio, S.L. reserves all rights not expressly
 * granted by the License. coreBOS distributed by JPL TSolucio S.L. is distributed in
 * the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied
 * warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. Unless required by
 * applicable law or agreed to in writing, software distributed under the License is
 * distributed on an "AS IS" BASIS, WITHOUT ANY WARRANTIES OR CONDITIONS OF ANY KIND,
 * either express or implied. See the License for the specific language governing
 * permissions and limitations under the License. You may obtain a copy of the License
 * at <http://corebos.org/documentation/doku.php?id=en:devel:vpl11>
 *************************************************************************************************
 *  Author       : JPL TSolucio, S. L.
 *************************************************************************************************//
-->*}

<script src="modules/com_vtiger_workflow/resources/vtigerwebservices.js" type="text/javascript" charset="utf-8"></script>
<script type="text/javascript" charset="utf-8">var moduleName = '{$entityName}';</script>

<div class="slds-grid slds-p-horizontal_x-large">
	<div class="slds-col slds-p-around_x-small">
		<h2 class="slds-text-title_bold">{'Image Field'|@getTranslatedString:'com_vtiger_workflow'}</h2>
	</div>
</div>

<div class="slds-grid slds-p-horizontal_x-large">
	<div class="slds-col slds-size_6-of-12 slds-p-around_x-small">
		<div class="slds-form-element slds-m-top_small">
			<label class="slds-form-element__label" for="image_field">{'Which field from Users should be used as a signature?'|@getTranslatedString:$MODULE_NAME}</label>
			<div class="slds-form-element__control">
				<input type="text" id="image_field" name="image_field" class="slds-input" value="{if isset($task->image_field)}{$task->image_field}{/if}"/>
			</div>
		</div>
	</div>
</div>

<div class="slds-grid slds-p-horizontal_x-large">
	<div class="slds-col slds-p-around_x-small">
		<h2 class="slds-text-title_bold">{'Coordinates'|@getTranslatedString:'com_vtiger_workflow'}</h2>
	</div>
</div>

<div class="slds-grid slds-p-horizontal_x-large">
	<div class="slds-col slds-size_6-of-12 slds-p-around_x-small">
		<div class="slds-form-element">
			<div class="slds-form-element__control slds-m-top_medium">
				<div class="slds-checkbox">
					<input type="checkbox" name="usecontextcoordinates" id="usecontextcoordinates" {if $task->usecontextcoordinates}checked{/if}/>
					<label class="slds-checkbox__label" for="usecontextcoordinates">
						<span class="slds-checkbox_faux"></span>
						<span class="slds-form-element__label">{$MOD.usecontextcoordinates}</span>
					</label>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="slds-grid slds-p-horizontal_x-large">
	<div class="slds-col slds-size_6-of-12 slds-p-around_x-small">
		<label class="slds-form-element__label" for="coordX">{'Coordinate X'|@getTranslatedString}</label>
		<div class="slds-form-element">
			<div class="slds-form-element__control">
				<input type="text" id="coordX" name="coordX" class="slds-input" value="{if isset($task->coordX)}{$task->coordX}{/if}"/>
			</div>
		</div>
	</div>
	<div class="slds-col slds-size_6-of-12 slds-p-around_x-small">
		<label class="slds-form-element__label" for="coordY">{'Coordinate Y'|@getTranslatedString}</label>
		<div class="slds-form-element">
			<div class="slds-form-element__control">
				<input type="text" id="coordY" name="coordY" class="slds-input" value="{if isset($task->coordY)}{$task->coordY}{/if}"/>
			</div>
		</div>
	</div>
</div>
