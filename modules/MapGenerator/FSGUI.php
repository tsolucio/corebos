<script>

jQuery( document ).ready(function() {

var reportdataSource = new kendo.data.DataSource({
  serverFiltering: true,
  transport: {
    read: {
      url: "index.php?module=MapGenerator&action=MapGeneratorAjax&file=populateReport",
      dataType: "json",
    }
  }
});

// jQuery("#report").kendoDropDownList({
//              autoBind: false,
//              optionLabel: "Select Report...",
//              dataTextField: "reportValue",
//              dataValueField: "reportId",
//              dataSource:reportdataSource
//}).data("kendoDropDownList");

var report = jQuery("#report").kendoDropDownList({
    autoBind: false,
    cascadeFrom: "report",
    optionLabel: "Select group...",
    dataTextField: "groupValue",
    dataValueField: "groupId",
    dataSource: {
         transport: {
           read: {
               url:"index.php?module=MapGenerator&action=MapGeneratorAjax&file=populateReport",
               dataType: "json",
           }
         },
       serverFiltering: true,
    }
}).data("kendoDropDownList");
});
</script>
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
$data =  '<br> <br><br> <br>';
$data.='<div  name="crea" id ="crea">';
$data.='Nome tabella  <input class="k-input" style="margin-right: 30px;" type="text" value="" name="nometab"  id="nometab"> ';
$data.='Report <input  id="report" style="margin-right: 30px;" name="report"/>';
$data.='Groupby <input  id="groupby" style="margin-right: 30px;" name="groupby"/>';
$data.='<input type="button" name="createRaport" id="createRaport" style="margin-right: 30px;" onclick="createRaprtTable();" class="k-button" value="Crea tabella" class="crmbutton edit small">';
$data.='<div id="screated"> </div>';
$data.='</div>';
echo $data;
?>