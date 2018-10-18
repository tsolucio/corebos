<!-- <div id="divloadingid"></div>
<div id="LoadfromMapFirstStep">
	<div class="slds-form-element" style="margin-right:20%; ">
		<label class="slds-form-element__label" for="select-01">{$MOD.ChoseMapTXT}</label>
		<div class="slds-form-element__control">
			<div class="slds-select_container" style="width: 65%;">
				<select id="GetALLMaps" class="slds-select">
					{$AllMaps}
				</select>
			</div>
		</div>
	</div>
	<a id="set" style="margin-top: 30px;" data-select-map-load="true" data-loading="true" data-loading-divid="divloadingid" data-select-map-load-id-relation="GetALLMaps" data-select-map-load-id-to-show="LoadfromMapSecondStep" data-select-map-load-url="MapGenerator,GetMapGeneration" data-showhide-load="true" data-tools-id="LoadfromMapFirstStep,LoadfromMapSecondStep" class="slds-button slds-button--neutral">Next
</a>
</div>
<div id="LoadfromMapSecondStep" style="display: none;">
</div>

 -->
<div id="LoadfromMapFirstStep">
	<table  class="slds-table slds-no-row-hover slds-table-moz load-map-table">
		<tbody>
			<tr class="blockStyleCss" id="DivObjectID">
				<td class="detailViewContainer" valign="top">
					<div class="forceRelatedListSingleContainer">
						<article class="slds-card forceRelatedListCardDesktop" aria-describedby="header">
							<div class="slds-card__header slds-grid">
								<header class="slds-media slds-media--center slds-has-flexi-truncate">
									<div class="slds-media__body">
										<h2>
											<span class="slds-text-title--caps slds-truncate slds-m-right--xx-small" title="Organization Information">
												<b>{$MOD.ChoseMapTXT}</b>
											</span>
										</h2>
									</div>
								</header>
							</div>
						</article>
					</div>
					<div class="slds-truncate">
						<table class="slds-table slds-table--cell-buffer slds-no-row-hover slds-table--bordered slds-table--fixed-layout small detailview_table">
							<tr class="slds-line-height--reset">
								<td class="dvtCellLabel" width="30%">{$MOD.ChoseMapTXT}</td>
								<td class="dvtCellInfo" align="left" width="70%">
									<style scoped>
										@import "modules/MapGenerator/css/Searchselect.css";
										span{
											/*width: 500px;*/
										}
									</style>
									<div class="load-all-maps-select-container">
										<select class="js-example-basic-single slds-select" name="state" id="GetALLMaps" >
											{$AllMaps}
										</select>
									</div>
								</td>
							</tr>
						</table>
					</div>
					<div style="text-align: right;">
						<div style="padding: .5rem;">
							<input id="set" data-select-map-load="true"  data-loading="true" data-loading-divid="LoadingDivId"  data-select-map-load-id-relation="GetALLMaps" data-select-map-load-id-to-show="LoadfromMapSecondStep" data-select-map-load-url="MapGenerator,GetMapGeneration" data-showhide-load="true" data-tools-id="LoadfromMapFirstStep,LoadfromMapSecondStep" name="mapbutton" value="Next" class="slds-button slds-button--small slds-button--brand" title="Locate Map" type="button">
						</div>
					</div>
				</td>
			</tr>
		</tbody>
	</table>

	<script type="text/javascript">
		// In your Javascript (external .js resource or <script> tag)
	$(document).ready(function() {
		$('.js-example-basic-single').select2();
	});
	</script>
</div>

<div id="LoadfromMapSecondStep" style="display: none;"></div>