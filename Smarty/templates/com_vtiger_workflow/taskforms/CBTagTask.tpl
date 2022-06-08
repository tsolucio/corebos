{*<!--
/*************************************************************************************************
 * Copyright 2014 JPL TSolucio, S.L. -- This file is a part of TSOLUCIO coreBOS Customizations.
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
<script type="text/javascript" charset="utf-8">
var moduleName = '{$entityName}';
</script>

<div class="slds-grid slds-p-horizontal_x-large">
	<div class="slds-col slds-p-around_x-small">
		<h2 class="slds-text-title_bold">{'LBL_ACTION'|@getTranslatedString:'com_vtiger_workflow'}</h2>
	</div>
</div>

<div class="slds-grid slds-p-horizontal_x-large">
	<div class="slds-col slds-p-around_x-small">
		<fieldset class="slds-form-element">
				<div class="slds-form-element__control">
					<span class="slds-radio">
						<input type="radio" id="eq1" name="addrel" value="1" {if isset($task->addrel) && $task->addrel eq 1}checked{/if} />
							<label class="slds-radio__label" for="eq1">
								<span class="slds-radio_faux"></span>
									<span class="slds-form-element__label"> {'Add Tag'|@getTranslatedString:'com_vtiger_workflow'} </span>
							</label>
					</span>
					<span class="slds-radio">
						<input type="radio" id="eq0" name="addrel" value="0" {if isset($task->addrel) && $task->addrel eq 0}checked{/if} />
							<label class="slds-radio__label" for="eq0">
								<span class="slds-radio_faux"></span>
									<span class="slds-form-element__label"> {'Delete Tag'|@getTranslatedString:'com_vtiger_workflow'} </span>
							</label>
					</span>
				</div>
		</fieldset>
	</div>
</div>

<div class="slds-grid slds-p-horizontal_x-large">
	<div class="slds-col slds-p-around_x-small">
		<fieldset class="slds-form-element slds-form-element_compound">
			<div class="slds-form-element__control">
				<div class="slds-form__row">
					<div class="slds-size_2-of-12">
						<div class="slds-form__item" role="listitem">
							<span class="slds-radio">
								<input type="radio" id="eq_1" name="forallusers" value="1" {if isset($task->forallusers) && $task->forallusers eq 1}checked{/if} />
									<label class="slds-radio__label" for="eq_1">
										<span class="slds-radio_faux"></span>
											<span class="slds-form-element__label"> {'ForAllUsers'|@getTranslatedString:'com_vtiger_workflow'} </span>
									</label>
							</span>
						</div>
					</div>
					<div class="slds-size_1-of-12">
						<h2 class="slds-page-header__meta-text"> {'LBL_OR'|@getTranslatedString} </h2>
					</div>
					<div class="slds-size_2-of-12">
						<div class="slds-form__item" role="listitem">
							<span class="slds-radio">
								<input type="radio" id="eq_0" name="forallusers" value="0" {if isset($task->forallusers) && $task->forallusers eq 0}checked{/if} />
									<label class="slds-radio__label" for="eq_0">
										<span class="slds-radio_faux"></span>
											<span class="slds-form-element__label"> {'ForCurrentUser'|@getTranslatedString:'com_vtiger_workflow'} </span>
									</label>
							</span>
						</div>
					</div>
				</div>
			</div>
		</fieldset>
	</div>
</div>

<div class="slds-grid slds-p-horizontal_x-large">
	<div class="slds-col slds-p-around_x-small">
		<h2 class="slds-text-title_bold">{'Tags'|@getTranslatedString:'com_vtiger_workflow'}</h2>
	</div>
</div>

<div class="slds-grid slds-p-horizontal_x-large">
	<div class="slds-col slds-size_6-of-12 slds-p-around_x-small">
		<div class="slds-form-element">
			<div class="slds-form-element__control">
				<input type="text" id="listoftags" name="listoftags" class="slds-input" value="{if isset($task->listoftags)}{$task->listoftags}{/if}"/>
			</div>
		</div>
	</div>
</div>
