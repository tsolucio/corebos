<link rel="stylesheet" href="modules/MapGenerator/css/style.css" />
<script src="modules/MapGenerator/functions.js"></script>
<script src="modules/DemoBuilder/js/jquery-1.7.1.js" type="text/javascript"></script>
<script type="text/javascript">jQuery.noConflict();</script>
<script src="modules/DemoBuilder/js/kendo.web.min.js" type="text/javascript"></script>
<link href="modules/DemoBuilder/styles/kendo.common.min.css" rel="stylesheet" />
<link href="modules/DemoBuilder/styles/kendo.default.min.css" rel="stylesheet" />

<script>
jQuery(document).ready(function() {
function onSelect(e) {
var dataItem = this.dataItem(e.item.index());
jQuery("#accinsdata").val(dataItem.accInstId);
}

function setModule(e){
var dataItem = this.dataItem(e.item.index());
jQuery("#accinsmodule").val(dataItem.name); 
}

function getInstallationModules(e){
var dataItem = this.dataItem(e.item.index());
var installation = jQuery("#accinsdata").val(); 
    if(dataItem.value == 1){
    if(jQuery("#installmodules").is(":visible")) {
       jQuery("#modscriptsel").data("kendoDropDownList").dataSource.read(); 
    }
    else {
    jQuery("#installmodules").show();
    jQuery("#modscriptsel").kendoDropDownList({
              autoBind: false,
              optionLabel: "Select Module",
              dataTextField: "name",
              dataValueField: "tabid",
              dataSource: {
                 transport: {
                 read: {
                    url:"index.php?module=MapGenerator&action=MapGeneratorAjax&file=getInstallationEntities&installation="+installation,
                    dataType: "json",
                }
                },
               serverFiltering: true,
    },
    select: setModule,
}).data("kendoDropDownList");
  }
}
else {
     jQuery("#installmodules").hide();
}
}
var accdataSource = new kendo.data.DataSource({
  serverFiltering: true,
  transport: {
    read: {
      url: "index.php?module=MapGenerator&action=MapGeneratorAjax&file=getAccount",
      dataType: "json"
    }
  }
});
var acc = jQuery("#acc").kendoDropDownList({
              autoBind: false,
              optionLabel: "Select Acc...",
              dataTextField: "accountValue",
              dataValueField: "accountId",
              dataSource:accdataSource
}).data("kendoDropDownList");
var accins = jQuery("#accins").kendoDropDownList({
    autoBind: false,
    cascadeFrom: "acc",
    optionLabel: "Select installation...",
    dataTextField: "accInstValue",
    dataValueField: "accInstId",
    dataSource: {
         transport: {
           read: {
               url:"index.php?module=MapGenerator&action=MapGeneratorAjax&file=getAccountInstallation",
               dataType: "json",
           }
         },
       serverFiltering: true,
    },
    select:onSelect
}).data("kendoDropDownList");
var report = jQuery("#report").kendoDropDownList({
    autoBind: false,
    cascadeFrom: "accins",
    optionLabel: "Select report...",
    dataTextField: "reportValue",
    dataValueField: "reportId",
    dataSource: {
         transport: {
           read: {
}).data("kendoDropDownList");
var data = [
              { text: "FS", value: "1" },
              { text: "Unidata", value: "2" }
            ];

jQuery("#scriptsel").kendoDropDownList({
              autoBind: false,
              optionLabel: "Select Script",
              dataTextField: "text",
              dataValueField: "value",
              dataSource:data,
              select:getInstallationModules
}).data("kendoDropDownList");
});
</script>
</head>
<body>
<div id="mvcontainer">
     <div id="mvmenu">
        <div id="mvtitle">Creatore di viste materializzate</div>
        <div id="pulsantiMenù">
                <button style="width:19%;" class="pulsante" onclick="openMenuCreaView();">Creazione nuova vista</button>
                <button style="width:19%;" class="pulsante" onclick="openMenuManage();">Gestione viste</button>
                <button style="width:19%;" class="pulsante" onclick="window.open('index.php?module=MapGenerator&action=MapGeneratorAjax&file=tabellascript&nr=0','CREA SCRIPT','width=640,height=602,resizable=0,scrollbars=1');">Creazione script report</button>
<!--                <button style="width:19%;" class="pulsante" onclick="createReportScript(0);">Creazione script report</button>
                <button style="width:19%;" class="pulsante" onclick="createReportScript(1);">Creazione script Name report</button>-->
                <button style="width:19%;" class="pulsante" onclick="window.open('index.php?module=MapGenerator&action=MapGeneratorAjax&file=tabellascript&nr=1','CREA SCRIPT','width=640,height=602,resizable=0,scrollbars=1');">Creazione script Name report</button>
                <button style="width:19%;" class="pulsante" onclick="createFSScript();">Creazione script FS</button>
        </div>
        </div>
<div id="content" > </div>
<br> <br><br> <br>
<div  name="crea" id ="crea" style ="display:none">
Account <input  id="acc" style="margin-right: 30px;" name="acc"/>
Account Installation  <input  id="accins" style="margin-right: 30px;" name="accins"/>
<input  type="hidden" id="accinsdata"  name="accinsdata"/>
<input  type="hidden" id="accinsmodule"  name="accinsmodule"/>
Report <input  id="report" style="margin-right: 30px;" name="report"/>
Nome tabella  <input class="k-input" style="margin-right: 30px;" type="text" value="" name="nometab"  id="nometab"> 
<br> <br> <br> <br>
Script <input  style="margin-right: 30px;" type="text" value="" name="scriptsel"  id="scriptsel"> 
<div id="installmodules" style="display:none;">
 Module<input  style="margin-right: 30px;" type="text" value="" name="modscriptsel"  id="modscriptsel"> 
</div>
<input type="button" name="createRaport" id="createRaport" style="margin-right: 60px;" onclick="createRaprtTable();" class="k-button" value="Crea tabella" class="crmbutton edit small">
<div id="screated"> </div>
</div>
<div name="creaRp" id="creaRp" style ="display:none"> 
  <form id="ajaxform" name="ajaxform" method="post" onsubmit=" submitForm();return false;">
 <br><br><br><br>
Nome cliente  <select style="margin-right: 30px;" class="small" style='width:30%'  id="mod1" name="mod1" onchange="sellist1();" >
<?php
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
*************************************************************************************************/
    global $adb;
    $dbname = 'crm_evolutivoservice';
    $q = "select * from vtiger_accountinstallation ";
    $res = $adb->query("select * from vtiger_accountinstallation ");
    echo $adb->query_result($res,1,"acinstallationname");
    global $log;
     for($i=0;$i<$adb->num_rows($res);$i++){
        $name=$adb->query_result($res,$i,"acinstallationname");
        $id=$adb->query_result($res,$i,"accountinstallationid");
        $path=explode("/",$adb->query_result($res,$i,"vtigerpath"));
        $n=count($path);
        $a.='<option value="'.$id.'">'.$name.'</option>';
    }
    echo $a;
?>
</select>
  Report <select class="small" style='width:30%'  style="margin-right: 40px;" id="groupby1" name="groupby1" onchange="choose_fields3();" ></select>
  Nome tabella  <input class="small" type="text" value=""  name="nometab1" id="nometab1">
  <input type="hidden" name="count" id="count">
  <input type="hidden" id="nr" name="nr">
  <br><br><br>
 <table width="85%"  class="small" cellspacing="1" border="0" cellpadding="0" id ="fieldTab">
 
 </table>
 <input type="submit" name="button1" id="button1" value="Crea tabella" class="crmbutton edit small">    
    </form>
 </div>
</div>
