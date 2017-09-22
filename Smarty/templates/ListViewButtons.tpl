{foreach key=button_check item=button_label from=$BUTTONS}
	{if $button_check eq 'del'}
		<input class="slds-button slds-button--destructive slds-button--small" type="button" value="{$button_label}" onclick="return massDelete('{$MODULE}')"/>
	{elseif $button_check eq 'mass_edit'}
		<input class="slds-button slds-button--brand slds-button--small" type="button" value="{$button_label}" onclick="return mass_edit(this, 'massedit', '{$MODULE}', '{$CATEGORY}')"/>
	{elseif $button_check eq 's_mail'}
		<input class="slds-button slds-button_success slds-button--small" type="button" value="{$button_label}" onclick="return eMail('{$MODULE}',this);"/>
	{elseif $button_check eq 's_cmail'}
		<input class="slds-button slds-button_success slds-button--small" type="submit" value="{$button_label}" onclick="return massMail('{$MODULE}')"/>
	{elseif $button_check eq 'mailer_exp'}
		<input class="slds-button slds-button_success slds-button--small" type="submit" value="{$button_label}" onclick="return mailer_export()"/>
	{* Mass Edit handles Change Owner for other module except Calendar *}
	{elseif $button_check eq 'c_owner' && $MODULE eq 'Calendar'}
		<input class="slds-button slds-button_success slds-button--small" type="button" value="{$button_label}" onclick="return change(this,'changeowner')"/>
	{/if}
{/foreach}
{include file='ListViewCustomButtons.tpl'}
{if $MODULE eq 'Documents'}
	{if $CHECK.EditView eq 'yes'}
		<input type="button" name="move" value="{$MOD.LBL_MOVE}" class="slds-button slds-button_success slds-button--small" onClick="fnvshNrm('movefolderlist'); posLay(this,'movefolderlist');" title="{$MOD.LBL_MOVE_DOCUMENTS}">
		<div style="display:none;position:absolute;" id="movefolderlist" >
			<div class="layerPopup thickborder" style="display:block;position:relative;width:250px;z-index: 999;">
				<table class="slds-table slds-no-row-hover" width="100%">
					<tr class="slds-text-title--header">
						<th scope="col">
							<div class="slds-truncate moduleName">
								<b>{$MOD.LBL_MOVE_TO}</b>
							</div>
						</th>
						<th scope="col" style="padding: .5rem;text-align: right;">
							<div class="slds-truncate">
								<a onclick="fninvsh('movefolderlist')" href="javascript:void(0);">
								<img border="0" align="absmiddle" src="{'close.gif'|@vtiger_imageurl:$THEME}"/></a>
							</div>
						</th>
					</tr>
				</table>
				<table class="slds-table slds-table--bordered drop_down" width="100%">
					{foreach item=folder from=$ALL_FOLDERS}
						<tr class="slds-line-height--reset">
							<td align="left">
								<a href="javascript:;" onClick="MoveFile('{$folder.folderid}','{$folder.foldername}');" > {$folder.foldername}</a>
							</td>
						</tr>
					{/foreach}
				</table>
			</div>
		</div>
		<input type="button" name="add" value="{$MOD.LBL_ADD_NEW_FOLDER}" class="slds-button slds-button_success slds-button--small" onClick="fnvshobj(this,'orgLay');" title="{$MOD.LBL_ADD_NEW_FOLDER}">
		<div id="orgLay" style="display:none;width:350px;z-index: 9999;" class="layerPopup" >
			<table class="slds-table slds-no-row-hover" width="100%">
				<tr class="slds-text-title--header">
					<th scope="col" id="editfolder_info">
						<div class="slds-truncate moduleName">
							<b>{$MOD.LBL_ADD_NEW_FOLDER}</b>
						</div>
					</th>
					<th scope="col" style="padding: .5rem;text-align: right;">
						<div class="slds-truncate">
							<a href="javascript:;" onClick="closeFolderCreate();">
								<img src="{'close.gif'|@vtiger_imageurl:$THEME}" align="absmiddle" border="0">
							</a>
						</div>
					</th>
				</tr>
			</table>
			<table class="slds-table slds-no-row-hover slds-table--bordered addfolderDocuments">
				<tr class="slds-line-height--reset">
					<td align="right" nowrap class="cellLabel small">
						<font color='red'>*</font>&nbsp;<b>{$MOD.LBL_FOLDER_NAME}</b>
					</td>
					<td align="left" class="cellText small" >
						<input id="folder_id" name="folderId" type="hidden" value=''>
						<input id="fldrsave_mode" name="folderId" type="hidden" value='save'>
						<input id="folder_name" name="folderName" class="txtBox slds-input" type="text" placeholder="{$MOD.LBL_MAXIMUM_20}">
					</td>
				</tr>
				<tr class="slds-line-height--reset">
					<td class="cellLabel small" align="right" nowrap >
						<b>{$MOD.LBL_FOLDER_DESC}</b>
					</td>
					<td class="cellText small" align="left" >
						<input id="folder_desc" name="folderDesc" class="txtBox slds-input" type="text" placeholder="{$MOD.LBL_MAXIMUM_50}">
					</td>
				</tr>
			</table>
			<table border=0 cellspacing=0 cellpadding=5 width=100% class="layerPopupTransport">
				<tr class="slds-line-height--reset">
					<td align="center" style="padding: .3rem;">
						<input name="save" value=" &nbsp;{$APP.LBL_SAVE_BUTTON_LABEL}&nbsp; " class="slds-button slds-button_success slds-button--small" onClick="AddFolder();" type="button">&nbsp;&nbsp;
						<input name="cancel" value=" {$APP.LBL_CANCEL_BUTTON_LABEL} " class="slds-button slds-button--destructive slds-button--small" onclick="closeFolderCreate();" type="button">
					</td>
				</tr>
			</table>
		</div>
	{/if}
	{if $EMPTY_FOLDERS|@count gt 0}
		<input type="button" name="show" value="{$MOD.LBL_VIEW_EMPTY_FOLDERS}" class="slds-button slds-button--destructive slds-button--small" onClick="fnvshobj(this,'emptyfolder');" title="{$MOD.LBL_VIEW_EMPTY_FOLDERS}">
		<div class="layerPopup thickborder" style="display:none;position:absolute; left:193px;top:106px;width:250px;z-index:1" id="emptyfolder">
			<table class="slds-table slds-no-row-hover" width="100%">
				<tr class="slds-line-height--reset">
					<th scope="col">
						<div class="slds-truncate moduleName">
							<b>{$MOD.LBL_EMPTY_FOLDERS}</b>
						</div>
					</th>
					<th scope="col" style="padding: .5rem;text-align: right;">
						<div class="slds-truncate">
							<a onclick="fninvsh('emptyfolder')" href="javascript:void(0);">
							<img border="0" align="absmiddle" src="{'close.gif'|@vtiger_imageurl:$THEME}"/></a>
						</div>
					</th>
				</tr>
			</table>
			<table class="slds-table slds-table--bordered drop_down" width="100%">
				{foreach item=folder from=$EMPTY_FOLDERS}
					<tr class="slds-line-height--reset">
						<td>{$folder.foldername}</td>
						<td align=right>
							{if $IS_ADMIN eq "on" && $folder.folderid neq "1"}
								<a href="javascript:;" onclick="DeleteFolderCheck({$folder.folderid});">
									<img border="0" class="delete-documents-folder" src="{'delete.gif'|@vtiger_imageurl:$THEME}"/>
								</a>
							{else}
								&nbsp;
							{/if}
						</td>
					</tr>
				{/foreach}
			</table>
		</div>
	{/if}
{/if}