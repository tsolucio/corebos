<form action="index.php" class="dropzone" id="product-dropzone">
	<input type="hidden" name="module" value="{$MODULE}">
	<input type="hidden" name="action" value="{$MODULE}Ajax">
	<input type="hidden" name="file" value="UploadImage">
	<input type="hidden" name="record" value="{$ID}">
	<div class="dz-message">
		<span><img alt="{'Drag file here or click to upload'|@getTranslatedString}" src="include/dropzone/upload_120.png"></span>
		<span>{'Drag file here or click to upload'|@getTranslatedString}</span>
	</div>
</form>
<script>var productDropzone = new Dropzone("#product-dropzone");</script>