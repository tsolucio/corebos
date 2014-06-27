/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/

ModTrackerCommon = {
	OVERLAYID: '__ModTrackerCommonOverlay__',
	
	initOverlay : function() {
		if($(ModTrackerCommon.OVERLAYID)) {
			return;
		}
		var overlaynode = document.createElement('div');
		overlaynode.id = ModTrackerCommon.OVERLAYID;
		overlaynode.style.width = '550px';
		overlaynode.style.display = 'none';
		document.body.appendChild(overlaynode);
	},
	
	showdiff : function(record, atpoint, highlight) {
		ModTrackerCommon.initOverlay();
		
		if(typeof(atpoint) == 'undefined') atpoint = 0;
		if(typeof(highlight) == 'undefined') highlight = true;

		$('status').show();
		new Ajax.Request(
			'index.php',
	        {queue: {position: 'end', scope: 'command'},
            	method: 'post',
	            postBody:'module=ModTracker&action=ModTrackerAjax&file=ShowDiff&id='+encodeURIComponent(record)+'&atpoint='+encodeURIComponent(atpoint)+'&highlight='+encodeURIComponent(highlight),
				onComplete: function(response) {
					$('status').hide();

					var responseVal = response.responseText;
					$(ModTrackerCommon.OVERLAYID).show();					
					$(ModTrackerCommon.OVERLAYID).innerHTML = response.responseText;
				
					$(ModTrackerCommon.OVERLAYID).style.display='block';
					placeAtCenter($(ModTrackerCommon.OVERLAYID));
           		}
            }
         );
	},
	
	showhistory : function(record, atpoint, highlight) {
		ModTrackerCommon.initOverlay();
		
		if(typeof(atpoint) == 'undefined') atpoint = 0;
		if(typeof(highlight) == 'undefined') highlight = false;

		$('status').show();		
		new Ajax.Request(
			'index.php',
	        {queue: {position: 'end', scope: 'command'},
            	method: 'post',
	            postBody:'module=ModTracker&action=ModTrackerAjax&file=ShowDiff&mode=history&id='+encodeURIComponent(record)+'&atpoint='+encodeURIComponent(atpoint)+'&highlight='+encodeURIComponent(highlight),
				onComplete: function(response) {
					$('status').hide();
					
					var responseVal = response.responseText;
					$(ModTrackerCommon.OVERLAYID).innerHTML = response.responseText;
				
					$(ModTrackerCommon.OVERLAYID).style.display='block';
					placeAtCenter($(ModTrackerCommon.OVERLAYID));
           		}
            }
         );
	},	
	hide : function() {
		$(ModTrackerCommon.OVERLAYID).hide();
	}
}
