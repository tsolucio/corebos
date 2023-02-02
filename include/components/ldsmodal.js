const ldsModalClosing = new Event('closemodal');
const ldsModal = {
	show : (headText, content, size = 'medium', saveAction = '', cancelButtonText = '', destroy = true, drawButtonBool = false, drawButtonText = '', doc2edit = '') => {
		cancelButtonText = cancelButtonText === '' ? alert_arr.JSLBL_CANCEL : cancelButtonText;
		drawButtonText = drawButtonText === '' ? alert_arr.JSLBL_DRAW : drawButtonText;
		let drawButton = (drawButtonBool !==false && doc2edit != '') ? `<button class="slds-button slds-button_neutral" id="drawOnImage" onClick="javascript:window.open('index.php?module=Utilities&action=UtilitiesAjax&file=Paint2Document&formodule=Contacts&forrecord=1084&inwindow=1&doc2edit=${doc2edit}','photo2doc','width=800,height=860')">${drawButtonText}</button>`:``;
		let sact = (saveAction!==false && saveAction!='') ? `<button class="slds-button slds-button_brand" onclick="${saveAction}">${alert_arr.JSLBL_SAVE}</button>` : '';
		let modal = `<section role="dialog" tabindex="-1" class="slds-modal slds-fade-in-open slds-modal_${size}" aria-modal="true">
			<div class="slds-modal__container">
				<header class="slds-modal__header">
					<button class="slds-button slds-button_icon slds-modal__close slds-button_icon-inverse"
							title="${alert_arr.LBL_CLOSE_TITLE}"
							onClick="javascript:ldsModal.close(${destroy})"
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
					${drawButton}
					<button class="slds-button slds-button_neutral" onClick="javascript:ldsModal.close(${destroy})">${cancelButtonText}</button>
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
	close : (destroy = true) => {
		let modalContainer = document.getElementById('global-modal-container');
		modalContainer.dispatchEvent(ldsModalClosing);
		ldsModal.active = false;
		if (destroy) {
			document.body.removeChild(modalContainer);
		} else {
			modalContainer.style.display = 'none';
		}
	},
	updateTitle : (title) => {
		document.getElementById('global-modal-container__title').innerHTML = title;
	},
	active: false
};
