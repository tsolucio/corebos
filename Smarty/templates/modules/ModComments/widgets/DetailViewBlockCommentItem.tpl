{*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************}
<div id="comment_id_{$COMMENTMODEL->id()}">
<div id="mouseArea_{$COMMENTMODEL->id()}" onmouseover="hndMouseOver(19,'{$COMMENTMODEL->id()}');" onmouseout="fnhide('crmspanid');" {if $COMMENTMODEL->editPermission() }onclick='handleEdit(event);'{/if}>
	<div id="dtlview_{$COMMENTMODEL->id()}">
		<div class="dataField" id="comment_content_{$COMMENTMODEL->id()}" style="width: 99%; padding-top: 10px;">
			{$COMMENTMODEL->content()|@nl2br}
		</div>
		<div class="dataLabel" style="border-bottom: 1px dotted rgb(204, 204, 204); width: 99%; padding-bottom: 5px;color:darkred;">
			{$MOD.LBL_AUTHOR}: {$COMMENTMODEL->author()} {$MOD.LBL_ON_DATE} {$COMMENTMODEL->timestamp()}
		</div>
	</div>
	<div id="editarea_{$COMMENTMODEL->id()}" style="display:none">
		<textarea id="txtbox_{$COMMENTMODEL->id()}" class="detailedViewTextBox" onFocus="this.className='detailedViewTextBoxOn'" onBlur="this.className='detailedViewTextBox'" cols="90" rows="8">{$COMMENTMODEL->content()}</textarea>
		<br><a href="javascript:;" class="detailview_ajaxbutton ajax_save_detailview" onclick="ModCommentsCommon.editComment('{$COMMENTMODEL->id()}');">{$APP.LBL_SAVE_LABEL}</a>
	</div>
</div>
</div>