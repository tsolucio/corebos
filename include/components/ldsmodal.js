const ldsModalClosing = new Event('closemodal');
const ldsModal = {
	show : (headText, content, size = 'medium', saveAction = '', cancelButtonText = '') => {
		cancelButtonText = cancelButtonText === '' ? alert_arr.JSLBL_CANCEL : cancelButtonText;
		let sact = (saveAction!==false && saveAction!='') ? `<button class="slds-button slds-button_brand" onclick="${saveAction}">${alert_arr.JSLBL_SAVE}</button>` : '';
		let modal = `<section role="dialog" tabindex="-1" class="slds-modal slds-fade-in-open slds-modal_${size}" aria-modal="true">
			<div class="slds-modal__container">
				<header class="slds-modal__header">
					<button class="slds-button slds-button_icon slds-modal__close slds-button_icon-inverse"
							title="${alert_arr.LBL_CLOSE_TITLE}"
							onClick="javascript:ldsModal.close()"
						>
						<svg class="slds-button__icon slds-button__icon_large" aria-hidden="true">
							<use xlink: href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#close"></use>
						</svg>
						<span class="slds-assistive-text">${alert_arr.LBL_CLOSE_TITLE}</span>
					</button>
					<h2 class="slds-modal__title slds-hyphenate slds-text-heading_medium" 
						id="global-modal-container__title">
						${headText}
					</h2>
				</header>
				<div class="slds-modal__content slds-p-around_medium" id="global-modal-container__content">
					${content}
				</div>
				<footer class="slds-modal__footer" style="width: 100%;">
					<button class="slds-button slds-button_neutral" onClick="javascript:ldsModal.close()">${cancelButtonText}</button>
					${sact}
				</footer>
			</div>
		</section >
		<div class="slds-backdrop slds-backdrop_open"></div>`;
		ldsModal.insert(modal);
	},
	insert : (html) => {
		if (ldsModal.active === true) {
			console.error('Can\'t show two modals, close the first one first');
		} else {
			let modalContainer = document.createElement('DIV');
			modalContainer.id = 'global-modal-container';
			modalContainer.innerHTML = html;
			document.body.appendChild(modalContainer);
			ldsModal.active = true;
			vtlib_executeJavascriptInElement(document.getElementById('global-modal-container'));
		}
	},
	close : () => {
		let modalContainer = document.getElementById('global-modal-container');
		modalContainer.dispatchEvent(ldsModalClosing);
		document.body.removeChild(modalContainer);
		ldsModal.active = false;
	},
	updateTitle : (title) => {
		document.getElementById('global-modal-container__title').innerHTML = title;
	},
	active: false
};
