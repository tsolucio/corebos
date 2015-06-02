function edittaskscript($){

	function NumberBox(element){
		var elementId = element.attr("id");
		var boxId = '#'+elementId+'-number-box';
		var str = "";
		for(var i = 1; i <= 30; i++){
			str += '<a href="#'+i+'" class="box_cel">'+(i < 10? ("0"+i) : i)+'</a> ';
			if(!(i % 5)){
				str+="<br>";
			}
		}
		element.after('<div id="'+elementId+'-number-box" style="display:none;" class="box">'+str+'</div>');
		element.focus(function(){
			var pos = element.position();
			$(boxId).css('display', 'block');
			$(boxId).css({
				position: 'absolute',
				top: (pos.top+25)+'px'
			});
		});

		element.blur(function(){
			setTimeout(function(){$(boxId).css('display', 'none');},500);
		});

		$('.box_cel').click(function(){
			element.attr('value', parseInt($(this).text(), 10));
		});
	}



	$(document).ready(function(){
		validator = new VTFieldValidator($('#new_task_form'));
		validator.mandatoryFields = ['summary'];
		$('.time_field').timepicker();
		NumberBox($('#select_date_days'));
        //UI to set the date for executing the task.
    	$('#check_select_date').click(function(){
    	    if($(this).attr('checked')){
    	        $('#select_date').css('display', 'block');
    	    }else{
    	        $('#select_date').css('display', 'none');
    	    }
    	});
			$('#edittask_cancel_button').click(function(){
				window.location=returnUrl;
			});
		$("#save").bind("click", function(){
			var conditions = [];
			i=0;
			$("#save_conditions").children(".condition_group_block").each(function(j, conditiongroupblock){
				$(conditiongroupblock).children(".save_condition_group").each(function(k, conditiongroup){
					$(conditiongroup).children().each(function(l){
						var fieldname = $(this).children(".fieldname").attr("value");
						var operation = $(this).children(".operation").attr("value");
						var value = $(this).children(".expressionvalue").attr("value");
						var valuetype = $(this).children(".expressiontype").attr("value");
						var joincondition = $(this).children(".joincondition").attr("value");
						var groupid = $(this).children(".groupid").attr("value");
						var groupjoin = '';
						if(groupid != '') groupjoin = $('#save_condition_group_'+groupid+'_joincondition').attr("value");
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
			if(conditions.length==0){
				var out = "";
			}else{
				var out = JSON.stringify(conditions);
			}
			$("#save_conditions_json").attr("value", out);
		});
    });
}