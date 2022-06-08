<div class="slds-button-group" role="group">
	{foreach key=button_check item=button_label from=$BUTTONS}
		{if $button_check eq 'del'}
			<button type="button" class="slds-button slds-button_text-destructive" onclick="return massDelete('{$MODULE}')">
				<svg class="slds-button__icon slds-button__icon_left" aria-hidden="true">
					<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#delete"></use>
				</svg>
			{$button_label}
		</button>
		{elseif $button_check eq 'mass_edit'}
			<button type="button" class="slds-button slds-button_success" onclick="return mass_edit(this, 'massedit', '{$MODULE}')" style="color: #ffffff">
				<svg class="slds-button__icon slds-button__icon_left" aria-hidden="true">
					<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#edit"></use>
				</svg>
			{$button_label}
		</button>
		{/if}
	{/foreach}
	{include file='ListViewCustomButtons.tpl'}
	{if $MODULE eq 'Documents'}
		{if $CHECK.EditView eq 'yes'}
			{assign var="customfunction" value="fnvshNrm('movefolderlist'); posLay(this,'movefolderlist');"}
			{if $moduleView == 'tuigrid'}
				{assign var="customfunction" value="DocumentsView.MoveFile()"}
			{/if}
			<button type="button" name="move" class="slds-button slds-button_neutral" onclick="{$customfunction}" title="{$MOD.LBL_MOVE_DOCUMENTS}">
				<svg class="slds-button__icon slds-button__icon_left" aria-hidden="true">
					<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#touch_action"></use>
				</svg>
				{$MOD.LBL_MOVE}
			</button>
			<div style="display:none;position:absolute;width:150px;z-index: 9999" id="movefolderlist" >
				<div class="layerPopup thickborder" style="display:block;position:relative;width:250px;z-index: 9999">
					<table  class="layerHeadingULine" border="0" cellpadding="5" cellspacing="0" width="100%">
						<tr>
							<td class="genHeaderSmall" align="left" width="90%">
								{$MOD.LBL_MOVE_TO}
							</td>
							<td align="right" width="10%">
								<a onclick="fninvsh('movefolderlist')" href="javascript:void(0);">
								<img border="0" align="absmiddle" src="{'close.gif'|@vtiger_imageurl:$THEME}"/></a>
							</td>
						</tr>
					</table>
					<div style='padding: 10px;'>
						<table class="drop_down"  border="0" cellpadding="5" cellspacing="1" width="100%">
							{foreach item=folder from=$ALL_FOLDERS}
								<tr class='lvtColData' onmouseout="this.className='lvtColData'" onmouseover="this.className='lvtColDataHover'">
									<td align="left">
										<a href="javascript:;" onClick="MoveFile('{$folder.folderid}','{$folder.foldername}');" > {$folder.foldername}</a>
									</td>
								</tr>
							{/foreach}
						</table>
					</div>
				</div>
			</div>
			<button type="button" name="add" class="slds-button slds-button_neutral" onclick="fnvshobj(this,'orgLay');" title="{$MOD.LBL_ADD_NEW_FOLDER}">
				<svg class="slds-button__icon slds-button__icon_left" aria-hidden="true">
					<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#touch_action"></use>
				</svg>
				{$MOD.LBL_ADD_NEW_FOLDER}
			</button>
			<div id="orgLay" style="display:none;width:350px;" class="layerPopup" >
				<table border=0 cellspacing=0 cellpadding=5 width=100% class=layerHeadingULine>
					<tr>
						<td class="genHeaderSmall" nowrap align="left" width="30%" id="editfolder_info">{$MOD.LBL_ADD_NEW_FOLDER}</td>
						<td align="right"><a href="javascript:;" onClick="closeFolderCreate();"><img src="{'close.gif'|@vtiger_imageurl:$THEME}" align="absmiddle" border="0"></a></td>
					</tr>
				</table>
				<table border=0 cellspacing=0 cellpadding=5 width=95% align=center>
					<tr>
						<td class="small">
							<table border=0 cellspacing=0 cellpadding=5 width=100% align=center bgcolor=white>
							<tr>
								<td align="right" nowrap class="cellLabel small"><font color='red'>*</font>&nbsp;<b>{$MOD.LBL_FOLDER_NAME}</b></td>
								<td align="left" class="cellText small">
								<input id="folder_id" name="folderId" type="hidden" value=''>
								<input id="fldrsave_mode" name="folderId" type="hidden" value='save'>
								<input id="folder_name" name="folderName" class="txtBox" type="text" placeholder="{$MOD.LBL_MAXIMUM_20}">
								</td>
							</tr>
							<tr>
								<td class="cellLabel small" align="right" nowrap><b>{$MOD.LBL_FOLDER_DESC}</b></td>
								<td class="cellText small" align="left"><input id="folder_desc" name="folderDesc" class="txtBox" type="text" placeholder="{$MOD.LBL_MAXIMUM_50}"></td>
							</tr>
							</table>
						</td>
					</tr>
				</table>
				<table border=0 cellspacing=0 cellpadding=5 width=100% class="layerPopupTransport">
					<tr>
						<td class="small" align="center">
							<input name="save" value=" &nbsp;{$APP.LBL_SAVE_BUTTON_LABEL}&nbsp; " class="crmbutton small save" onClick="AddFolder();" type="button">&nbsp;&nbsp;
							<input name="cancel" value=" {$APP.LBL_CANCEL_BUTTON_LABEL} " class="crmbutton small cancel" onclick="closeFolderCreate();" type="button">
						</td>
					</tr>
				</table>
			</div>
		{/if}
		{if $EMPTY_FOLDERS|@count gt 0}
			<button type="button" name="show" class="slds-button slds-button_neutral" onclick="fnvshobj(this,'emptyfolder');" title="{$MOD.LBL_VIEW_EMPTY_FOLDERS}">
				<svg class="slds-button__icon slds-button__icon_left" aria-hidden="true">
					<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#touch_action"></use>
				</svg>
				{$MOD.LBL_VIEW_EMPTY_FOLDERS}
			</button>
			<div class="layerPopup thickborder" style="display:none;position:absolute; left:193px;top:106px;width:250px;z-index:1" id="emptyfolder">
				<table  class="layerHeadingULine" border="0" cellpadding="5" cellspacing="0" width="100%">
					<tr>
						<td class="genHeaderSmall" align="left">
							{$MOD.LBL_EMPTY_FOLDERS}
						</td>
						<td align="right" width="40%">
							<a onclick="fninvsh('emptyfolder')" href="javascript:void(0);">
							<img border="0" align="absmiddle" src="{'close.gif'|@vtiger_imageurl:$THEME}"/></a>
						</td>
					</tr>
				</table>
				<div style='padding: 10px;'>
					<table class="drop_down"  border=0 cellpadding=5 cellspacing=0 width=100%>
						{foreach item=folder from=$EMPTY_FOLDERS}
							<tr onmouseout="this.className='lvtColData'" onmouseover="this.className='lvtColDataHover'">
								<td>{$folder.foldername}</td>
								<td align=right>
									{if $IS_ADMIN eq "on" && $folder.folderid neq "1"}
										<a href="javascript:;" onclick="DeleteFolderCheck({$folder.folderid});"><img border="0" src="{'delete.gif'|@vtiger_imageurl:$THEME}" style="cursor: pointer;"/></a>
									{else}
										&nbsp;
									{/if}
								</td>
							</tr>
						{/foreach}
					</table>
				</div>
			</div>
		{/if}
	{/if}
</div>
