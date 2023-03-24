<div class="small detailview_utils_table_bottom noprint" style="padding-right: 1%;padding-left: 1%">
{if empty($Module_Popup_Edit)}
	<div class="detailview_utils_table_tabactionsep detailview_utils_table_tabactionsep_bottom" id="detailview_utils_table_tabactionsep_bottom"></div>
		<div class="detailview_utils_table_actions detailview_utils_table_actions_bottom" id="detailview_utils_actions_bottom">
		<div class="slds-button-group" role="group">
			{if $EDIT_PERMISSION eq 'yes'}
				<button
					class="slds-button slds-button_neutral"
					title="{$APP.LBL_EDIT_BUTTON_TITLE}"
					value="{$APP.LBL_EDIT_BUTTON_TITLE}"
					accessKey="{$APP.LBL_EDIT_BUTTON_KEY}"
					onclick="DetailView.return_module.value='{$MODULE}'; 
							DetailView.return_action.value='DetailView';
							DetailView.return_id.value='{$ID}';
							DetailView.module.value='{$MODULE}';
							submitFormForAction('DetailView','EditView');"
					type="button"
					name="Edit"
					>
					<svg class="slds-button__icon slds-button__icon_left" aria-hidden="true">
						<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#edit"></use>
					</svg>
					{$APP.LBL_EDIT_BUTTON_LABEL}
				</button>
			{/if}
			{if ((isset($CREATE_PERMISSION) && $CREATE_PERMISSION eq 'permitted') || (isset($EDIT_PERMISSION) && $EDIT_PERMISSION eq 'yes')) && $MODULE neq 'Documents'}
				<button
					class="slds-button slds-button_neutral"
					title="{$APP.LBL_DUPLICATE_BUTTON_TITLE}"
					value="{$APP.LBL_DUPLICATE_BUTTON_TITLE}"
					accessKey="{$APP.LBL_DUPLICATE_BUTTON_KEY}"
					onclick="DetailView.return_module.value='{$MODULE}'; 
							DetailView.return_action.value='DetailView'; 
							DetailView.isDuplicate.value='true';
							DetailView.module.value='{$MODULE}'; 
							submitFormForAction('DetailView','EditView');" 
					type="submit"
					name="Duplicate"
					>
					<svg class="slds-button__icon slds-button__icon_left" aria-hidden="true">
						<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#file"></use>
					</svg>
					{$APP.LBL_DUPLICATE_BUTTON_LABEL}
				</button>
			{/if}
			{if $DELETE eq 'permitted'}	
				<button
					class="slds-button slds-button_text-destructive"
					title="{$APP.LBL_DELETE_BUTTON_TITLE}"
					value="{$APP.LBL_DELETE_BUTTON_TITLE}"
					accessKey="{$APP.LBL_DELETE_BUTTON_KEY}"
					onclick="DetailView.return_module.value='{$MODULE}'; 
						DetailView.return_action.value='index'; 
						{if $MODULE eq 'Accounts'} var confirmMsg = '{$APP.NTC_ACCOUNT_DELETE_CONFIRMATION}' {else} var confirmMsg = '{$APP.NTC_DELETE_CONFIRMATION}' {/if}; submitFormForActionWithConfirmation('DetailView', 'Delete', confirmMsg);"
					type="submit"
					name="Delete" 
					>
					<svg class="slds-button__icon slds-button__icon_left" aria-hidden="true">
						<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#delete"></use>
					</svg>
					{$APP.LBL_DELETE_BUTTON_LABEL}
				</button>
			{/if}
		</div>
		{if empty($Module_Popup_Edit)}
			<div class="slds-button-group" role="group">
				{include file='Components/DetailViewPirvNext.tpl'}
			</div>
		{/if}
		</div>
	{/if}
</div>