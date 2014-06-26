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
*  Module       : cbupdater
*  Version      : 5.5.0
*  Author       : JPL TSolucio, S. L.
*************************************************************************************************/
-->*}

{include file='Buttons_List.tpl'}

{if $ERROR}
<div id="errorcontainer" style="padding:20px;">
  <div id="errormsg" style="color: #f85454; font-weight: bold; padding: 10px; border: 1px solid #FF0000; background: #FFFFFF; border-radius: 5px; margin-bottom: 10px;">{$ERRORMSG}</div>
</div>
{/if}
{foreach item=detail from=$CBUPDATES}
<div style="padding:20px;">
  <div style="color: olive; font-weight: bold; padding: 10px; border: 1px solid olive; background: #FFFFFF; border-radius: 5px; margin-bottom: 10px;">
  <b>{'ChangeSet'|getTranslatedString:$MODULE}:</b> {$detail.filename}::{$detail.classname}
  {if isset($detail.description)}<br>{$detail.description}{/if}
  </div>
</div>
{/foreach}
<br>
<div style="padding:20px;text-align:center;font-size:large;">
<a href='index.php?module=cbupdater&action=ListView'>{'LBL_GO_BACK'|getTranslatedString:$MODULE}</a>
</div>
