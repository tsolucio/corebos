<div
	style="text-align: center; font-family: courier new, courier, monospace; font-size: 220%; font-weight: bold;">
	{if $SHOW_WATCH eq 'halted'}
	 <img src="modules/Timecontrol/images/clock-red.gif" id="clock_image" style="vertical-align: middle">
	 <span id="clock_display" style="vertical-align: middle">{$WATCH_DISPLAY}</span>
	 <input	title="{$MOD.LBL_WATCH_RESTART}" type="submit"
		value="{$MOD.LBL_WATCH_RESTART}" style="vertical-align: middle"
		onclick="DetailView.return_module.value='{$MODULE}'; DetailView.return_action.value='DetailView'; DetailView.isDuplicate.value='restart';DetailView.module.value='{$MODULE}'; submitFormForAction('DetailView','EditView');">
	{/if}
	{if $SHOW_WATCH eq 'started'}
	<form action="" method="post">
		<div>
			<input type="hidden" name="module" value="{$MODULE}">
			<input type="hidden" name="record" value="{$ID}">
			<input type="hidden" name="mode" value="edit">
			<input type="hidden" name="action" value="Save">
			<input type="hidden" name="stop_watch" value="1">
			<img src="modules/Timecontrol/images/clock-green.gif" id="clock_image" style="vertical-align: middle">
			<span id="clock_display_hours" style="vertical-align: middle;">00</span><span id="clock_display_separator" style="vertical-align: middle;">:</span><span id="clock_display_minutes" style="vertical-align: middle;">00</span>
			<input id="clock_counter" type="hidden" value="{$WATCH_COUNTER}">
			<input title="{$MOD.LBL_WATCH_STOP}" type="submit" value="{$MOD.LBL_WATCH_STOP}" style="vertical-align: middle">
		</div>
	</form>
	<script type="text/javascript">
      updateClock(true);
      setInterval("updateClock()", 1000);
    </script>
	{/if}
</div>
