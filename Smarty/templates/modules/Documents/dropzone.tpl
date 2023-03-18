{if $SHOWDOCS}
<form action="index.php" class="dropzone" id="updoc-dropzone">
	<div class="dz-message">
		<span><img alt="{'Drag file here or click to upload'|@getTranslatedString}" src="include/dropzone/upload_120.png"></span>
		<span>{'Drag file here or click to upload'|@getTranslatedString}</span>
	</div>
</form>
<script>
var moduleDropzone = new Dropzone('#updoc-dropzone', {
	url: 'index.php?module=Utilities&action=UtilitiesAjax&file=ExecuteFunctions&mode=ajax&functiontocall=saveAttachment&parent_id='+{$ID},
	paramName: 'qqfile',
	parallelUploads: 1,
	addRemoveLinks: false,
	createImageThumbnails: true,
	uploadMultiple: false
});
</script>
{/if}
{if $SHOWLINKS}
<div contenteditable="true" class="text-dropzone" id="url-zoneURLDropzone" ondrop="handlePaste(event, 'URLDropzone')" onpaste="handlePaste(event, 'URLDropzone')" data-text="{'Paste the link here'|@getTranslatedString}"></div>
{/if}
