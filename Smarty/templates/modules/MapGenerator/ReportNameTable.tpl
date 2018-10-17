<script type="text/javascript" src="modules/MapGenerator/js/script.js"></script>
<br><br><br>

<div  name="reportContainer2" id ="reportContainer2">
<form name="tabelascript2" id="tabelascript2" onsubmit="generateReportTable2('reportTable');return false;">
<input  type="hidden" id="accinsdata2"  name="accinsdata2" value=""/>
<input  type="hidden" id="accinsmodule2"  name="accinsmodule2"/>
<input  type="hidden" id="nr"  name="nr" value="1"/>
<input  type="hidden" id="count"  name="count" value="0"/>
<table>
    <tr>
        <td><label><b>Nome tabella </b></label> </td>
        <td><input class="k-input"  type="text" value="" name="tablename2"  id="tablename2"> </td>
    </tr>
<tr>
<td><label><b>Installation</b></label></td>
<td><select  id="clientinstallations2" name="clientinstallations2">
{section name=clientdata loop=$INSTALLATIONS}
  <option value="{$INSTALLATIONS[clientdata].accountinstallationid}-{$INSTALLATIONS[clientdata].acin_no}{$INSTALLATIONS[clientdata].dbname}">{$INSTALLATIONS[clientdata].acinstallationname}</option>
{/section}
</select>
</td>
</tr>
<tr>
<td><label><b>Report</b></label></td>
<td><select  id="clientreport2" style="margin-right: 30px;" name="clientreport2"></select></td>
</tr>
</table>
<table width="85%"  class="small" cellspacing="1" border="0" cellpadding="0" id='fieldTab2'>
</table>
    <center>
        <input type="submit" name="button2" id="button2" style="margin-right: 60px;"  value="{$MOD.CREATETABLE}" >
    </center>
        <div id="screated"> </div>
             <div id="dialog-message" title="Report created" style = "display:none">
      <p>
        <span class="ui-icon ui-icon-circle-check" style="float:left; margin:0 7px 50px 0;"></span>
         Your report was successfully created!
      </p>
    </div>
       </form>
</div>

{literal}
<style>
   #tabForm{
       position: absolute;
       bottom: 2px;
       right: 2px;
    }
   select { width: 300px; }
  .overflow { height: 200px; }
  td{
      width:170px;
      padding: 10px;
  }
</style>
<script>
     jQuery("#button2").button()
     jQuery("#clientinstallations2")
     .selectmenu({change: function( event, ui ) { 
                                                  var dataDb = ui.item.value.split("-");
                                                   installationID = dataDb[0];
                                                   nameDb = dataDb[1];
                                                   populateReport("clientreport2");
                                               }})
     .selectmenu("menuWidget" )
     .addClass( "overflow" );
     jQuery( "#clientreport2")
     .selectmenu({change: function( event, ui ) { 
                                               reportId = ui.item.value;
                                               choose_fields(reportId,'fieldTab2');
                                               }})
     .selectmenu("menuWidget" )
     .addClass( "overflow" );  
</script>
{/literal}