<link type="text/css" href="modules/MapGenerator/css/jquery-ui.css" rel="stylesheet" />
<script type="text/javascript" src="include/jquery/jquery.js"></script>
<script type="text/javascript" src="include/jquery/jquery-ui.js"></script>
<link rel="stylesheet" type="text/css" href="modules/MapGenerator/css/popupNotification.css">
<link rel="stylesheet" type="text/css" href="modules/MapGenerator/css/main.css" >
<link rel="stylesheet" type="text/css" href="modules/MapGenerator/css/WSstyle.css" >
<link rel="stylesheet" type="text/css" href="modules/MapGenerator/css/Loading.css" rel="stylesheet">
<link rel="stylesheet" href="modules/MapGenerator/css/LoadingAll.css" type="text/css" />
<link rel="stylesheet" href="modules/MapGenerator/css/settings.css" type="text/css" />
<link rel="stylesheet" href="modules/MapGenerator/css/all-modules.css" type="text/css" />
<script type="text/javascript" src="modules/MapGenerator/language/{$currlang}.lang.js"></script>
<script type="text/javascript" src="modules/MapGenerator/js/functions.js"></script>
<script type="text/javascript" src="modules/MapGenerator/js/script.js"></script>
<script type="text/javascript" src="modules/MapGenerator/js/MapGenerator.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/js/select2.min.js"></script>
<div class="small">
	<table class="slds-table slds-no-row-hover slds-table--cell-buffer slds-table-moz" style="background-color: #f7f9fb;">
		<tr class="slds-text-title--caps">
			<td style="padding: 0;">
				{assign var="USE_ID_VALUE" value=$MOD_SEQ_ID} {if $USE_ID_VALUE eq ''} {assign var="USE_ID_VALUE" value=$ID} {/if}
				<div class="slds-page-header s1FixedFullWidth s1FixedTop forceHighlightsStencilDesktop" style="height: 60px; margin-top: 15px;width: 100%">
					<div class="slds-grid primaryFieldRow" style="transform: translate3d(0, -8.65823px, 0);">
						<div class="slds-grid slds-col slds-has-flexi-truncate slds-media--center">
							<div class="slds-media__body">
								<h1 class="slds-page-header__title slds-m-right--small slds-truncate slds-align-middle">
								<span class="uiOutputText">{$MOD.MVCreator}</span>
								</h1>
							</div>
						</div>
					</div>
				</div>
			</td>
		</tr>
	</table>
	{* <div id="LoadingDivId" class="bar"></div> *}
	<div id="LoadingDivId" class="loading"></div>
	<br/>
	<table border=0 cellspacing=0 cellpadding=0 width=90% align=center>
		<tr>
			<td>
				<div class="slds-truncate" style="min-height: 500px;">
					<table class="slds-table slds-no-row-hover dvtContentSpace" style="width: 90%;" align="center">
						<tr>
							<td valign="top" style="padding: 0;">
								<div class="slds-tabs--scoped">
									<ul class="slds-tabs--scoped__nav" role="tablist" style="margin-bottom: 0;">
										<li id="firstTab" class="slds-tabs--scoped__item active" title="" role="presentation">
											<a onclick="selectTab(true);" class="slds-tabs--scoped__link" data-autoload-maps="true" data-autoload-Filename="MapGenerator,createMaps" aria-selected="true" data-autoload-id-put="CreateMaps" data-autoload-id-relation="LoadMAps" href="" role="tab" tabindex="0" aria-selected="true" aria-controls="tab--scoped-1" id="tab--scoped--1__item">{$MOD.CreateView}</a>
										</li>
										<li id="secondTab" class="slds-tabs--scoped__item slds-dropdown-trigger slds-dropdown-trigger_click slds-is-open" title="" role="presentation">
											<a onclick="selectTab(false);" class="slds-tabs--scoped__link" data-autoload-maps="true" data-autoload-Filename="MapGenerator,LoadAllMaps" data-autoload-id-put="LoadMAps" data-autoload-id-relation="CreateMaps" href="" role="tab" tabindex="-1" aria-selected="false" aria-controls="tab--scoped-2" id="tab--scoped-2__item">{$MOD.LoadMap}</a>
										</li>
									</ul>
									{* for notification *}
									<div id="snackbar"></div>
									<!-- CREATE MAP TAB -->
									<div id="CreateMaps" role="tabpanel" aria-labelledby="tab--scoped-1__item" class="slds-tabs--scoped__content slds-truncate">
										{include file="modules/MapGenerator/CreateMaps.tpl"}
									</div>
									<div id="LoadMAps" style="display: none;" role="tabpanel" aria-labelledby="tab--scoped-1__item" class="slds-tabs--scoped__content slds-truncate">
									</div>
								</div>
							</td>
						</tr>
					</table>
				</div>
			</td>
		</tr>
	</table>
</div>
{* {literal} *}
<style type="text/css">
	label{
	  background-color: transparent;
	}
</style></strong>

<script>

	function  closeView() {
		if (confirm('Are you sure you want to close this page')) {
			location.reload();
		} else {
			// Do nothing!
		}

	}


	App.baseUrl = '{$URLAPP}'+'/';
	App.disambleInspectelement=Boolean(Number("{$MapGenerator_Remove_inspectElement}"));

</script>

{* {/literal} *}