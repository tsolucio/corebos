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
<h2>{'LBL_INV_DAYS_FROM_WF'|@getTranslatedString:'com_vtiger_workflow'}</h2>
<input type="text" name="invoice_duedate_days" id="invoice_duedate_days" value="{$task->invoice_duedate_days}"><br>
<h2>{'EXACT_PAYMENT_CONDS'|@getTranslatedString:'com_vtiger_workflow'}</h2>
<input type="text" name="exact_payment_cond" id="exact_payment_cond" value="{$task->exact_payment_cond}">