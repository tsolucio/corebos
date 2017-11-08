<div id="signature-pad" data-role="main" class="m-signature-pad">
	<div class="m-signature-pad--body">
		{if isset($SIGNPATH) && $SIGNPATH != ''}
			<img border="0" align="middle" src="../../{$SIGNPATH}"/>
	</div>
</div>
		{else}
			<canvas></canvas>
	</div>
	<div class="m-signature-pad--footer">
		<button type="button" data-role='button' class="button ui-btn ui-btn-inline ui-shadow ui-corner-all ui-icon-refresh ui-btn-icon-left" data-action="clear"></button>
		<button type="button" data-role='button' class="button ui-btn ui-btn-inline ui-shadow ui-corner-all ui-icon-check ui-btn-icon-left" data-action="save"></button>
	</div>
</div>

{literal}
	<script>
		var wrapper = document.getElementById("signature-pad"),
			clearButton = wrapper.querySelector("[data-action=clear]"),
			saveButton = wrapper.querySelector("[data-action=save]"),
			canvas = wrapper.querySelector("canvas"),
			signaturePad;
		
		// Adjust canvas coordinate space taking into account pixel ratio,
		// to make it look crisp on mobile devices.
		// This also causes canvas to be cleared.
		function resizeCanvas() {
			// When zoomed out to less than 100%, for some very strange reason,
			// some browsers report devicePixelRatio as less than 1
			// and only part of the canvas is cleared then.
			var ratio =  Math.max(window.devicePixelRatio || 1, 1);
			canvas.width = canvas.offsetWidth * ratio;
			canvas.height = canvas.offsetHeight * ratio;
			canvas.getContext("2d").scale(ratio, ratio);
		}

		window.onresize = resizeCanvas;
		$('div[data-role=collapsible]').on( "collapsibleexpand", function( event, ui ) {
			if(event.target.id = "signatureCollapsible")
				resizeCanvas();
		} );

		signaturePad = new SignaturePad(canvas);

		clearButton.addEventListener("click", function (event) {
			signaturePad.clear();
		});
		
		saveButton.addEventListener("click", function (event) {
			if (signaturePad.isEmpty()) {
				alert("Please provide signature first.");
			} else {
				$.ajax({
					method: "POST",
					url: "index.php?_operation=saveSignature",
					dataType: "json",
					data: {
						signature: signaturePad.toDataURL("image/png"),
						recordid: $('#recordid').val()
					}
				})
				.done(function( msg ) {
					signaturePad.clear();
					location.href='index.php?_operation=fetchRecord&record='+$('#recordid').val();
					return false;
				})
				.fail(function() {
					alert( "Error al crear documento" );
					return false;
				});
			}
		});
	</script>
{/literal}
{/if}
