<div class="slds new-map-modal">
	<div class="slds-modal" aria-hidden="false" role="dialog" id="modal">
		<div class="slds-modal__container">
			<div class="slds-modal__header">
				<button class="slds-button slds-button--icon-inverse slds-modal__close" data-modal-saveas-close="true">
					<svg aria-hidden="true" class="slds-button__icon slds-button__icon--large">
						<use xlink:href="include/LD//assets/icons/action-sprite/svg/symbols.svg#close"></use>
					</svg>
					<span class="slds-assistive-text">{$MOD.close}</span>
				</button>
				<h2 class="slds-text-heading--medium">{$MOD.HeadOfModalSaveAs}</h2>
			</div>
			<div class="slds-modal__content slds-p-around--medium">
				<div class="slds-form-element">
					<label class="slds-form-element__label" for="SaveasMapText">{$MOD.MapName}</label>
					<input type="text" id="SaveasMapText" name="nameView" required="" class="slds-input" placeholder="{$MOD.mapname}" data-controll="true" data-controll-idlabel="ErrorLabelModal" data-controll-file="MapGenerator,CheckNameOfMap" data-controll-id-relation="SendDataButton">
				</div>
				<label id="ErrorLabelModal" class="slds-form-element__label slds-required">{$MOD.requiredstring}</label>
			</div>
			<div class="slds-modal__footer">
				<button data-send="true" data-send-url="{$Datas}" data-send-data-id="{$dataid},SaveasMapText" id="SendDataButton" data-save-history="true" data-save-history-show-id="LoadHistoryPopup" data-save-history-show-id-relation="LoadShowPopup" data-send-savehistory-functionname="{$anotherfunction}" data-loading="true" data-loading-divid="LoadingDivId" disabled class="slds-button slds-button--neutral slds-button--brand">
					{$MOD.save}
				</button>
				<button class="slds-button slds-button--neutral" data-modal-saveas-close="true">{$MOD.cancel}
				</button>
				<!-- data-send-savehistory="{$savehistory}" -->
			</div>
		</div>
	</div>
	<div class="slds-backdrop" id="backdrop"></div>
</div>