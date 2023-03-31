/*************************************************************************************************
 * Copyright 2022 JPL TSolucio, S.L. -- This file is a part of TSOLUCIO coreBOS Customizations.
* Licensed under the vtiger CRM Public License Version 1.1 (the "License"); you may not use this
* file except in compliance with the License. You can redistribute it and/or modify it
* under the terms of the License. JPL TSolucio, S.L. reserves all rights not expressly
* granted by the License. coreBOS distributed by JPL TSolucio S.L. is distributed in
* the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied
* warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. Unless required by
* applicable law or agreed to in writing, software distributed under the License is
* distributed on an "AS IS" BASIS, WITHOUT ANY WARRANTIES OR CONDITIONS OF ANY KIND,
* either express or implied. See the License for the specific language governing
* permissions and limitations under the License. You may obtain a copy of the License
* at <http://corebos.org/documentation/doku.php?id=en:devel:vpl11>
*************************************************************************************************/
const Slider = {
	Instance: false,
	Autoplay: false,
	Infinite: true,
	Initial: 1,
	Dots: true,
	Arrows: true,
	ActiveSlide: 1,
	TotalSlides: 0,
	Data: '',
	DataObj: '',
	CurrentObj: {},

	Init: (el) => {
		Slider.Instance = new Carousel({
			elem: el,
			autoplay: Slider.Autoplay,
			infinite: Slider.Infinite,
			initial: Slider.Initial,
			dots: Slider.Dots,
			arrows: Slider.Arrows,
		});
		if (Slider.Initial == 0) {
			Slider.ActiveSlide = 2;
		}
	},

	MoveLeft: () => {
		Slider.ActiveSlide--;
		if (Slider.ActiveSlide <= 1) {
			Slider.ActiveSlide = Slider.TotalSlides+1;
		}
		if (Slider.DataObj == '') {
			Slider.DataObj = JSON.parse(Slider.Data);
		}
		ldsModal.close();
		Slider.CurrentObj = Slider.DataObj[Slider.ActiveSlide-2];
		Slider.showFullImage(Slider.CurrentObj.path, Slider.CurrentObj.title, Slider.CurrentObj.id);
	},

	MoveRight: () => {
		Slider.ActiveSlide++;
		if (Slider.ActiveSlide-1 > Slider.TotalSlides) {
			Slider.ActiveSlide = 2;
		}
		if (Slider.DataObj == '') {
			Slider.DataObj = JSON.parse(Slider.Data);
		}
		ldsModal.close();
		Slider.CurrentObj = Slider.DataObj[Slider.ActiveSlide-2];
		Slider.showFullImage(Slider.CurrentObj.path, Slider.CurrentObj.title, Slider.CurrentObj.id);
	},

	showFullImage: (src, title, id, recordid = '', currentmodule = '') => {
		let header = `
			<a href="index.php?action=DetailView&module=Documents&record=${id}" target="_blank">${title}</a>
		`;
		let content = `
			<img src="${src}" style="display:block;margin:auto;">
			<div class="slds-grid slds-gutters">
				<div class="slds-col">
					<button class="slds-button slds-button_icon slds-button_icon-border-filled" onclick="Slider.MoveLeft()">
						<svg class="slds-button__icon" aria-hidden="true">
							<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#arrow_left"></use>
						</svg>
						<span class="slds-assistive-text">${alert_arr.JSLBL_LEFT}</span>
					</button>
				</div>
				<div class="slds-col">
					<button class="slds-button slds-button_icon slds-button_icon-border-filled slds-float_right" onclick="Slider.MoveRight()">
						<svg class="slds-button__icon" aria-hidden="true">
							<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#arrow_right"></use>
						</svg>
						<span class="slds-assistive-text">${alert_arr.JSLBL_RIGHT}</span>
					</button>
				</div>
			</div>`;
		let doc2edit = encodeURIComponent(id);
		let forrecord = recordid;
		let module = currentmodule;
		let drawButton = (doc2edit != '') ?
			`<button class="slds-button slds-button_neutral" id="drawOnImage" onClick="handleDrawClick('${module}', 'EditView', '${forrecord}', '${doc2edit}')">${alert_arr.JSLBL_DRAW}</button>`
			: '';
		ldsModal.show(header, content, 'large', '', '', true, drawButton);
	}
};

async function handleGenerateLinkButton() {
	const result = await ExecuteFunctions('shareLink', 'recordId=1084&operation=create');
	const resultObj = JSON.parse(result);
	const shareToken = resultObj['shareToken'];
	const validUntil = resultObj['validUntil'];
	const imageSliderShareLinkPopupContainer = document.getElementById('imageSliderShareLinkPopupContainer');
	const imageSliderValidUntill = document.getElementById('imageSliderValidUntill');
	const imageSliderShareLink = document.getElementById('imageSliderShareLink');
	imageSliderShareLinkPopupContainer.style.display = 'flex';
	const shareLink = `${gVTsiteUrl}/notifications.php?type=docshare&share_token=${shareToken}`;
	const translation = JSON.parse(await ExecuteFunctions('getTranslatedStrings', 'tkeys=LBL_YOUR_SHARE_LINK;LBL_LINK_VALID_UNTIL'));
	imageSliderShareLink.innerHTML = `${translation['LBL_YOUR_SHARE_LINK']} <a id="imageSliderShareLinkATag" href="${shareLink}">${shareLink}</a>`;
	imageSliderValidUntill.innerHTML = `${translation['LBL_LINK_VALID_UNTIL']} ${validUntil}`;
}

async function handleGenerateLinkCopyButton() {
	const imageSliderShareLinkUrl = document.getElementById('imageSliderShareLinkATag');
	const textarea = document.createElement("textarea");
	textarea.value = imageSliderShareLinkUrl.href;
	document.body.appendChild(textarea);
	textarea.select();
	navigator.clipboard.writeText(textarea.value);
	document.body.removeChild(textarea);
}

async function handleClosingGenerateLinkPopupWindow() {
	const imageSliderShareLinkPopupContainer = document.getElementById('imageSliderShareLinkPopupContainer');
	imageSliderShareLinkPopupContainer.style.display = 'none';
}

function handleDownloadAllImages() {
	const arr = JSON.parse(Slider.Data);
	const imageUrls = arr.map(obj => obj.path);
	downloadFilesAsZip('document_' + generateUUID(), imageUrls);
}

window.addEventListener('load', function () {
	Slider.Init('carousel');
});
