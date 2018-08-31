<script type="text/javascript" src="modules/MapGenerator/jquery/script.js"></script>
<br><br><br>

<div  name="reportContainer" id ="reportContainer">
<form name="tabelascript" id="tabelascript" onsubmit="generateReportTable('reportTable');return false;">
<input  type="hidden" id="accinsdata"  name="accinsdata" value=""/>
<input  type="hidden" id="accinsmodule"  name="accinsmodule"/>
<input  type="hidden" id="nr"  name="nr" value="0"/>
<input  type="hidden" id="count"  name="count" value="0"/>
<table>
    <tr>
        <td><label><b>Nome tabella </b></label> </td>
        <td><input class="k-input"  type="text" value="" name="tablename"  id="tablename"> </td>
    </tr>
<tr>
<td><label><b>Installation</b></label></td>
<td><select  id="clientinstallations" name="clientinstallations">
{section name=clientdata loop=$INSTALLATIONS}
  <option value="{$INSTALLATIONS[clientdata].accountinstallationid}-{$INSTALLATIONS[clientdata].acin_no}{$INSTALLATIONS[clientdata].dbname}">{$INSTALLATIONS[clientdata].acinstallationname}</option>
{/section}
</select>
</td>
</tr>
<tr>
<td><label><b>Report</b></label></td>
<td><select  id="clientreport" style="margin-right: 30px;" name="clientreport"></select></td>
</tr>
</table>
<table width="85%"  class="small" cellspacing="1" border="0" cellpadding="0" id='fieldTab'>

</table>
    <center>
        <input type="submit" name="button1" id="button1" style="margin-right: 60px;"  value="{$MOD.CREATETABLE}" >
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
     jQuery("#button1").button()
     jQuery("#clientinstallations")
     .selectmenu({change: function( event, ui ) { 
                                                  var dataDb = ui.item.value.split("-");
                                                   installationID = dataDb[0];
                                                   nameDb = dataDb[1];
                                                   populateReport("clientreport");
                                               }})
     .selectmenu("menuWidget" )
     .addClass( "overflow" );
     jQuery( "#clientreport")
     .selectmenu({change: function( event, ui ) { 
                                               reportId = ui.item.value;
                                               choose_fields(reportId,'fieldTab');
                                               }})
     .selectmenu("menuWidget" )
     .addClass( "overflow" );  
</script>
{/literal}