<ul class="slds-list_horizontal">
  <li>
  <input type="hidden" name="multipleschtime" id="multipleschtime" value="{$multipleschtime}">
	<div class="slds-pill_container" id="sheduletimelist">
	{foreach $multipleschtime_12h as $schtime}
	{if !empty($schtime)
	}
		<span class="slds-pill slds-pill_link" id="{preg_replace('/\s+|\:/','-',$schtime)}">
		<a href="#" class="slds-pill__action" title="">
		<span class="slds-pill__label">{$schtime}</span>
		</a>
		<a class="slds-button slds-button_icon slds-button_icon slds-pill__remove" title="{$MOD.REMOVE}" onclick="removeSchTime('{$schtime}')">
		<svg class="slds-button__icon" aria-hidden="true">
			<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#close"></use>
		</svg>
		<span class="slds-assistive-text">{$MOD.REMOVE}</span>
		</a>
	</span>
	{/if}
	{/foreach}
	</div>
  </li>
  <li>
  <div class="slds-col slds-size_4-of-4 slds-p-around_xxx-small" id="addschedulebtn">
			<a class="slds-button slds-button_neutral"  onclick="addScheduleTime()">
				<svg class="slds-button__icon slds-button__icon_left" aria-hidden="true">
					<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#add"></use>
				</svg>
			</a>
			</div>
  </li>
</ul>
<div class="slds-form-element" style="display:none" id="addscheduleform">
	<div class="slds-form-element__control">
		<div class="slds-combobox_container">
			<div class="slds-combobox slds-dropdown-trigger slds-dropdown-trigger_click  slds-timepicker" id="chtimemultipletimers">
				<div class="slds-combobox__form-element slds-input-has-icon slds-input-has-icon_right" role="none">
					<input type="text" class="slds-input slds-combobox__input slds-has-focus" id=""
						aria-activedescendant="" aria-autocomplete="list" aria-controls="select-schedule-time"
						aria-expanded="true" aria-haspopup="listbox" autoComplete="off" role="combobox"
						placeholder="{$MOD.SELECT_TIME}" value="" / onclick="triggerTimeInput()">
					<span class="slds-icon_container slds-icon-utility-clock slds-input__icon slds-input__icon_right">
						<svg class="slds-icon slds-icon slds-icon_x-small slds-icon-text-default" aria-hidden="true">
							<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#clock"></use>
						</svg>
					</span>
				</div>
				<div id="select-schedule-time" class="slds-dropdown slds-dropdown_length-5 slds-dropdown_fluid" role="listbox">
					<ul class="slds-listbox slds-listbox_vertical" role="presentation">
					{foreach $schedulerTimeOptions as $schtime}
							<li role="presentation" class="slds-listbox__item" >
							<div id="option1"
								class="slds-media slds-listbox__option slds-listbox__option_plain slds-media_small"
								role="option"
								onclick="selectschTime('{$schtime}')">
								<span class="slds-media__figure slds-listbox__option-icon"></span>
								<span class="slds-media__body">
									<span class="slds-truncate" title="{$schtime}">{$schtime}</span>
								</span>
							</div>
						</li>
					{/foreach}
					</ul>
				</div>
			</div>
		</div>
	</div>
</div>