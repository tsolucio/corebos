{foreach item=_COMMENT from=$_COMMENTS}
	<div class="ui-grid-a">
		<div class="ui-block-a">
			{$_COMMENT.commentcontent}<p />
			<font size="2">
				{'LBL_AUTHOR'|@getTranslatedString:'ModComments'}: {$_COMMENT.assigned_user_id} {'LBL_ON'|@getTranslatedString:'ModComments'} {$_COMMENT.createdtime}
			</font>
			<hr />
		</div>
	</div>
{/foreach}