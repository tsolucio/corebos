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
<script type="text/javascript">var moduleName = '{$entityName}';</script>
<script src="modules/com_vtiger_workflow/resources/vtigerwebservices.js" type="text/javascript" charset="utf-8"></script>
<script src="modules/com_vtiger_workflow/resources/many2mayrelationscript.js" type="text/javascript" charset="utf-8"></script>
<script type="text/javascript" src="include/js/vtlib.js"></script>
<script type="text/javascript" charset="utf-8">
var relrecords = {$task->relrecords|json_encode};
</script>
<br/>
<fieldset class="slds-form-element">
  <div class="slds-form-element__control">
    <span class="slds-radio">
      <input type="radio" id="radio-3" name="relAction" {if ($task->selAct eq "addrel")}{"checked"}{/if} value="addrel" />
      <label class="slds-radio__label" for="radio-3">
        <span class="slds-radio_faux"></span>
        <span class="slds-form-element__label">{'Add Relation'|@getTranslatedString}</span>
      </label>
    </span>
    <span class="slds-radio">
      <input type="radio" id="radio-4" name="relAction" {if ($task->selAct eq "delrel")}{"checked"}{/if} value="delrel" />
      <label class="slds-radio__label" for="radio-4">
        <span class="slds-radio_faux"></span>
        <span class="slds-form-element__label">{'Delete Relation'|@getTranslatedString}</span>
      </label>
    </span>
    <span class="slds-radio">
      <input type="radio" id="radio-5" name="relAction" {if ($task->selAct eq "dellAllrel")}{"checked"}{/if} value="dellAllrel" />
      <label class="slds-radio__label" for="radio-5">
        <span class="slds-radio_faux"></span>
        <span class="slds-form-element__label"> {'Delete All Relation'|@getTranslatedString}</span>
      </label>
    </span>
  </div>
</fieldset>
<br/>
<input type="hidden" name="idlist" value="" id="idlist">

<div class="slds-form-element">
  <label class="slds-form-element__label" for="relModlist">{'Select Related Module'|@getTranslatedString}</label><br>
    <div class="slds-form-element__control">
    <table class="slds-table slds-table_cell-buffer" style="width:100%">
      <tr>
        <td>
          <div class="slds-select_container">
            <select class="slds-select" id="relModlist_type">
              <option value="">{'No Module'|@getTranslatedString}</option>
            </select>
          </div>
        </td>
        <td> 
          <svg class="slds-icon slds-icon-text-default" aria-hidden="true" onclick='return vtlib_open_popup_window("","relModlist","{$entityName}","");'>
            <use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#new"></use>
          </svg>
        </td>
      </tr>
    </table>
    </div>
</div>

<br/>
<table aria-multiselectable="true" class="slds-table slds-table_bordered slds-table_edit slds-table_fixed-layout slds-table_resizable-cols slds-tree slds-table_tree" role="treegrid">
  <thead>
    <tr class="slds-line-height_reset">
      <th class="slds-text-align_right" scope="col" style="width: 3.25rem;">
        <div class="slds-th__action slds-th__action_form">
          <div class="slds-checkbox">
            <input type="checkbox" name="options1" disabled id="checkbox-6" tabindex="-1" aria-labelledby="check-select-all-label column-group-header" value="checkbox-6" />
            <label class="slds-checkbox__label" for="checkbox-6" id="check-select-all-label">
              <span class="slds-checkbox_faux"></span>
            </label>
          </div>
        </div>
      </th>
      <th aria-label="Entity Name" aria-sort="none" class="slds-has-button-menu slds-is-resizable slds-is-sortable" scope="col">
        <a class="slds-th__action slds-text-link_reset" href="javascript:void(0);" role="button" tabindex="-1">
          <div class="slds-grid slds-grid_vertical-align-center slds-has-flexi-truncate">
            <span class="slds-truncate" title="Entity Name">{'Entity Name'|@getTranslatedString}</span>
            <span class="slds-icon_container slds-icon-utility-arrowdown">
              <svg class="slds-icon slds-icon-text-default slds-is-sortable__icon " aria-hidden="true" >
                <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#arrowdown" />
              </svg>
            </span>
          </div>
        </a>
        <button class="slds-button slds-button_icon slds-th__action-button slds-button_icon-x-small" aria-haspopup="true" tabindex="-1" title="Show Entity Name column actions">
          <svg class="slds-button__icon slds-button__icon_hint slds-button__icon_small" aria-hidden="true">
            <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#chevrondown" />
          </svg>
        </button>
        <div class="slds-resizable">
          <input aria-label="Entity Name column width" class="slds-resizable__input slds-assistive-text" id="cell-resize-handle-6" max="1000" min="20" tabindex="-1" type="range" />
          <span class="slds-resizable__handle">
            <span class="slds-resizable__divider"></span>
          </span>
        </div>
      </th>
      <th aria-label="Entity" aria-sort="none" class="slds-has-button-menu slds-is-resizable slds-is-sortable" scope="col">
        <a class="slds-th__action slds-text-link_reset" href="javascript:void(0);" role="button" tabindex="-1">
          <div class="slds-grid slds-grid_vertical-align-center slds-has-flexi-truncate">
            <span class="slds-truncate" title="Entity">{'Entity Type'|@getTranslatedString}</span>
            <span class="slds-icon_container slds-icon-utility-arrowdown">
              <svg class="slds-icon slds-icon-text-default slds-is-sortable__icon " aria-hidden="true">
                <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#arrowdown" />
              </svg>
            </span>
          </div>
        </a>
        <div class="slds-resizable">
          <input aria-label="Entity column width" class="slds-resizable__input slds-assistive-text" id="cell-resize-handle-7" max="1000" min="20" tabindex="-1" type="range" />
          <span class="slds-resizable__handle">
            <span class="slds-resizable__divider"></span>
          </span>
        </div>
      </th>
      <th class="" scope="col" style="width: 3.25rem;">
      </th>
    </tr>
  </thead>
  <tbody id="selected_recordsDiv">
  </tbody>
</table>