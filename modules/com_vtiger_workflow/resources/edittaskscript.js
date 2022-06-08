function edittaskscript($) {

	function NumberBox(element, value) {
		var elementId = element.prop('id');
		var boxId = '#'+elementId+'-number-box';
		var str = '';
		var box_max_len = 0;
		var box_rm = 0;
		if (value == 'days') {
			box_max_len = 30;
			box_rm = 5;
		} else {
			box_max_len = 24;
			box_rm = 4;
		}
		for (var i = 1; i <= box_max_len; i++) {
			str += '<a href="#'+i+'" class="box_cel">'+(i < 10? ('0'+i) : i)+'</a> ';
			if (!(i % box_rm)) {
				str+='<br>';
			}
		}
		element.after('<div id="'+elementId+'-number-box" style="display:none;" class="box">'+str+'</div>');
		element.focus(function () {
			var pos = element.position();
			$(boxId).css('display', 'block');
			$(boxId).css({
				width: '200px',
				'z-index': '1',
				position: 'absolute',
				top: (pos.top+25)+'px'
			});
		});

		element.blur(function () {
			setTimeout(function () {
				$(boxId).css('display', 'none');
			}, 500);
		});

		$('.box_cel').click(function () {
			element.val(parseInt($(this).text(), 10));
		});
	}

	function changeDelayInput() {
		$('#select_date_days').val('');
		$('#select_date_hours').val('');
		if ($('#select_days_hours_option').val() == 'days') {
			NumberBox($('#select_date_days'), $('#select_days_hours_option').val());
			$('#select_date_hours').css('display', 'none');
			$('#select_date_days').css('display', 'block');
		} else { // hours and minutes
			NumberBox($('#select_date_hours'), $('#select_days_hours_option').val());
			$('#select_date_days').css('display', 'none');
			$('#select_date_hours').css('display', 'block');
		}
	}

	$(document).ready(function () {
		validator = new VTFieldValidator($('#new_task_form'));
		validator.mandatoryFields = ['summary'];
		$('.time_field').timepicker();
		if ($('#select_date_days').val() != '') {
			$('#select_date_days').css('display', 'block');
			$('#select_date_hours').css('display', 'none');
			$("select option[value='days']").attr("selected", "selected");
			NumberBox($('#select_date_days'), $('#select_days_hours_option').val());
			$('#select_days_hours_option').on('change', changeDelayInput);
		} else if ($('#select_date_hours').val() != '') {
			$('#select_date_hours').css('display', 'block');
			$('#select_date_days').css('display', 'none');
			//$("select option[value='hours']").attr("selected", "selected");
			NumberBox($('#select_date_hours'), $('#select_days_hours_option').val());
			$('#select_days_hours_option').on('change', changeDelayInput);
		} else {
			//UI to set the date for executing the task.
			$('#check_select_date').click(function () {
				if ($(this).prop('checked')) {
					$('#select_date').css('display', 'block');
					$('#select_date_hours').css('display', 'none');
					NumberBox($('#select_date_days'), $('#select_days_hours_option').val());
					$('#select_days_hours_option').on('change', changeDelayInput);
				} else {
					$('#select_date').css('display', 'none');
				}
			});
		}
		$('#edittask_cancel_button').click(function () {
			window.location=returnUrl;
		});
		$('#save').bind('click', function edittasksaveevent() {
			if (!validator.validate()) {
				return false;
			}
			var conditions = [];
			var i=0;
			$('#save_conditions').children('.condition_group_block').each(function (j, conditiongroupblock) {
				$(conditiongroupblock).children('.save_condition_group').each(function (k, conditiongroup) {
					$(conditiongroup).children().each(function () {
						var fieldname = this.querySelector('div > .cefieldname').value;
						var operation = this.querySelector('div > .ceoperation').value;
						var value = this.querySelector('div > .ceexpressionvalue').value;
						var valuetype = this.querySelector('div > .ceexpressiontype').value;
						var joincondition = this.querySelector('div > .cejoincondition').value;
						var groupid = this.querySelector('div > .groupid').value;
						var groupjoin = '';
						if (groupid != '') {
							let scgj = document.getElementById('save_condition_group_'+groupid+'_joincondition');
							if (scgj != null) {
								groupjoin = scgj.value;
							}
						}
						var condition = {
							fieldname:fieldname,
							operation:operation,
							value:value,
							valuetype:valuetype,
							joincondition:joincondition,
							groupid:groupid,
							groupjoin:groupjoin
						};
						conditions[i++]=condition;
					});
				});
			});
			var out = '';
			if (conditions.length>0) {
				out = JSON.stringify(conditions);
			}
			$('#save_conditions_json').prop('value', out);
			$._data(document.getElementById('save'), 'events').click.map(ev => {
				if (ev.handler.name!='edittasksaveevent') {
					ev.handler();
				}
			});
			document.forms['new_task'].submit();
		});
	});
}