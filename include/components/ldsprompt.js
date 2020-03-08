const ldsPrompt = {
	show : (headText, content) => {
		let modal = `<section role="alertdialog" tabindex="0" class="slds-modal slds-fade-in-open slds-modal_prompt" aria-modal="true">
		<div class="slds-modal__container">
			<header class="slds-modal__header slds-theme_error slds-theme_alert-texture">
				<button class="slds-button slds-button_icon slds-modal__close slds-button_icon-inverse" title="Close"
						onClick="ldsPrompt.close();">
					<svg class="slds-button__icon slds-button__icon_large" aria-hidden="true">
						<use xlink: href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#close"></use>
					</svg>
					<span class="slds-assistive-text">Close</span>
				</button>
			<h2 class="slds-text-heading_medium" id="prompt-heading-id">${headText}</h2>
			</header>
			<div class="slds-modal__content slds-p-around_medium" id="prompt-message-wrapper">
				<p>${content}</p>
			</div>
			<footer class="slds-modal__footer slds-theme_default" style="width: 100%;">
				<button class="slds-button slds-button_neutral" onClick="ldsPrompt.close();">Okay</button>
			</footer>
		</div>
	</section >
	<div class="slds-backdrop slds-backdrop_open"></div>`;
		ldsPrompt.insert(modal);
	},
	insert : (html) => {
		if (ldsPrompt.active === true) {
			console.error('Can\'t show two prompts, close the first one first');
		} else {
			let modalContainer = document.createElement('DIV');
			modalContainer.id = 'global-prompt-container';
			modalContainer.innerHTML = html;
			document.body.appendChild(modalContainer);
			ldsPrompt.active = true;
		}
	},
	close : () => {
		let modalContainer = document.getElementById('global-prompt-container');
		document.body.removeChild(modalContainer);
		ldsPrompt.active = false;
	},
	active: false
};