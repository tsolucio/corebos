{*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************}

{if empty($smarty.request.ajax)}
	<div class="forceRelatedListSingleContainer">
        <article class="slds-card forceRelatedListCardDesktop" aria-describedby="header">
            <div class="slds-card__header slds-grid">
                <header class="slds-media slds-media--center slds-has-flexi-truncate">
                    <div class="slds-media__figure">
                        <div class="extraSmall forceEntityIcon" style="height: 1rem;" 
                        data-aura-rendered-by="3:1782;a" data-aura-class="forceEntityIcon">
                        	<span data-aura-rendered-by="6:1782;a" class="uiImage" data-aura-class="uiImage">
								<a href="javascript:showHideStatus('tbl{$UIKEY}','aid{$UIKEY}','{$IMAGE_PATH}');">
									{if $BLOCKOPEN}
										   <span class="exp_coll_block inactivate">
                                            <img id="aid{$header|replace:' ':''}"
                                                 src="{'chevrondown_60.png'|@vtiger_imageurl:$THEME}" width="16"
                                                 style="border: 0px solid #000000;"
                                                 alt="{'LBL_Hide'|@getTranslatedString:'Settings'}"
                                                 title="{'LBL_Hide'|@getTranslatedString:'Settings'}"/>
                                            </span>
									{else}
										    <span class="exp_coll_block activate">
                                            <img id="aid{$header|replace:' ':''}"
                                                 src="{'chevronright_60.png'|@vtiger_imageurl:$THEME}" width="16"
                                                 style="border: 0px solid #000000;"
                                                 alt="{'LBL_Show'|@getTranslatedString:'Settings'}"
                                                 title="{'LBL_Show'|@getTranslatedString:'Settings'}"/>
                                            </span>
									{/if}
								</a>
							</span>
						</div>
					</div>
					<div class="slds-media__body">
	                    <h2 class="header-title-container" >
	                        <span class="slds-text-title--caps slds-truncate slds-m-right--xx-small">
	                            <b>{$WIDGET_TITLE}</b>
	                        </span>
	                    </h2>
	                </div>
				</header>
				<div class="slds-no-flex" data-aura-rendered-by="1224:0">
	                <div class="actionsContainer mapButton" data-aura-rendered-by="1225:0">
	                    <img src="{'vtbusy.gif'|@vtiger_imageurl:$THEME}" border=0 id="indicator{$UIKEY}" style="display:none;">
						{$APP.LBL_SHOW} <select class="small" onchange="ModCommentsCommon.reloadContentWithFiltering('{$WIDGET_NAME}', '{$ID}', this.value, 'tbl{$UIKEY}', 'indicator{$UIKEY}');">
							<option value="All" {if $CRITERIA eq 'All'}selected{/if}>{$APP.LBL_ALL}</option>
							<option value="Last5" {if $CRITERIA eq 'Last5'}selected{/if}>{$MOD.LBL_LAST5}</option>
							<option value="Mine" {if $CRITERIA eq 'Mine'}selected{/if}>{$MOD.LBL_MINE}</option>
						</select>
	                </div>
	            </div>
			</div>
	 	</article>
	</div>
{/if}

	<div id="tbl{$UIKEY}" class="slds-truncate" style="display: {if $BLOCKOPEN}block{else}none{/if}; white-space: normal;">
		
		<table class="small" border="0" cellpadding="0" cellspacing="0" width="100%">
		
			<tr style="height: 25px;">
				<td colspan="4" align="left" class="dvtCellInfo commentCell" style="white-space: normal;">
				<div id="contentwrap_{$UIKEY}">
						
					{foreach item=COMMENTMODEL from=$COMMENTS}
						{include file="modules/ModComments/widgets/DetailViewBlockCommentItem.tpl" COMMENTMODEL=$COMMENTMODEL}
					{/foreach}
					
				</div>
				</td>
			</tr>

		{if $CANADDCOMMENTS eq 'YES'}
			<tr style="height: 25px;" class='noprint'>
				<td class="dvtCellLabel" align="right" >
					{$MOD.LBL_ADD_COMMENT}
				</td>
				<td width="100%" colspan="3" class="dvtCellInfo" align="left">
					<div id="editarea_{$UIKEY}">
						<textarea id="txtbox_{$UIKEY}" class="slds-textarea" cols="90" rows="8" style="min-height: 35px;"></textarea>
						<br><a href="javascript:;" class="slds-button slds-button_success slds-button--x-small" onclick="ModCommentsCommon.addComment('{$UIKEY}', '{$ID}');">{$APP.LBL_SAVE_LABEL}</a>
						<a href="javascript:;" onclick="document.getElementById('txtbox_{$UIKEY}').value='';" class="slds-button slds-button--destructive slds-button--x-small">{$APP.LBL_CLEAR_BUTTON_LABEL}</a>
					</div>
				</td>
			</tr>
		{/if}
		</table>

	</div>

	