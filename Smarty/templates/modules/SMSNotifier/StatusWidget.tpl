{*<!--
/*********************************************************************************
** The contents of this file are subject to the vtiger CRM Public License Version 1.0
* ("License"); You may not use this file except in compliance with the License
* The Original Code is:  vtiger CRM Open Source
* The Initial Developer of the Original Code is vtiger.
* Portions created by vtiger are Copyright (C) vtiger.
* All Rights Reserved.
********************************************************************************/
-->*}

<div>
	<table width="100%" cellpadding="3" cellspacing="1" border="0" class="lvt small">

		{assign var="_TRSTARTED" value=false}

		{foreach item=RESULT from=$RESULTS name=NUMBERSECTION}

			{if $smarty.foreach.NUMBERSECTION.index % 4 == 0}

				{* Close the tr if it was started last *}
				{if $_TRSTARTED}
					</tr>
					{assign var="_TRSTARTED" value=false}
				{/if}

				<tr class="lvtColData" onmouseover="this.className='lvtColDataHover'" onmouseout="this.className='lvtColData'" >
					{assign var="_TRSTARTED" value=true}
			{/if}

			{assign var="_TDBGCOLOR" value="#FFFFFF"}

			{if $RESULT.status == 'Processing'}
				{assign var="_TDBGCOLOR" value="#FFFCDF"}
			{elseif $RESULT.status == 'Dispatched'}
				{assign var="_TDBGCOLOR" value="#E8FFCF"}
			{elseif $RESULT.status eq 'Failed'}
				{assign var="_TDBGCOLOR" value="#FFE2AF"}
			{/if}

			<td nowrap="nowrap" bgcolor="{$_TDBGCOLOR}" width="25%">{$RESULT.tonumber}</td>

		{/foreach}

		{* Close the tr if it was started last *}
		{if $_TRSTARTED}
			</tr>
			{assign var="_TRSTARTED" value=false}
		{/if}

	</table>
</div>