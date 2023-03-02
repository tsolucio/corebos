<div style="height: 500px">
	<div class="slds-text-heading_large slds-align_absolute-center">
		<img class="slds-m-bottom_large slds-m-top_large" src="{$COMPANY_DETAILS.applogo}">
	</div>
	<div class="slds-text-heading_large slds-align_absolute-center">
		<p>{'MSG_THANK_YOU'|@getTranslatedString}!</p>
	</div>
	<div class="slds-text-heading_small slds-align_absolute-center">
		<p>{'LBL_WIZARD_COMPLETED'|@getTranslatedString}</p>
	</div>
	<div class="slds-text-heading_small slds-align_absolute-center">
		<button type="button" class="slds-button slds-button_brand slds-path__mark-complete slds-float_right slds-m-top_large" onclick="wizard.Finish()">
			<svg class="slds-button__icon slds-button__icon_left" aria-hidden="true">
				<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#save"></use>
			</svg>
			{'LBL_FINISH'|@getTranslatedString}
		</button>
	</div>
</div>