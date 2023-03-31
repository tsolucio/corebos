{*<!--
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
-->*}
<script src="include/components/Carousel/vanilla-js-carousel.min.js"></script>
<link rel="stylesheet" href="include/components/Carousel/vanilla-js-carousel.css">
<div class="slds-page-header">
	<div class="slds-page-header__row">
		<div class="slds-page-header__col-title">
			<div class="slds-media">
				<div class="slds-media__body">
					<div class="slds-page-header__name">
						<div class="slds-page-header__name-title">
							<h1>
								<span class="slds-page-header__title slds-truncate">
									{$title}
								</span>
							</h1>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<div class="js-Carousel" id="carousel" style="margin-top: 1%;{$customstyle}">
	<ul>
		{foreach from=$images item=$i key=$seq}
		<li>
			<img src="{$i.path}" data-sequence="{$seq}" onclick="Slider.showFullImage('{$i.path}', '{$i.title}', {$i.id}, '{$recordid}', '{$currentmodule}')" style="cursor: pointer;{$customstyle}">
		</li>
		{/foreach}
	</ul>
</div>
<div id="imageSliderButtonsContainer">
	<div id="imageSliderButtons">
		<div id="generateShareLinkButton">
			<button class="slds-button slds-button_outline-brand" onclick="handleGenerateLinkButton()">{$APP.LBL_GENERATE_LINK}</button>
		</div>
		<div id="downloadAllImagesButton">
			<button class="slds-button slds-button_outline-brand" onclick="handleDownloadAllImages()">{$APP.LBL_DOWNLOAD_ALL_IMAGES}</button>
		</div>
	</div>
</div>
<div id="imageSliderShareLinkPopupContainer">
	<div id="imageSliderShareLinkPopup">
		<div id="imageSliderShareLink" class="slds-m-around_medium"></div>
		<div id="imageSliderValidUntill" class="slds-m-around_medium"></div>
		<button class="slds-button slds-button_outline-brand" onclick="handleGenerateLinkCopyButton()">{$APP.LBL_CLICK_TO_COPY}</button>
		<div id="imageSliderClosePopup" onclick="handleClosingGenerateLinkPopupWindow()">X</div>
	</div>
</div>
<script src="include/components/slider.js"></script>
<script type="text/javascript">
	Slider.Data = '{$imagesjson}';
	Slider.Autoplay = {$autoplay};
	Slider.Infinite = {$infinite};
	Slider.Initial = {$initial};
	Slider.ActiveSlide = {$initial};
	Slider.Dots = {$dots};
	Slider.Arrows = {$arrows};
	Slider.TotalSlides = {$totalslides};
</script>