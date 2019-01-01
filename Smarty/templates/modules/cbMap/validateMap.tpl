<div>
<tr class="actionlink">
   <td align="left" style="padding-left:10px;">
      <img id="CheckIntegrity_img_id" src="{'yes.gif'|@vtiger_imageurl:$THEME}" alt="Validate this map"
         hspace="5" align="absmiddle" border="0"/>
      <a href="javascript:;" onClick="validateMap('{$ID}');">{$MOD.VALIDATE_MAP}</a>&nbsp;
	  <span id="vtbusy_validate_info" style="display:none;">
		<img src="{'vtbusy.gif'|@vtiger_imageurl:$THEME}" border="0"></span>
      <div id="map_error" style="display:none">
         </br>
         <div align="center" class="slds-popover_small role="dialog">
            <div class="slds-popover__header slds-theme_error" >
               <h3>{$MOD.MAP_NOT_VALID}</h3>
            </div>
            <div id="map_validation_message" class="slds-popover__body"></div>
         </div>
      </div>
      <div id="map_valid" style="display:none">
         </br>
         <div align="center" class="slds-popover_small slds-theme_success" role="dialog">
            <div id="map_validation_message" class="slds-popover__body">{$MOD.MAP_VALID}</div>
         </div>
      </div>
      <div id="map_not_implemented_yet" style="display:none">
         </br>
         <div align="center" class="slds-popover_small slds-theme_warning" role="dialog">
            <div id="map_validation_message" class="slds-popover__body">{$MOD.MAP_NOT_IMPLEMENTED_YET}</div>
         </div>
      </div>
   </td>
</tr>
</div>
