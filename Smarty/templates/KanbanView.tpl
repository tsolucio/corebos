{*<!--
/*********************************************************************************
** The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  coreBOS Open Source
 * The Initial Developer of the Original Code is coreBOS
 * All Rights Reserved.
 ********************************************************************************/
-->*}
{if $showDesert}
	{assign var='DESERTInfo' value='LBL_NO_DATA'|@getTranslatedString:$MODULE}
	{include file='Components/Desert.tpl'}
{else}
	<link rel="stylesheet" href="include/jkanban/jkanban.css">
	<script type="text/javascript" src="include/jkanban/jkanban.js"></script>
	<script type="text/javascript" src="include/js/dtlviewajax.js"></script>
	<!-- List View's Buttons and Filters starts -->
	{assign var=SHOWPAGENAVIGATION value=false}
	{include file='ListViewFilter.tpl'}
	<!-- List View's Buttons and Filters ends -->
	<style>
		#{$kanbanID} {
			padding: 20px 0;
		}
		.kanban-board-header, .kanban-item {
			border-radius:0.25rem;
		}
		{foreach from=$kbLanes item=BOARD key=TITLE}
			{if !empty($BOARD.color)}
			.kanban-{$BOARD.color} {
				background-color: {$BOARD.color};
			}
			{/if}
		{/foreach}
		.kanban-board {
			border:1px solid #dddbda;
			border-radius:0.25rem;
			background-clip:padding-box;
		}
		.kanbanboard {
			max-width: "25rem";
			min-width: "12rem";
		}
		.kanban-container {
			overflow-x:scroll;
			white-space: nowrap;
			display: flex;
			padding-bottom:0.75rem;
		}
	</style>
	<div id="{{$kanbanID}}" style="max-width: 96vw; min-height:105vh;"></div>
	<script>
	var KanbanTest = new jKanban({
		element: "#{$kanbanID}",
		gutter: "10px",
		widthBoard: "kanbanboard slds-card",
		itemHandleOptions:{
			enabled: false,
		},
		dropEl: function(el, target, source, sibling){
		console.log(target.parentElement.getAttribute('data-id'));
		console.log(el, target, source, sibling)
		},
		buttonClick: function(el, boardId) {
		console.log(el);
		console.log(boardId);
		// create a form to enter element
		var formItem = document.createElement("form");
		formItem.setAttribute("class", "itemform");
		formItem.innerHTML =
			'<div class="form-group"><textarea class="form-control" rows="2" autofocus></textarea></div><div class="form-group"><button type="submit" class="btn btn-primary btn-xs pull-right">Submit</button><button type="button" id="CancelBtn" class="btn btn-default btn-xs pull-right">Cancel</button></div>';

		KanbanTest.addForm(boardId, formItem);
		formItem.addEventListener("submit", function(e) {
			e.preventDefault();
			var text = e.target[0].value;
			KanbanTest.addElement(boardId, {
			title: text
			});
			formItem.parentNode.removeChild(formItem);
		});
		document.getElementById("CancelBtn").onclick = function() {
			formItem.parentNode.removeChild(formItem);
		};
		},
		itemAddOptions: {
			enabled: false,
			footer: false
		},
		boards: [
		{foreach from=$kbLanes item=BOARD key=TITLE}
		{
			id: "{$BOARD.id}",
			title: `{include file="Components/Kanban/KanbanHeader.tpl"}`,
			class: "{if !empty($BOARD.color)}kanban-{$BOARD.color}{/if}",
			item: [
			{
				id: "{$Tile.id}",
				title: `{include file="Components/Kanban/KanbanTile.tpl"}`,
			},
			{
				title: "Try Click This!",
			}
			]
		},
		{/foreach}
		]
	});

	// Custom CSS
	document.getElementById('{$kanbanID}').querySelectorAll('.tooltip').forEach(element => element.style.position='absolute');
	document.querySelectorAll('.slds-tile .slds-grid ul').forEach(element => element.classList.add('small'));
	</script>
{/if}