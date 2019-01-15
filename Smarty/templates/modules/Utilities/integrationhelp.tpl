<table width="100%" cellpadding="2" cellspacing="0" border="0" class="detailview_wrapper_table">
	<tr>
		<td class="detailview_wrapper_cell">
			{include file='Buttons_List.tpl'}
		</td>
	</tr>
</table>
<div class="slds-page-header" role="banner">
	<div class="slds-grid">
		<div class="slds-col slds-has-flexi-truncate">
			<div class="slds-media slds-no-space slds-grow">
				<div class="slds-media__figure">
					<svg aria-hidden="true" class="slds-icon slds-icon-standard-user">
						<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#sync"></use>
					</svg>
				</div>
				<div class="slds-media__body">
					<h1 class="slds-page-header__title slds-m-right_small slds-align-middle slds-truncate"
						title="{$TITLE_MESSAGE}">{$TITLE_MESSAGE}</h1>
				</div>
			</div>
		</div>
	</div>
</div>
<br>
<div class="">
<ul class="slds-list_vertical slds-m-left_large">
<li><a href="index.php?action=integration&module=Utilities&_op=getconfig2fa">{'GoTo2FAActivation'|@getTranslatedString:'Utilities'}</a></li>
<li><a href="index.php?action=integration&module=Utilities&_op=getconfiggcontact">{'GOOGLE_CONTACTS'|@getTranslatedString:'Contacts'}</a></li>
<li><a href="index.php?action=integration&module=Utilities&_op=getconfighubspot">{'HubSpot Activation'|@getTranslatedString:'Utilities'}</a></li>
<li><a href="index.php?action=integration&module=Utilities&_op=getconfigzendesk">{'Zendesk Activation'|@getTranslatedString:'Utilities'}</a></li>
</ul>
</div>