<!-- MassEdit Feature -->
<div id="massedit" class="layerPopup" style="display:none;width:80%;z-index:100;">
<table width="100%" border="0" cellpadding="3" cellspacing="0" class="layerHeadingULine">
<tr>
	<td class="layerPopupHeading" align="left" width="60%">{$APP.LBL_MASSEDIT_FORM_HEADER}</td>
	<td>&nbsp;</td>
	<td align="right" class="cblds-t-align_right" width="40%">
	<img onClick="fninvsh('massedit');" title="{$APP.LBL_CLOSE}" alt="{$APP.LBL_CLOSE}" style="cursor:pointer;" src="{'close.gif'|@vtiger_imageurl:$THEME}" align="absmiddle" border="0">
	</td>
</tr>
</table>
<div id="massedit_form_div"></div>
</div>

<div id="relresultssection" style="visibility:hidden;display:none;" class="slds-masseditprogress">
<div class="slds-grid">
<div class="slds-col">
	<div class="slds-page-header" role="banner">
		<div class="slds-col slds-has-flexi-truncate">
			<div class="slds-media slds-no-space slds-grow">
				<div class="slds-media__figure">
					<svg aria-hidden="true" class="slds-icon slds-icon-standard-user">
						<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#relate"></use>
					</svg>
				</div>
				<div class="slds-media__body">
					<h1 class="slds-page-header__title slds-m-right_small slds-align-middle slds-truncate" title="{$APP.Updated}">{$APP.Updated}...</h1>
				</div>
			</div>
		</div>
	</div>
</div>
<div class="slds-col slds-page-header" style="width:50%;">
<progress id='progressor' value="0" max='100' style="width:90%;height:14px;"></progress>
<span id="percentage" style="text-align:left; display:block; margin-top:5px;">0</span>
</div>
<div class="slds-col slds-page-header slds-p-top_small" style="width:10%;">
	<div class="slds-icon_container slds-icon_container_circle slds-p-around_xx-small slds-icon-action-close">
		<svg class="slds-icon slds-icon_xx-small" aria-hidden="true" onClick="fninvsh('relresultssection');">
			<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#close"></use>
		</svg>
		<span class="slds-assistive-text">{$APP.LBL_CLOSE}</span>
	</div>
</div>
</div>
<div class="slds-grid">
<div class="slds-col">
<div id="relresults" style="border:1px solid #000; padding:10px; width:90%; height:450px; overflow:auto; background:#eee; margin:auto; margin-top:10px;"></div>
</div>
</div>
</div>