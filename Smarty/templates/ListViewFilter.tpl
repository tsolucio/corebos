{if !isset($moduleShowFilter) || $moduleShowFilter}
<table style="width:100%;" class="slds-card">
	<tr>
		<td width="25%" class="small" nowrap align="left">
			{if $MODULE neq 'Documents'}{$recordListRange}{/if}
		</td>
		<td><table>
				<tr>
					<td>
						<!-- Filters -->
						{if empty($HIDE_CUSTOM_LINKS) || $HIDE_CUSTOM_LINKS neq '1'}
						<table cellpadding="5" cellspacing="0" class="small cblds-table-border_sep cblds-table-bordersp_medium">
							<tr>
								<td style="padding-left:5px;padding-right:5px" align="center">
									<strong>{$APP.LBL_VIEW}</strong>
									<select name="viewname" id="viewname" class="slds-select" style="max-width:240px;" onchange="showDefaultCustomView(this, '{$MODULE}')">{$CUSTOMVIEW_OPTION}</select>
								</td>
								{if isset($ALL) && $ALL eq 'All'}
								<td style="padding-left:5px;padding-right:5px;width:45%;" align="center"><a href="index.php?module={$MODULE}&action=CustomView">{$APP.LNK_CV_CREATEVIEW}</a>
									<span class="small">|</span>
									{if isset($EDIT_FILTER_ALL) && $EDIT_FILTER_ALL eq '1'}
										<a href="index.php?module={$MODULE}&action=CustomView&record={$VIEWID}&permitall=true">{$APP.LNK_CV_EDIT}</a>
									{else}
										<span class="small">{$APP.LNK_CV_EDIT}</span>
									{/if}
									<span class="small">|</span>
									<span class="small" disabled>{$APP.LNK_CV_DELETE}</span>
								</td>
								{else}
								<td style="padding-left:5px;padding-right:5px" align="center">
									<a href="index.php?module={$MODULE}&action=CustomView">{$APP.LNK_CV_CREATEVIEW}</a>
									<span class="small">|</span>
									{if $CV_EDIT_PERMIT neq 'yes' || $SQLERROR}
										<span class="small" disabled>{$APP.LNK_CV_EDIT}</span>
									{else}
										<a href="index.php?module={$MODULE}&action=CustomView&record={$VIEWID}">{$APP.LNK_CV_EDIT}</a>
									{/if}
									<span class="small">|</span>
									{if $CV_DELETE_PERMIT neq 'yes'}
										<span class="small" disabled>{$APP.LNK_CV_DELETE}</span>
									{else}
										<a href="javascript:confirmdelete('index.php?module=CustomView&action=Delete&dmodule={$MODULE}&record={$VIEWID}')">{$APP.LNK_CV_DELETE}</a>
									{/if}
									{if $CUSTOMVIEW_PERMISSION.ChangedStatus neq '' && $CUSTOMVIEW_PERMISSION.Label neq ''}
										<span class="small">|</span>
										<a href="#" id="customstatus_id" onClick="ChangeCustomViewStatus({$VIEWID}, {$CUSTOMVIEW_PERMISSION.Status}, {$CUSTOMVIEW_PERMISSION.ChangedStatus}, '{$MODULE}')">{$CUSTOMVIEW_PERMISSION.Label}</a>
									{/if}
								</td>
								{/if}
							</tr>
						</table>
						<!-- Filters END-->
						{/if}
					</td>
				</tr>
			</table>
		</td>
		<!-- Page Navigation -->
		<td nowrap align="right" width="25%" class="cblds-t-align_right">
			{if $MODULE neq 'Documents'}
			{if !isset($SHOWPAGENAVIGATION) || $SHOWPAGENAVIGATION}
			<table border=0 cellspacing=0 cellpadding=0 class="small" style="display: inline-block;">
				<tr>{$NAVIGATION}</tr>
			</table>
			{/if}
			{/if}
		</td>
	</tr>
</table>
{/if}