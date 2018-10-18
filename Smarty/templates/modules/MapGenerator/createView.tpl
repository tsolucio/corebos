{*<script type="text/javascript" src="include/js/jquery-multiselect.js"></script>
<script type="text/javascript" src="include/js/jquery-multiselect-filter.js"></script>
<link rel="stylesheet" type="text/css" href="include/styles/jquery-multiselect.css"/>
<link rel="stylesheet" type="text/css" href="include/styles/jquery-multiselect-filter.css"/>*}
<div id="firstStep">
<center>
<div id='divView' >
<div class="slds-text-title" id='labelNameView' style="float: left; overflow:hidden;"><h3 class="slds-section-title--divider">{$MOD.NameView}:</h3></div>
{*<div class='allinea' type='text' id='nameViewDiv'><input id='nameView' name='nameView'></div>*}
<div class="slds-form-element__control allinea" id='nameViewDiv'>
  <div class="slds-form-element"  style="width:100%;height:100%; ">
            <div id="SelectedTypeOfMap" class="slds-form-element__control">
                <div class="slds-select_container">
                    <select id="GetTypeOfMap" class="slds-select">
                    <option value="">{$MOD.ChooseTypeOfMap}</option>
                    <option value="MaterializedView">{$MOD.MaterializedView}</option>
                    <option value="Script">{$MOD.Script}</option>
                    <option value="Map">{$MOD.Map}</option>
                    </select>
                </div>
            </div>
        </div>
   {* <input type="text" id="nameView" class="slds-input" name='nameView' placeholder="{$MOD.addviewname}" />*}
  </div>
</div>
</center>
&nbsp;&nbsp;&nbsp;&nbsp;
<center>
<div class='selDataBase' id='selDb' style="padding-left: 1%;">
{*    <div>
            <input type="hidden" id='accid' name="accid" value="" >
                <label class="font-style">Installation: </label>  <select id="dbList" name="dbList" onchange="connect(this.id,this.value);" multiple class="singlecombofilter">
                 {foreach from=$allacc key="k" item="c"}
                     <option value="{$k}">{$c}</option>
                 {/foreach}
            </select>
    </div>*}
{* remuve the choose db from comboox
<select  id="dbList" name="dbList">
{section name=instdata loop=$INSTALLATIONS}
  <option value="{$INSTALLATIONS[instdata].accountinstallationid}-{$INSTALLATIONS[instdata].acin_no}{$INSTALLATIONS[instdata].dbname}">{$INSTALLATIONS[instdata].acinstallationname}</option>
{/section}
</select>
*}
</div>
</center>
<div id='tabForm'>
<button class="slds-button slds-button--neutral" id='sendTab' onclick='openMenuJoin2()'>Next</button>
{*<button class='pulsante' id='sendTab' onclick='openMenuJoin2()'>Next</button>*}
</div>
</div>
<input type="hidden" name="dbName" id="dbName" value="">
<div id="content">
</div>
{literal}
    <script>
  //      jQuery("#dbList").multiselect({
  //      multiple: false,
 //       header: true,
 //       noneSelectedText: "Select an Option",
//        selectedList: 1,
//}).multiselectfilter();
    </script>
    <style>
   #tabForm{
       position: absolute;
       bottom: 2px;
       right: 2px;
    }
   select { width: 300px; }
  .overflow { height: 200px; }
    </style>
     <script>
      function connect(id,value) {
           var allelems = value.split("-");
            jQuery('#accid').val(allelems[0]);
            installationID = allelems[0];
            nameDb = allelems[2];
             $("#dbName").val(nameDb);
        }
     jQuery( "#dbList")
     .selectmenu({change: function( event, ui ) {
                                                 var dataDb = ui.item.value.split("-");
                                                   installationID = dataDb[0];
                                                   nameDb = dataDb[1];
                                                  $("#dbName").val(nameDb);}})
     .selectmenu("menuWidget" )
      .addClass( "overflow" );
    </script>
{/literal}    