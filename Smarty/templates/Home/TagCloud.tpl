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
{if $USER_TAG_SHOWAS != 'flat'}
<div id="tagcloud" style="overflow: hidden; width: 100%; padding-left: 2%; padding-right: 3%; min-height: 250px;" >
 <canvas style="width:290px;height:290px;overflow:hidden;" id="tagcloudCanvas">
  <ul>{$ALL_TAG}</ul>
 </canvas>
</div>
{else}
<div id="tagcloud" style="overflow: hidden; width: 100%; padding-left: 2%; padding-right: 3%; min-height: 250px;" >
	<img src='{$IMAGE_PATH}/tagCloudName.gif' style="display: block; width: 100%;"/>
	<br>
	<span id="tagfields">{$ALL_TAG}</span>
</div>
{/if}