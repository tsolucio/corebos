{if !$NoFile && $_downloadurl}
    <style>
        #pdfPreviewiframe{
            border: none;
        }
        #pdfIframeContainer{
            margin-top: 15px;
            border: 1px solid #ddd;
        }
    </style>
    <div id="pdfIframeContainer">
        <iframe id="pdfPreviewiframe" src="Smarty/templates/modules/Documents/pdfViewer.html?file={$_downloadurl}&ulang={$UserLanguage}#zoom=page-width"  title="{$title}" width="100%" height="100%" />
        </iframe>
    </div>

    <script>
        const pdfIframeContainer = document.getElementById("pdfIframeContainer");
        window.onmessage = function(e) {
            let width = "{$width}";
            let height = "{$height}";
            if(!width){
               width = '100%';
            } else {
                width = width+'px';
            }
            if(!height){
               height = e?.data?.height;

                if(height){
                    height = Number(height.split('px')[0]);
                    height = height + 80;
                    height = height+'px';
                }
            } else {
                height = height+'px';
            }

            pdfIframeContainer.style.width = width;
            pdfIframeContainer.style.height = height;

        };
    </script>

{/if}


