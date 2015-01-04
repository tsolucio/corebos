<form action="index.php" class="dropzone" id="my-awesome-dropzone">
	<input type="hidden" name="module" value="{$MODULE}">
	<input type="hidden" name="action" value="{$MODULE}Ajax">
	<input type="hidden" name="file" value="UploadImage">
	<input type="hidden" name="record" value="{$ID}">
	<div class="dz-message fa fa-cloud-upload fa-4x">
		<span>{'Drag file here or click to upload'|@getTranslatedString}</span>
	</div>
</form>
<script type="text/javascript">
{literal}
jQuery( "<link>" ).appendTo( "head" ).attr({
  rel: "stylesheet",
  href: "//netdna.bootstrapcdn.com/font-awesome/4.0.2/css/font-awesome.css"
});
jQuery( "<link>" ).appendTo( "head" ).attr({
  rel: "stylesheet",
  href: "include/dropzone/dropzone.css"
});
jQuery( "<link>" ).appendTo( "head" ).attr({
  rel: "stylesheet",
  href: "include/dropzone/custom.css"
});
jQuery.getScript( "include/dropzone/dropzone.js" );
{/literal}
</script>
