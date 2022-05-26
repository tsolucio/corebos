<!DOCTYPE html>
<html>
<head>
	<title>Paint to Document</title>
	<meta name="viewport" content="width=device-width">
	<link rel="stylesheet" href="include/components/PaintJS/spectrum.css">
	<link rel="stylesheet" href="include/components/PaintJS/control.css">
	<link rel="stylesheet" href="include/components/PaintJS/paint.css">
	<link rel="stylesheet" href="include/components/PaintJS/introjs.css">
	<link rel="stylesheet" href="include/components/PaintJS/gradientcreator.css">
	<link rel="stylesheet" href="include/components/PaintJS/quicksettings.css">
	<link rel="stylesheet" href="include/LD/assets/styles/salesforce-lightning-design-system.css" >
	<link rel="stylesheet" href="include/LD/assets/styles/mainmenu.css">
	<link rel="stylesheet" href="include/LD/assets/styles/override_lds.css">
	<link rel="stylesheet" href="include/style.css">
	<script src="include/Webservices/WSClient.js"></script>
	<script src="include/components/ldsprompt.js"></script>
	<script type="text/javascript" src="include/js/{$USER_LANG}.lang.js"></script>
	<script type="text/javascript">
		const cbUserID = '{$USERID}';
		const cbFolderID = '{$DOCID}';
		const WSID = '{$WSID}';
	</script>
	<script src="include/components/PaintJS/script.js"></script>
	<script type="text/javascript">
		window.addEventListener('DOMContentLoaded', (event) => {
			window.paint = new PaintDocuments(cbUserID, cbFolderID, WSID);
			paint.Init();
		});
	</script>
</head>
<body class="slds-theme_shade slds-theme_alert-texture">
<div class="slds-grid" style="padding: 10px">
	<div class="slds-col slds-size_2-of-12">
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
				<select id="folders"  class="slds-input">
					{foreach from=$FOLDERS item=name}
					<option value="{$name[0]}">{$name[1]}</option>
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
	<div class="slds-col slds-size_10-of-12">
		<div id="fullscreen" style="position:absolute; left:19%; right:0; top:0; bottom:0;"></div>
	</div>
</div>
	<script src="include/jquery/jquery.js"></script>
	<script src="include/components/PaintJS/Paint.min.js"></script>
	</body>
</html>