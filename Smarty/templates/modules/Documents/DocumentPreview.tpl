{if !$NoFile && $_downloadurl}
    <style>
        #pdfPreviewiframe{
            border: none;
        }
        #pdfIframeContainer{
            margin-top: 15px;
            border: 1px solid #ddd;
            height: 1056px;
        }
        .slds-modal__container{
            width: 95% !important;
            height: 100% !important;
            padding: 2px;
        }
        .slds-modal__content{
            width: 100% !important;
            height: 100% !important;
        }
        .text-dropzone{
            width: 100%;
        }
        .genHeaderSmall, .navigationBtns{
            margin-left: 7px;
        }
        .slds-modal__footer{
            padding-top: 5px;
            padding-bottom: 5px;
        }
        .detailview_utils_table_actions .actionBtnsList ul{
            padding: 5px;
        }
        .detailview_utils_table_actions .actionBtnsList .slds-button{
            padding: 0 15px;
            width: 100%;
            margin: 1px 0;
        }
   </style>
    
    {if $showmodal && $showmodal == '1'}
        <section id="documentPreviewModal" role="dialog" tabindex="-1" class="slds-modal slds-fade-in-open slds-modal_large" aria-labelledby="documentPreviewModal" aria-modal="true" aria-describedby="{$title}">
            <div class="slds-modal__container">
                <button class="slds-button slds-button_icon slds-modal__close slds-button_icon-inverse" onClick="handlePdfPreviewModal(false);" title="{$APP.LBL_CLOSE}">
                    <svg class="slds-button__icon slds-button__icon_large" aria-hidden="true">
                        <use xlink:href="include/LD/assets/icons/utility-sprite/s_downloadurlvg/symbols.svg#close"></use>
                    </svg>
                    <span class="slds-assistive-text">{$APP.LBL_CLOSE}</span>
                </button>
                <div class="slds-modal__content">
                    <div class="slds-grid">
                        <div class="slds-col slds-size_9-of-12">
                            <div id="pdfIframeContainer">
                                <iframe id="pdfPreviewiframe" src="Smarty/templates/modules/Documents/pdfViewer.html?file={$_downloadurl}&siteURL={$site_URL}&id={$document_id}&filename={$filename}&ulang={$UserLanguage}#zoom=page-fit"  title="{$title}" width="100%" height="100%" />
                                </iframe>
                            </div>
                        </div>
                        <div class="slds-col slds-size_3-of-12">
                            <div class="detailview_utils_table_actions detailview_utils_table_actions_top" id="detailview_utils_actions_top">
                                <article class="slds-box slds-m-horizontal_medium slds-m-vertical_medium">
                                    <div class="slds-card__body_inner">

                                        <div class="slds-button-group navigationBtns" role="group">
                                            {include file='Components/DetailViewPirvNext.tpl'}
                                        </div>

                                        <div class="slds-m-vertical_medium"> 
                                            <span class="genHeaderSmall">{$APP.LBL_ACTIONS}</span> 
                                        </div>

                                        <div class="actionBtnsList">
                                            {* vtlib customization: Custom links on the Detail view basic links *}
                                            {if $CUSTOM_LINKS && $CUSTOM_LINKS.DETAILVIEWBASIC}
                                                <ul>
                                                    {foreach item=CUSTOMLINK from=$CUSTOM_LINKS.DETAILVIEWBASIC}
                                                        <li class="actionlink actionlink_customlink actionlink_{$CUSTOMLINK->linklabel|lower|replace:' ':'_'}">
                                                            {assign var="customlink_href" value=$CUSTOMLINK->linkurl}
                                                            {assign var="customlink_label" value=$CUSTOMLINK->linklabel}
                                                                    {assign var="customlink_success" value=$CUSTOMLINK->successmsg}
                                                                    {assign var="customlink_error" value=$CUSTOMLINK->errormsg}
                                                            {if $customlink_label eq ''}
                                                                {assign var="customlink_label" value=$customlink_href}
                                                            {else}
                                                                {* Pickup the translated label provided by the module *}
                                                                {assign var="customlink_label" value=$customlink_label|@getTranslatedString:$CUSTOMLINK->module()}
                                                            {/if}
                                                            {if $customlink_href=='ACTIONSUBHEADER'}
                                                                <span class="genHeaderSmall slds-truncate">{$customlink_label}</span>
                                                            {else}
                                                                        {if empty($CUSTOMLINK->showasbutton)}
                                                                {if $CUSTOMLINK->linkicon}
                                                                    {if strpos($CUSTOMLINK->linkicon, '}')>0}
                                                                        {assign var="customlink_iconinfo" value=$CUSTOMLINK->linkicon|json_decode:true}
                                                                        <span class="slds-icon_container slds-icon-{$customlink_iconinfo.library}-{$customlink_iconinfo.icon}" title="{$customlink_label}">
                                                                        <svg class="slds-icon slds-icon-text-default slds-icon_x-small" aria-hidden="true">
                                                                            <use xlink:href="include/LD/assets/icons/{$customlink_iconinfo.library}-sprite/svg/symbols.svg#{$customlink_iconinfo.icon}"></use>
                                                                        </svg>
                                                                        <span class="slds-assistive-text">{$customlink_label}</span>
                                                                        </span>
                                                                    {else}
                                                                                    <a class="webMnu" href="{$customlink_href}" data-success="{$customlink_success}" data-error="{$customlink_error}" data-title="{$customlink_label}">
                                                                        <img hspace=5 align="absmiddle" border=0 src="{$CUSTOMLINK->linkicon}">
                                                                        </a>
                                                                    {/if}
                                                                {else}
                                                                                <a class="webMnu" href="{$customlink_href}" data-success="{$customlink_success}" data-error="{$customlink_error}" data-title="{$customlink_label}"><img hspace=5 align="absmiddle" border=0 src="themes/images/no_icon.png"></a>
                                                                {/if}
                                                                            &nbsp;<a class="slds-text-link_reset" href="{$customlink_href}" data-success="{$customlink_success}" data-error="{$customlink_error}" data-title="{$customlink_label}">{$customlink_label}</a>
                                                                        {else}
                                                                        <a class="slds-button {if empty($CUSTOMLINK->linkicon)}slds-button_neutral{else}{$CUSTOMLINK->linkicon}{/if}" href="{$customlink_href}" data-success="{$customlink_success}" data-error="{$customlink_error}" data-title="{$customlink_label}">{$customlink_label}</a>
                                                                        {/if}
                                                            {/if}
                                                        </li>
                                                    {/foreach}
                                                </ul>
                                            {/if}

                                            {* vtlib customization: Custom links on the Detail view *}
                                            {if $CUSTOM_LINKS && $CUSTOM_LINKS.DETAILVIEW}
                                                <br>
                                                {if !empty($CUSTOM_LINKS.DETAILVIEW)}
                                                    <table>
                                                        <tr><td class="dvtUnSelectedCell" style="background-color: rgb(204, 204, 204); padding: 5px;">
                                                            <a href="javascript:;" onmouseover="fnvshobj(this,'vtlib_customLinksLay');" onclick="fnvshobj(this,'vtlib_customLinksLay');"><b>{$APP.LBL_MORE} {$APP.LBL_ACTIONS} &#187;</b></a>
                                                        </td></tr>
                                                    </table>
                                                    <br>
                                                    <div style="display: none; left: 193px; top: 106px;width:215px; position:absolute;" class="slds-box_border slds-card" id="vtlib_customLinksLay"
                                                            onmouseout="fninvsh('vtlib_customLinksLay')" onmouseover="fnvshNrm('vtlib_customLinksLay')">
                                                        <table class="slds-p-around_xx-small">
                                                            <tr><td style="border-bottom: 1px solid rgb(204, 204, 204); padding: 5px;"><b>{$APP.LBL_MORE} {$APP.LBL_ACTIONS} &#187;</b></td></tr>
                                                            <tr>
                                                                <td class="slds-p-around_xx-small">
                                                                <ul>
                                                                    {foreach item=CUSTOMLINK from=$CUSTOM_LINKS.DETAILVIEW}
                                                                        {assign var="customlink_href" value=$CUSTOMLINK->linkurl}
                                                                        {assign var="customlink_label" value=$CUSTOMLINK->linklabel}
                                                                        {if $customlink_label eq ''}
                                                                            {assign var="customlink_label" value=$customlink_href}
                                                                        {else}
                                                                            {* Pickup the translated label provided by the module *}
                                                                            {assign var="customlink_label" value=$customlink_label|@getTranslatedString:$CUSTOMLINK->module()}
                                                                        {/if}
                                                                        <li>
                                                                        {if $CUSTOMLINK->linkicon}
                                                                            {if strpos($CUSTOMLINK->linkicon, '}')>0}
                                                                                {assign var="customlink_iconinfo" value=$CUSTOMLINK->linkicon|json_decode:true}
                                                                                <span class="slds-icon_container slds-icon-{$customlink_iconinfo.library}-{$customlink_iconinfo.icon}" title="{$customlink_label}">
                                                                                <svg class="slds-icon slds-icon-text-default slds-icon_x-small" aria-hidden="true">
                                                                                    <use xlink:href="include/LD/assets/icons/{$customlink_iconinfo.library}-sprite/svg/symbols.svg#{$customlink_iconinfo.icon}"></use>
                                                                                </svg>
                                                                                <span class="slds-assistive-text">{$customlink_label}</span>
                                                                                </span>
                                                                            {else}
                                                                                <a class="webMnu" href="{$customlink_href}"><img hspace=5 align="absmiddle" border=0 src="{$CUSTOMLINK->linkicon}"></a>
                                                                            {/if}
                                                                        {else}
                                                                            <a class="webMnu" href="{$customlink_href}"><img hspace=5 align="absmiddle" border=0 src="themes/images/no_icon.png"></a>
                                                                        {/if}
                                                                        &nbsp;<a class="slds-text-link_reset" href="{$customlink_href}">{$customlink_label}</a>
                                                                        </li>
                                                                    {/foreach}
                                                                    </ul>
                                                                </td>
                                                            </tr>
                                                        </table>
                                                    </div>
                                                {/if}
                                            {/if}

                                            {if !empty($CUSTOM_LINKS.DETAILVIEWWIDGET)}
                                                {foreach key=CUSTOMLINK_NO item=CUSTOMLINK from=$CUSTOM_LINKS.DETAILVIEWWIDGET}
                                                    {assign var="customlink_href" value=$CUSTOMLINK->linkurl}
                                                    {assign var="customlink_label" value=$CUSTOMLINK->linklabel}
                                                    {* Ignore block:// type custom links which are handled earlier *}
                                                    {if !preg_match("/^block:\/\/.*/", $customlink_href) && !preg_match("/^top:\/\/.*/", $customlink_href)}
                                                        {if $customlink_label eq ''}
                                                            {assign var="customlink_label" value=$customlink_href}
                                                        {else}
                                                            {* Pickup the translated label provided by the module *}
                                                            {assign var="customlink_label" value=$customlink_label|@getTranslatedString:$CUSTOMLINK->module()}
                                                        {/if}
                                                        <br/>
                                                        <input type="hidden" id="{$CUSTOMLINK->linklabel|replace:' ':''}LINKID" value="{$CUSTOMLINK->linkid}">
                                                        <table style="border:0;width:100%" class="rightMailMerge" id="{$CUSTOMLINK->linklabel}">
                                                            {if $CUSTOMLINK->widget_header}
                                                                <tr>
                                                                    <td class="rightMailMergeHeader">
                                                                        <div>
                                                                        <b>{$customlink_label}</b>&nbsp;
                                                                        <img id="detailview_block_{$CUSTOMLINK->linkid}_indicator" style="display:none;" src="{'vtbusy.gif'|@vtiger_imageurl:$THEME}" border="0" align="absmiddle" />
                                                                        </div>
                                                                    </td>
                                                                </tr>
                                                            {/if}
                                                            {if $CUSTOMLINK->widget_width neq ''}
                                                                {assign var="widget_width" value="width:"|cat:$CUSTOMLINK->widget_width|cat:";"}
                                                            {else}
                                                                {assign var="widget_width" value=''}
                                                            {/if}
                                                            {if $CUSTOMLINK->widget_height neq ''}
                                                                {assign var="widget_height" value="height:"|cat:$CUSTOMLINK->widget_height|cat:";"}
                                                            {else}
                                                                {assign var="widget_height" value=''}
                                                            {/if}
                                                            <tr style="height:25px">
                                                                <td class="rightMailMergeContent"><div id="detailview_block_{$CUSTOMLINK->linkid}" style="{$widget_width} {$widget_height}"></div></td>
                                                            </tr>
                                                            <script type="text/javascript">
                                                                vtlib_loadDetailViewWidget("{$customlink_href}", "detailview_block_{$CUSTOMLINK->linkid}", "detailview_block_{$CUSTOMLINK->linkid}_indicator");
                                                            </script>
                                                        </table>
                                                    {/if}
                                                {/foreach}
                                            {/if}
                                            {* END *}
                                            <!-- Action links END -->
                                            
                                            <ul>
                                                {if $_schedaTecnicaDownloadurl}
                                                <li>
                                                    <button 
                                                        class="slds-button slds-button_neutral"
                                                        title="{$_schedaTecnicaDownloadurl}"
                                                        onClick="window.open('{$_schedaTecnicaDownloadurl}', '_blank', cbPopupWindowSettings);"
                                                    >
                                                        {$APP.LBL_ATTACHMENT}
                                                    </button>
                                                </li>
                                                {/if}
                                            </ul>
                                        </div>

                                    </div>

                                </article>
                            </div>
                        </div>
                    </div>
                </div>
                
            </div>
        </section>
        <div id="sldsBackdropOpen" class="slds-backdrop slds-backdrop_open" role="presentation"></div>
    {else}
        <div id="pdfIframeContainer">
            <iframe id="pdfPreviewiframe" src="Smarty/templates/modules/Documents/pdfViewer.html?file={$_downloadurl}&siteURL={$site_URL}&id={$document_id}&filename={$filename}#zoom=page-fit"  title="{$title}" width="100%" height="100%" />
            </iframe>
        </div>
    {/if}

     <script>
        const pdfIframeContainer = document.getElementById("pdfIframeContainer");

        function handlePdfPreviewModal(open = true){
            const sldsBackdropOpen = document.getElementById("sldsBackdropOpen");
            if(open){
                show('documentPreviewModal');
            } else {
                hide('documentPreviewModal');
            }
            sldsBackdropOpen.classList.toggle("slds-backdrop_open");
        }
        
        var signaturePosition = {
            coordXPercentage: 0,
            coordYPercentage: 0
        }

        function setSignaturePosition(newPosition) {
            signaturePosition = newPosition;
        }

        window.onmessage = function(e) {
            let width = "{$width}";
            let height = "{$height}";
            if (!width) {
               width = '100%';
            } else {
                width = width+'px';
            }
            if (!height) {
                height = e?.data?.height;
                if (height) {
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