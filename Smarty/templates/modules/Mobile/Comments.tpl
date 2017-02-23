{foreach item=_COMMENT from=$_COMMENTS}
	<div class="ui-grid-a">
		<div class="ui-block-a">
			{$_COMMENT.commentcontent}<p />
			<font size="2">
				{$_COMMENT.assigned_user_id} {$MOD.LBL_ON_DATE} {$_COMMENT.createdtime}
			</font>
			<hr />
		</div>
	</div>
{/foreach}