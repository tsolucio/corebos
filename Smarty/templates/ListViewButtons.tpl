{foreach key=button_check item=button_label from=$BUTTONS}
	{if $button_check eq 'del'}
		<input class="crmbutton small delete" type="button" value="{$button_label}" onclick="return massDelete('{$MODULE}')"/>
	{elseif $button_check eq 'mass_edit'}
		<input class="crmbutton small edit" type="button" value="{$button_label}" onclick="return mass_edit(this, 'massedit', '{$MODULE}', '{$CATEGORY}')"/>
	{elseif $button_check eq 's_mail'}
		<input class="crmbutton small edit" type="button" value="{$button_label}" onclick="return eMail('{$MODULE}',this);"/>
	{elseif $button_check eq 's_cmail'}
		<input class="crmbutton small edit" type="submit" value="{$button_label}" onclick="return massMail('{$MODULE}')"/>
	{elseif $button_check eq 'mailer_exp'}
		<input class="crmbutton small edit" type="submit" value="{$button_label}" onclick="return mailer_export()"/>
	{* Mass Edit handles Change Owner for other module except Calendar *}
	{elseif $button_check eq 'c_owner' && $MODULE eq 'Calendar'}
		<input class="crmbutton small edit" type="button" value="{$button_label}" onclick="return change(this,'changeowner')"/>
	{/if}
{/foreach}
{include file='ListViewCustomButtons.tpl'}
