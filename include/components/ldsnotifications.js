const ldsNotificationClosing = new Event('closenotifications');
const ldsNotification = {
	show : (headText, bodyText, theme) => {
		theme = theme || 'error';
		let color = 'red';
		if (theme != 'error') {
			color = 'green';
		}
		let popup = `
		<section class="slds-notification" role="dialog" style='margin-top: 5px' id="popup-notification-${ldsNotification.index}">
			<div class="slds-notification__body" id="">
				<a class="slds-notification__target slds-media" href="#">
					<span class="slds-icon slds-icon-text-default">
						<svg class="slds-icon slds-icon_small" aria-hidden="true">
							<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#${theme}">
							</use>
						</svg>
						<span class="slds-assistive-text"></span>
					</span>
					<div class="slds-media__body">
						<h2 class="slds-text-heading_small slds-m-bottom_xx-small" style="color: ${color}">
							${headText}
						</h2>
						<p>${bodyText}</p>
					</div>
				</a>
				<button class="slds-button slds-button_icon slds-button_icon-container slds-notification__close" onclick="ldsNotification.close(${ldsNotification.index})">
					<svg class="slds-button__icon" aria-hidden="true">
						<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#close"></use>
					</svg>
					<span class="slds-assistive-text"></span>
				</button>
			</div>
		</section>`;
		ldsNotification.index++;
		ldsNotification.insert(popup);
	},
	insert : (html) => {
		if (document.getElementById('global-popup-container') === null) {
			let popupContainer = document.createElement('div');
			popupContainer.style.height = '25rem';
			let notificationContainer = document.createElement('div');
			notificationContainer.id = 'global-popup-container';
			notificationContainer.className = 'slds-notification-container';
			notificationContainer.setAttribute('style', 'z-index:9999');
			popupContainer.appendChild(notificationContainer);
			document.body.appendChild(popupContainer);
		}
		let notifcation = document.createElement('div');
		notifcation.innerHTML = html;
		setTimeout(function () {
			notifcation.remove();
		}, 5000);
		document.getElementById('global-popup-container').appendChild(notifcation);
		vtlib_executeJavascriptInElement(document.getElementById('global-popup-container'));
	},
	close : (idx) => {
		document.getElementById(`popup-notification-${idx}`).remove();
	},

	index: 0
};