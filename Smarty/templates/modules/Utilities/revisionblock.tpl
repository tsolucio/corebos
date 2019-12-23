{if $NUMBER > 0}
	<select class='small' size=8 style='width:100%;' id='dqrevision' name='dqrevision'>
	{foreach key=label item=key from=$REVISIONES}
		<option value='{$key.unique}'>{$key.revision} ({$key.modifiedtime})</option>
	{/foreach}
	</select>
{/if}
<table width="100%" border="0" cellpadding="5" cellspacing="0">
{if $NUMBER > 0}
<tr class="actionlink">
	<td align="left" style="padding-left:10px;">
		<a class="webMnu" href="javascript:dqrevRecover('{$ID}','{$MODULE}');">
			<span class="fa fa-file-text-o" aria-hidden="true" style="padding-right: 0.5em;"></span>
			<span class="glyphicon-class">{'Recover'|@getTranslatedString:'Recover'}</span>
		</a>
	</td>
</tr>
{/if}
<tr class="actionlink">
	<td align="left" style="padding-left:10px;">
	<a class="webMnu" href="javascript:dqrevCreate('{$ID}','{$MODULE}');">
		<span class="fa fa-file-text-o" aria-hidden="true" style="padding-right: 0.5em;"></span>
		<span class="glyphicon-class">{'CRevision'|@getTranslatedString:'CRevision'}</span>
	</a>
	</td>
</tr>
</table>
<div class="cb-alert-info" id="dqrevisionmsg" style="display:none;">{'CreatingRevision'|@getTranslatedString:'CreatingRevision'}</div>
