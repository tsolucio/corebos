function getData(empid, divid){
	$.ajax({
		url: 'index.php?module=Settings&action=SettingsAjax&file=loaddata&empid='+empid,
		success: function(html) {
			var ajaxDisplay = document.getElementById(divid);
			ajaxDisplay.innerHTML = html;
		}
	});
}

function OnScrollDiv (div) {
	var info = document.getElementById ('info');
	info.innerHTML = 'Horizontal: ' + div.scrollLeft
						+ 'px<br/>Vertical: ' + div.scrollTop + 'px';
}
