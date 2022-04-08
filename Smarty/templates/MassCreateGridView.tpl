<script type="text/javascript">
	var GridColumns = '{$GridColumns}';
	var EmptyData = '{$EmptyData}';
</script>
<script src="./include/MassCreateGridView/MassCreateGridView.js"></script>
<div class="slds-button-group" role="group" style="float: right; margin-bottom: 5px">
	<button class="slds-button slds-button_neutral" onclick="MCGrid.Append()" id="append-btn" accesskey="A">Append Row</button>
	<button class="slds-button slds-button_neutral" onclick="MCGrid.Save()">Save</button>
	<button class="slds-button slds-button_neutral" onclick="MCGrid.Delete()">Delete</button>
</div>
<div id="ListViewContents" class="small" style="width:100%;">