{if $INWINDOW}
<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<meta name="robots" content="noindex">
	<link REL="SHORTCUT ICON" HREF="{$COMPANY_DETAILS.favicon}">
	<title>{$APP.LBL_PAINT2DOCUMENT}</title>
	<meta name="viewport" content="width=device-width">
	<link rel="stylesheet" href="include/LD/assets/styles/salesforce-lightning-design-system.css" >
	<link rel="stylesheet" href="include/LD/assets/styles/mainmenu.css">
	<link rel="stylesheet" href="include/LD/assets/styles/override_lds.css">
	<link rel="stylesheet" href="include/style.css">
	<script type="text/javascript" src="include/js/{$USER_LANG}.lang.js"></script>
{/if}
	<script src="include/Webservices/WSClient.js"></script>
	<script src="include/components/ldsprompt.js"></script>
	<link rel="stylesheet" type="text/css" href="include/components/toast-ui/tui-color-picker/tui-color-picker.css" />
	<link type="text/css" href="include/components/toast-ui/image-editor/tui-image-editor.css" rel="stylesheet" />
	<script type="text/javascript">
		const cbUserID = '{$USERID}';
		const cbFolderID = '{$DOCID}';
		const WSID = '{$WSID}';
		let i18nLanguage = '{$USER_LANG}';
		if (!['en_us', 'es_es'].includes(i18nLanguage)) {
			i18nLanguage = 'en_us';
		}
	</script>
	<script type="text/javascript" src="include/components/toast-ui/image-editor/locale.js"></script>
{if $INWINDOW}
</head>
<body class="slds-theme_shade slds-theme_alert-texture" style="height: 800px;">
{/if}
<div class="slds-grid" style="padding: 10px;height: 800px;">
	<div class="slds-col slds-size_2-of-12 slds-p-right_xx-small">
		<div class="slds-form-element">
			<label class="slds-form-element__label" for="form-element-01">
				{$APP.LBL_TITLE} <span style="color: red">*</span>
			</label>
			<div class="slds-form-element__control">
				<input type="text" id="title" class="slds-input">
			</div>
		</div>
		<div class="slds-form-element">
			<label class="slds-form-element__label" for="form-element-01">
				{$APP.LBL_DESCRIPTION}
			</label>
			<div class="slds-form-element__control">
				<textarea id="content" class="slds-input"></textarea>
			</div>
		</div>
		<div class="slds-form-element">
			<label class="slds-form-element__label" for="form-element-01">
				{$APP.LBL_FILENAME} <span style="color: red">*</span>
			</label>
			<div class="slds-form-element__control">
				<input type="text" id="filename" class="slds-input">
			</div>
		</div>
		<div class="slds-form-element">
			<label class="slds-form-element__label" for="form-element-01">
				{$APP.LBL_FOLDER} <span style="color: red">*</span>
			</label>
			<div class="slds-form-element__control">
				<select id="folders" class="slds-input">
					{foreach from=$FOLDERS item=name}
					<option value="{$name[0]}"{if $FOLDERID==$name[0]} selected{/if}>{$name[1]}</option>
					{/foreach}
				</select>
			</div>
		</div>
		<div class="slds-form-element slds-m-top_small">
			<div class="slds-form-element__control">
				<button type="button" class="slds-button slds-button_neutral" onclick="paint.Create();">
					<svg class="slds-button__icon slds-button__icon_left" aria-hidden="true">
						<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#save"></use>
					</svg>
					{$APP.LBL_SAVE_LABEL}
				</button>
			</div>
		</div>
	</div>
	<div class="slds-col slds-size_10-of-12" style="height: 100%">
	<div id="tui-image-editor-container" class="tui-image-editor" style="height: 500px"><canvas></canvas></div>
	</div>
</div>
{if $INWINDOW}
<script src="include/jquery/jquery.js"></script>
{/if}
<script type="text/javascript" src="include/components/toast-ui/image-editor/script.js"></script>
<script type="text/javascript" src="include/components/toast-ui/image-editor/FileSaver.min.js"></script>
<script type="text/javascript" src="include/components/toast-ui/image-editor/fabric.js"></script>
<script type="text/javascript" src="include/components/toast-ui/tui-code-snippet/tui-code-snippet.js"></script>
<script type="text/javascript" src="include/components/toast-ui/tui-color-picker/tui-color-picker.js"></script>
<script type="text/javascript" src="include/components/toast-ui/image-editor/tui-image-editor.js"></script>
<script type="text/javascript" src="include/components/toast-ui/image-editor/white-theme.js"></script>
<script>
	// Image editor
	var imageEditor = new tui.ImageEditor('#tui-image-editor-container', {
		includeUI: {
			loadImage: {
			path: 'include/components/toast-ui/image-editor/blank.png',
			name: 'blank',
			},
			locale: i18nImageEditor[i18nLanguage],
			theme: whiteTheme, // or whiteTheme
			initMenu: 'filter',
			menuBarPosition: 'bottom',
		},
		cssMaxWidth: 700,
		cssMaxHeight: 500,
		usageStatistics: false,
	});
	window.onresize = function () {
		imageEditor.ui.resizeEditor();
	};
	window.paint = new PaintDocuments(cbUserID, cbFolderID, WSID);
</script>
{if $INWINDOW}
</body>
</html>
{/if}