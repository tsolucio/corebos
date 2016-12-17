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
            // 6 create an instance when the DOM is ready
            var inst = $('#jstree').jstree({
                "core" : {
                    "check_callback" : true,
                    "data" : {/literal}{$MENUSTRUCTURE}{literal},
                    "themes" : {
                        "variant" : "large"
                    }
                },
                "types" : {
                    "#" : {
                        "valid_children" : ["root"]
                    },
                    "root" : {
                        "icon" : "/static/3.3.3/assets/images/tree_icon.png",
                        "valid_children" : ["default", "root", "file"]
                    },
                    "default" : {
                        "valid_children" : ["default","file"]
                    },
                    "file" : {
                        "icon" : "glyphicon glyphicon-file",
                        "valid_children" : []
                    }
                },
                "plugins" : [
                    "dnd",
                    "state", "types", "wholerow"
                ]
            });
            // 7 bind to events triggered on the tree
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

<div style="padding:20px"><div style="color: #14a1e9; font-weight: bold; font-size: medium; padding: 10px; border: 1px solid #1399dd; background: #FFFFFF; border-radius: 5px; margin-bottom: 4px;">{'evvtMenuTitle'|getTranslatedString:$MODULE}</div></div>

<div id="evvtmenu" class="k-content">
  <div class="evvtmenu-section">
    <div class="evvtmenu_header">{'evvtMenuLayout'|getTranslatedString:$MODULE}</div>
    <div class="evvtmenu_content">
      <form action="index.php?module={$MODULE}&action=Save&parenttab={$CATEGORY}" method="POST" id="menuconfigform">
        <input type="hidden" name="evvtmenutree" id="evvtmenutree" value="">
        <input type="hidden" name="evvtmenudo" value="doSave">
      </form>

        <button class="slds-button slds-button--brand" style="float:right;margin-right: 45px;margin-bottom: 15px;" onclick="saveTree();">{'LBL_SAVE_LABEL'|getTranslatedString:$MODULE}</button>

        <div id="jstree">
            {$MENUTREE}
        </div>

  </div></div>
  <div class="evvtmenu-form">
    <div class="evvtmenu_header">{'evvtMenuItemInfo'|getTranslatedString:$MODULE}</div>
      <form action="index.php?module={$MODULE}&action=Save&parenttab={$CATEGORY}" method="POST" id="menuitemform" style="margin-top:25px">
        <input type="hidden" name="evvtmenuid" id="evvtmenuid" value="">
        <input type="hidden" name="evvtmenudo" id="evvtmenudo" value="">
        <input type="hidden" name="treeIds" id="treeIds" value="">
        <input type="hidden" name="treeParents" id="treeParents" value="">
        <input type="hidden" name="treePositions" id="treePositions" value="">
      <div class="slds-form--horizontal" style="float:left;margin-left:40px;;width:90%;text-align:left;">
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
                      <select name="modname" id="modname" class="slds-select">
                          {foreach item=detail from=$MODNAMES }
                            <option value="{$detail}">{$detail}</option>
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
          <label class="slds-form-element__label evvtmenu-label" for="mpermission">{'MenuPermission'|getTranslatedString:$MODULE}</label>
          {html_options name="mpermission[]" id="mpermission" multiple="multiple" options=$PROFILES}
			</div>
          </br>
			<div style="width:90%;margin:auto;">
              <button class="slds-button slds-button--brand" onclick="VtigerJS_DialogBox.block();processTree('doAdd');">{'LBL_ADD_BUTTON'|getTranslatedString:$MODULE}</button>
              <button class="slds-button slds-button--brand" onclick="VtigerJS_DialogBox.block();processTree('doUpd');">{'LBL_UPDATE'|getTranslatedString:$MODULE}</button>
              <button class="slds-button slds-button--destructive" onclick="VtigerJS_DialogBox.block();processTree('doDel');">{'LBL_DELETE_BUTTON'|getTranslatedString:$MODULE}</button>
              <button class="slds-button slds-button--neutral" onclick="clearForm();">{'LBL_CLEAR_BUTTON_LABEL'|getTranslatedString:$MODULE}</button>
			</div>
      </div>
	</form>
  </div>
</div>
