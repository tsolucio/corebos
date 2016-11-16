{*
// esto no hace falta porque se carga en el menu, lo quito para optimizar carga y mantener estilo homogéneo
<link href="{$KUIDIR}styles/kendo.common.min.css" rel="stylesheet" type="text/css"/>
<link href="{$KUIDIR}styles/kendo.bootstrap.min.css" rel="stylesheet" type="text/css"/>
*}
<script type="text/javascript" src="modules/evvtMenu/evvtMenu.js"></script>
<script src="https://use.fontawesome.com/6022c11b2b.js"></script>
<link href="modules/evvtMenu/evvtMenu.css" rel="stylesheet" type="text/css"/>

<div style="padding:20px"><div style="color: #14a1e9; font-weight: bold; font-size: medium; padding: 10px; border: 1px solid #1399dd; background: #FFFFFF; border-radius: 5px; margin-bottom: 4px;">{'evvtMenuTitle'|getTranslatedString:$MODULE}</div></div>

<div id="evvtmenu" class="k-content">
  <div class="evvtmenu-section">
    <div class="evvtmenu_header">{'evvtMenuLayout'|getTranslatedString:$MODULE}</div>
    <div class="evvtmenu_content">
      <form action="index.php?module={$MODULE}&action=Save&parenttab={$CATEGORY}" method="POST" id="menuconfigform">
        <input type="hidden" name="evvtmenutree" id="evvtmenutree" value="">
        <input type="hidden" name="evvtmenudo" value="doSave">
      </form>
      <input type="button" onclick="sendMenuConfig();" class="crmbutton small save" style="float:right;margin-right: 45px;" value=" {'LBL_SAVE_LABEL'|getTranslatedString:$MODULE} " name="menusve">
        <a onclick="expandTree();"<i class="fa fa-plus" aria-hidden="true"></i></a>&nbsp;&nbsp;<a onclick="contractTree();"<i class="fa fa-minus" aria-hidden="true"></i></a>
        <div class="treeview" id="treeview">
             <ul>
                {$MENU}
            </ul>
      {*<input type="button" onclick="sendMenuConfig();" class="crmbutton small save" style="float:right;margin-right: 45px;margin-bottom: 15px;" value=" {'LBL_SAVE_LABEL'|getTranslatedString:$MODULE} " name="menusve">*}
    </div>
  </div></div>
  <div class="evvtmenu-form">
    <div class="evvtmenu_header">{'evvtMenuItemInfo'|getTranslatedString:$MODULE}</div>
    <div class="evvtmenu_content">
      <form action="index.php?module={$MODULE}&action=Save&parenttab={$CATEGORY}" method="POST" id="menuitemform">
        <input type="hidden" name="evvtmenuid" id="evvtmenuid" value="">
        <input type="hidden" name="mparent" id="mparent" value="">
        <input type="hidden" name="evvtmenudo" id="evvtmenudo" value="">

        </div>
      <div class="slds-form--horizontal" style="float:left;">
          <div class="slds-form-element" id="typeForm">
              <label class="slds-form-element__label" for="mtype">Type</label>
              <div class="slds-form-element__control">
                  <div class="slds-select_container">
                      <select name="mtype" id="mtype" onchange="showFormParts(this.value);" class="slds-select">
                          <option value="menu">{'mt_menu'|getTranslatedString:$MODULE}</option>
                          <option value="module">{'mt_module'|getTranslatedString:$MODULE}</option>
                          <option value="url">{'mt_url'|getTranslatedString:$MODULE}</option>
                          <option value="sep">{'mt_separator'|getTranslatedString:$MODULE}</option>
                      </select>
                  </div>
              </div>
          </div>
          <div class="slds-form-element" id="labelForm">
              <label class="slds-form-element__label" for="mlabel">Label</label>
              <div class="slds-form-element__control">
                  <input id="mlabel" class="slds-input" type="text" name="mlabel"/>
              </div>
          </div>
          <div class="slds-form-element" id="actionForm">
              <label class="slds-form-element__label" for="mvalue">Action</label>
              <div class="slds-form-element__control">
                  <input name="mvalue" id="mvalue" class="slds-input" type="text"/>
              </div>
          </div>
          <div class="slds-form-element hide" id="moduleForm">
              <label class="slds-form-element__label" for="modname">Action</label>
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
          <label class="slds-form-element__label" for="mpermission">Permissions</label>
          {html_options name="mpermission[]" id="mpermission" multiple="multiple" options=$PROFILES}
          </br>

              <button class="slds-button slds-button--brand" onclick="VtigerJS_DialogBox.block();processTree('doAdd');">{'LBL_ADD_BUTTON'|getTranslatedString:$MODULE}</button>
              <button class="slds-button slds-button--brand" onclick="VtigerJS_DialogBox.block();processTree('doUpd');">{'LBL_UPDATE'|getTranslatedString:$MODULE}</button>
              <button class="slds-button slds-button--destructive" onclick="VtigerJS_DialogBox.block();processTree('doDel');">{'LBL_DELETE_BUTTON'|getTranslatedString:$MODULE}</button>
              <button class="slds-button slds-button--neutral" onclick="clearForm();">{'LBL_CLEAR_BUTTON_LABEL'|getTranslatedString:$MODULE}</button>


      </div>



          {*<input type="submit" onclick="VtigerJS_DialogBox.block();processTree('doAdd');" class="crmbutton small create" value="{'LBL_ADD_BUTTON'|getTranslatedString:$MODULE}" name="menuadd">*}
          {*<input type="submit" onclick="VtigerJS_DialogBox.block();processTree('doUpd');" class="crmbutton small create" value="{'LBL_UPDATE'|getTranslatedString:$MODULE}" name="menuupd">*}
          {*<input type="submit" onclick="VtigerJS_DialogBox.block();processTree('doDel');" class="crmbutton small delete" value="{'LBL_DELETE_BUTTON'|getTranslatedString:$MODULE}" name="menudel">*}
          {*<input type="button" onclick="clearForm();" class="crmbutton small cancel" value=" {'LBL_CLEAR_BUTTON_LABEL'|getTranslatedString:$MODULE} " name="menucls">*}







        {*<table width="100%" cellspacing="1" cellpadding="3" border="0" class="small" style="margin-top: 20px; border: 1px solid #eeeeee;background-color:#eaeaea;">*}
          {*<tbody>*}
          {*<tr><td width="200px" class="lvtCol">{'MenuType'|getTranslatedString:$MODULE}</td>*}
            {*<td><select name="mtype" id="mtype">*}
                {*<option value="menu">{'mt_menu'|getTranslatedString:$MODULE}</option>*}
                {*<option value="module">{'mt_module'|getTranslatedString:$MODULE}</option>*}
                {*<option value="url">{'mt_url'|getTranslatedString:$MODULE}</option>*}
                {*<option value="sep">{'mt_separator'|getTranslatedString:$MODULE}</option>*}
              {*</select>*}
            {*</td></tr>*}
          {*<tr><td width="200px" class="lvtCol">{'MenuLabel'|getTranslatedString:$MODULE}</td>*}
            {*<td><input type="text" value="" name="mlabel" id="mlabel"></td></tr>*}
          {*<tr><td width="200px" class="lvtCol">{'MenuValue'|getTranslatedString:$MODULE}</td>*}
            {*<td><span id="hidemvalue"><input type="text" value="" name="mvalue" id="mvalue"></span><span id="hidemodname">{html_options name="modname" id="modname" options=$MODNAMES}</span></td></tr>*}
          {*<tr><td width="200px" class="lvtCol">{'MenuPermission'|getTranslatedString:$MODULE}</td>*}
            {*<td>{html_options name="mpermission[]" id="mpermission" multiple="multiple" options=$PROFILES}*}
            {*</td></tr>*}
          {*<tr><td colspan="2">*}
            {*</td></tr>*}

          {*</tbody></table>*}
      {*</form>*}
      {*<br/><br/>*}
      {*<table border=0 width=90% align=center>*}
        {*<tr>*}
          {*<td colspan=3><H2>coreBOS Menu Editor by <span style="color: #4c9316">JPL TSolucio</span></H2></td>*}
        {*</tr>*}
        {*<tr>*}
          {*<td colspan=3><ul>*}
              {*<li>{'MarkVisibleAndSort'|getTranslatedString:$MODULE}</li>*}
              {*<li>{'SelectToEditDelete'|getTranslatedString:$MODULE}</li>*}
              {*<li>{'AddNewItem'|getTranslatedString:$MODULE}</li>*}
              {*<li>{'MenuItemPermissions'|getTranslatedString:$MODULE}</li>*}
            {*</ul></td>*}
        {*</tr>*}
        {*<tr valign=top>*}
          {*<td width=45%><span style="font-size:large"><p>This extension permits you to manage complex menu structures inside coreBOS using <a href="http://www.kendoui.com" target="_blank">KendoUI</a> menu web widget.</p>*}
{*<br />*}
{*Thank you for your support.<br/>*}
{*<span style="color: #4c9316">TSolucio</span>*}
          {*</td>*}
          {*<td>&nbsp;</td>*}
          {*<td width=45%><p align=center><a href="http://www.tsolucio.com" target="_blank"><img src="modules/{$MODULE}/tsolucio_block.jpg" border="0" align=center/></a></p><br /></td>*}
        {*</tr>*}
        {*<tr><td colspan="3"><span style="color: #4c9316"><a href="http://www.tsolucio.com" target="_blank">TSOLUCIO</a></span> is an IT Consulting company (Internet, Decision Support Systems, Sales Force Automation, Data Security and Protection, Business Process Continuity). Our clients are growing companies wishing to use new technologies to improve their businesses and achieve long term success.</td></tr>*}
      {*</table>*}
    </div>
  </div>
</div>
