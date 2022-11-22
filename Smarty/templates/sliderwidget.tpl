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
<div class="js-Carousel" id="carousel" style="margin-top: 1%">
	<ul>
		{foreach from=$images item=$i}
		<li>
			<img src="{$i.path}" onclick="showFullImage('{$i.path}', '{$i.title}', {$i.id})" style="cursor: pointer;">
		</li>
		{/foreach}
	</ul>
</div>
<script type="text/javascript">
	window.addEventListener('load', function() {
		var carousel = new Carousel({
			elem: 'carousel',
			autoplay: false,
			infinite: true,
			initial: 0,
			dots: true,
			arrows: true,
		});
	});
	function showFullImage(src, title, id) {
		let header = '<a href="index.php?action=DetailView&module=Documents&record='+id+'" target="_blank">'+title+'</a>';
		let content = '<img src="'+src+'" style="display:block;margin:auto;">';
		ldsModal.show(header, content, 'large');
	}
</script>