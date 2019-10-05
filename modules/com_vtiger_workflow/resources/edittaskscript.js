function edittaskscript($) {

	function NumberBox(element) {
		var elementId = element.prop('id');
		var boxId = '#'+elementId+'-number-box';
		var str = '';
		for (var i = 1; i <= 30; i++) {
			str += '<a href="#'+i+'" class="box_cel">'+(i < 10? ('0'+i) : i)+'</a> ';
			if (!(i % 5)) {
				str+='<br>';
			}
		}
		element.after('<div id="'+elementId+'-number-box" style="display:none;" class="box">'+str+'</div>');
		element.focus(function () {
			var pos = element.position();
			$(boxId).css('display', 'block');
			$(boxId).css({
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

	$(document).ready(function () {
		validator = new VTFieldValidator($('#new_task_form'));
		validator.mandatoryFields = ['summary'];
		$('.time_field').timepicker();
		NumberBox($('#select_date_days'));
		//UI to set the date for executing the task.
		$('#check_select_date').click(function () {
			if ($(this).prop('checked')) {
				$('#select_date').css('display', 'block');
			} else {
				$('#select_date').css('display', 'none');
			}
		});
		$('#edittask_cancel_button').click(function () {
			window.location=returnUrl;
		});
		$('#save').bind('click', function () {
			var conditions = [];
			var i=0;
			$('#save_conditions').children('.condition_group_block').each(function (j, conditiongroupblock) {
				$(conditiongroupblock).children('.save_condition_group').each(function (k, conditiongroup) {
					$(conditiongroup).children().each(function () {
						var fieldname = $(this).children('.fieldname').val();
						var operation = $(this).children('.operation').val();
						var value = $(this).children('.expressionvalue').val();
						var valuetype = $(this).children('.expressiontype').val();
						var joincondition = $(this).children('.joincondition').val();
						var groupid = $(this).children('.groupid').val();
						var groupjoin = '';
						if (groupid != '') {
							groupjoin = $('#save_condition_group_'+groupid+'_joincondition').val();
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
		});
	});
}
