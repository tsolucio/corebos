{if $INWINDOW}
<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<meta name="robots" content="noindex">
	<link REL="SHORTCUT ICON" HREF="{$COMPANY_DETAILS.favicon}">
	<title>{$APP.Photo2Document}</title>
	<meta name="viewport" content="width=device-width">
	<link rel="stylesheet" href="include/LD/assets/styles/salesforce-lightning-design-system.css" >
	<link rel="stylesheet" href="include/LD/assets/styles/mainmenu.css">
	<link rel="stylesheet" href="include/LD/assets/styles/override_lds.css">
	<link rel="stylesheet" href="include/style.css">
	<script type="text/javascript" src="include/js/{$USER_LANG}.lang.js"></script>
{/if}
	<script src="include/Webservices/WSClient.js"></script>
	<script src="include/components/ldsprompt.js"></script>
<style>
	<!--
	.videobox {
		display: flex;
		flex-direction: row;
		width:100%;
	}
	.p2dbutton {
		justify-content: left;
	}
	-->
</style>
	<script type="text/javascript">
		const p2dcbUserID = '{$USERID}';
		const p2dcbFolderID = '{$DOCID}';
		const p2dWSID = '{$WSID}';
		const i18nP2D = {
			'ERROR': '{$APP.ERROR}',
			'DocumentCreatedRelated': '{$APP.DocumentCreatedRelated}',
			'LBL_CREATING': '{$APP.LBL_CREATING}',
		};
	</script>
{if $INWINDOW}
</head>
<body class="slds-theme_shade slds-theme_alert-texture" style="height: 600px;">
{/if}
<div class="slds-page-header"><span class="slds-page-header__title slds-truncate">{$APP.Photo2Document}</span></div>
<div class="slds-grid" style="padding: 10px;height: 600px;">
	<div class="slds-col slds-size_3-of-12 slds-p-right_xx-small">
		<div class="slds-form-element">
			<label class="slds-form-element__label" for="form-element-01">
				{$APP.LBL_TITLE} <span style="color: red">*</span>
			</label>
			<div class="slds-form-element__control">
				<input type="text" id="docname" name="docname" class="slds-input">
			</div>
		</div>
		<div class="slds-form-element">
			<label class="slds-form-element__label" for="form-element-01">
				{$APP.LBL_DESCRIPTION}
			</label>
			<div class="slds-form-element__control">
				<textarea id="docdesc" name="docdesc" class="slds-input"></textarea>
			</div>
		</div>
		<div class="slds-form-element">
			<label class="slds-form-element__label" for="form-element-01">
				{$APP.LBL_FILENAME} <span style="color: red">*</span>
			</label>
			<div class="slds-form-element__control">
				<input type="text" id="filename" name="filename" class="slds-input" value="Photo2Document.png">
			</div>
		</div>
		<div class="slds-form-element">
			<label class="slds-form-element__label" for="form-element-01">
				{$APP.LBL_FOLDER} <span style="color: red">*</span>
			</label>
			<div class="slds-form-element__control">
				<select id="docfolder" name="docfolder" class="slds-input">
					{foreach from=$FOLDERS item=name}
					<option value="{$name[0]}"{if $FOLDERID==$name[0]} selected{/if}>{$name[1]}</option>
					{/foreach}
				</select>
			</div>
		</div>
		<div class="slds-form-element slds-m-top_small">
			<div class="slds-form-element__control">
				<button type="button" class="slds-button slds-button_neutral slds-container_fluid" style="justify-content: left;" onclick="p2dSnap();">
					<svg class="slds-button__icon slds-button__icon_left" aria-hidden="true">
						<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#photo"></use>
					</svg>
					{$APP['Snap Photo']}
				</button>
			</div>
			<div class="slds-form-element__control">
				<button type="button" class="slds-button slds-button_neutral slds-container_fluid" style="justify-content: left;" onclick="p2dClearPicture();">
					<svg class="slds-button__icon slds-button__icon_left" aria-hidden="true">
						<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#unlock"></use>
					</svg>
					{$APP['Clear Photo']}
				</button>
			</div>
			<div class="slds-form-element__control">
				<button type="button" class="slds-button slds-button_neutral slds-container_fluid" style="justify-content: left;" onclick="createdoc();">
					<svg class="slds-button__icon slds-button__icon_left" aria-hidden="true">
						<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#save"></use>
					</svg>
					{$APP.LBL_SAVE_LABEL}
				</button>
			</div>
{if $INWINDOW}
			<div class="slds-form-element__control">
				<button type="button" class="slds-button slds-button_neutral slds-container_fluid" style="justify-content: left;" onclick="window.close();">
					<svg class="slds-button__icon slds-button__icon_left" aria-hidden="true">
						<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#close"></use>
					</svg>
					{$APP.LBL_CLOSE}
				</button>
			</div>
{/if}
		</div>
	</div>
	<div class="slds-col slds-size_9-of-12">
		<div class="videobox">
			<div style="width:90%">
				<video id="video" width="640" height="480" autoplay></video>
				<canvas id="canvas" width="640" height="480" style="display:none"></canvas>
			</div>
		</div>
	</div>
</div>
{if $INWINDOW}
<script src="include/jquery/jquery.js"></script>
{/if}
<script src="include/components/Photo2Document.js"></script>
{if $INWINDOW}
</body>
</html>
{/if}