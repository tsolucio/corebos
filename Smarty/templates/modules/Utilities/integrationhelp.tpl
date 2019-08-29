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
<div class="slds-grid slds-gutters">
	<div class="slds-col">
		<ul class="slds-list_vertical slds-m-left_large slds-m-right_large">
		<li><div class="slds-box"><a href="index.php?action=integration&module=Utilities&_op=getconfig2fa">{'GoTo2FAActivation'|@getTranslatedString:'Utilities'}</a></div></li>
		<li><div class="slds-box"><a href="index.php?action=integration&module=Utilities&_op=getconfiggcontact">{'GOOGLE_CONTACTS'|@getTranslatedString:'Contacts'}</a></div></li>
		<li><div class="slds-box"><a href="index.php?action=integration&module=Utilities&_op=getconfighubspot">{'HubSpot Activation'|@getTranslatedString:'Utilities'}</a></div></li>
		<li><div class="slds-box"><a href="index.php?action=integration&module=Utilities&_op=getconfigsmtp&savemode=false">{'SMTP Configuration'|@getTranslatedString:'Utilities'}</a></div></li>
		</ul>
	</div>
	<div class="slds-col">
		<ul class="slds-list_vertical slds-m-left_large slds-m-right_large">
		<li><div class="slds-box"><a href="index.php?action=integration&module=Utilities&_op=getconfigzendesk">{'Zendesk Activation'|@getTranslatedString:'Utilities'}</a></div></li>
		<li><div class="slds-box"><a href="index.php?action=integration&module=Utilities&_op=getconfigwhatsapp">{'Whatsapp Activation'|@getTranslatedString:'Utilities'}</a></div></li>
		<li><div class="slds-box"><a href="index.php?action=integration&module=Utilities&_op=getconfigsendgrid">{'SendGrid Activation'|@getTranslatedString:'Utilities'}</a></div></li>
		<li><div class="slds-box"><a href="index.php?action=integration&module=Utilities&_op=getconfiggmp">{'GMP Activation'|@getTranslatedString:'Utilities'}</a></div></li>
		</ul>
	</div>
</div>
</div>