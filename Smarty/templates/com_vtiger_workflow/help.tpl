<article class="slds-card">
<div class="slds-card__header slds-grid">
<header class="slds-media slds-media_center slds-has-flexi-truncate">
<div class="slds-media__body">
<h2 class="slds-card__header-title">
<span>{$HELPHEADER}</span>
</h2>
</div>
</header>
</div>
<div class="slds-card__body slds-card__body_inner">
{$HELPDESC}
</div>
{if count($HELPCTX)>0}
	<div class="slds-card__header slds-grid">
	<header class="slds-media">
	<div class="slds-media__body">
	<h3 class="slds-card__header-title">
	<span>{'CONTEXT_VARIABLES'|getTranslatedString:'com_vtiger_workflow'}</span>
	</h3>
	</div>
	</header>
	</div>
{/if}
{foreach key=name item=ctxinfo from=$HELPCTX}
	<div class="slds-card__header slds-grid">
	<header class="slds-media">
	<div class="slds-media__body">
	<b>{$name}</b>
	</div>
	</header>
	</div>
	<div class="slds-card__body slds-card__body_inner">
	{$ctxinfo.description|getTranslatedString:'com_vtiger_workflow'}
	</div>
{/foreach}
</article>