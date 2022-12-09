<table border=0 cellspacing=0 cellpadding=0 width=100% class="small detailview_header_table">
	<!-- This is added to display the existing comments -->
	{if $header eq $APP.LBL_COMMENTS || (isset($MOD.LBL_COMMENTS) && $header eq $MOD.LBL_COMMENTS) || (isset($MOD.LBL_COMMENT_INFORMATION) && $header eq $MOD.LBL_COMMENT_INFORMATION)}
		<tr>
			<td colspan=4 class="dvInnerHeader">
				<b>{if isset($MOD.LBL_COMMENT_INFORMATION)}{$MOD.LBL_COMMENT_INFORMATION}{else}{$APP.LBL_COMMENTS}{/if}</b>
			</td>
		</tr>
		<tr>
			<td colspan=4 class="dvtCellInfo">{$COMMENT_BLOCK}</td>
		</tr>
		<tr><td>&nbsp;</td></tr>
	{/if}

	{if $header neq 'Comments' && (!isset($BLOCKS[$idx]['__fields'].relatedlist) || $BLOCKS[$idx]['__fields'].relatedlist eq 0)}
		<div class="slds-section slds-is-open" style="margin-bottom: 0rem !important">
			<h3 class="slds-section__title">
				<button aria-expanded="true" class="slds-button slds-section__title-action" onclick="showHideStatus('tbl{$header|replace:' ':''}','aid{$header|replace:' ':''}','{$IMAGE_PATH}');">
					{if isset($BLOCKINITIALSTATUS[$header]) && $BLOCKINITIALSTATUS[$header] eq 1}
						<svg class="slds-section__title-action-icon slds-button__icon slds-button__icon_left" aria-hidden="true">
							<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#switch"></use>
						</svg>
					{else}
						<svg class="slds-section__title-action-icon slds-button__icon slds-button__icon_left" aria-hidden="true">
							<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#chevronright"></use>
						</svg>
					{/if}
					<span class="slds-truncate" title="{$header}">
						<strong>{$header}</strong>
					</span>
				</button>
			</h3>
		</div>
	{/if}
	<tr>
		<td class="cblds-t-align_right">
			{if isset($MOD.LBL_ADDRESS_INFORMATION) && $header eq $MOD.LBL_ADDRESS_INFORMATION && ($MODULE eq 'Accounts' || $MODULE eq 'Contacts' || $MODULE eq 'Leads') }
				{if $MODULE eq 'Leads'}
				<div class="slds-dropdown-trigger slds-dropdown-trigger_click slds-is-open slds-button_last">
					<button
						class="slds-button slds-button_neutral "
						title="{$APP.LBL_LOCATE_MAP}"
						value="{$APP.LBL_LOCATE_MAP}"
						onClick="searchMapLocation('Main')"
						type="button"
						name="mapbutton"
						>
						{$APP.LBL_LOCATE_MAP}
					</button>
				</div>
				{else}
				<div class="slds-dropdown-trigger slds-dropdown-trigger_click slds-is-open slds-button_last">
					<button
						class="slds-button slds-button_neutral "
						title="{$APP.LBL_LOCATE_MAP}"
						value="{$APP.LBL_LOCATE_MAP}"
						onClick="fnvshobj(this,'locateMap');" onMouseOut="fninvsh('locateMap');"
						type="button"
						name="mapbutton"
						>
						<svg class="slds-button__icon slds-button__icon_left" aria-hidden="true">
							<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#down"></use>
						</svg>
						{$APP.LBL_LOCATE_MAP}
					</button>
				</div>
				{/if}
			{/if}
		</td>
	</tr>
</table>
{if $header neq 'Comments'}
	{if (isset($BLOCKINITIALSTATUS[$header]) && $BLOCKINITIALSTATUS[$header] eq 1) || !empty($BLOCKS[$idx]['__fields'].relatedlist)}
		<div style="width:auto;display:block;" id="tbl{$header|replace:' ':''}" >
		{else}
		<div style="width:auto;display:none;" id="tbl{$header|replace:' ':''}" >
		{/if}
			<table border=0 cellspacing=0 cellpadding=0 width="100%" class="small detailview_table">
			{if !empty($CUSTOMBLOCKS.$header.custom)}
				{include file=$CUSTOMBLOCKS.$header.tpl}
			{elseif isset($BLOCKS[$idx]['__fields'].relatedlist) && $IS_REL_LIST neq false && $IS_REL_LIST|@count > 0}
				{foreach key=bhkey item=bhitem from=$BLOCKS[$idx]['__fields']}
					{if isPermitted($bhkey, 'index')=='yes'}
						{assign var='RELBINDEX' value=$BLOCKS[$idx]['__fields'].relatedlist}
						{include file='RelatedListNew.tpl' RELATEDLISTS=$RELATEDLISTBLOCK.$RELBINDEX RELLISTID=$RELBINDEX}
					{/if}
					{break}
				{/foreach}
			{else}
				{foreach item=detailInfo from=$detail}
					<tr style="height:25px" class="detailview_row">
						{assign var=numfieldspainted value=0}
						{foreach key=label item=data from=$detailInfo}
							{assign var=numfieldspainted value=$numfieldspainted+1}
							{assign var=keyid value=$data.ui}
							{assign var=keyval value=$data.value}
							{assign var=keytblname value=$data.tablename}
							{assign var=keyfldname value=$data.fldname}
							{assign var=keyfldid value=$data.fldid}
							{assign var=keyoptions value=$data.options}
							{assign var=keysecid value=$data.secid}
							{assign var=keyseclink value=$data.link}
							{assign var=keycursymb value=$data.cursymb}
							{assign var=keysalut value=$data.salut}
							{assign var=keyaccess value=$data.notaccess}
							{assign var=keycntimage value=$data.cntimage}
							{assign var=keyadmin value=$data.isadmin}
							{assign var=display_type value=$data.displaytype}
							{assign var=_readonly value=$data.readonly}
							{assign var=extendedfieldinfo value=$data.extendedfieldinfo}

							{if $label ne '' && ($keyid ne 83 || count($TAX_DETAILS)>0)}
								<td class="dvtCellLabel" align=right width=25% style="white-space: normal;">{strip}
								{if $keycntimage ne ''}
									{$keycntimage}
								{elseif $keyid eq '71' || $keyid eq '72'}<!-- Currency symbol -->
									{$label} ({$keycursymb})
								{elseif $keyid eq '9'}
									{$label} {$APP.COVERED_PERCENTAGE}
								{elseif $keyid eq '14'}
									{$label} {"LBL_TIMEFIELD"|@getTranslatedString}
								{else}
									{$label}
								{/if}
								{/strip}</td>
								{if $EDIT_PERMISSION eq 'yes' && $display_type neq '2' && $display_type neq '4' && $display_type neq '5' && $_readonly eq '0'}
									{* Performance Optimization Control *}
									{if !empty($DETAILVIEW_AJAX_EDIT) }
										{include file="DetailViewUI.tpl"}
									{else}
										{include file="DetailViewFields.tpl"}
									{/if}
									{* END *}
								{else}
									{include file="DetailViewFields.tpl"}
								{/if}
							{/if}
						{/foreach}
						{if $numfieldspainted eq 1 && $keyid neq 19 && $keyid neq 20}<td colspan=2></td>{/if}
					</tr>
				{/foreach}
			{/if}
			</table>
		</div>
	{/if}