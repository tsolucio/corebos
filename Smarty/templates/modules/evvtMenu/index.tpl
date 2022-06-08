<link href="modules/evvtMenu/evvtMenu.css" rel="stylesheet" type="text/css"/>
<script type="text/javascript" src="modules/evvtMenu/evvtMenu.js"></script>
<script src="modules/evvtMenu/jquery.fancytree-all.min.js"></script>
<link rel="stylesheet" href="modules/evvtMenu/style.min.css" />
<script src="modules/evvtMenu/jstree.min.js"></script>
{literal}
<script type="text/javascript">
	var ids = [];
	var parents = [];
	var positions = [];
	$(function () {
		// create an instance when the DOM is ready
		var inst = $('#jstree').jstree({
			'core': {
				'check_callback': true,
				'data': {/literal}{$MENUSTRUCTURE}{literal},
				'themes': {
					'variant' : 'large'
				}
			},
			'types': {
				'#': {
					'valid_children' : ['root']
				},
				'root': {
					'icon' : '/static/3.3.3/assets/images/tree_icon.png',
					'valid_children' : ['default', 'root', 'file']
				},
				'default': {
					'valid_children' : ['default','file']
				},
				'file': {
					'icon' : 'glyphicon glyphicon-file',
					'valid_children' : []
				}
			},
			'plugins': [
				'dnd',
				'state', 'types', 'wholerow'
			]
		});
		// bind to events triggered on the tree
		$('#jstree').on('move_node.jstree', function (e, data) {
			var id = data.node.id;
			var parentId = data.instance.get_node(data.node.parent).id;
			var position = data.position + 1;
			ids.push(id);
			parents.push(parentId);
			positions.push(position);
		});
	});
</script>
{/literal}

<div id="mainmenus">
	<div class="slds-p-top_small slds-p-left_large slds-p-right_large">
		<div style="color: #14a1e9; font-weight: bold; font-size: medium; padding: 10px; border: 1px solid #1399dd; background: #FFFFFF; border-radius: 5px; margin-bottom: 4px;">
			{'evvtMenuTitle'|getTranslatedString:$MODULE}
			<button class="slds-button slds-button_neutral" style="float:right;line-height:unset;" onclick="opensavedmenu();">{'LBL_IMPORT_EXPORT_BUTTON'|getTranslatedString:$MODULE}</button>
		</div>
	</div>
	<div id="evvtmenu" class="k-content slds-box slds-m-around_medium" style="float: left; width: 98%; box-sizing: border-box; background: #ffffff;">
		<div class="evvtmenu-section">
			<div class="evvtmenu_header">
				{'evvtMenuLayout'|getTranslatedString:$MODULE}
				<button class="slds-button slds-button_brand" style="float:right;line-height:unset;" onclick="saveTree();">{'LBL_SAVE_LABEL'|getTranslatedString:$MODULE}</button>
			</div>
			<div class="evvtmenu_content">
				<form action="index.php?module={$MODULE}&action=Save" method="POST" id="menuconfigform">
					<input type="hidden" name="evvtmenutree" id="evvtmenutree" value="">
					<input type="hidden" name="evvtmenudo" value="doSave">
				</form>
				<div id="jstree"></div>
			</div>
		</div>
		<div class="evvtmenu-form">
			<div class="evvtmenu_header">{'evvtMenuItemInfo'|getTranslatedString:$MODULE}</div>
			<form action="index.php?module={$MODULE}&action=Save" method="POST" id="menuitemform" style="margin-top:25px">
				<input type="hidden" name="evvtmenuid" id="evvtmenuid" value="">
				<input type="hidden" name="evvtmenudo" id="evvtmenudo" value="">
				<input type="hidden" name="treeIds" id="treeIds" value="">
				<input type="hidden" name="treeParents" id="treeParents" value="">
				<input type="hidden" name="treePositions" id="treePositions" value="">
				<div class="slds-form_horizontal" style="float:left;margin-left:40px;;width:90%;text-align:left;">
					<div id="typeForm">
						<label class="slds-form-element__label evvtmenu-label" for="mtype">{'MenuType'|getTranslatedString:$MODULE}</label>
						<div class="slds-form-element__control">
							<div class="slds-select_container">
								<select name="mtype" id="mtype" onchange="showFormParts(this.value);" class="slds-select">
									<option value="menu">{'mt_menu'|getTranslatedString:$MODULE}</option>
									<option value="module">{'mt_module'|getTranslatedString:$MODULE}</option>
									<option value="url">{'mt_url'|getTranslatedString:$MODULE}</option>
									<option value="headtop">{'mt_headertop'|getTranslatedString:$MODULE}</option>
									<option value="headbottom">{'mt_headerbottom'|getTranslatedString:$MODULE}</option>
									<option value="sep">{'mt_separator'|getTranslatedString:$MODULE}</option>
								</select>
							</div>
						</div>
					</div>
					<div id="labelForm">
						<label class="slds-form-element__label evvtmenu-label" for="mlabel">{'MenuLabel'|getTranslatedString:$MODULE}</label>
						<div class="slds-form-element__control">
							<input id="mlabel" class="slds-input" type="text" name="mlabel"/>
						</div>
					</div>
					<div id="actionForm">
						<label class="slds-form-element__label evvtmenu-label" for="mvalue">{'MenuValue'|getTranslatedString:$MODULE}</label>
						<div class="slds-form-element__control">
							<input name="mvalue" id="mvalue" class="slds-input" type="text"/>
						</div>
					</div>
					<div class="hide" id="moduleForm">
						<label class="slds-form-element__label evvtmenu-label" for="modname">{'MenuValue'|getTranslatedString:$MODULE}</label>
						<div class="slds-form-element__control">
							<div class="slds-select_container">
								<select name="modname" id="modname" class="slds-select" onchange="document.getElementById('mlabel').value=this.value;">
									{foreach item=detail from=$MODNAMES }
										<option value="{$detail}">{$detail|getTranslatedString:$detail}</option>
									{/foreach}
								</select>
							</div>
						</div>
					</div>
					<div id="parentForm">
						<label class="slds-form-element__label evvtmenu-label" for="mparent">{'MenuParent'|getTranslatedString:$MODULE}</label>
						<div class="slds-form-element__control">
							<div class="slds-select_container">
								<select name="mparent" id="mparent" class="slds-select">
									{foreach item=details key=k from=$PARENTS}
										<option value="{$k}">{$details}</option>
									{/foreach}
								</select>
							</div>
						</div>
					</div>
					<div id="permissionForm">
						<label class="slds-form-element__label evvtmenu-label" for="mvisible">{'MenuVisible'|getTranslatedString:$MODULE}</label>
						<input type="checkbox" name="mvisible" id="mvisible" checked>
					</div>
					<div id="permissionForm">
						<label class="slds-form-element__label evvtmenu-label" for="mpermission">{'MenuPermission'|getTranslatedString:$MODULE}</label>
						{html_options name="mpermission[]" id="mpermission" multiple="multiple" options=$PROFILES}
					</div>
					</br>
					<div class="slds-align_absolute-center slds-m-bottom_small" style="width:90%;">
						<button class="slds-button slds-button_brand" onclick="VtigerJS_DialogBox.block();processTree('doAdd');">{'LBL_ADD_BUTTON'|getTranslatedString:$MODULE}</button>
						<button class="slds-button slds-button_brand" onclick="VtigerJS_DialogBox.block();processTree('doUpd');">{'LBL_UPDATE'|getTranslatedString:$MODULE}</button>
						<button class="slds-button slds-button_destructive" onclick="VtigerJS_DialogBox.block();processTree('doDel');">{'LBL_DELETE_BUTTON'|getTranslatedString:$MODULE}</button>
						<button class="slds-button slds-button_neutral" onclick="clearForm();">{'LBL_CLEAR_BUTTON_LABEL'|getTranslatedString:$MODULE}</button>
					</div>
				</div>
			</form>
		</div>
	</div>
</div>

<div id="savedmenus" style="display:none;">
	<div class="slds-p-top_small slds-p-left_large slds-p-right_large">
		<div style="color: #14a1e9; font-weight: bold; font-size: medium; padding: 10px; border: 1px solid #1399dd; background: #FFFFFF; border-radius: 5px; margin-bottom: 4px;">
			{'evvtMenuTitle'|getTranslatedString:$MODULE}
			<button class="slds-button slds-button_neutral" style="float:right;line-height:unset;" onclick="openmainmenu();">{'LBL_MENU_BUTTON'|getTranslatedString:$MODULE}</button>
		</div>
	</div>

	<div id="savedmenu" class="k-content slds-box slds-m-around_medium" style="float: left; width: 98%; box-sizing: border-box; background: #ffffff;">
		<div class="evvtmenu-section">
			<div class="evvtmenu_header">
				{'LBL_SAVED_Menus'|getTranslatedString:$MODULE}
			</div>
			<div class="evvtmenu_content">
				<div class="slds-dropdown-trigger slds-dropdown-trigger_click slds-is-open">
					<div class="slds-dropdown_left">
						<ul class="slds-dropdown__list" role="menu">
							{foreach item=menus key=k from=$SAVEDMENU}
								<li class="slds-dropdown__item slds-page-header__meta-text" role="presentation">
									<a href="javascript:void(0);" onclick="getsavedmenuname('{$menus}','{$k}');" role="menuitem" style="padding-top:0.1rem;padding-bottom:0.1rem;">
										<span class="slds-truncate" title="{$menus}">{$menus}</span>
									</a>
								</li>
							{/foreach}
						</ul>
					</div>
				</div>
			</div>
		</div>

		<div class="evvtmenu-form">
			<div class="evvtmenu_header">{'LBL_ACTIONS'|getTranslatedString:$MODULE}</div>
				<form action="index.php?module={$MODULE}&action=Save" method="POST" ENCTYPE="multipart/form-data" id="savedmenuform" style="margin-top:25px">
					<input type="hidden" name="savemenuid" id="smkey">
					<input type="hidden" name="savedmenudo" id="savedmenudo" value="">
					<div class="slds-form_horizontal" style="float:left;margin-left:40px;;width:90%;text-align:left;">
						<div id="menuForm">
							<label class="slds-form-element__label evvtmenu-label" for="mlabel">Name</label>
							<div class="slds-form-element__control">
								<input id="menuname" class="slds-input" type="text" name="menuname"/>
							</div>
						</div>
						<div id="actionForm">
							<div class="slds-form-element">
								<div class="slds-file-selector slds-file-selector_files">
									<div class="slds-file-selector__dropzone">
										<input type="file" name="jsonfile" value="jsonfile" class="slds-file-selector__input slds-assistive-text" accept=".json" id="importmenu" aria-labelledby="file-selector-primary-label file-selector-secondary-label" />
										<label class="slds-file-selector__body" for="importmenu" id="file-selector-secondary-label">
											<span class="slds-file-selector__button slds-button slds-button_neutral">
												<svg class="slds-button__icon slds-button__icon_left" aria-hidden="true">
													<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#upload"></use>
												</svg>Import Menu</span>
										</label>
									</div>
								</div>
							</div>
						</div>
						</br>
						<div class="slds-align_absolute-center slds-m-bottom_small" style="width:100%;">
							<button class="slds-button slds-button_brand" onclick="VtigerJS_DialogBox.block();processMenus('doSaveCurrent');">{'LBL_SAVE_CURRENT_BUTTON'|getTranslatedString:$MODULE}</button>
							<button class="slds-button slds-button_brand" onclick="VtigerJS_DialogBox.block();processMenus('doApplySaved');">{'LBL_APPLY_BUTTON'|getTranslatedString:$MODULE}</button>
							<button class="slds-button slds-button_brand" onclick="VtigerJS_DialogBox.block();processMenus('doRenameSaved');">{'LBL_RENAME_BUTTON'|getTranslatedString:$MODULE}</button>
							<button class="slds-button slds-button_brand" onclick="processMenus('doDownloadMenu');">{'LBL_DOWNLOAD_BUTTON'|getTranslatedString:$MODULE}</button>
							<button class="slds-button slds-button_brand" onclick="VtigerJS_DialogBox.block();processMenus('doImportMenu');">{'LBL_IMPORT_BUTTON'|getTranslatedString:$MODULE}</button>
							<button class="slds-button slds-button_destructive" onclick="VtigerJS_DialogBox.block();processMenus('doDelSaved');">{'LBL_DELETE_BUTTON'|getTranslatedString:$MODULE}</button>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>
