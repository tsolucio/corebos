<script type="text/javascript" src="modules/MapGenerator/jquery/script.js"></script>
<br><br><br>

<div  name="crea" id ="crea">
{*Account <input  id="acc" style="margin-right: 30px;" name="acc"/>
Account Installation  <input  id="accins" style="margin-right: 30px;" name="accins"/>*}
<input  type="hidden" id="accinsdata"  name="accinsdata" value=""/>
<input  type="hidden" id="accinsmodule"  name="accinsmodule"/>
<table>
    <tr>
        <td><label><b>Nome tabella </b></label> </td>
        <td><input class="k-input"  type="text" value="" name="nometab"  id="nometab"> </td>
    </tr>
<tr>
<td><label><b>Installation</b></label></td>
<td><select  id="installations" name="installations">
{section name=instdata loop=$INSTALLATIONS}
  <option value="{$INSTALLATIONS[instdata].accountinstallationid}-{$INSTALLATIONS[instdata].acin_no}{$INSTALLATIONS[instdata].dbname}">{$INSTALLATIONS[instdata].acinstallationname}</option>
{/section}
</select>
</td>
</tr>
<tr>
<td><label><b>Report</b></label></td>
<td><select  id="report" style="margin-right: 30px;" name="report"></select></td>
</tr>
<tr>
<td><label><b>Script </b></label></td>
<td>
<select  style="margin-right: 30px;"  name="scriptsel"  id="scriptsel"> 
    <option value='0'>--None--</option>
    <option value='1'>{$MOD.FS}</option>
    <option value='2'>{$MOD.Unidata}</option>
    <option value='3'>{$MOD.FS} GroupByTextAnalysis</option>
    <option value='4'>{$MOD.FS} Special</option>
</select>
</td>
</tr>
<tr id="installmodules" style="display:none;">
    <td>
        <b><label id="modulelabel" >Module</label></b>
    </td>
 <td><select  style="margin-right: 30px;"   name="modscriptsel"  id="modscriptsel"> </select>
 </td>
</tr>
</table>
    <center>
        <input type="button" name="createRaport" id="createRaport" style="margin-right: 60px;" onclick="createRaprtTable();"  value="{$MOD.CREATETABLE}" >
    </center>
        <div id="screated"> </div>
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
     jQuery("#createRaport").button();
     jQuery( "#installations")
     .selectmenu({change: function( event, ui ) { 
                                                  var dataDb = ui.item.value.split("-");
                                                   installationID = dataDb[0];
                                                   nameDb = dataDb[1];
                                                   populateReport("report");
                                                   dispalyModules();
                                               }})
     .selectmenu("menuWidget" )
     .addClass( "overflow" );
     jQuery( "#report")
     .selectmenu()
     .selectmenu("menuWidget" )
     .addClass( "overflow" );

      jQuery( "#scriptsel")
     .selectmenu({change: function( event, ui ) { 
                                                   getInstallationModules(ui.item.value);
                                               }})
     .selectmenu("menuWidget" )
     .addClass( "overflow" );
     
      jQuery( "#modscriptsel")
     .selectmenu({change: function( event, ui ) { console.log(ui.item);
                                                   jQuery("#accinsmodule").val(ui.item.label);
                                               }})
     .selectmenu("menuWidget" )
     .addClass( "overflow" );
     
</script>
{/literal}