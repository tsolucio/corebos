const ldsMailClosing = new Event('closemail');
const ldsMail = {
	show : (headText, content, footer = '', height = '35', width = '58') => {
		ldsMail.height = height;
		ldsMail.width = width;
		let mail = `
		<div class="slds-docked_container" style="z-index:100;">
			<section class="slds-docked-composer slds-grid slds-grid_vertical slds-fade-in-open slds-is-open slds-has-focus" id="global-mail-box" role="dialog" style="height: ${height}rem; width: ${width}rem;">
				<header class="slds-docked-composer__header slds-grid slds-shrink-none">
					<div class="slds-media slds-media_center slds-no-space">
						<div class="slds-media__figure slds-m-right_x-small">
							<span class="slds-icon_container">
								<svg class="slds-icon slds-icon_small slds-icon-text-default" aria-hidden="true">
									<use xlink:href="include/LD/assets/icons/standard-sprite/svg/symbols.svg#email"></use>
								</svg>
							</span>
						</div>
						<div class="slds-media__body">
							<h2 class="slds-truncate" id="global-mail-container__title" title="${headText}">${headText}</h2>
						</div>
					</div>
					<div class="slds-col_bump-left slds-shrink-none">
						<button class="slds-button slds-button_icon slds-button_icon" id="minimize-mail-btn" title="${alert_arr.LBL_MIN_PANEL}" onClick="javascript:ldsMail.minimize()">
							<svg class="slds-button__icon" aria-hidden="true">
								<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#minimize_window"></use>
							</svg>
							<span class="slds-assistive-text">${alert_arr.LBL_MIN_PANEL}</span>
						</button>
						<button class="slds-button slds-button_icon slds-button_icon" id="expand-mail-btn" style="display:none;" title="${alert_arr.LBL_EXPAND_PANEL}" onClick="javascript:ldsMail.expand()">
							<svg class="slds-button__icon" aria-hidden="true">
								<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#expand_alt"></use>
							</svg>
							<span class="slds-assistive-text">${alert_arr.LBL_EXPAND_PANEL}</span>
						</button>
						<button class="slds-button slds-button_icon slds-button_icon" title="${alert_arr.LBL_CLOSE_TITLE}" onClick="javascript:ldsMail.close()">
							<svg class="slds-button__icon" aria-hidden="true">
								<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#close"></use>
							</svg>
							<span class="slds-assistive-text">${alert_arr.LBL_CLOSE_TITLE}</span>
						</button>
					</div>
				</header>
				<div class="slds-docked-composer__body" id="global-mail-content" style="overflow-y:auto;height:${ldsMail.height-10}rem">
					${content}
				</div>
				<footer class="slds-docked-composer__footer slds-shrink-none" id="global-mail-footer">
					${footer}
				</footer>
			</section>
		</div>
		`;
		ldsMail.insert(mail);
	},
	insert : (html) => {
		if (ldsMail.active === true) {
			console.error('Can\'t show two mail components, close the first one first');
		} else {
			let mailContainer = document.createElement('DIV');
			mailContainer.id = 'global-mail-container';
			mailContainer.innerHTML = html;
			document.body.appendChild(mailContainer);
			ldsMail.active = true;
			vtlib_executeJavascriptInElement(document.getElementById('global-mail-container'));
		}
	},
	close : () => {
		let mailContainer = document.getElementById('global-mail-container');
		mailContainer.dispatchEvent(ldsMailClosing);
		mailContainer.remove(mailContainer);
		ldsMail.active = false;
	},
	minimize : () => {
		let box = document.getElementById('global-mail-box');
		box.style.height = '2.5rem';
		box.style.width = '28rem';
		box.classList.remove('slds-is-open');
		box.classList.remove('slds-has-focus');
		box.classList.add('slds-is-closed');
		let btnExp = document.getElementById('expand-mail-btn');
		let btnMin = document.getElementById('minimize-mail-btn');
		btnMin.style.display = 'none';
		btnExp.style.display = '';
	},
	expand : () => {
		let box = document.getElementById('global-mail-box');
		box.classList.remove('slds-is-closed');
		box.classList.add('slds-is-open');
		box.classList.add('slds-has-focus');
		box.style.height = `${ldsMail.height}rem`;
		box.style.width = `${ldsMail.width}rem`;
		let btnExp = document.getElementById('expand-mail-btn');
		let btnMin = document.getElementById('minimize-mail-btn');
		btnMin.style.display = '';
		btnExp.style.display = 'none';
	},
	updateTitle : (title) => {
		document.getElementById('global-mail-container__title').innerHTML = title;
	},
	active: false,
	height: 0,
	width: 0
};
